<?php
include_once '../includePackage.php';
include_once $GLOBALS['mypath'] . '/wechat/interfaceHandler.php';
session_start();

//include 'view/wxpay.html.php';

//mylog('reach');
if (isset($_POST['prePay'])) {
//    mylog(getArrayInf($_SERVER));

    $query = pdoQuery('order_tbl', null, array('id' => $_POST['order_id'], 'stu' => '0'), ' limit 1');

    if ($inf = $query->fetch()) {
        if (0 == $inf['stu']) {


            $date = array();
            $date['appid'] = APP_ID;
            $date['mch_id'] = MCH_ID;
            $date['nonce_str'] = getRandStr(32);
            $date['body'] = '卡券官方商城订单';
            $date['spbill_create_ip'] = $_SERVER['REMOTE_ADDR'];
            $date['out_trade_no'] = $_POST['order_id'];
            $date['total_fee'] = $inf['total_fee'] * 100;
            $date['notify_url'] = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
            $date['trade_type'] = 'JSAPI';
            $date['openid'] = $inf['c_id'];
            $sign = makeSign($date, KEY);
            $date['sign'] = $sign;
            $xml = toXml($date);
            $handler = new interfaceHandler(WEIXIN_ID);
            $data = $handler->postByCurl('https://api.mch.weixin.qq.com/pay/unifiedorder', $xml);
//            mylog('prePayInf:' . $data);
            $dataArray = xmlToArray($data);
            $dataJson = json_encode($dataArray, JSON_UNESCAPED_UNICODE);
//            mylog('formated payInf' . getArrayInf($dataArray));
        }
        if ('SUCCESS' == $dataArray['return_code']) {
            if ('SUCCESS' == $dataArray['result_code']) {
                if (signVerify($dataArray)) {
                    $_SESSION['userKey']['package'] = 'prepay_id=' . $dataArray['prepay_id'];
                    echo 'ok';
                    exit;
                }
            } else {
                echo '支付失败，错误代码' . $dataArray['err_code'] . ':' . $dataArray['err_code'] . $dataArray['err_code_des'];
            }
        } else {
            echo $dataArray['return_msg'];
            exit;
        }

        echo $dataJson;
        exit;
    } else {
        echo getOrderStu($inf['stu']);
    }
}

if (isset($GLOBALS["HTTP_RAW_POST_DATA"])) {
    $tmpmsg = array(
        'touser' => $to,
        'template_id' => 'oMhzLlRCMJ_vXQKQL9Yx12DsG8fXlIUzcz0qz4kb9SI',
        'url' => 'http://www.qq.com',
        'data' => array(
            'first' => array('value' => '交易成功'),
            'product' => array('value' => '测试商品1'),
            'price' => array('value' => '1988.00'),
            'time' => array('value' => '1月9日16:00'),
            'remark' => array('value' => '欢迎再次选购'),
        )
    );
    $error = array('return_code' => 'SUCCESS', 'return_msg' => 'OK');
    $responseData = xmlToArray($GLOBALS["HTTP_RAW_POST_DATA"]);
//    mylog(getArrayInf($responseData));
    if ('SUCCESS' == $responseData['return_code']) {
        if ('SUCCESS' == $responseData['result_code']) {

            if (signVerify($responseData)) {
                include_once '../wechat/serveManager.php';
                $orderId = $responseData['out_trade_no'];
                pdoUpdate('order_tbl', array('stu' => "1"), array('id' => $orderId));
                pdoInsert('order_record_tbl',array('order_id'=>$orderId,'event'=>1,'pay_mode'=>1));
                $payChkArray=array(
                    'first'=>array('value'=>'您在卡券商城的网购订单已支付成功：'),
                    'orderno'=>array('value'=>$orderId,'color'=>'#0000ff'),
                    'amount'=>array('value'=>'￥'.$responseData['total_fee']/100,'color'=>'#0000ff'),
                    'remark'=>array('value'=>'商城即将安排发货，请留意物流通知')
                );
                $re=sendTemplateMsg($responseData['openid'],$template_key_order,'',$payChkArray);
                gainshare($orderId);
            } else {

            }
        } else {

        }
    } else {

    }
    echo toXml($error);
    exit;


}
