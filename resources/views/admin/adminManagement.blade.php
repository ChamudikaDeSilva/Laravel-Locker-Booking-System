@extends('layouts.adminNavbar')

@section('content')


<script>
    function appendUserToTable(user) {
    var userId = user.id;
    var existingRow = $('#example tbody tr[data-id="' + userId + '"]');

    if (existingRow.length > 0) {
        // If the row exists, update its content
        existingRow.find('td:eq(1)').text(user.role_id);
        existingRow.find('td:eq(2)').text(user.first_name);
        existingRow.find('td:eq(3)').text(user.last_name);
        existingRow.find('td:eq(4)').text(user.email);
        existingRow.find('td:eq(5)').text(user.phone);
        existingRow.find('td:eq(6)').text(user.registration_number);
        existingRow.find('td:eq(7)').text(user.faculty);
    } else {
        // If the row doesn't exist, append a new one
        $('#example tbody').append(
            '<tr data-id="' + userId + '">' +
            '<td>' + userId + '</td>' +
            '<td>' + user.role_id + '</td>' +
            '<td>' + user.first_name + '</td>' +
            '<td>' + user.last_name + '</td>' +
            '<td>' + user.email + '</td>' +
            '<td>' + user.phone + '</td>' +
            '<td>' + user.registration_number + '</td>' +
            '<td>' + user.faculty + '</td>' +
            '<td>' +
            '<button class="btn btn-info btn-sm" onclick="editAdmin(' + userId + ')">Edit</button>' +
            '<button class="btn btn-primary btn-sm" onclick="viewAdmin(' + userId + ')">View</button>' +
            '<button class="btn btn-danger btn-sm" onclick="confirmDelete(' + userId + ')">Delete</button>' +
            '</td>' +
            '</tr>'
        );
    }
}


