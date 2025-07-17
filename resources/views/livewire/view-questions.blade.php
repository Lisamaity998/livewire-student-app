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
        <h1 class="content-title">Questions List</h1>
        <!-- Parent Blade View -->
        <div>
            <button wire:click="$dispatch('openAddQuestionModal')" class="btn btn-success">Add New Question</button>

            <!-- Modal for insert Question -->
            <div wire:ignore class="modal fade" id="addQuestionModal" tabindex="-1" aria-labelledby="addQuestionModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content p-3">
                        <div class="modal-header">
                            <h5 class="modal-title">Add New Question</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <livewire:question-entry />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for update Question -->
    <div wire:ignore.self class="modal fade" id="updateQuestionModal" tabindex="-1" aria-labelledby="updateQuestionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content p-3">
                <div class="modal-header">
                    <h5 class="modal-title">Update Question</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="student-table">
                        <div>
                            <form wire:submit.prevent="updateQuestion">
                                <div class="form-group mb-3">
                                    <label for="courseSelect">Course</label>
                                    <select id="courseSelect" class="form-select" wire:model="course_id">
                                        <option value="">-- Choose a course --</option>
                                        @forelse($courses as $course)
                                            <option value="{{ $course->id }}" {{ $course->id == $course_id ? 'selected' : '' }}>{{ $course->name }}</option>
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
                                    <input type="text" class="form-control" placeholder="Please enter your question" id="questionName" wire:model="question_name">
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
            </div>
        </div>
    </div>

    <div class="student-table">
        <table class="table table-bordered">
            <thead>
              <tr>
                <th>#</th>
                <th>Topic</th>
                <th>Question</th>
                <th>Option A</th>
                <th>Option B</th>
                <th>Option C</th>
                <th>Option D</th>
                <th>Correct Answer</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
                @forelse ($questions as $index => $question)
                    <tr>
                        <th scope="row">{{ $index + 1 }}</th>
                        <td>{{ $question->course->name ?? 'N/A' }}</td>
                        <td>{{ $question->question_name }}</td>
                        <td>{{ $question->answer1 }}</td>
                        <td>{{ $question->answer2 }}</td>
                        <td>{{ $question->answer3 }}</td>
                        <td>{{ $question->answer4 }}</td>
                        <td>{{ $question->correct_answer }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <a href="#" class="text-success me-2" style="cursor: pointer" wire:click.prevent="editQuestion({{ $question->id }})">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="#" class="text-danger me-2" style="cursor: pointer" wire:click.prevent="deleteQuestion({{ $question->id }})">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center">No Data Found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{-- {{dd($this->course_id, $this->question_name, $this->answer1, $this->answer2, $this->answer3, $this->answer4, $this->correct_answer);}} --}}
</div>

<!-- Bootstrap Modal Listener Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add Question Modal Events
        window.addEventListener('openAddQuestionModal', () => {
            try {
                const modal = new bootstrap.Modal(document.getElementById('addQuestionModal'));
                modal.show();
            } catch (error) {
                console.error('Error opening add modal:', error);
            }
        });

        window.addEventListener('closeAddQuestionModal', () => {
            try {
                const modal = bootstrap.Modal.getInstance(document.getElementById('addQuestionModal'));
                if (modal) {
                    modal.hide();
                }
            } catch (error) {
                console.error('Error closing add modal:', error);
            }
        });

        // Update Question Modal Events
        window.addEventListener('openUpdateQuestionModal', () => {
            try {
                console.log('Update Modal Event Fired');
                const modal = new bootstrap.Modal(document.getElementById('updateQuestionModal'));
                modal.show();
            } catch (error) {
                console.error('Error opening update modal:', error);
            }
        });
        
        window.addEventListener('closeUpdateQuestionModal', () => {
            try {
                const modal = bootstrap.Modal.getInstance(document.getElementById('updateQuestionModal'));
                if (modal) {
                    modal.hide();
                }
            } catch (error) {
                console.error('Error closing update modal:', error);
            }
        });

        // Handle Livewire events
        // window.addEventListener('resetAddForm', () => {
        //     window.Livewire.dispatch('resetAddForm');
        // });

        // window.addEventListener('resetUpdateForm', () => {
        //     window.Livewire.dispatch('resetUpdateForm');
        // });
    });
</script>
