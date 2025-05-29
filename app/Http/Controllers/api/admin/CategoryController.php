<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\ChildCategory;
use App\Models\Brand;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    //
    // ---------------- CATEGORY METHODS ----------------

    public function createCategory(Request $request)
    {

        Auth::guard('api-admin')->user();
        $request->validate(['name' => 'required|string|max:255']);

        try {
            $category = Category::create(['category_name' => $request->name]);

            return response()->json([
                'status' => 'success',
                'message' => 'Category created successfully.',
                'data' => $category,
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating category: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to create category.'], 500);
        }
    }

    public function findCategory($id)
    {
        Auth::guard('api-admin')->user();

        $category = Category::find($id);
        if (!$category) {
            return response()->json(
                [
                    'status' => 'error',
                    "message" => 'CATEGORY NOT FOUND'
                ],
                400
            );
        }
        return response()->json($category);
    }

    public function findALLCategory()
    {
        Auth::guard('api-admin')->user();
        $Subcategory = Category::all();
        return response()->json($Subcategory);
    }
    public function updateCategory(Request $request, $id)

    {
        Auth::guard('api-admin')->user();
        $request->validate(['name' => 'required|string|max:255']);
        $category = Category::find($id);
        if (!$category) {
            return response()->json(
                [
                    'status' => 'error',
                    "message" => 'CATEGORY NOT FOUND'
                ],
                400
            );
        }
        try {

            $category->update(['category_name' => $request->name]);

            return response()->json([
                'status' => 'success',
                'message' => 'Category updated successfully.',
                'data' => $category,
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating category: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to update category.'], 500);
        }
    }

    public function deleteCategory($id)
    {
        Auth::guard('api-admin')->user();
        DB::beginTransaction();
        $category = Category::find($id);
        if (!$category) {
            return response()->json(
                [
                    'status' => 'error',
                    "message" => 'CATEGORY NOT FOUND'
                ],
                400
            );
        }
        try {


            // Delete all related subcategories and child categories
            $category->subcategories()->each(function ($subcategory) {
                $subcategory->childCategories()->delete();
                $subcategory->delete();
            });

            $category->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Category deleted successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting category: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to delete category.'], 500);
        }
    }

    // ---------------- SUBCATEGORY METHODS ----------------

    public function createSubcategory(Request $request)
    {
        Auth::guard('api-admin')->user();
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
        ]);

        try {
            $subcategory = Subcategory::create([
                'name' => $request->name,
                'category_id' => $request->category_id,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Subcategory created successfully.',
                'data' => $subcategory,
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating subcategory: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to create subcategory.'], 500);
        }
    }

    public function findSubcategory($id)
    {
        Auth::guard('api-admin')->user();
        $Subcategory = Subcategory::find($id);
        if (!$Subcategory) {
            return response()->json(
                [
                    'status' => 'error',
                    "message" => 'SUBCATEGORY NOT FOUND'
                ],
                400
            );
        }
        return response()->json($Subcategory);
    }

    public function findALLSubcategory()
    {
        Auth::guard('api-admin')->user();
        $Subcategory = Subcategory::all();
        return response()->json($Subcategory);
    }

    public function updateSubcategory(Request $request, $id)
    {
        Auth::guard('api-admin')->user();
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
        ]);
        $subcategory = Subcategory::find($id);
        if (!$subcategory) {
            return response()->json(
                [
                    'status' => 'error',
                    "message" => 'SUBCATEGORY NOT FOUND'
                ],
                400
            );
        }
        try {

            $subcategory->update([
                'name' => $request->name,
                'category_id' => $request->category_id,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Subcategory updated successfully.',
                'data' => $subcategory,
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating subcategory: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to update subcategory.'], 500);
        }
    }

    public function deleteSubcategory($id)
    {
        Auth::guard('api-admin')->user();
        DB::beginTransaction();
        $subcategory = Subcategory::find($id);
        if (!$subcategory) {
            return response()->json(
                [
                    'status' => 'error',
                    "message" => 'SUBCATEGORY NOT FOUND'
                ],
                400
            );
        }

        try {

            // Delete all related child categories
            $subcategory->childCategories()->delete();
            $subcategory->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Subcategory deleted successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting subcategory: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to delete subcategory.'], 500);
        }
    }

    // ---------------- CHILD CATEGORY METHODS ----------------

    public function createChildCategory(Request $request)
    {
        Auth::guard('api-admin')->user();
        $request->validate([
            'name' => 'required|string|max:255',
            'subcategory_id' => 'required|exists:sub_categories,id',
        ]);

        try {
            $childCategory = ChildCategory::create([
                'name' => $request->name,
                'sub_category_id' => $request->subcategory_id,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Child category created successfully.',
                'data' => $childCategory,
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating child category: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to create child category.'], 500);
        }
    }

    public function findChildCategory($id)
    {
        //$admin = Auth::guard('admin')->user();
        $ChildCategory = ChildCategory::find($id);
        if (!$ChildCategory) {
            return response()->json(
                [
                    'status' => 'error',
                    "message" => 'CHILD CATEGORY NOT FOUND'
                ],
                400
            );
        }
        return response()->json($ChildCategory);
    }
    public function findALLChildcategory()
    {
        Auth::guard('api-admin')->user();
        $Subcategory = Childcategory::all();
        return response()->json($Subcategory);
    }

    public function updateChildCategory(Request $request, $id)
    {
        Auth::guard('api-admin')->user();
        $request->validate([
            'name' => 'required|string|max:255',
            'subcategory_id' => 'required|exists:sub_categories,id',
        ]);
        $childCategory = ChildCategory::find($id);
        if (!$childCategory) {
            return response()->json(
                [
                    'status' => 'error',
                    "message" => 'CHILD CATEGORY NOT FOUND'
                ],
                400
            );
        }
        try {

            $childCategory->update([
                'name' => $request->name,
                'sub_category_id' => $request->subcategory_id,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Child category updated successfully.',
                'data' => $childCategory,
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating child category: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to update child category.'], 500);
        }
    }

    public function deleteChildCategory($id)
    {
        Auth::guard('api-admin')->user();
        try {
            $childCategory = ChildCategory::findOrFail($id);
            $childCategory->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Child category deleted successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting child category: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to delete child category.'], 500);
        }
    }

    // ---------------- BRAND METHODS ----------------

    public function createBrand(Request $request)
    {
        Auth::guard('api-admin')->user();
        $request->validate([
            'brand_name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'sub_category_id' => 'nullable|exists:sub_categories,id',
            'child_category_id' => 'nullable|exists:child_categories,id',
            'slug' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        try {
            $brand = Brand::create($request->all());

            return response()->json([
                'status' => 'success',
                'message' => 'Brand created successfully.',
                'data' => $brand,
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating brand: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to create brand.'], 500);
        }
    }

    public function findBrand($id)
    {
        Auth::guard('api-admin')->user();
        $Brand = Brand::find($id);
        if (!$Brand) {
            return response()->json(
                [
                    'status' => 'error',
                    "message" => 'BRAND NOT FOUND'
                ],
                400
            );
        }
        return response()->json($Brand);
    }

    public function findALLBrand()
    {
        Auth::guard('api-admin')->user();
        $Brand = Brand::all();
        return response()->json($Brand);
    }

    public function updateBrand(Request $request, $id)
    {
        //$admin = Auth::guard('admin')->user();
        $request->validate([
            'brand_name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'sub_category_id' => 'nullable|exists:sub_categories,id',
            'child_category_id' => 'nullable|exists:child_categories,id',
            'slug' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $brand = Brand::find($id);
        if (!$brand) {
            return response()->json(
                [
                    'status' => 'error',
                    "message" => 'BRAND NOT FOUND'
                ],
                400
            );
        }
        try {

            $data = $request->all();
            $brand->update($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Brand updated successfully.',
                'data' => $brand,
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating brand: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to update brand.'], 500);
        }
    }

    public function deleteBrand($id)
    {

        $brand = Brand::find($id);
        if (!$brand) {
            return response()->json(
                [
                    'status' => 'error',
                    "message" => 'BRAND NOT FOUND'
                ],
                400
            );
        }
        try {

            $brand->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Brand deleted successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting brand: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to delete brand.'], 500);
        }
    }
}
