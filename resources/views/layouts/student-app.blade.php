<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/adminDashboard.css') }}">
    @livewireStyles
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            STUDENT PANEL
        </div>
        <ul class="sidebar-nav">
            <li><a href="{{ route('student.dashboard') }}" class="{{ request()->routeIs('student.dashboard') ? 'active' : '' }}" wire:navigate>Dashboard</a></li>
            <li><a href="{{ route('live.class') }}" class="{{ request()->routeIs('live.class') ? 'active' : '' }}" wire:navigate>Ongoing Classes</a></li>
            <li><a href="{{ route('upcoming.class') }}" class="{{ request()->routeIs('upcoming.class') ? 'active' : '' }}" wire:navigate>Upcoming Classes</a></li>
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

    <!-- Main Content -->
    <div class="main-content">
        {{$slot}}
    </div>
    
    @livewireScripts
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">    
</body>
</html>