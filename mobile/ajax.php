<?php
/**
 * Created by PhpStorm.
 * User: godlee
 * Date: 2015/10/26
 * Time: 13:09
 */
include_once '../includePackage.php';;
session_start();
if(isset($_SESSION['openid'])){
        if('pay'==substr($_POST['module'],0,3)){
            include_once '../wechat/wxPay.php';
        }
        switch($_POST['module']){
            default:
                $_POST['module']();
                break;
        }

}
function pay_pre(){
    mylog('pay_pre');
    $cardId=$_POST['card_id'];
    $number=$_POST['number'];
    $priceInf=pdoQuery('card_price_tbl',array('price'),array('card_id'=>$cardId,'user_level'=>$_SESSION['user_level']),' limit 1');
    $price=$priceInf->fetch()['price'];
    $order_id=time().rand(1000,9999);
    pdoTransReady();
    try{
        pdoInsert('card_order_tbl',array('card_order_id'=>$order_id,'card_id'=>$cardId,'open_id'=>$_SESSION['openid'],'price'=>$price,'total_price'=>$number*$price,'number'=>$number));
        pdoInsert('order_recorder_tbl',array('order_id'=>$order_id,'status'=>0));
        pdoCommit();
    }catch(PDOException $e){
        mylog($e->getMessage());
        pdoRollBack();
        ajaxBack(null,4009,'数据库错误');
        exit;
    }
    include_once '../wechat/interfaceHandler.php';
    $wxPay=new wxPay($_SESSION['openid']);
    $wxPay->setOrderId($order_id)->setTotalFee($number*$price);
    $array=$wxPay->prePay();
    $data=$wxPay->getH5Package();
    if(is_array($data)){
        echo ajaxBack($data);
    }else{
        echo ajaxBack(null,6,$data);
    }


}