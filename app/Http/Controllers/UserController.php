<?php

namespace App\Http\Controllers;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Log;


use Illuminate\Http\Request;

class UserController extends Controller
{
    public function allBookingHistory()
    {
        // Get all booking history
        $booking = Booking::all();

        // Pass the booking history to the view
        return view('users.AllbookingHistory', compact('booking'));
    }

    public function dashboard()
    {
        // Your dashboard logic here
        return view ('users.userDashboard');
    }

    public function topUp()
    {
        // Your top-up logic here
        return view('users.topUp');
    }

    public function paymentHistory()
    {
        // Your payment history logic here
        return view('users.paymentHistory');
    }

    public function bookLocker()
    {
        // Your book locker logic here
        return view('users.booking');
    }

    public function bookingHistory()
    {
        // Your booking history logic here
        return view('users.bookingHistory');
    }

    public function contact()
    {
        // Your contact logic here
        return view('users.contact');
    }

    public function profile()
    {
        // Your profile logic here
        return view('users.profile');
    }

    //user management
    /*public function index()
    {
        $users = User::all();
        return view('admin.userManagement', ['users' => $users]);
    }*/

    //displaying admin management page
    public function index()
    {
        $usersWithRoleId1 = User::where('role_id', 1)->simplePaginate(10);

        return view('admin.adminManagement', compact('usersWithRoleId1'));
    }
    //displaying user management page
    public function index01()
    {
        $usersWithRoleId2 = User::where('role_id', 2)->simplePaginate(3);

        return view('admin.userManagement', compact('usersWithRoleId2'));
    }

    //adding new admins
    public function saveAdmin(Request $request)
    {
        try {
            // Validate the request
            $validator = $request->validate([
                'role_id' => 'required',
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required|email|unique:users,email',
                'phone' => 'required',
                'registration_number' => 'required',
                'faculty' => 'required',
                'password' => 'required|min:6|confirmed',

            ]);

            // Create a new user using the data from the request
            $user = new User([
                'role_id' => $request->input('role_id'),
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'registration_number' => $request->input('registration_number'),
                'faculty' => $request->input('faculty'),
                'password' => bcrypt($request->input('password')),
            ]);

            // Save the user
            $user->save();

            // Return a success response
            return response()->json(['status' => 'success', 'user' => $user]);
        }
        catch (\Exception $e) {
            // Log the exception
            Log::error('Error saving admin: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());


            // Return an error response
           return response()->json(['status' => 'error', 'message' => 'Error saving admin: ' . $e->getMessage()], 500);

        }
    }


    //geting user id in admins edit
    public function getUserDetails($id) {
        $user = User::find($id);
        return response()->json($user);
    }

    //updating admins
    public function updateUserDetails(Request $request)
    {
        // Validate the request data
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,email,' . $request->input('user_id'),
            'phone' => 'required',
            'registration_number' => 'required',
            'faculty' => 'required',
            'password' => 'nullable|min:6|confirmed', // Allow null password for not updating it
        ]);

        // Update user details
        $user = User::find($request->input('user_id'));
        $user->role_id = $request->input('role_id');
        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->email = $request->input('email');
        $user->phone = $request->input('phone');
        $user->registration_number = $request->input('registration_number');
        $user->faculty = $request->input('faculty');

        // If password fields are present in the form and not empty, update password
        if ($request->filled('password')) {
            $user->password = bcrypt($request->input('password'));
        }

        $user->save();

        return response()->json(['message' => 'User details updated successfully', 'user' => $user]);
    }

    //deleting an admin
    public function deleteUser(Request $request) {
        try {
            $userId = $request->input('userId');
            $deletedCount = User::where('id', $userId)->delete();

            if ($deletedCount > 0) {
                // Return a success response
                return response()->json(['success' => true, 'message' => 'User deleted successfully']);
            } else {
                // Return a response indicating that no user was found for deletion
                return response()->json(['success' => false, 'message' => 'User not found for deletion']);
            }
        } catch (\Exception $e) {
            // Handle exceptions or errors
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    //viewing admin details
    public function getUser($userId)
    {
        $user = User::find($userId);

        return response()->json($user);
    }

    public function getAllUsers()
    {
        $users = User::all();
        return response()->json($users);
    }

    public function enableDisableUser(Request $request)
    {
        $userId = $request->input('userId');
        $action = $request->input('action'); // 'enable' or 'disable'

        $user = User::find($userId);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Update the is_disabled field based on the action
        $user->is_disabled = ($action === 'disable');
        $user->save();

        return response()->json(['success' => true]);
    }





}

