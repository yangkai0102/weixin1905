<?php

namespace App\Http\Controllers\WeiXin;

use App\Http\Controllers\Controller;
use App\WeiXin\P_wx_users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use GuzzleHttp\Client;
class WxController extends Controller
{
    protected $access_token;

    public function __construct()
    {
        $this->access_token=$this->getAccessToken();

    }
    function test(){
        echo $this->access_token;
    }

    public function getAccessToken(){
        $key='wx_access_token';
        $access_token=Redis::get($key);
        if($access_token){
            return $access_token;
        }
        $url='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.env('WX_APPID').'&secret='.env('WX_APPSECRET');
        $data_json=file_get_contents($url);
        $arr=json_decode($data_json,true);
        Redis::set($key,$arr['access_token']);
        Redis::expire($key,3600);
        return $arr['access_token'];

    }


    public function wx(){
        $token="2259b56f5898cd6192c50d338723";
//        echo "$token";die;
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $echostr=$_GET["echostr"];

        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            echo $echostr;
        }else{
            die('not ok');
        }
    }

    //接收微信推送事件
    public function receiv(){
        //将接收到的苏剧记录到日志文件
        $log_file="wx.log";
        $xml_str= file_get_contents("php://input");
        $data=date("Y-m-d h:i:s").">>>>>>>>\n".$xml_str."\n\n";
        file_put_contents($log_file,$data,FILE_APPEND);

        //处理xml数据
        $xml_obj=simplexml_load_string($xml_str);
        $event=$xml_obj->Event;
        $openid=$xml_obj->FromUserName;//获取用户的openid

        if($event=='subscribe'){

            $res=P_wx_users::where('openid',$openid)->first();
            if($res){
                $msg='欢迎回来';
                $xml='<xml>
  <ToUserName><![CDATA['.$openid.']]></ToUserName>
  <FromUserName><![CDATA['.$xml_obj->ToUserName.']]></FromUserName>
  <CreateTime>'.time().'</CreateTime>
  <MsgType><![CDATA[text]]></MsgType>
  <Content><![CDATA['.$msg.']]></Content>
</xml>';
                echo $xml;
            }else{
                //获取用户信息
                $url='https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$this->access_token.'&openid=' .$openid;
                $user_info=file_get_contents($url);
                $user=json_decode($user_info,true);//转换为数组
//                dump($user);
                file_put_contents('wx_user.log',$user_info,FILE_APPEND);

                $data=[
                    'openid'=>$openid,
                    'subscribe_time'=>$user['subscribe_time'],
                    'nickname'=>$user['nickname'],
                    'sex'=>$user['sex'],
                    'headimgurl'=>$user['headimgurl']

                ];
                $res=P_wx_users::create($data);
                $msg='谢谢关注';
                $xml='<xml>
  <ToUserName><![CDATA['.$openid.']]></ToUserName>
  <FromUserName><![CDATA['.$xml_obj->ToUserName.']]></FromUserName>
  <CreateTime>'.time().'</CreateTime>
  <MsgType><![CDATA[text]]></MsgType>
  <Content><![CDATA['.$msg.']]></Content>
</xml>';
                echo $xml;
            }
        }elseif ($event=='CLICK'){


            //请求第三方接口 获取天气
            $weather_api='https://free-api.heweather.net/s6/weather/now?location=beijing&key=84b0762b44004553957edc38e40862c9';
            $weather_info=file_get_contents($weather_api);

            $weather_info_arr=json_decode($weather_info,true);
//            print_r($weather_info_arr);die;
            $cond_txt=$weather_info_arr['HeWeather6'][0]['now']['cond_txt'];
            $tmp=$weather_info_arr['HeWeather6'][0]['now']['tmp'];
            $wind_dir=$weather_info_arr['HeWeather6'][0]['now']['wind_dir'];
            $msg=$cond_txt.' 温度：'.$tmp . '风向：'. $wind_dir;

            if($xml_obj->EventKey=='weather'){
                $response_weather='<xml><ToUserName><![CDATA['.$openid.']]></ToUserName>
<FromUserName><![CDATA['.$xml_obj->ToUserName.']]></FromUserName>
<CreateTime>'.time().'</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA['. date('Y-m-d H:i:s') . $msg.']]></Content>
</xml>';
            }
            echo $response_weather;
        }
        $msg_type=$xml_obj->MsgType;
        $touser = $xml_obj->FromUserName;     //接收消息的用户openid
        $fromuser=$xml_obj->ToUserName;
        $time=time();
        $media_id=$xml_obj->MediaId;
        if($msg_type=="text"){
            $content=date('Y-m-d h:i:s').$xml_obj->Content;
            $response_text='<xml>
                <ToUserName><![CDATA['.$touser.']]></ToUserName>
                <FromUserName><![CDATA['.$fromuser.']]></FromUserName>
                <CreateTime>'.$time.'</CreateTime>
                <MsgType><![CDATA[text]]></MsgType>
                <Content><![CDATA['.$content.']]></Content>
               </xml>';
            echo $response_text;
        }elseif ($msg_type=="image"){     //图片消息
            //下载图片
            $this->getMedia2($media_id,$msg_type);
            //回复图片
            $response_image='<xml>
                            <ToUserName><![CDATA['.$touser.']]></ToUserName>
                            <FromUserName><![CDATA['.$fromuser.']]></FromUserName>
                            <CreateTime>'.time().'</CreateTime>
                            <MsgType><![CDATA[image]]></MsgType>
                              <Image>
                                <MediaId><![CDATA['.$media_id.']]></MediaId>
                              </Image>                            
                              </xml>';
            echo $response_image;
        }elseif ($msg_type=="voice"){     //语音消息
            //下载语音
            $this->getMedia2($media_id,$msg_type);
            //回复语音
            $response_voice='<xml>
                            <ToUserName><![CDATA['.$touser.']]></ToUserName>
                            <FromUserName><![CDATA['.$fromuser.']]></FromUserName>
                            <CreateTime>'.time().'</CreateTime>
                            <MsgType><![CDATA[voice]]></MsgType>
                            <Voice>
                                <MediaId><![CDATA['.$media_id.']]></MediaId>
                            </Voice>
                            </xml>';
            echo $response_voice;
        }elseif ($msg_type=="video"){
            $this->getMedia2($media_id,$msg_type);

            $response='<xml>
<ToUserName><![CDATA['.$touser.']]></ToUserName>
<FromUserName><![CDATA['.$fromuser.']]></FromUserName>
<CreateTime>'.time().'</CreateTime>
<MsgType><![CDATA[video]]></MsgType>
<Video>
    <MediaId><![CDATA['.$media_id.']]></MediaId>
    <Title><![CDATA[测试]]></Title>
    <Description><![CDATA[不可描述]]></Description>
  </Video>
</xml>';
            echo $response;
        }
    }

    /*
    *
    */
    public function getUserInfo($access_token,$openid){
        $url='https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&openid='.$openid;
        $json_str=file_get_contents($url);
        $log_file='wx_user.log';
        file_put_contents($log_file,$json_str,FILE_APPEND);
    }

    public function getMedia(){
        $media_id='eKYowwuJEy0MKSPwhVkFlQygCWkaYsXMKM7YMtET4xlJvZu0yO50350Iy3CdX_UY';
        $url='https://api.weixin.qq.com/cgi-bin/media/get?access_token='.$this->access_token.'&media_id='.$media_id;
//        echo $url;
        $img=file_get_contents($url);
        file_put_contents('img.jpg',$img);

        echo "下载成功";
    }

    function getMedia2($media_id,$media_type){
        $url='https://api.weixin.qq.com/cgi-bin/media/get?access_token='.$this->access_token.'&media_id='.$media_id;
        //获取素材内容
        $client=new Client();
        $response=$client->request('GET',$url);
//        dd($response);die;
        $content_type=$response->getHeader('Content-disposition')[0];
        $extension=substr(trim($content_type,'"'),strpos($content_type,'.'));
//        echo "文件类型：".$extension;die;

        //获取文件扩展名
        $file_content=$response->getBody();

        $save_path='wx_media/';
        if($media_type=='image'){    //保存图片
            $file_name=date('Ymdhis').mt_rand(11111,99999).$extension;
            $save_path=$save_path.'imgs/'.$file_name;
        }elseif ($media_type=='voice'){   //保存语音
            $file_name=date('Ymdhis').mt_rand(11111,99999).$extension;
            $save_path=$save_path.'voice/'.$file_name;
        }elseif ($media_type=="video"){
            $file_name=date('Ymdhis').mt_rand(11111,99999).$extension;
            $save_path=$save_path.'video/'.$file_name;
        }
        file_put_contents($save_path,$file_content);

//        echo '文件保存成功：'.$save_path;
    }

    //自定义菜单
    public function createMenu(){

        $url='https://1905yangkai.comcto.com/vote';
        $redirect_uri=urlencode($url);         //授权后跳转页面
        //获取自定义菜单的接口
        $url='https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$this->access_token;
        $menu=[
            'button'=>[
                [
                    "type"=>"click",
                    "name"=>"获取天气",
                    "key"=>"weather"
                ],
                [
                    "type"=>"view",
                    "name"=>"投票",
                    "url" =>'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx112dc5198a6a8695&redirect_uri='.$redirect_uri.'&response_type=code&scope=snsapi_userinfo&state=yk0102#wechat_redirect'
                ],

            ]
        ];
        $menu_json=json_encode($menu,JSON_UNESCAPED_UNICODE);
        $client=new Client();
        $response=$client->request('POST',$url,[
            'body'=>$menu_json
        ]);

        echo $response->getBody();
    }

}
