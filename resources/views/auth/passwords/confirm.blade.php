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
        background-color: #4d0026;
        color:#ffff00;
        font-weight: bold;
    }

    .card-body {
        background-color: #e2bee2;
        padding: 20px;
    }

    .form-control {
        margin-bottom: 15px;
        box-shadow: 0 2px 4px #B04759;
    }

    .btn-primary {
        background-color:#B04759;
        border-color:#B04759;
        color:#ffff00;
    }

    .btn-primary:hover {
        background-color: #4d0026;
        border-color:  #4d0026;
    }

    .form-check-label {
        color: #555;
    }

    .btn-link {
        color: #4d0026 !important;
    }
    </style>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Confirm Password') }}</div>

                <div class="card-body">
                    {{ __('Please confirm your password before continuing.') }}

                    <form method="POST" action="{{ route('password.confirm') }}">
                        @csrf

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

                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Confirm Password') }}
                                </button>

                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
