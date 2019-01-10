@extends('weChat.layout.app')
@section('title', $title_name)
@section('css')
<link rel="stylesheet" href="{{asset('weui/css/all.css')}}"/>
@endsection()
@section('body')
    <div class="page__hd">
        <h1 class="page-hd-title text-center">
            {{$title_name}}
        </h1>
        <p><i class="weui-icon-info-circle"></i>请勿在油机旁使用手机！</p>

    </div>
            <div class="weui-cells weui-cells_form">
                <div class="weui-cell weui-cell_vcode">
                    <div class="weui-cell__hd">
                        <label class="weui-label">油枪：</label>
                    </div>
                    <div class="weui-cell__bd">
                        <input class="weui-input" id="nozzle_no" type="number" pattern="[0-9]*" placeholder="请输入油枪号">
                    </div>
                    <div class="weui-cell__ft">
                        <button class="weui-vcode-btn" id="gas_no" errflag="1"></button>
                    </div>
                </div>
                <div class="weui-cell weui-cell_vcode">
                    <div class="weui-cell__hd">
                        <label class="weui-label">金额：</label>
                    </div>
                    <div class="weui-cell__bd">

                        <input id="money" class="weui-input" type="number" minlength="{{$pay_limit['min_money']}}" maxlength="{{$pay_limit['max_money']}}" pattern="[1-10000]*"
                               placeholder="加油金额（{{$pay_limit['min_money']}}-{{$pay_limit['max_money']}}元）">
                    </div>
                    <div class="weui-cell__ft">
                        <button class="weui-vcode-btn">
                            <!-- 优惠 ¥0.5/L -->
                        </button>
                    </div>
                </div>
                <div id="tc" class="weui-grids">
                    <a href="javascript:;" class="weui-grid">

                        <p class="weui-grid__label"><span>100</span>元</p>
                    </a>
                    <a href="javascript:;" class="weui-grid">

                        <p class="weui-grid__label"><span>200</span>元</p>
                    </a>
                    <a href="javascript:;" class="weui-grid">

                        <p class="weui-grid__label"><span>300</span>元</p>
                    </a>
                    <a href="javascript:;" class="weui-grid">

                        <p class="weui-grid__label"><span>400</span>元</p>
                    </a>
                </div>
                <input type="hidden" id="pay_type" value="2">
                <div id="pay" class="page__bd page__bd_spacing">
                    <a href="javascript:;" class="weui-btn weui-btn_primary">支付：¥0.00 </a>
                </div>
            </div>
    @include('weChat.layout.copyright')

