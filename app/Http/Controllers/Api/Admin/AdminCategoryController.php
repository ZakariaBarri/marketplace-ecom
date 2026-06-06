<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\AdminCategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

use App\Traits\ApiResponse;
use Illuminate\Validation\Rule;

class AdminCategoryController extends Controller
{
    use ApiResponse;
    /**
     * 📋 List categories
     */
    public function index()
    {
        $categories = Category::withCount('products')->latest()->paginate(20);

        return AdminCategoryResource::collection($categories)->additional([
            'success' => true,
            'message' => 'Categories retrieved successfully'
        ]);
    }

    /**
     * ➕ Create category
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:25|unique:categories,name',
        ]);

        $category = Category::create($request->only('name'));

        return $this->success(
            new AdminCategoryResource($category),
            'Category created successfully',
            201
        );
    }

    /**
     * 👁️ Show category
     */
    public function show(Category $category)
    {
        $category->loadCount('products');

        return $this->success(
            new AdminCategoryResource($category),
            'Category retrieved successfully'
        );
    }

    /**
     * ✏️ Update category
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => [
                'required',
                'string','max:25',
                Rule::unique('categories', 'name')->ignore($category->id)
            ]
        ]);

        $category->update($request->only('name'));

        return $this->success(new AdminCategoryResource($category), 'Category updated successfully');
    }

    /**
     * 🗑️ Delete category
     */
    public function destroy(Category $category)
    {
        // optional safety check
        if ($category->products()->exists()) {
            return $this->error('Cannot delete category with products', null, 422);
        }

        $category->delete();

        return $this->success(null, 'Category deleted successfully',);
    }
}
