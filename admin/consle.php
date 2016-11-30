<?php
/**
 * Created by PhpStorm.
 * User: godlee
 * Date: 2015/10/29
 * Time: 10:50
 */
include_once '../includePackage.php';
session_start();


if (isset($_SESSION['login'])) {
    if (isset($_GET['createNews'])) {
        $id = $_POST['id'];
        $title = addslashes(trim($_POST['title']));
        $digest = addslashes(trim($_POST['digest']));
        $title_img = isset($_POST['title_img']) ? $_POST['title_img'] : 'img/0.jpg';
        $content = addslashes($_POST['content']);
        if ($title != '' && $content != '') {
            switch ($_GET['createNews']) {
                case '1': {//创建图文
                    $value = array('title' => $title, 'digest' => $digest, 'title_img' => $title_img, 'content' => $content, 'source' => 'local', 'media_id' => 'local' . time() . rand(100, 999), 'create_time' => time());
                    if ($id > 0) $value['id'] = $id;
                    pdoInsert('news_tbl', $value, 'update');
                    header('location:index.php?newslist=1');
                    exit;
                    break;
                }
                case '2': {//创建通知
                    $sendNow = $_POST['sendNow'];
                    $value = array('title' => $title, 'intro' => $digest, 'title_img' => $title_img, 'inf' => $content, 'create_time' => time());
                    if ($id > 0) $value['id'] = $id;
                    $notice_id = pdoInsert('notice_tbl', $value, 'update');
                    if ($sendNow == '0') {
                        header('location:index.php?newslist=1');
                    } else {
                        header('location:index.php?sendNotice=' . $notice_id . '&notice_id=' . $notice_id);
                    }
                    exit;
                    break;
                }
                case '3': {
                    $cate = $_POST['jm_cate'] ? $_POST['jm_cate'] : -1;
                    $value =array('category' => $cate, 'title' => $title, 'title_img' => $title_img, 'content' => $content, 'create_time' => time());
                    if ($id > 0) $value['id'] = $id;
//                              mylog(json_encode($value));
//                              mylog($id);
                    pdoInsert('jm_news_tbl',$value,'update');
                    header('location:index.php?jm=1&jm_list=1');
                    exit;
                    break;

                }
            }


        } else {
            header('location:index.php?newslist=1');
            exit;
        }
        exit;

    }
    if (isset($_GET['getNotice'])) {//在预览框架中显示
        $css = '<style type="text/css">'
            . 'img {max-width:100%;}'
            . '</style>';

        $noticeId = $_GET['getNotice'];
        if ($noticeId == -1) {
            echo '预览';
            exit;
        }
        $notice = pdoQuery('notice_tbl', array('inf'), array('id' => $noticeId), ' limit 1');
        $notice = $notice->fetch();
        echo $css;
        echo $notice['inf'];
        exit;
    }
    if (isset($_GET['userdetail'])) {
        $openid = $_GET['userdetail'];
        $userinf = getUserInf($openid);
        $groupid = $userinf['groupid'];
        $markquery = pdoQuery('user_mark_view', null, array('openid' => $openid), null);
        $markStr = '';
        foreach ($markquery as $row) {
            $markStr .= ($row['notice_id'] . ',');
            $markList[] = $row;
        }
        if (isset($markList)) $markList = array();
        $markStr = rtrim($markStr, ',');
        $str = $markStr != '' ? ' and id not in(' . $markStr . ')' : '';
        $unmarkQuery = pdoQuery('notice_tbl', array('title', 'create_time'), array('situation' => 1, 'groupid' => $groupid), $str);
        $unmarkList = $unmarkQuery->fetchAll();
        $bbsTopic = pdoQuery('bbs_topic_tbl', array('count(*) as count'), array('open_id' => $openid), ' limit 1');
        $bbsTopic = $bbsTopic->fetch();
        $bbsReply = pdoQuery('bbs_reply_tbl', array('count(*) as count'), array('openid' => $openid), 'limit 1');
        $bbsReply = $bbsReply->fetch();
        $stdTest = pdoQuery('std_user_score_tbl', null, array('openid' => $openid), ' limit 5');
        $stdTest = $stdTest->fetchAll();
        printView('admin/view/user_detail.html.php', '详细信息');

    }

    //公众号操作
    if (isset($_GET['wechat'])) {
        include_once '../wechat/serveManager.php';
//        $re='';
        if (isset($_GET['createButton'])) {
            deleteButton();
            createButtonTemp();
            exit;
        }
        if (isset($_GET['createUniButton'])) {
            $glist = getGroupListOnline();
            foreach ($glist as $row) {
                if ($row['id'] > 99) {
                    echo $row['name'] . ':' . $row['id'];
                    $url = 'http://' . $_SERVER['HTTP_HOST'] . DOMAIN . '/mobile/controller.php?mainSite=1';
                    $button1sub1 = array('name' => '国防法规', 'type' => 'view', 'url' => $url . '&cate=1');
                    $button1sub2 = array('name' => '征兵信息', 'type' => 'view', 'url' => $url . '&cate=2');
//                    $button1sub3 = array('name' => '每月一课', 'type' => 'view', 'url' => $url . '&cate=4');
                    $button1sub4 = array('name'=>'军民融合','type'=>'view','url'=>'http://'.$_SERVER['HTTP_HOST'].DOMAIN.'/mobile/controller.php?jmrh=1&static=1');
                    $button1sub5 = array('name' => '军人荣誉', 'type' => 'view_limited', 'media_id' => 'mpDQKIcMlKu6mqA_Pa4i18ID0dTlEGSifZhS1Y9XWXk');
                    $button1 = array('name' => '兴武征程', 'sub_button' => array($button1sub1, $button1sub4, $button1sub2, $button1sub5));
//                    $button1 = array('name' => '兴武征程', 'sub_button' => array( $button1sub1, $button1sub4, $button1sub2));
                    $button2 = array('name' => '学习平台', 'type' => 'view', 'url' => 'http://' . $_SERVER['HTTP_HOST'] . DOMAIN . '/mobile/controller.php?study=1');
                    $button3sub1 = array('type' => 'click', 'name' => $row['name'], 'key' => 'moldule2');
                    $button3sub2 = array('type' => 'view', 'name' => '互动社区', 'url' => 'http://' . $_SERVER['HTTP_HOST'] . DOMAIN . '/mobile/controller.php?bbs=1');
                    $button3 = array('name' => '互动社区', 'sub_button' => array($button3sub1, $button3sub2));
                    $mainButton = array('button' => array($button1, $button2, $button3), 'matchrule' => array('group_id' => $row['id']));
                    $jsondata = json_encode($mainButton, JSON_UNESCAPED_UNICODE);
                    echo createUniButton($jsondata);
                }
            }
            exit;

            $url = 'http://' . $_SERVER['HTTP_HOST'] . DOMAIN . '/mobile/controller.php?mainSite=1';
            $button1sub1 = array('name' => '国防法规', 'type' => 'view', 'url' => $url . '&cate=1');
            $button1sub2 = array('name' => '征兵信息', 'type' => 'view', 'url' => $url . '&cate=2');
//            $button1sub3 = array('name' => '每月一课', 'type' => 'view', 'url' => $url . '&cate=4');
            $button1sub4 = array('name'=>'军民融合','type'=>'view','url'=>'http://'.$_SERVER['HTTP_HOST'].DOMAIN.'/mobile/controller.php?jmrh=1&static=1');
            $button1sub5 = array('name' => '军人荣誉', 'type' => 'view_limited', 'media_id' => 'mpDQKIcMlKu6mqA_Pa4i18ID0dTlEGSifZhS1Y9XWXk');
            $button1 = array('name' => '兴武征程', 'sub_button' => array($button1sub1, $button1sub4, $button1sub2, $button1sub5));
            $button2 = array('name' => '学习平台', 'type' => 'view', 'url' => 'http://' . $_SERVER['HTTP_HOST'] . DOMAIN . '/mobile/controller.php?mainSite=1');
            $button3sub1 = array('type' => 'click', 'name' => '互动', 'key' => 'moldule2');
            $button3sub2 = array('type' => 'click', 'name' => '互动社区', 'key' => 'bbs');
            $button3 = array('name' => '互动社区', 'sub_button' => array($button3sub1, $button3sub2));
            $mainButton = array('button' => array($button1, $button2, $button3), 'matchrule' => array('group_id' => 101));
            $jsondata = json_encode($mainButton, JSON_UNESCAPED_UNICODE);
            echo createUniButton($jsondata);
        }
        if (isset($_GET['getMenuInf'])) {
            echo getUserButton();
            exit;
        }
        if (isset($_GET['test'])) {
//            $data=curlTest();
            $data = sendKFMessage('o_Luwt9OgYENChNK0bBZ4b1tl5hc', '你好');
            echo $data;
            exit;
        }

    }

    exit;
}
header('location:index.php');
exit;

