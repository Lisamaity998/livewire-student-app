<div>
    <div class="content-header">
        <h1 class="content-title">Upcoming Classes</h1>
    </div>

    <div class="student-table">
        @if(count($upcomingClasses) > 0)
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Class Name</th>
                        <th>Teacher</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($upcomingClasses as $class)
                        <tr 
                            wire:click="$dispatch('openClassModal', {
                                classId: {{ $class->id }},
                                className: '{{ $class->class_name }}',
                                topic: '{{ $class->course->name ?? 'N/A' }}',
                                teacher: '{{ $class->teacher->name ?? 'N/A' }}',
                                date: '{{ \Carbon\Carbon::parse($class->start_date)->format('d M Y') }}',
                                time: '{{ \Carbon\Carbon::parse($class->class_time)->format('H:i') }}'
                            })" 
                            style="cursor:pointer;"
                        >
                            <td>{{ $class->class_name }}</td>
                            <td>{{ $class->teacher->name ?? 'N/A' }}</td>
                            <td>{{ \Carbon\Carbon::parse($class->start_date)->format('d M Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>No upcoming classes found for your course's.</p>
        @endif
    </div>

    <!-- Bootstrap Modal -->
    <div wire:ignore.self class="modal fade" id="classModal" tabindex="-1" aria-labelledby="classModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Class Details</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p><strong>Class Name:</strong> <span id="modal-class-name"></span></p>
            <p><strong>Topic:</strong> <span id="modal-topic"></span></p>
            <p><strong>Teacher:</strong> <span id="modal-teacher"></span></p>
            <p><strong>Date:</strong> <span id="modal-date"></span></p>
            <p><strong>Time:</strong> <span id="modal-time"></span></p>
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
    Livewire.on('openClassModal', ({ classId, className, topic, teacher, date, time }) => {
        Livewire.dispatch('setSelectedClass', { classId });
        
        document.getElementById('modal-class-name').textContent = className;
        document.getElementById('modal-topic').textContent = topic;
        document.getElementById('modal-teacher').textContent = teacher;
        document.getElementById('modal-date').textContent = date;
        document.getElementById('modal-time').textContent = time;

        const modal = new bootstrap.Modal(document.getElementById('classModal'));
        modal.show();
    });
</script>