<div>
    @session('success')
    <div class="alert alert-success" role="alert">
        {{ $value }}
    </div>
    @endsession
    @session('error')
    <div class="alert alert-danger" role="alert">
        {{ $value }}
    </div>
    @endsession
    <div class="content-header">
        <h1 class="content-title">Class List</h1>
        <!-- Parent Blade View -->
        <div>
            <button wire:click="$dispatch('openAddClassModal')" class="btn btn-success">Add New Class</button>
        </div>
    </div>
    
    <div class="student-table">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Class Name</th>
                    <th>Topic</th>
                    <th>Teacher</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Interested Student</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($classes as $index => $class)
                    @php
                        $classDateTime = \Carbon\Carbon::parse($class->start_date . ' ' . $class->class_time);
                        $isPast = $classDateTime->isPast();
                    @endphp
                    <tr class="{{ $isPast ? 'table-danger' : '' }}">
                        <th scope="row">{{ $index + 1 }}</th>
                        <td>{{ $class->class_name }}</td>
                        <td>{{ $class->course->name ?? 'N/A' }}</td>
                        <td>{{ $class->teacher->name ?? 'N/A' }}</td>
                        <td>{{ \Carbon\Carbon::parse($class->start_date)->format('d M Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($class->class_time)->format('h:i A') }}</td>
                        <td>{{ $class->interestedStudents->count() }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <a href="#" class="text-danger me-2" style="cursor: pointer" wire:click.prevent="deleteClass({{ $class->id }})">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">No Data Found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <!-- Modal -->
    <div wire:ignore.self class="modal fade" id="addClassModal" tabindex="-1" aria-labelledby="addClassModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content p-3">
                <div class="modal-header">
                    <h5 class="modal-title" id="addClassModalLabel">Add New Class</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" wire:click="$dispatch('closeAddClassModal')"></button>
                </div>
                <div class="modal-body">
                    <livewire:add-new-class />
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Bootstrap Modal Listener Script -->
<script>
    if (typeof addClassModal === 'undefined') {
        var addClassModal = new bootstrap.Modal(document.getElementById('addClassModal'));
    }
    // ===== Add Class Modal Events =====
    window.addEventListener('openAddClassModal', () => {
        try {
            addClassModal.show();
        } catch (error) {
            console.error('Error opening add class modal:', error);
        }
    });

    window.addEventListener('closeAddClassModal', () => {
        try {
            addClassModal.hide();
        } catch (error) {
            console.error('Error closing add class modal:', error);
        }
    });
</script>