<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use App\Models\Locker;
use App\Models\Payment;
use App\Models\Booking;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\TopUp;
use App\Models\Contact;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;



class BookingController extends Controller
{
    public function confirmBooking(Request $request)
    {
        try {
            $currentDate = Carbon::now();
            $startTime = $request->input('startTime');
            $endTime = $request->input('endTime');
            $date = $request->input('date');
            $lockerId = $request->input('selectedLockerId');

            if (auth()->check()) {
                $user = auth()->user();
                $userId = auth()->user()->id;

                // Calculate usage slots
                $startDateTime = Carbon::parse($startTime);
                $endDateTime = Carbon::parse($endTime);
                $usageSlots = $startDateTime->diffInMinutes($endDateTime) / 30;

                // Calculate payment_for_booking based on the booking's usage
                $unitAmount = 5; // Replace this with your actual unit amount
                $payment_amount = $unitAmount * $usageSlots;

                // Check if the user has enough balance
                if ($user->balance <  $payment_amount) {
                    return response()->json(['error' => 'Insufficient balance. Please add funds to your account.'], 400);
                }

                // Create the booking
                $booking = Booking::create([
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'date' => $date,
                    'locker_id' => $lockerId,
                    'user_id' => $userId,
                    'usage' => $usageSlots,
                ]);

                // Update the locker status to 'booked'
                Locker::where('id', $lockerId)->update(['status' => 'booked']);

                // Create a new payment record
                $payment = new Payment();
                $payment->user_id = $userId;
                $payment->booking_id = $booking->id; // Now $booking is defined
                $payment->date = $date;
                $payment-> payment_amount=  $payment_amount;

                $payment->save();

                // Deduct payment_for_booking from the user's balance
                $user = User::findOrFail($userId);
                $user->balance -= $payment_amount;
                $user->save();

                return response()->json(['message' => 'Booking confirmed successfully']);
            } else {
                return response()->json(['error' => 'User not authenticated.'], 401);
            }
        } catch (\Exception $e) {
            Log::error('Unexpected error creating booking: ' . $e->getMessage());
            return response()->json(['error' => 'Unexpected error. Please try again.'], 500);
        }
    }


    public function getBookings()
    {
        $bookings = Booking::all();
        return response()->json(['booking' => $booking]);
    }
    public function lockerView(){
        $lockers=Locker::all();
        return view('users.booking',compact('lockers'));
    }

    public function bookingHistory()
    {
        if (auth()->check()) {
        $userId = auth()->user()->id;
        $bookings = Booking::where('user_id', $userId)->with('locker', 'payment')->get();

        return view('users.bookingHistory', compact('bookings'));
        }
        else {
            abort(403, 'Unauthorized');
        }
    }

    public function bookingAllHistory()
    {
        if (auth()->check()) {
        $userId = auth()->user()->id;
        $bookings = Booking::where('user_id', $userId)->with('locker', 'payment')->get();

        return view('users.myAllBookings', compact('bookings'));
        }
        else {
            abort(403, 'Unauthorized');
        }
    }



        public function cancelBooking($id)
        {
            try {
                $booking = Booking::findOrFail($id);

                // Check if the booking is cancellable (optional, depending on your logic)
                if ($booking->status === 'Cancelled') {
                    return redirect()->route('booking.history')->with('error', 'Booking has already been cancelled');
                }

                // Calculate the refund amount based on the usage and unit amount
                $unitAmount = 5; // Replace this with your actual unit amount
                $refundAmount = $unitAmount * $booking->usage;

                // Update booking status to 'Cancelled'
                $booking->status = 'Cancelled';
                $booking->save();

                $locker = Locker::find($booking->locker_id);

                // Check if there are any upcoming bookings for the same locker
                $upcomingBookings = Booking::where('locker_id', $locker->id)
                    ->where('status', '!=', 'Completed')
                    ->where('date', '>', now())
                    ->count();

                if ($upcomingBookings == 1) {
                    // If only the current booking is remaining, update the locker status to 'available'
                    $locker->status = 'available';
                    $locker->save();
                }

                // Refund the amount to the user's balance
                $user = User::findOrFail($booking->user_id);
                $user->balance += $refundAmount;
                $user->save();

                $counter = TopUp::count() + 1;
                $receiptNumber = 'Rcpt' . $counter;

                // Create a new top-up record
                $topUp = new TopUp();
                $topUp->user_id = $user->id;
                $topUp->user_email = $user->email;
                $topUp->receipt_number = $receiptNumber;
                $topUp->topUp_type = 'Refund';
                $topUp->amount = $refundAmount;
                $topUp->created_date = now()->toDateString();
                $topUp->created_time = now()->toTimeString();
                $topUp->save();

                return redirect()->route('booking.history')->with('success', 'Booking cancelled successfully. Refund processed.');
            }
            catch (\Exception $e) {
                Log::error('Error cancelling booking: ' . $e->getMessage());
                return redirect()->route('booking.history')->with('error', 'Error cancelling booking');
            }
        }

