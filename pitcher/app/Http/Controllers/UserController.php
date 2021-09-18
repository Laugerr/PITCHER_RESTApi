<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use Cache;
use Storage;
use File;
use Carbon\Carbon;
use App\Models\User;

class UserController extends Controller
{
    public function index(){
        return User::all();
    }

    public function profile(){
        $user = Auth::user();

        if(Auth::check()){
            return ['profile' => $user];
        }
        else{
            return response([
                'message' => 'You\'re not logged in!'
            ], 404);
        }
    }

    public function userOnlineStatus()
    {
        $users = DB::table('users')->get();
        
        foreach ($users as $user) {
            $online = $user->full_name . " is Online\u{1F929}\r\n";
            $offline = $user->full_name . " is Offline\u{1F634} | Last seen " . Carbon::parse($user->last_seen)->diffForHumans() . "\r\n";

            if (Cache::has('is_online' . $user->id))
                echo "\u{1F7E2} | " . $online;
            else
                echo "\u{1F534} | " .$offline;
        }
    }

    public function store(Request $request) {
        if(Auth::user()){
        $validate = $request->validate([
            'login' => 'required|string|unique:users,login|max:21|min:3',
            'full_name' => 'required|regex:/^[\pL\s\-]+$/u|max:50|min:3',
            'email' => 'required|unique:users,email|email',
            'role' => 'in:Admin,User',
            'password' => 'required|string|confirmed|min:6',
        ]);

        $user = User::create([
            'login' => $validate['login'],
            'full_name' => $validate['full_name'],
            'email' => $validate['email'],
            'role' => $validate['role'],
            'profile_picture' => '/images/defaultProfile.png',
            'password' => bcrypt($validate['password'])
        ]);

        $token = $user->createToken('mypitchertoken')->plainTextToken;

        $response = [
            '' => '============Account Created by Admin Successfully !============',
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);}
        else{
            return response(['You\'re not Logged in!'], 403);
        };
    }

    public function avatar_create(Request $request) {
        $validate = $request->validate([
            'profile_picture' => 'nullable|image|mimes:png|max:4096'
        ]);

        $user = Auth::user();

        $file = file_get_contents($validate['profile_picture']);
        $path = '/images/profiles/' . $user['login'] . '.png';
        file_put_contents(public_path() . $path, $file);

        $user = User::find($user['id']);
        $user->update([
            'profile_picture' => ".$path"
        ]);

        return response([
            'message' => 'Account updated successfully'
        ], 201);
    }
}