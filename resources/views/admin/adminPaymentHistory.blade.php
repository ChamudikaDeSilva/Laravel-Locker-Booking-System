@extends('layouts.adminNavbar')

@section('content')

<div class="container">
    <div class="content">
        <div class="dashboard-content px-3 pt-4">
            <h2 class="fs-5">Top Up History</h2>
            <hr style="border-top: 2px solid #000;">
            <!-- Table to display payment history -->
            <div class="table-responsive">
            <table id="example" class="table table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User ID</th>
                        <th>User Email</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Receipt Number</th>
                        <th>Payment Type</th>
                        <th>Amount</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $payment)
                        <tr>
                            <td>{{ $payment->id }}</td>
                            <td>{{ $payment->user_id }}</td>
                            <td>{{ $payment->user_email }}</td>
                            <td>{{ $payment->created_date }}</td>
                            <td>{{ $payment->created_time }}</td>
                            <td>{{ $payment->receipt_number }}</td>
                            <td>{{$payment->topUp_type}}</td>
                            <td>{{ $payment->amount }}</td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
    </div>
</div>
@endsection
