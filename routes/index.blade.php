
@extends('weChat.layout.app')
@section('title', $title_name)
@section('body')
    <div class="page-hd">
        <h3 class="page-hd-title text-center">
            会员充值
        </h3>
        <p></p>

    </div>
    <div class="page__bd page__bd_footer">
        <div class="weui-panel weui-panel_access">
            <form method="post" id="form-add_class">
                <div class="weui-cells weui-cells_form margin0">
                    <div class="weui-cell">
                        <div class="weui-cell__hd"><label class="weui-label">金额</label></div>
                        <div class="weui-cell__bd">
                            <input class="weui-input"  name="recharge" id="form-recharge" placeholder="请输入充值金额" tips="金额不正确">
                        </div>
                    </div>
                    <div class="weui-cell">
                        <div class="weui-cell__bd">
                            <label class="weui-media-box__desc">说明:充100送10元，充200送30元.</label>
                        </div>
                    </div>
                    <div class="clear paybar">
                        <img src="/weui/images/wepay_logo_green_200x200.png" style="vertical-align:middle;width:30px;" /> 微信支付
                    </div>

                <div class="weui-btn-area page__bd_footer">
                    <a class="weui-btn weui-btn_primary" href="javascript:" id="btn_save">立即充值</a>
                </div>
            </form>
        </div>

        @include('weChat.layout.copyright')
    </div>
@endsection
@section('js')
    <script>

        $(function () {

            var pattern = {
                    REG_RECHARGE:/^[0-9]+$/,
            };
            var btn_save = $('#btn_save');
            btn_save.click(function () {
                alert('ddd');
                weui.form.validate('#form-add_class', function (error) {
                    if (!error) {
                        var form_url = '{{url('wechat/recharge/add')}}';
                        var jump_url = '{{url('wechat/recharge/index')}}';

                        var data = $('#form-add_class').serialize();
                        subActionAjaxForMime(form_url, data, jump_url);
                    }
                }, {
                    regexp: pattern
                });
            });
        });

    </script>
@endsection


