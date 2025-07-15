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
            <form wire:submit.prevent="addQuestion">
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
                <div class="form-group mb-3">
                    <label for="questionName">Question</label>
                    <input type="text" class="form-control" placeholder="Please enter your question" id="questionName" wire:model.blur="question_name">
                    @error('question_name') 
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>
                <div class="form-group mb-3">
                    <label for="answer1">Option A</label>
                    <input type="text" class="form-control" placeholder="Please enter option A" id="answer1" wire:model.blur="answer1">
                    @error('answer1') 
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>
                <div class="form-group mb-3">
                    <label for="answer2">Option B</label>       
                    <input type="text" class="form-control" placeholder="Please enter option B" id="answer2" wire:model.blur="answer2">
                    @error('answer2') 
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>
                <div class="form-group mb-3">
                    <label for="answer3">Option C</label>
                    <input type="text" class="form-control" placeholder="Please enter option C" id="answer3" wire:model.blur="answer3">
                    @error('answer3') 
                        <p class="text-danger">{{ $message }}</p>       
                    @enderror       
                </div>
                <div class="form-group mb-3">
                    <label for="answer4">Option D</label>
                    <input type="text" class="form-control" placeholder="Please enter option D" id="answer4" wire:model.blur="answer4">
                    @error('answer4') 
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>
                <div class="form-group mb-3">
                    <label for="correctAnswer">Correct Answer</label>
                    <input type="text" class="form-control" placeholder="Please enter the correct answer" id="correctAnswer" wire:model.blur="correct_answer">
                    @error('correct_answer') 
                        <p class="text-danger">{{ $message }}</p>
                    @enderror
                </div>
                <div class="d-flex justify-content-center align-items-center">
                    <button type="submit" class="btn btn-success mt-3">Save Question</button>
                </div>
            </form>
        </div>
    </div>
</div>
