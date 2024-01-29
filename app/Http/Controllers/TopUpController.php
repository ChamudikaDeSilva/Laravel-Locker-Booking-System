<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use App\Models\Locker;
use App\Models\Payment;
use App\Models\Booking;
use App\Models\TopUp;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Contact;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;




class TopUpController extends Controller{

    public function showTopUpForm()
    {
        $user = Auth::user();
        return view('users.topUp', compact('user'));
    }

    public function showAdminTopUpForm()
    {
        return view('admin.adminTopUp');
    }

    public function getUserDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        $user = User::where('email', $request->email)->first();

        if ($user) {
            return response()->json(['success' => true, 'user' => $user]);
        } else {
            return response()->json(['success' => false, 'message' => 'User not found.']);
        }
    }

    public function topUpAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }
        try {
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User not found.']);
            }

            // Update the user's account balance
            $user->balance += $request->amount;
            $user->save();

            //$receiptNumber = 'TOPUP_' . Str::uuid()->toString();
            $counter = TopUp::count() + 1;
            $receiptNumber = 'Rcpt' . $counter;

            // Record the top-up transaction
            $topUp = TopUp::create([
                'user_id' => $user->id,
                'user_email' => $user->email,
                'receipt_number' => $receiptNumber,
                'amount' => $request->amount,
                'created_date' => Carbon::now()->toDateString(),
                'created_time' => Carbon::now()->toTimeString(),
            ]);

        return response()->json(['success' => true, 'message' => 'Account topped up successfully.', 'topUp' => $topUp]);
        }
         catch (\Exception $e) {
            \Log::error($e->getMessage());

        return response()->json(['success' => false, 'message' => 'Internal Server Error.']);
        }
    }

    public function showPaymentHistory()
    {
        $payments = TopUp::all();
        return view('admin.adminPaymentHistory',compact('payments'));
    }

    //user top up account
    public function showUserProfile()
    {
        $user = Auth::user();
        $topUpHistory = TopUp::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
        $totalTopUp = $topUpHistory->sum('amount');
        return view('users.topupHistory', compact('user', 'topUpHistory','totalTopUp'));
    }

    //topUp analysis
    public function showTransactionHistory()
    {
        $user = auth()->user();

        $payments = Payment::where('user_id', $user->id)->get();

        // Calculate total payment
        $totalPayment = $payments->sum('payment_amount');

        return view('users.paymentHistory', compact('payments', 'totalPayment'));
    }

    public function showAllPayments()
    {
        // Retrieve payment records with associated user details
        $payments = Payment::with('user')->get();

        // Calculate the total payment
        $totalPayment = $payments->sum('payment_amount');

        return view('admin.allpaymentHistory', [
            'payments' => $payments,
            'totalPayment' => $totalPayment,
        ]);
    }
}
