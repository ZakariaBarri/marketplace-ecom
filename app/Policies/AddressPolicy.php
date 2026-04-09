<?php

namespace App\Policies;

use App\Models\Addresse;
use App\Models\User;

class AddressPolicy
{
    /**
     * عرض كل العناوين
     */
    public function viewAny(User $user)
    {
        return true; // أي مستخدم مسجل يمكنه رؤية عناوينه
    }

    /**
     * عرض عنوان واحد
     */
    public function view(User $user, Addresse $address)
    {
        return $address->buyer_id === $user->id;
    }

    /**
     * إنشاء عنوان
     */
    public function create(User $user)
    {
        return true; // أي مستخدم يمكنه إضافة عنوان
    }

    /**
     * تحديث عنوان
     */
    public function update(User $user, Addresse $address)
    {
        return $address->buyer_id === $user->id;
    }

    /**
     * حذف عنوان
     */
    public function delete(User $user, Addresse $address)
    {
        return $address->buyer_id === $user->id;
    }
}