<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Session;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Admin;

class LoginController extends Controller
{
    public function index()
    {
        return view('user.signin', [
            'title' => 'Sign in'
        ]);
    }

    public function store(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6'],
            'login_type' => ['required', 'in:user,vendor,admin']
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $email = $request['email'];
        $password = $request['password'];
        $loginType = $request['login_type'];

        // Determine model to check against
        if ($loginType === 'user') {
            log::info("User login attempt for email: $email");
            $model = User::where('email', $email)->first();
            $redirect = '/';
        } elseif ($loginType === 'vendor') {
            log::info("Vendor login attempt for email: $email");
            $model = Vendor::where('email', $email)->first();
            $redirect = '/vendor/dashboard';
        } elseif ($loginType === 'admin') {
            log::info("Admin login attempt for email: $email");
            $model = Admin::where('email', $email)->first();
            $redirect = '/admin/dashboard';
        } else {
            throw ValidationException::withMessages(['error' => 'Invalid login type']);
        }

        // Validate password
        if (!$model || !Hash::check($password, $model->password)) {
            throw ValidationException::withMessages(['error' => 'Invalid credentials']);
        }

        // Manual login (since we're not using guards)
        Session::put('auth_user', $model);
        Session::put('auth_user_type', $loginType);

        // Generate JWT
        $payload = [
            'iss' => config('app.name'),
            'sub' => $model->id,
            'iat' => time(),
            'exp' => time() + 3600
        ];
        $jwtToken = JWT::encode($payload, env('JWT_SECRET'), 'HS256');
        log::info("JWT Token generated for user ID: {$model->id}");
        log::info($jwtToken);
        Session::put('jwt_token', $jwtToken);

        return response()->json(['success' => 'Login successful!', 'key' => $jwtToken], 200);
    }

    public function logout(Request $request)
    {
        Session::flush(); // Clears all sessions
        return response()->json(['redirect' => '/signin', 'success' => 'Logged out successfully!'], 200);
    }
}
