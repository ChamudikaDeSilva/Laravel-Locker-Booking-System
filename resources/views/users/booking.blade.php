@extends('layouts.navbar')
<style>
    .checkbox-container {

        border: 1px round;
        border-radius: 5px;
        height: 100px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        overflow:hidden;
        text-align:center;
    }
    .text-container {
        max-height: 100%;
        max-width:100%;
        overflow: hidden;
    }
    @media (max-width: 767px) {
        .checkbox-container {
            font-size: 10px;
        }
    }
    .disabled-booked {
        background-color: #db3838;
        opacity: 0.7;
        pointer-events: none;
    }
</style>
@section('content')

<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.8.0/angular.min.js"></script>
<script>
// Define AngularJS app and controller
var app = angular.module('myApp', []);

    app.service('ModalService', function ($q) {
    this.data = {};
    this.modalInstance = null;

    this.openModal = function () {
        this.modalInstance = $('#confirmationModal').modal('show');
    };

    this.closeModal = function () {
        var deferred = $q.defer();

        if (this.modalInstance) {
            this.modalInstance.on('hidden.bs.modal', function () {
                deferred.resolve();
            });

            this.modalInstance.modal('hide');
        } else {
            deferred.resolve();
        }

        return deferred.promise;
    };

    this.closeConfirmationModal = function () {
        var deferred = $q.defer();

        if (this.modalInstance) {
            this.modalInstance.on('hidden.bs.modal', function () {
                deferred.resolve();
            });

            this.modalInstance.modal('hide');
        } else {
            deferred.resolve();
        }

        return deferred.promise;
    };
});

