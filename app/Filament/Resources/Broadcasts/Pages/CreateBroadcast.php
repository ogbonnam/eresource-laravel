<?php

namespace App\Filament\Resources\Broadcasts\Pages;

use App\Filament\Resources\Broadcasts\BroadcastResource;
use App\Models\Broadcast;
use App\Models\BroadcastAttachment;
use App\Models\BroadcastRecipient;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateBroadcast extends CreateRecord
{
    protected static string $resource = BroadcastResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            /*
             * -------------------------------------------------------------
             * 1. Get the selected teachers before removing form-only fields.
             * -------------------------------------------------------------
             */
            $teacherIds = $data['teacher_ids'] ?? [];

            /*
             * -------------------------------------------------------------
             * 2. Get the uploaded attachment paths.
             * -------------------------------------------------------------
             */
            $attachments = $data['attachments'] ?? [];

            /*
             * -------------------------------------------------------------
             * 3. Remove fields that do not exist in the broadcasts table.
             * -------------------------------------------------------------
             */
            unset(
                $data['teacher_ids'],
                $data['attachments']
            );

            /*
             * -------------------------------------------------------------
             * 4. Record who sent the broadcast and when.
             * -------------------------------------------------------------
             */
            $data['sender_id'] = auth()->id();
            $data['sent_at'] = now();

            /*
             * -------------------------------------------------------------
             * 5. Create the broadcast itself.
             * -------------------------------------------------------------
             */
            $broadcast = Broadcast::create($data);

            /*
             * -------------------------------------------------------------
             * 6. Determine the actual recipients.
             *
             * We snapshot the teachers at send time.
             * Future changes to departments will not alter this broadcast.
             * -------------------------------------------------------------
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
             * -------------------------------------------------------------
             * 7. Create one recipient record for every teacher.
             * -------------------------------------------------------------
             */
            foreach ($recipients as $teacherId) {
                BroadcastRecipient::create([
                    'broadcast_id' => $broadcast->id,
                    'teacher_id' => $teacherId,
                    'open_count' => 0,
                ]);
            }

            /*
             * -------------------------------------------------------------
             * 8. Save attachment information.
             *
             * FileUpload has already stored the physical files.
             * We store their paths and metadata in the database.
             * -------------------------------------------------------------
             */
            foreach ($attachments as $attachment) {
                if (! is_string($attachment)) {
                    continue;
                }

                $disk = \Illuminate\Support\Facades\Storage::disk('local');

                $broadcast->attachments()->create([
                    'file_name' => basename($attachment),
                    'file_path' => $attachment,
                    'mime_type' => $disk->mimeType($attachment),
                    'file_size' => $disk->size($attachment),
                ]);
            }

            return $broadcast;
        });
    }
}