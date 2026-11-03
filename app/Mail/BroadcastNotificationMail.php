<?php

namespace App\Mail;

use App\Models\Broadcast;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BroadcastNotificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Broadcast $broadcast,
        public User $teacher
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New School Broadcast: ' . $this->broadcast->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.broadcast-notification',
            with: [
                'broadcast' => $this->broadcast,
                'teacher' => $this->teacher,
                'broadcastUrl' => route(
                    'teacher.broadcasts.show',
                    $this->broadcast
                ),
                'messagePreview' => \Illuminate\Support\Str::limit(
                    trim(strip_tags($this->broadcast->message)),
                    300
                ),
            ],
        );
    }
}
