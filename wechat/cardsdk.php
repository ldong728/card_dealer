<?php
include_once  $GLOBALS['mypath']. '/wechat/interfaceHandler.php';

class cardsdk{
//    private $appId;
//    private $appSecret;
//    private $mInterfaceHander=null;
    function __construct(){
//        $temp=new interfaceHandler(WEIXIN_ID);
//        $this->mInterfaceHander= $temp;
//        $this->appId = APP_ID;
//        $this->appSecret = APP_SECRET;
    }
    private function getCardApiTicket() {
        // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
        $data = json_decode(file_get_contents($GLOBALS['mypath'].'/tokens/card_api_ticket.dat'));
        if ($data->expire_time < time()) {
            interfaceHandler::getHandler()->reflashAccessToken();
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=ACCESS_TOKEN&type=wx_card";
            $temptoken=interfaceHandler::getHandler()->getByCurl($url);
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

    public function createCard($data){
        $re=interfaceHandler::getHandler()->postJsonByCurl('https://api.weixin.qq.com/card/create?access_token=ACCESS_TOKEN',$data);
        return json_decode($re,true);
    }
    public function getSignPackage($cardType) {
        $cardapiTicket = $this->getCardApiTicket();
        $timestamp = time();
        $nonceStr = $this->createNonceStr();
        $list=array($cardapiTicket,$timestamp,$nonceStr,APP_ID,$cardType);
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
    public function getCardExt($openid,$cardId,$extra) {
        $cardTicket=$this->getCardApiTicket();
        $timestamp=time();
        $extraLength=31-strlen($extra);
        $nonceStr= $extra.$this->createNonceStr($extraLength);
        $list=array($cardTicket,$timestamp,$nonceStr,$cardId,$openid);
        $sign=$this->sign($list);
        return array(
            'openid'=>$openid,
            'timestamp'=>$timestamp,
            'nonce_str'=>$nonceStr,
            'signature'=>$sign
        ) ;

    }
    public function requestCardList($status='CARD_STATUS_NOT_VERIFY',$offset=0,$count=20){

        $data=array('offset'=>$offset,'count'=>$count,'status_list'=>$status);
        $re=interfaceHandler::getHandler()->postArrayByCurl('https://api.weixin.qq.com/card/batchget?access_token=ACCESS_TOKEN',$data);
        return $re;
    }
    public function requestCardInf($cardId,$returnAsArray=false){
        $data=array('card_id'=>$cardId);
        $re=interfaceHandler::getHandler()->postArrayByCurl('https://api.weixin.qq.com/card/get?access_token=ACCESS_TOKEN',$data);
        if(!$returnAsArray)return $re;
        else return json_decode($re,true);
    }
    public function encodeCardCode($encryptCode){
        $encryptCode=array('encrypt_code'=>$encryptCode);
        $data=interfaceHandler::getHandler()->postArrayByCurl('https://api.weixin.qq.com/card/code/decrypt?access_token=ACCESS_TOKEN',$encryptCode);
        $data=json_decode($data,true);
        if(0==$data['errcode']){
            return $data['code'];
        }else{
            mylog('encrypt error:'.$data['errcode'].' :'.$data['errmsg']);
            return null;
        }
    }
    public function checkCard($cardCode,$check_detail=false){
        $data=array('code'=>$cardCode,'check_consume'=>$check_detail);
        $re=interfaceHandler::getHandler()->postArrayByCurl('https://api.weixin.qq.com/card/code/get?access_token=ACCESS_TOKEN',$data);
        $re=json_decode($re,true);
        if($check_detail)return $re;
        else{
            return $re['can_consume'];
        }
    }
    public function consumeCard($cardCode){
        if($this->checkCard($cardCode)){
            $data=array('code'=>$cardCode);
            $re=interfaceHandler::getHandler()->postArrayByCurl('https://api.weixin.qq.com/card/code/consume?access_token=ACCESS_TOKEN',$data);
            $re=json_decode($re,true);
            if(0==$re['errcode'])return true;
            else return false;
        }
        return false;
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