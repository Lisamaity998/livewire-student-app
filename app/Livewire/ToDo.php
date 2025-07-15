<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ToDoList;


class ToDo extends Component
{
    public $todos;
    public string $task = '';
    public $updateId = null;
    public string $updateTask = '';
 

    public function mount()
    {
        $this->fatchTodos();
    }

    public function fatchTodos()
    {
        $this->todos = ToDoList::all()->reverse();
    }

    public function addTodo()
    {
        $this->validate([
            'task' => 'required|string|max:255',
        ]);
        if($this->task != '') {
            $newToDo = new ToDoList();
            $newToDo->task = $this->task;
            $newToDo->status = 'open';
            $newToDo->save();
        }
        $this->task = '';
        $this->fatchTodos();
    }

    public function markAsDone($id)
    {
        $todo = ToDoList::find($id);
        if ($todo) {
            $todo->status = 'done';
            $todo->save();
            $this->fatchTodos();
        }
    }

    public function editTodo($id)
    {
        $todo = ToDoList::find($id);
        if ($todo) {
            $this->updateId = $id;
            $this->updateTask = $todo->task;
        }
    }


    public function updateTodo()
    {
        $this->validate([
            'updateTask' => 'required|string|max:255',
        ]);
        $todo = ToDoList::find($this->updateId);
        if ($todo) {
            $todo->task = $this->updateTask;
            $todo->save();
        }
        $this->reset(['updateId', 'updateTask']);
        $this->fatchTodos();
    }


    public function deleteTodo($id)
    {
        $todo = ToDoList::find($id);
        if ($todo) {
            $todo->delete();
            $this->fatchTodos();
        }
    }

    public function render()
    {
        return view('livewire.to-do');
    }
}
