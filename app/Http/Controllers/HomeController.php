<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Locker;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if(Auth::id())
        {
            $role_id=Auth()->user()->role_id;
            if($role_id=='1')
            {
                $totalUsers = User::count();
                $totalLockers = Locker::count();
                $totalBookings = Booking::where('status', 'active')->orWhere('status', 'processing')->count();

                return view('home', compact('totalUsers', 'totalLockers', 'totalBookings'));

            }
            else if($role_id=='2')
            {
                return view('users.userDashboard');
            }
            else
            {
                return redirect()->back();
            }
        }
    }
    public function adminBookings()
    {
        return view('admin.adminBookings');
    }
}
