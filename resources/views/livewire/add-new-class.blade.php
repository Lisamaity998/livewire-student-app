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
    <div class="student-table">
        <div>
            <form wire:submit.prevent="addClass">
                <div class="form-group mb-3">
                    <label for="className">Class Name</label>
                    <input type="text" class="form-control" placeholder="Please enter your class name" id="className" wire:model.blur="class_name">
                    @error('class_name') 
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>
                <div class="form-group mb-3">
                    <label for="Choose Date">Choose Date</label>
                    <input type="date" id="ChooseDate" wire:model.lazy="selected_date" min="{{ now()->toDateString() }}" class="form-control">
                    @error('selected_date') 
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>
                <div class="form-group mb-3">
                    <label for="classTime">Choose Time</label>
                    <input type="time" id="classTime" wire:model.lazy="class_time" class="form-control">
                    @error('class_time') 
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>                
                <div class="form-group mb-3">
                    <label for="courseSelect">Course</label>
                    <select id="courseSelect" class="form-select" wire:model.lazy="course_id">
                        <option value="">-- Choose a course --</option>
                        @forelse($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->name }}</option>
                        @empty
                            <option disabled>No courses available.</option>
                        @endforelse
                    </select>
                    @error('course_id') 
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>
                @if(!empty($teachers))
                    <div class="form-group mb-3">
                        <label for="teacherSelect">Select a Teacher</label>
                        <select id="teacherSelect" class="form-select" wire:model.blur="teacher_id">
                            <option value="">-- Choose a teacher --</option>
                            @forelse($teachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                            @empty
                                <option disabled>No courses available.</option>
                            @endforelse
                        </select>                   
                        @error('teacher_id') 
                            <p class="text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                @else
                    <div class="text-muted fst-italic">Please select a course to see skilled teachers for that course.</div>
                @endif

                <div class="d-flex justify-content-center align-items-center">
                    <button type="submit" class="btn btn-success mt-3">Add Class</button>
                </div>
            </form>
        </div>
    </div>
</div>
