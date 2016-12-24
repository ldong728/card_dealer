<?php
/**
 * Created by PhpStorm.
 * User: godlee
 * Date: 2015/11/3
 * Time: 23:20
 */
define('SDP_KEY','329qkd98ekjd9aqkrmr87t');
define('TIME_OUT',15);

function printAdminView($addr,$title='abc',$subPath='/admin'){
    if(!isset($_SESSION['pms'])){
        if(isset($_SESSION['operator_id'])){
            if(-1==$_SESSION['operator_id']){
                $menuQuery=pdoQuery('pms_view',null,null,' order by f_id,s_id asc');
            }else{
                $opQuery=pdoQuery('op_pms_tbl',array('pms_id'),array('o_id'=>$_SESSION['operator_id']),null);
                foreach ($opQuery as $row) {
                    $pmList[]=$row['pms_id'];
                }
                $menuQuery=pdoQuery('pms_view',null,array('f_id'=>$pmList),' order by f_id,s_id asc');
            }
        }else{
            $menuQuery=pdoQuery('pms_view',null,array('f_key'=>'dealer'),' order by f_id,s_id asc');
        }

        foreach ($menuQuery as $row) {
            if(!isset($_SESSION['pms'][$row['f_key']])){
                $_SESSION['pms'][$row['f_key']]=array('key'=>$row['f_key'],'name'=>$row['f_name'],'sub'=>array());
            }
            if(isset($row['s_id']))$_SESSION['pms'][$row['f_key']]['sub'][]=array('id'=>$row['s_id'],'key'=>$row['s_key'],'name'=>$row['s_name']);
        }
    }
//    pdoQuery('sub_menu_tbl',null,array('parent_id'=>array()))
    $mypath= $GLOBALS['mypath'];
    include $mypath.$subPath.'/templates/header.html.php';
    include $mypath.'/'.$addr;
    include $mypath.$subPath.'/templates/footer.html.php';
}
function init()
{
    $smq=array();
    $mq=array();
    $sub_cg = pdoQuery('category_overview_view', null, null, '');
    foreach ($sub_cg as $sl) {
        $smq[] = array(
            'id' => $sl['id'],
            'name' => $sl['father_name'] . '--' . $sl['sub_name']
        );
    }
    $father_cg = pdoQuery('category_tbl', null, null, '');
    foreach ($father_cg as $l) {
        $mq[] = array(
            'id' => $l['id'],
            'name' => $l['name']
        );
    }
    $_SESSION['mq'] = $mq;
    $_SESSION['smq'] = $smq;
}
function getOrderStu($index){
    $list=array('待付款','处理中','已付款','已领取','已删除','已过期','支付出错');
    return $list[$index];
}
function getCardStu($index){
    $list=array("正常","转赠中","核销中","已核销","已过期","已删除","异常");
    return $list[$index];
}
function getProvince($pro){
    $datafile = 'config/province.inc.php';
    if(file_exists($datafile)){
        $config = include($datafile);
        return $config[$pro];
    }
}
function getCity($pro,$city){
    $datafile = 'config/city.inc.php';
    if(file_exists($datafile)){
        $config = include($datafile);
        $province_id=$pro;
        if($province_id != ''){
            $citylist = array();
            if(is_array($config[$province_id]) && !empty($config[$province_id])){
                $citys = $config[$province_id];
                return $citys[$city];
            }
        }
    }
}
function getArea($pro,$city,$area){
    $datafile = 'config/area.inc.php';
    if(file_exists($datafile)){
        $config = include($datafile);
        $province_id = $pro;
        $city_id = $city;
        if($province_id != '' && $city_id != ''){
            $arealist = array();
            if(isset($config[$province_id][$city_id]) && is_array($config[$province_id][$city_id]) && !empty($config[$province_id][$city_id])){
                $areas = $config[$province_id][$city_id];
                return $areas[$area];
            }
        }
    }
}

function getConfig($path){
    $data=file_get_contents($path);
    return json_decode($data,true);
}
function saveConfig($path,array $config){
    $data=json_encode($config);
    file_put_contents($path,$data);
}

function cardConsume($code,$consumeType,$inf){
    $card=new cardsdk();
    if($card->consumeCard($code)) {
        pdoTransReady();
        try {
            pdoUpdate('card_user_tbl', array('status' => 2), array('card_code' => $code), ' limit 1');

            switch ($consumeType) {
                case 'online':
                    pdoinsert('express_tbl', array('open_id' => $_SESSION['consume_inf']['openid'], 'card_id' => $_SESSION['consume_inf']['cardid'], 'code_id' => $code, 'address_id' => $inf));
                    $value = array('code' => $code, 'openid' => $_SESSION['consume_inf']['openid'], 'address_id' => $inf);
                    $cardId = $_SESSION['consume_inf']['cardid'];
                    unset($_SESSION['consume_inf']);
                    break;
                case 'local':
                    $value = array('code' => $code, 'partner_operator_id' => $inf);
                    $cardId = $_SESSION['operator']['current_cardid'];
                    unset($_SESSION['operator']['current_cardid']);
                    break;
            }
            $str = 'update card_tbl set consumed_number=consumed_number+1 where card_id="' . $cardId . '"';
            exeNew($str);
            $value['type'] = $consumeType;
            pdoInsert('card_consume_recorder_tbl', $value);
            pdoCommit();
            return true;
        } catch (PDOException $e) {
            mylog($e->getMessage());
            pdoRollBack();
            return false;
        }
    }return false;

}

