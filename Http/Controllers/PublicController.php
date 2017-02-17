<?php

namespace Modules\Ihelpers\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Modules\Core\Http\Controllers\BasePublicController;
use DOMDocument;
use DOMXPath;
use Request;

class PublicController extends BasePublicController {

    private $app;

    public function __construct(Application $app)
    {
        parent::__construct();

        $this->app = $app;
    }


    public function inlinesave() {


        if(!$this->auth->hasAccess('page.pages.edit')) return;

        try {

            $request = Request::all();
            $inlinedata = $request["inlinedata"];

            if($request["type"]=="page") {

                if(\LaravelLocalization::getDefaultLocale()==\LaravelLocalization::getCurrentLocale()) {
                    if (!empty($request["id"])) $tplpath = base_path('Themes/Imagina2017/views/pages/content/' . intval($request["id"]) . '.blade.php');
                } else {
                    if (!empty($request["id"])) $tplpath = base_path('Themes/Imagina2017/views/pages/content/'.\LaravelLocalization::getCurrentLocale() ."/". intval($request["id"]) . '.blade.php');
                }


                $html = file_get_contents($tplpath);

                $dom = new DomDocument();
                $dom->loadHTML($html, LIBXML_HTML_NODEFDTD);
                //$dom->loadHTMLFile($tplpath, LIBXML_HTML_NODEFDTD);
                
                $finder = new DomXPath($dom);
                $classname="icontenteditable";

                $nodes = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");



                foreach($inlinedata as $k=>$inlineitem) {
                    $nodes->item($k)->nodeValue='';
                    $this->appendHTML($nodes->item($k), $inlineitem);
                }

                $dom->saveHTMLFile($tplpath);

                $html = file_get_contents($tplpath);
                $html = str_replace(['<html><body>','</body></html>'],'',$html);


                //Todo: Fix this replace.
                $html = str_replace('$page-&gt;','$page->',$html);
                file_put_contents($tplpath,$html);


                return response()->json(['success'=>'true']);
            }


        } catch(\Throwable $t) {
            \Log::error($t);
        }

        return response()->json(['success'=>'false']);

    }

    function appendHTML($parent, $source) {
        $tmpDoc = new DOMDocument();
        $tmpDoc->loadHTML($source);
        foreach ($tmpDoc->getElementsByTagName('body')->item(0)->childNodes as $node) {
            $node = $parent->ownerDocument->importNode($node,true);
            $parent->appendChild($node);
        }
    }

}

?>