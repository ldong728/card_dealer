<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/30
 * Time: 13:14
 */
include_once $GLOBALS['mypath'] . '/wechat/interfaceHandler.php';

class wxPay
{
    private $out_trade_no, $total_fee;
    private $notify_url, $spbill_create_ip, $openid;
    private $body = '卡券官方商城订单';
    private $trade_type = 'JSAPI';
    private $package;
    private $errmsg=null;


    public function __construct($openid)
    {
        $this->openid = $openid;
        $this->notify_url = 'http://' . $_SERVER['HTTP_HOST'] . DOMAIN . '/mobile/controller.php';
        $this->spbill_create_ip = $_SERVER['REMOTE_ADDR'];
    }

    public function setAttr($key, $value)
    {
        $this->$key = $value;
        return $this;
    }
    public function setOrderId($orderId){
        $this->setAttr('out_trade_no',$orderId);
        return $this;
    }
    public function setTotalFee($totalFee){
        $this->setAttr('total_fee',$totalFee);
        return $this;
    }
    public function prePay()
    {
        if (isset($this->out_trade_no) && isset($this->total_fee)) {
            $date = array();
            $date['appid'] = APP_ID;
            $date['mch_id'] = MCH_ID;
            $date['nonce_str'] = getRandStr(32);
            $date['body'] = $this->body;
            $date['spbill_create_ip'] = $this->spbill_create_ip;
            $date['out_trade_no'] = $this->out_trade_no;
            $date['total_fee'] = $this->total_fee;
            $date['notify_url'] = $this->notify_url;
            $date['trade_type'] = 'JSAPI';
            $date['openid'] = $this->openid;
            $sign = makeSign($date, KEY);
            $date['sign'] = $sign;
            $xml = toXml($date);
            $data = interfaceHandler::getHandler()->postByCurl('https://api.mch.weixin.qq.com/pay/unifiedorder', $xml);
            $dataArray = xmlToArray($data);
//            $dataJson = json_encode($dataArray, JSON_UNESCAPED_UNICODE);
            if ('SUCCESS' == $dataArray['return_code']) {
                if ('SUCCESS' == $dataArray['result_code']) {
                    if (signVerify($dataArray)) {
                        $this->errmsg=null;
                        $this->package='prepay_id='.$dataArray['prepay_id'];
                    }
                } else {
                    $this->errmsg=  '支付失败，错误代码' . $dataArray['err_code'] . ':' . $dataArray['err_code'] . $dataArray['err_code_des'];
                }
            } else {
                $this->errmsg= $dataArray['return_msg'];
            }
        }
        return $this;
    }
    public function getH5Package(){
        if(!$this->errmsg){
            $data=array('appId'=>APP_ID,'timestamp'=>time(),'nonceStr'=>getRandStr(32),'package'=>$this->package,'signType'=>'MD5');
            $sign = makeSign($data, KEY);
            $data['paySign'] = $sign;
            return $data;
        }else{
            return $this->errmsg;
//            $data=array('appId'=>APP_ID,'timestamp'=>time(),'nonceStr'=>getRandStr(32),'package'=>'prepay_id=abcdefg','signType'=>'MD5');
//            echo ajaxBack($data);
        }
    }
} 