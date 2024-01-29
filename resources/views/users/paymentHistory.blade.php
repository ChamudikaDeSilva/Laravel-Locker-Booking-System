@extends('layouts.navbar')

@section('content')

<div class="container">
    <div class="dashboard-content px-3 pt-4">
        <b><h2 class="fs-5">Transaction Analysis</h2></b>
    </div>
    <div class="content">
        <hr style="border-top: 2px solid #000;">

    <div>
        <h5>Payment History</h5>
        <div class="table-responsive">
        <table id="example" class="table table-striped" style="width:100%">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>User Id</th>
                    <th>Booking Id</th>
                    <th>Date</th>
                    <th>Payment Type</th>
                    <th>Payment</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($payments as $payment)
                    <tr>
                        <td>{{ $payment->id }}</td>
                        <td>{{ $payment->user_id }}</td>
                        <td>{{ $payment->booking_id }}</td>
                        <td>{{ $payment->date }}</td>
                        <td>{{$payment->payment_type}}</td>
                        <td>Rs.{{ $payment->payment_amount }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>
    <div>
        <h3>The Total  Payment: Rs. <b>{{ $totalPayment }}</b></h3>
    </div>
</div>

</div>


</div>

@endsection