function updateUserInTable(user) {
    console.log('Updating row for user with ID: ' + user.id);

    var userId = user.id;
    var existingRow = $('#example tbody tr[data-id="' + userId + '"]');

    if (existingRow.length > 0) {
        // If the row exists, update its content
        existingRow.find('td:eq(1)').text(user.role_id);
        existingRow.find('td:eq(2)').text(user.first_name);
        existingRow.find('td:eq(3)').text(user.last_name);
        existingRow.find('td:eq(4)').text(user.email);
        existingRow.find('td:eq(5)').text(user.phone);
        existingRow.find('td:eq(6)').text(user.registration_number);
        existingRow.find('td:eq(7)').text(user.faculty);
    } else {
        // If the row doesn't exist, append a new one (this should not happen)
        console.error('Error: Row not found for user with ID ' + userId);
    }
}
    function removeUserFromTable(userId) {
        $('#example tbody').find('td:contains("' + userId + '")').closest('tr').remove();
    }

    function fetchDataAndUpdateTable() {
    $.ajax({
        type: 'GET',
        url: '{{ route("admin.getAllUsers") }}', // Replace with your actual route to fetch users
        success: function (data) {
            $('#adminTableBody').append(data);
        },
        error: function (error) {
            console.error('Error fetching data:', error);
        }
    });
}
function saveAdmin() {
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    });
    var formData = $('#addAdminForm').serialize();

    $.ajax({
        type: 'POST',
        url: '{{ route("admin.saveUser") }}',
        data: formData,
        dataType: 'json',
        success: function (data) {
            if (data.status === 'success') {
                //alert('The Admin added successfully');
                $('#addAdminModal').modal('hide');
                appendUserToTable(data.user);
            } else {
                alert('Error adding user: ' + data.message);
            }
        },
        error: function (xhr, status, error) {
            // Check if there's a specific error message from the server
            var errorMessage = 'Error adding user. Please check the console for details.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }

            alert(errorMessage);
        }
    });
}

    //Passing values to the modal
    function editAdmin(userId) {
        // Perform AJAX request to get user details by ID
        $.ajax({
            type: 'GET',
            url: '/admin/getUser/' + userId,
            success: function (data) {
                // Fill the form fields with the retrieved user data
                $('#editAdminForm #user_id').val(data.id);
                $('#editAdminForm #edit_role_id').val(data.role_id);
                $('#editAdminForm #edit_first_name').val(data.first_name);
                $('#editAdminForm #edit_last_name').val(data.last_name);
                $('#editAdminForm #edit_email').val(data.email);
                $('#editAdminForm #edit_phone').val(data.phone);
                $('#editAdminForm #edit_registration_number').val(data.registration_number);
                $('#editAdminForm #edit_faculty').val(data.faculty);

                // Clear password fields
                $('#editAdminForm #edit_password').val('');
                $('#editAdminForm #edit_confirmPassword').val('');

                // Show the edit modal
                $('#editAdminModal').modal('show');
            },
            error: function (error) {
                alert('Error fetching user details');
            }
        });
    }

    //saving the edited details
    function saveChanges() {
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    });

    // Get form data
    var formData = $('#editAdminForm').serialize();
    // Make AJAX request to update user details
    $.ajax({
        type: 'POST',
        url: '/admin/updateUser',
        data: formData,
        success: function (response) {
            // Handle success, e.g., show a success message
            $('#editAdminModal').modal('hide');
            var userId = response.user.id;
            var existingRow = $('#adminTableBody tr[data-id="' + userId + '"]');
                console.log('Existing Row:', existingRow);

            if (existingRow.length > 0) {
                // If the row exists, update its content
                existingRow.find('td:eq(1)').text(response.user.role_id);
                existingRow.find('td:eq(2)').text(response.user.first_name);
                existingRow.find('td:eq(3)').text(response.user.last_name);
                existingRow.find('td:eq(4)').text(response.user.email);
                existingRow.find('td:eq(5)').text(response.user.phone);
                existingRow.find('td:eq(6)').text(response.user.registration_number);
                existingRow.find('td:eq(7)').text(response.user.faculty);
            } else {
                // If the row doesn't exist, append a new one (this should not happen)
                console.error('Error: Row not found for user with ID ' + userId);
            }
        },
        error: function (error) {
            // Handle error, e.g., show an error message
            alert('Error saving changes');
        }
    });
}


    $(document).ready(function () {
    // Click event for the "Close" button in the edit modal
    $('#editAdminModal').on('click', '#closeEditModalBtn', function () {
        $('#editAdminModal').modal('hide');
    });

    $('#editAdminModal').on('click', '.close', function () {
        $('#editAdminModal').modal('hide');
    });

    $('#deleteAdminModal').on('click', '#closeDeleteModalBtn', function () {
        $('#deleteAdminModal').modal('hide');
    });

    $('#deleteAdminModal').on('click', '.close', function () {
        $('#deleteAdminModal').modal('hide');
    });

    $('#viewAdminModal').on('click', '.close', function () {
        $('#viewAdminModal').modal('hide');
    });

    $('#viewAdminModal').on('click', '#closeViewModalBtn', function () {
        $('#viewAdminModal').modal('hide');
    });

});

function confirmDelete(userId) {
    $('#deleteAdminModal').modal('show');

    // Store the user ID in a data attribute of the modal
    $('#deleteAdminModal').data('user-id', userId);
}

//deleting admins
function deleteAdmin() {
    var userId = $('#deleteAdminModal').data('user-id');
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
            //alert('Admin deleted successfully');
            $('#deleteAdminModal').modal('hide');
            if (data.success) {
                removeUserFromTable(userId);
            }

        },
        error: function (error) {
            alert('Error deleting admin');
            $('#deleteAdminModal').modal('hide');
        }
    });
}

// Function to fill and display the "View Admin" modal
function viewAdmin(userId) {
        $.ajax({
            type: 'GET',
            url: '/admin/getUser/' + userId,
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
                $('#viewAdminModal').modal('show');
            },
            error: function (error) {
                alert('Error fetching user details');
            }
        });
    }
</script>

