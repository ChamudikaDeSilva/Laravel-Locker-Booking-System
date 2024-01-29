@extends('layouts.adminNavbar')
@section('content')
<style>

</style>

<script>
    function updateLockerTable(lockers) {
    var tableBody = $('#lockerTableBody');
    tableBody.empty(); // Clear existing table rows

    // Loop through the lockers and append rows to the table
    $.each(lockers, function(index, locker) {
        tableBody.append(`
            <tr>
                <td>${locker.id}</td>
                <td>${locker.locker_type}</td>
                <td>${locker.status}</td>
                <td>${locker.position_x}</td>
                <td>${locker.position_y}</td>
                <td>
                    <button class="btn btn-info btn-sm edit-locker-btn" data-locker-id="${locker.id}" data-toggle="modal" data-target="#editLockerModal" onclick="populateEditModal('${locker.id}')">
                        Edit
                    </button>
                    <button class="btn btn-primary btn-sm" onclick="viewLocker('${locker.id}')">View</button>
                    <button class="btn btn-danger btn-sm" onclick="openDeleteConfirmationModal('${locker.id}')">Delete</button>
                </td>
            </tr>
        `);
    });
}

function handleAjaxError(error) {
    console.error('AJAX Error:', error);
}

function fetchAndUpdateLockers() {
    $.ajax({
        type: 'GET',
        url: '/get-all-lockers', // Assuming you have a route to fetch all lockers
        success: function (lockers) {
            // Update the table with the latest locker data
            updateLockerTable(lockers);
        },
        error: function (error) {
            console.error('Error fetching updated locker data:', error);
        }
    });
}
//Add locker
function addLocker() {
    var formData = $('#addLockerForm').serialize();

    $.ajax({
        type: 'POST',
        url: '/add-locker',
        data: formData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            // Check if the booking was successful
            if (response.message === 'Locker added successfully') {
                // Reset the form (optional)
                $('#addLockerForm')[0].reset();
                // Close the addLockerModal using Bootstrap modal method
                $('#addLockerModal').modal('hide');
                // Fetch and update the table with the latest locker data
                fetchAndUpdateLockers();
                // Display your success modal
                $('#successModal').modal('show');
            } else {
                // Handle other success cases or display an error message
                console.error('Unexpected response:', response);
            }
        },
        error: function (error) {
            // Handle the error if needed
            console.error('Error adding locker:', error);
        }
    });
}


    // Use 'shown.bs.modal' event to trigger actions after modal is shown
    $('#successModal').on('shown.bs.modal', function (e) {
        // Additional actions when successModal is shown
    });

    // Use 'hidden.bs.modal' event to trigger actions after modal is hidden
    $('#successModal').on('hidden.bs.modal', function (e) {
        // Additional actions when successModal is hidden, if needed
    });

    // Close the success modal and perform additional actions
    function closeSuccessModal() {
        // Close the successModal using Bootstrap modal method
        $('#successModal').modal('hide');
        // Additional actions after successModal is hidden
    }

    // Function to populate the edit locker modal with locker details
    var currentLockerId;

    function populateEditModal(lockerId) {
        currentLockerId = lockerId;
        console.log('Locker ID:', lockerId); // Add this line for debugging

        // Assuming you have a route like '/get-locker/{id}' to fetch locker details by ID
        $.ajax({
            type: 'GET',
            url: '/get-locker/' + lockerId,
            success: function(response) {
                // Populate the form fields with locker details
                $('#edit_locker_type').val(response.locker_type);
                $('#edit_position_x').val(response.position_x);
                $('#edit_position_y').val(response.position_y);
            },
            error: function(error) {
                console.error('Error fetching locker details:', error);
            }
        });
    }

    //update an existing locker
    function updateLocker() {
    var formData = $('#editLockerForm').serialize();
    formData += '&locker_id=' + currentLockerId;
    $.ajax({
        type: 'POST',
        url: '/update-locker/' + currentLockerId,
        data: formData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            // Check if the update was successful
            if (response.message === 'Locker updated successfully') {
                // Fetch and update the table with the latest locker data
                fetchAndUpdateLockers();
            } else {
                // Handle other success cases or display an error message
                console.error('Unexpected response:', response);
            }
        },
        error: function (error) {
            // Handle the error if needed
            console.error('Error updating locker:', error);
        }
    });
}

    function toggleSaveButton() {
    var lockerType = $('#locker_type').val();
    var positionX = $('#position_x').val();
    var positionY = $('#position_y').val();

    var saveButton = $('#addLockerSaveButton');
    saveButton.prop('disabled', !(lockerType && positionX && positionY));
}
    // Use 'shown.bs.modal' event to trigger actions after modal is shown
    $('#addLockerModal').on('shown.bs.modal', function (e) {
        // Additional actions when addLockerModal is shown

        // Enable/disable the "Save Locker" button initially
        toggleSaveButton();
    });

    // Use 'hidden.bs.modal' event to trigger actions after modal is hidden
    $('#addLockerModal').on('hidden.bs.modal', function (e) {
        // Additional actions when addLockerModal is hidden, if needed
    });

    // Add an event listener to the input fields to check for changes
    $('#locker_type, #position_x, #position_y').on('keyup', function () {
        // Enable/disable the "Save Locker" button based on input fields
        toggleSaveButton();
    });

    function displayViewLockerModal(response) {
    $('#view_locker_id').text(response.id);
    $('#view_locker_type').text(response.locker_type);
    $('#view_locker_status').text(response.status);
    $('#view_position_x').text(response.position_x);
    $('#view_position_y').text(response.position_y);
    $('#viewLockerModal').modal('show');
}

