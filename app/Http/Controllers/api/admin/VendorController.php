<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vendor;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\QueryException;
use Illuminate\support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class VendorController extends Controller
{
    //

    // Get all vendors with product count for DataTable
    public function getVendorsWithProductCount(Request $request)
    {
        Auth::guard('api-admin')->user();
        $vendors = Vendor::withCount('product')->get();

        return DataTables::of($vendors)
            ->addColumn('product_count', function ($vendor) {
                return $vendor->product_count;
            })->editColumn('profile_image', function ($row) {
                $imagePath = $row->profile_image ? asset('storage/' . $row->profile_image) : 'images/placeholder.png';
                return '<img src="' . $imagePath . '" width="50" height="50" alt="User Image"/>';
            })
            ->rawColumns(['profile_image']) // Mark the 'profile_image' column as raw to render HTML
            ->editColumn('logo', function ($row) {
                $logo = $row->logo ? asset('storage/' . $row->logo) : 'images/placeholder.png';
                return '<img src="' . $logo . '" width="50" height="50" alt="User Logo"/>';
            })
            ->rawColumns(['logo']) // Mark the 'logo' column as raw to render HTML
            ->make(true);
    }
    // Create a new vendor
    public function create(Request $request)
    {
          Auth::guard('api-admin')->user();
        Log::info('Vendor creation started');
        $validator = Validator::make($request->all(), [
            'vendor_name' => 'required|string|max:255|unique:vendors',
            'slug' => 'required|string|max:255|unique:vendors',
            'email' => 'required|string|email|max:255|unique:vendors',
            'phone_number' => 'required|string|max:15|unique:vendors',
            'company_name' => 'required|string|max:255|unique:vendors',
            'description' => 'nullable|string',
            'address' => 'required|string',
            'logo' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:5120',
            'password' => 'required|string|min:4',
            'profile_image' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:5120',
            'country_id' => 'required|integer|exists:countries,id',
            'state_id' => 'required|integer|exists:states,id',
            'city_id' => 'required|integer|exists:cities,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $data = $request->except(['logo', 'profile_image', 'password']);
            $data['password'] = Hash::make($request->password);

            if ($request->hasFile('logo')) {
                $data['logo'] = $request->file('logo')->store('logos/', 'public');
            }

            if ($request->hasFile('profile_image')) {
                $data['profile_image'] = $request->file('profile_image')->store('profile_images/', 'public');
            }

            $vendor = Vendor::create($data);

            if (!$vendor) {
                return response()->json([
                    'message' => 'Vendor creation failed.',
                ], 500);
            }
            return response()->json([
                'message' => 'Vendor created successfully.',
                'vendor' => $vendor,
            ], 201);
        } catch (QueryException $e) {
            Log::error('Database error during vendor creation', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Database error during vendor creation.',
                'error' => $e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            Log::error('Vendor creation failed', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Unexpected error during vendor creation.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Get all vendors
    // public function index()
    // {
    //     $vendors = Vendor::all();
    //     return response()->json($vendors, 200);
    // }

    // Get a single vendor by ID

    public function find($id)
    {
        Auth::guard('api-admin')->user();

        $vendor = Vendor::withCount('product')->with('countries:id,name', 'states:id,name', 'cities:id,name')->find($id);

        if (!$vendor) {
            return response()->json(['message' => 'Vendor not found'], 404);
        }


        // Format the image URLs
        $vendor->profile_image_url = $vendor->profile_image
            ? asset('storage/' . $vendor->profile_image)
            : asset('images/placeholder.png');

        $vendor->logo_url = $vendor->logo
            ? asset('storage/' . $vendor->logo)
            : asset('images/placeholder.png');

        return response()->json([
            'message' => 'Vendor fetched successfully',
            'vendor' => $vendor
        ], 200);
    }


    // Update a vendor
    public function update(Request $request, $id)
    {
        Auth::guard('api-admin')->user();

        $vendor = Vendor::find($id);


        if (!$vendor) {
            return response()->json(data: ['message' => 'Vendor not found'], status: 404);
        }

        $validator = Validator::make($request->all(), [
            'vendor_name' => 'sometimes|string|max:255|unique:vendors',
            'slug' => 'sometimes|string|max:255|unique:vendors',
            'email' => 'sometimes|string|email|max:255|unique:vendors',
            'phone_number' => 'sometimes|string|max:15|unique:vendors',
            'company_name' => 'sometimes|string|max:255|unique:vendors',
            'description' => 'nullable|string',
            'address' => 'sometimes|string',
            'logo' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:5120',
            'password' => 'sometimes|string|min:4',
            'profile_image' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:5120',
            'country_id' => 'sometimes|integer|exists:countries,id',
            'state_id' => 'sometimes|integer|exists:states,id',
            'city_id' => 'sometimes|integer|exists:cities,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $data = $request->except(['logo', 'profile_image', 'password']);

            // Handle logo upload
            if ($request->hasFile('logo')) {
                if ($vendor->logo) {
                    Storage::disk('public')->delete($vendor->logo);
                }
                $data['logo'] = $request->file('logo')->store('logos', 'public');
            }

            // Handle profile_image upload
            if ($request->hasFile('profile_image')) {
                if ($vendor->profile_image) {
                    Storage::disk('public')->delete($vendor->profile_image);
                }
                $data['profile_image'] = $request->file('profile_image')->store('vendors', 'public');
            }

            // Hash password if present
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            try {
                $vendor->update($data);
                if ($vendor->wasChanged() === false) {
                    return response()->json([
                        'message' => 'Vendor update failed.',
                    ], 500);
                }
                return response()->json([
                    'message' => 'Vendor updated successfully',
                    'vendor' => $vendor
                ], 200);
            } catch (QueryException $e) {
                return response()->json([
                    'message' => 'Database error while updating vendor',
                    'error' => $e->getMessage(), // Optional: hide in production
                ], 500);
            }
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Database error while updating vendor',
                'error' => $e->getMessage(), // Optional: hide in production
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Unexpected error occurred',
                'error' => $e->getMessage(), // Optional: hide in production
            ], 500);
        }
    }

    // Delete a vendor
    public function delete($id)
    {
        Auth::guard('api-admin')->user();

        $vendor = Vendor::find($id);

        if (!$vendor) {
            return response()->json(['message' => 'Vendor not found'], 404);
        }

        // Delete profile image if it exists
        if (!empty($vendor->profile_image)) {
            Storage::disk('public')->delete($vendor->profile_image);
        }

        // Delete logo if it exists
        if (!empty($vendor->logo)) {
            Storage::disk('public')->delete($vendor->logo);
        }

        $vendor->delete();

        return response()->json(['message' => 'Vendor deleted successfully'], 200);
    }
}
