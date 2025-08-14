<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Models\Project;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
  use ApiResponseTrait;
  public function create(Request $request, $project)
  {
    $project = Project::find($project);

    if (is_null($project)) {
      return $this->apiResponse(404, 'Project not found', null, null);
    }
    if (!$request->has(['created_by', 'project_id'])) {
      $request->merge([
        'project_id' => $request->project_id ?? $project->id,
        'created_by' => auth()->user()->id,
      ]);
    }

    $validation = Validator::make($request->all(), [
      'title' => 'required|string|min:3|max:255',
      'description' => 'sometimes',
      'priority' => [Rule::in(['low', 'medium', 'high'])],
      'status' => [Rule::in(['todo', 'in_progress', 'completed'])],
      'due_date' => 'sometimes|date|after_or_equal:today',
      'project_id' => 'required|exists:projects,id',
      'assigned_to' => 'required|exists:users,id',
      'created_by' => 'sometimes|exists:users,id',
    ]);

    if ($validation->fails()) {
      $errors = collect($validation->errors()->messages())
        ->map(fn($messages) => $messages[0]); // get first error message for each field
      return $this->apiResponse(422, 'validation error', $errors);
    }


    $task = Task::create($request->all());

    return $this->apiResponse(201, 'task : ' . $task->title . ' has been created successfully', null, $task);
  }

  public function index($project)
  {
    $project = Project::find($project);

    if (is_null($project)) {
      return $this->apiResponse(404, 'Project not found', null, null);
    }


    $tasks = Task::where('project_id', $project->id)->paginate(5);

    if ($tasks->isEmpty()) {
      return $this->apiResponse(404, 'no tasks assigned to this project', null, null);
    }

    return $this->apiResponse(200, 'tasks list related to project : ' . $project->name, null, TaskResource::collection($tasks));
  }


  public function show($task)
  {
    $task = Task::find($task);
    if (is_null($task)) {
      return $this->apiResponse(404, 'Task not found', null, null);
    }

    return $this->apiResponse(200, 'Data of task : ' . $task->name, null, new TaskResource($task));
  }

  public function delete($task)
  {
    $task = Task::find($task);
    if (is_null($task)) {
      return $this->apiResponse(404, 'Task not found', null, null);
    }
    $task->delete();

    return $this->apiResponse(200, 'Task : ' . $task->title . ' has been deleted successfully', null, null);
  }


  public function assignTaskToUser(Request $request, $task)
  {
    $task = Task::find($task);
    if (is_null($task)) {
      return $this->apiResponse(404, 'Task not found', null, null);
    }
    $validation = Validator::make($request->only('assigned_to'), [

      'assigned_to' => 'required|exists:users,id',
    ], [
      'assigned_to.exists'   => 'The selected member is invalid.',
      'assigned_to.required'   => 'The selected member is required.',
    ]);


    if ($validation->fails()) {
      $errors = collect($validation->errors()->messages())
        ->map(fn($messages) => $messages[0]); // get first error message for each field
      return $this->apiResponse(422, 'validation error', $errors);
    }
    $task->update($request->only('assigned_to'));

    return $this->apiResponse(200, 'task : ' . $task->name, null, new TaskResource($task));
  }


  public function updateTaskStatus(Request $request, $task)
  {
    $task = Task::find($task);
    if (is_null($task)) {
      return $this->apiResponse(404, 'Task not found', null, null);
    }
    $validation = Validator::make($request->only('status'), [

      'status' => [Rule::in(['todo', 'in_progress', 'completed'])],
    ], [
      'status.role'   => 'The selected status is invalid.',
    ]);


    if ($validation->fails()) {
      $errors = collect($validation->errors()->messages())
        ->map(fn($messages) => $messages[0]); // get first error message for each field
      return $this->apiResponse(422, 'validation error', $errors);
    }

    $task->update($request->only('status'));

    return $this->apiResponse(200, 'task : ' . $task->name, null, new TaskResource($task));
  }
}
