<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use Illuminate\Notifications\Messages\BroadcastMessage;

class OrderCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    //public $order;
    /**
     * Create a new notification instance.
     */
    public function __construct(public $order)
    {
        //$this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage($this->payload());
    }

    public function toDatabase($notifiable)
    {
        return $this->payload();
    }

    private function payload()
    {
        return [
            'type' => 'order_created',
            'order_id' => $this->order->id,
            'buyer' => [
                'id' => $this->order->buyer->id,
                'name' => $this->order->buyer->name,
            ],
            'message' => $this->order->buyer->name . ' placed a new order',
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

}
