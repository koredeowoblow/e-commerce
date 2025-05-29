<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\ChildCategory;
use App\Models\Brand;
use Illuminate\Support\Facades\Log;

class SearchController extends Controller
{
    // Search products by title
    public function search(string $title)
    {
        // Search for products with the given title
        $products = Product::with(['vendor:id,vendor_name', 'category:id,category_name', 'brand:id,brand_name', 'subcategory:id,name', 'childcategory:id,name'])
            ->where('title', 'like', '%' . $title . '%')
            ->get();

        if ($products->isEmpty()) {
            return response()->json(['status' => 'fail', 'message' => 'No products found.'], 404);
        }

        return response()->json(['status' => 'success', 'products' => $products], 200);
    }

    // Get all categories
    public function getCategory()
    {
        $categories = Category::all();
        return response()->json($categories, 200);
    }

    // Get subcategories for a given category
    public function getSubCategoriesByCategory($categoryId)
    {
        try {
            $subCategories = SubCategory::where('category_id', $categoryId)->get();
            return response()->json($subCategories);
        } catch (\Exception $e) {
            Log::error('Failed to fetch subcategories: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch subcategories.'], 500);
        }
    }

    // Get child categories for a given subcategory
    public function getChildCategoriesBySubCategory($subCategoryId)
    {
        try {
            $childCategories = ChildCategory::where('sub_category_id', $subCategoryId)->get();
            return response()->json($childCategories);
        } catch (\Exception $e) {
            Log::error('Failed to fetch child categories: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch child categories.'], 500);
        }
    }

    // Get brands by category, subcategory, and child category
    public function getBrands(Request $request)
    {

        try {
            if ($request->categoryId && !$request->subCategoryId && !$request->childCategoryId) {
                $brands = Brand::where('category_id', $request->categoryId)->get();
            } elseif ($request->categoryId && $request->subCategoryId && !$request->childCategoryId) {
                $brands = Brand::where('category_id', $request->categoryId)
                    ->where('sub_category_id', $request->subCategoryId)
                    ->get();
            } elseif ($request->categoryId && $request->subCategoryId && $request->childCategoryId) {
                $brands = Brand::where('category_id', $request->categoryId)
                    ->where('sub_category_id', $request->subCategoryId)
                    ->where('child_category_id', $request->childCategoryId)
                    ->get();
            } else {
                return response()->json(['error' => 'Invalid request.'], 400);
            }

            if ($brands->isEmpty()) {
                return response()->json(['message' => 'No brands found.'], 404);
            }

            return response()->json($brands);
        } catch (\Exception $e) {
            Log::error('Failed to fetch brands: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch brands.'], 500);
        }
    }
}
