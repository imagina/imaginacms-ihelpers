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
              <li class="breadcrumb-item "><a class="text-white" href="{{ URL::to('/') }}">Home</a></li>
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
  $loopCategory=5;
  $loopSubCategory=5;
@endphp

  <!--content-->
  <div class="page-content pb-5">
    <div class="container">
      <div class="row">

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

        <div class="col-12 col-md-6 col-lg-4">

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
                  <!--sitemap (second level) -->
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
                    <!--sitemap (second level) -->
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
        </div>

      </div>
    </div>
  </div>

</div>

@endsection
