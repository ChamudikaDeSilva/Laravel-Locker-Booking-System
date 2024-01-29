@extends('layouts.navbar')
@section('content')
<div class="container">
<div class="content">
    <div class="dashboard-content px-3 pt-4">
        <h2 class="fs-5"> Top Up Account</h2>
        <hr style="border-top: 2px solid #000;">

    </div>

        <div class="row">
            <div class="col-md-3">
                <!-- Display User's Image -->
                <img src="{{ asset('images/flat-business-man-user-profile-avatar-icon-vector-4333097.jpg') }}" alt="User Image" class="img-fluid">
            </div>

            <div class="col-md-9">
                <!-- Display User Details -->
                <h2>Welcome {{ $user->first_name }} {{ $user->last_name }} !!!</h2>
                <p>Email: {{ $user->email }}</p>
                <p>Phone: {{ $user->phone }}</p>
                <p>Registration Number: {{ $user->registration_number }}</p>
                <p>Faculty: {{ $user->faculty }}</p>
            </div>
        </div>
    

        <div class="row mt-3">
            <div class="col-md-12 text-right">
                <!-- Display Available Balance -->
                <h1>Available Balance: Rs.{{ $user->balance}}</h1>
            </div>
        </div>
    </div>
</div>

@endsection
