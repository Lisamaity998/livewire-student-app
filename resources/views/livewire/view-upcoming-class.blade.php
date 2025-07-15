<div>
    <div class="content-header">
        <h1 class="content-title">Class List</h1>
        <!-- Parent Blade View -->
        <div>
            <button wire:click="$dispatch('openAddClassModal')" class="btn btn-success">Add New Class</button>

            <!-- Modal -->
            <div class="modal fade" id="addClassModal" tabindex="-1" aria-labelledby="addClassModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content p-3">
                        <div class="modal-header">
                            <h5 class="modal-title">Add New Class</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <livewire:add-new-class />
                        </div>
                    </div>
                </div>
            </div>
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
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">No Data Found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Bootstrap Modal Listener Script -->
<script>
    Livewire.on('openAddClassModal', () => {
        var modal = new bootstrap.Modal(document.getElementById('addClassModal'));
        modal.show();
    });
    Livewire.on('closeAddClassModal', () => {
        var modal = bootstrap.Modal.getInstance(document.getElementById('addClassModal'));
        modal.hide();
    });
</script>
