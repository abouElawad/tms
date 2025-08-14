<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
  use ApiResponseTrait;
  public function stats()
  {
    // dd(Task::count());
    $stats = [
      'users_count'     => User::count(),
      'projects_count'   => Project::count(),
      'all_tasks'     => Task::count(),
      'active_tasks'     => Task::where('status', 'in_progress')->count(),
      'completed_tasks'  => Task::where('status', 'completed')->count(),
      'overdue_tasks'    => Task::where('due_date', '<', now())
        ->where('status', '!=', 'completed')
        ->count(),
    ];

    return $this->apiResponse(200, 'dashboard statistics', null, $stats);
  }

  public function recentActivity()
  {


    $projects = Project::with('user:id,name')
      ->latest()
      ->take(5)
      ->get()
      ->map(function ($project) {
        return [
          'type' => 'project',
          'title' => $project->name,
          'created_by' => $project->user->name,
          'created_at' => $project->created_at->format('y-m-d h:i:s'),
        ];
      });

    $tasks = Task::with('createdBy:id,name')
      ->latest()
      ->take(5)
      ->get()
      ->map(function ($task) {
        return [
          'type' => 'task',
          'title' => $task->title,
          'created_by' => $task->createdBy->name,
          'assigned_to' => $task->assignedTo->name,
          'created_at' => $task->created_at->format('y-m-d h:i:s'),
          'due_date' => $task->due_date,
          'remaining time' => $task->created_at->diff($task->due_date)->format('%d days, %h hours, %i minutes'),
        ];
      });


    $activities = array_merge(['Projects' => $projects], ['tasks' => $tasks->toArray()]);
    return $this->apiResponse(200, 'recent $activities', null, $activities);
  }

  # way from user
  public function showUserTasks($user)
  {
    $user = User::find($user);
    if (is_null($user)) {
      return $this->apiResponse(404, 'User not found', null, null);
    }
    $assignedTasks = $user->assignedTasks()->paginate(5)->map(function ($task) {
      return [
        "id" => $task->id,
        "title" => $task->title,
        "description" => $task->description,
        "priority" => $task->priority,
        "status" => $task->status,
        "due_date" => $task->due_date,
        "project_name" => $task->project->name,
        "assigned_to" => $task->assignedTo->name,
        "created_by" => $task->createdBy->name,
      ];
    });
    // dd($assignedTasks);
    $userTasks = Task::where('assigned_to', $user->id)->get();
    if ($userTasks->isEmpty()) {
      return $this->apiResponse(200, 'user is not assigned to any tasks');
    }

    return $this->apiResponse(200, 'all ', null, $assignedTasks);
  }


# Way from task

      public function showUserTasks1($user)
  {
    $user = User::find($user);

    if (is_null($user)) {
    return $this->apiResponse(404, 'User not found', null, null);
  }
    $assignedTasks = Task::where('assigned_to',$user->id)->get();
    // dd($assignedTasks);
    $userTasks = Task::where('assigned_to',$user->id)->get();
    if($userTasks->isEmpty())
      {
        return $this->apiResponse(200,'user is not assigned to any tasks');
    }

    return $this->apiResponse(200,'all ',null,TaskResource::collection($assignedTasks));
  }
}
