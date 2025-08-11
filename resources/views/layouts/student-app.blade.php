<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- <meta name="csrf-token" content="{{ csrf_token() }}"> --}}
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

    <div class="top-navbar d-flex justify-content-between align-items-center px-4 shadow-sm position-relative">
        <span class="fw-bold">Welcome, {{ Auth::guard('student')->user()->name }}</span>

        <div class="notification-wrapper position-relative">
            <i class="bi bi-bell-fill fs-5 text-dark" id="notificationToggle" style="cursor: pointer;"></i>
            <span id="notificationDot" 
                class="position-absolute bg-danger border border-light rounded-circle d-none"
                style="width:10px; height:10px; top: -2px; right: -2px;">
            </span>

            <!-- Notification Dropdown -->
            <div class="notification-dropdown shadow-sm" id="notificationDropdown">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <p class="mb-0 fw-bold text-primary">Notifications</p>
                    <small id="clearAllNotifications" class="text-muted " style="font-size: 0.8rem; cursor: pointer;">Clear All</small>
                </div>
                <hr class="mt-0 mb-2">

                <div id="notificationList">
                    <div class="notification-item align-item-center justify-content-center text-muted py-1" id="noNotifications">No new notifications.</div>
                </div>
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

    <script>
        const userId = {{ auth()->guard('student')->user()->id }};
    </script>

    <script>
        {!! Vite::content('resources/js/app.js') !!}
    </script>

    <script type="text/javascript">
        const icons = {
            reminder: { icon: "bi-megaphone-fill", color: "text-primary" },
            upload: { icon: "bi-file-earmark-arrow-up-fill", color: "text-success" },
            schedule: { icon: "bi-calendar-event-fill", color: "text-warning" },
            mockTest: { icon: "bi-clipboard-check-fill", color: "text-info" },
            classCancelled: { icon: "bi-calendar-x-fill", color: "text-danger" }
        };

        const notificationRoutes = {
            upload: "{{ route('upcoming.class') }}",
            schedule: "{{ route('upcoming.class') }}",
            reminder: "{{ route('live.class') }}",
            mockTest: "{{ route('mock.test') }}"
        };

        Echo.private(`user.${userId}`)
            .listen('.new-class-created', (e) => {
                showNotification(e.message.type, e.message.title, e.message.description)
            })
            .listen('.class-material-uploaded', (e) => {
                showNotification(e.message.type, e.message.title, e.message.description)
            })
            .listen('.class-reminder', (e) => {
                showNotification(e.message.type, e.message.title, e.message.description)
            })
            .listen('.mock-test-notification', (e) => {
                showNotification(e.message.type, e.message.title, e.message.description)
            })
            .listen('.class-deleted-notification', (e) => {
                showNotification(e.message.type, e.message.title, e.message.description)
            });

        function showNotification(type, title, description) {
            const container = document.getElementById('notificationList');
            const emptyMsg = document.getElementById('noNotifications');

            // Remove "No new notifications" if it's still there
            if (emptyMsg) emptyMsg.remove();

            const { icon, color } = icons[type] || { icon: "bi-info-circle-fill", color: "text-secondary" };

            let linkHTML = "";
            // Add link only if type is in notificationRoutes
            if (notificationRoutes[type]) {
                linkHTML = `<a href="${notificationRoutes[type]}" class="ms-1 text-primary fw-bold" wire:navigate style="text-decoration:none;">Click here</a>`;
            }

            const notificationHTML = `
                <div class="notification-item">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-start">
                            <i class="bi ${icon} ${color} me-3 fs-5"></i>
                            <div>
                                <div class="fw-semibold">${title}</div>
                                <div class="small text-muted">${description} ${linkHTML}</div>
                            </div>
                        </div>
                        <i class="fa-solid fa-xmark close-notification"></i>
                    </div>
                </div>
            `;

            container.insertAdjacentHTML('afterbegin', notificationHTML);
            updateNotificationDot();
        }

        document.addEventListener('click', function (event) {
            if (event.target.classList.contains('close-notification')) {
                const notificationItem = event.target.closest('.notification-item');
                if (notificationItem) {
                    notificationItem.remove();
                }

                const container = document.getElementById('notificationList');
                if (container.children.length === 0) {
                    container.innerHTML = `<div class="notification-item align-item-center justify-content-center text-muted py-1" id="noNotifications">No new notifications.</div>`;
                }
            }
            updateNotificationDot();
        });

        document.getElementById('clearAllNotifications').addEventListener('click', function () {
            const container = document.getElementById('notificationList');
            container.innerHTML = `<div class="notification-item align-item-center justify-content-center text-muted py-1" id="noNotifications">No new notifications.</div>`;
        });

        function updateNotificationDot() {
            const dot = document.getElementById('notificationDot');
            const noNotifications = document.getElementById('noNotifications');

            if (!noNotifications) {
                dot.classList.remove('d-none'); 
            } else {
                dot.classList.add('d-none'); 
            }
        }
    </script>
</body>
</html>