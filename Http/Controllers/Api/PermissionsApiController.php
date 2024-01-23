<?php

namespace Modules\Ihelpers\Http\Controllers\Api;

use Modules\Core\Http\Controllers\BasePublicController;
use Modules\Iprofile\Repositories\UserApiRepository;
use Modules\User\Entities\Sentinel\User;
use Modules\Iprofile\Entities\Role;
use Illuminate\Support\Arr;

class PermissionsApiController extends BasePublicController
{
  private $userApiRepository;
  public function __construct(UserApiRepository $userApiRepository)
  {
    $this->userApiRepository = $userApiRepository;
  }
  
  //Get settings by relatedId and entityName
  public function index($params = [])
  {
    
    $params = (object)$params;//Conver params
    if (!$params || !$params->relatedId || !$params->entityName) return [];//Validate params
    $permissions = [];//Default response

    //Request
    if (!is_array($params->relatedId)) $params->relatedId = [$params->relatedId];

    //Get user permissons
    if ($params->entityName == 'user'){
      $user = $this->userApiRepository->getItem($params->relatedId);
      $permissionsData = $user->pluck('permissions')->toArray();
    }

    //Get role permissons
    if ($params->entityName == 'role'){
      $roleRepository = app("Modules\Iprofile\Repositories\RoleApiRepository");
      $permissionsData = $roleRepository->getItemsBy(json_decode(json_encode(["filter" => ["id" => $params->relatedId]])))->pluck('permissions')->toArray();
    }


    //Merge all permissions
    foreach ($permissionsData as $group) {
        if(is_array($group)) {
          foreach ($group as $name => $value) {
            if (!isset($permissions[$name])) $permissions[$name] = $value;
            else if (!$permissions[$name]) $permissions[$name] = $value;
          }
        }
    }

    //Response
    return $permissions;
  }

  //Return all settings assigned to user
  public function getAll($params = [])
  {
    $params = (object)$params;//Conver params
    $permissions = [];//Default response

    if (!isset($params->userId) || !$params->userId) return [];//Validate userID params
  
    $user = $this->userApiRepository->getItem($params->userId,json_decode(json_encode(["include" => ["roles"]])));

    if (!isset($params->roleId) || !$params->roleId) $params->roleId = $user->roles->pluck('id')->toArray();//Validate roleId

    //Get settings per entity
    $userPermissions = $this->index(['relatedId' => $params->userId, 'entityName' => 'user']);
    $rolePermissions = $this->index(['relatedId' => $params->roleId, 'entityName' => 'role']);

    //Merge all settings with priority
    $permissions = array_merge($rolePermissions, $userPermissions);

    //Response
    return $permissions;
  }
}
