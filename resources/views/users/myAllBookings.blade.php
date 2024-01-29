
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
    .status-cell.completed {
        color: #4e54c8;
    }
    .status-cell.cancelled{
        color:#ee0a0a;
    }

</style>
<script>
   function submitReview(bookingId) {
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    var formData = {
        rating: $('#rating' + bookingId).val(),
        message: $('#message' + bookingId).val(),
    };

    $.ajax({
        type: 'POST',
        url: '{{ route('booking.review.submit', ['id' => ':bookingId']) }}'.replace(':bookingId', bookingId),
        data: formData,
        dataType: 'json',
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        success: function (data) {
            console.log(data);

            // Close the Review modal
            $('#reviewModal' + bookingId).modal('hide');

            // Show the Thank You modal
            $('#thankYouModal' + bookingId).modal('show');

            // Disable the Review button after successful submission
            $('#reviewButton' + bookingId).prop('disabled', true);
        },
        error: function (error) {
            console.error('Error:', error);
        }
    });
}




    $(document).ready(function () {
        $('body').on('click', '#thankYouModalBtn', function () {
            $(this).closest('.modal').modal('hide');
        });
    });


        // Function to display error modal
        function displayErrorModal(message) {
            // Set the error message in the modal
            $('#errorModal .modal-body p').text(message);
            // Show the error modal
            $('#errorModal').modal('show');
        }


</script>

<div class="container">
<div class="content">
            <div class="dashboard-content px-3 pt-4">
                <h2 class="fs-5">My Booking History</h2>
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
                            @if(in_array(strtolower($booking->status), ['completed', 'cancelled','fully_completed']))
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

                                        @if(in_array(strtolower($booking->status), ['completed','fully_completed']))
                                        <button type="button" class="btn btn-primary btn-sm" id="reviewButton{{ $booking->id }}"
                                            data-toggle="modal" data-target="#reviewModal{{ $booking->id }}"
                                            {{ $booking->reviewed ? 'disabled' : '' }}>
                                        Review
                                    </button>

                                        @else
                                            <button type="button" class="btn btn-primary btn-sm"disabled>
                                            Review
                                            </button>
                                        @endif



                                     <!-- Review Modal -->
                                    <div class="modal fade" id="reviewModal{{ $booking->id }}" tabindex="-1" role="dialog" aria-labelledby="reviewModalLabel{{ $booking->id }}" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="reviewModalLabel{{ $booking->id }}">Rate Your Booking Experience</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <!-- Add read-only fields for user_id, locker_id, locker_type, and booking_id -->

                                                    <div class="form-group">
                                                        <p>User ID: {{ $booking->user->id }}</p>
                                                    </div>
                                                    <div class="form-group">
                                                        <p>Locker ID: {{ $booking->locker->id }}</p>
                                                    </div>
                                                    <div class="form-group">
                                                        <p>Locker Type: {{ $booking->locker->locker_type }}</p>
                                                    </div>
                                                    <div class="form-group">
                                                        <p>Booking ID: {{ $booking->id }}</p>
                                                    </div>

                                                    <!-- Rest of the review form -->
                                                    <div class="alert alert-primary" role="alert">
                                                    <form method="post" action="{{ route('booking.review.submit', ['id' => $booking->id]) }}">
                                                        @csrf
                                                        <div class="form-group">
                                                            <label for="rating{{ $booking->id }}"><b>Rating</b></label>
                                                            <select class="form-control" id="rating{{ $booking->id }}" name="rating" required>
                                                                <option value="1">1</option>
                                                                <option value="2">2</option>
                                                                <option value="3">3</option>
                                                                <option value="4">4</option>
                                                                <option value="5">5</option>
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="message{{ $booking->id }}">Review Message</label>
                                                            <textarea class="form-control" id="message{{ $booking->id }}" name="message" rows="3" required></textarea>
                                                        </div>
                                                        </div>
                                                        <button type="submit" class="btn btn-primary btn-sm" onclick="submitReview({{ $booking->id }})" data-dismiss="modal">Submit Review</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

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
                            </td>
                        </tr>
                    @endif
                @endforeach
                </tbody>
            </table>
        </div>

    </div>
@endsection

