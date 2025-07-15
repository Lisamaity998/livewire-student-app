<div>
    <section class="vh-100" style="background-color: #e2d5de;">
        <div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col col-xl-10">
                    <div class="card" style="border-radius: 15px;">
                        <div class="card-body p-5">
                            <h3 class="mb-3">Todo List</h3>
                            <form class="d-flex justify-content-center align-items-center mb-4" wire:submit.prevent="addTodo">
                                <div data-mdb-input-init class="form-outline flex-fill">
                                    <input type="text" id="form" class="form-control form-control-lg" placeholder="What do you need to do today?" wire:model="task" />
                                    @error('task') 
                                        <p class="text-danger">{{ $message }}</p>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-primary btn-lg ms-2">Add</button>
                            </form>
                            <ul class="list-group mb-0" style="max-height: 400px; overflow-y: auto;">
                                @forelse($todos as $todo)
                                    <li class="list-group-item d-flex justify-content-between align-items-center border-start-0 border-top-0 border-end-0 border-bottom rounded-0 mb-2">
                                        @if($updateId === $todo->id)
                                            <div class="d-flex align-items-center w-100">
                                                <input type="text" class="form-control me-2" wire:model="updateTask" />
                                                <a class="text-success me-2" style="cursor: pointer" wire:click.prevent="updateTodo">
                                                    <i class="fas fa-check"></i>
                                                </a>
                                            </div>
                                            @error('updateTask') 
                                                <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        @else
                                            <div class="d-flex align-items-center">                                              
                                                <input class="form-check-input me-2" type="checkbox" :checked="@js($todo->status === 'done')" wire:change="markAsDone({{ $todo->id }})" />
                                                <label style="{{ $todo->status === 'done' ? 'text-decoration: line-through;' : '' }}">{{ $todo->task }}</label>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                @if ($todo->status === 'open')
                                                    <a href="#" class="text-success me-2" style="cursor: pointer" wire:click.prevent="editTodo({{ $todo->id }})">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endif
                                                @if ($todo->status === 'done')
                                                    <a href="#" class="text-danger me-2" style="cursor: pointer" wire:click.prevent="deleteTodo({{ $todo->id }})">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        @endif
                                    </li>
                                @empty
                                    <li class="list-group-item text-center text-muted" style="font-style: italic; font-size: 1.2rem; min-height: 150px;">
                                        No todos here.
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
