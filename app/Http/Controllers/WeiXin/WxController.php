<?php

namespace App\Http\Controllers\WeiXin;

use App\Http\Controllers\Controller;
use App\WeiXin\GuanLi;
use App\WeiXin\Message;
use App\WeiXin\P_wx_users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use GuzzleHttp\Client;
class WxController extends Controller
{
//    protected $access_token;
//
//    public function __construct()
//    {
//        $this->access_token=$this->getAccessToken();
//
//    }
//    function test(){
//        echo $this->access_token;
//    }
//
//    //微信网页授权登录
//    public function login(){
//        $code=$_GET['code'];
//        $url='https://api.weixin.qq.com/sns/oauth2/access_token?appid='.env('WX_APPID').'&secret='.env('WX_APPSECRET').'&code='.$code.'&grant_type=authorization_code';
//        $json_data=file_get_contents($url);
//        $data=json_decode($json_data,true);
//
//        //判断用户是否已存在
//        $openid=$data['openid'];
//
//        $u=P_wx_users::where(['openid'=>$openid])->first();
//        if($u){
//            $user_info=$u->toArray();
//        }else{
//            $user_info=$this->getUserInfo($data['access_token'],$data['openid']);
//            P_wx_users::insertGetId($user_info);
//        }
//        return redirect('/');
//
//    }
//
//    public function getAccessToken(){
//        $key='wx_access_token';
//        $access_token=Redis::get($key);
//        if($access_token){
//            return $access_token;
//        }
//        $url='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.env('WX_APPID').'&secret='.env('WX_APPSECRET');
//        $data_json=file_get_contents($url);
//        $arr=json_decode($data_json,true);
//        Redis::set($key,$arr['access_token']);
//        Redis::expire($key,3600);
//        return $arr['access_token'];
//    }
//
//
//
//
//    public function wx(){
//        $token="2259b56f5898cd6192c50d338723";
////        echo "$token";die;
//        $signature = $_GET["signature"];
//        $timestamp = $_GET["timestamp"];
//        $nonce = $_GET["nonce"];
//        $echostr=$_GET["echostr"];
//
//        $tmpArr = array($token, $timestamp, $nonce);
//        sort($tmpArr, SORT_STRING);
//        $tmpStr = implode( $tmpArr );
//        $tmpStr = sha1( $tmpStr );
//
//        if( $tmpStr == $signature ){
//            echo $echostr;
//        }else{
//            die('not ok');
//        }
//    }
//
//
//    //接收微信推送事件
//    public function receiv(){
//        //将接收到的数据记录到日志文件
//        $log_file="wx.log";
//        $xml_str= file_get_contents("php://input");
//        $data=date("Y-m-d h:i:s").">>>>>>>>\n".$xml_str."\n\n";
//        file_put_contents($log_file,$data,FILE_APPEND);
//
//        //处理xml数据
//        $xml_obj=simplexml_load_string($xml_str);
//        $event=$xml_obj->Event;        //获取类型
////        dd($event);
//        $openid=$xml_obj->FromUserName;//获取用户的openid
//
//        if($event=='subscribe'){
//
//            $res=P_wx_users::where('openid',$openid)->first();
//            if($res){
//                $msg='欢迎回来';
//                $xml='<xml>
//  <ToUserName><![CDATA['.$openid.']]></ToUserName>
//  <FromUserName><![CDATA['.$xml_obj->ToUserName.']]></FromUserName>
//  <CreateTime>'.time().'</CreateTime>
//  <MsgType><![CDATA[text]]></MsgType>
//  <Content><![CDATA['.$msg.']]></Content>
//</xml>';
//                echo $xml;
//            }else{
//                //获取用户信息
//                $url='https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$this->access_token.'&openid='.$openid;
//                $user_info=file_get_contents($url);
//                $user=json_decode($user_info,true);//转换为数组
//                file_put_contents('wx_user.log',$user_info,FILE_APPEND);
//
//                $data=[
//                    'openid'=>$openid,
//                    'subscribe_time'=>$user['subscribe_time'],
//                    'nickname'=>$user['nickname'],
//                    'sex'=>$user['sex'],
//                    'headimgurl'=>$user['headimgurl']
//
//                ];
//                $res=P_wx_users::create($data);
//                $msg='谢谢关注';
//                $xml='<xml>
//  <ToUserName><![CDATA['.$openid.']]></ToUserName>
//  <FromUserName><![CDATA['.$xml_obj->ToUserName.']]></FromUserName>
//  <CreateTime>'.time().'</CreateTime>
//  <MsgType><![CDATA[text]]></MsgType>
//  <Content><![CDATA['.$msg.']]></Content>
//</xml>';
//                echo $xml;
//            }
//        }elseif ($event=='CLICK'){
//            //请求第三方接口 获取天气
//            $weather_api='https://free-api.heweather.net/s6/weather/now?location=beijing&key=84b0762b44004553957edc38e40862c9';
//            $weather_info=file_get_contents($weather_api);
//
//            $weather_info_arr=json_decode($weather_info,true);
////            print_r($weather_info_arr);die;
//            $cond_txt=$weather_info_arr['HeWeather6'][0]['now']['cond_txt'];
//            $tmp=$weather_info_arr['HeWeather6'][0]['now']['tmp'];
//            $wind_dir=$weather_info_arr['HeWeather6'][0]['now']['wind_dir'];
//            $msg=$cond_txt.' 温度：'.$tmp . '风向：'. $wind_dir;
//
//            if($xml_obj->EventKey=='weather'){
//                $response_weather='<xml><ToUserName><![CDATA['.$openid.']]></ToUserName>
//                                        <FromUserName><![CDATA['.$xml_obj->ToUserName.']]></FromUserName>
//                                        <CreateTime>'.time().'</CreateTime>
//                                        <MsgType><![CDATA[text]]></MsgType>
//                                        <Content><![CDATA['. date('Y-m-d H:i:s') . $msg.']]></Content>
//                                   </xml>';
//                echo $response_weather;
//            }
//        }
//        $msg_type=$xml_obj->MsgType;
//        $touser = $xml_obj->FromUserName;     //接收消息的用户openid
//        $fromuser=$xml_obj->ToUserName;       //开发者公众号的ID
//        $time=time();
//        $media_id=$xml_obj->MediaId;
//        $content=date('Y-m-d h:i:s').$xml_obj->Content;
//
//
//        if($msg_type=="text"){
//            $response_text='<xml>
//                <ToUserName><![CDATA['.$touser.']]></ToUserName>
//                <FromUserName><![CDATA['.$fromuser.']]></FromUserName>
//                <CreateTime>'.$time.'</CreateTime>
//                <MsgType><![CDATA[text]]></MsgType>
//                <Content><![CDATA['.$content.']]></Content>
//               </xml>';
//
//
//
//            echo $response_text;
//        }elseif ($msg_type=="image"){     //图片消息
//            //下载图片
//            $this->getMedia2($media_id,$msg_type);
//
//            //回复图片
//            $response_image='<xml>
//                            <ToUserName><![CDATA['.$touser.']]></ToUserName>
//                            <FromUserName><![CDATA['.$fromuser.']]></FromUserName>
//                            <CreateTime>'.time().'</CreateTime>
//                            <MsgType><![CDATA[image]]></MsgType>
//                              <Image>
//                                <MediaId><![CDATA['.$media_id.']]></MediaId>
//                              </Image>
//                              </xml>';
//            echo $response_image;
//        }elseif ($msg_type=="voice"){     //语音消息
//            //下载语音
//            $this->getMedia2($media_id,$msg_type);
//            //回复语音
//            $response_voice='<xml>
//                            <ToUserName><![CDATA['.$touser.']]></ToUserName>
//                            <FromUserName><![CDATA['.$fromuser.']]></FromUserName>
//                            <CreateTime>'.time().'</CreateTime>
//                            <MsgType><![CDATA[voice]]></MsgType>
//                            <Voice>
//                                <MediaId><![CDATA['.$media_id.']]></MediaId>
//                            </Voice>
//                            </xml>';
//            echo $response_voice;
//        }elseif ($msg_type=="video"){
//            $this->getMedia2($media_id,$msg_type);
//
//            $response='<xml>
//<ToUserName><![CDATA['.$touser.']]></ToUserName>
//<FromUserName><![CDATA['.$fromuser.']]></FromUserName>
//<CreateTime>'.time().'</CreateTime>
//<MsgType><![CDATA[video]]></MsgType>
//<Video>
//    <MediaId><![CDATA['.$media_id.']]></MediaId>
//    <Title><![CDATA[测试]]></Title>
//    <Description><![CDATA[不可描述]]></Description>
//  </Video>
//</xml>';
//            echo $response;
//        }
//    }
//
//    /*
//    *
//    */
//    public function getUserInfo($access_token,$openid){
//        $url='https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&openid='.$openid;
//        $json_str=file_get_contents($url);
//        $log_file='wx_user.log';
//        file_put_contents($log_file,$json_str,FILE_APPEND);
//    }
//
//    public function getMedia(){
//        $media_id='eKYowwuJEy0MKSPwhVkFlQygCWkaYsXMKM7YMtET4xlJvZu0yO50350Iy3CdX_UY';
//        $url='https://api.weixin.qq.com/cgi-bin/media/get?access_token='.$this->access_token.'&media_id='.$media_id;
////        echo $url;
//        $img=file_get_contents($url);
//        file_put_contents('img.jpg',$img);
//
//        echo "下载成功";
//    }
//
//    function getMedia2($media_id,$media_type){
//        $url='https://api.weixin.qq.com/cgi-bin/media/get?access_token='.$this->access_token.'&media_id='.$media_id;
//        //获取素材内容
//        $client=new Client();
//        $response=$client->request('GET',$url);
////        dd($response);die;
//        $content_type=$response->getHeader('Content-disposition')[0];
//        $extension=substr(trim($content_type,'"'),strpos($content_type,'.'));
////        echo "文件类型：".$extension;die;
//
//        //获取文件扩展名
//        $file_content=$response->getBody();
//
//        $save_path='wx_media/';
//        if($media_type=='image'){    //保存图片
//            $file_name=date('Ymdhis').mt_rand(11111,99999).$extension;
//            $save_path=$save_path.'imgs/'.$file_name;
//
//        }elseif ($media_type=='voice'){   //保存语音
//            $file_name=date('Ymdhis').mt_rand(11111,99999).$extension;
//            $save_path=$save_path.'voice/'.$file_name;
//        }elseif ($media_type=="video"){
//            $file_name=date('Ymdhis').mt_rand(11111,99999).$extension;
//            $save_path=$save_path.'video/'.$file_name;
//        }
//        file_put_contents($save_path,$file_content);
//
////        echo '文件保存成功：'.$save_path;
//    }
//
//    //自定义菜单
//    public function createMenu(){
//
//        $url='http://1905yangkai.comcto.com/vote';
//        $redirect_uri=urlencode($url);         //授权后跳转页面
//
//        //微信商城
//        $shop_url='http://1905yangkai.comcto.com/wx/login';
//        $redirect_shop=urlencode($shop_url);
//
////        print_r($redirect_uri);
//        //获取自定义菜单的接口
//        $url='https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$this->access_token;
//        $menu=[
//            'button'=>[
//                [
//                    "type"=>"view",
//                    "name"=>"投票",
//                    "url" =>'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx112dc5198a6a8695&redirect_uri='.$redirect_uri.'&response_type=code&scope=snsapi_userinfo&state=yk0102#wechat_redirect'
//                ],
//                [
//                    "type"=>"view",
//                    "name"=>"微信商城",
//                    "url" =>'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx112dc5198a6a8695&redirect_uri='.$redirect_shop.'&response_type=code&scope=snsapi_userinfo&state=yk0102#wechat_redirect'
//                ],
//                [
//                    "name"=>"菜单",
//                    "sub_button"=>[
//                        [
//                            "type"=>'click',
//                            "name"=>"获取天气",
//                            "key"=>'weather'
//                        ],
//                        [
//                        "type"=>'click',
//                        "name"=>"获取个人信息",
//                        "key"=>'user'
//                    ]
//                    ],
//
//                ],
//            ]
//
//        ];
//        $menu_json=json_encode($menu,JSON_UNESCAPED_UNICODE);
//        $client=new Client();
//        $response=$client->request('POST',$url,[
//            'body'=>$menu_json
//        ]);
//        print_r($menu);
//        echo $response->getBody();    //接收  微信接口的响应数据
//    }
//
//    //access_token
//    function access_token(){
//        $key = 'wx_access_token';
//        Redis::del($key);
//        echo $this->getAccessToken();
//    }

