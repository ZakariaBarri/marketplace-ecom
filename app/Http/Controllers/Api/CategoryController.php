<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;

use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->success(
            Category::latest()->get(),
            'Categories retrieved successfully'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('admin-only');

        $request->validate([
            'name' => 'required|string|unique:categories,name'
        ]);

        $category = Category::create($request->only('name'));

        return $this->success(
            $category,
            'Category created successfully',
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        Gate::authorize('admin-only');

        /*if (!$category) {
            return $this->error('Category not found', null, 404);
        }*/
            
        $request->validate([
            'name' => [
                'required',
                'string',
                Rule::unique('categories', 'name')->ignore($category->id)
            ]
        ]);

        $category->update($request->only('name'));

        return $this->success($category, 'Category updated successfully',);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        Gate::authorize('admin-only');

        $category->delete();

        return $this->success(null, 'Category deleted successfully',);
    }
}
