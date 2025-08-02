<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">  
    <link rel="stylesheet" href="{{ asset('css/adminDashboard.css') }}">
    @livewireStyles
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        @php
            $classRoutes = ['upcoming.class', 'live.class', 'previous.class'];
            $isClassRoute = in_array(Route::currentRouteName(), $classRoutes);
        @endphp
        <div class="sidebar-header">
            STUDENT PANEL
        </div>
        <ul class="sidebar-nav">
            <li><a href="{{ route('student.dashboard') }}" class="{{ request()->routeIs('student.dashboard') ? 'active' : '' }}" wire:navigate>Dashboard</a></li>
            <!-- Class Menu -->
            <li>
                <a data-bs-toggle="collapse" href="#classSubmenu" role="button" aria-expanded="{{ $isClassRoute ? 'true' : 'false' }}" aria-controls="classSubmenu"> Class <i class="bi bi-chevron-down float-end"></i></a>
                <div class="collapse {{ $isClassRoute ? 'show' : '' }}" id="classSubmenu">
                    <ul class="submenu list-unstyled ps-3">
                        <li>
                            <a href="{{ route('upcoming.class') }}" class="{{ request()->routeIs('upcoming.class') ? 'active' : '' }}" wire:navigate>Upcoming Class</a>
                        </li>
                        <li>
                            <a href="{{ route('live.class') }}" class="{{ request()->routeIs('live.class') ? 'active' : '' }}" wire:navigate>Ongoing Class</a>
                        </li>
                        <li>
                            <a href="{{ route('previous.class') }}" class="{{ request()->routeIs('previous.class') ? 'active' : '' }}" wire:navigate>Previous Class</a>
                        </li>
                    </ul>
                </div>
            </li>
            <li><a href="{{ route('mock.test') }}" class="{{ request()->routeIs('mock.test') ? 'active' : '' }}" wire:navigate>Mock Test</a></li>
            <li><a href="{{ route('test.results') }}" class="{{ request()->routeIs('test.results') ? 'active' : '' }}" wire:navigate>Test Results</a></li>
        </ul>
        <div class="sidebar-footer">
            <form>
                <button type="submit" class="logout-btn">
                    <i data-lucide="log-out"></i>
                    <a href="{{ route('student.logout') }}" class="logout-text" style="all:unset">Logout</a>
                </button>
            </form>
        </div>
    </div>

    @php
        $notifications = Auth::user()->unreadNotifications;
    @endphp

    <div class="top-navbar d-flex justify-content-between align-items-center px-4 shadow-sm position-relative">
        <span class="fw-bold">Welcome, {{ Auth::user()->name }}</span>

        <div class="notification-wrapper position-relative">
            <i class="bi bi-bell-fill fs-5 cursor-pointer" id="notificationToggle"></i>

            <!-- Notification Dropdown -->
            <div class="notification-dropdown shadow" id="notificationDropdown">
                <p class="mb-1 fw-bold">Notifications</p>
                <hr class="mt-0 mb-2">

                @forelse ($notifications as $note)
                    <div class="notification-item">
                        📢 {{ $note->data['message'] }} <a href="{{ route('markasred', $note->id) }}"><i class="fa-solid fa-x text-danger"></i></a>
                    </div>
                @empty
                    <div class="notification-item">No new notifications.</div>
                @endforelse
            </div>
        </div>
    </div>


    <!-- Main Content -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>  
    <div class="main-content">
        {{$slot}}
    </div>
    
    @livewireScripts

    <script type="text/javascript">
        // Function to initialize DOM elements
        function initializeDOM() {
            console.log('DOM initialized');
            // Handle notification toggle
            const bellIcon = document.getElementById('notificationToggle');
            const dropdown = document.getElementById('notificationDropdown');

            console.log('Bell icon:', bellIcon);
            console.log('Dropdown:', dropdown);
            if (bellIcon && dropdown) {
                console.log('Elements not found!');
                // Remove existing listeners to avoid duplicates
                bellIcon.replaceWith(bellIcon.cloneNode(true));
                const newBellIcon = document.getElementById('notificationToggle');
                newBellIcon.addEventListener('click', function () {
                    setTimeout(() => {
                        dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
                    }, 100);
                });
 
                // Close dropdown when clicking outside
                document.addEventListener('click', function (event) {
                    console.log('Clicked outside:', event.target);
                    if (!newBellIcon.contains(event.target) && !dropdown.contains(event.target)) {
                        dropdown.style.display = 'none';
                    }
                });
            }
        }
 
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', initializeDOM);
 
        // Re-initialize on Livewire navigation
        document.addEventListener('livewire:navigated', function() {
            setTimeout(initializeDOM, 100);
        });
 
        // Also handle Livewire content updates
        document.addEventListener('livewire:load', initializeDOM);
    </script>
</body>
</html>