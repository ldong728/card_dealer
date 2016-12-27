<?php
include_once '../includePackage.php';
session_start();

if (isset($_SESSION['partner'])) {
    if(isset($_POST['action'])){
        $_POST['action']($_POST['data']);

        exit;
    }
}

function add_operator($data){
    $phone=$data['phone'];
    $password=md5($data['password']);
    $id=pdoInsert('partner_operator_tbl',array('partner_id'=>$_SESSION['partner'],'phone'=>$phone,'password'=>$password),'ignore');
    if($id){
        echo ajaxBack($id);
    }else{
        echo ajaxBack(null,8,'插入失败');
    }
}
function settle_card($data){
    $whereStr='';
    foreach ($data as $row) {
        $whereStr.=$row.',';
    }
    $whereStr='where card_code in ('.trim($whereStr,',').')';
    $query=pdoQuery('pre_settle_view',array('sum(price) as total'),null,' '.$whereStr);
    $price=$query->fetch()['total'];
    $totalPrice=$price?$price:0;
    pdoTransReady();
    try{
        pdoUpdate('card_user_tbl',array('status'=>4),array(),$whereStr);
        pdoInsert('account_record_tbl',array('partner_id'=>$_SESSION['partner'],'fee'=>$totalPrice,'nonce'=>getRandStr(16)));
        $str='update partner_account_tbl set total_balence=total_balence+'.$totalPrice.' where partner_id='.$_SESSION['partner'];
        exeNew($str);
        pdoCommit();
        echo ajaxBack();
    }catch(PDOException $e){
        mylog($e->getMessage());
        echo ajaxBack(null,8,'数据库出错');
    }

}