app.controller('myController', function ($scope, $http, $timeout,$element, ModalService) {
        $scope.lockers = []; // Array to store locker information
        $scope.selectedLocker = null;
        $scope.ModalService = ModalService;

        // Add these lines inside your AngularJS controller
        $scope.selectedStartTime = null;
        $scope.selectedEndTime = null;

        // Function to check locker availability
        $scope.checkLockerAvailability = function () {
                // Get selected start time, end time, and date
                $scope.selectedStartTime = document.getElementById('start-time').value;
                $scope.selectedEndTime = document.getElementById('end-time').value;
                var date = document.getElementById('date').value;

                // Make an Ajax request to Laravel backend
                $http.post('/check-locker-availability-01', { startTime: $scope.selectedStartTime, endTime: $scope.selectedEndTime, date: date })
                    .then(function (response) {
                        // Update lockers array with the response data
                        $scope.lockers = response.data.map(function(locker) {
                            return {
                                id: locker.id,
                                locker_type: locker.locker_type,
                                isBooked: locker.isBooked,
                                isInitiallyBooked: locker.isBooked,
                                isDisabled: false,
                            };
                        });
                    })
                    .catch(function (error) {
                        console.error('Error checking locker availability', error);
                    });
        };


        // Function to toggle locker status
        $scope.toggleLockerStatus = function(locker) {
            if ($scope.selectedLocker === locker) {
                // If the same locker is clicked again, unselect it and enable all lockers
                $scope.selectedLocker = null;
                $scope.lockers.forEach(function (otherLocker) {
                    otherLocker.isBooked = otherLocker.isInitiallyBooked; // Revert to initially booked state
                });
            } else {
                // Copy relevant properties to selectedLocker
                $scope.selectedLocker = {
                    id: locker.id,
                    locker_type: locker.locker_type,
                    isBooked: locker.isBooked,
                    startTime: locker.startTime,
                    endTime: locker.endTime,
                    date: locker.date
                };

                // Disable all other lockers except the selected one
                $scope.lockers.forEach(function (otherLocker) {
                    otherLocker.isBooked = otherLocker === $scope.selectedLocker;
                });
            }
        };


        // Function to open the confirmation modal
        $scope.openConfirmationModal = function () {
            var startTime = document.getElementById('start-time').value;
            var endTime = document.getElementById('end-time').value;
            var date = document.getElementById('date').value;

            ModalService.data = {
                startTime: startTime,
                endTime: endTime,
                date: date,
                selectedLocker: $scope.selectedLocker ? $scope.selectedLocker.id : '',
            };

            console.log('ModalService.data:', ModalService.data); // Check the console for debugging

            ModalService.openModal();
        };


        $scope.dateToday = new Date();
        $scope.selectedDate = $scope.dateToday.toISOString().slice(0, 10); // Format as 'yyyy-MM-dd'

        // Function to set the minimum date
        $scope.setMinDate = function () {
            document.getElementById('date').min = $scope.selectedDate;
        };

        // Function to check if the necessary fields are selected
        $scope.areFieldsSelected = function () {
            return $scope.selectedStartTime && $scope.selectedEndTime && $scope.selectedLocker;
        };


        $scope.saveDataAndCloseModal = function () {
            // Check if necessary fields are selected
            if (!$scope.areFieldsSelected()) {
                alert('Please select all required fields.');
                return;
            }

            // Create an object to hold the data to be sent to the server
            var dataToSend = {
                startTime: ModalService.data.startTime,
                endTime: ModalService.data.endTime,
                date: ModalService.data.date,
                selectedLockerId: ModalService.data.selectedLocker || ''
            };

            // Make an Ajax request to Laravel backend for saving the data
            $http.post('/confirm-booking', dataToSend)
                .then(function (response) {
                    // Handle success
                    console.log('Booking confirmed successfully', response.data.message);

                    // Close the confirmation modal
                    ModalService.closeConfirmationModal().then(function () {
                        // Open the success modal
                        $scope.openSuccessModal();
                    });
                })
                .catch(function (error) {
                    // Handle error
                    console.error('Error confirming booking', error.data.error);

                    // Close the confirmation modal
                    ModalService.closeConfirmationModal().then(function () {
                        // Open the error modal
                        $scope.openErrorModal(error.data.error);
                    });
                });
        };

        $scope.openSuccessModal = function () {
            $('#successModal').modal('show');
            // Clear selected values
            $scope.selectedStartTime = null;
            $scope.selectedEndTime = null;
            $scope.selectedDate = null;
            $scope.lockers = []; // Clear lockers array
        };

        $scope.closeSuccessModal = function () {
            // Close the success modal using jQuery
            $('#successModal').modal('hide');
            $('.modal-backdrop').remove();
        };

        $scope.closeConfirmationModal = function () {
            $('#confirmationModal').modal('hide');
            $element.find('.modal-backdrop').remove();

            // Reset values
            $scope.selectedStartTime = null;
            $scope.selectedEndTime = null;
            $scope.selectedDate = null;
            $scope.selectedLocker = null;
            $scope.lockers = []; // Clear lockers array
        };

        $scope.openErrorModal = function (errorMessage) {
            $scope.errorModalMessage = errorMessage;
            $('#errorModal').modal('show');
        };

        $scope.closeErrorModal = function () {
            // Close the success modal using jQuery
            $('#errorModal').modal('hide');
            $('.modal-backdrop').remove();

            // Reset values
            $scope.selectedStartTime = null;
                $scope.selectedEndTime = null;
                $scope.selectedDate = null;
                $scope.selectedLocker = null;
                $scope.lockers = []; // Clear lockers array
    };
});
</script>

