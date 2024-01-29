@extends('layouts.navbar')

@section('content')
    <div class="container">
        <div class="content">
            <div class="dashboard-content px-3 pt-4">
                <h2 class="fs-5">Top Up History</h2>
                <hr style="border-top: 2px solid #000;">
            </div>
            <div class="table-responsive">
            <table id="example" class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User ID</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>User Email</th>
                                <th>Receipt Number</th>
                                <th>Payment Type</th>
                                <th>Amount</th>


                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topUpHistory as $topUp)
                                <tr>
                                    <td>{{ $topUp->id }}</td>
                                    <td>{{ $topUp->user_id }}</td>
                                    <td>{{ $topUp->created_at->toDateString() }}</td>
                                    <td>{{ $topUp->created_at->format('H:i:s') }}</td>
                                    <td>{{ $topUp->user_email }}</td>
                                    <td>{{ $topUp->receipt_number }}</td>
                                    <td>{{$topUp->topUp_type}}</td>
                                    <td>Rs.{{ $topUp->amount }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
            </div>
                    <div>
                        <h3>The Total TopUp: Rs. <b>{{ $totalTopUp }}</b></h3>
                    </div>
            </div>
        </div>
    </div>

@endsection
