<?php

if(! function_exists('canonical_url')) {

    function canonical_url() {
        return str_replace(URL::to(''),config('app.url'),URL::current());
    }

}


?>