@extends('layouts.adminNavbar')
@section('content')
<style>
</style>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>




<script>


function viewUser(userId) {
    $.ajax({
    type: 'GET',
    url:'/admin/getUser/' + userId,
    success: function (data) {
        // Fill the form fields with the retrieved user data
        $('#view_user_id').text(data.id);
        $('#view_role_id').text(data.role_id);
        $('#view_first_name').text(data.first_name);
        $('#view_last_name').text(data.last_name);
        $('#view_email').text(data.email);
        $('#view_phone').text(data.phone);
        $('#view_registration_number').text(data.registration_number);
        $('#view_faculty').text(data.faculty);

        // Show the "View Admin" modal
        $('#viewUserModal').modal('show');
    },
    error: function (error) {
        alert('Error fetching user details');
    }
});
}

function confirmDelete(userId) {
    $('#deleteUserModal').modal('show');
    // Store the user ID in a data attribute of the modal
    $('#deleteUserModal').data('user-id', userId);
}

//deleting users
function deleteUser() {
    var userId = $('#deleteUserModal').data('user-id');
    // Get CSRF token value from the meta tag
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    });
    $.ajax({
        type: 'POST',
        url: '/admin/deleteUser',
        data: { userId: userId },
        success: function (data) {
            //alert('User deleted successfully');
            $('#deleteUserModal').modal('hide');
            if (data.success) {
                // Remove the deleted user from the table without reloading the page
                removeUserFromTable(userId);
            }
        },
        error: function (error) {
            // Handle error
            alert('Error deleting admin');
            $('#deleteUserModal').modal('hide');
        }
    });
}
// Function to remove the deleted user from the table
function removeUserFromTable(userId) {
    // Find and remove the table row with the corresponding user ID
    $('#userTableBody tr[data-id="' + userId + '"]').remove();
}

// Function to update the user status in the table
function updateTableUserStatus(userId, action) {
    var statusText = action === 'enable' ? 'Active' : 'Inactive';
    var statusClass = action === 'enable' ? 'text-success' : 'text-danger';

    // Update the user status in the table
    $('#userTableBody tr[data-id="' + userId + '"] .user-status').text(statusText).removeClass('text-success text-danger').addClass(statusClass);
}


$(document).ready(function () {
    //view user modal
    $('#viewUserModal').on('click', '#closeViewModalBtn', function () {
        $('#viewUserModal').modal('hide');
    });

    $('#viewUserModal').on('click', '.close', function () {
        $('#viewUserModal').modal('hide');
    });

    //delete user modal
    $('#deleteUserModal').on('click', '#closeDeleteModalBtn', function () {
        $('#deleteUserModal').modal('hide');
    });

    $('#deleteUserModal').on('click', '.close', function () {
        $('#deleteUserModal').modal('hide');
    });

    //view user modal
    $('#viewUserModal').on('click', '#closeViewModalBtn', function () {
        $('#viewUserModal').modal('hide');
    });

    $('#viewUserModal').on('click', '.close', function () {
        $('#viewUserModal').modal('hide');
    });

    //DisableEnable user modal
    $('#enableDisableModal').on('click', '#DisEnaCloseBtn', function () {
        $('#enableDisableModal').modal('hide');
    });

    $('#enableDisableModal').on('click', '.close', function () {
        $('#enableDisableModal').modal('hide');
    });

    //Success Modal
    $('#successModal').on('click', '#successCloseBtn', function () {
        $('#successModal').modal('hide');
    });

    $('#successModal').on('click', '.close', function () {
        $('#successModal').modal('hide');
    });


    // Add a click event listener for the new button
$('.enable-disable-btn').on('click', function () {
    var userId = $(this).data('user-id');
    var isDisabled = $(this).data('is-disabled') === 'true';

    // Set the selected option based on the current status
    $('#enableDisableSelect').val(isDisabled ? 'disable' : 'enable');

    // Store the user ID in a data attribute of the modal
    $('#enableDisableModal').data('user-id', userId);

    // Show the "Enable/Disable User" modal
    $('#enableDisableModal').modal('show');
});

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
// Add a click event listener for the "Confirm" button in the modal
$('#confirmEnableDisableBtn').on('click', function () {
    var userId = $('#enableDisableModal').data('user-id');
    var action = $('#enableDisableSelect').val();

    $.ajax({
        type: 'POST',
        url: '/admin/enableDisableUser',
        data: { userId: userId, action: action },
        success: function (data) {
            $('#enableDisableModal').modal('hide');
            if (data.success) {
                var statusText = action.charAt(0).toUpperCase() + action.slice(1);
                var successMessage = statusText + 'd user successfully';

                // Set the success message in the success modal
                $('#successMessage').text(successMessage);

                // Show the success modal
                $('#successModal').modal('show');
                // Update the user status in the table without reloading the page
                updateTableUserStatus(userId, action);
            }
        },
        error: function (error) {
            // Handle error
            alert('Error enabling/disabling user');
            $('#enableDisableModal').modal('hide');
        }
    });
});

});
</script>

