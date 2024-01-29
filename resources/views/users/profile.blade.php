@extends('layouts.navbar')
@section('content')
<style>
    .image-container{
        padding:15px;
    }
    .detail-container{
        padding:15px;
    }
</style>

<div class="container">
<div class="content">
    <div class="dashboard-content px-3 pt-4">
        <h2 class="fs-5">Profile</h2>
        <hr style="border-top: 2px solid #000;">

    </div>

    <div class="card mb-3" style="max-width: 1080px;">
        <div class="row g-0">
          <div class="col-md-4">
            <img src="{{ asset('images/flat-business-man-user-profile-avatar-icon-vector-4333097.jpg') }}" alt="User Image" class="img-fluid rounded-start">
          </div>
          <div class="col-md-8">
            <div class="card-body">
              <h2 class="card-title">Your Profile</h2>
              <p><strong>Name:</strong> {{ $user->first_name }} {{ $user->last_name }}</p>
                    <p><strong>Email:</strong> {{ $user->email }}</p>
                    <p><strong>Faculty:</strong> {{ $user->faculty }}</p>
                    <p><strong>Registration Number:</strong> {{ $user->registration_number }}</p>
                    <p><strong>Phone Number:</strong> {{ $user->phone }}</p>
                    <button type="button" class="btn btn-info" data-toggle="modal" data-target="#editModal">
                        Edit Profile
                    </button>
            </div>
          </div>
        </div>
      </div>



    <!--Edit Modal-->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Profile</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" action="{{ route('profile.update') }}">
                        @csrf
                        <div class="form-group">
                            <label for="first_name">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" value="{{ $user->first_name }}">
                        </div>

                        <div class="form-group">
                            <label for="first_name">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" value="{{ $user->last_name }}">
                        </div>

                        <div class="form-group">
                            <label for="faculty">Faculty</label>
                            <select class="form-control" id="faculty" name="faculty">
                                <option value="applied_sciences" {{ $user->faculty == 'Applied_sciences' ? 'selected' : '' }}>Faculty of Applied Sciences</option>
                                <option value="business_studies" {{ $user->faculty == 'business_studies' ? 'selected' : '' }}>Faculty of Business Studies</option>
                                <option value="technological_studies" {{ $user->faculty == 'technological_studies' ? 'selected' : '' }}>Faculty of Technological Studies</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="first_name">Registration Number</label>
                            <input type="text" class="form-control" id="registration_number" name="registration_number" value="{{ $user->registration_number }}">
                        </div>

                        <div class="form-group">
                            <label for="first_name">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}">
                        </div>

                        <div class="form-group">
                            <label for="first_name">Phone</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="{{ $user->phone }}">
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password">
                        </div>
                        <button type="submit" class="btn btn-success ">Save changes</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