// Function to handle deleteLocker success
function handleDeleteLockerSuccess(response) {
    if (response.message === 'Locker deleted successfully') {
        fetchAllLockersAndUpdateTable();
    } else {
        console.error('Unexpected response:', response);
    }
}

// Function to handle deleteLocker error
function handleDeleteLockerError(error) {
    handleAjaxError(error);
}

    //view locker
    function viewLocker(lockerId) {
        // Assuming you have a route like '/get-locker/{id}' to fetch locker details by ID
        $.ajax({
            type: 'GET',
            url: '/get-locker/' + lockerId,
            success: function (response) {
                // Populate the view modal with locker details
                $('#view_locker_id').text(response.id);
                $('#view_locker_type').text(response.locker_type);
                $('#view_locker_status').text(response.status);
                $('#view_position_x').text(response.position_x);
                $('#view_position_y').text(response.position_y);

                // Show the view modal
                $('#viewLockerModal').modal('show');
            },
            error: function (error) {
                console.error('Error fetching locker details:', error);
            }
        });
    }

    $(document).ready(function () {
            // Add an event listener to the close button in the modal footer
            $('#closeViewModalButton').on('click', function () {
                // Close the viewLockerModal using Bootstrap modal method
                $('#viewLockerModal').modal('hide');
            });
            // Add an event listener to the modal header close button (x)
            $('#viewLockerModal').find('.close').on('click', function () {
                // Close the viewLockerModal using Bootstrap modal method
                $('#viewLockerModal').modal('hide');
            });
        });

        function openDeleteConfirmationModal(lockerId) {
        // Set the current lockerId to a data attribute of the modal
        $('#deleteConfirmationModal').data('locker-id', lockerId);
        // Show the delete confirmation modal
        $('#deleteConfirmationModal').modal('show');
    }

// Function to delete the locker
function deleteLocker() {
    var lockerId = $('#deleteConfirmationModal').data('locker-id');

    $.ajax({
        type: 'POST',
        url: '/delete-locker/' + lockerId,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: handleDeleteLockerSuccess,
        error: handleDeleteLockerError,
        complete: function () {
            $('#deleteConfirmationModal').modal('hide');
        }
    });
}
// Function to handle success modal close
function closeSuccessModal() {
    $('#successModal').modal('hide');
}

// Function to handle viewLockerModal close
function closeViewLockerModal() {
    $('#viewLockerModal').modal('hide');
}

// Use 'shown.bs.modal' event to trigger actions after modal is shown
$('#addLockerModal, #successModal, #editLockerModal, #viewLockerModal, #deleteConfirmationModal').on('shown.bs.modal', function (e) {
    // Additional actions when modals are shown
});

// Use 'hidden.bs.modal' event to trigger actions after modal is hidden
$('#addLockerModal, #successModal, #editLockerModal, #viewLockerModal, #deleteConfirmationModal').on('hidden.bs.modal', function (e) {
    // Additional actions when modals are hidden
});

// Add an event listener to the close button in the modal footer
$('#closeViewModalButton').on('click', closeViewLockerModal);

// Add an event listener to the modal header close button (x)
$('#viewLockerModal').find('.close').on('click', closeViewLockerModal);
function fetchAllLockersAndUpdateTable() {
    $.ajax({
        type: 'GET',
        url: '/get-all-lockers',
        success: updateLockerTable,
        error: handleAjaxError
    });
}
$(document).ready(fetchAllLockersAndUpdateTable);

function openDeleteConfirmationModal(lockerId) {
    // Set the current lockerId to a data attribute of the modal
    $('#deleteConfirmationModal').data('locker-id', lockerId);

    // Add an event listener to the close button in the modal footer
    $('#deleteConfirmationModal').find('.btn-secondary').on('click', function () {
        // Close the deleteConfirmationModal using Bootstrap modal method
        $('#deleteConfirmationModal').modal('hide');
    });

    // Add an event listener to the modal header close button (x)
    $('#deleteConfirmationModal').find('.close').on('click', function () {
        // Close the deleteConfirmationModal using Bootstrap modal method
        $('#deleteConfirmationModal').modal('hide');
    });

    // Show the delete confirmation modal
    $('#deleteConfirmationModal').modal('show');
}
</script>

