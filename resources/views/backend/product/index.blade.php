@extends('backend.layout.app')

@section('content')

    <div class="row">
        <div class="col-sm-12">
            <div class="ibox widget-box float-e-margins">

                <div class="ibox-title clearfix">
                    <h5>{{$list_title_name}}
                        <small>列表</small>
                    </h5>
                    <div class="pull-right">
                        <button class="btn btn-info btn-xs" data-form="add-model" data-toggle="modal" data-target="#formModal">添加</button>
                    </div>
                </div>
                <div class="ibox-content">

                    <table class="table table-stripped toggle-arrow-tiny" data-sort="false">
                        <thead>
                            <tr>
                                <th>油品型号</th>
                                <th>价格</th>
                                <th>类别</th>
                                <th>状态</th>
                                <th>备注</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lists as $list)
                            <tr>
                                <td>{{$list->gas_no}} #</td>
                                <td>{{$list->price/100}} 元</td>
                                <td>@if($list->type==2) 柴油 @else 汽油 @endif</td>
                                <td>{{config('params')['status'][$list->status]}}</td>
                                <td>{{$list->remark}}</td>
                                <td><button class="btn btn-info btn-xs" data-form="edit-model" data-toggle="modal" data-target="#formModal"
                                        data-id="{{$list->id}}"
                                        data-gas_no="{{$list->gas_no}}"
                                        data-price="{{$list->price/100}}"
                                        data-type="{{$list->type}}"
                                        data-status="{{$list->status}}"
                                        data-remark="{{$list->remark}}"
                                    >修改</button></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>

            </div>
        </div>
    </div>

    <div class="modal inmodal fade" id="formModal" tabindex="-1" role="dialog"  aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title"></h4>
                </div>
                <form method="post" id="form-validate-submit" class="form-horizontal m-t">
                    <div class="modal-body">
                        @include('backend.product.form')
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
        var form_url = '{{route('backend.product.save')}}';
        var index_url = window.location.href;
        var rules = [];
        var title = "{{$list_title_name}}";
        subActionAjaxValidateForMime('#form-validate-submit', rules, form_url, index_url);


        /**
         * 点击添加按钮触发的操作
         */
        $('[data-form="add-model"]').click(function () {
            var n = '';
            $('#form-id').val(n);
            $('#form-gas_no').val(n);
            $('#form-price').val(n);
            $('#form-type').val(n);
            $('#form-remark').val(n);
            $('#form-status').val(n);
            $('.modal-title').text(title+'添加');
            $('#form-option').val('add');

        });
        /**
         * 点击修改按钮触发的操作
         */
        $('[data-form="edit-model"]').click(function () {
            var id = $(this).attr('data-id');
            var gas_no = $(this).attr('data-gas_no');
            var price = $(this).attr('data-price');
            var type = $(this).attr('data-type');
            var status = $(this).attr('data-status');
            var remark = $(this).attr('data-remark');
            $('.modal-title').text(title+'编辑');
            $('#form-id').val(id);
            $('#form-gas_no').val(gas_no);
            $('#form-price').val(price);
            $('#form-type').val(type);
            $('#form-remark').val(remark);
            $('#form-status').val(status).trigger('chosen:updated');
            $('#form-option').val('edit');
        });
    });

</script>
@endsection