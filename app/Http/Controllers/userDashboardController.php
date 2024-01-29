<?php
namespace App\Http\Controllers;
use Carbon\Carbon;
use App\Models\Booking;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Locker;


class UserDashboardController extends Controller{
    public function userDashboard()
    {

        return view('users.userDashboard');
    }

        public function LockerTotal(){
            $lockers=Locker::all();
            return view('users.userDashboard');
        }

        public function UserTotal(){
            $users=User::all();
            return view('users.userDashboard');
        }
}

