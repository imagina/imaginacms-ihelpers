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
    public function parametersUrl($page = false, $take = false, $filter = [], $include = [])
    {
        $request = request();

        return (object)[
            "page" => is_numeric($request->input('page')) ? $request->input('page') : $page,
            "take" => is_numeric($request->input('take')) ? $request->input('take') : $take,
            "filter" => $request->input('filter') ? json_decode($request->input('filter')) : $filter,
            "include" => $request->input('include') ? explode(",", $request->input('include')) : $include
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