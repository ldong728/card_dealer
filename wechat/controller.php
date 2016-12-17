<?php
include_once '../includePackage.php';
include_once $GLOBALS['mypath'].'/wechat/interfaceHandler.php';
include_once $GLOBALS['mypath'].'/wechat/wechat.php';
include_once $GLOBALS['mypath'].'/wechat/reply.php';
//mylog($_SERVER['QUERY_STRING']);
$weixin=new wechat(WEIXIN_ID);
$weixin->valid();
$msg=$weixin->receiverFilter();
$random=rand(1000,9999);
$eventList=array
(
    'VIEW',
    'kf_create_session',
    'kf_close_session',
    'user_get_card',
    'user_del_card',
    'CLICK',
    'subscribe'

);

if(in_array($msg['MsgType'],array('text','voice','img'))){
    normalReply($weixin,$msg);

}

if($msg['MsgType']=='event'){
    include_once 'event.php';
    if(in_array($msg['Event'],$eventList)){
        $msg['Event']($msg);
    }
}
echo 'success';
exit;

