<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TasksController
{
    public function index(): View|Application|Factory|\Illuminate\Contracts\Foundation\Application
    {
        $tasks = Task::where('user_id', Auth::id())->paginate(2);

        return view('tasks', [
            'tasks' => $tasks
        ]);
    }

    function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'comment' => 'required|max:500',
            'time_spent' => 'required',
            'due_date' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('/tasks')
                ->withInput()
                ->withErrors($validator);
        }

        $task = new Task;
        $task->name = $request->name;
        $task->comment = $request->comment;
        $task->time_spent = $request->time_spent;
        $task->due_date = $request->due_date;
        $task->user_id = Auth::id();
        $task->save();

        return redirect('/tasks');
    }

    public function delete(Task $task): Application|Redirector|RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        $task->delete();

        return redirect('/tasks');
    }

    public function export(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'due_date_from' => 'required',
            'due_date_to' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('/tasks')
                ->withInput()
                ->withErrors($validator);
        }

        $fileName = 'tasks.csv';
        $tasks = Task::whereBetween('due_date', [$request->due_date_from, $request->due_date_to])->get();

        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $callback = function () use ($tasks) {
            $totalTime = 0;

            $columns = array('Task');
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($tasks as $task) {
                $totalTime += $task->time_spent;
                $row['Title'] = $task->name;

                fputcsv($file, array($row['Title']));
            }

            fputcsv($file, ['Total time spent: ' . $totalTime]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