    public function showReviewForm($id)
    {
        // Find the booking
        $booking = Booking::findOrFail($id);

        return view('users.reviewForm', compact('booking'));
    }

    public function submitReview(Request $request, $id)
    {
        try {
            \Log::info('Received data:', $request->all());
            // Validate the form data
            $request->validate([
                'rating' => 'required|integer|between:1,5',
                'message' => 'required|string',
            ]);

            // Find the booking
            $booking = Booking::findOrFail($id);
            $booking->reviewed = true;
            $booking->save();

            // Categorize as positive or negative based on the rating threshold
            $sentiment = ($request->rating >= 3) ? 'positive' : 'negative';

            // Create a new contact record
            $contact = new Contact(); // Adjusted to use Contact instead of Review
            $contact->user_id = $booking->user_id;
            $contact->booking_id = $booking->id;
            $contact->locker_id = $booking->locker_id;
            $contact->name=$booking->user->first_name;
            $contact->email = $booking->user->email;
            $contact->rating = $request->input('rating');
            $contact->message = $request->input('message');
            $contact->sentiment = $sentiment;
            $contact->save();

            return response()->json(['success' => 'Review submitted successfully']);
        }
        catch (\Exception $e) {
            // Log the exception for debugging
            \Log::error('Review submission error: ' . $e->getMessage());

            // Return a JSON response with an error message
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }




    //admin booking reviews handle
    public function reviewManager()
    {
        $contacts = Contact::all(); // Fetch all contacts from the database

        return view('admin.reviewManager', compact('contacts'));
    }

    //returning everything as jason files
    public function fetchAllContacts()
    {
        $contacts = Contact::all(); // Fetch all contacts from the database

        return response()->json($contacts);
    }



    public function saveReviewAction(Request $request)
    {
        $contactId = $request->input('contactId');
        $selectedAction = $request->input('action');

        // Retrieve the contact by ID
        $contact = Contact::find($contactId);

        if ($contact) {
            // Update the 'action' and 'final_state' fields
            $contact->action = ($selectedAction === 'fixed') ? 'fixed' : 'Nothing';
            $contact->final_state = ($selectedAction === 'fixed') ? 'completed' : 'Not completed yet';

            // Save the changes
            $contact->save();

            // Include the 'final_state' in the response
            return response()->json(['status' => 'success', 'finalState' => $contact->final_state]);
        }

        return response()->json(['status' => 'error']);
    }


    //getting all booking details to the admin panel
    public function showBookings()
    {
        $bookings = Booking::with('user')->get();

        return view('admin.bookingManagement', compact('bookings'));
    }

    public function showAllBookings()
    {
        $bookings = Booking::with('user')->get();

        return view('admin.allBookingManagement', compact('bookings'));
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $booking = Booking::findOrFail($id);
            $newStatus = $request->input('status');

            // Check if the status is being updated to 'Completed'
            if ($newStatus === 'Completed' && $booking->status !== 'Completed') {
                $userId = $booking->user_id;
                $date = $booking->date;

                // Retrieve the locker associated with the booking
                $locker = $booking->locker;

                // Check if the locker exists and update its status
                if ($locker) {
                    $locker->status = 'Available';
                    $locker->save();
                }
            }

            // Check if the status is being updated to 'Processing'
            if ($newStatus === 'Processing' && $booking->status !== 'Processing') {
                // Update the key_management column to 'gave_to_user'
                $booking->key_management = 'gave_to_user';
            }

            // Update the booking status
            $booking->status = $newStatus;
            $booking->save();

            // Include the updated booking details in the response
            $booking->load('user'); // Ensure the user relationship is loaded
            return response()->json(['booking' => $booking, 'message' => 'Status updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    public function checkLockerAvailability(Request $request)
    {
        try {
            // Validate request data (adjust validation rules as needed)
            $request->validate([
                'startTime' => 'required|date_format:H:i',
                'endTime' => 'required|date_format:H:i|after:startTime',
                'date' => 'required|date',
            ]);

            // Perform logic to get available lockers based on $request data
            $startTime = $request->startTime;
            $endTime = $request->endTime;
            $date = $request->date;

            // Retrieve all locker IDs
            $allLockerIds = Locker::pluck('id')->toArray();

            // Retrieve booked locker IDs for the given time slot
            $bookedLockerIds = Booking::whereIn('locker_id', $allLockerIds)
                ->where('date', $date)
                ->where('status', '!=', 'Cancelled')
                ->where('status', '!=', 'Completed')
                ->where(function ($query) use ($startTime, $endTime) {
                    $query->where(function ($query) use ($startTime, $endTime) {
                        $query->where(\DB::raw('TIME(end_time)'), '>', $startTime)
                            ->where(\DB::raw('TIME(start_time)'), '<', $endTime);
                    })
                        ->orWhere(function ($query) use ($startTime, $endTime) {
                            $query->whereBetween(\DB::raw('TIME(start_time)'), [$startTime, $endTime])
                                ->orWhereBetween(\DB::raw('TIME(end_time)'), [$startTime, $endTime])
                                ->orWhere(function ($query) use ($startTime, $endTime) {
                                    $query->where(\DB::raw('TIME(start_time)'), '<', $startTime)
                                        ->where(\DB::raw('TIME(end_time)'), '>', $endTime);
                                });
                        });
                })
                ->pluck('locker_id')
                ->toArray();

            \Log::info('Booked Locker IDs: ' . implode(', ', $bookedLockerIds));

            // Identify available lockers and mark them as not booked
            $availableLockers = collect($allLockerIds)->map(function ($lockerId) use ($bookedLockerIds) {
                return [
                    'id' => $lockerId,
                    'locker_type' => Locker::find($lockerId)->locker_type,
                    'isBooked' => in_array($lockerId, $bookedLockerIds),
                ];
            });

            \Log::info('Available Lockers: ' . $availableLockers->toJson());

            // Pass the data directly to the view
            return response()->json($availableLockers);
        } catch (\Exception $e) {
            // Log the exception
            \Log::error("Error in checkLockerAvailability: " . $e->getMessage());

            // Return a generic error response
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }


    public function updateKeyManagement(Request $request)
    {
        $bookingId = $request->input('bookingId');
        $keyManagement = $request->input('keyManagement');

        try {
            $booking = Booking::findOrFail($bookingId);
            $booking->key_management = $keyManagement;

            // Check if key_management is "handed_over" and update status accordingly
            if ($keyManagement == 'handed_over') {
                $booking->status = 'Fully_Completed';
            } elseif ($keyManagement == 'not_handed_over') {
                // If key_management is "not_handed_over," set status to "completed"
                $booking->status = 'Completed';
            }

            $booking->save();

            return response()->json(['success' => true, 'booking' => $booking]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error updating key management']);
        }
    }


    public function savePayment(Request $request)
    {
        try {
            // Validate the request as needed
            $request->validate([
                'userId' => 'required|exists:users,id',
                'bookingId' => 'required|exists:booking,id', // Assuming booking table has 'id' column
                'date' => 'required|date',
                'paymentAmount' => 'required|numeric',
                'paymentType' => 'required|in:ExtraPayment',
            ]);

            // Retrieve the user based on user_id
            $user = User::findOrFail($request->userId);

            // Save payment details in the database
            $payment = new Payment();
            $payment->user_id = $user->id;
            $payment->booking_id = $request->bookingId;
            $payment->date = $request->date;
            $payment->payment_amount = $request->paymentAmount;
            $payment->payment_type = $request->paymentType;
            $payment->save();

            // Deduct payment_amount from the user's balance
            $user->balance -= $request->paymentAmount;
            $user->save();

            return response()->json(['message' => 'Payment details saved successfully', 'payment' => $payment]);
        }
        catch (\Exception $e)
            {
                Log::error('Error saving payment details: ' . $e->getMessage());
                return response()->json(['error' => 'Internal Server Error'], 500);
            }
        }

        public function getBookingDetails($bookingId)
        {
            try {
                $booking = Booking::findOrFail($bookingId);

                return response()->json(['booking' => $booking], 200);
            } catch (\Exception $exception) {
                return response()->json(['error' => 'Booking not found.'], 404);
            }
        }
}
