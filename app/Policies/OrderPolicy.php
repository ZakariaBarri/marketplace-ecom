<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrderPolicy
{
    // البائع فقط يقبل الطلب
    public function accept(User $user, Order $order)
    {
        return $user->id === $order->seller_id
            && $order->status === 'pending';
    }

    // 🔥 البائع يرفض الطلب
    public function reject(User $user, Order $order)
    {
        return $user->id === $order->seller_id
            && $order->status === 'pending';
    }

    // 🔥 البائع يشحن
    public function ship(User $user, Order $order)
    {
        return $user->id === $order->seller_id
            && $order->status === 'accepted';
    }

    // 🔥 البائع يؤكد التسليم
    public function deliver(User $user, Order $order)
    {
        return $user->id === $order->seller_id
            && $order->status === 'shipped';
    }

    // 🔥 المشتري يلغي
    public function cancel(User $user, Order $order)
    {
        return $user->id === $order->buyer_id
            && in_array($order->status, ['pending', 'accepted']);
    }

    // 🔥 فشل التوصيل (البائع)
    public function failDelivery(User $user, Order $order)
    {
        return $user->id === $order->seller_id
            && $order->status === 'shipped';
    }
    /**
     * Determine whether the user can view the model.
     */
    // عرض الطلب
    public function view(User $user, Order $order): bool
    {
        return $user->id === $order->buyer_id
            || $user->id === $order->seller_id;
    }

    // 🔥 إنشاء طلب جديد
    public function create(User $user, Product $product)
    {
        // ❌ لا يمكن شراء منتجك
        if ($product->seller_id === $user->id) {
            return false;
        }

        // ❌ المنتج غير متاح
        if ($product->status !== 'available') {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    // تحديث الطلب (فقط المشتري قبل الشحن)
    public function update(User $user, Order $order): bool
    {
        return $user->id === $order->buyer_id
            && $order->status === 'pending';
    }

    /**
     * Determine whether the user can delete the model.
     */
    // حذف الطلب (فقط المشتري قبل الشحن)
    public function delete(User $user, Order $order)
    {
        return $user->id === $order->buyer_id
            && $order->status === 'pending';
    }
}
