@extends('layouts.master')
@section('content')
<style media="screen">
ul{
  list-style:none;
}
.text-color_dark{
  color:#34383d;
}
.page-title  {
  padding: 100px 0 104px;
}
.breadcrumb-item + .breadcrumb-item::before {
  font-family: FontAwesome;
  color: #ffffff;
  content: "\F105";
}
.icon_wrap_size_0 {
  width: 18px;
  height: 18px;
  line-height: 18px;
  font-size: 12px;
  margin: 4px 0 0 -28px;
  border-width: 1px;
  border-style: solid;
  text-align: center;
  color: #bfc4c8;
  float: left;
  display: block;
  border-radius:50%;
}
.icon_wrap_size_0  i {
  padding: 3px 7px;
}
.text-color_dark:hover .icon_wrap_size_0, .text-color_dark:hover .icon_wrap_size_0 i{
  color: #25858a;
}
#hoverColor:hover{
  color:#3056A0!important;
  cursor:pointer;
}
</style>
<div class="sitemap">
  <!--page title-->
  <div class="page-title bg-primary mb-5">
    <div class="container">
      <div class="row justify-content-center align-items-end">
        <div class="col-12">
          <h1 class="text-center text-white my-0">
            {{trans('ihelpers::common.sitemap.title')}}
          </h1>
        </div>
        <div class="col-auto text-center">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0">
              <li class="breadcrumb-item "><a class="text-white" href="{{ URL::to('/') }}">{{trans('ihelpers::common.sitemap.home')}}</a></li>
              <li class="breadcrumb-item text-white active" aria-current="page">{{trans('ihelpers::common.sitemap.title')}}</li>
            </ol>
          </nav>
        </div>
      </div>
    </div>
  </div>

@php
  $sitemapJson=json_decode(Storage::disk('publicmedia')->get('sitemap.json'));
  //dd($sitemapJson);
  function recursive($array,$categoryTitle){
    $html='<ul >';
    foreach($array as $subcategory){
      $html.='<li class="m_bottom_12 text-capitalize">';
      $temp='';
      if(isset($subcategory->children) || isset($subcategory->items)){
        $temp='title="'.trans('ihelpers::common.sitemap.seeMore').'" data-toggle="collapse" href="#IcChildren'.$subcategory->title.'" ';
        $temp.='role="button" aria-expanded="false" aria-controls="IcChildren'.$subcategory->title.'"';
      }
      $html.='<span class="icon_wrap_size_0"'.$temp.'>';
      $html.='<i class="fa fa-angle-right"></i>';
      $html.='</span>';
      $html.='<a href="'.$subcategory->url.'" class="text-color_dark d-inline-block font-weight-bold">';
      $html.=$subcategory->title;
      $html.='</a>';
      $html.='</li>';
      $html.='<div class="collapse" id="IcChildren'.$subcategory->title.'">';
      if(isset($subcategory->children)){
        $html.=recursive($subcategory->children,$subcategory->title);
      }
      if(isset($subcategory->items)){
        $html.='<ul class="mt-2" >';
        foreach($subcategory->items as $products){
          $html.='<li class="m_bottom_12 text-capitalize">';
          $html.='<a href="'.$products->url.'" class="text-color_dark d-inline-block">';
          $html.='<span class="icon_wrap_size_0 ">';
          $html.='<i class="fa fa-angle-right"></i>';
          $html.='</span>';
          $html.=$products->title;
          $html.='</a>';
          $html.='</li>';
        }//foreach items
        $html.='</ul>';
      }//if have items
      $html.="</div>";
    }//foreach
    $html.='</ul>';
    return $html;
  }
