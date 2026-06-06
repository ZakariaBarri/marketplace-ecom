<?php

namespace App\Listeners;

use App\Events\NewOrderCreated;
use App\Notifications\OrderCreatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifySeller implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(NewOrderCreated $event)
    {
        $order = $event->order->load(['seller', 'buyer']);

        if (!$order->seller) {
            return;
        }

        $order->seller->notify(
            new OrderCreatedNotification($order)
        );
    }
}
