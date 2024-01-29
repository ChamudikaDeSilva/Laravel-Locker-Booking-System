@extends('layouts.AdminNavbar')

@section('content')
<style>

</style>
<script>
    function getUserDetails() {
        const userEmail = document.getElementById('user_email').value;

        // Validate email format
        if (!userEmail || !isValidEmail(userEmail)) {
            displayErrorMessage('Please enter a valid email address.');
        return;
        }

        axios.post('/admin/get-user-details', { email: userEmail })
            .then(response => {
                const userDetailsDiv = document.getElementById('userDetails');

                if (response.data.success) {
                    const user = response.data.user;

                    userDetailsDiv.innerHTML = `
                        <p><b>First Name:</b> ${user.first_name}</p>
                        <p><b>Last Name:</b> ${user.last_name}</p>
                        <p><b>Email:</b> ${user.email}</p>
                        <p><b>Phone:</b> ${user.phone}</p>
                        <p><b>Registration Number:</b> ${user.registration_number}</p>
                        <p><b>Faculty:</b> ${user.faculty}</p>
                    `;
                } else {
                    displayErrorMessage('User not found.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                displayErrorMessage('An error occurred while fetching user details.');
            });
    }

    function topUpAccount() {
    const userEmailInput = document.getElementById('user_email');
    const topupAmountInput = document.getElementById('topup_amount');
    const userDetailsDiv = document.getElementById('userDetails');

    const userEmail = userEmailInput.value;
    const topupAmount = topupAmountInput.value;

    // Validate email format
    if (!userEmail || !isValidEmail(userEmail)) {
            displayErrorMessage('Please enter a valid email address.');
        return;
    }

    axios.post('/admin/top-up-account', { email: userEmail, amount: topupAmount })
        .then(response => {
            console.log('Response:', response);

            // Display success modal on successful top-up
            if (response.data.success) {
                const successMessage = response.data.message;
                document.getElementById('successMessage').innerText = successMessage;
                $('#successModal').modal('show');

                // Clear user details on successful top-up
                userEmailInput.value = '';
                topupAmountInput.value = '';
                userDetailsDiv.innerHTML = '';
            } else {
                displayErrorMessage('Cannot topup an unknown user.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
}


    // Validate email format using a simple regular expression
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    // Add the following function
    function redirectToPaymentHistory() {
        window.location.href = '/admin/payment-history';
    }

    function displayErrorMessage(message) {
        document.getElementById('errorMessageText').innerText = message;
        $('#errorMessage').modal('show');
    }

    $(document).ready(function () {
        //success modal
        $('#successCloseBtn').on('click', function () {
            $('#successModal').modal('hide');
        });

        $('#successModal').find('.close').on('click', function () {
            $('#successModal').modal('hide');
        });

        //error modal
        $('#errorMessage').find('.close').on('click',function(){
            $('#errorMessage').modal('hide');
        });

        $('#errorModalCloseBtn').on('click', function () {
            $('#errorMessage').modal('hide');
        });
    });

</script>
    <div class="container">
    <div class="content">
        <div class="dashboard-content px-3 pt-4">
            <h2 class="fs-5">Top Up User Account</h2>
        <hr style="border-top: 2px solid #000;">
        <div class="card">
            <div class="card-body">
            <div class="mb-3">
               <h6><label for="user_email" class="form-label">User Email:</label></h6>
                <div class="input-group">
                    <input type="email" class="form-control" id="user_email" placeholder="Enter user email">
                    <button class="btn btn-info" onclick="getUserDetails()">Search</button>
                </div>
                <div id="userDetails" class="mt-2"></div>
            </div>

            <div class="mb-3">
                <h6><label for="topup_amount" class="form-label">Top-up Amount:</label></h6>
                <div class="input-group">
                    <input type="text" class="form-control" id="topup_amount" placeholder="Enter amount">
                    <button class="btn btn-success" onclick="topUpAccount()">Top-up Account</button>
                </div>
            </div>
          
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
                    <button type="button" class="btn btn-secondary" id="successCloseBtn">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!--error modal-->
    <div id="errorMessage" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="errorModalLabel">Error</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger" role="alert">
                    <p id="errorMessageText"></p>
                </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="errorModalCloseBtn">Close</button>
                </div>
            </div>
        </div>
    </div>


</div>

@endsection
