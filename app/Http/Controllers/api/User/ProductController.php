<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{

    /**
     * Display a listing of the products with optional filters and sorting.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand', 'subCategory', 'childCategory', 'country', 'state']);

        // Relationship filters: key => [relationship, column]
        $filters = [
            'category' => ['category', 'category_name'],
            'brand' => ['brand', 'brand_name'],
            'subcategory' => ['subCategory', 'name'],
            'child_category' => ['childCategory', 'name'],
            'country' => ['country', 'name'],
            'state' => ['state', 'name'],
        ];

        foreach ($filters as $inputKey => [$relation, $column]) {
            if ($request->filled($inputKey)) {
                $query->whereHas($relation, function ($q) use ($request, $inputKey, $column) {
                    $q->where($column, $request->input($inputKey));
                });
            }
        }

        // Search by product title or description
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%");
            });
        }

        // Sort logic
        $sort = $request->input('sort');
        match ($sort) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            default => $query->orderBy('created_at', 'desc'),
        };

        // Pagination
        $products = $query->paginate(12);

        return response()->json([
            'products' => $products->items(),
            'hasMore' => $products->hasMorePages(),
        ]);
    }

    /**
     * Fetch more products based on the provided filters and sorting.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchMoreProducts(Request $request)
    {
        $query = Product::query();

        // Apply filters (similar to the `index` method)
        if ($request->has('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('name', $request->input('category'));
            });
        }
        // Filter by category
        if ($request->has('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('name', $request->input('category'));
            });
        }

        // Filter by brand
        if ($request->has('brand')) {
            $query->whereHas('brand', function ($q) use ($request) {
                $q->where('name', $request->input('brand'));
            });
        }

        // Filter by subcategory
        if ($request->has('subcategory')) {
            $query->whereHas('subcategory', function ($q) use ($request) {
                $q->where('name', $request->input('subcategory'));
            });
        }

        // Filter by child category
        if ($request->has('child_category')) {
            $query->whereHas('childCategory', function ($q) use ($request) {
                $q->where('name', $request->input('child_category'));
            });
        }

        // Filter by country
        if ($request->has('country')) {
            $query->whereHas('country', function ($q) use ($request) {
                $q->where('name', $request->input('country'));
            });
        }

        // Filter by state
        if ($request->has('state')) {
            $query->whereHas('state', function ($q) use ($request) {
                $q->where('name', $request->input('state'));
            });
        }



        // Search by product name or description
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->input('search') . '%')
                    ->orWhere('description', 'like', '%' . $request->input('search') . '%');
            });
        }

        // Sort by price, popularity, or other criteria
        if ($request->has('sort')) {
            switch ($request->input('sort')) {
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc'); // Default sorting
            }
        } else {
            $query->orderBy('created_at', 'desc'); // Default sorting
        }

        $products = $query->paginate(12); // Return the next set of products

        return response()->json([
            'products' => $products->items(),
            'hasMore' => $products->hasMorePages(),
        ]);
    }


    /**
     * Show the details of a specific product.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $product = Product::with([
            'category:id,category_name',
            'state:id,name',
            'city:id,name',
            'subCategory:id,name',
            'childCategory:id,name',
            'brand:id,brand_name',
            'vendor:id,vendor_name,email,phone_number,company_name', // Optional: limit vendor fields
            'ProductAttribute' => function ($query) {
                $query->select('id', 'product_id', 'attribute_id', 'value')->with([
                    'attribute:id,name'
                ]);
            }
        ])->findOrFail($id);

        return response()->json([
            'product' => $product,
        ]);
    }
}