@endsection
@section('js')
    <script>
        const h = document.body.scrollHeight // 用onresize事件监控窗口或框架被调整大小，先把一开始的高度记录下来
        window.onresize = function () { // 如果当前窗口小于一开始记录的窗口高度，那就让当前窗口等于一开始窗口的高度
            if (document.body.scrollHeight < h) {
                document.body.style.height = h
            }
        }

        $(function () {
            //点击切换套餐
            $("#tc a").click(function (e) {
                money = $(this).find("span").text();
                // $("#tc a").css("background-color", "#fff");
                // $("#tc a p").css("color", "#000");
                // $(this).css("background-color", "#9ED99D");
                // $(this).find("p").css("color", "#fff");
                $("#pay a").text('支付：¥ ' + money + '元');
                $("#money").val(money);
                $("#tc").hide();
            })

            $('#money').on('input focus keyup', function (e) {
                onlyNumber(this);
                //实时监听金额输入框变化
                if ($('#money').val()) {
                    //输入框内容不为空
                    money = $('#money').val();
                    $("#pay a").text('支付：¥ ' + money + '元');
                    $("#tc").hide();
                } else {
                    //输入框内容为空
                    $("#pay a").text('支付：¥ 0元');
                    $("#tc").show();
                }
            })

            //验证数字
            function onlyNumber(obj) {
                //先把非数字的都替换掉，除了数字和.
                obj.value = obj.value.replace(/[^\d\.]/g, '');
                //必须保证第一个为数字而不是.
                obj.value = obj.value.replace(/^\./g, '0.');
                //保证只有出现一个.而没有多个.
                obj.value = obj.value.replace(/\.{2,}/g, '.');
                //保证.只出现一次，而不能出现两次以上
                obj.value = obj.value.replace('.', '$#$').replace(/\./g, '').replace('$#$', '.');
                //只能输入两个小数
                obj.value = obj.value.replace(/^(\-)*(\d+)\.(\d\d).*$/, '$1$2.$3');
            }


            var $iosDialog2 = $('#iosDialog2');

               //关闭
            $('#dialogs').on('click', function () {
                $('.js_dialog').fadeOut(200);
            });

            //油枪选择
            $('#nozzle_no').on('input blur keyup',function () {
                onlyNumber(this);
                //实时监听油枪输入框变化
                if ($('#nozzle_no').val()) {
                    nozzle_no = $('#nozzle_no').val();
                    $.ajax({
                        type: 'POST',
                        url: "{{url('wechat/payment/getGasNo')}}",
                        data: {nozzle_no:nozzle_no},
                        dataType: 'json',
                        success:function(res){
                            if(res.code == 1 ){
                                $('#gas_no').text('');
                                weui.topTips(res.msg);
                                $(this).attr('errflag',"1");
                                return false;
                            }else{
                                var gas_no = res.data.gas_no;
                                var gas_type = gas_no>0? '汽油':'柴油';
                                var d = gas_no+"# "+gas_type;
                                //console.log(data);
                                $(this).attr('errflag',"0");
                                $('#gas_no').text(d);
                                return true;
                            }
                        }
                });

                } else {
                    weui.topTips('油枪号不能为0');
                    return false;
                }
            });

            //支付
            $('#pay').on('click', function () {
                $iosDialog2.fadeIn(200);
                $result = checkPaymentPost();
                if(false === $result){
                    return false;
                }
                //统一下单，成功将调起支付
                var payData = $result;
                var checkurl =  "{{url('wechat/payment/add')}}";
                $.ajax({
                    type: 'POST',
                    url: checkurl,
                    data: payData,
                    dataType: 'json',
                    success:function(data){
                        console.log(data);
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

            //验证
            function checkPaymentPost() {

                var minMoney = "{{$pay_limit['min_money']}}";
                var maxMoney =  "{{$pay_limit['max_money']}}";
                minMoney = parseInt(minMoney);
                maxMoney = parseInt(maxMoney);
                var money = parseInt($('#money').val());
                var openid = "{{$openid}}";
                var payType = $("#pay_type").val();
                var nozzle_no  = $("#nozzle_no").val();
                if (money.length ==0) {
                    weui.topTips('金额不能为空');
                    return false;
                }
                //console.log(maxMoney);
                if (money < minMoney) {
                    weui.topTips('金额不能小于' + minMoney);
                    return false;
                }
                //console.log(money > maxMoney);
                if (money > maxMoney) {
                    weui.topTips('金额不能超过' + maxMoney);
                    return false;
                }

                //油枪errflag是否有误
                var errflag = $("#gas_no").attr("errflag");
                if(errflag == "1"){
                    weui.topTips("油枪号有误，请询问工作人员");
                    return false;
                }
                var pay = {
                    open_id: openid,
                    pay_fee: money*100,
                    pay_type:payType,
                    nozzle_no:nozzle_no
                };
                return pay;
            }

            //调用微信
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


            var oHeight = $(document).height();
            $(window).resize(function(){ //ios软键盘弹出不会触发resize事件
                if($(document).height() < oHeight){
                    $(".weui-footer").css("position","static");
                }else{
                    $(".weui-footer").css("position","absolute"); //adsolute或fixed，看你布局
                }
            });

        });
    </script>
@endsection


