@extends('backend.layout.app')

@section('content')

    <div class="row">
        <div class="col-sm-12">
            <div class="ibox widget-box float-e-margins">
                <div class="ibox-title">
                    <form role="form" class="form-inline">
                        <div class="form-group form-group-sm">
                            <label>昵称：</label>
                            <input type="text" name="wx_name" value="{{$request_params->wx_name}}"
                                   class="form-control input-sm">
                        </div>
                        <button class="btn btn-sm btn-primary" type="submit">查询</button>
                        <div class="form-group form-group-sm">

                            <label>用户手机：</label>
                            <input type="text" name="phone" value="{{$request_params->phone}}"
                                   class="form-control input-sm">
                            <button class="btn btn-sm btn-primary" type="submit">查询</button>
                            <input type="hidden" name="_token" value="">

                        </div>
                        |


                    </form>
                </div>

                <div class="ibox-title clearfix">
                    <h5>{{$list_title_name}}
                        <small>列表</small>
                    </h5>
                    {{--<div class="pull-right">--}}
                    {{--<button class="btn btn-info btn-xs" data-form="add-model" data-toggle="modal" data-target="#formModal">添加</button>--}}
                    {{--</div>--}}
                </div>
                <div class="ibox-content">

                    <table class="table table-stripped toggle-arrow-tiny" data-sort="false">
                        <thead>
                        <tr>
                            <th>昵称</th>
                            <th>微信openid</th>
                            <th>会员卡号</th>
                            <th>手机号</th>
                            <th>身份</th>
                            <th>当前余额</th>
                            <th>剩余积分</th>
                            <th>状态</th>
                            <th class="text-right">操作</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($lists as $list)
                            <tr>
                                <td>{{$list->wx_name}}</td>
                                <td>{{$list->openid}}</td>
                                <td>@if(!empty($list->vipcard)) {{$list->vipcard->code}} @endif </td>
                                <td>@if(!empty($list->vipcard)) {{$list->vipcard->phone}} @endif </td>
                                <td>{{config('params')['vip_type'][$list->vip]}}</td>
                                <th>@if(!empty($list->vipcard)) {{$list->vipcard->balance}} @endif </th>
                                <th>@if(!empty($list->vipcard)) {{$list->vipcard->bonus}} @endif </th>
                                <td>{{config('params')['status'][$list->status]}}</td>
                                <td class="project-actions">

                                    <button class="btn btn-white btn-xs" data-form="edit-model" data-toggle="modal"
                                            data-target="#formModal"
                                            data-id="{{$list->id}}"
                                            data-phone="@if(!empty($list->vipcard)){{$list->vipcard->phone}}@endif"
                                            data-name="{{$list->wx_name}}"
                                            data-vip="{{$list->vip}}"

                                            data-status="{{$list->status}}"
                                    >修改
                                    </button>

                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    {{$lists->appends($request_params->all())->render()}}
                </div>

            </div>
        </div>
    </div>
    <div class="modal inmodal fade" id="formModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">{{$list_title_name}}编辑</h4>
                </div>
                <form method="post" id="form-validate-submit" class="form-horizontal m-t">
                    <div class="modal-body">
                        @include('backend.customer.form')
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white btn-sm" data-dismiss="modal">关闭</button>
                        <button type="submit" class="btn btn-primary btn-sm">保存</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
<!--    js    -->
@section('js_code')
    <script>
        $(function () {
            var form_url = '{{route('backend.user.save')}}';
            var index_url = window.location.href;
            var rules = [];
            subActionAjaxValidateForMime('#form-validate-submit', rules, form_url, index_url);
            $("#vip_at_id").hide();


            /**
             * 点击修改按钮触发的操作
             */
            $('[data-form="edit-model"]').click(function () {
                var id = $(this).attr('data-id');
                var phone = $(this).attr('data-phone');
                var name = $(this).attr('data-name');
                var type = $(this).attr('data-type');
                var vip = $(this).attr('data-vip');

                var status = $(this).attr('data-status');
                $('#form-id').val(id);
                $('#form-phone').val(phone);
                $('#form-name').val(name);
                $('#form-type').val(type);
                $('#form-vip').val(vip);

                $('#form-status').val(status).trigger('chosen:updated');
            });


        });



    </script>
@endsection