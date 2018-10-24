@extends('layouts.master')

@section('content-header')
    <h1>
        SiteMap Generator
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li class="active">SiteMap Generator</li>
    </ol>
@stop
@section('content')
    <div class="row" id="sitemap">
        <div class="col-xs-12">
            <div class="box box-primary">
                <div class="box-body">
                    <div id="error" style="display:none" class="text-center">
                        <h3><strong>Error:</strong> <span id="messageError"></span></h3>
                    </div>
                    <div class="col-md-12 text-center" id="generateButton">
                        <button class="btn btn-success" onclick="generateSiteMap()">Generate Sitemap</button>
                        <br>
                    </div>
                    <div id="success" style="display:none">
                        <div class="col-md-12 text-center">
                            <h3><strong><span id="message"></span></strong></h3>
                            <h4><strong>Number of routes generated:</strong> <span id="quantityroutes"></span></h4>
                            <h4><strong>Number of sitemap.xml generated:</strong> <span id="quantitysitemap"></span> </h4>
                            <h4><strong>Sitemap xml path:</strong> <span id="xmlpath"></span> </h4>
                        </div>
                        <div class="col-md-12 table-responsive">
                            <table id="tableRoutes" class="table table-bordered table-striped table-sm">
                                <thead class="thead-light">
                                    <th>#</th>
                                    <th>Title</th>
                                    <th>Type</th>
                                    <th>Route</th>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- /.box -->
            </div>
        </div>
    </div>
@stop

@section('footer')
    <a data-toggle="modal" data-target="#keyboardShortcutsModal"><i class="fa fa-keyboard-o"></i></a> &nbsp;
@stop

@section('scripts')
    <script type="text/javascript">
        $('#success').hide();
        function generateSiteMap(){
            $.ajax({
                url:"{{url('/')}}"+'/backend/ihelpers/sitemapPost',
                type:'POST',
                headers:{'X-CSRF-TOKEN': "{{csrf_token()}}"},
                dataType:"json",
                data:{},
                success:function(result){
                    //console.log(result);
                    var htmlTbody="";
                    if(result.success==1){
                        $('#error').hide();
                        $('#message').html("The site map has been generated successfully.");
                        $('#quantityroutes').html(result['QuantityOfUrl']);
                        $('#quantitysitemap').html(result['QuantityOfSiteMap']);
                        $('#xmlpath').html('<a href="'+result["SiteXmlPath"]+'">'+result["SiteXmlPath"]+'</a>');
                        var count=0;
                        //For Pages
                        for(var i=0;i<result.Routes.Pages.length;i++){
                            //console.log(result['Routes'][i]);
                            // console.log(result.Routes.Pages[i].title);
                            count++;
                            htmlTbody+="<tr>";
                            htmlTbody+="<td>"+count+"</td>";
                            htmlTbody+="<td>"+result.Routes.Pages[i].title+"</td>";
                            htmlTbody+="<td>Page</td>";
                            htmlTbody+="<td>"+result.Routes.Pages[i].url+"</td>";
                            htmlTbody+="</tr>";
                        }//for Pages
                        //For Categories
                        for(var i=0;i<result.Routes.Categories.length;i++){
                            //console.log(result['Routes'][i]);
                            count++;
                            htmlTbody+="<tr>";
                            htmlTbody+="<td>"+count+"</td>";
                            htmlTbody+="<td>"+result.Routes.Categories[i].title+"</td>";
                            htmlTbody+="<td>Category</td>";
                            htmlTbody+="<td>"+result.Routes.Categories[i].url+"</td>";
                            htmlTbody+="</tr>";
                            for(var b=0;b<result.Routes.Categories[i].elements.length;b++){
                              count++;
                              htmlTbody+="<tr>";
                              htmlTbody+="<td>"+count+"</td>";
                              htmlTbody+="<td>"+result.Routes.Categories[i].elements[b].title+"</td>";
                              htmlTbody+="<td>Product</td>";
                              htmlTbody+="<td>"+result.Routes.Categories[i].elements[b].url+"</td>";
                              htmlTbody+="</tr>";
                            }//for
                            for(var b=0;b<result.Routes.Categories[i].subCategory.length;b++){
                              count++;
                              htmlTbody+="<tr>";
                              htmlTbody+="<td>"+count+"</td>";
                              htmlTbody+="<td>"+result.Routes.Categories[i].subCategory[b].title+"</td>";
                              htmlTbody+="<td>SubCategory</td>";
                              htmlTbody+="<td>"+result.Routes.Categories[i].subCategory[b].url+"</td>";
                              htmlTbody+="</tr>";
                              for(var s=0;s<result.Routes.Categories[i].subCategory[b].elements.length;s++){
                                count++;
                                htmlTbody+="<tr>";
                                htmlTbody+="<td>"+count+"</td>";
                                htmlTbody+="<td>"+result.Routes.Categories[i].subCategory[b].elements[s].title+"</td>";
                                htmlTbody+="<td>Product</td>";
                                htmlTbody+="<td>"+result.Routes.Categories[i].subCategory[b].elements[s].url+"</td>";
                                htmlTbody+="</tr>";
                              }//for
                            }//for
                        }//for Categories
                        $('#tableRoutes tbody').html(htmlTbody);
                        $('#tableRoutes').DataTable();
                        // $('#generateButton').hide();
                        $('#generateButton').val('Update SiteMap');
                        $('#success').show();
                    }else{
                        $('#generateButton').show();
                        $('#success').hide();
                        $('#tableRoutes tbody').html("");
                        $('#messageError').html(result.message);
                        $('#error').show();
                        //alert(result.message);
                    }

                },
                error:function(error){
                    console.log(error);
                }
            });//ajax
        }//function generateSiteMap
        $( document ).ready(function() {
            console.log( "ready!" );
        });

    </script>
@stop
