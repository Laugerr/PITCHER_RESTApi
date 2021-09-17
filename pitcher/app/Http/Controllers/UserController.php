<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use Cache;
use Carbon\Carbon;
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
            $online = $user->full_name . " is Online\u{1F929}\r\n";
            $offline = $user->full_name . " is Offline\u{1F634} | Last seen " . Carbon::parse($user->last_seen)->diffForHumans() . "\r\n";

            if (Cache::has('is_online' . $user->id))
                echo "\u{1F7E2} | " . $online;
            else
                echo "\u{1F534} | " .$offline;
        }
    }
}