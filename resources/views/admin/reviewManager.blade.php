@extends('layouts.adminNavbar')

@section('content')
<style>


</style>


<script>

     function openReviewModal(contactId) {
        // Set the contact ID in the modal for reference
        $("#reviewModal").data('contact-id', contactId);
        // Open the modal
        $("#reviewModal").modal('show');
    }

  function saveReviewAction() {
    // Get the selected action and contact ID
    var selectedAction = $("#actionSelect").val();
    var contactId = $("#reviewModal").data('contact-id');

    // Get the CSRF token value
    var csrfToken = $('meta[name="csrf-token"]').attr('content');

    // Perform AJAX request to update the database
    $.ajax({
        url: '/save-review-action',
        type: 'POST',
        data: {
            contactId: contactId,
            action: selectedAction
        },
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        success: function (response) {
            console.log("AJAX request successful.");

            // Close the modal
            $("#reviewModal").modal('hide');

            // Update the table columns after a successful save
            updateTableColumns(contactId, selectedAction, response.finalState);
        },
        error: function (error) {
            // Handle error
            console.error('Error saving review action:', error);
        }
    });
}

function updateTableColumns(contactId, selectedAction, finalState) {
    console.log("Updating columns:", contactId, selectedAction, finalState);

    // Find the table row with the corresponding contactId
    var tableRow = $('[data-contact-id="' + contactId + '"]');

    // Update the "Action" column only if it contains a button with the "btn-warning" class
    if (tableRow.find('.action-column button.btn-warning').length > 0) {
        // Update the "Action" column only if it's a button (indicating it hasn't been saved yet)
        tableRow.find('.action-column').empty().append(
            $('<button class="btn btn-warning btn-sm disabled">').text('Action')
        );

        // Update the "Final State" column directly in the HTML
        tableRow.find('.final-state-column').text(finalState);
    }
}
</script>
<div class="container">
<div class="content">
    <div class="dashboard-content px-3 pt-4">
        <h2 class="fs-5">Review Manager</h2>
        <hr style="border-top: 2px solid #000;">
        <div class="table-responsive">
        <table id="example" class="table table-striped" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User ID</th>
                    <th>Locker ID</th>
                    <th>Booking ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Message</th>
                    <th>Rating</th>
                    <th>Sentiment</th>
                    <th>Action</th>
                    <th>Final State</th>
                </tr>
            </thead>
            <tbody>
                @foreach($contacts as $contact)
                    <tr data-contact-id="{{ $contact->id }}">
                        <td>{{ $contact->id }}</td>
                        <td>{{ $contact->user_id }}</td>
                        <td>{{ $contact->locker_id }}</td>
                        <td>{{ $contact->booking_id }}</td>
                        <td>{{ $contact->name }}</td>
                        <td>{{ $contact->email }}</td>
                        <td>{{ $contact->message }}</td>
                        <td>{{ $contact->rating }}</td>
                        <td>{{ $contact->sentiment }}</td>
                        <td class="action-column">
                            @if($contact->sentiment === 'negative')
                                @if($contact->final_state !== 'completed')
                                <button class="btn btn-warning btn-sm" onclick="openReviewModal({{ $contact->id }})">
                                    Action
                                </button>
                                @else
                                    <button class="btn btn-warning btn-sm disabled">
                                        Action
                                    </button>
                                @endif
                            @else
                                {{ $contact->action }}
                            @endif
                        </td>
                        <td class="final-state-column">{{ $contact->final_state }}</td>

                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Review Action</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="reviewForm">
                    <div class="form-group">
                        <label for="actionSelect">Select Action:</label>
                        <select class="form-control" id="actionSelect">
                            <option value="fixed">Fixed</option>
                            <!--option value="not-yet">Not Yet</option-->
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="saveReviewAction()">Save</button>
            </div>
        </div>
    </div>
</div>

</div>
@endsection
