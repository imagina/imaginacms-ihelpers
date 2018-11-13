@extends('layouts.master')
@section('content-header')
  <h1>
      {{trans('ihelpers::common.sitemap.titleModule')}}
  </h1>
  <ol class="breadcrumb">
      <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
      <li class="active">{{trans('ihelpers::common.sitemap.titleModule')}}</li>
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
                  <div class="col-md-12 text-center" >
                      <button class="btn btn-success" id="generateButton" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> {{trans('ihelpers::common.sitemap.generatingSiteMap')}}" onclick="generateSiteMap()">{{trans('ihelpers::common.sitemap.generateSiteMap')}}</button>
                      <br>
                  </div>
                  <div id="success" style="display:none">
                      <div class="col-md-12 text-center">
                          <h3><strong><span id="message"></span></strong></h3>
                          <h4><strong>{{trans('ihelpers::common.sitemap.routesGenerated')}}:</strong> <span id="quantityroutes"></span></h4>
                          <h4><strong>{{trans('ihelpers::common.sitemap.xmlGenerated')}}:</strong> <span id="quantitysitemap"></span> </h4>
                          <h4><strong>{{trans('ihelpers::common.sitemap.siteMapPath')}}:</strong> <span id="xmlpath"></span> </h4>
                      </div>
                      <div class="col-md-12 table-responsive">
                          <table id="tableRoutes" class="table table-bordered table-striped table-sm">
                              <thead class="thead-light">
                                  <th>#</th>
                                  <th>{{trans('ihelpers::common.sitemap.table.title')}}</th>
                                  <th>{{trans('ihelpers::common.sitemap.table.route')}}</th>
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
            $('#generateButton').button('loading');
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
                        $('#message').html("{{trans('ihelpers::common.sitemap.messageSuccess')}}");
                        $('#quantityroutes').html(result['QuantityOfUrl']);
                        $('#quantitysitemap').html(result['QuantityOfSiteMap']);
                        $('#xmlpath').html('<a href="'+result["SiteXmlPath"]+'">'+result["SiteXmlPath"]+'</a>');
                        var count=0;
                        //For Pages
                        for(var i=0;i<result.Routes.length;i++){
                            //console.log(result['Routes'][i]);
                            // console.log(result.Routes.Pages[i].title);
                            count++;
                            htmlTbody+="<tr>";
                            htmlTbody+="<td>"+count+"</td>";
                            htmlTbody+="<td>"+result.Routes[i].title+"</td>";
                            htmlTbody+="<td>"+result.Routes[i].url+"</td>";
                            htmlTbody+="</tr>";
                        }//for Pages
                        if ( $.fn.DataTable.isDataTable('#tableRoutes') ) {
                          $('#tableRoutes').DataTable().destroy();
                        }
                        $('#tableRoutes tbody').html(htmlTbody);
                        $('#tableRoutes').DataTable();
                        // $('#generateButton').hide();
                        $('#generateButton').val("{{trans('ihelpers::common.sitemap.updateSiteMap')}}");
                        $('#success').show();
                    }else{
                        $('#generateButton').show();
                        $('#success').hide();
                        $('#tableRoutes tbody').html("");
                        $('#messageError').html(result.message);
                        $('#error').show();
                        //alert(result.message);
                    }
                    $('#generateButton').button('reset');
                },
                error:function(error){
                    console.log(error);
                }
            });//ajax
        }//function generateSiteMap
        // $( document ).ready(function() {
        //     console.log( "ready!" );
        // });

    </script>
@stop