<div class="container">
<div class="content">
    <div class="dashboard-content px-3 pt-4">
        <h2 class="fs-5">User Management</h2>
        <hr style="border-top: 2px solid #000;">
        <div>
            <div class="table-responsive">
                <table id="example" class="table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Role ID</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Registration Number</th>
                            <th>Faculty</th>
                            <th>User Status</th>
                            <th>Action</th>

                        </tr>
                    </thead>
                    <tbody id="userTableBody">
                        @foreach ($usersWithRoleId2 as $user)
                        <tr data-id="{{ $user->id }}">
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->role_id }}</td>
                                    <td>{{ $user->first_name }}</td>
                                    <td>{{ $user->last_name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->phone }}</td>
                                    <td>{{ $user->registration_number }}</td>
                                    <td>{{ $user->faculty }}</td>
                                    <td class="user-status">
                                        @if($user->is_disabled)
                                            <span class="text-danger">Inactive</span>
                                        @else
                                            <span class="text-success">Active</span>
                                        @endif
                                    </td>
                                    <td>
                                <button class="btn btn-primary btn-sm" onclick="viewUser({{ $user->id }})">View</button>
                                <button class="btn btn-danger btn-sm" onclick="confirmDelete({{ $user->id }})">Delete</button>
                                <button class="btn btn-warning btn-sm enable-disable-btn" data-user-id="{{ $user->id }}" data-is-disabled="{{ $user->is_disabled ? 'true' : 'false' }}">Action</button>

                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

        <!-- View User Modal -->
        <div class="modal fade" id="viewUserModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">View User Details</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info" role="alert">
                        <!-- Display admin details using HTML elements -->
                        <p><strong>ID:</strong> <span id="view_user_id"></span></p>
                        <p><strong>Role ID:</strong> <span id="view_role_id"></span></p>
                        <p><strong>First Name:</strong> <span id="view_first_name"></span></p>
                        <p><strong>Last Name:</strong> <span id="view_last_name"></span></p>
                        <p><strong>Email:</strong> <span id="view_email"></span></p>
                        <p><strong>Phone:</strong> <span id="view_phone"></span></p>
                        <p><strong>Registration Number:</strong> <span id="view_registration_number"></span></p>
                        <p><strong>Faculty:</strong> <span id="view_faculty"></span></p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"id="closeViewModalBtn"data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Admin Modal -->
        <div class="modal fade" id="deleteUserModal" tabindex="-1" role="dialog" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteUserModalLabel">Delete Admin</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger" role="alert">
                            Are you sure you want to delete this User?
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"id="closeDeleteModalBtn"data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-danger" onclick="deleteUser()"data-dismiss="modal">Confirm Delete</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enable/Disable User Modal -->
        <div class="modal fade" id="enableDisableModal" tabindex="-1" role="dialog" aria-labelledby="enableDisableModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="enableDisableModalLabel">Change Status Of The User</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning" role="alert">
                        <div class="form-group">
                            <label for="enableDisableSelect">Select Action:</label>
                            <select class="form-control" id="enableDisableSelect">
                                <option value="enable">Enable User</option>
                                <option value="disable">Disable User</option>
                            </select>
                        </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="DisEnaCloseBtn" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="confirmEnableDisableBtn">Confirm</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Modal -->
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
                        <p id="successMessage"></p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="successCloseBtn"data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>



        </div>
    </div>
</div>
@endsection