@endphp

  <!--content-->
  <div class="page-content pb-5">
    <div class="container">
      <div class="row">
        <!-- PAGES -->
        <div class="col-12 col-md-6 col-lg-4">
          <h5 class="font-weight-bold mb-3">{{trans('ihelpers::common.sitemap.pages')}}</h5>
          <ul>
            @foreach($sitemapJson->Pages as $page)
            <li class="mb-2">
              <a href="{{$page->url}}" class="text-color_dark d-inline-block">
                <span class="icon_wrap_size_0 ">
                  <i class="fa fa-angle-right"></i>
                </span>
                {{$page->title}}
              </a>
            </li>
            @endforeach
          </ul>
        </div>
        @if(count($sitemapJson->Manufacturers))
        <!-- BRANDS -->
        <div class="col-12 col-md-6 col-lg-4">
          <h5 class="font-weight-bold mb-2">{{trans('ihelpers::common.sitemap.brands')}}</h5>
          <ul>
            @foreach($sitemapJson->Manufacturers as $brand)
            <li class="mb-2">
              <a href="{{$brand->url}}" class="text-color_dark d-inline-block text-capitalize">
                <span class="icon_wrap_size_0 ">
                  <i class="fa fa-angle-right"></i>
                </span>
                {{$brand->title}}
              </a>
            </li>
            @endforeach
          </ul>
        </div>
        @endif
        <!-- POST -->
        @if(count($sitemapJson->Posts))
        <div class="col-12 col-md-6 col-lg-4">
          <h5 class="font-weight-bold mb-3">{{trans('ihelpers::common.sitemap.posts')}}</h5>
          <ul>
            @foreach($sitemapJson->Posts as $category)
              <li class="mb-2">
                <span class="icon_wrap_size_0" @if(isset($category->children) || isset($category->items))
                  title="{{trans('ihelpers::common.sitemap.seeMore')}}" data-toggle="collapse" href="#IcChildren{{$category->title}}"
                  role="button" aria-expanded="false" aria-controls="IcChildren{{$category->title}}"
                  @endif >
                  <i class="fa fa-angle-right"></i>
                </span>
                <a href="{{$category->url}}" class="text-color_dark d-inline-block">
                  {{$category->title}}
                </a>
              </li>
              <div class="collapse" id="IcChildren{{$category->title}}">
                @if(isset($category->children))
                  @php
                    echo recursive($category->children,$category->title)
                  @endphp
                @endif
                @if(isset($category->items))
                <ul class="mt-2" >
                  @foreach($category->items as $products)
                  <li class="m_bottom_12 text-capitalize">
                    <a href="{{$products->url}}" class="text-color_dark d-inline-block">
                      <span class="icon_wrap_size_0 ">
                        <i class="fa fa-angle-right"></i>
                      </span>
                      {{$products->title}}
                    </a>
                  </li>
                  @endforeach
                </ul>
                @endif
              </div>
            @endforeach
          </ul>
        </div>
        @endif
        <!-- PRODUCTS -->
        @if(count($sitemapJson->Products))
        <div class="col-12 col-md-6 col-lg-3">
          <h5 class="font-weight-bold mb-3">{{trans('ihelpers::common.sitemap.products')}}</h5>
          <ul>
            @foreach($sitemapJson->Products as $category)
              <li class="mb-2">
                <span class="icon_wrap_size_0" @if(isset($category->children))
                  title="{{trans('ihelpers::common.sitemap.seeMore')}}" data-toggle="collapse" href="#IcChildren{{$category->title}}"
                  role="button" aria-expanded="false" aria-controls="IcChildren{{$category->title}}"
                  @endif >
                  <i class="fa fa-angle-right"></i>
                </span>
                <a href="{{$category->url}}" class="text-color_dark d-inline-block font-weight-bold">
                  {{$category->title}}
                </a>
              </li>
              <div class="collapse" id="IcChildren{{$category->title}}">
              @if(isset($category->children))
                @php
                  echo recursive($category->children,$category->title)
                @endphp
              @endif
              @if(isset($category->items))
                <ul class="mt-2">
                  @foreach($category->items as $products)
                  <li class="m_bottom_12 text-capitalize">
                    <a href="{{$products->url}}" class="text-color_dark d-inline-block">
                      <span class="icon_wrap_size_0 ">
                        <i class="fa fa-angle-right"></i>
                      </span>
                      {{$products->title}}
                    </a>
                  </li>
                  @endforeach
                </ul>
              @endif
            </div>
            @endforeach
          </ul>
        </div>
@endif
        {{-- <div class="col-12 col-md-6 col-lg-4">

          <h5 class="font-weight-bold mb-3">{{trans('ihelpers::common.sitemap.products')}}</h5>
          <ul>
            @foreach($sitemapJson->Categories as $category)
              @php
                $loopCategory=$loop->iteration;
              @endphp
              <li class="mb-2 text-capitalize">
                <a class="text-color_dark d-inline-block"
                   @if(count($category->elements)>0) data-toggle="collapse" href="#collapseElements{{$loopCategory}}" role="button" aria-expanded="false" aria-controls="collapseElements{{$loopCategory}}"
                   @else href="{{$category->url}}"
                   @endif>
				           <span class="icon_wrap_size_0 ">
			        <i class="fa fa-angle-right"></i>
                  </span>
                  {{$category->title}}
                </a>
                @foreach($category->elements as $productCategory)
                  <ul class="collapse mt-2" id="collapseElements{{$loopCategory}}">
                    <li class="m_bottom_12 text-capitalize">
                      <a href="{{$productCategory->url}}" class="text-color_dark d-inline-block">
					               <span class="icon_wrap_size_0 ">
			                     <i class="fa fa-angle-right"></i>
                        </span>
                        {{$productCategory->title}}
                      </a>
                    </li>
                  </ul>
                @endforeach
                @foreach($category->subCategory as $subcategory)
                <li class="mb-2 text-capitalize ml-3">
                  <a class="text-color_dark d-inline-block"
                     @if(count($subcategory->elements)>0) data-toggle="collapse" href="#collapse{{$subcategory->title}}" role="button" aria-expanded="false" aria-controls="collapse{{$subcategory->title}}"
                     @else href="{{$subcategory->url}}"
                     @endif>
                    <span class="icon_wrap_size_0">
			          <i class="fa fa-angle-right"></i>
                    </span>
                    {{$subcategory->title}}
                  </a>
                  @foreach($subcategory->elements as $productSubCategory)
                    <ul class="collapse mt-2" id="collapse{{$subcategory->title}}">
                        <li class="mb-2 text-capitalize">
                          <a href="{{$productSubCategory->url}}" class="text-color_dark d-inline-block">
                            <span class="icon_wrap_size_0 ">
                              <i class="fa fa-angle-right"></i>
                            </span>
                            {{$productSubCategory->title}}
                          </a>
                        </li>
                      </ul>
                  @endforeach
                </li>
                @endforeach
              </li>
            @endforeach
          </ul>
        </div> --}}

      </div>
    </div>
  </div>

</div>

@endsection
