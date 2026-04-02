<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

use App\Http\Requests\UpdateProductRequest;

use App\Traits\ApiResponse;

class ProductController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //http://127.0.0.1:8000/api/products?search=laptop&category_id=2&condition_id=1&min_price=100&max_price=1000&page=2
        
        $products = Product::query();

        $products->where('status', 'available');

        // Search by title
        if ($request->search) {
            $products->where('title', 'like', '%' . $request->search . '%');
        }

        // Filter by category
        if ($request->category_id) {
            $products->where('category_id', $request->category_id);
        }

        // Filter by condition
        if ($request->condition_id) {
            $products->where('condition_id', $request->condition_id);
        }

        // Price range filter
        if ($request->min_price) {
            $products->where('price', '>=', $request->min_price);
        }

        if ($request->max_price) {
            $products->where('price', '<=', $request->max_price);
        }

        // ترتيب من الأحدث للأقدم
        //$products = $products->orderBy('created_at', 'desc')->paginate(10);
        // Pagination
        $products = $products->latest()->paginate(10);

        return ProductResource::collection($products)->additional([
            'status' => 'success',
            'message' => 'Products retrieved successfully',
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $this->authorize('create', Product::class);

        $product = Product::create([
            ...$request->validated(),
            'seller_id' => auth()->id()
        ]);

        // Load relations if needed in resource
        $product->load(['seller', 'condition', 'category']);

        return $this->success(
            new ProductResource($product),
            'Product created successfully'
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        /*if (!$product) {
            return $this->error('Product not found', null, 404);
        }*/
        $product->load(['seller', 'condition', 'category']);
        return $this->success(
            new ProductResource($product),
            'Product found'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $this->authorize('update', $product);

        $product->update($request->validated());

        return $this->success(
            new ProductResource($product),
            'Product updated successfully'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);

        $product->delete();
        return $this->success(null, 'Product deleted successfully');
    }
}
