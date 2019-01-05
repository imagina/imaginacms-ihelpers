<?php

namespace Modules\Ihelpers\Http\Controllers\Api;

use Modules\Core\Http\Controllers\BasePublicController;
use Mockery\CountValidator\Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Validator;
use Route;
use Log;

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
  public function getParamsRequest($request, $params = [])
  {
    //Convert to object the params
    $defaultValues = (object)$params;

    //Set default values
    $default = (object)[
      "page" => $defaultValues->page ?? false,
      "take" => $defaultValues->take ?? false,
      "filter" => $defaultValues->filter ?? [],
      'include' => $defaultValues->include ?? [],
      'fields' => $defaultValues->fields ?? []
    ];

    //Return params
    $params = (object)[
      "page" => is_numeric($request->input('page')) ? $request->input('page') : $default->page,
      "take" => is_numeric($request->input('take')) ? $request->input('take') :
        ($request->input('page') ? 12 : $default->take),
      "filter" => json_decode($request->input('filter')) ?? (object)$default->filter,
      "include" => $request->input('include') ? explode(",", $request->input('include')) : $default->include,
      "fields" => $request->input('fields') ? explode(",", $request->input('fields')) : $default->fields,
      "user" => Auth::user(),
    ];

    //set language translation
    if (isset($params->filter->locale) && !is_null($params->filter->locale))
      \App::setLocale($params->filter->locale);

    return $params;
  }

  //Validate if response Api is successful
  public function validateResponseApi($response)
  {
    //Get response
    $data = json_decode($response->content());

    //If there is errors, throw error
    if (isset($data->errors))
      throw new Exception($data->errors, $response->getStatusCode());
    else {//if response is successful, return response
      return $data->data;
    }
  }

  //Validate if fields are validated according to rules
  public function validateRequestApi($request)
  {
    //Create Validator
    $validator = Validator::make($request->all(), $request->rules());

    //if get errors, throw errors
    if ($validator->fails()) {
      $errors = json_decode($validator->errors());
      throw new Exception(json_encode($errors), 401);
    } else {//if vlaidation is sucessful, return true
      return true;
    }
  }

  //Validate if code is like status response, and return status code
  public function getStatusError($code = false)
  {
    switch ($code) {
      case 401:
        return 401;
        break;
      case 403:
        return 403;
        break;
      case 404:
        return 404;
        break;
      case 502:
        return 502;
        break;
      case 504:
        return 504;
        break;
      default:
        return 500;
        break;
    }
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

  //Return current user
  public function getAuthUser()
  {
    return Auth::user();
  }
}