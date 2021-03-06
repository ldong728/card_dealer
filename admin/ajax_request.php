<?php
include_once '../includePackage.php';
include_once '../wechat/cardsdk.php';
session_start();

if (isset($_SESSION['login'])&&DOMAIN==$_SESSION['login']) {

//    mylog('session ok'.getArrayInf($_POST));
//    mylog('ajax reached');
    if(isset($_POST['pms'])&&array_key_exists($_POST['pms'],$_SESSION['pms'])){
        if(isset($_POST['method'])){
            switch ($_POST['method']) {
                case 'add_dealer':
                    mylog('reach');
                    foreach ($_POST['data'] as $k=>$v) {
                        if('use_password'==$k){
                            $value[$k]=md5($v);
                        }else{
                            $value[$k]=addslashes($v);
                        }
                    }
                    if(isset($_SESSION['dealer_id'])){
                        $value['use_parent_id']=$_SESSION['dealer_id'];
                        $value['use_grade']=$_SESSION['dealer_grade']+1;
                        $value['use_note']=0==$_SESSION['dealer_grade']?'pass':'audit';
//                        $value['use_note']=0==$_SESSION['dealer_grade']?'pass':'pass';
                    }
                    $id=pdoInsert('gd_users', $value,'ignore');
                    if($id){
//                        $back['id']=$id;
                        echo ajaxBack(array('id'=>$id));
                    }else{
//                        $back['erro']
                        echo ajaxBack(null,1,'记录无法保存');
                    }

                    break;
                case 'audit':
                    $auditId=$_POST['id'];
                    $id=pdoUpdate('gd_users',array('use_note'=>'pass'),array('use_id'=>$auditId));
                    if($id){
                        echo ajaxBack();
                    }else{
                        ajaxBack(null,1,'操作失败');
                    }
                    break;
                case 'delete_audit':
                    $deleteId=$_POST['id'];
                    $id=pdoDelete('gd_users',array('use_id'=>$deleteId,'use_note'=>'audit'));
                    if($id){
                        echo ajaxBack();
                    }else{
                        echo ajaxBack(null,1,'操作失败');
                    }
                    break;
                default:
                    $_POST['method']();
                    break;
            }
        }
        if (isset($_POST['alteTblVal'])) {//快速更改
            $data = pdoUpdate($_POST['tbl'], array($_POST['col'] => $_POST['value']), array($_POST['index'] => $_POST['id']));
            if($data){
                echo ajaxBack(array('id'=>$data));
            }else{
                echo ajaxBack(null,1,'记录无法修改');
            }
            exit;
        }
        if (isset($_POST['deleteTblVal'])) {//快速删除
            try{
                pdoDelete($_POST['tbl'], $_POST['value'], ' limit 1');
                                echo ajaxBack();

            }catch(PDOException $e){
                echo ajaxBack(null,1,'记录无法修改');
            }
            exit;
        }
        if (isset($_POST['addTblVal'])) {//快速插入
            try{
                $id=pdoInsert($_POST['tbl'], $_POST['value'], $_POST['onDuplicte']);
                echo ajaxBack(array('id'=>$id));
            }catch(PDOException $e){
                echo ajaxBack(null,1,'记录无法修改');
            }
            exit;
        }
        if(isset($_POST['altConfig'])){//快速更改设置
            $path='../config/'.$_POST['name'].'.json';
            $config=getConfig($path);
            if(array_key_exists($_POST['key'],$config)){
                $config[$_POST['key']]=$_POST['value'];
                saveConfig($path,$config);
                echo ajaxBack();
            }else{
                echo ajaxBack(null,'3','不存在的设置项');
            }
            exit;
        }

    }else{
        echo ajaxBack(null,9,'无权限');
        exit;
    }
}
function create_card(){
    $data=$_POST['data'];

    $inf=$data['inf'];
//    mylog('data:'.json_encode($data,JSON_UNESCAPED_UNICODE));
//    mylog('data:'.json_encode($data,JSON_UNESCAPED_UNICODE));
    $card=new cardsdk();
    $card_inf=$card->createCard($data['card']);
//    mylog(getArrayInf($card_inf));
    if(0==$card_inf['errcode']){
        $card_id=$card_inf['card_id'];
        foreach ($data['price'] as $k=>$v) {
            $price[]=array('card_id'=>$card_id,'user_level'=>$k,'price'=>$v);
        }
        $price=$price?$price:array();
        pdoTransReady();
        try{
            pdoInsert('card_tbl',array('card_id'=>$card_id,'partner_id'=>$inf['partner_id'],'total_number'=>$inf['quantity'],'card_title'=>$inf['title'],'card_status'=>'CARD_STATUS_NOT_VERIFY','end_time'=>timeUnixToMysql($inf['end']),'color'=>$inf['color']),'update');
            pdoBatchInsert('card_price_tbl',$price);
            pdoCommit();
            echo ajaxBack($card_id);
        }catch(PDOException $e){
            mylog($e->getMessage());
            pdoRollBack();
            echo ajaxBack(null,'8','数据库错误');
        }
    }else{
        echo ajaxBack(null,'2','格式错误');
    }
    exit;
}
function get_article(){
    $id=$_POST['id'];
    $text=pdoQuery('gd_article',array('art_text'),array('art_id'=>$id),' limit 1');
    $text=$text->fetch();
    echo ajaxBack($text['art_text']);
}
?>