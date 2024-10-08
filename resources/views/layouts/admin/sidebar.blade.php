<ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar"
    style="background-color: #006DCF; border-right : solid grey 2px">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-start" href="{{ url('/dashboard') }}">
        {{-- <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Holiday Admin Panel<sup> </sup></div> --}}
        <img src="{{ asset('admin\img\logo.png') }}" alt="Holiday Admin Panel" style="width: 8rem;"
            class="sidebar-brand-image">
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{ Request::is('/dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ url('/dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Interface
    </div>

    <!-- Nav Item - user Menu -->
    <li
        class="nav-item {{ Request::is('admin/user/create') || Request::is('admin/users') || Request::is('admin/user/trash') ? 'active' : '' }}">
        <a class="nav-link {{ Request::is('admin/user/create') || Request::is('admin/user') || Request::is('admin/user/trash') ? '' : 'collapsed' }}"
            href="#" data-toggle="collapse" data-target="#collapseCategory"
            aria-expanded="{{ Request::is('admin/user/create') || Request::is('admin/user') ? true : false }}"
            aria-controls="collapseCategory">
            <i class="fas fa-user"></i>
            <span>Users</span>
        </a>
        <div id="collapseCategory"
            class="collapse {{ Request::is('admin/user/create') || Request::is('admin/users') || Request::is('admin/user/trash') ? 'show' : '' }}"
            aria-labelledby="headingCategory" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                @if (Auth::guard('admin')->user()->user_type == 1)
                    <a class="collapse-item {{ Request::is('admin/user/create') ? 'active' : '' }}"
                        href="{{ url('/admin/user/create') }}">Add User</a>
                @endif
                <a class="collapse-item {{ Request::is('admin/users') ? 'active' : '' }}"
                    href="{{ url('/admin/users') }}">View user</a>
                <a class="collapse-item {{ Request::is('admin/user/trash') ? 'active' : '' }}"
                    href="{{ url('/admin/user/trash') }}">Deleted Users</a>
            </div>
        </div>
    </li>

    <hr class="sidebar-divider">

    <!-- Coupons Section-->
    <li
        class="nav-item {{ Request::is('admin/coupon/create') || Request::is('admin/coupon') || Request::is('admin/coupon/trash') ? 'active' : '' }}">
        <a class="nav-link {{ Request::is('admin/coupon/create') || Request::is('admin/coupon') || Request::is('admin/coupon/trash') ? '' : 'collapsed' }}"
            href="#" data-toggle="collapse" data-target="#collapsecoupon"
            aria-expanded="{{ Request::is('admin/coupon/create') || Request::is('admin/coupon') ? true : false }}"
            aria-controls="collapsecoupon">
            <i class="fa fa-gift" style="font-size: 15px"></i>
            <span>Coupon</span>
        </a>
        <div id="collapsecoupon"
            class="collapse {{ Request::is('admin/coupon/create') || Request::is('admin/coupon/trash') || Request::is('admin/coupon') ? 'show' : '' }}"
            aria-labelledby="headingcoupon" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item {{ Request::is('admin/coupon/create') ? 'active' : '' }}"
                    href="{{ url('/admin/coupon/create') }}">Add coupon</a>
                <a class="collapse-item {{ Request::is('admin/coupon') ? 'active' : '' }}"
                    href="{{ url('/admin/coupon') }}">View coupon</a>
                <a class="collapse-item {{ Request::is('admin/coupon/trash') ? 'active' : '' }}"
                    href="{{ url('/admin/coupon/trash') }}">Experied/Deleted</a>
            </div>
        </div>
    </li>

    <!-- Nav Item - Featured Menu -->
    <hr class="sidebar-divider">

    {{-- <li
        class="nav-item {{ Request::is('admin/booked/flight')  || Request::is('admin/booked/package') || Request::is('admin/booked/hotel') ? 'active' : '' }} ">
        <a class="nav-link {{ Request::is('admin/featured/coupon') || Request::is('admin/featured/users') || Request::is('admin/booked/hotel')? '' : 'collapsed' }} "
            href="#" data-toggle="collapse" data-target="#collapseFeatured"
            aria-expanded="{{ Request::is('admin/featured/coupon') || Request::is('admin/featured/users') ? true : false }}"
            aria-controls="collapseFeatured">
            <i class="fab fa-stack-exchange"></i>
            <span>Booking Details</span>
        </a>
        <div id="collapseFeatured"
            class="collapse {{Request::is('admin/featured/coupon') || Request::is('admin/featured/users') ? 'show' : ''}}"
            aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item {{ Request::is('admin/booked/flight') ? 'active' : '' }}"
                    href="{{ url('/admin/booked/flight') }}">Flights</a>
                <a class="collapse-item {{ Request::is('admin/booked/package') ? 'active' : '' }}"
                    href="{{ url('/admin/booked/package') }}">Packages</a>
                <a class="collapse-item {{ Request::is('admin/booked/hotel') ? 'active' : '' }}"
                    href="{{ url('/admin/booked/hotel') }}">Hotels </a>
            </div>
        </div>
    </li> --}}

    <li class="nav-item {{ Request::is('admin/payments') ? 'active' : '' }}">
        <a class="nav-link" href="{{ url('/admin/payments') }}">
            <i class="fas fa-credit-card" style="font-size: 15px"></i>
            <span>Payment Status</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <li class="nav-item {{ Request::is('admin/booked') ? 'active' : '' }}">
        <a class="nav-link" href="{{ url('/admin/booked') }}">
            <i class="fas fa-book"></i>
            <span>Booked Details</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <li class="nav-item {{ Request::is('admin/pending') ? 'active' : ' ' }}">
        <a class="nav-link" href="{{ url('/admin/pending') }}">
            <i class="fas fa-hourglass-half"></i>
            <span>Pending Status</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <li class="nav-item {{ Request::is('admin/transfer') ? 'active' : ' ' }}">
        <a class="nav-link" href="{{ url('/admin/transfer') }}">
            <i class="fas fa-exchange-alt"></i>
            <span>Transfers</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <li class="nav-item {{ Request::is('admin/parking') ? 'active' : ' ' }}">
        <a class="nav-link" href="{{ url('/admin/parking') }}">
            <i class="fas fa-parking"></i>
            <span>Airport Parking</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <li class="nav-item {{ Request::is('admin/insurance') ? 'active' : ' ' }}">
        <a class="nav-link" href="{{ url('/admin/insurance') }}">
            <i class="fas fa-shield-alt"></i>
            <span>Insurance</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <li class="nav-item {{ Request::is('admin/luggage') ? 'active' : ' ' }}">
        <a class="nav-link" href="{{ url('/admin/luggage') }}">
            <i class="fas fa-suitcase"></i>
            <span>Luggage</span>
        </a>
    </li>



    {{-- <li
        class="nav-item {{ Request::is('admin/featured/coupon') || Request::is('admin/featured/users') ? 'active' : '' }} ">
        <a class="nav-link {{ Request::is('admin/featured/coupon') || Request::is('admin/featured/users') ? '' : 'collapsed' }} "
            href="#" data-toggle="collapse" data-target="#collapseFeatured"
            aria-expanded="{{ Request::is('admin/featured/coupon') || Request::is('admin/featured/users') ? true : false }}"
            aria-controls="collapseFeatured">
            <i class="fab fa-stack-exchange"></i>
            <span>Pending Details</span>
        </a>
        <div id="collapseFeatured"
            class="collapse {{Request::is('admin/featured/coupon') || Request::is('admin/featured/users') ? 'show' : ''}}"
            aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item {{ Request::is('admin/pending/flight') ? 'active' : '' }}"
                    href="{{ url('/admin/pending/flight') }}">Flights</a>
                <a class="collapse-item {{ Request::is('admin/pending/package') ? 'active' : '' }}"
                    href="{{ url('/admin/pending/package') }}">Packages</a>
                <a class="collapse-item {{ Request::is('admin/pending/hotel') ? 'active' : '' }}"
                    href="{{ url('/admin/pending/hotel') }}">Hotels </a>
            </div>
        </div>
    </li> --}}

    {{-- <li
        class="nav-item {{ Request::is('admin/featured/coupon') || Request::is('admin/featured/users') ? 'active' : '' }} ">
        <a class="nav-link {{ Request::is('admin/featured/coupon') || Request::is('admin/featured/users') ? '' : 'collapsed' }} "
            href="#" data-toggle="collapse" data-target="#collapsePackage"
            aria-expanded="{{ Request::is('admin/featured/coupon') || Request::is('admin/featured/users') ? true : false }}"
            aria-controls="collapsePackage">
            <i class="fa fa-suitcase"></i>
            <span>Packages</span>
        </a>
        <div id="collapsePackage"
            class="collapse {{ Request::is('admin/featured/coupon') || Request::is('admin/featured/users') ? 'show' : '' }}"
            aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item {{ Request::is('admin/featured/coupon') ? 'active' : '' }}"
                    href="{{ url('/admin/featured/coupon') }}">Holiday</a>
                <a class="collapse-item {{ Request::is('admin/featured/users') ? 'active' : '' }}"
                    href="{{ url('/admin/featured/users') }}">People</a>
            </div>
        </div>
    </li>

    <hr class="sidebar-divider">

    <li
        class="nav-item {{ Request::is('admin/featured/coupon') || Request::is('admin/featured/users') ? 'active' : '' }} ">
        <a class="nav-link {{ Request::is('admin/featured/coupon') || Request::is('admin/featured/users') ? '' : 'collapsed' }} "
            href="#" data-toggle="collapse" data-target="#collapseFlight"
            aria-expanded="{{ Request::is('admin/featured/coupon') || Request::is('admin/featured/users') ? true : false }}"
            aria-controls="collapseFlights">
            <i class="fa fa-plane"></i>
            <span>Flights</span>
        </a>
        <div id="collapseFlight"
            class="collapse {{ Request::is('admin/featured/coupon') || Request::is('admin/featured/users') ? 'show' : '' }}"
            aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item {{ Request::is('admin/featured/coupon') ? 'active' : '' }}"
                    href="{{ url('/admin/featured/coupon') }}">Booked</a>
                <a class="collapse-item {{ Request::is('admin/featured/users') ? 'active' : '' }}"
                    href="{{ url('/admin/featured/users') }}">Pending</a>
            </div>
        </div>
    </li>

    <hr class="sidebar-divider">

    <li
        class="nav-item {{ Request::is('admin/featured/coupon') || Request::is('admin/featured/users') ? 'active' : '' }} ">
        <a class="nav-link {{ Request::is('admin/featured/coupon') || Request::is('admin/featured/users') ? '' : 'collapsed' }} "
            href="#" data-toggle="collapse" data-target="#collapseHotel"
            aria-expanded="{{ Request::is('admin/featured/coupon') || Request::is('admin/featured/users') ? true : false }}"
            aria-controls="collapseHotel">
            <i class="fa fa-building"></i>
            <span>Hotels</span>
        </a>
        <div id="collapseHotel"
            class="collapse {{ Request::is('admin/featured/coupon') || Request::is('admin/featured/users') ? 'show' : '' }}"
            aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item {{ Request::is('admin/featured/coupon') ? 'active' : '' }}"
                    href="{{ url('/admin/featured/coupon') }}">Booked</a>
                <a class="collapse-item {{ Request::is('admin/featured/users') ? 'active' : '' }}"
                    href="{{ url('/admin/featured/users') }}">Pending</a>
            </div>
        </div>
    </li>--}}

    <hr class="sidebar-divider">

    <li class="nav-item {{ Request::is('admin/subscribers') ? 'active' : ' ' }}">
        <a class="nav-link" href="{{ url('/admin/subscribers    ') }}">
            <i class="fas fa-envelope"></i>
            <span>Subscribers</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <li class="nav-item {{ Request::is('admin/registeredUser') ? 'active' : ' ' }}">
        <a class="nav-link" href="{{ url('/admin/registeredUser') }}">
            <i class="fas fa-user-check"></i>
            <span>Registered Users</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <li class="nav-item {{ Request::is('admin/post/create') || Request::is('admin/post') ? 'active' : '' }} ">
        <a class="nav-link {{ Request::is('admin/post/create') || Request::is('admin/post') ? '' : 'collapsed' }} "
            href="#" data-toggle="collapse" data-target="#collapsePost"
            aria-expanded="{{ Request::is('admin/post/create') || Request::is('admin/post') ? true : false }}"
            aria-controls="collapsePost">
            <i class="fas fa-edit"></i>
            <span>Posts</span>
        </a>
        <div id="collapsePost"
            class="collapse {{ Request::is('admin/post/create') || Request::is('admin/post') ? 'show' : '' }}"
            aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item {{ Request::is('admin/post/create') ? 'active' : '' }}"
                    href="{{ url('/admin/post/create') }}">Add Post</a>
                <a class="collapse-item {{ Request::is('admin/post') ? 'active' : '' }}"
                    href="{{ url('/admin/post') }}">Manage Post</a>
                <a class="collapse-item {{ Request::is('admin/post/delete') ? 'active' : '' }}"
                    href="{{ url('/admin/post/delete') }}">Deleted Post</a>
            </div>
        </div>
    </li>

    <hr class="sidebar-divider">

    <li
        class="nav-item {{ Request::is('admin/testimonial/create') || Request::is('admin/testimonial') || Request::is('admin/testimonial/delete') ? 'active' : '' }} ">
        <a class="nav-link {{ Request::is('admin/testimonial/create') || Request::is('admin/testimonial') || Request::is('admin/testimonial/delete') ? '' : 'collapsed' }} "
            href="#" data-toggle="collapse" data-target="#collapseTestimonial"
            aria-expanded="{{ Request::is('admin/testimonial/create') || Request::is('admin/testimonial') || Request::is('admin/testimonial/delete') ? true : false }}"
            aria-controls="collapseTestimonial">
            <i class="fas fa-comment"></i>
            <span>Testimonials</span>
        </a>
        <div id="collapseTestimonial"
            class="collapse {{ Request::is('admin/testimonial/create') || Request::is('admin/testimonial') || Request::is('admin/testimonial/delete') ? 'show' : '' }}"
            aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item {{ Request::is('admin/testimonial/create') ? 'active' : '' }}"
                    href="{{ url('/admin/testimonial/create') }}">Add Testimonial</a>
                <a class="collapse-item {{ Request::is('admin/testimonial/publish') ? 'active' : '' }}"
                    href="{{ url('/admin/testimonial/publish') }}">Manage Testimonials</a>
                <a class="collapse-item {{ Request::is('admin/testimonial/draft') ? 'active' : '' }}"
                    href="{{ url('/admin/testimonial/draft') }}">Draft Testimonials</a>
            </div>
        </div>
    </li>

    <hr class="sidebar-divider">

    <li class="nav-item {{ Request::is('admin/history') ? 'active' : ' ' }}">
        <a class="nav-link" href="{{ url('/admin/history') }}">
            <i class="fas fa-history"></i>
            <span>History</span>
        </a>
    </li>


    {{-- <!-- Nav Item - Utilities Collapse Menu -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities"
            aria-expanded="true" aria-controls="collapseUtilities">
            <i class="fas fa-fw fa-wrench"></i>
            <span>Utilities</span>
        </a>
        <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Custom Utilities:</h6>
                <a class="collapse-item" href="utilities-color.html">Colors</a>
                <a class="collapse-item" href="utilities-border.html">Borders</a>
                <a class="collapse-item" href="utilities-animation.html">Animations</a>
                <a class="collapse-item" href="utilities-other.html">Other</a>
            </div>
        </div>
    </li> --}}

    {{-- <!-- Divider -->
    <hr class="sidebar-divider"> --}}

    <!-- Heading -->
    {{-- <div class="sidebar-heading">
        Addons
    </div> --}}

    <!-- Nav Item - Pages Collapse Menu -->
    {{-- <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages" aria-expanded="true"
            aria-controls="collapsePages">
            <i class="fas fa-fw fa-folder"></i>
            <span>Pages</span>
        </a>
        <div id="collapsePages" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Login Screens:</h6>
                <a class="collapse-item" href="login.html">Login</a>
                <a class="collapse-item" href="register.html">Register</a>
                <a class="collapse-item" href="forgot-password.html">Forgot Password</a>
                <div class="collapse-divider"></div>
                <h6 class="collapse-header">Other Pages:</h6>
                <a class="collapse-item" href="404.html">404 Page</a>
                <a class="collapse-item" href="blank.html">Blank Page</a>
            </div>
        </div>
    </li>

    <!-- Nav Item - Charts -->
    <li class="nav-item">
        <a class="nav-link" href="charts.html">
            <i class="fas fa-fw fa-chart-area"></i>
            <span>Charts</span></a>
    </li>

    <!-- Nav Item - Tables -->
    <li class="nav-item">
        <a class="nav-link" href="tables.html">
            <i class="fas fa-fw fa-table"></i>
            <span>Tables</span></a>
    </li> --}}

    <!-- Divider -->
    {{--
    <hr class="sidebar-divider d-none d-md-block"> --}}

    <!-- Sidebar Toggler (Sidebar) -->

    <!-- Divider -->
    <hr class="sidebar-divider">

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>



</ul>
