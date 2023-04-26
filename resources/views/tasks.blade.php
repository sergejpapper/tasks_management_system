@extends('auth.layouts')

@section('content')
    <div class="panel-body">
        @include('common.errors')

        <form action="{{ url('task') }}" method="POST">
            {{ csrf_field() }}

            <div class="form-group">
                <label for="task-name" class="col-sm-3 control-label"><strong>Task</strong></label>
                <div class="col-sm-6">
                    <input type="text" name="name" id="task-name" class="form-control">
                </div>

                <label for="task-comment" class="col-sm-3 control-label"><strong>Comment</strong></label>
                <div class="col-sm-6">
                    <textarea name="comment" id="task-comment" class="form-control"></textarea>
                </div>

                <label for="task-time-spent" class="col-sm-3 control-label"><strong>Time Spent (min)</strong></label>
                <div class="col-sm-6">
                    <input type="number" min="0" value="0" name="time_spent" id="task-time-spent" class="form-control">
                </div>

                <label for="task-due-date" class="col-sm-3 control-label"><strong>Due Date</strong></label>
                <div class="col-sm-6">
                    <input type="date" name="due_date" id="task-due-date" class="form-control">
                </div>
            </div>
            <br>
            <div class="form-group">
                <div class="col-sm-6">
                    <button type="submit" class="btn btn-primary float-end">Add Task</button>
                </div>
            </div>
        </form>
    </div>

    <br><br>
    <hr class="hr"/>

    @if (count($tasks) > 0)
        <div class="panel panel-default">
            <div class="panel-body">
                <table class="table table-striped task-table">
                    <thead>
                    <tr>
                        <th>Task</th>
                        <th>Comment</th>
                        <th>Time Spent (min)</th>
                        <th>Due Date</th>
                        <th>&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($tasks as $task)
                        <tr>
                            <td class="table-text">{{ $task->name }}</td>
                            <td class="table-text">{{ $task->comment }}</td>
                            <td class="table-text">{{ $task->time_spent }}</td>
                            <td class="table-text">{{ $task->due_date }}</td>
                            <td>
                                <form action="{{ url('task/'.$task->id) }}" method="POST">
                                    {{ csrf_field() }}
                                    {{ method_field('DELETE') }}

                                    <button type="submit" class="btn btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        {!! $tasks->links('pagination::bootstrap-5') !!}

        <hr class="hr"/>

        <form class="form-group" action="{{ url('export') }}" method="GET">
            {{ csrf_field() }}

            <div class="row">
                <div class="col">
                    <label for="tasks-due-date-from" class="col-sm-3 control-label"><strong>Due Date
                            From</strong></label>
                    <div class="col-sm-6">
                        <input type="date" name="due_date_from" id="tasks-due-date-from" class="form-control">
                    </div>
                </div>

                <div class="col">
                    <label for="tasks-due-date-to" class="col-sm-3 control-label"><strong>Due Date To</strong></label>
                    <div class="col-sm-6">
                        <input type="date" name="due_date_to" id="tasks-due-date-to" class="form-control">
                    </div>
                </div>
            </div>
            <br>
            <button type="submit" id="export" class="btn btn-success float-end">Export</button>
        </form>
    @endif
@endsection
