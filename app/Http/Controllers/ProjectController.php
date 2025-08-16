<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Traits\checkExistenceTrait;
use function PHPUnit\Framework\isNull;
use App\Http\Resources\ProjectResource;
use Illuminate\Support\Facades\Validator;

use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Contracts\Providers\Auth;

class ProjectController extends Controller
{
  use ApiResponseTrait;
  public function index()
  {

    $projects = Project::with(['user:id,name,role_id', 'user.role:id,name'])->paginate(5);
    // return (ProjectResource::collection($projects));  to return paginated with meta and links

    return $this->apiResponse(200, 'all projects', null, ProjectResource::collection($projects));
    // to navigate pages ?page=3
  }
  public function create(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'name' => 'required|unique:projects,name',
      'description' => 'sometimes|string|max:1000',
      'status' => [
        'sometimes',
        Rule::in(['planning', 'active', 'on_hold', 'completed', 'archived']),
      ],
      'user_id' => 'sometimes|exists:users,id'
    ]);

    if ($validator->fails()) {
      $errors = collect($validator->errors()->messages())
        ->map(fn($messages) => $messages[0]); // get first error message for each field
      return $this->apiResponse(422, 'validation error', $errors);
    }

    if (!$request->has('user_id')) {
      $request->merge(['user_id' => auth()->user()->id]);
    }

    $project = Project::create($request->all());

    $data = array_merge($project->toArray(), ['user' => $project->user->name]);

    return $this->apiResponse(201, 'new project has been created successfully', null, $data);
  }

  public function show($project)
  {
    $project = Project::find($project);

    if (is_null($project)) {
      return $this->apiResponse(404, 'Project not found', null, null);
    }

    return $this->apiResponse(200, 'the data of the project : ' . $project->name, null, new ProjectResource($project));
  }
  public function update(Request $request, $project)
  {

    $project = Project::find($project);
    if (is_null($project)) {
      return $this->apiResponse(404, 'Project not found', null, null);
    }


    $validation = Validator::make($request->all(), [
      'name' => 'sometimes|min:3|unique:projects,name,' . $project->id,
      'description' => 'sometimes|string|max:1000',
      'status' => [
        'sometimes',
        Rule::in(['planning', 'active', 'on_hold', 'completed', 'archived']),
      ],
      'user_id' => 'sometimes|exists:users,id'
    ]);

    if ($validation->fails()) {
      $errors = collect($validation->messages())
        ->map(fn($messages) => $messages[0]); // get first error message for each field
      return $this->apiResponse(400, null, $errors, null);
    }

    $project->update($request->all());

    return $this->apiResponse(200, 'the data of the project : ' . $project->name, null, new ProjectResource($project));
  }
  public function delete($project)
  {

    $project = Project::find($project);

    if (is_null($project)) {
      return $this->apiResponse(404, 'Project not found', null, null);
    }

    $project->delete();
    return $this->apiResponse(200, 'Project : ' . $project->name . ' has been deleted successfully', null, null);
  }

  public function addMember(Request $request, $project)
  {
    //check project existence 
    $project = Project::find($project);
    if (is_null($project)) {
      return $this->apiResponse(404, 'Project not found', null, null);
    }

    //check user selection 
    if (!$request->has('user_id')) {
      $request->merge(['user_id' => auth()->user()->id]);
    }

    // validate user 
    $validation = validator::make($request->all(), [
      'user_id' => [
        'sometimes',
        'exists:users,id',
        Rule::unique('project_user')
          ->where('project_id', $project->id)
      ]
    ], [
      // 'user_id.required' => 'The user field is required.',
      // 'user_id.exists'   => 'The selected user does not exist.',
      'user_id.unique'   => 'This user is already a member of this project.',
    ]);
    //validation failure message
    if ($validation->fails()) {
      $errors = collect($validation->messages())
        ->map(fn($messages) => $messages[0]); // get first error message for each field
      return $this->apiResponse(400, '', $errors, null);
    }
    //adding a member to a project 
    $project->users()->attach($request->user_id, ['role_id' => $project->user->role_id]);

    
    return $this->apiResponse(201, 'adding a member to a project');
  }

//   public function removeMember( $project,$user)
//   {
//     $pivot = DB::table('project_user')
//     ->where([
//         'project_id' => $project,
//         'user_id'    => $user
//     ])
//     ->first();

//     if(!is_null($pivot))
//     {
//       DB::table('project_user')
//     ->where([
//         'project_id' => $project,
//         'user_id'    => $user
//     ])->delete();

//       return $this->apiResponse(200,'record deleted',null);
//     }

//     return $this->apiResponse(404,'no record to delete');
//   }

public function removeMember( $project,$user)
  {
    $project = Project::find($project);

    if (is_null($project)) {
      return $this->apiResponse(404, 'no Project not found for this record', null, null);
    }

    if(!$project->users()->detach($user))
    {
      return $this->apiResponse(404, 'the user is not registered to the project', null, null);
    }
      return $this->apiResponse(200,'member removed from project : '. $project->name,null);

  }

}
