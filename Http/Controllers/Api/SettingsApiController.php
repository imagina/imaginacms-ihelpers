<?php

namespace Modules\Ihelpers\Http\Controllers\Api;

use Modules\Core\Http\Controllers\BasePublicController;
use Modules\User\Entities\Sentinel\User;
use Modules\Iprofile\Entities\Role;
use Modules\Iprofile\Entities\Setting;
use Illuminate\Support\Arr;

class SettingsApiController extends BasePublicController
{
  //Get settings by relatedId and entityName
  public function index($params = [])
  {
    $params = (object)$params;//Conver params
    if (!isset($params) || !isset($params->relatedId) || !isset($params->entityName)) return [];//Validate params
    $settings = [];//Defualt response

    //Request
    if (!is_array($params->relatedId)) $params->relatedId = [$params->relatedId];
    $settingsData = Setting::where('entity_name', $params->entityName)
      ->whereIn('related_id', $params->relatedId)->get()->toArray();

    //Merge Settings
    foreach ($settingsData as $setting) {
      $settingName = $setting['name'];//Setting name
      $settingValue = $setting['value'];//Setting value

      //Get all values of same setting name and validate value
      $filtered = Arr::where($settingsData, function ($value, $key) use ($settingName) {
        if ($value['name'] != $settingName) return false;//Validate same name
        if (is_null($value['value'])) return false;//Validate if is not null
        if ($value['value'] == '') return false;//Validate if is empty string
        if (is_array($value['value']) && !count($value['value'])) return false;//Validate if is array and has values
        return true;//Default response
      });

      //Set setting if the setting exist the same number of relatedIds
      //if (count($params->relatedId) == count($filtered)) {
        //Merge if is array
        if (is_array($settingValue))
          $settings[$settingName] = array_merge(($settings[$settingName] ?? []), $settingValue);
        //Replace value
        else $settings[$settingName] = $settingValue;
      //};
    }

    //Response
    return $settings;
  }

  //Return all settings assigned to user
  public function getAll($params = [])
  {
    $params = (object)$params;//Conver params
    $settings = [];//Default response

    if (!isset($params->userId) || !$params->userId) return [];//Validate userID params
    $userApiRepository = app("Modules\Iprofile\Repositories\UserApiRepository");
    $user = $userApiRepository->getItem($params->userId,json_decode(json_encode(["include" => ["roles","departments"]])));

    //Validate roleId
    if (!isset($params->roleId) || !$params->roleId)
      $params->roleId = $user->roles->pluck('id')->toArray();
    //Validate department id
    if (!isset($params->departmentId) || !$params->departmentId)
      $params->departmentId = $user->departments->pluck('id')->toArray();

    //Get settings per entity
    $userSettings = $this->index(['relatedId' => $params->userId, 'entityName' => 'user']);
    $departmentSettings = $this->index(['relatedId' => $params->departmentId, 'entityName' => 'department']);
    $roleSettings = $this->index(['relatedId' => $params->roleId, 'entityName' => 'role']);

    //Merge all settings with priority
    $settings = array_merge($roleSettings, $departmentSettings, $userSettings);

    //Response
    return $settings;
  }
}
