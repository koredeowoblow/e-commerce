<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public  function index()
    {
        // Check if the user is authenticated
        $id = Auth::guard('api-user')->id();
        if ($id == null) {
            return response()->json(["message" => 'you are not logged in'], 401);
        }
        $user = User::findOrFail($id);
        return response()->json($user);
    }
    public function update(Request $request)
    {
        $id = Auth::guard('api-user')->id() ;
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'user not found'], 400);
        }

        $validator = $request->validate([
            'user_name' => 'string|string|max:255',
            'slug' => 'string|string|max:255|unique:users,slug,' . $id,
            'email' => 'string|string|email|max:255|unique:users,email,' . $id,
            'phone_number' => 'string|string|max:15',
            'company_name' => 'string|string|max:255',
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'logo' => 'nullable|string',
            'status' => 'sometimes|boolean',
            'is_verified' => 'sometimes|boolean',
            'email_verified' => 'sometimes|boolean',
            'email_verify_token' => 'nullable|string',
            'email_verified_at' => 'nullable|date',
            'password' => 'nullable|string|min:8',
            'profile_image' => 'nullable|string',
            'country_id' => 'nullable|integer',
            'state_id' => 'nullable|integer',
            'city_id' => 'nullable|integer',
        ]);



        $data = $request->all();

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return response()->json(['message' => 'user updated successfully', 'user' => $user], 200);
    }
}
