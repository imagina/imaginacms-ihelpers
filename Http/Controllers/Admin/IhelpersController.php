<?php

namespace Modules\Ihelpers\Http\Controllers\Admin;

use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use Modules\Ihelpers\Other\ImResponseCache\ImResponseCache;

class IhelpersController extends AdminBaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function clearcache()
    {

        $imresponsecache = resolve(ImResponseCache::class);
        //Clear cache for spatie cache.
        $imresponsecache->flush();
        //Clear page cache in html files.
        $imresponsecache->flushPageCache();

        return redirect()->route('dashboard.index')
            ->withSuccess(trans('ihelpers::common.cache_cleared'));

    }

}
