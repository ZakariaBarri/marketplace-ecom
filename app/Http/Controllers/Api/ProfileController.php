<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\ChangePasswordRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Resources\Profile\ProfileResource;
use App\Http\Resources\Profile\ProfileStatsResource;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

use App\Traits\ApiResponse;

class ProfileController extends Controller
{
    use ApiResponse;

    // Afficher les informations du profil utilisateur

    public function show(Request $request)
    {
        return $this->success(
            new ProfileResource($request->user()),
            'User found'
        );
    }

    // Mise à jour du profil

    public function update(UpdateProfileRequest $request)
    {
        $user = $request->user();

        $this->authorize('update', $user);

        $user->update($request->validated());

        return $this->success(
            new ProfileResource($user),
            'Profile successfully updated'
        );
    }

    // Changement du mot de passe

    public function changePassword(ChangePasswordRequest $request)
    {
        $user = $request->user();

        if (!Hash::check(
            $request->current_password,
            $user->password
        )) {
            return $this->error('Current password is incorrect', null, 422);
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return $this->success(null, 'Password updated successfully');
    }

    /**
     * GET /api/profile/stats
     * Statistiques personnelles
     */
    public function stats(Request $request)
    {
        $user = $request->user();

        $totalSales = $user->sales()
            ->where('status', 'delivered')
            ->count();

        $totalPurchases = $user->orders()
            ->where('status', 'delivered')
            ->count();

        $data = [
            'total_sales' => $totalSales,
            'total_purchases' => $totalPurchases,
            'seller_rating_avg' => $user->seller_rating_avg,
            'seller_rating_count' => $user->seller_rating_count,
            'buyer_rating_avg' => $user->buyer_rating_avg,
            'buyer_rating_count' => $user->buyer_rating_count,
        ];

        return $this->success(
            new ProfileStatsResource($data),
            'Stats retrieved successfully'
        );
    }
}
