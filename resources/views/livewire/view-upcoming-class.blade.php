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
        <input type="text" id="searchClass" class="form-control" style="margin-bottom:30px; width:15rem" placeholder="Search class hare" wire:model.live.debounce.400ms="search">
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
                        $classDate = \Carbon\Carbon::parse($class->start_date)->startOfDay();
                        $today = now()->startOfDay();
                        $isPast = $today->lessThanOrEqualTo($classDate) ? false : true;
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
                            <div class="d-flex align-items-center gap-2">
                                <a href="#" class="text-danger me-2" style="cursor: pointer" wire:click.prevent="deleteClass({{ $class->id }})">
                                    <i class="fa-solid fa-trash"></i>
                                </a>

                                @php
                                    $classDate = \Carbon\Carbon::parse($class->start_date)->startOfDay();
                                    $today = now()->startOfDay();
                                @endphp

                                @if ($today->lessThanOrEqualTo($classDate))
                                    <a href="#" class="text-success me-2" style="cursor: pointer" wire:click.prevent="editClass({{ $class->id }})">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endif
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
    <!-- add class Modal -->
    <div wire:ignore.self class="modal fade" id="addClassModal" tabindex="-1" aria-labelledby="addClassModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content p-3">
                <div class="modal-header">
                    <h5 class="modal-title" id="addClassModalLabel">Add New Class</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <livewire:add-new-class />
                </div>
            </div>
        </div>
    </div>

    {{-- edit class Modal --}}
    <div wire:ignore.self class="modal fade" id="editClassModal" tabindex="-1" aria-labelledby="editClassModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content p-3">
                <div class="modal-header">
                    <h5 class="modal-title" id="editClassModalLabel">Edit Class</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div>
                        <form wire:submit.prevent="updateClass">
                            <div class="form-group mb-3">
                                <label for="className">Class Name</label>
                                <input type="text" class="form-control" id="className" wire:model="class_name" disabled>
                            </div>

                            <div class="form-group mb-3">
                                <label for="courseSelect">Course</label>
                                <select id="courseSelect" class="form-select" wire:model="course_id" disabled>
                                    <option value="">-- Choose a course --</option>
                                    @forelse($courses as $course)
                                        <option value="{{ $course->id }}">{{ $course->name }}</option>
                                    @empty
                                        <option disabled>No courses available</option>
                                    @endforelse
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="ChooseDate">Choose Date</label>
                                <input type="date" id="ChooseDate" wire:model.lazy="selected_date" min="{{ now()->toDateString() }}" class="form-control" {{ $isEditable ? '' : 'disabled' }}>
                                @error('selected_date') 
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="classTime">Choose Time</label>
                                <input type="time" id="classTime" wire:model.lazy="class_time" class="form-control" {{ $isEditable ? '' : 'disabled' }}>
                                @error('class_time') 
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>

                            @if(!empty($teachers))
                                <div class="form-group mb-3">
                                    <label for="teacherSelect">Select a Teacher</label>
                                    <select id="teacherSelect" class="form-select teacherSelect" wire:model="teacher_id" {{ $isEditable ? '' : 'disabled' }}>
                                        <option value="">-- Choose a teacher --</option>
                                        @forelse($teachers as $teacher)
                                            <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                        @empty
                                            <option disabled>No teachers available</option>
                                        @endforelse
                                    </select>                  
                                    @error('teacher_id') 
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                            @else
                                <div class="text-muted fst-italic">Please select a course to see skilled teachers for that course.</div>
                            @endif

                            {{-- Optional File Uploads --}}
                            @php
                                $classStart = \Carbon\Carbon::parse($selected_date . ' ' . $class_time);
                                $uploadWindowStart = $classStart->copy()->subMinutes(15);
                                $uploadWindowEnd = $classStart->copy()->endOfDay();
                            @endphp

                            @if(now()->between($uploadWindowStart, $uploadWindowEnd))
                                {{-- Video Upload --}}
                                <div class="form-group mb-3">
                                    <label for="videoUpload">Upload Video (Optional)</label>
                                    <input type="file" id="videoUpload" class="form-control" wire:model="video" accept="video/*">
                                    @error('video') 
                                        <p class="text-danger">{{ $message }}</p> 
                                    @enderror
                                </div>

                                {{-- Notes Upload --}}
                                <div class="form-group mb-3">
                                    <label for="notesUpload">Upload Notes (Optional)</label>
                                    <input type="file" id="notesUpload" class="form-control" wire:model="notes" accept=".pdf">
                                    @error('notes') 
                                        <p class="text-danger">{{ $message }}</p> 
                                    @enderror
                                </div>

                                {{-- YouTube URL --}}
                                <div class="form-group mb-3">
                                    <label for="youtubeUrl">YouTube Video URL (Optional):</label>
                                    <input type="text" id="youtubeUrl" wire:model.lazy="youtubeUrl" class="form-control" placeholder="Enter YouTube video URL">
                                    @error('youtubeUrl') 
                                        <p class="text-danger">{{ $message }}</p> 
                                    @enderror
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    Material uploads are only allowed from {{ $uploadWindowStart->format('d M Y h:i A') }} until end of the same day.
                                </div>
                            @endif

                            <div class="d-flex justify-content-center align-items-center">
                                <button type="submit" class="btn btn-primary mt-3">Update Class</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Bootstrap Modal Listener Script -->
<script>
    // ===== Add Class Modal Events =====
    addClassModal = new bootstrap.Modal(document.getElementById('addClassModal'));
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

    // ===== Edit Class Modal Events =====
    $(document).ready(function(){
        editClassModal = new bootstrap.Modal(document.getElementById('editClassModal'));
        window.addEventListener('openEditClassModal', (event) => {
            try {
                const teacherId = event.detail.teacherId || null;
                setTimeout(() => {
                    const select = document.getElementById('teacherSelect');
                    if (select) {
                        select.value = teacherId;
                    } else {
                        console.warn("Dropdown not yet available");
                    }
                }, 100); // slight delay to allow DOM update
                editClassModal.show();
            } catch (error) {
                console.error('Error opening edit class modal:', error);
            }
        }); 
        window.addEventListener('closeEditClassModal', () => {
            try {
                editClassModal.hide();
            } catch (error) {
                console.error('Error closing edit class modal:', error);
            }
        });
    });
</script>