<?php

include_once '../includePackage.php';
include_once $GLOBALS['mypath'].'/wechat/interfaceHandler.php';
include_once $GLOBALS['mypath'].'/wechat/wechat.php';
include_once $GLOBALS['mypath'].'/wechat/reply.php';
$weixin=new wechat(WEIXIN_ID);
$weixin->valid();
include_once $GLOBALS['mypath'].'/wechat/serveManager.php';
$myHandler=new interfaceHandler(WEIXIN_ID);
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
mylog(getArrayInf($msg));
if(in_array($msg['MsgType'],array('text','voice','img'))){
//    mylog('inmsgType');
    normalReply($weixin,$msg);
//    mylog('normalReply');
//            exit;

}

if($msg['MsgType']=='event'){
    include_once 'event.php';
    if(in_array($msg['Event'],$eventList)){
        $msg['Event']($msg);
    }
}

echo 'success';
exit;

