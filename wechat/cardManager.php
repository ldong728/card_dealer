<?php
//session_start();
include_once  $GLOBALS['mypath']. '/wechat/interfaceHandler.php';
//$mInterface=new interfaceHandler(WEIXIN_ID);


function createCard(array $cardInf){
    $sInterFace=new interfaceHandler(WEIXIN_ID);



}

function uploadLogo($file)
{
    $sInterFace=new interfaceHandler(WEIXIN_ID);
    $back = $sInterFace->uploadFileByCurl('https://api.weixin.qq.com/cgi-bin/media/uploadimg?access_token=ACCESS_TOKEN', $file,'buffer');
    $backArray=json_decode($back,true);
    if(isset($backArray['url'])){
        return $backArray['url'];
    }else{
        return $backArray['errcode'];
    }
}
function encodeCard($encryptCode){
    $encryptCode=array('encrypt_code'=>$encryptCode);
    $sInterFace=new interfaceHandler(WEIXIN_ID);
    $data=$sInterFace->postArrayByCurl('https://api.weixin.qq.com/card/code/decrypt?access_token=ACCESS_TOKEN',$encryptCode);
    return $data;
}
function getCardCode($encryptCode,$check_consume=false){
    $code=array('encrypt_code'=>$encryptCode);
    $sInterFace=new interfaceHandler(WEIXIN_ID);
    $return=$sInterFace->postArrayByCurl('https://api.weixin.qq.com/card/code/decrypt?access_token=ACCESS_TOKEN',$code);
    $return=json_decode($return,true);
    if($return['errcode']==0){
        $code=array('code'=>$return['code']);
        $code['check_consume']=$check_consume;
        $data=$sInterFace->postArrayByCurl('https://api.weixin.qq.com/card/code/get?access_token=ACCESS_TOKEN',$code);
        $dataArray=json_decode($data,true);
        $dataArray['card']['card_code']=$return['code'];
        return $dataArray;
    }else{
        return false;
    }
}
function consumeCard($cardCode){
    $sInterFace=new interfaceHandler(WEIXIN_ID);
    $code=array('code'=>$cardCode);
    $data=$sInterFace->postArrayByCurl('https://api.weixin.qq.com/card/code/get?access_token=ACCESS_TOKEN',$code);
    $data=json_decode($data,true);
    if($data['can_consume']==1){
        $return=$sInterFace->postArrayByCurl('https://api.weixin.qq.com/card/code/consume?access_token=ACCESS_TOKEN',$code);
        $return=json_decode($return,true);
        if($return['errcode']==0){
            return $return;
        }else{
            return false;
        }
    }else{
        return false;
    }
}

function getCardDetail($card_id){
    $sInterFace=new interfaceHandler(WEIXIN_ID);
    $inf=array('card_id'=>$card_id);
    $data=$sInterFace->postArrayByCurl('https://api.weixin.qq.com/card/get?access_token=ACCESS_TOKEN',$inf);
    return $data;
}