<?php
include_once '../includePackage.php';
session_start();

if (isset($_SESSION['login'])&&DOMAIN==$_SESSION['login']) {
    if (isset($_POST['pms']) && array_key_exists($_POST['pms'], $_SESSION['pms'])) {
        mylog('post ok:' . getArrayInf($_POST));
        switch ($_POST['method']) {
            case 'add_dealer':
                mylog('reach');
                $parent_id = isset($_SESSION['dealer_id']) ? $_SESSION['dealer_id'] : '0';
                pdoInsert('gd_users', array('use_phone' => $_POST['phone'], 'use_username' => $_POST['name'], 'use_password' => md5($_POST['psw']), 'use_parent_id' => $parent_id));
                echo 'ok';
                break;
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

    } else {
        echo '无此权限';
        exit;
    }
}


