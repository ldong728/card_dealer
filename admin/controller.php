<?php
/**
 * Created by PhpStorm.
 * User: godlee
 * Date: 2015/10/29
 * Time: 10:50
 */
include_once '../includePackage.php';
session_start();

if (isset($_SESSION['login']) && DOMAIN == $_SESSION['login']) {
    if (isset($_GET['menu']) && array_key_exists($_GET['menu'], $_SESSION['pms'])) {
        if(isset($_GET['get_editor'])){
            $channel = pdoQuery('gd_channel',array('cha_id'),array('cha_code'=>$_GET['sub']),' limit 1');
            if($channelId=$channel->fetch()){
                $_GET['cha_id']=$channelId['cha_id'];
            }
            $articleId=$_GET['get_editor'];
            if($articleId){
                $articleInf=pdoQuery('gd_article',null,array('art_id'=>$articleId),' limit 1');
                $articleInf=$articleInf->fetch();
            }else{
                $articleInf=null;
            }
//            alert('ok');
            printAdminView('admin/view/editor.html.php','编辑');
            exit;
        }
        if(isset($_GET['get_goods_editor'])){
            $channel = pdoQuery('gd_channel',array('cha_id'),array('cha_code'=>$_GET['sub']),' limit 1');
            if($channelId=$channel->fetch()){
                $_GET['cha_id']=$channelId['cha_id'];
            }
            $articleId=$_GET['get_goods_editor'];
            if($articleId){
                $articleInf=pdoQuery('gd_article',null,array('art_id'=>$articleId),' limit 1');
                $articleInf=$articleInf->fetch();
                $imgList=explode(',',$articleInf['art_more_img']);
                if(!$imgList)$imgList=array();
            }else{
                $articleInf=null;
                $imgList=array();
            }
//            alert('ok');
            printAdminView('admin/view/goods_editor.html.php','编辑');
            exit;
        }
        if(isset($_GET['get_activities_editor'])){
            $channel = pdoQuery('gd_channel',array('cha_id'),array('cha_code'=>$_GET['sub']),' limit 1');
            if($channelId=$channel->fetch()){
                $_GET['cha_id']=$channelId['cha_id'];
            }
            $articleId=$_GET['get_activities_editor'];
            if($articleId){
                $articleInf=pdoQuery('gd_article',null,array('art_id'=>$articleId),' limit 1');
                $articleInf=$articleInf->fetch();
                $imgList=explode(',',$articleInf['art_more_img']);
                if(!$imgList)$imgList=array();
            }else{
                $articleInf=null;
                $imgList=array();
            }
//            alert('ok');
            printAdminView('admin/view/activities_edit.html.php','编辑');
            exit;
        }
        if(isset($_GET['edit_article'])){
            mylog('get get:'.getArrayInf($_GET));
            mylog('get post'.getArrayInf($_POST));
            foreach ($_POST as $k => $v) {
                if('art_more_img'==$k)$v=trim($v,',');
                $value[$k]=addslashes($v);
            }
            $value['art_add_time']=time();
            $id=pdoInsert('gd_article',$value,'update');
            $value['art_id']=$id;
            $articleInf=$value;
            if($_GET['rediract']){
                mylog('header to');
                header('location: index.php?menu='.$_GET['menu'].'&sub='.$_GET['sub']);
                exit;
            }
            printAdminView('admin/view/editor.html.php','编辑');
            exit;
        }
    }
    //公众号操作
    if (isset($_GET['wechat'])) {
        include_once '../wechat/serveManager.php';

        if (isset($_GET['createButton'])) {
            deleteButton();
//            createButtonTemp();
            $url = 'http://' . $_SERVER['HTTP_HOST'] . DOMAIN . '/wechat/?oauth=snsapi_base&diract=';
//            $button1sub1 = array('name' => '微官网', 'type' => 'view', 'url' => 'http://admin88.winjubao.com/weixinpl/weixin_inter/menu_index.php?customer_id=413');
//            $button1sub2 = array('name' => '品牌介绍', 'type' => 'view', 'url' =>'http://admin88.winjubao.com/weixin/plat/app/Html/413/953692/about.html?fromuser=null&wxref=mp.weixin.qq.com');
//            $button1sub3 = array('name' => '时尚态度', 'type' => 'view', 'url' =>'http://admin88.winjubao.com/weixin/plat/app/Html/413/953692/Detail_32.php?single_id=10288&C_id=413&fromuser=null&wxref=mp.weixin.qq.com');
//            $button1sub4 = array('name' => '细节展示', 'type' => 'view', 'url' => 'http://admin88.winjubao.com/weixin/plat/app/Html/413/953692/diy413_4922.html?fromuser=null&wxref=mp.weixin.qq.com');
//            $button2sub3 = array('name' => '经销商入口', 'type' => 'view', 'url'=>$url . 'log_check');
//            $button1=array('name' => '微官网', 'type' => 'view', 'url' => 'http://' . $_SERVER['HTTP_HOST'] . DOMAIN . '/mobile/controller.php?channel=index');
//            $button2sub1 = array('name' => '卡券商城', 'type' => 'view', 'url' => $url . 'qr_verify');
//            $button2sub2 = array('name' => '渠道回溯', 'type' => 'view', 'url' => $url . 'qr_query');
//            $button2sub3 = array('name' => '核销员操作', 'type' => 'view', 'url' => $url . 'qr_book');
//            $button3=array('name'=>'卡券领取','type'=>'view','url'=>$url.'user_inf');
//            $button2sub4 = array('name' => '我要参赛', 'type' => 'view', 'url' =>'http://admin88.winjubao.com/weixin/plat/app/Html/413/953692/Detail_32.php?single_id=10314&C_id=413&fromuser=null&wxref=mp.weixin.qq.com');
//            $button2sub5 = array('name' => '我要投票', 'type' => 'click', 'key' =>'wsy_413_16759');
//            $button3sub1 = array('name' => '联系我们', 'type' => 'view', 'url' => 'http://admin88.winjubao.com/weixin/plat/app/Html/413/953692/contact.html?fromuser=null&wxref=mp.weixin.qq.com');
//            $button3sub2 = array('name' => '意见反馈', 'type' => 'view', 'url' =>'http://admin88.winjubao.com/weixinpl/liuyan/show_liuyan.php?customer_id=413&fromuser=null&wxref=mp.weixin.qq.com');
//            $button3sub3 = array('name' => '买家秀展', 'type' => 'view', 'url' =>'http://admin88.winjubao.com/weixin/plat/app/Html/413/953692/diy413_4923.html?fromuser=null&wxref=mp.weixin.qq.com');
//            $button3sub4 = array('name' => '每月一课', 'type' => 'view', 'url' => $url . '&cate=4');
//            $button3sub5 = array('name' => '每月一课', 'type' => 'view', 'url' => $url . '&cate=4');
//            $button_card=array('name'=>'卡券系统','type'=>'view','url'=> 'http://' . $_SERVER['HTTP_HOST'] . '/card_dealer/wechat/?oauth=snsapi_base&diract=card_test');
//            $button1 = array('name' => '关于我们', 'sub_button' => array($button1sub1, $button1sub2, $button1sub3, $button1sub4));
//            $button2 = array('name' => '功能菜单', 'sub_button' => array($button2sub1, $button2sub3));
//            $button3 = array('name' => '联系我们', 'sub_button' => array($button3sub1, $button3sub2));
            $button1=array('name'=>'卡券商城','type'=>'view','url'=>$url.'card_mall');
            $button2=array('name'=>'领取卡券','type'=>'view','url'=>$url.'card_bought_list');
            $button3=array('name'=>'核销员','type'=>'view','url'=>$url.'operator');
//            $mainButton = array('button' => array($button1, $button2, $button3), 'matchrule' => array('group_id' => $row['id']));
//            $button1=array('name'=>'二维码','sub_button'=>array($button2sub3,$button2sub1,$button3));
            $mainButton = array('button' => array($button1,$button2,$button3));
            $jsondata = json_encode($mainButton, JSON_UNESCAPED_UNICODE);
            echo createButton($jsondata);
            exit;
        }
        if (isset($_GET['createUniButton'])) {
//            $url = 'http://' . $_SERVER['HTTP_HOST'] . DOMAIN . '/wechat/?oauth=snsapi_base&diract=';
//            $button1=array('name' => '微官网', 'type' => 'view', 'url' => 'http://' . $_SERVER['HTTP_HOST'] . DOMAIN . '/mobile/controller.php?channel=index');
//            $button2sub1 = array('name' => '防伪验证', 'type' => 'view', 'url' => $url . 'qr_verify');
//            $button2sub3 = array('name' => '发货扫描', 'type' => 'view', 'url' => $url . 'qr_book');
//            $button3=array('name'=>'个人中心','type'=>'view','url'=>$url.'user_inf');
//            $button2 = array('name' => '个人中心', 'sub_button' => array($button1, $button3));
//            $mainButton = array('button' => array($button2sub3, $button2sub1, $button2), 'matchrule' => array('tag_id' => 100));
//            $jsondata = json_encode($mainButton, JSON_UNESCAPED_UNICODE);
//            echo createUniButton($jsondata);
            exit;
        }
        if (isset($_GET['getMenuInf'])) {
            echo getUserButton();
            exit;
        }
        if (isset($_GET['test'])) {
            include_once '../wechat/usersdk.php';
            echo json_encode(usersdk::getTaglist(), JSON_UNESCAPED_UNICODE);

//            $data=curlTest();
//            $data = sendKFMessage('o_Luwt9OgYENChNK0bBZ4b1tl5hc', '你好');
//            echo $data;
            exit;
        }

    }

    exit;
}
header('location:index.php');

exit;

