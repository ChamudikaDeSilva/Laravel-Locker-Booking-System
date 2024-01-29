<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class userProfileController extends Controller
{
    public function showProfile()
{
    $user = Auth::user();
    return view('users.profile', compact('user'));
}

public function updateProfile(Request $request)
{
    $user = Auth::user(); 

    $request->validate([
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'faculty' => 'required|string|max:255',
        'registration_number' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'required|numeric',
        'password' => 'nullable|string|min:6', // Adjust as needed
    ]);

    $user->update([
        'first_name' => $request->input('first_name'),
        'last_name' => $request->input('last_name'),
        'email' => $request->input('email'),
        'faculty' => $request->input('faculty'),
        'registration_number' => $request->input('registration_number'),
        'phone' => $request->input('phone'),
        'password' => bcrypt($request->input('password')),
    ]);

    return redirect()->route('profile')->with('success', 'Profile updated successfully!');
}

}
