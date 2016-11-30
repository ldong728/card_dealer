<?php
include_once '../includePackage.php';
include_once $GLOBALS['mypath'].'/wechat/interfaceHandler.php';
include_once $GLOBALS['mypath'].'/wechat/wechat.php';
include_once $GLOBALS['mypath'].'/wechat/reply.php';
include_once $GLOBALS['mypath'].'/wechat/oauth.php';
include_once $GLOBALS['mypath'].'/wechat/serveManager.php';
session_start();
//createButtonTemp();


if(isset($_GET['oauth'])&&!isset($_SESSION['openId'])){//未获取用户信息，跳转至授权页面
    //getparam:oauth&diract
    $diract=isset($_GET['diract'])?$_GET['diract'] : 'none';
    $oauth=new oauth($_GET['oauth'],$diract);
    $_SESSION['oauthType']=$_GET['oauth'];
    $oauth->getOauth();
    exit;
}elseif(isset($_SESSION['openId'])){//已获取用户信息，直接跳转
    $diract=isset($_GET['diract'])?$_GET['diract'] : 'none';
    header('location:../mobile/controller.php?module='.$diract);
}
if(isset($_GET['state'])&&isset($_SESSION['oauthType'])){//从授权页跳转至此
//    mylog('oauthType');
    if(isset($_GET['code'])){
        $userId=oauth::getOauthToken($_GET['code']);
        $_SESSION['openId']=$userId['openid'];
        $user=new usersdk($_SESSION['open']);
        $userInf=$user->syncUserInf('user_tbl');
        $_SESSION['user_level']=$userInf['user_level'];
        if('snsapi_userinfo'==$_SESSION['oauthType']){
            //TODO 获取用户数据的操作
        }else{
            $_SESSION['userInf']=getUnionId($_SESSION['openId']);
        }
        switch($_GET['state']){
            default:
                header('location:../mobile/controller.php?module='.$_GET['state']);
                break;
        }
    }else{

        //TODO 无法获取openid
    }
}


