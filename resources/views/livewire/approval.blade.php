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
        <h1 class="content-title">Approval</h1>
        <input type="text" class="form-control" style="margin-bottom:30px; width:15rem" placeholder="Search Student hare" wire:model.live.debounce.400ms="search">
    </div>
    <div class="student-table">
        <table class="table">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Email</th>
                <th scope="col">Phone No</th>
                <th scope="col">Address</th>
                <th scope="col">Course</th>
                <th scope="col">Image</th>
                <th scope="col">Action</th>
              </tr>
            </thead>
            <tbody>
                @forelse ($data as $student)
                    <tr>
                        <th scope="row">{{ $loop->iteration }}</th>
                        <td>{{ $student->name }}</td>
                        <td>{{ $student->email_address }}</td>
                        <td>{{ $student->phone_number }}</td>
                        <td>{{ $student->address }}</td>
                        <td>{{ $student->course }}</td>
                        <td><img src="{{ asset('storage/' . $student->image) }}" alt="Student Image" width="50px"></td>
                        <td>
                            <div class="d-flex gap-2">
                                <button class="btn btn-success" wire:click="approve({{ $student->id }})">Approve</button>
                                <button class="btn btn-danger" wire:click="reject({{ $student->id }})">Reject</button>
                            </div>
                        </td>
                    </tr>
                @empty  
                    <tr>
                        <td colspan="8" class="text-center">No Data Found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
