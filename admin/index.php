<?php

include_once '../includePackage.php';
include_once '../wechat/serveManager.php';
session_start();

if (isset($_SESSION['login'])&&DOMAIN==$_SESSION['login']) {
    if (isset($_GET['menu']) && array_key_exists($_GET['menu'], $_SESSION['pms'])) {
        switch ($_GET['sub']) {
            case 'add_dealer':
                printAdminView('admin/view/dealer_add.html.php', '新建用户');
                break;
        }
        exit;
    }


    if (isset($_GET['operator'])) {
        if (isset($_SESSION['pms']['operator'])) {
            $query = pdoQuery('pms_tbl', null, null, null);
            foreach ($query as $row) {
                $pmsList[$row['key']] = array('value' => $row['key'], 'name' => $row['name']);
            }
            $query = pdoQuery('pms_view', null, null, null);
            foreach ($query as $row) {
                if (!isset($opList[$row['id']])) {
                    $opList[$row['id']] = array(
                        'id' => $row['id'],
                        'name' => $row['name'],
                        'pwd' => $row['pwd'],
                        'pms' => $pmsList
                    );
//                    $opList[$row['id']]=$pmsList;
                }
                $opList[$row['id']]['pms'][$row['pms']]['checked'] = 'checked';
            }
//            mylog(getArrayInf($opList));
            printAdminView('admin/view/operator.html.php', '操作员管理');
            exit;

        } else {
            echo '权限不足';
            exit;
        }
    }
    if (isset($_GET['logout'])) {//登出
        session_unset();
        include 'view/login.html.php';
        exit;
    }
    printAdminView('admin/view/blank.html.php', '卡券商城');
    exit;
} else {
    echo 'not login';
    if (isset($_GET['login'])) {
        $name = $_POST['adminName'];
        $pwd = $_POST['password'];
        if ($_POST['adminName'] . $_POST['password'] == ADMIN . PASSWORD) {
            $_SESSION['login'] = DOMAIN;
            $_SESSION['operator_id'] = -1;
            printAdminView('admin/view/blank.html.php', '卡券商城');
        } else {
            $query = pdoQuery('operator_tbl', null, array('name' => $name, 'md5' => md5($pwd)), ' limit 1');
            $op_inf = $query->fetch();
            if (!$op_inf) {
                $dealQuery=pdoQuery('gd_users',null,array('use_username'=>$name,'use_password'=>md5($pwd)),' limit 1');
                $dealer_inf=$dealQuery->fetch();
                if($dealer_inf){
                    $_SESSION['login'] = DOMAIN;
                    $_SESSION['dealer_id']=$dealer_inf['use_id'];
                    printAdminView('admin/view/blank.html.php', '卡券商城');
                    exit;
                }else{
                    include 'view/login.html.php';
                    exit;
                }
            } else {
                $_SESSION['login'] = DOMAIN;
                $_SESSION['operator_id'] = $op_inf['id'];
                printAdminView('admin/view/blank.html.php', '卡券商城');
                exit;
            }

        }
        exit;
    }

//    include 'view/login.html.php';
    echo 'ok';
    exit;
}