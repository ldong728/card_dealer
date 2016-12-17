<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/2
 * Time: 15:04
 */
include_once 'interfaceHandler.php';


class templateMsg {
    private $msg;
    public function __construct(){
    }

//    private function getIdList

    public function createTmplateMsg(array $msg){
        $temp=array();
        foreach ($msg as $k => $v) {
            if(!isset($v[1]))$v[1]='#000000';
            $temp[$k]=array('value'=>$v[0],'color'=>$v[1]);
        }
        $this->msg=$temp;
        return $this;
    }
    public function sendTmplateMsg($openid,$tmpId,$url){
        if(isset($this->msg)){
            $fullMsg=array(
                'touser'=>$openid,
                'template_id'=>$tmpId,
                'url'=>$url,
                'data'=>$this->msg
            );
            $response=interfaceHandler::getHandler()->postArrayByCurl('https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=ACCESS_TOKEN',$fullMsg);
            $back=json_decode($response,true);
            if($back['errcode']!=0){
                mylog($response);
            }
            return $response;
        }
    }
} 