<?php
/**
 * Created by PhpStorm.
 * User: godlee
 * Date: 2015/10/20
 * Time: 11:44
 */
include_once '../includePackage.php';
session_start();




$state='none';
$url='https://open.weixin.qq.com/connect/oauth2/authorize?'
    .'appid='.APP_ID
    .'&redirect_uri='.urlencode('http://'.$_SERVER['HTTP_HOST'].DOMAIN.'/mobile/controller.php?oauth=1')
    .'&response_type=code&scope=snsapi_base'
    .'&state='.$state.'#wechat_redirect';
//mylog($url);
$config = getConfig('config/config.json');
$adQuery = pdoQuery('ad_tbl', null, null, '');
foreach ($adQuery as $adRow) {
    $adList[$adRow['category']][] = $adRow;
}
$indexRmark=pdoQuery('index_remark_tbl',null,null,null);
$menuid=$_SESSION['sdp']['level']>1?2:$_SESSION['sdp']['level'];
//mylog('level:'.$menuid);
$menuQuery=pdoQuery('sdp_menu_tbl',null,null,' where level like "%'.$menuid.'%" limit 5');
unset($_SESSION['sdp']['menu']);
foreach ($menuQuery as $row) {

    $_SESSION['sdp']['menu'][]=$row;
}

mylog(urldecode("http%3A%2F%2Fwww.tian1gang.com%2Fweixin%2Fauthorizeback%3Fs_waid%3D945%26wx_flag%3D1%26t_b%3D636132675993117562_5302%26wx_scope%3D0&response_type=code&scope=snsapi_base&state=openid#wechat_redirect"));


include 'view/index.html.php';
exit;