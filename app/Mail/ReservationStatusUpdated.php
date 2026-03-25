<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservationStatusUpdated extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $reservation;

    public function __construct(Reservation $reservation)
    {
        $this->reservation = $reservation;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pembaruan Status Reservasi Anda (' . \Carbon\Carbon::parse($this->reservation->reservation_date)->format('d M Y') . ')',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.reservations.status_updated',
        );
    }
}
