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
    @session('warning')
        <div class="alert alert-warning" role="alert">
            {{ $value }}
        </div>
    @endsession

    <div class="content-header">
        <h1 class="content-title">Upcoming Classes</h1>
        <input type="text" id="searchClass" class="form-control" style="margin-bottom:30px; width:15rem" placeholder="Search class hare" wire:model.live.debounce.400ms="search">
    </div>

    <div class="student-table">
        @if(count($upcomingClasses) > 0)
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Class Name</th>
                        <th>Teacher</th>
                        <th>Date</th>
                        <th class="w-25">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($upcomingClasses as $class)
                        <tr>
                            <td>{{ $class->class_name }}</td>
                            <td>{{ $class->teacher->name ?? 'N/A' }}</td>
                            <td>{{ \Carbon\Carbon::parse($class->start_date)->format('d M Y') }}</td>
                            <td>
                                <button class="btn btn-primary" wire:click="$dispatch('openClassModal', { classId: {{ $class->id }} })">View Details</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>No upcoming classes found for your course's.</p>
        @endif
    </div>

    <!-- Bootstrap Modal -->
    <div wire:ignore.self class="modal fade" id="upcomingClassModal" tabindex="-1" aria-labelledby="classModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Class Details</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
                <p><strong>Class Name:</strong> <span>{{ $modalData['className'] }}</span></p>
                <p><strong>Topic:</strong> <span>{{ $modalData['topic'] }}</span></p>
                <p><strong>Teacher:</strong> <span>{{ $modalData['teacher'] }}</span></p>
                <p><strong>Date:</strong> <span>{{ $modalData['date'] }}</span></p>
                <p><strong>Time:</strong> <span>{{ $modalData['time'] }}</span></p>
                <p><strong style="margin-right:15px">Are Going To Attend: </strong>
                    <span style="display: inline-flex; gap: 25px;" class="ml-3">
                    <a style="cursor: pointer;" wire:click='attendClass'><i class="fa-solid fa-check text-success"></i></a>
                    <a style="cursor: pointer;" wire:click='missClass'><i class="fa-solid fa-x text-danger"></i></a>
                    </span>
                </p>              
            </div>
        </div>
      </div>
    </div>
</div>
<script>
    upcomingClassModal = new bootstrap.Modal(document.getElementById('upcomingClassModal'));
    document.addEventListener('livewire:navigated', () => {
        Livewire.on('openClassModal', ({ classId }) => {
            // Just dispatch the setSelectedClass event
            Livewire.dispatch('setSelectedClass', { classId });
            
            // Show modal after a brief delay
            setTimeout(() => {
                upcomingClassModal.show();
            }, 150);
        });
        window.addEventListener('closeClassModal', () => {
            upcomingClassModal.hide();
        });
    });

</script>