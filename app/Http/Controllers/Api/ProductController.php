<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

use App\Http\Requests\UpdateProductRequest;

use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Storage;

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
            $products->where('title', 'like', '%' . trim($request->search) . '%');
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
        if ($request->has('min_price')) {
            $products->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price')) {
            $products->where('price', '<=', $request->max_price);
        }

        // ترتيب من الأحدث للأقدم
        //$products = $products->orderBy('created_at', 'desc')->paginate(10);
        // Pagination
        $products = $products->latest()->paginate(10);

        return ProductResource::collection($products)->additional([
            'success' => true,
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
            'seller_id' => auth()->id(),
            'status' => 'available'
        ]);

        foreach ($request->file('images') as $index => $image) {
            // حفظ الصورة
            $path = $image->store('products', 'public');

            // حفظ في DB
            $product->images()->create([
                'path' => $path,
                'is_main' => $index === 0 // أول صورة هي الرئيسية
            ]);
        }

        return $this->success(
            new ProductResource(
                $product->load(['images', 'condition', 'category', 'seller'])
            ),
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
        return $this->success(
            new ProductResource(
                $product->load(['images', 'condition', 'category', 'seller'])
            ),
            'Product found'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $this->authorize('update', $product);

        $product->update($request->only([
            'title',
            'description',
            'price',
            'condition_id',
            'category_id'
        ]));

        // حذف الصور
        if ($request->filled('delete_image_ids')) {
            $imagesToDelete = $product->images()->whereIn('id', $request->delete_image_ids)->get();
            foreach ($imagesToDelete as $img) {
                Storage::disk('public')->delete($img->path);
                $img->delete();
            }
        }

        // رفع الصور الجديدة
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $imgFile) {
                $path = $imgFile->store('products', 'public');
                $product->images()->create([
                    'path' => $path,
                    'is_main' => false
                ]);
            }
        }

        // تغيير main image
        if ($request->filled('main_image_id')) {
            $product->images()->update(['is_main' => false]);
            $product->images()->where('id', $request->main_image_id)->update(['is_main' => true]);
        }

        // ضمان وجود main_image
        if (!$product->images()->where('is_main', true)->exists()) {
            $first = $product->images()->first();
            if ($first) {
                $first->update(['is_main' => true]);
            }
        }

        return $this->success(
            new ProductResource($product->load(['images', 'condition', 'category', 'seller'])),
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
