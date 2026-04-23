<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewReservationNotification extends Notification
{
    use Queueable;

    protected $reservation;

    /**
     * Create a new notification instance.
     */
    public function __construct(Reservation $reservation)
    {
        $this->reservation = $reservation;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'reservation_id' => $this->reservation->id,
            'customer'       => $this->reservation->customer_name,
            'date'           => $this->reservation->reservation_date,
            'time'           => $this->reservation->reservation_time_slot,
            'message'        => "Reservasi baru dari {$this->reservation->customer_name} untuk tanggal {$this->reservation->reservation_date}",
            'type'           => 'reservation',
        ];
    }
}
