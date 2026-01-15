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
        <h1 class="content-title">Activity Logs</h1>
        <input type="text" id="searchStudent" class="form-control" style="margin-bottom:30px; width:15rem" placeholder="Search hare" wire:model.live.debounce.400ms="search">
    </div>
    <div class="student-table">
        <table class="table">
            <thead>
                <tr>
                    <th>User Role</th>
                    <th>User Name</th>
                    <th>Action</th>
                    <th>Description</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td>{{ ucfirst($log->user_role) }}</td>
                        <td>{{ $log->user_name }}</td>
                        <td>{{ $log->activity_type }}</td>
                        <td>{{ $log->description }}</td>
                        <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">No logs found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
