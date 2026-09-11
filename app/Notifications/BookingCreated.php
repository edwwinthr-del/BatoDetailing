<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingCreated extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Appointment $appointment) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('Booking received — BatoDetailing'))
            ->greeting(__('Hello :name,', ['name' => $notifiable->first_name]))
            ->line(__('We have received your booking for :date.', ['date' => $this->appointment->scheduled_at->format('d.m.Y H:i')]))
            ->line(__('Vehicle: :vehicle', ['vehicle' => $this->appointment->vehicle->displayName()]))
            ->line(__('Total: :total €', ['total' => number_format((float) $this->appointment->total, 2)]))
            ->line(__('We will confirm your appointment shortly.'))
            ->action(__('View appointment'), route('appointments.show', $this->appointment));
    }
}
