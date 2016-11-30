<?php
include_once  $GLOBALS['mypath']. '/wechat/interfaceHandler.php';

class cardsdk{
    private $appId;
    private $appSecret;
    private $mInterfaceHander=null;
    function __construct(){
        mylog('start create');
        $temp=new interfaceHandler(WEIXIN_ID);
        $this->mInterfaceHander= $temp;
        $this->appId = APP_ID;
        $this->appSecret = APP_SECRET;
        mylog('construct_card');
    }

    private function getCardApiTicket() {
        // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
        $data = json_decode(file_get_contents($GLOBALS['mypath'].'/tokens/card_api_ticket.dat'));
        if ($data->expire_time < time()) {
            $this->mInterfaceHander->reflashAccessToken();
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=ACCESS_TOKEN&type=wx_card";
            $temptoken=$this->mInterfaceHander->getByCurl($url);
            $res = json_decode($temptoken);
            $ticket = $res->ticket;
            if ($ticket) {
                $data->expire_time = time() + 7000;
                $data->jsapi_ticket = $ticket;
                $data=json_encode($data);
                file_put_contents($GLOBALS['mypath'].'/tokens/card_api_ticket.dat',$data);
            }
        } else {
            $ticket = $data->jsapi_ticket;
        }
        return $ticket;
    }
    private function createNonceStr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    public function getSignPackage($cardType) {
        $cardapiTicket = $this->getCardApiTicket();
//        mylog('ticket: '.$cardapiTicket);
//        mylog('appid: '.$this->appId);
        $timestamp = time();
        $nonceStr = $this->createNonceStr();
//        $appId= $this->appId;
        $list=array($cardapiTicket,$timestamp,$nonceStr,$this->appId,$cardType);
//        sort($list,SORT_STRING);
//        $str='';
//        foreach ($list as $value) {
//            $str.=$value;
//        }
//        $cardSign=sha1($str);
        $cardSign=$this->sign($list);

        $signPackage = array(
            "nonceStr"  => $nonceStr,
            "timestamp" => $timestamp,
            "cardSign"=>$cardSign,
            "cardType"=>$cardType
        );
//        mylog(getArrayInf($signPackage));
        return $signPackage;
    }
    public function getCardExt($openId,$cardId) {
        $cardTicket=$this->getCardApiTicket();
        $timestamp=time();
        $nonceStr= $this->createNonceStr();
        $list=array($cardTicket,$timestamp,$nonceStr,$cardId,$openId);
//        sort($list,SORT_STRING);
//        $str='';
//        foreach ($list as $value) {
//            $str.=$value;
//        }
//        $sign=sha1($str);
        $sign=$this->sign($list);
        return array(
            'openid'=>$openId,
            'timestamp'=>$timestamp,
            'nonce_str'=>$nonceStr,
            'signature'=>$sign
        ) ;

    }
    public function requestCardList($status='CARD_STATUS_NOT_VERIFY',$offset=0,$count=20){

        $data=array('offset'=>$offset,'count'=>$count,'status_list'=>$status);
        $re=$this->mInterfaceHander->postArrayByCurl('https://api.weixin.qq.com/card/batchget?access_token=ACCESS_TOKEN',$data);
        return $re;
    }
    public function requestCardInf($cardId,$returnAsArray=false){
        $data=array('card_id'=>$cardId);
        $re=$this->mInterfaceHander->postArrayByCurl('https://api.weixin.qq.com/card/get?access_token=ACCESS_TOKEN',$data);
        if(!$returnAsArray)return $re;
        else return json_decode($re,true);
    }


    private function sign($list){
        sort($list,SORT_STRING);
        $str='';
        foreach ($list as $value) {
            $str.=$value;
        }
        return sha1($str);
    }


}