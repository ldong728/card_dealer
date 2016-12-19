<?php

function VIEW($msg){
//    mylog('it work');

}
function kf_create_session($msg){

}
function kf_close_session($msg){
//    updateWechatMode($msg['from'],'normal');
}

function user_get_card($msg){
    mylog('user_get_card');
    pdoTransReady();
    try{
        if($msg['IsGiveByFriend']){
            pdoUpdate('card_user_tbl',array('card_code'=>$msg['UserCardCode'],'open_id'=>$msg['FromUserName'],'status'=>'0','update_time'=>timeUnixToMysql(time())),array('card_code'=>$msg['OldUserCardCode'],'card_id'=>$msg['CardId']));
            pdoInsert('card_record_tbl',array('card_id'=>$msg['CardId'],'from_card_code'=>$msg['OldUserCardCode'],'to_card_code'=>$msg['UserCardCode'],'from_id'=>$msg['FriendUserName'],'to_id'=>$msg['FromUserName']));
        }else{
            pdoInsert('card_user_tbl',array('card_id'=>$msg['CardId'],'card_code'=>$msg['UserCardCode'],'open_id'=>$msg['FromUserName'],'original_id'=>$msg['FromUserName']));
        }
        pdoCommit();
    }catch(PDOException $e){
        mylog($e->getMessage());
        pdoRollBack();
        echo 'error';
        exit;
    }



//    pdoInsert('card_repository_tbl',array('card_id'=>$msg['CardId'],''=>$msg['UserCardCode'],'customer_id'=>$msg['from']),'update');
}
function user_del_card($msg){
    $card_code=$msg['UserCardCode'];
    pdoUpdate('card_user_tbl',array('status'=>5),array('card_code'=>$card_code));
    return;
}
function user_consume_card($msg){

    return;
}
function user_gifting_card($msg){
//    $from_id=$msg['FromUserName'];
//    $to_id=$msg['FriendUserName'];
    $card_code=$msg['UserCardCode'];
    pdoUpdate('card_user_tbl',array('status'=>1),array('card_code'=>$card_code));

    return;
}

function CLICK($msg){
//    mylog('click');
//    if($msg['EventKey']=='kf'){
////        mylog('kf');
//        sendKFMessage($msg['FromUserName'],'已为您接入人工客服，请稍候');
//        $GLOBALS['weixin']->toKFMsg();
//    }
    return;
}

function subscribe($msg){
//    if(isset($msg['EventKey'])){
//        if(preg_match('/qrscene_/',$msg['EventKey'])){
//            $f_sdp_id=preg_replace('/qrscene_/','',$msg['EventKey']);
//            pdoInsert('sdp_subscribe_tbl',array('open_id'=>$msg['FromUserName'],'f_sdp_id'=>$f_sdp_id),'update');
//        }
//    }
    return;
}