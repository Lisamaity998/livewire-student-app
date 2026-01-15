<div>
    @session('success')
        <div class="alert alert-success">{{ $value }}</div>
    @endsession
    @session('error')
        <div class="alert alert-danger">{{ $value }}</div>
    @endsession

    <div class="content-header mb-4">
        <h1 class="content-title">Mock Test</h1>
    </div>

    <div class="student-table">

        {{-- Subject Selection --}}
        @if (!$testStarted && !$testSubmitted)
            <div class="p-4 border rounded">
                <h5 class="mb-3">Please select a subject to begin the test</h5>

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
                    @if ($hasQuestions)
                        <button wire:click="startTest" class="btn btn-primary mt-2">
                            Start Test
                        </button>
                    @else
                        <div class="alert alert-warning mt-2">
                            Test is not available for this subject. Please select another.
                        </div>
                    @endif
                @endif
            </div>
        @endif

        {{-- Test UI --}}
        @if ($testStarted && !$testSubmitted)
            <div>
                <div class="alert alert-info text-center">
                    Time Remaining: <strong id="timer-display">{{ gmdate('i:s', $remainingTime) }}</strong>
                </div>

                <div class="mt-4">
                    <h5>Question {{ $currentIndex + 1 }} of {{ count($questions) }}</h5>
                    <p class="fw-bold">{{ $questions[$currentIndex]->question_name }}</p>

                    @foreach (['answer1', 'answer2', 'answer3', 'answer4'] as $option)
                        <div class="form-check">
                            <input class="form-check-input"
                                type="radio"
                                wire:model.live="answers.{{ $currentIndex }}"
                                value="{{ $questions[$currentIndex]->$option }}"
                                id="question{{ $currentIndex }}_{{ $option }}">
                            <label class="form-check-label" for="question{{ $currentIndex }}_{{ $option }}">
                                {{ $questions[$currentIndex]->$option }}
                            </label>
                        </div>
                    @endforeach

                    <div class="mt-3 d-flex justify-content-between">
                        @if ($currentIndex > 0)
                            <button class="btn btn-secondary" wire:click="previous">Previous</button>
                        @else
                            <span></span>
                        @endif

                        @if ($currentIndex < count($questions) - 1)
                            <button wire:click="next" class="btn btn-primary">Next</button>
                        @else
                            <button wire:click="submit" class="btn btn-success">Submit</button>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- Result UI --}}
        @if ($testSubmitted)
            <div class="mt-4">
                <h4 class="text-success">Test Completed!</h4>
                @php
                    $percentage = round(($score / count($questions)) * 100, 2);
                @endphp
                <h5 class="fw-bold">Your Score: {{ $percentage }}%</h5>

                <table class="table table-bordered mt-4">
                    <thead>
                        <tr>
                            <th>#</th>
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
                                <td>{{ $answers[$index] ?? '-' }}</td>
                                <td>{{ $question->correct_answer }}</td>
                                <td>
                                    @if (($answers[$index] ?? '') == $question->correct_answer)
                                        ✅ Correct
                                    @else
                                        ❌ Wrong
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="d-flex justify-content-center mt-3">
                    <a href="{{ route('mock.test') }}" class="{{ request()->routeIs('mock.test') ? 'active' : '' }} btn btn-primary" wire:navigate>Exit</a>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener("livewire:navigated", function () {
        // re-bind the event listener after navigation if needed
        registerTimerListeners();
    });

    document.addEventListener("DOMContentLoaded", function () {
        registerTimerListeners();
    });

    function registerTimerListeners() {
        let interval;
        let remaining;
        let syncCount = 0;
        let testCancelled = false;

        Livewire.on('startTimer', (initialTime) => {
            remaining = initialTime;
            console.log('Timer started with remaining time:', remaining);
            syncCount = 0;
            testCancelled = false;

            if (interval) clearInterval(interval);

            interval = setInterval(() => {
                if (remaining <= 0) {
                    clearInterval(interval);
                    Livewire.dispatch('autoSubmit');
                    return;
                }

                remaining--;

                const minutes = String(Math.floor(remaining / 60)).padStart(2, '0');
                const seconds = String(remaining % 60).padStart(2, '0');
                const timerDisplay = document.getElementById('timer-display');

                if (timerDisplay) {
                    timerDisplay.innerText = `${minutes}:${seconds}`;
                } else {
                    clearInterval(interval);
                    return;
                }

                syncCount++;
                if (syncCount === 60) {
                    Livewire.dispatch('syncTime');
                    syncCount = 0;
                }
            }, 1000);
        });

        Livewire.on('updateRemainingTime', (backendRemaining) => {
            remaining = backendRemaining;
        });

        document.addEventListener('visibilitychange', function () {
            if (document.hidden && !testCancelled) {
                testCancelled = true;
                Livewire.dispatch('cancelTest');
                clearInterval(interval);
            }
        });
    }
</script>
