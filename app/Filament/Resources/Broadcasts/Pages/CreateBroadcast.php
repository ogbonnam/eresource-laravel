<?php

namespace App\Filament\Resources\Broadcasts\Pages;

use App\Filament\Resources\Broadcasts\BroadcastResource;
use App\Mail\BroadcastNotificationMail;
use App\Models\Broadcast;
use App\Models\BroadcastRecipient;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class CreateBroadcast extends CreateRecord
{
    protected static string $resource = BroadcastResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Save the broadcast and recipients.
        |--------------------------------------------------------------------------
        */

        $broadcast = DB::transaction(function () use ($data) {

            $teacherIds = $data['teacher_ids'] ?? [];

            $attachments = $data['attachments'] ?? [];

            unset(
                $data['teacher_ids'],
                $data['attachments']
            );

            $data['sender_id'] = auth()->id();

            $data['sent_at'] = now();

            $broadcast = Broadcast::create($data);

            /*
            |--------------------------------------------------------------------------
            | 2. Determine the recipients.
            |--------------------------------------------------------------------------
            */

            $recipients = match ($broadcast->target_type) {

                'all' => User::query()
                    ->where('role', 'teacher')
                    ->pluck('id'),

                'department' => User::query()
                    ->where('role', 'teacher')
                    ->where('faculty_id', $broadcast->department_id)
                    ->pluck('id'),

                'individual' => User::query()
                    ->where('role', 'teacher')
                    ->whereIn('id', $teacherIds)
                    ->pluck('id'),

                default => collect(),

            };

            /*
            |--------------------------------------------------------------------------
            | 3. Create recipient tracking records.
            |--------------------------------------------------------------------------
            */

            foreach ($recipients as $teacherId) {

                BroadcastRecipient::create([
                    'broadcast_id' => $broadcast->id,
                    'teacher_id' => $teacherId,
                    'open_count' => 0,
                ]);

            }

            /*
            |--------------------------------------------------------------------------
            | 4. Save attachment information.
            |--------------------------------------------------------------------------
            */

            foreach ($attachments as $attachment) {

                if (! is_string($attachment)) {
                    continue;
                }

                $disk = Storage::disk('local');

                $broadcast->attachments()->create([

                    'file_name' => basename($attachment),

                    'file_path' => $attachment,

                    'mime_type' => $disk->mimeType($attachment),

                    'file_size' => $disk->size($attachment),

                ]);

            }

            return $broadcast;

        });

        /*
        |--------------------------------------------------------------------------
        | 5. Queue individual emails AFTER the transaction commits.
        |--------------------------------------------------------------------------
        */

        $recipientRecords = BroadcastRecipient::query()
            ->with('teacher:id,name,email')
            ->where('broadcast_id', $broadcast->id)
            ->get();

        foreach ($recipientRecords as $recipient) {

            $teacher = $recipient->teacher;

            /*
             * Skip teachers without a valid email address.
             */

            if (
                ! $teacher ||
                ! filter_var($teacher->email, FILTER_VALIDATE_EMAIL)
            ) {

                Log::warning(
                    'Broadcast email skipped: invalid teacher email.',
                    [
                        'broadcast_id' => $broadcast->id,
                        'teacher_id' => $recipient->teacher_id,
                    ]
                );

                continue;

            }

            try {

                Mail::to($teacher->email)->queue(
                    new BroadcastNotificationMail(
                        $broadcast,
                        $teacher
                    )
                );

            } catch (\Throwable $exception) {

                /*
                 * An email queue failure must not undo
                 * the already-created broadcast.
                 */

                Log::error(
                    'Unable to queue broadcast email.',
                    [
                        'broadcast_id' => $broadcast->id,
                        'teacher_id' => $teacher->id,
                        'error' => $exception->getMessage(),
                    ]
                );

            }

        }

        return $broadcast;
    }
}
