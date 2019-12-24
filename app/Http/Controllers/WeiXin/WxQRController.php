<?php

namespace App\Http\Controllers\WeiXin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
class WxQRController extends Controller
{
    function qrcode(){
        $scene_id=$_GET['scene'];
        $key='wx_access_token';
        $access_token=Redis::get($key);
        $url='https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$access_token;

    }
}
