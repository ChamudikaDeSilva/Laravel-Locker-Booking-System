<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
{
    $validator = Validator::make($data, [
        'first_name' => ['required', 'string', 'max:255'],
        'last_name' => ['required', 'string', 'max:255'],
        'faculty' => ['required', 'string', 'max:255'],
        'registration_number' => ['required', 'string', 'max:255', 'unique:users'],
        'phone' => ['required', 'integer'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
    ]);

    if ($validator->fails()) {
        \Log::error('Validation Errors: ' . json_encode($validator->errors()->all()));
    }

    return $validator;
}

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    /*protected function create(array $data)
    {
        return User::create([
        'first_name' => $data['first_name'],
        'last_name' => $data['last_name'],
        'faculty' => $data['faculty'],
        'registration_number' => $data['registration_number'],
        'phone' => $data['phone'],
        'email' => $data['email'],
        'password' => Hash::make($data['password']),
        ]);
    }*/

    protected function create(array $data)
{
    try {
        \Log::info('Create method executed', $data);

        return User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'faculty' => $data['faculty'],
            'registration_number' => $data['registration_number'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    } catch (\Exception $e) {
        \Log::error('User Creation Error: ' . $e->getMessage());
        return null;
    }
}


}


