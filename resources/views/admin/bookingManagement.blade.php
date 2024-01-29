@extends('layouts.adminNavbar')
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
    .status-cell.completed {
        color: #4e54c8;
    }
    .status-cell.cancelled{
        color:#ee0a0a;
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
// Function to handle form submission and update the table
function saveBooking(bookingId) {
    console.log('saveBooking called with bookingId:', bookingId);
    var form = $('#statusForm' + bookingId);
    var url = form.attr('action');
    var method = form.attr('method');
    var data = form.serialize();

    $.ajax({
        url: url,
        type: method,
        data: data,
        success: function(response) {
            console.log(response);

            // Close the modal using JavaScript
            $('#modal' + bookingId).modal('hide');

            // Reset the form (optional)
            form.trigger('reset');

            // Ensure that response.booking is defined
            var updatedBooking = response.booking;
            if (updatedBooking !== undefined) {
                // Update the table row with the entire booking object
                updateTableRow(updatedBooking);

                // Check if the status is 'Completed', and remove the row from the table
                if (updatedBooking.status.toLowerCase() === 'completed') {
                    // Remove the table row with a fade-out effect
                    $('tr[data-booking-id="' + bookingId + '"]').fadeOut(500, function() {
                        $(this).remove();
                    });
                }
            } else {
                console.error('Booking information is not defined in the server response.');
            }
        },
        error: function(error) {
            console.error(error);
        }
    });
}


// Function to update the table row with the booking object
function updateTableRow(booking) {
    console.log('updateTableRow called with booking:', booking);

    var row = $('tr[data-booking-id="' + booking.id + '"]');

    // Ensure the row is found before updating
    if (row.length > 0) {
        // Update each column with the corresponding property
        row.find('td:eq(0)').text(booking.id);
        row.find('td:eq(1)').text(booking.user_id);
        row.find('td:eq(2)').text(booking.locker_id);
        row.find('td:eq(3)').text(booking.user.first_name + ' ' + booking.user.last_name);
        row.find('td:eq(4)').text(booking.user.email);
        row.find('td:eq(5)').text(booking.date);
        row.find('td:eq(6)').text(booking.start_time);
        row.find('td:eq(7)').text(booking.end_time);
        row.find('td:eq(8)').html(booking.status);

        // Check if the status is 'Completed', and remove the row from the table
        if (booking.status.toLowerCase() === 'completed') {
            // Remove the table row with a fade-out effect
            row.fadeOut(500, function() {
                $(this).remove();
            });
        }
    } else {
        console.error('Row not found for bookingId:', booking.id);
    }
}


// Add new class based on the updated status
function updateStatus(bookingId, newStatus) {
    var statusCell = $('tr[data-booking-id="' + bookingId + '"] .status-cell');
    statusCell.removeClass();
    statusCell.addClass('status-cell ' + newStatus.toLowerCase());
}
</script>

<div class="container">
    <div class="content">
        <div class="dashboard-content px-3 pt-4">
            <h2 class="fs-5">Current Booking Management</h2>
            <hr style="border-top: 2px solid #000;">

            <table id="example" class="table table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th>Booking Id</th>
                        <th>User ID</th>
                        <th>Locker ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Date</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bookings as $booking)
                    @if (in_array(strtolower($booking->status), ['active', 'processing']))
                    <tr data-booking-id="{{ $booking->id }}">
                            <td>{{$booking->id}}</td>
                            <td>{{ $booking->user_id }}</td>
                            <td>{{ $booking->locker_id }}</td>
                            <td>{{ $booking->user->first_name . ' ' . $booking->user->last_name }}</td>
                            <td>{{ $booking->user->email }}</td>
                            <td>{{ $booking->date }}</td>
                            <td>{{ $booking->start_time }}</td>
                            <td>{{ $booking->end_time }}</td>
                            <td class="status-cell {{ strtolower($booking->status) }}">
                                {{ $booking->status }}
                            </td>
                            <td>
                                @if($booking->status =='Cancelled')
                                    <button type="button" class="btn btn-warning btn-sm"disabled>
                                        Control
                                    </button>
                                @else
                                    <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modal{{ $booking->id }}" data-booking-id="{{ $booking->id }}">Control</button>
                                @endif

                                <div class="modal fade" id="modal{{ $booking->id }}" tabindex="-1" role="dialog" aria-labelledby="modal{{ $booking->id }}Label" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="modal{{ $booking->id }}Label">Change Booking Status</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="alert alert-secondary" role="alert">
                                                <p>Booking ID: {{ $booking->id }}</p>
                                                <form id="statusForm{{ $booking->id }}" action="{{ route('booking.update', ['id' => $booking->id]) }}" method="post">
                                                    @csrf
                                                    @method('PATCH')
                                                    <div class="form-group">
                                                        <label for="status">Status:</label>
                                                        <select name="status" id="status" class="form-control">

                                                            <option value="Processing" {{ $booking->status === 'Processing' ? 'selected' : '' }}>Processing</option>
                                                            <option value="Completed" {{ $booking->status === 'Completed' ? 'selected' : '' }}>Completed</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-success" onclick="saveBooking({{ $booking->id }})" data-dismiss="modal">Save</button>
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                    </div>
                                                </form>
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
</div>
</div>
@endsection
