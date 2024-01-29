<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
    integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
        integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
  <!-- Link to Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">

  <!-- Link to DataTables CSS with Bootstrap styling -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <style>
        body {
            background: #eee;
        }

        #side_nav {
            background-color: #0766AD;
            color: #ffff00;
            min-width: 250px;
            max-width: 250px;
            transition: all 0.3s;
        }

        .content {
            min-height: 100vh;
            width: 100%;
        }

        hr.h-color {
            background: #eee;
        }

        .sidebar li.active {
            background: #eee;
            border-radius: 8px;
        }

        .sidebar li.active a,
        .sidebar li.active a:hover {
            color: #000;
        }

        .sidebar li a {
            color: #fff;
        }

        .sidebar li a:hover {
            background-color: #C5E898;
            color: black;
        }
        .navbar{
            background-color: #0766AD;
        }


        @media (max-width: 767px) {
            #side_nav {
                margin-left: -250px;
                position: absolute;
                min-height: 100vh;
                z-index: 1;
            }

            #side_nav.active {
                margin-left: 0;
            }

            .toggle-btn {
                display: block;
            }

            .navbar-toggler {
                display: block;
            }
        }

        @media (min-width: 768px) {
            .toggle-btn {
                display: none;
            }
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/js/bootstrap.bundle.min.js"></script>

     <script>
        $(document).ready(function () {
            $('.toggle-btn').on('click', function () {
                $('#side_nav').toggleClass('active');
            });
            new DataTable('#example');
        });
    </script>

</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-color shadow-sm">
            <div class="container">
                <button class="btn d-block toggle-btn px-1 py-0 text-dark">
                    <i class="fa-solid fa-bars-staggered text-dark"></i>
                </button>

                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                    aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <i class="fa-solid fa-user text-dark"></i>
                    <!--span class="navbar-toggler-icon"></span-->
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->first_name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                        onclick="event.preventDefault();
                                                         document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-0">
            <div class="main-container d-flex">
                <div class="sidebar" id="side_nav">
                    <div class="header-box px-2 pt-3 pb-4 d-flex justify-content-between">
                        <h1 class="fs-4"><span class="bg-white text-dark rounded shadow px-2 me-2">SB</span> <span class="text-white">Secure Booking</span></h1>
                            <!--button class="btn d-block toggle-btn px-1 py-0 text-dark">
                                <i class="fa-solid fa-bars-staggered text-dark"></i>
                            </button-->
                    </div>
                    <ul class="list-unstyled px-2">
                        <li>
                            <a href="{{ route('users.userDashboard') }}" class="text-decoration-none px-3 py-2 d-block">
                                <i class="fa-solid fa-house"></i> User Dashboard
                            </a>

                        </li>
                        <hr class="h-color mx-2">
                        <li>
                            <a href="{{ route('top-up-form') }}" class="text-decoration-none px-3 py-2 d-block">
                            <i class="fa-solid fa-jet-fighter-up"></i> TopUp Account
                            </a>
                            <a href="{{ route('user.top-up-history') }}" class="text-decoration-none px-3 py-2 d-block">
                                <i class="fa-solid fa-file-invoice-dollar"></i> TopUp History
                            </a>
                            <a href="{{ route('transaction.history') }}" class="text-decoration-none px-3 py-2 d-block">
                            <i class="fa-solid fa-receipt"></i> Payment History
                            </a>
                        </li>
                        <hr class="h-color mx-2">
                        <!--li>
                            <a href="" class="text-decoration-none px-3 py-2 d-block">
                                <i class="fa-solid fa-money-check-dollar"></i> Payment History
                            </a>
                        </li-->

                        <li>
                            <a href="{{ route('book.locker') }}" class="text-decoration-none px-3 py-2 d-block d-flex justify-content-between">
                                <span><i class="fa-solid fa-lock"></i> Book A Locker</span>
                            </a>
                        </li>
                        <hr class="h-color mx-2">
                        <li>
                            <a href="{{ route('booking.history') }}" class="text-decoration-none px-3 py-2 d-block d-flex justify-content-between">
                                <span><i class="fa-solid fa-clock-rotate-left"></i> Current Bookings</span>
                            </a>
                            <a href="{{ route('booking.Allhistory') }}" class="text-decoration-none px-3 py-2 d-block d-flex justify-content-between">
                                <span><i class="fa-solid fa-bookmark"></i> Booking History</span>
                            </a>
                        </li>
                        <hr class="h-color mx-2">
                        <li>
                            <a href="{{ route('profile') }}" class="text-decoration-none px-3 py-2 d-block">
                               <span> <i class="fa-solid fa-user"></i></span> My Profile
                            </a>
                        </li>
                    </ul>
                <hr class="h-color mx-2">
                </div>
                @yield('content')

            </div>
        </main>
    </div>
    </div>
</body>
</html>
