<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\QueryException;
use App\Models\Vendor;
use App\Models\Admin;
use App\Models\countries;
use App\Models\States;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendOtpMail;

class RegisterController extends Controller
{
    public function index(Request $request)
    {
        $route = $request->route()->getName();

        $data = match ($route) {
            'vendor.register' => ['title' => 'Create Stumak Vendor Account', 'data' => 'vendor'],
            'admin.register' => ['title' => 'Create Stumak Admin Account', 'data' => 'admin'],
            default => ['title' => 'Create Stumak User Account', 'data' => 'user'],
        };

        return view('user.register', $data);
    }

    public function fetch_country()
    {
        if (!countries::exists()) {
            return response()->json(['message' => 'No countries found'], 404);
        }
        return response()->json(countries::all());
    }

    public function fetch_state($id)
    {
        if (!States::where('country_id', $id)->exists()) {
            return response()->json(['message' => 'No states found for this country'], 404);
        }
        return response()->json(States::where('country_id', $id)->get());
    }

    // public function store(Request $request)
    // {
    //     $route = $request->route()->getName();

    //     return match ($route) {
    //         'vendor.store' => $this->registerVendor($request),
    //         'admin.store' => $this->registerAdmin($request),
    //         default => $this->registerUser($request),
    //     };
    // }

    public function registerUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'phone' => 'required|numeric|digits_between:7,12,|unique:users,phone',
            'matric_no' => 'required|string|max:255|unique:users,matric_no',
            'country_id' => 'required|exists:countries,id',
            'state_id' => 'required|exists:states,id',
            'city_id' => 'required|exists:cities,id', // Uncomment if cities table exists
            'post_code' => 'nullable|string|max:10',
            'street_address' => 'nullable|string|max:255',
            'password' => 'required|string|min:6|confirmed',
            'email' => 'required|email|unique:users,email|max:255'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $otp = random_int(100000, 999999);

            $data = $request->only([
                'first_name',
                'last_name',
                'middle_name',
                'phone',
                'matric_no',
                'country_id',
                'state_id',
                'post_code',
                'street_address',
                'email'
            ]);
            $otp = rand(100000, 999999);

            $data['email_verify_token'] = Hash::make($otp);
            $data['verified_status'] = 'unverified';
            $data['password'] = Hash::make($request->password);

            $user = User::create($data);
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User creation failed.'], 500);
            }
            Mail::to($user->email)->send(new SendOtpMail($user));


            return response()->json([
                'success' => true,
                'message' => 'User registered successfully! Please verify your email.',
                'data' => [
                    'email' => $user->email,
                    'matric_no' => $user->matric_no,
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Registration failed.', 'error' => $e->getMessage()], 500);
        }
    }
    private function sendVerificationMail($user)
    {
        Mail::to($user->email)->send(new SendOtpMail($user));
    }

    public function registerVendor(Request $request)
    {
        $request->validate([
            'vendor_name' => 'required|string|max:255',
            'slug' => 'required|string|unique:vendors,slug',
            'email' => 'required|email|unique:vendors,email',
            'phone_number' => 'nullable|string|max:20',
            'company_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'password' => 'required|string|min:6|confirmed',
            'country_id' => 'nullable|exists:countries,id',
            'state_id' => 'nullable|exists:states,id',
            // 'city_id' => 'nullable|exists:cities,id', // Uncomment if cities table exists
        ]);

        try {
            $otp = random_int(100000, 999999);

            $data = $request->only([
                'vendor_name',
                'slug',
                'email',
                'phone_number',
                'company_name',
                'description',
                'address',
                'country_id',
                'state_id'
            ]);

            $data['password'] = Hash::make($request->password);
            $data['email_verify_token'] = Str::random(64);
            $data['is_verified'] = false;

            $vendor = Vendor::create($data);

            session(['email_otp' => $otp, 'verifying_email' => $vendor->email]);

            return response()->json([
                'success' => true,
                'message' => 'Vendor registered successfully!',
                'data' => [
                    'email' => $vendor->email,
                    'slug' => $vendor->slug,
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Vendor registration failed.', 'error' => $e->getMessage()], 500);
        }
    }

    public function registerAdmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|string|min:6|confirmed',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        try {
            $data = $request->only(['name', 'email']);
            $data['password'] = Hash::make($request->password);

            Log::info("Started creating admin");

            $admin = Admin::create($data);
            if (!$admin) {
                return response()->json([
                    'message' => 'Admin creation failed.',
                ], 500);
            }
            return response()->json([
                'success' => true,
                'message' => 'Admin registered successfully!',
                'data' => [
                    'email' => $admin->email,
                    'name' => $admin->name
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Admin registration failed.', 'error' => $e->getMessage()], 500);
        }
    }

    public function verifyEmail(Request $request)
    {
        $token = $request->query('token');

        $user = User::where('email_verify_token', $token)->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Invalid or expired verification link.'], 400);
        }

        $user->email_verify_token = null;
        $user->verified_status = 'verified';
        $user->save();

        return response()->json(['success' => true, 'message' => 'Email verified successfully!']);
    }
}