<div class="container">
<div class="content">
    <div class="dashboard-content px-3 pt-4">
        <h2 class="fs-5">Locker Management</h2>
        <hr style="border-top: 2px solid #000;">
    <!-- Button to trigger modal -->
<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addLockerModal">
    Add a Locker
</button>

<!-- Add Locker Modal -->
<div class="modal fade" id="addLockerModal" tabindex="-1" role="dialog" aria-labelledby="addLockerModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addLockerModalLabel">Add Locker</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="alert alert-secondary" role="alert">
            <div class="modal-body">
                <!-- Your form goes here -->
                <form id="addLockerForm">
                    <!-- Add your form fields, including locker_type, position_x, position_y -->
                    <div class="form-group">
                        <label for="locker_type">Locker Type</label>
                        <select class="form-control" id="locker_type" name="locker_type">
                            <option value="small">Small</option>
                            <option value="medium">Medium</option>
                            <option value="large">Large</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="position_x">Position X</label>
                        <input type="text" class="form-control" id="position_x" name="position_x">
                    </div>
                    <div class="form-group">
                        <label for="position_y">Position Y</label>
                        <input type="text" class="form-control" id="position_y" name="position_y">
                    </div>
                </form>
            </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success btn-sm" onclick="addLocker()"data-dismiss="modal">Save Locker</button>
            </div>
        </div>
    </div>
</div>

<br>
<div class="table-responsive">
    <table id="example" class="table table-striped" style="width:100%">
    <thead>
        <tr>
            <th>Locker ID</th>
            <th>Locker Type</th>
            <th>Status</th>
            <th>Position X</th>
            <th>Position Y</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody id="lockerTableBody">
        @foreach($locker as $locker)
    <tr>
        <td>{{ $locker->id }}</td>
        <td>{{ $locker->locker_type }}</td>
        <td>{{ $locker->status }}</td>
        <td>{{ $locker->position_x }}</td>
        <td>{{ $locker->position_y }}</td>
        <td>
            <button class="btn btn-info btn-sm edit-locker-btn" data-locker-id="{{ $locker->id }}" data-toggle="modal" data-target="#editLockerModal" onclick="populateEditModal('{{ $locker->id }}')">
                Edit
            </button>

            <button class="btn btn-primary btn-sm" onclick="viewLocker('{{ $locker->id }}')">View</button>


            <button class="btn btn-danger btn-sm" onclick="openDeleteConfirmationModal('{{ $locker->id }}')">Delete</button>
        </td>
    </tr>
@endforeach
</table>
</div>
</div>

<!-- Edit Locker Modal -->
<div class="modal fade" id="editLockerModal" tabindex="-1" role="dialog" aria-labelledby="editLockerModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editLockerModalLabel">Edit Locker</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-secondary" role="alert">
                <!-- Your form goes here -->
                <form id="editLockerForm">
                    <!-- Add your form fields, including locker_type, position_x, position_y -->
                    <div class="form-group">
                        <label for="edit_locker_type">Locker Type</label>
                        <select class="form-control" id="edit_locker_type" name="edit_locker_type">
                            <option value="small">Small</option>
                            <option value="medium">Medium</option>
                            <option value="large">Large</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_position_x">Position X</label>
                        <input type="text" class="form-control" id="edit_position_x" name="edit_position_x">
                    </div>
                    <div class="form-group">
                        <label for="edit_position_y">Position Y</label>
                        <input type="text" class="form-control" id="edit_position_y" name="edit_position_y">
                    </div>
                </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success btn-sm" onclick="updateLocker()" data-dismiss="modal">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- View Locker Modal -->
<div class="modal fade" id="viewLockerModal" tabindex="-1" role="dialog" aria-labelledby="viewLockerModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewLockerModalLabel">Locker Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info" role="alert">
                <!-- Display locker details here -->
                <p><strong>Locker ID:</strong> <span id="view_locker_id"></span></p>
                <p><strong>Locker Type:</strong> <span id="view_locker_type"></span></p>
                <p><strong>Status:</strong> <span id="view_locker_status"></span></p>
                <p><strong>Position X:</strong> <span id="view_position_x"></span></p>
                <p><strong>Position Y:</strong> <span id="view_position_y"></span></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" id="closeViewModalButton">Close</button>
            </div>


        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteConfirmationModalLabel">Delete Locker</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger" role="alert">
                    Are you sure you want to delete this locker?
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger btn-sm" onclick="deleteLocker()">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Success</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                The locker has been added successfully.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" onclick="closeSuccessModal()">Close</button>
            </div>
        </div>
    </div>
</div>
</div>
</div>
@endsection

