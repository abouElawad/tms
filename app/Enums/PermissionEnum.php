<?php

namespace App\Enums;

enum PermissionEnum: string
{
  #user permissions
  case VIEW_USERS = 'view_users';
  case VIEW_USER = 'view_user';
  case CREATE_USER = 'create_user';
  case UPDATE_USER = 'update_user';
  case DELETE_USER = 'delete_user';

    #role permissions

  case VIEW_ROLES = 'view_roles';
  case CREATE_ROLE = 'create_role';
  case UPDATE_ROLE = 'update_role';
  case DELETE_ROLE = 'delete_role';

  #permission permissions

   case VIEW_PERMISSIONS = 'view_permissions';
  case CREATE_PERMISSION = 'create_permission';
  case UPDATE_PERMISSION = 'update_permission';
  case DELETE_PERMISSION = 'delete_permission';
}
