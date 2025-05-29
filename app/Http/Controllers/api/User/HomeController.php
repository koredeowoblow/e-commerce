<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    function index()
    {
        return response()->json(['status' => 'success', 'message' => 'Welcome to the user home page.'], 200);
    }

}
