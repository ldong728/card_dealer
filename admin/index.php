<?php

include_once '../includePackage.php';
include_once '../wechat/serveManager.php';
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
if (isset($_GET['logout'])) {//登出
    session_unset();
    include 'view/login.html.php';
    exit;
}
if (isset($_SESSION['login']) && DOMAIN == $_SESSION['login']) {
    if (isset($_GET['menu']) && array_key_exists($_GET['menu'], $_SESSION['pms'])) {
        switch ($_GET['sub']) {
            case 'add_dealer':
                $rank='';
                if(isset($_SESSION['dealer_rank'])){
                    $rank=' where rank>'.$_SESSION['dealer_rank'];
                }else{
                    $rank='where rank>0';
                }
                $rank=pdoQuery('gd_user_rank',null,null,$rank. ' order by rank asc');
                printAdminView('admin/view/dealer_add.html.php', '新建用户');
                break;
            case 'dealer_list':
                $page=$_GET['page'];
                $where=null;
                $rank=0;
                if(isset($_SESSION['dealer_id'])){
                    $where=array('use_parent_id'=>$_SESSION['dealer_id']);

                }
                $dealerList=pdoQuery('gd_users',null,$where,' limit ' . $page * $num . ', ' .$num);
                printAdminView('admin/view/dealer_list.html.php','经销商列表');
                break;
            case 'dealer_audit':
                if($_SESSION['dealer_grade']===0){
                    $page=$_GET['page'];
                    $where=null;
//                    if(isset($_SESSION['dealer_id']))$where=array('f_id'=>$_SESSION['dealer_id']);
//                    else $where=array('f_id'=>'0');
                    $dealerList=pdoQuery('gd_audit_view',null,null,' limit ' . $page * $num . ', ' .$num);
                    printAdminView('admin/view/dealer_audit.html.php','经销商审核');
                }else{
                    printAdminView('admin/view/blank.html.php','首页');
                }
                break;
            case 'wx_config':
                printAdminView('admin/view/wechatConfig.html.php','微信设置');
                break;
            default:
                $_GET['sub']();
                break;
        }
        exit;
    }

    printAdminView('admin/view/blank.html.php', 'Zaman Goz');
    exit;
} else {
    if (isset($_GET['login'])) {
        $name = $_POST['adminName'];
        $pwd = $_POST['password'];
        if ($_POST['adminName'] . $_POST['password'] == ADMIN . PASSWORD) {
            $_SESSION['login'] = DOMAIN;
            $_SESSION['operator_id'] = -1;
            $_SESSION['dealer_rank']=0;
            printAdminView('admin/view/blank.html.php', 'Zaman Goz');
        } else {
            $query = pdoQuery('operator_tbl',array('id'), array('name' => $name, 'md5' => md5($pwd)), ' limit 1');
            $op_inf = $query->fetch();
            if ($op_inf) {
                $_SESSION['login'] = DOMAIN;
                $_SESSION['operator_id'] = $op_inf['id'];
                printAdminView('admin/view/blank.html.php', 'Zaman Goz');
                exit;
            } else {
                include 'view/login.html.php';
                exit;
            }

        }
        exit;
    }

    include 'view/login.html.php';
    exit;
}
function card_create(){
    $parnerQuery=pdoQuery('partner_tbl',array('id','p_inf'),null,null);
    printAdminView('admin/view/card_edit.html.php','创建卡券');
}
function operator(){
    global $pmsList,$opList;
    $pms=pdoQuery('pms_tbl',null,null,null);
    foreach ($pms as $row) {
        $pmsList[$row['id']]=$row;
    }

    $op=pdoQuery('op_pms_view',null,null,null);
    foreach ($op as $row) {

        if(!isset($opList[$row['id']])){
            $opList[$row['id']]=$row;
            $opList[$row['id']]['pms']=$pmsList;
        }
        if($row['pms_id'])$opList[$row['id']]['pms'][$row['pms_id']]['checked']='checked';
    }
//    mylog(getArrayInf($opList));
    printAdminView('admin/view/operator.html.php','操作员管理');


}
function index_config(){
    global $getStr;
    $articleInf=pdoQuery('gd_article',array('art_id'),array('art_channel_id'=>-1),' limit 1');
    $articleId=$articleInf->fetch();
    header('location: controller.php?get_editor='.$articleId['art_id'].'&'.$getStr);

}
function about(){
    global $getStr;
    $articleInf=pdoQuery('gd_article_view',array('art_id'),array('cha_code'=>$_GET['sub']),' limit 1');
    $articleId=$articleInf->fetch();
    if(!$articleId)$articleId['art_id']=0;
    header('location: controller.php?get_editor='.$articleId['art_id'].'&'.$getStr);
}
function customer_photo(){
    global $getStr;
    global $page;
    global $num;
    global $list;
    global $count;
    $list=pdoQuery('gd_article_view',array('art_id','art_img','art_title','art_show','art_index'),array('cha_code'=>$_GET['sub']), ' order by art_add_time desc,art_index asc limit ' . $page * $num . ', ' .$num);
    $list=$list->fetchAll();
    $count=pdoQuery('gd_article_view',array('count(*) as count'),array('cha_code'=>$_GET['sub']),null);
    $count=$count->fetch()['count'];
    printAdminView('admin/view/customer_photo_list.html.php');
}
function goods(){
    global $getStr;
    global $page;
    global $num;
    global $list;
    global $count;
    $list=pdoQuery('gd_article_view',array('art_id','art_more_img','art_title','art_show','art_index'),array('cha_code'=>$_GET['sub']), ' order by art_add_time desc,art_index asc limit ' . $page * $num . ', ' .$num);
    $list=$list->fetchAll();
    $count=pdoQuery('gd_article_view',array('count(*) as count'),array('cha_code'=>$_GET['sub']),null);
    $count=$count->fetch()['count'];
    printAdminView('admin/view/goods_list.html.php');
}
function activities(){
    global $getStr;
    global $page;
    global $num;
    global $list;
    global $count;
    global $source;
    $list=pdoQuery('gd_article_view',array('art_id','art_img','art_title','art_show','art_index'),array('cha_code'=>$_GET['sub']), ' order by art_add_time desc,art_index asc limit ' . $page * $num . ', ' .$num);
    $list=$list->fetchAll();
    $count=pdoQuery('gd_article_view',array('count(*) as count'),array('cha_code'=>$_GET['sub']),null);
    $count=$count->fetch()['count'];
    $source=getConfig('../config/mainConfig.json')['activity_source'];
    printAdminView('admin/view/activities_list.html.php');
}
function goods_verify(){

    printAdminView('admin/view/blank.html.php');
}
function dealer_verify(){
    printAdminView('admin/view/blank.html.php');
}
