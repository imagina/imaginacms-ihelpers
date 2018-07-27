<?php

namespace Modules\Ihelpers\Http\Controllers\Admin;

use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use Modules\Ihelpers\Other\ImResponseCache\ImResponseCache;
use Modules\Ihelpers\Jobs\CreateSitemap;
use Illuminate\Support\Facades\Storage;

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
    /*
     * This method return view to generate sitemap
     */
    public function sitemapGet(){
        return view('ihelpers::indexSiteMap');
    }

    public function queryRelationSiteMap($configEntities,$paramRelation){
        $raw="1=1";
        $paramQuery="";
        if(!empty($configEntities['params'])){
            foreach($configEntities["params"] as $params){
                if($params["param1"]!="getParam")
                    $paramQuery=$params["param1"];
                else
                    $paramQuery=$paramRelation;
                $raw.=' and '.$params["param0"].$params["operator"].$paramQuery;
            }
        }
        if(!empty($configEntities['orderBy']))
            $entity=app($configEntities['Entity'])->whereRaw($raw)->orderBy($configEntities["orderBy"]["param0"],$configEntities["orderBy"]["param1"])->get();
        else
            $entity=app($configEntities['Entity'])->whereRaw($raw)->get();
        return $entity;
    }
    /* Config Urls with slug to load sitemap
        * Params:
        * @name: Category
        * @Entity:\Modules\Icommerce\Entities\Category
        * @url : env('APP_URL').'/category'
        * @replaceInUrl: 'category'
        * @orderBy : 'title','ASC'
        * @findTitle: true or false
        * @params : array of params with param0,param1 and operator
    */
    public function sitemapPost(){
        $html='<!DOCTYPE html><html lang="en" dir="ltr">
               <head>
                <meta charset="utf-8">
                <title>'.$_SERVER["SERVER_NAME"].'</title>
                <style type="text/css">
       	            body {
       		            background-color: #fff;
       		            font-family: "Roboto", "Helvetica", "Arial", sans-serif;
       		            margin: 0;
       	            }
                  	#top {
                   		background-color: #b1d1e8;
       		            font-size: 16px;
       		            padding-bottom: 40px;
       	            }
                   	h3 {
       		            margin: auto;
       		            padding: 10px;
       		            max-width: 600px;
       		            color: #666;
       	            }
                   	h3 span {
       		            float: right;
       	            }
                   	h3 a {
       		            font-weight: normal;
       		            display: block;
       	            }
                   	#cont {
       		            position: relative;
       		            border-radius: 6px;
       		            box-shadow: 0 16px 24px 2px rgba(0, 0, 0, 0.14), 0 6px 30px 5px rgba(0, 0, 0, 0.12), 0 8px 10px -5px rgba(0, 0, 0, 0.2);
                   		background: #f3f3f3;
                   		margin: -20px 30px 0px 30px;
       		            padding: 20px;
       	            }   
                   	a:link,
       	            a:visited {
       		            color: #0180AF;
       		            text-decoration: underline;
       	            }
                   	a:hover {
       		            color: #666;
       	            }
                   	#footer {
       		            padding: 10px;
       		            text-align: center;
       	            }
                   	ul {
           	            margin: 0px;
                       	padding: 0px;
           	            list-style: none;
       	            }
       	            li {
       		            margin: 0px;
       	            }
       	            li ul {
       		            margin-left: 20px;
       	            }
       	            .lhead {
		                background: #ddd;
		                padding: 10px;
    	                margin: 10px 0px;
	                }
	                .ml-20{
	                    margin-left:20px;
	                }
	                .ml-40{
	                    margin-left:40px;
	                }	                
	                .ml-60{
	                    margin-left:60px;
	                }
	            	.lcount {
                		padding: 0px 10px;
                	}
                   	.lpage {
       		            border-bottom: #ddd 1px solid;
       		            padding: 5px;
       	            }
       	            .last-page {
       		            border: none;
       	            }
       	        </style>
            </head>
            <body>
                <div id="top" style="text-align:center;">
                    <h3>Generated Site Map - '.env('APP_URL').'</h3>
                </div>
                <div id="cont">
                    <label> Sites of project</label>
                <ul>';
        $config = config("asgard.ihelpers.config.configuration");
        // counters
        $counter = 0;
        $sitemapCounter = 0;
        $routesArray=array();
        // counters
        $xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?><urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">";
        $xml .='<url><loc>'.env('APP_URL').'</loc>
                <lastmod>'.date("Y-m-d H:i:s").'</lastmod>
                <priority>1.00</priority></url>';
        $html.='<li class="lhead"><a href="'.env('APP_URL').'/">'.env('APP_URL').'</a></li>';
        $counter++;
        $routesArray=array_merge($routesArray,array(['title'=>env('APP_URL'),'type'=>'Home','route'=>env('APP_URL')]));
        try{

            foreach($config as $configEntities){
                if ($counter == 100) {
                    //End of xml
                    $xml.="</urlset>";
                    // Create the sitemap xml
                    Storage::disk('publicmedia')->put('sitemap-'.$sitemapCounter.'.xml', $xml);
                    // reset the counter
                    $counter = 0;
                    // count generated sitemap
                    $sitemapCounter++;
                    //Init new xml text
                    $xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?><urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">";
                }//counter==100
                $raw="1=1";
                if(!empty($configEntities['params'])){
                    foreach($configEntities["params"] as $params){
                        $raw.=' and '.$params["param0"].$params["operator"].$params["param1"];
                    }
                }
                if(!empty($configEntities['orderBy']))
                    $entity=app($configEntities['Entity'])->whereRaw($raw)->orderBy($configEntities["orderBy"]["param0"],$configEntities["orderBy"]["param1"])->get();
                else
                    $entity=app($configEntities['Entity'])->whereRaw($raw)->get();
                if(!$configEntities['findTitle']){
                    //if not need find the tittle
                    foreach($entity as $key){
                        $xml .=
                            '<url>
                        <loc>'.str_replace($configEntities['replaceInUrl'],$key->slug,$configEntities['url']).'</loc>
                         <lastmod>'.date("Y-m-d H:i:s").'</lastmod>
                        <priority>1.00</priority>
                        </url>';
                        $counter++;
                        $routesArray=array_merge($routesArray,array(['title'=>$key->title,'type'=>$configEntities['name'],'route'=>str_replace($configEntities['replaceInUrl'],$key->slug,$configEntities['url'])]));
                        if ($counter == 100) {
                            //End of xml
                            $xml.="</urlset>";
                            // Create the sitemap xml
                            Storage::disk('publicmedia')->put('sitemap-'.$sitemapCounter.'.xml', $xml);
                            // reset the counter
                            $counter = 0;
                            // count generated sitemap
                            $sitemapCounter++;
                            //Init new xml text
                            $xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?><urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">";
                        }//counter==100
                        if(!empty($configEntities['relation'])) {
                            $relation=$this->queryRelationSiteMap($configEntities['relation'],$key->id);
                            $html.='<li class="lhead ml-20"><a href="'.str_replace($configEntities['replaceInUrl'],$key->slug,$configEntities['url']).'/">'.$key->title.'</a> / <span class="lcount">'.count($relation).' pages</span></li>';
                            foreach($relation as $key2) {
                                //dd($key,$relation);
                                if ($counter == 100) {
                                    //End of xml
                                    $xml.="</urlset>";
                                    // Create the sitemap xml
                                    Storage::disk('publicmedia')->put('sitemap-'.$sitemapCounter.'.xml', $xml);
                                    // reset the counter
                                    $counter = 0;
                                    // count generated sitemap
                                    $sitemapCounter++;
                                    //Init new xml text
                                    $xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?><urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">";
                                }//counter==100
                                $xml .='<url>
                                <loc>'.str_replace($configEntities['relation']['replaceInUrl'],$key2->slug,$configEntities['relation']['url']).'</loc>
                                <lastmod>'.date("Y-m-d H:i:s").'</lastmod>
                                <priority>1.00</priority>
                                </url>';
                                $counter++;
                                $routesArray=array_merge($routesArray,array(['title'=>$key2->title,'type'=>$configEntities['relation']['name'],'route'=>str_replace($configEntities['relation']['replaceInUrl'],$key2->slug,$configEntities['relation']['url'])]));
                                if(!empty($configEntities['relation']['relation'])) {
                                    $relation2=$this->queryRelationSiteMap($configEntities['relation']['relation'],$key2->id);
                                    $html.='<li class="lhead ml-40" ><a href="'.str_replace($configEntities['relation']['replaceInUrl'],$key2->slug,$configEntities['relation']['url']).'/">'.$key2->title.'</a> / <span class="lcount">'.count($relation2).' pages</span></li>';
                                    /* Test
                                    if($key2->id==131 || $key2->id=="131"){
                                        $entidad=app('\Modules\Icommerce\Entities\Product');
                                        dd($key2->id,$configEntities['relation']['relation'],$relation2,$entidad->with(['category','categories','parent','tags','manufacturer','product_discounts'])->whereStatus(Status::ENABLED)->where('date_available','<=',date('Y-m-d'))->take(10)->where('category_id',131)->orderBy('created_at', 'DESC')->get());
                                    }*/
                                    foreach($relation2 as $key3) {
                                        if ($counter == 100) {
                                            //End of xml
                                            $xml.="</urlset>";
                                            // Create the sitemap xml
                                            Storage::disk('publicmedia')->put('sitemap-'.$sitemapCounter.'.xml', $xml);
                                            // reset the counter
                                            $counter = 0;
                                            // count generated sitemap
                                            $sitemapCounter++;
                                            //Init new xml text
                                            $xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?><urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">";
                                        }//counter==100
                                        $xml .='<url>
                                            <loc>'.str_replace($configEntities['relation']['relation']['replaceInUrl'],$key3->slug,$configEntities['relation']['relation']['url']).'</loc>
                                            <lastmod>'.date("Y-m-d H:i:s").'</lastmod>
                                            <priority>1.00</priority>
                                            </url>';
                                        $html.='<li class="lpage ml-60" ><a href="'.str_replace($configEntities['relation']['relation']['replaceInUrl'],$key3->slug,$configEntities['relation']['relation']['url']).'/">'.$key3->title.'</a></li>';
                                        $counter++;
                                        $routesArray=array_merge($routesArray,array(['title'=>$key3->title,'type'=>$configEntities['relation']['relation']['name'],'route'=>str_replace($configEntities['relation']['relation']['replaceInUrl'],$key3->slug,$configEntities['relation']['relation']['url'])]));
                                    }//foreach relation
                                }//relation
                                else
                                    $html.='<li class="lhead ml-40" ><a href="'.str_replace($configEntities['relation']['replaceInUrl'],$key2->slug,$configEntities['relation']['url']).'/">'.$key2->title.'</a> / <span class="lcount">0 pages</span></li>';
                            }//foreach relation
                        }//relation
                        else
                            $html.='<li class="lhead ml-20"><a href="'.str_replace($configEntities['replaceInUrl'],$key->slug,$configEntities['url']).'/">'.$key->title.'</a> / <span class="lcount">0 pages</span></li>';
                    }//entity
                }else{
                    //Here execute dispatch to get title with curl, sending array of data and append to xml
                    //CreateSitemap::dispatch(str_replace($replaceInUrl,$categories[0]->slug,$url));
                }
            }//foreach config entities
            //dd($counter,$xml);
            if ($counter >0) {
                $xml.="</urlset>";
                Storage::disk('publicmedia')->put('sitemap-'.$sitemapCounter.'.xml', $xml);
                $sitemapCounter++;
                $xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?><sitemapindex xmlns=\"http://www.google.com/schemas/sitemap/0.84\">";
            }//counter>0
            for($i=0;$i<$sitemapCounter;$i++){
                $xml.='<sitemap><loc>'.env('APP_URL').'/sitemap-'.$i.'.xml</loc>
                   <lastmod>'.date("Y-m-d H:i:s").'</lastmod>
                   </sitemap>';
            }
            $xml.="</sitemapindex>";
            Storage::disk('publicmedia')->put('sitemap.xml', $xml);
            $content="User-agent: *\n";
            $content.="Disallow: \n";
            $content.="\nSitemap: ".env('APP_URL')."/sitemap.xml";
            Storage::disk('publicmedia')->put('robots.txt',$content);
            $html.='</ul></div><div id="footer">
                <a href="http://'.env('APP_URL').'/">'.env('APP_URL').'</a> - Site Map - Last Updated '.date("Y-m-d H:i:s").'
                </div></body></html>';
            Storage::disk('publicmedia')->put('sitemap.html', $html);
            return ['success'=>1,
                'QuantityOfSiteMap'=>$sitemapCounter,
                'QuantityOfUrl'=>count($routesArray),
                'Routes'=>$routesArray,
                'SiteHtmlPath'=>env('APP_URL').'/sitemap.html',
                'SiteXmlPath'=>env('APP_URL').'/sitemap.xml'];
        }catch(\Exception $e){
            return ['success'=>0,'message'=>$e->getMessage()];
        }
    }//siteMapPost()

}
