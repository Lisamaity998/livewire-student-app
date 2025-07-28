<div>
    <div class="content-header">
        <h1 class="content-title">Test Result List</h1>
        <input type="text" id="searchTest" class="form-control" style="margin-bottom:30px; width:15rem" placeholder="Search test result here" wire:model.live.debounce.400ms="search">
    </div>

    <div class="student-table">
        <table class="table">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Student Name</th>
                <th scope="col">Couese Name</th>
                <th scope="col">Start Date</th>
                <th scope="col">Start Time</th>
                <th scope="col">Score</th>
                <th scope="col">Status</th>
              </tr>
            </thead>
            <tbody>
                @forelse ($testResults as $result)
                    <tr>
                        <th>{{ $loop->iteration }}</th>
                        <td>{{ $result->student->name ?? 'N/A' }}</td>
                        <td>{{ $result->course->name ?? 'N/A' }}</td>
                        <td>{{ \Carbon\Carbon::parse($result->test_date)->format('d M Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($result->start_time)->format('h:i:s A') }}</td>
                        @php
                            $percentage = round(($result->score / $result->total_questions) * 100, 2);
                        @endphp
                        <td>{{ $percentage }}%</td>
                        <td>
                            @if($percentage >= 70)
                                <span class="badge bg-success">Passed</span>
                            @else
                                <span class="badge bg-danger">Failed</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No Data Found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
