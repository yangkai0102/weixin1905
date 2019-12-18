<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
class VoteController extends Controller
{
    //
    public function index(){

        $code=$_GET['code'];
        //获取access_token
        $data=$this->getAccessToken($code);

        //获取用户信息
        $user_info=$this->getUserInfo($data['access_token'],$data['openid']);

        //处理业务逻辑
        $openid=$user_info['openid'];
        $redis_key='vote';
        $number=Redis::incr($redis_key);
        echo "投票成功，当前投票数：".$number;
    }

    //TODO 根据code获取access_token
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
