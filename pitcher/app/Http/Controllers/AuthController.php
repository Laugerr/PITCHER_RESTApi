<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request){
        $validate = $request->validate([
            'login' => 'required|string|unique:users,login|max:21|min:3',
            'full_name' => 'required|alpha|max:50|min:3',
            'email' => 'required|unique:users,email|email',
            'password' => 'required|string|confirmed|min:6',
        ]);

        $user = User::create([
            'login' => $validate['login'],
            'full_name' => $validate['full_name'],
            'email' => $validate['email'],
            'password' => bcrypt($validate['password'])
        ]);

        $token = $user->createToken('mypitchertoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    public function login(Request $request) {
        $validate = $request->validate([
            'login' => 'required|string|exists:users,login|max:21|min:3',
            'password' => 'required|string|min:6'
        ]);

        $user = User::where('login', $validate['login'])->first();

        if(!$user || !Hash::check($validate['password'], $user->password)) {
            return response([
                'message' => 'Incorrect Login or Password, Try Again!'
            ], 401);
        }

        $token = $user->createToken('mypitchertoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    public function logout(Request $request) {
        Auth::logout();

        return [
            'message' => 'Logged out!'
        ];
    }
}
