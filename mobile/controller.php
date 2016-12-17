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
if(isset($_SESSION['openid'])){
    if(isset($_GET['module'])){
        if('card'==substr($_GET['module'],0,4)){
            global $card;
            include_once '../wechat/cardsdk.php';
            $card=new cardsdk();
            $_GET['module']();
            exit;
        }
        switch($_GET['module']){
            case 'card_mall':
                $cardid='pubtTtwIDpuhWcvKtOW0e9Dj01Ig';
                for($i=0;$i<3;$i++){
                    $cardInfList[]=array('id'=>$cardid,'ext'=>json_encode($card->getCardExt($_SESSION['openid'],$cardid)));
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
                $cardIdList=pdoQuery('card_tbl',array('card_id'),null,null);
                foreach ($cardIdList as $row) {
                    $cardinf=$card->requestCardInf($row['card_id'],true);
//                    mylog(getArrayInf($cardinf));
                    if($cardinf['errcode']==0){
                        $cardtype=strtolower($cardinf['card']['card_type']);
//                        $endTime=

//                        mylog('title='.$cardinf['card'][$cardtype]['base_info']['title']);
                    pdoUpdate('card_tbl',array('card_title'=>$cardinf['card'][$cardtype]['base_info']['title']),array('card_id'=>$row['card_id']));

                    }
                }
                echo 'ok';
                break;
        }

        exit;
        }


    if(isset($_GET['get_buyed_card'])){
//        $cardList=pdoQuery('')
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

function card_list(){

}
function card_mall(){
    mylog($_SESSION['user_level']);
    $cardList=pdoQuery('card_view',null,array('user_level'=>$_SESSION['user_level']),null);
    include 'view/card_mall.html.php';
}
function card_order(){
    $cardId=$_GET['card_id'];
//    mylog('card_order reached '.$cardId);
    include 'view/card_detail.html.php';
//    echo 'ok,card_id='.$cardId;

}