<div class="container">
<div class="content">
    <div class="dashboard-content px-3 pt-4">
        <h2 class="fs-5">Admin Management</h2>
        <hr style="border-top: 2px solid #000;">
        <div class="buttons">
            <!-- Button to trigger modal -->
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addAdminModal">
                Add Admins
            </button>

        </div>

        <div class="table-responsive">
            <!--h3>Admins</h3-->
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
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="adminTableBody">
                    @foreach ($usersWithRoleId1 as $user)
                    <tr data-id="{{ $user->id }}">
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->role_id }}</td>
                                <td>{{ $user->first_name }}</td>
                                <td>{{ $user->last_name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->phone }}</td>
                                <td>{{ $user->registration_number }}</td>
                                <td>{{ $user->faculty }}</td>
                                <td>
                                    <button class="btn btn-info btn-sm" onclick="editAdmin({{ $user->id }})">Edit</button>
                                    <button class="btn btn-primary btn-sm" onclick="viewAdmin({{ $user->id }})">View</button>
                                    <button class="btn btn-danger btn-sm" onclick="confirmDelete({{ $user->id }})">Delete</button>
                                </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- Add Admin Modal -->
<div class="modal fade" id="addAdminModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Admin</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="alert alert-secondary" role="alert">
            <div class="modal-body">
                <!-- Form for adding a new admin -->
                <form id="addAdminForm">
                    @csrf
                    <div class="form-group">
                        <label for="role_id">Role ID</label>
                        <input type="text" class="form-control" id="role_id" name="role_id" value="01" required>
                    </div>
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" class="form-control" id="first_name" name="first_name" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="tel" class="form-control" id="phone" name="phone" required>
                    </div>
                    <div class="form-group">
                        <label for="registration_number">Registration Number</label>
                        <input type="text" class="form-control" id="registration_number" name="registration_number" required>
                    </div>
                    <div class="form-group">
                        <label for="faculty">Faculty</label>
                        <input type="text" class="form-control" id="faculty" name="faculty" required>
                    </div>

                    <!-- Password-related fields -->
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirmPassword">Confirm Password</label>
                        <input type="password" class="form-control" id="confirmPassword" name="password_confirmation" required>
                    </div>

                </form>
            </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="saveAdmin()"data-dismiss="modal">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Admin Modal -->
<div class="modal fade" id="editAdminModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Admin</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-secondary" role="alert">
                <!-- Form for editing an existing admin -->
                <form id="editAdminForm">
                    <input type="hidden" id="user_id" name="user_id"> <!-- Hidden field to store user ID -->

                    <!-- Your existing fields for editing -->
                    <div class="form-group">
                        <label for="role_id">Role ID</label>
                        <input type="text" class="form-control" id="edit_role_id" name="role_id" required>
                    </div>
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" class="form-control" id="edit_first_name" name="first_name" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" class="form-control" id="edit_last_name" name="last_name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="edit_email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="tel" class="form-control" id="edit_phone" name="phone" required>
                    </div>
                    <div class="form-group">
                        <label for="registration_number">Registration Number</label>
                        <input type="text" class="form-control" id="edit_registration_number" name="registration_number" required>
                    </div>
                    <div class="form-group">
                        <label for="faculty">Faculty</label>
                        <input type="text" class="form-control" id="edit_faculty" name="faculty" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="edit_password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirmPassword">Confirm Password</label>
                        <input type="password" class="form-control" id="edit_confirmPassword" name="password_confirmation" required>
                    </div>

                </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" id="closeEditModalBtn">Close</button>
                <button type="button" class="btn btn-success" onclick="saveChanges()"data-dismiss="modal">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Admin Modal -->
<div class="modal fade" id="deleteAdminModal" tabindex="-1" role="dialog" aria-labelledby="deleteAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteAdminModalLabel">Delete Admin</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger" role="alert">
                Are you sure you want to delete this admin?
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"id="closeDeleteModalBtn"data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger" onclick="deleteAdmin()"data-dismiss="modal">Confirm Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- View Admin Modal -->
<div class="modal fade" id="viewAdminModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">View Admin</h5>
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
</div>
</div>
</div>
@endsection
