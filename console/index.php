<?php

include_once '../includePackage.php';
include_once '../wechat/serveManager.php';
include_once 'function.php';
session_start();
$num=15;
$getStr = '';
$page=isset($_GET['page'])? $_GET['page'] : 0;
$orderIndex=isset($_GET['index'])?$_GET['index']:'id';
$order=isset($_GET['order'])?$_GET['order']:'asc';
foreach ($_GET as $k => $v) {
    if ($k == 'page') continue;
    if ($k == 'index') continue;
    if ($k == 'order') continue;
    $getStr .= $k . '=' . $v . '&';
}
$getStr=rtrim($getStr,'&');

if (isset($_SESSION['partner'])) {
    if (isset($_GET['logout'])) {//登出
        session_unset();
        include 'view/login.html.php';
        exit;
    }
    if(isset($_GET['sub'])){
        $func='menu_'.$_GET['sub'];
        $func();
        exit;
    }

    printView('view/blank.html.php', '控制台', '/console');
    exit;
} else {
    if (isset($_GET['login'])) {
        $name = $_POST['adminName'];
        $pwd = $_POST['password'];
        $query = pdoQuery('partner_tbl', null, array('p_code' => $name, 'password' => md5($pwd)), ' limit 1');

        $op_inf = $query->fetch();
        if (!$op_inf) {
            include 'view/login.html.php';
            exit;
        } else {
            $_SESSION['partner'] = $op_inf['id'];
            $_SESSION['p_code'] = $op_inf['p_code'];
            printView('view/blank.html.php', '控制台');
            exit;
        }

    }
    include 'view/login.html.php';
    exit;
}

function menu_settle(){
    global $getStr,$page,$num,$orderIndex,$order,$list,$balence;
    $query=pdoQuery('pre_settle_view',array('card_order_id','card_title','card_id','price','card_code','consume_time','type'),array('partner_id'=>$_SESSION['partner']),'order by '.$orderIndex.' '.$order.' limit '.$num*$page.','.$num);
    foreach($query as $row){
        $list[]=$row;
    }
    $balence=pdoQuery('partner_account_tbl',array('total_balence'),array('partner_id'=>$_SESSION['partner']),' limit 1');
    $balence=$balence->fetch()['total_balence'];
    $list=$list?$list:array();
    printView('view/settle.html.php','待结算列表');
}
function menu_operator(){
    $list=getList('partner_operator_view');
    printView('view/operator.html.php');
    exit;

}
function menu_pre_express(){
    global $getStr,$page,$num,$orderIndex,$order,$list;
    $list=getList('express_view');
    printView('view/express_list.html.php');
    exit;

}
function getList($tableName){
    global $getStr,$page,$num,$orderIndex,$order,$list;
    $query=pdoQuery($tableName,null,array('partner_id'=>$_SESSION['partner']),'order by '.$orderIndex.' '.$order.' limit '.$num*$page.','.$num);
    foreach($query as $row){
        $list[]=$row;
    }
    $list=$list?$list:array();
    return $list;
}
