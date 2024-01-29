<?php

namespace App\Http\Controllers;

use App\Models\Locker;
use Illuminate\Support\Facades\Log;
use Validator;
use Illuminate\Http\Request;

class LockerController extends Controller
{
    //showing user locker booking dashboard
    public function showBookingDashboard()
    {
        $locker = Locker::all();
        return view('users.booking', compact('locker'));
    }

    //showing the locker management blade
    //public function index()
    //{
        //$locker = Locker::all();
       // return view('admin.LockerManagement', compact('locker'));
    //}

    public function showLockers()
    {
        $locker = Locker::all(); // Fetch all lockers from the database

        return view('admin.lockerManagement', compact('locker'));
    }


    public function addLocker(Request $request)
    {
        // Validate the request data
        $request->validate([
            'locker_type' => 'required',
            'position_x' => 'required',
            'position_y' => 'required',
        ]);

        // Add the locker to the database
        $locker = new Locker();
        $locker->locker_type = $request->input('locker_type');
        $locker->position_x = $request->input('position_x');
        $locker->position_y = $request->input('position_y');
        $locker->status = 'Available'; // You might want to set an initial status
        $locker->save();

        // Return a success response with locker details
        return response()->json([
            'message' => 'Locker added successfully',
            'locker' => $locker,
        ]);
    }

    public function getLocker($id)
    {
        // Assuming you have a Locker model
        $locker = Locker::findOrFail($id);

        return response()->json($locker);
    }

    public function updateLocker(Request $request)
    {
        try {
            $data = $request->validate([
                'edit_locker_type' => 'required',
                'edit_position_x' => 'required',
                'edit_position_y' => 'required',
            ]);

            $locker = Locker::find($request->input('locker_id'));
            if (!$locker) {
                return response()->json(['message' => 'Locker not found'], 404);
            }

            $locker->locker_type = $data['edit_locker_type'];
            $locker->position_x = $data['edit_position_x'];
            $locker->position_y = $data['edit_position_y'];
            $locker->save();

            return response()->json(['message' => 'Locker updated successfully']);
        }
        catch (\Exception $e) {
        // Log the exception for further investigation
        \Log::error('Error updating locker: ' . $e->getMessage());
        return response()->json(['message' => 'Error updating locker'], 500);
        }
    }


    public function getAllLockers()
    {
        $locker = Locker::all();

        return response()->json($locker);
    }

    public function deleteLocker($id)
    {
        $locker = Locker::find($id);

        if (!$locker) {
            return response()->json(['message' => 'Locker not found'], 404);
        }

        $locker->delete();

        return response()->json(['message' => 'Locker deleted successfully']);
    }




}
