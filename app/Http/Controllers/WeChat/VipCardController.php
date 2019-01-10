<?php

namespace App\Http\Controllers\WeChat;

use App\Models\User;
use App\Models\WeChat;
use App\Models\VipCard;
use Carbon\Carbon;
use Illuminate\Http\Request;
use EasyWeChat\Factory;
use Illuminate\Support\Facades\Validator;

class VipCardController extends BaseController
{
    protected $official_config;
    protected $card;

    public function __construct(){
        parent::__construct();
        $this->official_config = config('wechat.official_account.default');
        $app = Factory::officialAccount($this->official_config);
        $this->card = $app->card;
    }

    //会员卡货架建立
    public function index(){
        $banner = "http://gashk.twovan.cn/weui/images/card_banner.png";
        $pageTitle = '会员卡领取';
        $canShare  = true;

        $scene = 'SCENE_H5';
        $card_id = $this->getCradId();
        $cardList = [
            ['card_id' =>$card_id, 'thumb_url' => ''],
        ];

        $result = $this->card->createLandingPage($banner, $pageTitle, $canShare, $scene, $cardList);
        if($result['errcode']==0){
            $url = $result['url'];
           return  redirect($url);
        }
    }
    //会员卡激活
    public function active($info =[]){
        return $result = $this->card->member_card->activate($info);
    }

    /**
     * 获取vipcard 信息
     * @param $openid 用户openid
     */
    public function getVipInfo($openid){
        $vip_info = [];
        $result = VipCard::where('openid',$openid)->where('has_active',1)->first();
        if($result){
            $vip_info = $result;
        }
        return $vip_info;
    }


    /**
     * 更新微信会员卡信息(微信端)
     * @param array $info
     * $info['code'] 必须
     * @return bool
     */
    public function updateWxMemberInfo($info=[]){
        //必须非空数组
        if(!is_array($info) || empty($info)){
            return false;
        }

        //code必须
        if(empty($info['code'])){
            return false;
        }
        //card_id 必须
        if(!isset($info['card_id'])){
            return false;
        }
        $result = $this->card->member_card->updateUser($info);

        if($result['errcode'] != 0){
            return false;
        }
        return $result;
    }

    //获取会员卡信息,更新到数据库
    public function info($cardId,$code){
        $memberInfo =   $this->card->member_card->getUser($cardId, $code);
        if($memberInfo['errcode']==0) {
            $sex = '';
            if (!$memberInfo['sex']) {
                $sex = $memberInfo['sex'] == 'MALE' ? 0 : 1;
            }
            $userinfo = $memberInfo['user_info']['common_field_list'];
            $phone  ='';
            $username = '';
           foreach ($userinfo as  $key=>$value){
               if($value['name'] =='USER_FORM_INFO_FLAG_MOBILE'){
                   $phone = $value['value'];
               }
               if($value['name'] =='USER_FORM_INFO_FLAG_NAME'){
                   $username = $value['value'];
               }
            }
            $data = [
                'openid' => $memberInfo['openid'],
                'membership_number' => $memberInfo['membership_number'],
                'code' => $code,
                'card_id' => $cardId,
                'phone'=> $phone,
                'user_name' =>$username,
                'nickname' => empty($memberInfo['nickname']) ? '' : $memberInfo['nickname'],
                'sex' => $sex,
                'bonus' => empty($memberInfo['bonus']) ? 0 : $memberInfo['bonus'],
                'balance' => empty($memberInfo['balance']) ? 0 : $memberInfo['balance'],
                'user_card_status'=>$memberInfo['user_card_status'],
                'has_active'=>$memberInfo['has_active']=='true'?1:0,
            ];
           try{
               $result = VipCard::where('openid',$data['openid'])->get();
               if(collect($result)->isNotEmpty()){
                   VipCard::where('openid',$data['openid'])->update($data);
               }else{
                   VipCard::create($data);
               }

           }catch (\Exception $ex){
               \Log::info($ex->getMessage());

           }

        }
    }
    //会员卡激活字段设置
    public function setActivationForm(){
        $cardId = $this->getCradId();
        $settings = [
            'required_form' => [
                'common_field_id_list' => [
                    'USER_FORM_INFO_FLAG_NAME',
                    'USER_FORM_INFO_FLAG_MOBILE',
                ],
            ]
        ];
        if($cardId){
           return $result = $this->card->member_card->setActivationForm($cardId, $settings);
        }
    }


    //设置会员卡信息
    public function setVipCard( $attributes){
        $card_id = $this->getCradId();
        if(!is_null($card_id)){
            $type = 'MEMBER_CARD';
            return $this->card->update($card_id, $type, $attributes);
        }

    }


    //获取会员卡id
    public function getCradId(){
        $card_id = '';
        $membercard = $this->getVipCard();
        if(is_array($membercard)){
            $card_id = $membercard['base_info']['id'];
        }
        return $card_id;
    }

    //获取会员卡信息
    public function getVipCard(){
        $cardlist =  $this->lists();
        $membercard =[];
        foreach ($cardlist as $cardId){
            $cardInfo = $this->card->get($cardId);
           if(!empty($cardInfo)&&$cardInfo['card']['card_type']=='MEMBER_CARD'){
               $membercard = $cardInfo['card']['member_card'];
           }
        }
        return $membercard;
    }

    //获取所有的卡卷信息
    public function lists(){

        $result =  $this->card->list($offset = 0, $count = 10, $statusList = 'CARD_STATUS_DISPATCH');
        $card_id_list =[];
        if(collect($result)->isNotEmpty()){
            $card_id_list = $result['card_id_list'];
        }
        return $card_id_list;
    }


    //通过用户查找卡卷
    public  function getMycard(Request $request){
        $user = $request->getUser;
        $card_list =[];
        if($user){
            $openid = $user['openid'];
            $cardId =   $this->getCradId();
            $result =  $this->card->getUserCards($openid, $cardId);
            if($result['errcode'] == 0){
                $card_list =  $result['card_list'];
            }
        }

        return $card_list;
    }



}
