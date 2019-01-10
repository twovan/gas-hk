@extends('backend.layout.app')

@section('meta')
<!--<meta http-equiv="refresh" content="10">-->
@endsection



@section('content')

    <div class="row">
        <div class="col-sm-12">
            <div class="ibox widget-box float-e-margins">
                <div class="ibox-title no-borders">
                    <form role="form" class="form-inline">
                        <div class="form-group form-group-sm">
                            <label>订单号</label>
                            <input type="text" name="order_no" value="{{$request_params->order_no}}" class="form-control">
                        </div>



                        <div class="form-group form-group-sm">
                            <label>起始时间</label>
                            <input type="text" name="stime" id="form-search_stime"value="{{$request_params->stime}}"  class="form-control">
                            <label>截止时间</label>
                            <input type="text" name="etime" id="form-search_etime" value="{{$request_params->etime}}"  class="form-control">
                      </div>
                        <button class="btn btn-sm btn-primary" type="submit">查询</button>
                    </form>
                </div>

                <div class="ibox-title clearfix">
                    <h5>{{$list_title_name}}
                        <small>列表</small>
                    </h5>
                </div>
                <div class="ibox-content">

                    <table class="table table-stripped toggle-arrow-tiny" data-sort="false">
                        <thead>
                            <tr>
                                <th>下单时间</th>
                                <th>订单号</th>
                                <th>排队号</th>
                                <th>油枪号</th>
                                <th>类型</th>
                                <th>金额</th>
                                <th>获得积分</th>
                                <th>积分说明</th>
                                <th>用户手机号</th>
                                <th>会员卡号</th>
                                <th>状态</th>
                                <th>操作</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($lists as $list)
                            <tr>
                                <td>
                                    @if(!empty($list->order_at))
                                        {{date('Y-m-d H:i:s',$list->order_at)}}
                                    @else
                                        {{$list->created_at}}
                                    @endif
                                 </td>
                                <td>{{$list->order_no}}</td>
                                <td>{{$list->queue_no}}</td>
                                <td>{{$list->nozzle_no}}</td>
                                <td>{{config('params.pay_type')[$list->pay_type]}}</td>
                                <td>{{$list->pay_fee/100}} 元</td>
                                <td>{{$list->add_bonus}}</td>
                                <td>{{$list->record_bonus}}</td>
                                <td>@if(!empty($list->vipcard)) {{$list->vipcard->phone}} @endif</td>
                                <td>@if(!empty($list->vipcard)) {{$list->vipcard->code}} @endif</td>
                                <td>

                                    <span class="label {{ $list->status== 1 ? 'label-success': ' label-danger'}}">
                                     {{config('params.order_status')[$list->status]}}
                                    </span>
                                </td>
                                <td><button class="btn btn-default btn-xs" data-form="refresh"
                                    data-queue_no="{{$list->queue_no}}"
                                    data-order_no="{{$list->order_no}}"
                                    data-order_at="{{date('Ymd',$list->order_at)}}"

                                    >刷新</button></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{$lists->appends($request_params->all())->render()}}

                </div>

            </div>
        </div>
    </div>

@endsection

<!--    js    -->
@section('js_code')
<script>
    function sleep(delay) {
        var start = (new Date()).getTime();
        while ((new Date()).getTime() - start < delay) {
            continue;
        }
    }
    $(function () {
        $('[data-form="refresh"]').click(function () {
            var queue_no = $(this).attr('data-queue_no');
            var order_no = $(this).attr('data-order_no');
            var order_at = $(this).attr('data-order_at');

            //查询订单
            var orderData = {requestNo:queue_no,outTransNo:order_no,transDate:order_at};
            var postUrl = "{{url('Veecard/service/query')}}";
            $.ajax({
                type: 'POST',
                url: postUrl,
                data: orderData,
                dataType: 'json',
                success: function (data) {
                    if(data.code == 0){
                        swal({
                            title: '成功',
                            text: data.msg,
                            type: 'success',
                            confirmButtonText: '确定',
                        },function(){
                            window.location.reload();
                        });


                    }else{
                        console.log(data.data);
                        swal({
                            title: '出错啦',
                            text: data.msg+" ,回执状态:"+data.data.orderState,
                            type: 'error',
                            confirmButtonText: '确定',
                        },function(){
                            window.location.reload();
                        });
                        return false;
                    }
                },
                error: function (jqXHR) {
                    console.log("Error: " + jqXHR.status);
                }
            });
        });

        var var_search_stime= {
            elem: "#form-search_stime",
            format: "YYYY-MM-DD",
            min: "2010-01-01",
            max: "2037-12-31",
            istime: true,
            istoday: false,
            choose: function (datas) {
            }
        };

        var var_search_etime= {
            elem: "#form-search_etime",
            format: "YYYY-MM-DD",
            min: "2010-01-01",
            max: "2037-12-31",
            istime: true,
            istoday: false,
            choose: function (datas) {
            }
        };
        laydate(var_search_stime);
        laydate(var_search_etime);
    });

</script>
@endsection