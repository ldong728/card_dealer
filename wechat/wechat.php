<?php
/**
 * Created by PhpStorm.
 * User: godlee
 * Date: 2015/4/17
 * Time: 8:31
 */

//define("TOKEN", "godlee");

class wechat
{
    public $weixinId='';
    public $msg;
    private $isReplied=false;
    public function __construct($wxid){
        $this->weixinId=$wxid;
}

    public function valid()  //微信服务器验证配置用
    {
//        wxlog('valid start');
        if (isset($_GET['echostr'])) {
//            mylog($_GET['signature']);
//            mylog($_GET['timestamp']);
//            mylog($_GET['nonce']);
//            mylog($_GET['echostr']);
            $echoStr = $_GET["echostr"];
            if ($this->checkSignature()) {
//                mylog($echoStr);
                echo $echoStr;
                exit;
            }
        }
    }
    public function receiverFilter()
    {
        if(isset($GLOBALS["HTTP_RAW_POST_DATA"])) {
            $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
            if (!empty($postStr)) {
                libxml_disable_entity_loader(true);
                $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $msg['from'] = (string)$postObj->FromUserName;
                $msg['me'] = (string)$postObj->ToUserName;
                $msg['content'] = (string)trim($postObj->Content);
                foreach ($postObj->children() as $child) {
                    $msg[$child->getName()] = (string)$child;
                }
                $this->msg = $msg;
                return $msg;
            }
        }else{
            return array();
        }
    }

    public function prepareTextMsg($sentTo, $me, $content)
    {
        $con=array('MsgType'=>'text','Content'=>$content);
        $resultStr=$this->prepareMsg($con);
        return $resultStr;
    }
    public function toKFMsg($kf_account=null){
        $str =isset($kf_account)? array('MsgType'=>'transfer_customer_service','TransInfo'=>array('KfAccount'=>$kf_account))
            :array('MsgType'=>'transfer_customer_service');
        $resultStr=$this->prepareMsg($str);
//        mylog($resultStr);
        echo $resultStr;
        return $resultStr;
    }

    public function replyMsg(array $content){
        if(!$this->isReplied) {
            $replyStr = $this->prepareMsg($content);
//            mylog($replyStr);
            echo $replyStr;
            $this->isReplied=true;
        }
    }

    private function prepareMsg(array $content){
        $textTpl = '<?xml version="1.0" encoding="utf-8"?><xml>
							<ToUserName><![CDATA['.$this->msg['FromUserName'].']]></ToUserName>
							<FromUserName><![CDATA['.$this->msg['ToUserName'].']]></FromUserName>
							<CreateTime>'.time().'</CreateTime>
							</xml>';
        $xml=new SimpleXMLElement($textTpl);
        $this->arrayToXml($xml,$content);
        $replyStr=$xml->asXML();
        return $replyStr;
    }
    private function arrayToXml(SimpleXMLElement $xml, array $array){
        foreach ($array as $k => $v) {
            if(is_array($v)){
                $this->arrayToXml($xml->addChild($k),$v);
            }else{
                $xml->addChild($k,$v);
            }
        }
        return $xml;

    }
    public function replytext($response){
        if(!$this->isReplied) {
            $content = array('MsgType' => 'text', 'Content' => $response);
            $this->replyMsg($content);
            $this->isReplied=true;
        }
    }

    public function prepareNewsMsg($sentTo,$me,$newsJson){
        $time=time();
        $data=json_decode($newsJson,true);
        $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[news]]></MsgType>
							<ArticleCount>".count($data['news_item'])."</ArticleCount>
                            <Articles>
							";
        $textTitle = sprintf($textTpl, $sentTo, $me, $time);

        $textTpl="<item>
                        <Title><![CDATA[%s]]></Title>
                        <Description><![CDATA[%s]]></Description>
                        <PicUrl><![CDATA[%s]]></PicUrl>
                        <Url><![CDATA[%s]]></Url>
                        </item>
                        ";
        foreach ($data['news_item'] as $row) {
            $title=(isset($row['title'])? $row['title'] : '无标题');
            $description=(isset($row['digest'])? $row['digest']:'');
            $picUrl=(isset($row['cover_url'])?$row['cover_url']: '');
            $url=(isset($row['url'])?$row['url']:'');
            $url=(isset($row['content_url'])?$row['content_url'] : $url);
            $content=sprintf($textTpl,$title,$description,$picUrl,$url);
            $textTitle=$textTitle.$content;
        }
        $textTitle=$textTitle."</Articles></xml>";
//        wxlog($textTitle);
        return $textTitle;

    }


    private function checkSignature()
    {

        // you must define TOKEN by yourself
        if (!defined("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }
//        $query=pdoQuery('user_tbl',array('token'),array('weixin_id'=>$this->weixinId),'limit 1');
//        $row=$query->fetch();
        $token=TOKEN;
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
//        mylog($tmpStr);
        $tmpStr = sha1($tmpStr);
//        mylog($tmpStr);
        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }

    public function validMsg()
    {
        if ($this->checkSignature()) return true;
        else return false;
    }

}