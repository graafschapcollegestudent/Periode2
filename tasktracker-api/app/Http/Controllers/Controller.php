<?php

namespace App\Http\Controllers;
use App\Models\Task;

abstract class Controller
{
    public function index()
{
    $tasks = Task::get();
    return view('tasks.index', compact('tasks'));
}
}
//test
