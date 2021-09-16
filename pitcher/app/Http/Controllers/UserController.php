<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use Cache;
use App\Models\User;

class UserController extends Controller
{
    public function index(){
        return User::all();
    }

    public function profile(){
        $user = Auth::user();
        return ['profile' => $user];
    }

    public function userOnlineStatus()
    {
        $users = DB::table('users')->get();
    
        foreach ($users as $user) {
            if (Cache::has('is_online' . $user->id))
                echo $user->role . " " . $user->full_name . " is online.\r\n";
            else
                echo $user->role . " " . $user->full_name . " is offline. Last seen" . $user->last_seen . "\r\n";
        }
    }

}