    protected $access_token;

    public function __construct()
    {
        $this->access_token=$this->getAccessToken();

    }

    public function getAccessToken(){
        $key='wx_access_token';

        $access_token=Redis::get($key);
        if($access_token){
            return $access_token;
        }
        $url='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.env('WX_APPID').'&secret='.env('WX_APPSECRET');
        $data=file_get_contents($url);
        $data=json_decode($data,true);
        Redis::set($key,$data['access_token']);
        Redis::expire($key,3600);
        return $data['access_token'];
    }

    public function wx()
    {
        $token = '2259b56f5898cd6192c50d338723';
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $echostr = $_GET['echostr'];
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if ($tmpStr == $signature) {
            echo $echostr;
        } else {
            die('no');
        }

    }

    public function wxreceiv(){
        $log='wx.log';
        $xml_str=file_get_contents("php://input");
        file_put_contents($log,$xml_str,FILE_APPEND);
        $xml=simplexml_load_string($xml_str);

        $event=$xml->Event;
        $openid=$xml->FromUserName;

        if($event=='subscribe'){
            $url='https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$this->access_token.'&openid='.$openid.'&lang=zh_CN';
//            echo $url;die;
            $data_arr=file_get_contents($url);
            $info=json_decode($data_arr,true);
//            print_r($info);die;
            $data=[
                'openid'=>$openid,
                'nickname'=>$info['nickname'],
                'sex'=>$info['sex'],
                'headimgurl'=>$info['headimgurl'],
                'subscribe_time'=>time()
            ];

            P_wx_users::insertGetId($data);

            $msg='欢迎'.$data['nickname'].'进入选课系统';

                $xml='<xml>
  <ToUserName><![CDATA['.$openid.']]></ToUserName>
  <FromUserName><![CDATA['.$xml->ToUserName.']]></FromUserName>
  <CreateTime>'.time().'</CreateTime>
  <MsgType><![CDATA[text]]></MsgType>
  <Content><![CDATA['.$msg.']]></Content>
</xml>';
                echo $xml;
        }
        $msgType=$xml->MsgType;
        if($msgType=='CLICK'){
            if($xml->EventKey=='cache'){
                $res=GuanLi::where('openid',$openid)->get();
                if($res){
                    $msg='用户名：'.$info['nickname'].'第一节: '.$res['a1'].'第二节: '.$res['a2'].'第三节: '.$res['a3'].'第四节: '.$res['a4'];
                }else{
                    $msg='请先选择课程';
                }

                $xml_ke='<xml>
  <ToUserName><![CDATA['.$openid.']]></ToUserName>
  <FromUserName><![CDATA['.$xml->ToUserName.']]></FromUserName>
  <CreateTime>'.time().'</CreateTime>
  <MsgType><![CDATA[text]]></MsgType>
  <Content><![CDATA['.$msg.']]></Content>
</xml>';
                echo $xml_ke;
            }
        }

    }

