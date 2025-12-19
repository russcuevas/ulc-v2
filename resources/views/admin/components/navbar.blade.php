<button id="sidebarToggle" class="sidebar-toggle-btn d-none d-lg-flex">
    <i class="fa-solid fa-angle-left"></i>
</button>

{{-- MOBILE SHOW --}}
<nav class="navbar navbar-expand-lg bg-body shadow-sm d-lg-none mb-3 sticky-top">
    <div class="container-fluid">
        <button class="btn btn-link link-body-emphasis text-decoration-none p-0 me-2" type="button"
            data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
            <svg width="24" height="24" fill="currentColor" viewBox="0 0 448 512">
                <path
                    d="M0 96C0 78.3 14.3 64 32 64H416c17.7 0 32 14.3 32 32s-14.3 32-32 32H32C14.3 128 0 113.7 0 96zM0 256c0-17.7 14.3-32 32-32H416c17.7 0 32 14.3 32 32s-14.3 32-32 32H32c-17.7 0-32-14.3-32-32zM448 416c0 17.7-14.3 32-32 32H32c-17.7 0-32-14.3-32-32s14.3-32 32-32H416c17.7 0 32 14.3 32 32z" />
            </svg>
        </button>

        <div class="d-flex align-items-center gap-2">
            {{-- <button class="icon-btn" onclick="alert('3 new notifications')">
                <svg width="20" fill="currentColor" viewBox="0 0 448 512">
                    <path
                        d="M224 0c-17.7 0-32 14.3-32 32V49.9C119.5 61.4 64 124.2 64 200v33.4c0 45.4-15.5 89.5-43.8 124.9L5.3 377c-5.8 7.2-6.9 17.1-2.9 25.4S14.8 416 24 416H424c9.2 0 17.6-5.3 21.6-13.6s2.9-18.2-2.9-25.4l-14.9-18.6C399.5 322.9 384 278.8 384 233.4V200c0-75.8-55.5-138.6-128-150.1V32c0-17.7-14.3-32-32-32zm0 512c-35.3 0-64-28.7-64-64h128c0 35.3-28.7 64-64 64z" />
                </svg>
                <span class="notification-badge"></span>
            </button> --}}

            <div class="user-avatar" data-bs-toggle="dropdown">RV</div>
            <ul class="dropdown-menu dropdown-menu-end shadow">
                <li><a class="dropdown-item" href="">Profile</a></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li>
                    <form action="" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="dropdown-item d-flex gap-2 align-items-center">
                            Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>



{{-- MOBILE SHOW --}}
<div class="offcanvas offcanvas-start" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="mobileSidebarLabel">Menu</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0">
    </div>
</div>

<div class="sidebar-wrapper d-none d-lg-block">
</div>


<div id="sidebarContent" class="d-none">
    <div class="sidebar-brand text-center text-lg-start">
        Ultraritz Lending<br>Corporation <i class="fa-solid fa-coins"></i>
    </div>
    <div class="nav flex-column nav-pills px-3 mt-2">
        <a href=""
            class="nav-link {{ request()->routeIs('admin.dashboard.page') ? 'active' : '' }}">
            <i class="fa-solid fa-house"></i>
            Dashboard
        </a>

        <a href="" class="nav-link ">
            <i class="fa-solid fa-user-lock"></i>
            Admins Information
        </a>

        <a href="" class="nav-link">
            <i class="fa-solid fa-users"></i>
            Clients Information
        </a>

        <a href="" class="nav-link">
            <i class="fa-solid fa-hand-holding-dollar"></i>
            Financial Counselor Area (FC)
        </a>
    </div>
</div>



<header
    class="dashboard-header d-none d-lg-flex justify-content-between align-items-center p-3 rounded-3 bg-body shadow-sm border mb-4">
    <h1 class="h4 mb-0 fw-bold"></h1>
    <div class="d-flex align-items-center gap-3">
        <button class="icon-btn" id="themeToggle">
            <svg id="sunIcon" fill="#fbbf24" width="20" viewBox="0 0 512 512">
                <path
                    d="M361.5 1.2c5 2.1 8.6 6.6 9.6 11.9L391 121l107.9 19.8c5.3 1 9.8 4.6 11.9 9.6s1.5 10.7-1.6 15.2L433.6 256l75.5 90.4c3.1 4.5 3.7 10.2 1.6 15.2s-6.6 8.6-11.9 9.6L391 391 371.1 498.9c-1 5.3-4.6 9.8-9.6 11.9s-10.7 1.5-15.2-1.6L256 433.6 165.6 509.1c-4.5 3.1-10.2 3.7-15.2 1.6s-8.6-6.6-9.6-11.9L121 391 13.1 371.1c-5.3-1-9.8-4.6-11.9-9.6s-1.5-10.7 1.6-15.2L78.4 256 2.9 165.6c-3.1-4.5-3.7-10.2-1.6-15.2s6.6-8.6 11.9-9.6L121 121 140.9 13.1c1-5.3 4.6-9.8 9.6-11.9s10.7-1.5 15.2 1.6L256 78.4 346.4 2.9c4.5-3.1 10.2-3.7 15.2-1.6zM160 256a96 96 0 1 1 192 0 96 96 0 1 1 -192 0zm224 0a128 128 0 1 0 -256 0 128 128 0 1 0 256 0z" />
            </svg>
            <svg id="moonIcon" class="d-none" fill="currentColor" width="20" viewBox="0 0 384 512">
                <path
                    d="M223.5 32C100 32 0 132.3 0 256S100 480 223.5 480c60.6 0 115.5-24.2 155.8-63.4c5-4.9 6.3-12.5 3.1-18.7s-10.1-9.7-17-8.5c-9.8 1.7-19.8 2.6-30.1 2.6c-96.9 0-175.5-78.8-175.5-176c0-65.8 36-123.1 89.3-153.3c6.1-3.5 9.2-10.5 7.7-17.3s-7.3-11.9-14.3-12.5c-6.3-.5-12.6-.8-19-.8z" />
            </svg>
        </button>

        {{-- <button class="icon-btn" onclick="alert('3 new notifications')">
            <svg width="20" fill="currentColor" viewBox="0 0 448 512">
                <path
                    d="M224 0c-17.7 0-32 14.3-32 32V49.9C119.5 61.4 64 124.2 64 200v33.4c0 45.4-15.5 89.5-43.8 124.9L5.3 377c-5.8 7.2-6.9 17.1-2.9 25.4S14.8 416 24 416H424c9.2 0 17.6-5.3 21.6-13.6s2.9-18.2-2.9-25.4l-14.9-18.6C399.5 322.9 384 278.8 384 233.4V200c0-75.8-55.5-138.6-128-150.1V32c0-17.7-14.3-32-32-32zm0 512c-35.3 0-64-28.7-64-64h128c0 35.3-28.7 64-64 64z" />
            </svg>
            <span class="notification-badge"></span>
        </button> --}}

        <div class="dropdown">
            <div class="user-avatar" data-bs-toggle="dropdown" aria-expanded="false">RV</div>
            <ul class="dropdown-menu dropdown-menu-end shadow">
                <li><a class="dropdown-item d-flex gap-2 align-items-center"
                        href="">Profile</a></li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li>
                    <form action="" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="dropdown-item d-flex gap-2 align-items-center">
                            Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>