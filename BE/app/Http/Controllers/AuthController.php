<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|min:3',
            'c_password' => 'required|same:password'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, $validator->messages()]);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password
        ]);
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'user does not created!']);
        }
        $token = $user->createToken('myApp')->plainTextToken;
        return response()->json([
            'status' => true,
            'user' => $user,
            'token' => $token
        ]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:3',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, $validator->messages()]);
        }
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'user does not exist']);
        }
        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['status' => false, 'message' => 'password is incorrect!']);
        }
        $token = $user->createToken('myApp')->plainTextToken;
        return response()->json(['status' => true, 'token' => $token]);
    }

    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();
        return response()->json(['status' => true, 'message' => 'successfuly loged out']);
    }
}
