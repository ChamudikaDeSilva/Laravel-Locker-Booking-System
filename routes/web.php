<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LockerController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\userDashboardController;
use App\Http\Controllers\userProfileController;
use App\Http\Controllers\TopUpController;
use App\Http\Controllers\Auth\LoginController;

use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->middleware('auth')->name('home');
Route::get('/admin.adminBookings',[App\Http\Controllers\HomeController::class, 'adminBookings'])->middleware(['auth','admin']);

Route::group(['middleware' => ['auth', 'user']], function () {
Route::get('/booking-dashboard', [LockerController::class, 'showBookingDashboard'])->name('booking.dashboard');
//Route::post('/save-booking', [BookingController::class, 'saveBooking'])->name('save.booking')->middleware('web');
Route::post('/confirm-booking', [BookingController::class,'confirmBooking'])->name('confirm.booking');

Route::get('/getBookings', [BookingController::class,'getBookings'])->name('getBookings');


Route::get('/top-up', [UserController::class, 'topUp'])->name('users.topUp');
Route::get('/payment-history', [UserController::class, 'paymentHistory'])->name('users.paymentHistory');
Route::get('/contact', [UserController::class, 'contact'])->name('users.contact');
Route::get('/profile', [UserController::class, 'profile'])->name('users.profile');
Route::get('/book-locker/l',[BookingController::class,'lockerView'])->name('book.locker');
Route::get('/booking/all-history/1', [UserController::class, 'allBookingHistory'])->name('booking.historyall');

//Route::get('/dashboard-01', [userDashboardController::class, 'userDashboard'])->name('users.dashboard');

Route::get('/user-dashboard', [UserDashboardController::class, 'userDashboard'])->name('users.userDashboard');


Route::get('/profile', [UserProfileController::class, 'showProfile'])->name('profile');
Route::post('/profile/update', [UserProfileController::class, 'updateProfile'])->name('profile.update');

Route::get('/booking-history', [BookingController::class,'bookingHistory'])->name('booking.history');
Route::get('/booking-Allhistory', [BookingController::class,'bookingAllHistory'])->name('booking.Allhistory');
Route::get('/booking/cancel/{id}', [BookingController::class, 'cancelBooking'])->name('booking.cancel');
Route::get('/booking/review/{id}', [BookingController::class, 'showReviewForm'])->name('booking.review.form');
Route::post('/booking/review/{id}', [BookingController::class, 'submitReview'])->name('booking.review.submit');

// Route to show the top-up form
Route::get('/top-up-form', [TopUpController::class, 'showTopUpForm'])->name('top-up-form');
// Route to complete a booking
Route::post('/complete-booking/{booking}', [TopUpController::class, 'completeBooking'])->name('complete-booking');

Route::post('/get-available-lockers', [BookingController::class, 'getAvailableLockers']);

// Checking locker availability
Route::get('/get-locker-status/{id}', [BookingController::class, 'getLockerStatus']);
Route::post('/check-locker-availability', [BookingController::class, 'checkLockerAvailability']);
Route::get('/user/top-up-history', [TopUpController::class, 'showUserProfile'])->name('user.top-up-history');
Route::post('/check-locker-availability', [BookingController::class, 'checkLockerAvailability']);
Route::get('/transaction-history', [TopUpController::class, 'showTransactionHistory'])->name('transaction.history');



});


//admin functions
//admin locker management
Route::group(['middleware' => ['auth', 'admin']], function () {
Route::get('/lockers', [LockerController::class, 'showLockers'])->name('lockers.show');
Route::post('/add-locker', [LockerController::class, 'addLocker']);
Route::get('/get-locker/{id}', [LockerController::class, 'getLocker']);
Route::post('/update-locker/{id}', [LockerController::class, 'updateLocker']);
Route::get('/get-all-lockers', [LockerController::class, 'getAllLockers']);
Route::post('/delete-locker/{id}', [LockerController::class, 'deleteLocker']);
//admin user management
Route::get('/users', [UserController::class, 'index'])->name('users.index');

Route::get('/usersManagement', [UserController::class, 'index01'])->name('admin.userManagement');
Route::post('/admin/saveUser', [UserController::class,'saveAdmin'])->name('admin.saveUser');


Route::get('/admin/getUser/{id}', [UserController::class,'getUserDetails']);
Route::post('/admin/updateUser', [UserController::class,'updateUserDetails']);
Route::post('/admin/deleteUser', [UserController::class,'deleteUser'])->name('admin.deleteUser');
Route::get('getUser/{userId}', [UserController::class, 'getUser'])->name('admin.getUser');
Route::get('/admin/getAllUsers', [UserController::class,'getAllUsers'])->name('admin.getAllUsers');
Route::get('/admin/reviewManager', [BookingController::class,'reviewManager'])->name('admin.reviewManager');
Route::post('/fix-locker/{id}', [BookingController::class,'fixLocker'])->name('admin.fixLocker');

// routes/web.php
Route::post('/save-review-action', [BookingController::class,'saveReviewAction']);

Route::get('/fetch-all-contacts', [BookingController::class,'fetchAllContacts']);

//admin top up routes
Route::get('/admin/topup', [TopUpController::class, 'showAdminTopUpForm'])->name('admin.showAdminTopUpForm');
Route::post('/admin/get-user-details', [TopUpController::class, 'getUserDetails']);
Route::post('/admin/top-up-account', [TopUpController::class, 'topUpAccount']);

Route::get('/admin/allpayment-analysis', [TopUpController::class, 'showAllPayments'])->name('showAllPayments.Admin');

Route::get('/admin/payment-history', [TopUpController::class,'showPaymentHistory'])->name('admin.payment.history');

//booking management
Route::get('/bookings', [BookingController::class, 'showBookings'])->name('admin.bookings');
Route::get('/show-all-bookings', [BookingController::class, 'showAllBookings'])->name('admin.Allbookings');

Route::patch('/bookings/{id}/update-status', [BookingController::class, 'updateStatus'])->name('booking.update');
Route::get('/get-all-bookings', [BookingController::class,'getAllBookingsAdmin']);

Route::post('/updateKeyManagement', [BookingController::class, 'updateKeyManagement'])->name('updateKeyManagement');
Route::post('/savePayment', [BookingController::class,'savePayment']);
Route::post('/admin/enableDisableUser', [UserController::class,'enableDisableUser']);
Route::get('/getBookingDetails/{bookingId}', [BookingController::class, 'getBookingDetails']);
});

Route::get('/book-locker/2',[BookingController::class,'lockerView01']);
Route::post('/check-locker-availability-01', [BookingController::class, 'checkLockerAvailability']);


