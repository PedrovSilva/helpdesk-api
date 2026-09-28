<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResouce;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        $categories = Category::query()->orderBy('name')->paginate(15);

        return CategoryResouce::collection($categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request): CategoryResouce
    {
        $category = Category::create($request->validated());

        return new CategoryResouce($category);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category): CategoryResouce
    {
        return new CategoryResouce($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category): CategoryResouce
    {
        $category->update($request->validated());

        return new CategoryResouce($category->refresh());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category): JsonResponse
    {
        $category->delete();

        return response()->json(null, 204);
    }
}
