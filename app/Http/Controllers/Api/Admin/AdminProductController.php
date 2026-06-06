<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\AdminProductResource;
use Illuminate\Http\Request;
use App\Models\Product;

use App\Traits\ApiResponse;

class AdminProductController extends Controller
{
    use ApiResponse;
    /**
     * 📋 List all products (with filters)
     */
    public function index(Request $request)
    {
        $query = Product::with(['seller', 'category', 'images'])
            ->withCount('orders')
            ->latest();

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('seller_id')) {
            $query->where('seller_id', $request->seller_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                    ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        $products = $query->paginate(10);

        return AdminProductResource::collection($products)->additional([
            'success' => true,
            'message' => 'Products retrieved successfully'
        ]);
    }

    /**
     * 👁️ Show single product
     */
    public function show(Product $product)
    {
        $product->load(['seller', 'category', 'images', 'condition', 'size'])->loadCount('orders');

        return $this->success(new AdminProductResource($product), 'Product retrieved successfully');
    }

    /**
     * 🗑️ Delete product (admin moderation)
     */
    public function destroy(Product $product)
    {
        //delete all orders of product
        $product->delete();

        return $this->success(null, 'Product deleted successfully');
    }
}
