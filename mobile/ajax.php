<?php
/**
 * Created by PhpStorm.
 * User: godlee
 * Date: 2015/10/26
 * Time: 13:09
 */
include_once '../includePackage.php';
include_once '../wechat/cardsdk.php';
session_start();

if(isset($_SESSION['consume_inf'])){
    if(isset($_POST['addr'])){
        $_POST['addr']();
        exit;
    }
}
if(isset($_SESSION['operator'])){
    $func='op_'.$_POST['action'];
    $func();
    exit;
}
if(isset($_SESSION['openid'])){
        if(isset($_POST['module'])&&'pay'==substr($_POST['module'],0,3)){
            include_once '../wechat/wxPay.php';
        }
        switch($_POST['module']){
            default:
                $_POST['module']();
                break;
        }
}


function op_consume(){
    $code=$_POST['data'];
    mylog($code);
    $inf=pdoQuery('card_user_view',array('card_id'),array('card_code'=>$code,'partner_id'=>$_SESSION['operator']['partner_id']),' limit 1');
    if($inf=$inf->fetch()){
        mylog();
        $_SESSION['operator']['current_cardid']=$inf['card_id'];
        if(cardConsume($code,'local',$_SESSION['operator']['id'])){
            echo ajaxBack();
        }else{
            echo ajaxBack(null,5,'服务器故障');
        }

    }
}
function addr_count(){
    $count=pdoQuery('address_tbl',array('count(*) as count'),array('open_id'=>$_SESSION['consume_inf']['openid']),null);
    $count=$count->fetch();
    echo ajaxBack($count['count']);
}
function addr_edit(){
    $id=$_POST['id'];
    $query=pdoQuery('address_tbl',null,array('id'=>$id),' limit 1');
    $query=$query->fetch();
    echo ajaxBack($query);

}
function add_addr(){
    $value=$_POST['data'];
    $value['open_id']=$_SESSION['consume_inf']['openid'];
    pdoInsert('address_tbl',$value,'update');
    echo ajaxBack();
}
function set_addr_default(){
    pdoTransReady();
    try{
        pdoUpdate('address_tbl',array('dft_a'=>0),array('open_id'=>$_SESSION['consume_inf']['openid']));
        pdoUpdate('address_tbl',array('dft_a'=>1),array('id'=>$_POST['id']),' limit 1');
        pdoCommit();
        echo ajaxBack();
    }catch(PDOException $e){
        mylog($e->getMessage());
        pdoRollBack();
        echo ajaxBack(null,9,'数据库错误');
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
function get_card(){
    include_once '../wechat/cardsdk.php';
    mylog(json_encode($_POST));
    $cardList=$_POST['cardList'];
    $value=array();
    $card=new cardsdk();


    pdoTransReady();
    try{
        foreach ($cardList as $row) {
            $code=$card->encodeCardCode($row['card_code']);
            pdoInsert('card_user_tbl',array('card_order_id'=>$row['order_id'],'card_id'=>$row['card_id'],'card_code'=>$code?$code:$row['card_code'],'open_id'=>$_SESSION['openid'],'original_id'=>$_SESSION['openid']),'update');
        }
        foreach ($_POST['getedCount'] as $k=>$value) {
            $str=' update card_order_tbl set getted=getted+'.$value.' where card_order_id="'.$k.'"';
            exeNew($str);
        }
        pdoCommit();
        echo ajaxBack();
    }catch(PDOException $e){
        mylog($e->getMessage());
        pdoRollBack();
        echo ajaxBack(null,9,'数据库错误');
    }

}