<div class="container">
<div ng-app="myApp" ng-controller="myController">
    <div class="content">
        <div class="dashboard-content px-3 pt-4">
            <h2 class="fs-5"> Book A Locker</h2>
            <hr style="border-top: 2px solid #000;">
            <div class="card">
            <div class="row mt-3">
                <div class="col-12">
                        <div class="d-inline-block ms-4">
                            <b><label for="start-time" class="ms-3">Start Time:</label></b>
                            <select id="start-time" class="form-select" ng-model="selectedStartTime">
                                @for ($hour = 8; $hour <= 17; $hour++)
                                    @for ($minute = 0; $minute <= 30; $minute += 30)
                                        <option value="{{ sprintf('%02d:%02d', $hour, $minute) }}">{{ sprintf('%02d:%02d', $hour, $minute) }}</option>
                                    @endfor
                                @endfor
                            </select>
                        </div>
                        <div class="d-inline-block ms-4">
                            <b><label for="end-time" class="ms-3">End Time:</label></b>
                            <select id="end-time" class="form-select" ng-model="selectedEndTime">
                                @for ($hour = 8; $hour <= 17; $hour++)
                                    @for ($minute = 0; $minute <= 30; $minute += 30)
                                    <option value="{{ sprintf('%02d:%02d', $hour, $minute) }}" ng-disabled="selectedStartTime && selectedStartTime >= '{{ sprintf('%02d:%02d', $hour, $minute) }}' && selectedEndTime !== '{{ sprintf('%02d:%02d', $hour, $minute) }}'">{{ sprintf('%02d:%02d', $hour, $minute) }}</option>
                                    @endfor
                                @endfor
                            </select>
                        </div>
                        <div class="d-inline-block ms-4">
                            <b><label for="date" class="ms-3">Date:</label></b>
                            <input type="date" id="date" class="form-control" ng-model="selectedDate" ng-init="setMinDate()" />
                        </div>
                        <div class="d-inline-block ms-4">
                            <button class="btn btn-info ms-3" id="searchBtn" ng-click="checkLockerAvailability()">Check Locker Availability</button>
                        </span>
                        </div>
                    </div>
                </div>
            <br>
        </div>
      <!-- Locker checkboxes container -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        Locker Selection
                    </div>
                    <div class="card-body locker-checkboxes">
                        <div class="row">
                            <div class="col-3" ng-repeat="locker in lockers">
                                <div class="mb-3 checkbox-container" ng-style="{'background-color': locker.isInitiallyBooked ? '#db3838' : '#81e478'}">
                                    <div class="locker-item">
                                        <input type="checkbox" ng-model="locker.isBooked" ng-disabled="locker.isInitiallyBooked" ng-click="toggleLockerStatus(locker)">
                                        <div class="text-container">
                                            <p>Locker Id: @{{ locker.id }}</p>
                                            <p>Type: @{{ locker.locker_type }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br>

        <div id="proceedButton">
            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#confirmationModal" ng-click="openConfirmationModal()" ng-disabled="!areFieldsSelected()">Proceed</button>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Confirmation</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Start Time: @{{ ModalService.data.startTime }}</p>
                        <p>End Time: @{{ ModalService.data.endTime }}</p>
                        <p>Date: @{{ ModalService.data.date }}</p>
                        <p>Selected Locker: @{{ ModalService.data.selectedLocker }}</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" ng-click="saveDataAndCloseModal()">Save</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" ng-click="closeConfirmationModal()">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Modal -->
        <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="successModalLabel">Booking Successful</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-success" role="alert">
                            <p>Your booking is successful!</p>
                        </div>
                        <div class="alert alert-warning" role="alert">
                            <strong>Important:</strong> Please pick up the key no earlier than 15 minutes before the start time. The key must be returned within 15 minutes after the end time. Failure to comply may result in additional charges.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="successCloseBtn" data-dismiss="modal" ng-click="closeSuccessModal()">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Error Modal -->
        <div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="errorModalLabel">Error</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning" role="alert">
                            <p>@{{ errorModalMessage }}</p> <!-- Error message will be inserted here -->
                        </div>
                    </div>
                    <div class="modal-footer">

                        <button type="button" class="btn btn-secondary" data-dismiss="modal" id="errorModalCloseBtn" ng-click="closeErrorModal()">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    </div>
</div>
</div>
</div>
@endsection
