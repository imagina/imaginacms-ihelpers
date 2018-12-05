<?php

namespace Modules\Ihelpers\Http\Controllers\Api;

use Illuminate\Http\Request;
use Log;
use Mockery\CountValidator\Exception;
use Modules\Core\Http\Controllers\BasePublicController;
use Modules\User\Transformers\UserProfileTransformer;
use Route;

class BaseApiController extends BasePublicController
{
  public function __construct()
  {
    parent::__construct();
  }

  //Request URL Get Standard or set default values
  public function parametersUrl($page = false, $take = false, $filter = [], $include = [], $fields = [])
  {
    $request = request();

    return (object)[
      "page" => is_numeric($request->input('page')) ? $request->input('page') : $page,
      "take" => is_numeric($request->input('take')) ? $request->input('take') : $take,
      "filter" => json_decode($request->input('filter')) ?? (object)$filter,
      "include" => $request->input('include') ? explode(",", $request->input('include')) : $include,
      "fields" => $request->input('fields') ? explode(",", $request->input('fields')) : $fields
    ];
  }

  //Return params from Request
  public function getParamsRequest($params = [])
  {
    //Convert to object the params
    $params = (object)$params;
    //Set default values
    $params = (object)[
      "page" => $params->page ?? false,
      "take" => $params->take ?? false,
      "filter" => $params->filter ?? [],
      'include' => $params->include ?? [],
      'fields' => $params->fields ?? []
    ];

    //Get params from Request
    $request = request();

    //Return params
    return (object)[
      "page" => is_numeric($request->input('page')) ? $request->input('page') : $params->page,
      "take" => is_numeric($request->input('take')) ? $request->input('take') : $params->take,
      "filter" => json_decode($request->input('filter')) ?? (object)$params->filter,
      "include" => $request->input('include') ? explode(",", $request->input('include')) : $params->include,
      "fields" => $request->input('fields') ? explode(",", $request->input('fields')) : $params->fields
    ];
  }

  //Transform data of Paginate
  public function pageTransformer($data)
  {
    return [
      "total" => $data->total(),
      "lastPage" => $data->lastPage(),
      "perPage" => $data->perPage(),
      "currentPage" => $data->currentPage()
    ];
  }
}