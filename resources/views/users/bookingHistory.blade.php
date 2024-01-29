
@extends('layouts.navbar')

@section('content')
<style>

    .status-cell {

    }
    .status-cell.active {
        color: #28a745;
    }
    .status-cell.processing {
        color:  #007bff;
    }

</style>
<script>
    $(document).ready(function () {
        // Set the booking ID when the Confirm button is clicked
        $('#confirmCancelModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var bookingId = button.data('booking-id');
            $('#confirmCancelBtn').attr('href', '{{ route('booking.cancel', ['id' => ':bookingId']) }}'.replace(':bookingId', bookingId));
        });
    });
</script>

<div class="container">
<div class="content">
            <div class="dashboard-content px-3 pt-4">
                <h2 class="fs-5">My Current Bookings</h2>
                <hr style="border-top: 2px solid #000;">
                <div class="table-responsive">
                    <table id="example" class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>User Id</th>
                                <th>Locker Id</th>
                                <th>Locker Type</th>
                                <th>Name</th>
                                <th>Date</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                                <th>Booking Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bookings as $booking)
                            @if(in_array(strtolower($booking->status), ['active', 'processing']))
                                <tr>
                                    <td>{{ $booking->user->id }}</td>
                                    <td>{{ $booking->locker->id }}</td>
                                    <td>{{ $booking->locker->locker_type}}
                                    <td>{{ $booking->user->first_name }}</td>
                                    <td>{{ $booking->date }}</td>
                                    <td>{{ $booking->start_time }}</td>
                                    <td>{{ $booking->end_time }}</td>
                                    <td class="status-cell {{ strtolower($booking->status) }}">
                                        {{ $booking->status }}
                                    </td>
                                    <td>
                                        @if($booking->status== 'Active')
                                            <button type="button" class="btn btn-danger btn-sm" title="Cancel Booking" data-toggle="modal" data-target="#confirmCancelModal" data-booking-id="{{ $booking->id }}">Cancel</button>
                                        @else
                                            <button type="button" class="btn btn-danger btn-sm" title="Cancel Booking" disabled>
                                                Cancel
                                            </button>
                                        @endif



                                        <!-- Thank You Modal -->
                                        <div class="modal fade" id="thankYouModal{{ $booking->id }}" tabindex="-1" role="dialog" aria-labelledby="thankYouModalLabel{{ $booking->id }}" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="thankYouModalLabel{{ $booking->id }}">Thank You for Your Review</h5>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" id="thankYouModalBtn" data-dismiss="modal" title="Close Thank You Modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Confirm Cancel Modal -->
                                <div class="modal fade" id="confirmCancelModal" tabindex="-1" role="dialog" aria-labelledby="confirmCancelModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="confirmCancelModalLabel">Confirm Booking Cancellation</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="alert alert-danger" role="alert">
                                                Are you sure you want to cancel this booking?
                                            </div>
                                        </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                <a href="#" id="confirmCancelBtn" class="btn btn-danger">Confirm</a>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endif
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>
@endsection

