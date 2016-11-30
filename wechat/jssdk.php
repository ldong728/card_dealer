<?php
require_once 'interfaceHandler.php';
class JSSDK {

  private $appId;
  private $appSecret;
  private $mInterfaceHander=null;
    public $weixinId;

  public function __construct($weixinId) {
      $this->weixinId=$weixinId;
      $temp=new interfaceHandler($weixinId);
    $this->mInterfaceHander= $temp;
    $this->appId = APP_ID;
    $this->appSecret = APP_SECRET;
  }

  public function getSignPackage() {
    $jsapiTicket = $this->getJsApiTicket();

    // 注意 URL 一定要动态获取，不能 hardcode.
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $timestamp = time();
    $nonceStr = $this->createNonceStr();

    // 这里参数的顺序要按照 key 值 ASCII 码升序排序
    $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

    $signature = sha1($string);

    $signPackage = array(
      "appId"     => $this->appId,
      "nonceStr"  => $nonceStr,
      "timestamp" => $timestamp,
      "url"       => $url,
      "signature" => $signature,
      "rawString" => $string
    );
    return $signPackage; 
  }

  private function createNonceStr($length = 16) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $str = "";
    for ($i = 0; $i < $length; $i++) {
      $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
  }

  private function getJsApiTicket() {
    // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
    $data = json_decode(file_get_contents($GLOBALS['mypath'].'/tokens/jsapi_ticket.dat'));
    if ($data->expire_time < time()) {
//        wxlog('ticket timeout: '.(time()-$data->expire_time));
      // 如果是企业号用以下 URL 获取 ticket
      // $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
        $this->mInterfaceHander->reflashAccessToken();
      $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=ACCESS_TOKEN";
      $temptoken=$this->mInterfaceHander->getByCurl($url);
//        mylog($temptoken);
      $res = json_decode($temptoken);
      $ticket = $res->ticket;
      if ($ticket) {
        $data->expire_time = time() + 7000;
        $data->jsapi_ticket = $ticket;
          $data=json_encode($data);
        file_put_contents($GLOBALS['mypath'].'/tokens/jsapi_ticket.dat',$data);
      }
//      mylog('get jsapiTicketOnLine');
    } else {
      $ticket = $data->jsapi_ticket;
//      wxlog('get jsapiTicket from file');
    }

    return $ticket;
  }


}

