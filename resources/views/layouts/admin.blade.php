<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Admin Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            display: flex;
            flex-direction: column; /* Use column layout */
            margin: 0;
            height: 100vh; /* Ensure full height */
        }

        .sidebar {
            width: 250px;
            background-color: #01386f;
            height: 100%; /* Set height to 100% */
            overflow-y: auto; /* Enable vertical scrolling only for sidebar */
        }

        .sidebar ul {
            list-style-type: none;
            padding: 0;
            margin: 0; /* Remove default margin */
        }

        .sidebar li {
            padding: 10px;
            text-align: left;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
        }

        .sidebar a:hover {
            background-color: #024384;
        }

        .sidebar hr {
            border-color: #012142;
        }

        .active {
            background-color: #007bff;
            color: white;
        }

        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column; /* Column layout for main content */
        }

        header {
            background-color: #92c7fd;
            margin: 0; /* Remove margin */
            padding: 15px; /* Optional padding */
            border-bottom: 1px solid #012243;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-right {
            display: flex;
            align-items: center;
        }

        .notification {
            margin-right: 20px;
            position: relative;
        }

        .notification .badge {
            position: absolute;
            top: -5px;
            right: -10px;
            background-color: red;
            color: white;
            padding: 3px 6px;
            border-radius: 50%;
        }

        footer {
            background-color: #92c7fd;
            padding: 10px;
            text-align: center;
            border-top: 1px solid #012243;
            margin: 0; /* Remove margin */
        }

        /* Dropdown styles */
        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: white;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            z-index: 1;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .dropdown-item {
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            color: black;
        }

        .dropdown-item:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <ul>
            <li class="nav-item {{ Request::is('admin/dashboard') ? 'active' : '' }}">
                <a href="/admin/dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            </li>
            <hr class="sidebars">

            <li class="nav-item {{ Request::is('admin/user/create') || Request::is('admin/users') || Request::is('admin/user/trash') ? 'active' : '' }}">
                <a href="/admin/users"><i class="fas fa-users"></i> Users</a>
            </li>
            <hr class="sidebars">

            <li class="nav-item {{ Request::is('admin/coupon/create') || Request::is('admin/coupon') || Request::is('admin/coupon/trash') ? 'active' : '' }}">
                <a href="/admin/coupons"><i class="fas fa-tag"></i> Coupons</a>
            </li>
            <hr class="sidebars">

            <li class="nav-item {{ Request::is('admin/payments') ? 'active' : '' }}">
                <a href="/admin/payment-status"><i class="fas fa-credit-card"></i> Payment Status</a>
            </li>
            <hr class="sidebars">

            <li class="nav-item {{ Request::is('admin/booked') ? 'active' : '' }}">
                <a href="/admin/booked-details"><i class="fas fa-book"></i> Booked Details</a>
            </li>
            <hr class="sidebars">

            <li class="nav-item {{ Request::is('admin/pending') ? 'active' : '' }}">
                <a href="/admin/pending-status"><i class="fas fa-hourglass-half"></i> Pending Status</a>
            </li>
            <hr class="sidebars">

            <li class="nav-item {{ Request::is('admin/transfer') ? 'active' : '' }}">
                <a href="/admin/transfer"><i class="fas fa-exchange-alt"></i> Transfer</a>
            </li>
            <hr class="sidebars">

            <li class="nav-item {{ Request::is('admin/parking') ? 'active' : '' }}">
                <a href="/admin/parking"><i class="fas fa-parking"></i> Airport Parking</a>
            </li>
            <hr class="sidebars">

            <li class="nav-item {{ Request::is('admin/insurance') ? 'active' : '' }}">
                <a href="/admin/insurance"><i class="fas fa-shield-alt"></i> Insurance</a>
            </li>
            <hr class="sidebars">

            <li class="nav-item {{ Request::is('admin/luggage') ? 'active' : '' }}">
                <a href="/admin/luggage"><i class="fas fa-suitcase"></i> Luggage</a>
            </li>
            <hr class="sidebars">

            <li class="nav-item {{ Request::is('admin/subscribers') ? 'active' : '' }}">
                <a href="/admin/subscribers"><i class="fas fa-envelope"></i> Subscribers</a>
            </li>
            <hr class="sidebars">

            <li class="nav-item {{ Request::is('admin/registeredUser') ? 'active' : '' }}">
                <a href="/admin/registeredUser"><i class="fas fa-user-check"></i> Registered Users</a>
            </li>
            <hr class="sidebars">

            <li class="nav-item {{ Request::is('admin/post/create') || Request::is('admin/post') ? 'active' : '' }}">
                <a href="/admin/posts"><i class="fas fa-edit"></i> Posts</a>
            </li>
            <hr class="sidebars">

            <li class="nav-item {{ Request::is('admin/testimonial/create') || Request::is('admin/testimonial') || Request::is('admin/testimonial/delete') ? 'active' : '' }}">
                <a href="/admin/testimonials"><i class="fas fa-comment"></i> Testimonials</a>
            </li>
            <hr class="sidebars">

            <li class="nav-item {{ Request::is('admin/history') ? 'active' : '' }}">
                <a href="/admin/history"><i class="fas fa-history"></i> History</a>
            </li>
            <hr class="sidebars">

            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>
    </div>

    <!-- Main content -->
    <div class="main-content">
        <!-- Header -->
        <header>
            <h1>@yield('page-title')</h1>
            <div class="header-right">
                <div class="notification">
                    <i class="fas fa-bell"></i>
                    <span class="badge">0</span>
                </div>
                <div class="dropdown">
                    <div class="profile" style="cursor: pointer;">
                        <i class="fas fa-user"></i>
                        <span>Ritik</span>
                    </div>
                    <div class="dropdown-content">
                        <a class="dropdown-item" href="{{ route('profile') }}">Profile</a>
                        <a class="dropdown-item" href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            Logout
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <div class="content" style="flex: 1; overflow-y: auto;"> <!-- Allow scrolling within the content area -->
            @yield('content')
        </div>

        <!-- Footer -->
        <footer>
            <p>Copyright © Sky Sea Holiday 2024</p>
        </footer>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
</body>

</html>
