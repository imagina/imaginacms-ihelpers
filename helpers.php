<?php
use Illuminate\Support\Str;

if(! function_exists('canonical_url')) {

    function canonical_url() {
        return str_replace(URL::to(''),config('app.url'),URL::current());
    }

}

if(! function_exists('istr_slug')) {
    function istr_slug($title, $separator = '-', $language = 'en',$allowedchars=array())
    {
        $title = Str::ascii($title, $language);
        // Convert all dashes/underscores into separator
        $flip = $separator == '-' ? '_' : '-';
        $title = preg_replace('![' . preg_quote($flip) . ']+!u', $separator, $title);
        // Replace @ with the word 'at'
        $title = str_replace('@', $separator . 'at' . $separator, $title);
        // Remove all characters that are not the separator, letters, numbers, or whitespace.
        $title = preg_replace('![^' . preg_quote($separator) . '^' . preg_quote(".") . '\pL\pN\s]+!u', '', mb_strtolower($title));
        // Replace all separator characters and whitespace by a single separator
        $title = preg_replace('![' . preg_quote($separator) . '\s]+!u', $separator, $title);
        return trim($title, $separator);
    }
}

?>