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
        <h1 class="content-title">Mock Test</h1>
    </div>

    <div class="student-table">
        {{-- Subject Selection --}}
        @unless($testStarted)
            <div class="p-4">
                <h5 class="mb-3">Please select a subject to give test</h5>

                <div class="mb-3">
                    <label class="form-label">Select Subject</label>
                    <select wire:model.lazy="selectedSubjectId" class="form-select">
                        <option value="">-- Select Subject --</option>
                        @foreach ($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>

                @if ($selectedSubjectId)
                    <button wire:click="startTest" class="btn btn-primary mt-2">
                        Start Test
                    </button>
                @endif
            </div>
        @endunless

        {{-- Test UI --}}
        @if($testStarted && !$testSubmitted)
            <div class="mt-4">
                <h5>Question {{ $currentIndex + 1 }} of {{ count($questions) }}</h5>
                <p class="fw-bold">{{ $questions[$currentIndex]->question_name }}</p>

                @foreach (['answer1', 'answer2', 'answer3', 'answer4'] as $option)
                    <div class="form-check">
                        <input class="form-check-input" type="radio"
                            wire:model="answers.{{ $currentIndex }}"
                            value="{{ $questions[$currentIndex]->$option }}"
                            id="{{ $option }}">
                        <label class="form-check-label" for="{{ $option }}">
                            {{ $questions[$currentIndex]->$option }}
                        </label>
                    </div>
                @endforeach

                <div class="mt-3 d-flex justify-content-between">
                    <button wire:click="previous" class="btn btn-secondary"
                        @disabled($currentIndex == 0)>
                        Previous
                    </button>

                    @if($currentIndex < count($questions) - 1)
                        <button wire:click="next" class="btn btn-primary">Next</button>
                    @else
                        <button wire:click="submit" class="btn btn-success">Submit</button>
                    @endif
                </div>
            </div>
        @endif

        {{-- Result UI --}}
        @if($testSubmitted)
            <div class="mt-4">
                <h4 class="text-success">Test Completed!</h4>
                @php
                    $percentage = round(($score / count($questions)) * 100, 2);
                @endphp
                <h5 class="fw-bold">Your Score: {{ $percentage }}%</h5>

                <table class="table mt-4">
                    <thead>
                        <tr>
                            <th>Q#</th>
                            <th>Question</th>
                            <th>Your Answer</th>
                            <th>Correct Answer</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($questions as $index => $question)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $question->question_name }}</td>
                                <td>{{ $answers[$index] }}</td>
                                <td>{{ $question->correct_answer }}</td>
                                <td>
                                    @if (($answers[$index]) == $question->correct_answer)
                                        ✅ Correct
                                    @else
                                        ❌ Wrong
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
