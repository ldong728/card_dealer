<?php
/**
 * Created by PhpStorm.
 * User: godlee
 * Date: 2015/10/28
 * Time: 15:14
 */
include_once '../includePackage.php';
session_start();
mylog(getArrayInf($_SESSION));
if (isset($_SESSION['openid'])) {
    if (isset($_GET['module'])) {
        if ('card' == substr($_GET['module'], 0, 4)) {
            global $card;
            include_once '../wechat/cardsdk.php';
            $card = new cardsdk();
            $_GET['module']();
            exit;
        }

        switch ($_GET['module']) {
            case 'card_mall':
                $cardid = 'pubtTtwIDpuhWcvKtOW0e9Dj01Ig';
                for ($i = 0; $i < 3; $i++) {
                    $cardInfList[] = array('id' => $cardid, 'ext' => json_encode($card->getCardExt($_SESSION['openid'], $cardid)));
                }
                include 'view/get_card_view.html.php';
                break;
            case 'card_list':
//                $cardList=$card->requestCardList();
//                mylog($cardList);
//                $cardListArray=json_decode($cardList,true);
//                if(0==$cardListArray['errcode']){
//                    foreach ($cardListArray['card_id_list'] as $row) {
//                        $value[]=array('card_id'=>$row,'partner_id'=>'0','card_status'=>'CARD_STATUS_NOT_VERIFY');
//                    }
//                    if(isset($value))pdoBatchInsert('card_tbl',$value);
//                }
                include 'view/card_mall.html.php';
                break;
            case 'card_tempset':
                $cardIdList = pdoQuery('card_tbl', array('card_id'), null, null);
                foreach ($cardIdList as $row) {
                    $cardinf = $card->requestCardInf($row['card_id'], true);
//                    mylog(getArrayInf($cardinf));
                    if ($cardinf['errcode'] == 0) {
                        $cardtype = strtolower($cardinf['card']['card_type']);
//                        $endTime=

//                        mylog('title='.$cardinf['card'][$cardtype]['base_info']['title']);
                        pdoUpdate('card_tbl', array('card_title' => $cardinf['card'][$cardtype]['base_info']['title']), array('card_id' => $row['card_id']));

                    }
                }
                echo 'ok';
                break;
        }
        exit;
    }


    if (isset($_GET['get_buyed_card'])) {
//        $cardList=pdoQuery('')
    }
}
if (isset($_GET['action'])) {
    $_GET['action']();
}

if (isset($_GET['consume_online'])) {
    if (!isset($_SESSION['consume_inf'])&&isset($_GET['encrypt_code'])){
        include_once '../wechat/cardsdk.php';
        $cardsdk = new cardsdk();
        $encrypt_code = $_GET['encrypt_code'];
        $code = $cardsdk->encodeCardCode($encrypt_code);
        if ($code) {
            $openid = $_GET['openid'];
            $cardid = $_GET['card_id'];
            $_SESSION['consume_inf'] = array('openid' => $openid, 'cardid' => $cardid, 'code' => $code);
        }
    }
    if(!isset($_SESSION['consume_inf']['address'])){
        $cardrecorder = pdoQuery('card_user_tbl', array('card_user_id'), array('card_code' => $code, 'open_id' => $openid), ' limit 1');
        if ($cardrecorder->fetch()) {
            $addrQuery = pdoQuery('address_tbl', null, array('open_id' => $openid), ' order by dft_a desc limit 5');
            foreach ($addrQuery as $row) {
                if (1 == $row['dft_a']) {
                    $_SESSION['consume_inf']['address'] = $row;
                    include 'view/consume_online.html.php';
                    break;
                }
                $addrlist[] = $row;
            }
            if (!$addrlist) $addrlist = array();
            include "view/address.html.php";
            exit;
        }
    }
    include 'view/consume_online.html.php';
    exit;


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
                pdoInsert('order_record_tbl', array('order_id' => $orderId, 'event' => 1, 'pay_mode' => 1));
                $payChkArray = array(
                    'first' => array('value' => '您在卡券商城的网购订单已支付成功：'),
                    'orderno' => array('value' => $orderId, 'color' => '#0000ff'),
                    'amount' => array('value' => '￥' . $responseData['total_fee'] / 100, 'color' => '#0000ff'),
                    'remark' => array('value' => '商城即将安排发货，请留意物流通知')
                );
                $re = sendTemplateMsg($responseData['openid'], $template_key_order, '', $payChkArray);
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
function consume_online()
{
    include_once '../wechat/cardsdk.php';
    $cardsdk = new cardsdk();
    $encrypt_code = $_GET['encrypt_code'];
    $code = $cardsdk->encodeCardCode($encrypt_code);
    if ($code) {
        $openid = $_GET['openid'];
        $cardid = $_GET['card_id'];
        $_SESSION['consume_inf'] = array('openid' => $openid, 'cardid' => $cardid, 'code' => $code);
        $cardrecorder = pdoQuery('card_user_tbl', array('card_user_id'), array('card_code' => $code, 'open_id' => $openid), ' limit 1');
        if ($cardrecorder->fetch()) {
            $addrQuery = pdoQuery('address_tbl', null, array('open_id' => $openid), ' order by dft_a desc limit 5');
            foreach ($addrQuery as $row) {
                if (1 == $row['dft_a']) {
                    $_SESSION['consume_inf']['address'] = $row;
                    include '';
                    break;
                }
                $addrlist[] = $row;
            }
            if (!$addrlist) $addrlist = array();
            include "view/address.html.php";
        }
    }
}

function card_list()
{

}

function card_mall()
{
    mylog($_SESSION['user_level']);
    $cardList = pdoQuery('card_view', null, array('user_level' => $_SESSION['user_level']), null);
    include 'view/card_mall.html.php';
}

function card_order()
{
    $cardId = $_GET['card_id'];
//    mylog('card_order reached '.$cardId);
    include 'view/card_detail.html.php';
//    echo 'ok,card_id='.$cardId;
}

function card_bought_list()
{//进入已购买但未领取列表
    global $card;
    $query = pdoQuery('card_order_tbl', null, array('open_id' => $_SESSION['openid']), ' limit 5');

    for ($i = 0; $i < 5 && $row = $query->fetch(); $i++) {
        for ($j = 0; $j < ($row['number'] - $row['getted']) && $i < 5; $j++) {
            $cardInfList[] = array('id' => $row['card_id'], 'ext' => json_encode($card->getCardExt($_SESSION['openid'], $row['card_id'], (string)$row['card_order_id'])));//将14位长订单号放入随机字串中
            $i++;
        }
    }
    include 'view/get_card_view.html.php';

}




