<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:users,name',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return $this->respondWithToken($user);
    }

    public function login(Request $request)
    {
        $credentials = $request->only(['email', 'password']);

        if (!auth()->attempt($credentials)) {
            return response()->json(['error' => 'Invalid email or password'], 422);
        }
        $user = auth()->user();
        return $this->respondWithToken($user);
    }


    public function user(Request $request)
    {
        return $request->user();
    }

    private function respondWithToken($user)
    {
        $success['token'] = $user->createToken('access_token')->plainTextToken;

        return response()->json([
            'success'=>true,
            'data'=>$success,
            'message'=>'User successsfully registered'
        ]);
    }
}