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
            <li><a href="{{ route('students.test.results') }}" class="{{ request()->routeIs('students.test.results') ? 'active' : '' }}" wire:navigate>Students Test Results</a></li>
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
    <script>
        // Helper to forcefully clean up Bootstrap modal artifacts
        function removeModalArtifacts() {
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            document.body.classList.remove('modal-open');
            document.body.style = ''; // Reset any overflow hidden or padding
        }

        // Class Modal
        window.addEventListener('openAddClassModal', () => {
            try {
                var modal = new bootstrap.Modal(document.getElementById('addClassModal'));
                modal.show();
            } catch (error) {
                console.error('Error opening add modal:', error);
            }
        });

        window.addEventListener('closeAddClassModal', () => {
            try {
                var modal1 = bootstrap.Modal.getInstance(document.getElementById('addClassModal'));
                if (modal1) {
                    modal1.hide();
                }
                // clean up the backdrop
                setTimeout(removeModalArtifacts, 300);
            } catch (error) {
                console.error('Error closing add modal:', error);
            }
        });

        // Add Question Modal Events
        window.addEventListener('openAddQuestionModal', () => {
            try {
                var modal2 = new bootstrap.Modal(document.getElementById('addQuestionModal'));
                modal2.show();
            } catch (error) {
                console.error('Error opening add modal:', error);
            }
        });

        window.addEventListener('closeAddQuestionModal', () => {
            try {
                var modal3 = bootstrap.Modal.getInstance(document.getElementById('addQuestionModal'));
                if (modal3) {
                    modal3.hide();
                }
                // clean up the backdrop
                setTimeout(removeModalArtifacts, 300);
            } catch (error) {
                console.error('Error closing add modal:', error);
            }
        });

        // Update Question Modal Events
        window.addEventListener('openUpdateQuestionModal', () => {
            try {
                var modal4 = new bootstrap.Modal(document.getElementById('updateQuestionModal'));
                modal4.show();
            } catch (error) {
                console.error('Error opening update modal:', error);
            }
        });

        window.addEventListener('closeUpdateQuestionModal', () => {
            try {
                var modal5 = bootstrap.Modal.getInstance(document.getElementById('updateQuestionModal'));
                if (modal5) {
                    modal5.hide();
                }
                // clean up the backdrop
                setTimeout(removeModalArtifacts, 300);
            } catch (error) {
                console.error('Error closing update modal:', error);
            }
        });

        // On component switch (Livewire navigate), always clean up lingering backdrops
        document.addEventListener('livewire:navigated', () => {
            removeModalArtifacts();
        });
    </script>
</body>
</html>