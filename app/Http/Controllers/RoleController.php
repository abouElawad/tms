<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignPermissionRequest;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class RoleController extends Controller
{
  use ApiResponseTrait;
  public function index()
  {
    return $this->apiResponse(200, 'all roles', null, RoleResource::collection(Role::get()));
  }

  public function create(StoreRoleRequest $request)
  {
    $role = Role::create($request->validated());
    return $this->apiResponse(201, 'new role created', null, RoleResource::make($role));
  }

  public function update(UpdateRoleRequest $request, $role)
  {
    $role = Role::find($role);

    if (is_null($role)) {
      return $this->apiResponse(404, 'Role not found', null, null);
    }

    $role->update($request->validated());

    return $this->apiResponse(201, $role->name . ' has been updated', null, RoleResource::make($role));
  }

  public function show($role)
  {
    $role = Role::find($role);

    if (is_null($role)) {
      return $this->apiResponse(404, 'Role not found', null, null);
    }

    return $this->apiResponse(201, 'role data', null, RoleResource::make($role->load('permissions')));
  }

  public function delete($role)
  {
    $role = Role::find($role);

    if (is_null($role)) {
      return $this->apiResponse(404, 'Role not found', null, null);
    }
    $role->delete();
    return $this->apiResponse(201, 'role deleted', null, RoleResource::make($role));

  }

  public function assignPermission(AssignPermissionRequest $request,Role $role)
  {
    $permissions = $request->input('permissions',[]);
   
    $role->permissions()->sync($permissions);
    // return $this->apiResponse(201,'permissions assigned successfully',null,RoleResource::make($role->load('permissions')));
    return $this->apiResponse(201,'permissions assigned successfully',null, RoleResource::make($role->load('permissions')));
  }

  public function removePermission(Request $request,Role $role)
  {
     $permissions = $request->input('permissions',[]);

    $role->permissions()->detach($permissions);
     return $this->apiResponse(201,'permissions removed successfully',null,RoleResource::make($role->load('permissions')));
  }
}
