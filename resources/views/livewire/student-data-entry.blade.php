<div>
    <form wire:submit.prevent="submitForm">
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
        <div class="form-group mb-3">
            <label for="name">Name</label>
            <input type="text" class="form-control" id="name" wire:model.blur="name">
            @error('name') 
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group mb-3">
            <label for="address">Address</label>
            <textarea id="address" class="form-control" cols="10" rows="3" wire:model.blur="address"></textarea>
            @error('address') 
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group mb-3">
            <label for="email_address">Email Address</label>
            <input type="email" class="form-control" id="email_address" wire:model.blur="email_address">
            @error('email_address') 
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group mb-3">
            <label for="password">Password</label>
            <input type="password" class="form-control" id="password" wire:model.blur="password">
            @error('password') 
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group mb-3">
            <label for="phone_number">Phone Number</label>
            <input type="text" class="form-control" id="phone_number" wire:model.blur="phone_number">
            @error('phone_number') 
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group mb-3">
            <label for="course">Course</label>
            <div class="border rounded p-2" style="max-height: 5.4rem; overflow-y: auto;">
                @forelse($courses as $course)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="{{ $course->name }}" value="{{ $course->name }}" wire:model.blur="course">
                        <label class="form-check-label" for="{{ $course->name }}">{{ $course->name }}</label>
                    </div>
                @empty
                    <p class="text-muted">No courses available.</p>
                @endforelse
            </div>
            @error('course') 
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group mb-3">
            <label for="image">Image</label>
            <input type="file" class="form-control" id="image" wire:model.blur="image">
            @error('image') 
                <p class="text-danger">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary mt-2">Submit</button>
    </form>
</div>
