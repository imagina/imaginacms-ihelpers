<?php

namespace Modules\Ihelpers\Http\Controllers\Admin;

use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use Modules\Ihelpers\Other\ImResponseCache\ImResponseCache;
use Illuminate\Support\Facades\Storage;
use App;
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
  * This method create Array with Url's of project
  */
  public function makeArrayRoutes(){
    $routesCategories=[];//Routes with categories,sub-category and products
    $routesPages=[];//Routes of pages.
    $configuration = json_decode(json_encode(config("asgard.ihelpers.config.configurationv2")));
    foreach($configuration as $config){
      ///////If config name is Products
      if($config->Name=="Products"){
        $repository=app($config->RepositoryCategory);//Instance repository
        $categories=$repository->findParentCategories();//Get all Parent Categories
        foreach($categories as $category){
          $products=[];//Array products of category
          $productsSubCategory=[];//Array products of sub-category
          $subcategories=[];//sub-categories of category
          $subcategoriesQuery=$repository->whereFilters(json_decode(json_encode(['parent'=>$category->id])));//Get all subCategories of category.
          $elements=app($config->RepositoryProducts)->whereCategory($category->id);//Get products of category.
          //Construct array with title and url of products category
          foreach($elements as $product){
            $products=array_merge($products,[['title'=>$product->title,'url'=>env('APP_URL').'/'.$product->slug]]);
          }//foreach elements
          //Construct array with title and url of sub-category with products of sub-category.
          foreach($subcategoriesQuery as $subcategory){
            $elementsSubCategory=app($config->RepositoryProducts)->whereCategory($category->id);//Get all products of sub-category
            //Construct array with title and url of products of sub-category
            foreach($elementsSubCategory as $product){
              $productsSubCategory=array_merge($productsSubCategory,[['title'=>$product->title,'url'=>env('APP_URL').'/'.$product->slug]]);
            }//foreach elements
            $subcategories=array_merge($subcategories,[['title'=>$subcategory->title,'url'=>env('APP_URL').'/'.$subcategory->slug,'elements'=>$productsSubCategory]]);
          }//foreach subcategory
          //Push new category in array RoutesCategories
          $routesCategories=array_merge($routesCategories,[
            [
              'title'=>$category->title,
              'url'=>env('APP_URL').'/'.$category->slug,
              'elements'=>$products,
              'subCategory'=>$subcategories
            ]
          ]);
        }//foreach categories with children
      }//Entity Products
      ////////////////If config name is Pages
      else if($config->Name=="Pages"){
        $repository=app($config->RepositoryPages);//Instance Repository Pages
        $pages=$repository->paginate(10);//Get All Pages
        //Make array of pages routes
        foreach($pages as $page){
          $routesPages=array_merge($routesPages,[
            [
              'title'=>$page->translations[0]->title,
              'url'=>env('APP_URL').'/'.$page->translations[0]->slug
            ]
          ]);
        }//foreach pages
      }///If config name is Pages
      ////////////////If config name is Posts
      else if($config->Name=="Post"){

      }//if config name is post
    }//foreach config
    $allRoutes=[];//
    $allRoutes=array_merge($allRoutes,['Categories'=>$routesCategories,'Pages'=>$routesPages]);//Make Array with index Categories,Pages
    return json_decode(json_encode($allRoutes));
  }

  /*
  * This method return view to generate sitemap
  */
  public function sitemapGet(){
    return view('ihelpers::indexSiteMap');
  }

  //Make Sitemapv2
  public function siteMapPost(){
    try {
      $routes=$this->makeArrayRoutes();//Get array of routes
      $counter=0;//Counter Routes in xml
      $sitemapCounter=0;//Counter xml's file -- 100 routes per sitemap.xml
      $counterRoutes=0;//Counter Global Routes
      $b=0;//Flag of first url
      Storage::disk('publicmedia')->put('sitemap.json',json_encode($routes));//Save in disk publicmedia sitemap.json with all routes
      //Pages
      foreach($routes->Pages as $page){
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
        if($b==0){
          $xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?><urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">";
          $xml .='<url><loc>'.$page->url.'</loc>
          <lastmod>'.date("Y-m-d H:i:s").'</lastmod>
          <priority>1.00</priority></url>';
        }else{
          $xml .=
          '<url>
          <loc>'.$page->url.'</loc>
          <lastmod>'.date("Y-m-d H:i:s").'</lastmod>
          <priority>1.00</priority>
          </url>';
        }
        $counter++;
      }//foreach pages
      //Products
      foreach($routes->Categories as $category){
        if ($counter == 100) {
          //End of xml
          $xml.="</urlset>";
          // Create the sitemap xml
          Storage::disk('publicmedia')->put('sitemap-'.$sitemapCounter.'.xml', $xml);
          // reset the counter
          $counterRoutes=$counterRoutes+$counter;
          $counter = 0;
          // count generated sitemap
          $sitemapCounter++;
          //Init new xml text
          $xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?><urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">";
        }//counter==100
        $xml .=
        '<url>
        <loc>'.$category->url.'</loc>
        <lastmod>'.date("Y-m-d H:i:s").'</lastmod>
        <priority>1.00</priority>
        </url>';
        $counter++;
        //Products of category
        if(count($category->elements)>0){
          foreach($category->elements as $product){
            if ($counter == 100) {
              //End of xml
              $xml.="</urlset>";
              // Create the sitemap xml
              Storage::disk('publicmedia')->put('sitemap-'.$sitemapCounter.'.xml', $xml);
              // reset the counter
              $counterRoutes=$counterRoutes+$counter;
              $counter = 0;
              // count generated sitemap
              $sitemapCounter++;
              //Init new xml text
              $xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?><urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">";
            }//counter==100
            $xml .=
            '<url>
            <loc>'.$product->url.'</loc>
            <lastmod>'.date("Y-m-d H:i:s").'</lastmod>
            <priority>1.00</priority>
            </url>';
            $counter++;
          }//products of category
        }//If this category has products
        //Sub-categories of category
        if(count($category->subCategory)>0){
          foreach($category->subCategory as $subcategory){
            if ($counter == 100) {
              //End of xml
              $xml.="</urlset>";
              // Create the sitemap xml
              Storage::disk('publicmedia')->put('sitemap-'.$sitemapCounter.'.xml', $xml);
              // reset the counter
              $counterRoutes=$counterRoutes+$counter;
              $counter = 0;
              // count generated sitemap
              $sitemapCounter++;
              //Init new xml text
              $xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?><urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">";
            }//counter==100
            $xml .=
            '<url>
            <loc>'.$subcategory->url.'</loc>
            <lastmod>'.date("Y-m-d H:i:s").'</lastmod>
            <priority>1.00</priority>
            </url>';
            $counter++;
            //If this sub-category has products
            if(count($subcategory->elements)>0){
              foreach($subcategory->elements as $product){
                if ($counter == 100) {
                  //End of xml
                  $xml.="</urlset>";
                  // Create the sitemap xml
                  Storage::disk('publicmedia')->put('sitemap-'.$sitemapCounter.'.xml', $xml);
                  // reset the counter
                  $counterRoutes=$counterRoutes+$counter;
                  $counter = 0;
                  // count generated sitemap
                  $sitemapCounter++;
                  //Init new xml text
                  $xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?><urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">";
                }//counter==100
                $xml .=
                '<url>
                <loc>'.$product->url.'</loc>
                <lastmod>'.date("Y-m-d H:i:s").'</lastmod>
                <priority>1.00</priority>
                </url>';
                $counter++;
              }//products of category
            }//If this category has products
          }//products of category
        }//If this category has products
      }//Category
      //Make end of sitemap.xml
      //if counter >0 make other sitemap.xml
      if ($counter >0) {
        $xml.="</urlset>";
        Storage::disk('publicmedia')->put('sitemap-'.$sitemapCounter.'.xml', $xml);
        $sitemapCounter++;
        $counterRoutes=$counterRoutes+$counter;
        $xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?><sitemapindex xmlns=\"http://www.google.com/schemas/sitemap/0.84\">";
      }//counter>0
      //Make sitemap.xml index and relation with others sitemap
      for($i=0;$i<$sitemapCounter;$i++){
        $xml.='<sitemap><loc>'.env('APP_URL').'/sitemap-'.$i.'.xml</loc>
        <lastmod>'.date("Y-m-d H:i:s").'</lastmod>
        </sitemap>';
      }//for
      $xml.="</sitemapindex>";//close sitemapindex
      Storage::disk('publicmedia')->put('sitemap.xml', $xml);//Make sitemap.xml,
      $content="User-agent: *\n";
      $content.="Disallow: \n";
      $content.="\nSitemap: ".env('APP_URL')."/sitemap.xml";
      Storage::disk('publicmedia')->put('robots.txt',$content);//Make robots.txt
      return ['success'=>1,
      'QuantityOfSiteMap'=>$sitemapCounter,
      'QuantityOfUrl'=>$counterRoutes,
      'Routes'=>$routes,
      'SiteXmlPath'=>env('APP_URL').'/sitemap.xml'];
    }catch(\Exception $e){
      return ['success'=>0,'message'=>$e->getMessage()];
    }
  }//siteMapPost()

}
