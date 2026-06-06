<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Category;
use App\Traits\ApiResponse;

class AdminController extends Controller
{
    use ApiResponse;

    public function dashboard()
    {
        $stats = [
            'total_users' => User::where('role', 'user')->count(),
            'total_products' => Product::count(),
            'total_orders' => Order::count(),
            'delivered_orders' => Order::where('status', 'delivered')->count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'total_categories' => Category::count(),
            'latest_orders' => Order::latest()->take(5)->get(),
        ];

        return $this->success($stats, 'Dashboard data fetched');
    }
}
