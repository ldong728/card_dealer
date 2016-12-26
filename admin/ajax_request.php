<?php
include_once '../includePackage.php';
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
function get_article(){
    $id=$_POST['id'];
    $text=pdoQuery('gd_article',array('art_text'),array('art_id'=>$id),' limit 1');
    $text=$text->fetch();
    echo ajaxBack($text['art_text']);
}
?>