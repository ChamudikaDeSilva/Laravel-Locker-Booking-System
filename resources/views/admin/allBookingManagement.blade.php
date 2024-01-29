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

    .key-management-modal .modal-dialog {
        max-width: 600px;
    }

</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

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
            alert('The status updated successfully!')

            // Close the modal using JavaScript
            $('#modal' + bookingId).modal('hide');
             // Display the payment success modal
             $('#paymentSuccessModal').modal('show');

            // Reset the form (optional)
            form.trigger('reset');

            // Ensure that response.booking is defined
            var updatedBooking = response.booking;
            if (updatedBooking !== undefined) {
                // Update the table row with the entire booking object
                updateTableRow(updatedBooking);
                // Update the modal with the end time
                $('#bookingEndTime').val(updatedBooking.end_time);
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


    $(document).ready(function () {
        // Add a click event listener to the "Key Management" button
        $('table').on('click', '.btn-warning', function () {
            // Get the booking ID from the data attribute
            var bookingId = $(this).closest('tr').data('booking-id');

            // Open the Key Management modal
            $('#keyManagementModal').modal('show');

            // Set a data attribute in the modal to identify the associated booking ID
            $('#keyManagementModal').data('booking-id', bookingId);
        });

       // Add a click event listener to the "Save" button in the Key Management modal
$('#saveKeyManagementBtn').on('click', function () {
    // Get the selected option from the modal
    var selectedOption = $('#keyManagementOption').val();

    // Get the associated booking ID from the modal data attribute
    var bookingId = $('#keyManagementModal').data('booking-id');

    // Get the user ID from the table row
    var userId = $('tr[data-booking-id="' + bookingId + '"] td:eq(1)').text();

    // Check if additional chargers are selected
    if ($('input[name="additionalChargers"]:checked').val() === 'yes') {
        // Get the current date
        var currentDate = moment().format('YYYY-MM-DD');

        // Get the amount value
        var amount = $('#amount').val();

        // Perform an AJAX request to update the key management column in the database
        $.ajax({
            type: 'POST',
            url: '/updateKeyManagement', // Replace with your actual route
            data: {
                bookingId: bookingId,
                keyManagement: selectedOption
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                console.log(response);

                // Close the Key Management modal
                $('#keyManagementModal').modal('hide');

                // Show the success modal
                $('#successModal').modal('show');

                // Perform another AJAX request to save payment details
                $.ajax({
                    type: 'POST',
                    url: '/savePayment', // Replace with your actual route
                    data: {
                        userId: userId,
                        bookingId: bookingId,
                        date: currentDate,
                        paymentAmount: amount,
                        paymentType: 'ExtraPayment'
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (paymentResponse) {
                        console.log(paymentResponse);
                        // Handle the payment response as needed
                    },
                    error: function (paymentError) {
                        console.error(paymentError);
                        alert('Error saving payment details');
                    }
                });
            },
            error: function (error) {
                console.error(error);
                alert('Error updating key management');
            }
        });
    } else {
        // If additional chargers are not selected, proceed with updating key management only
        $.ajax({
            type: 'POST',
            url: '/updateKeyManagement', // Replace with your actual route
            data: {
                bookingId: bookingId,
                keyManagement: selectedOption
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                console.log(response);

                // Close the Key Management modal
                $('#keyManagementModal').modal('hide');

                // Show the success modal
                $('#successModal').modal('show');
                // Update the status and action fields
                updateStatusAndAction(response.booking);
            },
            error: function (error) {
                console.error(error);
                alert('Error updating key management');
            }
        });
    }
});


        // Add a click event listener to the "Close" button in the Key Management modal
        $('#closeKeyManagementModalBtn').on('click', function () {
            // Close the Key Management modal
            $('#keyManagementModal').modal('hide');
        });
    });

    $(document).ready(function () {
    //Confirm modal closing
    $('#SuccesscloseBtn').on('click', function () {
    $('#successModal').modal('hide');
     // Fetch updated data from the server using AJAX
     var bookingId = $('#keyManagementModal').data('booking-id');
            $.ajax({
                type: 'GET',
                url: '/getBookingDetails/' + bookingId,  // Replace with your actual route
                success: function (response) {
                    console.log(response);

                    // Update the status in the table row
                    var newStatus = response.booking.status;
                    updateStatusInTable(bookingId, newStatus);
                     // Update the status and action fields
                    updateStatusAndAction(response.booking);

                    // Update other fields in the table row
                    updateOtherFieldsInTable(bookingId, response.booking);
                },
                error: function (error) {
                    console.error(error);
                    alert('Error fetching updated data');
                }
            });
    });

    $('#successModal').find('.close').on('click', function () {
    $('#successModal').modal('hide');
      // Fetch updated data from the server using AJAX
      var bookingId = $('#keyManagementModal').data('booking-id');
            $.ajax({
                type: 'GET',
                url: '/getBookingDetails/' + bookingId,  // Replace with your actual route
                success: function (response) {
                    console.log(response);

                    // Update the status in the table row
                    var newStatus = response.booking.status;
                    updateStatusInTable(bookingId, newStatus);

                     // Update the status and action fields
                    updateStatusAndAction(response.booking);

                    // Update other fields in the table row
                    updateOtherFieldsInTable(bookingId, response.booking);
                },
                error: function (error) {
                    console.error(error);
                    alert('Error fetching updated data');
                }
            });

});


// Add a change event listener to the additionalChargers radio buttons
$('input[name="additionalChargers"]').change(function () {
    if ($(this).val() === 'yes') {
        $('#additionalChargersDetails').show();
    } else {
        $('#additionalChargersDetails').hide();
    }
});
});


// Add this script at the end of your existing JavaScript code
$(document).ready(function () {
 // Add a click event listener to the "Key Management" button
$('table').on('click', '.btn-warning', function () {
    var bookingId = $(this).closest('tr').data('booking-id');
    var endTime = $(this).closest('tr').find('.booking-end-time').text();
    var endTimePlus15 = calculateEndTimePlus15Minutes(endTime);
    var currentTimeNow = moment().format('HH:mm');
    $('#currentTime').val(currentTimeNow);
    // Open the Key Management modal
    $('#keyManagementModal').modal('show');

    // Set the booking ID, end time, and calculate End Time + 15 Minutes in the modal
    $('#keyManagementModal').data('booking-id', bookingId);
    $('#bookingEndTime').val(endTime);
    $('#endTimePlus15').val(endTimePlus15);

    // Set the value for the "Current Time" input
    $('#currentTime').val(endTimePlus15);

    // Calculate and display the initial usage after a short delay
    setTimeout(calculateAndDisplayUsage, 100);
});


// Add an input event listener to the "Current Time" input
$('#currentTime').on('input', function () {
    // Get the value as a string
    var currentTimeString = $(this).val();

    // Check if a valid time is selected
    if (currentTimeString) {
        // Parse the string value into a moment object with the 'HH:mm' format
        var currentTimeMoment = moment(currentTimeString, 'HH:mm', true);

        // Check if the parsed moment object is valid
        if (currentTimeMoment.isValid()) {
            // If valid, set the value back to the input in the 'HH:mm' format
            $(this).val(currentTimeMoment.format('HH:mm'));

            // Trigger the calculateAndDisplayUsage function
            calculateAndDisplayUsage();
        } else {
            // Handle invalid time values if needed
            // For example, you can clear the 'Usage' field
            $('#usage').val('');
        }
    }
});

// Add an input event listener to the "Unit Amount" input
$('#unitAmount').on('input', function () {
    // Get the unit amount value as a string
    var unitAmountString = $(this).val();

    // Check if a valid unit amount is entered
    if (unitAmountString) {
        // Parse the string value into a floating-point number
        var unitAmount = parseFloat(unitAmountString);

        // Get the usage value
        var usage = $('#usage').val();

        // Check if usage is a valid number
        if (!isNaN(usage)) {
            // Calculate the amount and display it in the "Amount" input
            var amount = unitAmount * usage;
            $('#amount').val(amount.toFixed(2));
        } else {
            // Handle invalid usage values if needed
            // For example, you can clear the 'Amount' field
            $('#amount').val('');
        }
    } else {
        // Handle invalid unit amount values if needed
        // For example, you can clear the 'Amount' field
        $('#amount').val('');
    }
});

});

// Function to update the status in the table row
function updateStatusInTable(bookingId, newStatus) {
        var statusCell = $('tr[data-booking-id="' + bookingId + '"] .status-cell');
        statusCell.removeClass();
        statusCell.addClass('status-cell ' + newStatus.toLowerCase());
        statusCell.html(newStatus);
    }

    // Function to update the status and action fields
function updateStatusAndAction(booking) {
    var row = $('tr[data-booking-id="' + booking.id + '"]');

    // Ensure the row is found before updating
    if (row.length > 0) {
        // Update the status field
        row.find('td:eq(8)').html(booking.status);

        // Update the action field
        var actionField = row.find('td:eq(9)');
        if (booking.status.toLowerCase() === 'fully_completed' || booking.status.toLowerCase() === 'cancelled') {
            actionField.html('<p>Nothing</p>');
        } else {
            actionField.html('<button type="button" class="btn btn-warning btn-sm">Action</button>');
        }
    } else {
        console.error('Row not found for bookingId:', booking.id);
    }
}


// Function to calculate End Time + 15 Minutes
function calculateEndTimePlus15Minutes(endTime) {
    // Convert the time string to a Date object
    var endTimeDate = new Date('2000-01-01 ' + endTime);

    // Add 15 minutes
    endTimeDate.setMinutes(endTimeDate.getMinutes() + 15);

    // Format the result as HH:mm
    var endTimePlus15 = endTimeDate.getHours().toString().padStart(2, '0') + ':' +
                        endTimeDate.getMinutes().toString().padStart(2, '0');

    return endTimePlus15;
}

// Function to calculate and display the usage
function calculateAndDisplayUsage() {
    var endTimePlus15 = $('#endTimePlus15').val();
    var currentTime = $('#currentTime').val();

    console.log('endTimePlus15:', endTimePlus15);
    console.log('currentTime:', currentTime);

    // Parse the time values using Moment.js
    var endTimePlus15Moment = moment(endTimePlus15, 'HH:mm', true);
    var currentTimeMoment = moment(currentTime, 'HH:mm', true);

    console.log('endTimePlus15Moment:', endTimePlus15Moment);
    console.log('currentTimeMoment:', currentTimeMoment);

    // Check if both endTimePlus15Moment and currentTimeMoment are valid
    if (endTimePlus15Moment.isValid() && currentTimeMoment.isValid()) {
        // Calculate the difference in minutes
        var diffInMinutes = currentTimeMoment.diff(endTimePlus15Moment, 'minutes');

        console.log('diffInMinutes:', diffInMinutes);

        // Display the calculated usage
        $('#usage').val(diffInMinutes);
    } else {
        // Handle invalid time values if needed
        $('#usage').val('');
    }
}
</script>

<div class="container">
    <div class="content">
        <div class="dashboard-content px-3 pt-4">
            <h2 class="fs-5">Booking History</h2>
            <hr style="border-top: 2px solid #000;">
            <div class="table-responsive">
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
                        <th>Action</th>

                    </tr>
                </thead>
                <tbody>

                    @foreach ($bookings as $booking)
                    @if (in_array(strtolower($booking->status), ['completed', 'cancelled','fully_completed']))
                    <tr data-booking-id="{{ $booking->id }}">
                            <td>{{$booking->id}}</td>
                            <td>{{ $booking->user_id }}</td>
                            <td>{{ $booking->locker_id }}</td>
                            <td>{{ $booking->user->first_name . ' ' . $booking->user->last_name }}</td>
                            <td>{{ $booking->user->email }}</td>
                            <td>{{ $booking->date }}</td>
                            <td>{{ $booking->start_time }}</td>
                            <td class="booking-end-time">{{ $booking->end_time }}</td>
                            <td class="status-cell {{ strtolower($booking->status) }}">
                                {{ $booking->status }}
                            </td>
                            <td>
                                @if(in_array(strtolower($booking->status), ['fully_completed', 'cancelled']))
                                    <p>Nothing</p>
                                @else
                                    <button type="button" class="btn btn-warning btn-sm">
                                        Action
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @endif

                        <div class="modal fade key-management-modal" id="keyManagementModal" tabindex="-1" role="dialog" aria-labelledby="keyManagementModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="keyManagementModalLabel">Key Management</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="alert alert-secondary" role="alert">
                                        <label for="keyManagementOption">Select key management status:</label>
                                        <select class="form-control" id="keyManagementOption">
                                            <option value="handed_over">Handed Over</option>
                                            <option value="not_handed_over">Not Handed Over</option>
                                        </select>

                                        <div class="mt-3">
                                            <label>Additional Chargers:</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="additionalChargers" id="additionalChargersYes" value="yes">
                                                <label class="form-check-label" for="additionalChargersYes">Yes</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="additionalChargers" id="additionalChargersNo" value="no" checked>
                                                <label class="form-check-label" for="additionalChargersNo">No</label>
                                            </div>
                                        </div>

                                        <div id="additionalChargersDetails" class="mt-3" style="display: none;">
                                            <label for="bookingEndTime">Booking End Time:</label>
                                            <input type="text" class="form-control" id="bookingEndTime" name="bookingEndTime" readonly>

                                            <label for="endTimePlus15">End Time + 15 Minutes:</label>
                                            <input type="text" class="form-control" id="endTimePlus15" name="endTimePlus15" readonly>

                                            <div class="mt-3">
                                                <label for="currentTime">Current Time:</label>
                                                <input type="time" class="form-control" id="currentTime" name="currentTime" step="900">
                                            </div>

                                            <label for="usage">Usage (in minutes):</label>
                                            <input type="text" class="form-control" id="usage" name="usage" readonly>

                                            <div class="mt-3">
                                                <label for="unitAmount">Unit Amount (in Rs.):</label>
                                                <input type="text" class="form-control" id="unitAmount" name="unitAmount" step="0.01">
                                            </div>

                                            <label for="amount">Amount (in Rs.):</label>
                                            <input type="text" class="form-control" id="amount" name="amount"readonly>
                                        </div>

                                    </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" id="closeKeyManagementModalBtn" data-dismiss="modal">Close</button>
                                        <button type="button" class="btn btn-primary" id="saveKeyManagementBtn">Save</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>
<!-- Add a success modal -->
<div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="successModalLabel">Success</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            <div class="alert alert-success" role="alert">
                Key status and payment updated successfully!
            </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="SuccesscloseBtn">Close</button>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
