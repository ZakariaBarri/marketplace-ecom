<?php

namespace App\Events;

use App\Notifications\OrderCreatedNotification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;


class NewOrderCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public $order) {}

    public function broadcastOn()
    {
        return new PrivateChannel('orders.' . $this->order->seller_id);
    }

    public function broadcastAs(): string
    {
        return 'notification';
    }

     public function broadcastWith()
    {
        return (new OrderCreatedNotification($this->order))->toArray(request());
    }
}
