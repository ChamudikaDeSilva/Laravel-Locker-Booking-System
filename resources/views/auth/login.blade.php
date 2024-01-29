@extends('layouts.app')

@section('content')
<style>



    .container {
        margin-top: 50px;
    }

    .card {
        border: 1px solid #ccc;
        border-radius: 10px;



    }

    .card-header {
        background-color: #29ADB2;
        color:#000000;
        font-weight: bold;
    }

    .card-body {
        background-color: #C5E898;
        padding: 20px;

    }

    .form-control {
        margin-bottom: 15px;
        box-shadow: 0 2px 4px#29ADB2;
    }

    .btn-primary {
        background-color: #0766AD;
        border-color:  #0766AD;

        color:#ffffff;
    }

    .btn-primary:hover {
        background-color: #29ADB2;
        border-color:#29ADB2;
    }

    .form-check-label {
        color: #0766AD;
    }

    .btn-link {
        color: #0766AD;
    }

</style>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!--svg xmlns="http://www.w3.org/2000/svg" width="160" height="160" fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16">
                <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3Zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
              </svg-->
            <div class="card">

                <div class="card-header">{{ __('Login') }}</div>

                <div class="card-body">


                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Login') }}
                                </button>

                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                     <!-- Display custom error message for disabled account -->
                     @if(session('loginError'))
                     <div class="alert alert-danger mt-3" role="alert">
                         {{ session('loginError') }}
                     </div>
                 @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
