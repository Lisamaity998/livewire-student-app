<div>
    <div class="content-header">
        <h1 class="content-title">Student List</h1>
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
                <th scope="col">Course</th>
              </tr>
            </thead>
            <tbody>
                @forelse ($data as $student)
                    <tr>
                        <th scope="row">{{ $loop->iteration }}</th>
                        <td>{{ $student->name }}</td>
                        <td>{{ $student->email_address }}</td>
                        <td>{{ $student->phone_number }}</td>
                        <td>{{ $student->course }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">No Data Found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
