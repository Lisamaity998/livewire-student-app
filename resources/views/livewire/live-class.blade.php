<div>
    <div class="content-header">
        <h1 class="content-title">Ongoing Classes</h1>
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
            <p>No ongoing classes found for your course's.</p>
        @endif
    </div>

    <!-- Modal -->
    <div wire:ignore.self class="modal fade" id="classModal" tabindex="-1" aria-labelledby="classModalLabel" aria-hidden="true">
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
    document.addEventListener('livewire:init', () => {
        Livewire.on('openClassModal', ({ classId }) => {
            // Just dispatch the setSelectedClass event
            Livewire.dispatch('setSelectedClass', { classId });
            
            // Show modal after a brief delay
            setTimeout(() => {
                const modal = new bootstrap.Modal(document.getElementById('classModal'));
                modal.show();
            }, 100);
        });
    });

    window.addEventListener('closeClassModal', () => {
        var modal = bootstrap.Modal.getInstance(document.getElementById('classModal'));
        modal.hide();
    });
</script>
