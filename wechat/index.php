<?php
include_once '../includePackage.php';
include_once $GLOBALS['mypath'].'/wechat/interfaceHandler.php';
include_once $GLOBALS['mypath'].'/wechat/wechat.php';
include_once $GLOBALS['mypath'].'/wechat/reply.php';
include_once $GLOBALS['mypath'].'/wechat/oauth.php';
include_once $GLOBALS['mypath'].'/wechat/usersdk.php';
include_once $GLOBALS['mypath'].'/wechat/serveManager.php';

session_start();
if(isset($_GET['oauth'])&&!isset($_SESSION['openid'])){
    session_unset();
    //getparam:oauth&diract
    $diract=urlencode(str_replace('diract=','',strchr($_SERVER['QUERY_STRING'],'diract=')));//保存diract参数后的所有参数并传递
    if($diract=='')$diract='none';
    $oauth=new oauth($_GET['oauth'],$diract);
    $_SESSION['oauthType']=$_GET['oauth'];
    $oauth->getOauth();
    exit;
}elseif(isset($_SESSION['openid'])){
    $diract=str_replace('diract=','',strchr($_SERVER['QUERY_STRING'],'diract='));
    $user=new usersdk($_SESSION['openid']);
    $userInf=$user->syncUserInf('user_tbl');
    $_SESSION['user_level']=$userInf['user_level'];
    header('location:../mobile/controller.php?module='.$diract);
}

if(isset($_GET['state'])&&isset($_SESSION['oauthType'])){//从授权页跳转至此
    if(isset($_GET['code'])){
        $userId=oauth::getOauthToken($_GET['code']);
        $_SESSION['openid']=$userId['openid'];
        $user=new usersdk($_SESSION['openid']);
        $userInf=$user->syncUserInf('user_tbl');
        $_SESSION['user_level']=$userInf['user_level'];
        if('snsapi_userinfo'==$_SESSION['oauthType']){
            //TODO 获取用户数据的操作
        }else{
            $_SESSION['userInf']=getUnionId($_SESSION['openid']);
        }
        switch($_GET['state']){
            default:
                header('location:../mobile/controller.php?module='.urldecode($_GET['state']));
                break;
        }
    }else{

        //TODO 无法获取openid
    }
}


