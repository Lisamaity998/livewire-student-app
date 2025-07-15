<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/adminDashboard.css') }}">
    @livewireStyles
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            ADMIN PANEL
        </div>
        <ul class="sidebar-nav">
            <li><a href="{{ route('approval') }}" class="{{ request()->routeIs('approval') ? 'active' : '' }}" wire:navigate>Approval</a></li>
            <li><a href="{{ route('student.list') }}" class="{{ request()->routeIs('student.list') ? 'active' : '' }}" wire:navigate>Student List</a></li>
            <li><a href="{{ route('view.upcoming.class') }}" class="{{ request()->routeIs('view.upcoming.class') ? 'active' : '' }}" wire:navigate>Class</a></li>
            <li><a href="{{ route('view.question') }}" class="{{ request()->routeIs('view.question') ? 'active' : '' }}" wire:navigate>Question</a></li>
        </ul>
        <div class="sidebar-footer">
            <form>
                <button type="submit" class="logout-btn">
                    <i data-lucide="log-out"></i>
                    <a href="{{ route('admin.logout') }}" class="logout-text" style="all:unset">Logout</a>
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
</body>
</html>