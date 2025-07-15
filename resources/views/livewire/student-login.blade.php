<div>
    <form wire:submit.prevent="login">
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

        <div class="form-group mb-3">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" wire:model.blur="email">
            @error('email') 
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

        <div class="form-group mt-4 mb-1 d-flex justify-content-center">
            <button type="submit" class="btn btn-success pr-4 pl-4">Login</button>
        </div>
    </form>
</div>
