<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Services\GoogleCalendarService;
use Carbon\Carbon;

class GoogleController extends Controller
{
    protected GoogleCalendarService $google;

    public function __construct(GoogleCalendarService $google)
    {
        $this->google = $google;
    }

    public function syncTasks()
    {
        $tasks = Task::all();

        foreach ($tasks as $task) {
            $date = Carbon::parse($task->date)->format('Y-m-d');

            $this->google->addEvent($task->description, $task->description, $date);
        }

        return 'All tasks synced to Google Calendar!';
    }

    public function addTask($task){
        $date = Carbon::parse($task->date)->format('Y-m-d');        
    }   
}
