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
        <h1 class="content-title">Previous Classes</h1>
    </div>

    <div class="student-table">
        @if(count($upcomingClasses) > 0)
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Class Name</th>
                        <th>Teacher</th>
                        <th>Topic</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th >Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($upcomingClasses as $class)
                        <tr>                
                            <td>{{ $class->class_name }}</td>
                            <td>{{ $class->teacher->name ?? 'N/A' }}</td>
                            <td>{{ $class->course->name }}</td>
                            <td>{{ \Carbon\Carbon::parse($class->start_date)->format('d M Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($class->class_time)->format('h:i A') }}</td>
                            <td>
                                @if ($this->hasMarkedAttendance($class->id))
                                    <a href="{{ route('class.view', $class->id) }}" class="btn btn-success" style="">View Class</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>No previous classes found for your course's.</p>
        @endif
    </div>
</div>
