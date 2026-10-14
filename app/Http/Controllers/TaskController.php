<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::all();
        $trashedTasks = Task::onlyTrashed()->get();
        return view('index', compact('tasks', 'trashedTasks'));
    }

    public function store(Request $request)
    {
        $request->validate(['title' => 'required|max:255']);
        Task::create($request->only('title', 'description'));
        return back()->with('success', 'Tarefa criada com sucesso!');
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return back()->with('success', 'Tarefa movida para a lixeira!');
    }


    public function forceDelete($id)
    {
        Task::withTrashed()->where('id', $id)->forceDelete();
        return back()->with('success', 'Tarefa excluída permanentemente!');
    }
}
