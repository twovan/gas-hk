@extends('weChat.layout.app')
@section('title', $title_name)
@section('css')
    <link rel="stylesheet" href="{{asset('weui/css/all.css')}}"/>
@endsection()
@section('body')
    <div class="page-hd">
        <h1 class="page-hd-title text-center">
            会员充值
        </h1>
        <p></p>

    </div>
    <div class="page__bd page__bd_footer">
        <div class="weui-panel weui-panel_access">
            <form method="post" id="form-add_class">
                <div class="weui-cells weui-cells_form margin0">
                    <div class="weui-cell">
                        <div class="weui-cell__hd"><label class="weui-label">金额</label></div>
                        <div class="weui-cell__bd">
                            <input class="weui-input" type="number" name="recharge" id="form-recharge"
                                   placeholder="请输入充值金额" tips="金额不正确"/>
                            <input type="hidden" name="openid" id="wx_openid" value="{{$openid}}"/>
                        </div>
                    </div>
                    <div class="weui-cell">
                        <div class="weui-cell__bd">
                            <label class="weui-media-box__desc">说明:充值送好礼，详情进店面咨询.</label>
                        </div>
                    </div>
                    <div class="clear paybar">
                        <img src="/weui/images/wepay_logo_green_200x200.png" style="vertical-align:middle;width:30px;"/>
                        微信支付
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
            var btn_save = $('#btn_save');
            btn_save.on('click',function () {
                //检验数据
                $result = checkReChargePost();
                if(false === $result){
                    return false;
                }
                //统一下单，成功将调起支付
                var payData = $result;
                var checkurl =  "{{url('wechat/recharge/add')}}";
                $.ajax({
                    type: 'POST',
                    url: checkurl,
                    data: payData,
                    dataType: 'json',
                    success:function(data){
                     if(data.code==1){
                         weui.topTips(data.msg);
                         return false;
                     }else{
                         var response = data.data.payInfo;
                         response=$.parseJSON(response);
                         console.log(response);

                         if (typeof WeixinJSBridge == "undefined"){
                             if( document.addEventListener ){
                                 document.addEventListener('WeixinJSBridgeReady', function(){onBridgeReady(response)}, false);
                             }else if (document.attachEvent){
                                 document.attachEvent('WeixinJSBridgeReady',  function(){onBridgeReady(response)});
                                 document.attachEvent('onWeixinJSBridgeReady',  function(){onBridgeReady(response)});
                             }
                         }else{
                             onBridgeReady(response);
                         }

                     }
                    },
                    error:function(jqXHR){
                        console.log("Error: "+jqXHR.status);
                    }
                });
            });

            //检验提交
            function checkReChargePost() {
                var maxAmount = 500;
                var minAmount = 1;
                var recharge = $("#form-recharge").val();
                var openid = $("#wx_openid").val();
                if (recharge.length ==0) {
                    weui.topTips('金额不能为空');
                    return false;
                }
                if (!typeof(parseInt(recharge))) {
                    weui.topTips('只能为数字');
                    return false;
                }

                if (recharge < minAmount) {
                    weui.topTips('金额不能小于' + minAmount);
                    return false;
                }
                if (recharge > maxAmount) {
                    weui.topTips('金额不能超过' + maxAmount);
                    return false;
                }

                var pay = {
                    open_id: openid,
                    pay_fee: recharge*100,
                    pay_type:1,
                };
                return pay;
            }

        });

        function onBridgeReady(response){

            WeixinJSBridge.invoke(
                'getBrandWCPayRequest', {
                    "appId":response.appId,     //公众号名称，由商户传入
                    "timeStamp":response.timeStamp,         //时间戳，自1970年以来的秒数
                    "nonceStr":response.nonceStr, //随机串
                    "package":response.package,
                    "signType":response.signType,         //微信签名方式：
                    "paySign":response.paySign //微信签名
                },
                function(res){
                    if(res.err_msg == "get_brand_wcpay_request:ok" ){
                        // 使用以上方式判断前端返回,微信团队郑重提示：
                        //res.err_msg将在用户支付成功后返回ok，但并不保证它绝对可靠。
                        weui.topTips('支付成功');
                    }
                });
        }

        function IsJsonString(str) {
            try {
                JSON.parse(str);
            } catch (e) {
                return false;
            }
            return true;
        }


    </script>
@endsection


