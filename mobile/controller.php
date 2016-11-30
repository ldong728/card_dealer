<?php
/**
 * Created by PhpStorm.
 * User: godlee
 * Date: 2015/10/28
 * Time: 15:14
 */
include_once '../includePackage.php';
session_start();
if(isset($_SESSION['openId'])){
    if(isset($_GET['module'])){
        if('card'==substr($_GET['module'],0,4)){
            include_once '../wechat/cardsdk.php';
            $card=new cardsdk();
            $_GET['module']();
            exit;
        }
        switch($_GET['module']){
            case 'card_mall':
                $cardid='pubtTtwIDpuhWcvKtOW0e9Dj01Ig';
                for($i=0;$i<3;$i++){
                    $cardInfList[]=array('id'=>$cardid,'ext'=>json_encode($card->getCardExt($_SESSION['openId'],$cardid)));
                }
                include 'view/get_card_view.html.php';
                break;
            case 'card_list':
//                $cardList=$card->requestCardList();
//                mylog($cardList);
//                $cardListArray=json_decode($cardList,true);
//                if(0==$cardListArray['errcode']){
//                    foreach ($cardListArray['card_id_list'] as $row) {
//                        $value[]=array('card_id'=>$row,'partner_id'=>'0','card_status'=>'CARD_STATUS_NOT_VERIFY');
//                    }
//                    if(isset($value))pdoBatchInsert('card_tbl',$value);
//                }
                include 'view/card_mall.html.php';
                break;
            case 'card_tempset':
                $cardIdList=pdoQuery('card_tbl',array('card_id'),null,null);
                foreach ($cardIdList as $row) {
                    $cardinf=$card->requestCardInf($row['card_id'],true);
//                    mylog(getArrayInf($cardinf));
                    if($cardinf['errcode']==0){
                        $cardtype=strtolower($cardinf['card']['card_type']);
//                        $endTime=

//                        mylog('title='.$cardinf['card'][$cardtype]['base_info']['title']);
                    pdoUpdate('card_tbl',array('card_title'=>$cardinf['card'][$cardtype]['base_info']['title']),array('card_id'=>$row['card_id']));

                    }
                }
                echo 'ok';
                break;
        }

        exit;
        }


    if(isset($_GET['get_buyed_card'])){
//        $cardList=pdoQuery('')
    }
}
function card_list(){

}
function card_mall(){
    $cardList=pdoQuery('card_view',null,array('user_level'=>$_SESSION['user_level']),null);
    include 'view/card_mall.html.php';
}




