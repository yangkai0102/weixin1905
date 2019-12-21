<?php

namespace App\Http\Controllers\Crontab;

use App\Http\Controllers\Controller;
use App\WeiXin\P_wx_users;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class WxController extends Controller
{
    public function sendMsg(){
        $weather_api='https://free-api.heweather.net/s6/weather/now?location=beijing&key=84b0762b44004553957edc38e40862c9';
        $weather_info=file_get_contents($weather_api);

        $weather_info_arr=json_decode($weather_info,true);
//            print_r($weather_info_arr);die;
        $cond_txt=$weather_info_arr['HeWeather6'][0]['now']['cond_txt'];
        $tmp=$weather_info_arr['HeWeather6'][0]['now']['tmp'];
        $wind_dir=$weather_info_arr['HeWeather6'][0]['now']['wind_dir'];
        $msg=$cond_txt.' 温度：'.$tmp . '风向：'. $wind_dir;

        $key='wx_access_token';
        $access_token=Redis::get($key);
//        echo $access_token;
        $openid_arr=P_wx_users::select('openid')->get()->toArray();
//        print_r($openid_arr);die;
        $openid=array_column($openid_arr,'openid');
        $url='https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token='.$access_token;
//        echo $url;

        $msg1=date("Y-m-d H:i:s") . $msg;

        $data=[
            'touser'    =>    $openid,
            'msgtype'   =>    'text',
            'text'      =>    ['content'=>$msg1]
        ];

        $client=new Client();
        $response=$client->request('POST',$url,[
            'body'=>json_encode($data,JSON_UNESCAPED_UNICODE)
        ]);
        echo $response->getBody();
    }
}
