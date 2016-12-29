<?php
/**
 * Created by PhpStorm.
 * User: godlee
 * Date: 2015/10/28
 * Time: 15:14
 */
include_once '../includePackage.php';
include_once '../wechat/cardsdk.php';

session_start();
if (isset($_SESSION['openid'])) {
    if (isset($_GET['module'])) {
        if ('card' == substr($_GET['module'], 0, 4)) {
            global $card;
            $card = new cardsdk();
        }
        $_GET['module']();
        exit;

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
}
if(isset($_SESSION['openid'])&&isset($_SESSION['operator'])){
    if(isset($_GET['operator'])){
        $func='op_'.$_GET['operator'];
        $func();
        exit;
    }

}
if (isset($_GET['action'])) {
    $func='action_'.$_GET['action'];
    $func();
}
if (isset($_GET['consume_online'])) {
    action_consume_online();
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

function login(){
    $phone=$_POST['userphone'];
    $password=md5($_POST['password']);
    $logMode=$_GET['log_mode'];
    $where=array('phone'=>$phone,'password'=>$password);
    $query=pdoQuery('partner_operator_tbl',array('id','partner_id'),$where,' limit 1');
//    mylog();
    if($_SESSION['operator']=$query->fetch()){
//        mylog();
        pdoUpdate('partner_operator_tbl',array('open_id'=>$_SESSION['openid']),$where,' limit 1');
        header('location: controller.php?module='.$logMode);
        exit;
    }
}
function action_consume_online()
{
    if (!isset($_SESSION['consume_inf'])&&isset($_GET['encrypt_code'])){
        $cardsdk = new cardsdk();
        $encrypt_code = $_GET['encrypt_code'];
        $code = $cardsdk->encodeCardCode($encrypt_code);
        if ($code) {
            $openid = $_GET['openid'];
            $cardid = $_GET['card_id'];
            $_SESSION['consume_inf'] = array('openid' => $openid, 'cardid' => $cardid, 'code' => $code);
        }
    }else if(!isset($_SESSION['consume_inf'])&&!isset($_GET['encrypt_code'])){
        //TODO session 过期后的处理方法
    }
    if(isset($_GET['address_change'])){
        address_select($_SESSION['consume_inf']['openid'],'consume_online');
        exit;
    }
    if(isset($_GET['address_id'])){
        $where=array('id'=>$_GET['address_id']);
    }else{
        $where=array('dft_a'=>1,'open_id'=>$_SESSION['consume_inf']['openid']);
    }
    $address=pdoQuery('address_tbl',null,$where,' limit 1');
    if($address=$address->fetch()) {
        $address=$address;
    }else{
        address_select($_SESSION['consume_inf']['openid'],'consume_online');
        exit;
    }
    $cardDetail=array();
    include 'view/consume_detail.html.php';
    exit;
}
function action_consume_confirm()
{
    if (isset($_SESSION['consume_inf'])) {
        $address_id = $_GET['address_id'];
        $code = $_SESSION['consume_inf']['code'];
        cardConsume($code, 'online', $address_id);
    }


}
function address_select($openid,$action){
    $addrQuery = pdoQuery('address_tbl', null, array('open_id' => $openid), ' order by dft_a desc limit 5');
    foreach ($addrQuery as $row) {
        $addrlist[] = $row;
    }
    if (!$addrlist){
        mylog('no address');
        $addrlist = array();
    }
    include "view/address.html.php";
}
function card_list()
{

}
function card_mall()
{
    $cardList = pdoQuery('card_view', null, array('user_level' => $_SESSION['user_level']), 'and end_time>now()');
    include 'view/card_mall.html.php';
}
function card_order()
{
    $cardId = $_GET['card_id'];
    $cardInf=pdoQuery('card_view',null,array('card_id'=>$cardId,'user_level'=>$_SESSION['user_level']),' limit 1');
    $cardInf=$cardInf->fetch();
    include 'view/card_detail.html.php';
}
function card_bought_list()
{//进入已购买但未领取列表
    global $card;
    $query = pdoQuery('card_order_tbl', null, array('open_id' => $_SESSION['openid']), ' and getted<number limit 5');
    $i=0;
    while(($row=$query->fetch())&& $i<5){
        for ($j = 0; $j < ($row['number'] - $row['getted']) && $i < 5; $j++) {
            mylog('$i: '.$i.'  $j: '.$j);
            $cardInfList[] = array('id' => $row['card_id'], 'ext' => json_encode($card->getCardExt($_SESSION['openid'], $row['card_id'], (string)$row['card_order_id'])));//将14位长订单号放入随机字串中
            $i++;
        }
    }
    if(isset($cardInfList))include 'view/get_card_view.html.php';
    else{
        $errmsg='暂无可领取卡券';
        include('view/error.html.php');
    }

}
function card_getCardList(){

}
function operator(){
    if(isset($_SESSION['operator'])&&null!=$_SESSION['operator']){

        include 'view/operator_menu.html.php';
    }else{
        $operatorQuery=pdoQuery('partner_operator_tbl',array('id','partner_id'),array('open_id'=>$_SESSION['openid']),' limit 1');
        if($_SESSION['operator']=$operatorQuery->fetch()){
//            mylog(getArrayInf($_SESSION));
            include 'view/operator_menu.html.php';
        }else{
            mylog('login');
            $logMode='operator';
            include 'view/login.html.php';
        }
    }
}
function op_scan(){
    if(isset($_SESSION['operator'])){
        include 'view/qr_scan.html.php';
    }else{
        $logMode='operator';
        include 'view/login.html.php';
    }
}




