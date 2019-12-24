<?php

namespace App\Http\Controllers\Index;

use App\Http\Controllers\Controller;
use App\WeiXin\P_wx_users;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    //
    public function index(){
        $code=$_GET['code'];
        $data=$this->getAccessToken($code);

        //判断用户是否已存在
        $openid=$data['openid'];
        $u=P_wx_users::where(['openid'=>$openid])->first();
        if($u){
            $user_info=$u->toArray();
        }else{
            $user_info=$this->getUserInfo($data['access_token'],$data['openid']);
            P_wx_users::insertGetId($user_info);
        }

        //微信配置
        $nonceStr = Str::random(8);
        $signature = "";
        $wx_config = [
            'appId'     => env('WX_APPID'),
            'timestamp' => time(),
            'nonceStr'  => $nonceStr,
        ];
        $ticket = P_wx_users::getJsapiTicket();
        $url = $_SERVER['APP_URL'] . $_SERVER['REQUEST_URI'];;      //  当前url
        $jsapi_signature = P_wx_users::jsapiSign($ticket,$url,$wx_config);
        $wx_config['signature'] = $jsapi_signature;
        $data = [
            'u'         => $user_info,
            'wx_config' => $wx_config
        ];
        return view('index.index',$data);
    }

    public function getAccessToken($code){
        $url='https://api.weixin.qq.com/sns/oauth2/access_token?appid='.env('WX_APPID').'&secret='.env('WX_APPSECRET').'&code='.$code.'&grant_type=authorization_code';
        $json_data=file_get_contents($url);
        return json_decode($json_data,true);
    }

    public function getUserInfo($access_token,$openid){
        $url='https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
        $json_data=file_get_contents($url);
        $data=json_decode($json_data,true);
        if(isset($data['errcode'])){
            die('出错了  40001');
        }
        return $data;
    }
}
