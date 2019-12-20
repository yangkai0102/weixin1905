<?php

namespace App\Admin\Controllers;

use App\WeiXin\GoodsModel;
use App\WeiXin\P_wx_users;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Redis;

class WxMsgController extends AdminController
{
    protected $title="微信群发消息";

    public function sendMsg(){
        $key='wx_access_token';
        $access_token=Redis::get($key);
//        echo $access_token;
        $openid_arr=P_wx_users::select('openid')->get();
//        print_r($openid_arr);die;
        $openid=array_column($openid_arr,'openid');
        $url='https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token='.$access_token;
//        echo $url;

        $msg=date("Y-m-d H:i:s") . '快要放假了，感觉咋样啊';

        $data=[
            'touser'    =>    $openid,
            'msgtype'   =>    'text',
            'text'      =>    ['content'=>$msg]
        ];

        $client=new Client();
        $response=$client->request('POST',$url,[
           'body'=>json_encode($data,JSON_UNESCAPED_UNICODE)
        ]);
        echo $response->getBody();
    }
}