    public function menu(){
        $guanli_url='http://1905yangkai.comcto.com/wx/guanli';
        $redirect_url=urlencode($guanli_url);
        $url='https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$this->access_token;
        $menu=[
                'button'=>[
                    [
                        'type'=>'click',
                        'name'=>'查课课程',
                        'key'=>'chake'
                    ],
                    [
                        'type'=>'view',
                        'name'=>'管理课程',
                        'url'=>'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.env('WX_APPID').'&redirect_uri='.$redirect_url.'&response_type=code&scope=snsapi_userinfo&state=1905kecheng#wechat_redirect'
                    ],
                ]
        ];
        $menu=json_encode($menu,JSON_UNESCAPED_UNICODE);
        $client=new Client();
        $response=$client->request('POST',$url,[
            'body'=>$menu
        ]);
        echo $response->getBody();
    }


public function guanli(){

        $code=$_GET['code'];
        $url='https://api.weixin.qq.com/sns/oauth2/access_token?appid='.env('WX_APPID').'&secret='.env('WX_APPSECRET').'&code='.$code.'&grant_type=authorization_code';
        $data=file_get_contents($url);
        $data_arr=json_decode($data,true);
        $openid=$data_arr['openid'];

        return view('guanli',['openid'=>$openid]);

}

public function guanlido(){
        $post=request()->except('_token');
//        dd($post);
        $res=GuanLi::insertGetId($post);
        if($res){
            return redirect('wx/index');
        }
}


public function access_token(){
        $key='wx_access_token';
        Redis::del($key);
}

}
