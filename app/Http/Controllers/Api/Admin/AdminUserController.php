<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Http\Resources\Admin\AdminUserResource;
use App\Traits\ApiResponse;

class AdminUserController extends Controller
{
    use ApiResponse;
    /**
     * 📋 List all users
     */
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        $users = $query->latest()->paginate(20);

        return AdminUserResource::collection($users)
            ->additional([
                'success' => true,
                'message' => 'Users retrieved successfully',
            ]);
    }

    /**
     * 👤 Show single user
     */
    public function show(User $user)
    {
        return $this->success(new AdminUserResource($user), 'User found');
    }

    /**
     * 🗑️ Delete user
     */
    public function destroy(User $user)
    {
        if ($user->role === 'admin') {
            return $this->error('Admin cannot be deleted', null, 403);
        }

        if ($user->orders()->exists() || $user->sales()->exists()) {
            return $this->error('Cannot delete user with existing orders or sales', null, 422);
        }

        $user->delete(); 

        return $this->success(null, 'User deleted successfully');
    }
}
