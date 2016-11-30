<?php
include_once '../includePackage.php';
session_start();

if (isset($_SESSION['login'])) {
    if (isset($_POST['reflashUsers'])) {
        include_once '../wechat/serveManager.php';
        $inf = getOpenidList();
        $list = json_decode($inf, true);
        foreach ($list['data']['openid'] as $row) {
            getUserInf($row);
        }
        echo 'ok';

    }

    if (isset($_POST['altGroup'])) {
        mylog('altGroupname');
        $name = $_POST['name'];
        $id = $_POST['id'];
        include_once '../wechat/serveManager.php';
        if (0 == $id) {
            $inf = createGroup($name);
        } else {
            $inf = altGroup($name, $id);
        }

        echo $inf;
        exit;
    }
    if (isset($_POST['deleteGroup'])) {
        $id = $_POST['id'];
        include_once '../wechat/serveManager.php';
        $inf = deleteGroup($id);
        echo $inf;
        exit;

    }

    if (isset($_POST['groupQr'])) {
        $group_id = $_POST['group_id'];
        $url = 'http://' . $_SERVER['HTTP_HOST'] . DOMAIN . '/index.php?share=' . $group_id;
        echo urlencode($url);
        exit;
    }
    if (isset($_POST['setTitle'])) {
        $newsid = $_POST['newsid'];
        $type = $_POST['stu'] == 'true' ? 'title' : 'normal';
        pdoUpdate('news_tbl', array('type' => $type), array('id' => $newsid));
        echo 'ok';
        exit;
    }
    if (isset($_POST['reflashNews'])) {
        include_once '../wechat/serveManager.php';
        $totalcount = getMediaCount();
        $totalcount = json_decode($totalcount, true);
        $newsNum = $totalcount['news_count'];
        $local = pdoQuery('news_tbl', array('count(*) as num'), array('source' => 'wechat'), null);
        $local = $local->fetch();
        $localNum = $local['num'];
        if ($newsNum - $localNum > 0) {
            $news = getMediaList('news', 0, $newsNum - $localNum);
            foreach ($news['item'] as $row) {
                $media_id = $row['media_id'];
                $title_img = 'img/' . $media_id . '.jpg';
                $title = $row['content']['news_item'][0]['title'];
                $digest = $row['content']['news_item'][0]['digest'];
                $content = $row['content']['news_item'][0]['content'];
                $url = $row['content']['news_item'][0]['url'];
                $create_time = $row['content']['update_time'];
                if (!file_exists('../' . $title_img)) {
                    $img = getFromUrl($row['content']['news_item'][0]['thumb_url']);
                    file_put_contents('../' . $title_img, $img);
                }
                $value[] = array('media_id' => $media_id, 'title' => addslashes($title), 'digest' => addslashes($digest), 'title_img' => $title_img, 'content' => addslashes($content), 'url' => $url, 'create_time' => $create_time);
//                pdoInsert('news_tbl',array('media_id'=>$media_id,'title'=>addslashes($title),'digest'=>addslashes($digest),'title_img'=>$title_img,'content'=>addslashes($content),'url'=>$url,'create_time'=>$create_time),'ignore');
            }
            if (isset($value)) {
                pdoBatchInsert('news_tbl', $value, 'ignore');
            }
        }
        echo 'ok';
        exit;
    }
    if (isset($_POST['reflashSingleNews'])) {
        $media_id = $_POST['media_id'];
        $title_img = 'img/' . $media_id . '.jpg';
        include_once '../wechat/serveManager.php';
        $inf = array('media_id' => $media_id);
        $inf = json_encode($inf);
        $news_inf = getMedia($inf);
//        mylog($news_inf);
        $newsInf = json_decode($news_inf, true);
        $new = $newsInf['news_item'][0];
//        mylog(json_encode($new,JSON_UNESCAPED_UNICODE));
        $img = getFromUrl($new['thumb_url']);
        file_put_contents('../' . $title_img, $img);

        pdoUpdate('news_tbl', array('title' => addslashes($new['title']), 'digest' => addslashes($new['digest']), 'content' => addslashes($new['content']), 'url' => $new['url'], 'source' => 'wechat'), array('media_id' => $media_id));
        return 'ok';

    }
    if (isset($_POST['changeCategory'])) {
        $newsId = $_POST['newsId'];
        $category = $_POST['category'];
        pdoUpdate('news_tbl', array('category' => $category), array('id' => $newsId));
        echo 'ok';
        exit;
    }
    if (isset($_POST['deleteNews'])) {
        pdoDelete('news_tbl', array('media_id' => $_POST['media_id']));
        if ('wechat' == $_POST['source']) {
            include_once '../wechat/serveManager.php';
            $inf = deleteMedia($_POST['media_id']);

        }
        echo 'ok';
    }
    if (isset($_POST['sendNotice'])) {
        $newsId = $_POST['newsId'];
        $newsInf = pdoQuery('news_tbl', null, array('id' => $newsId), ' limit 1');
        $newsInf = $newsInf->fetch();
        $notice_id = pdoInsert('notice_tbl', array('title' => addslashes($newsInf['title']), 'intro' => addslashes($newsInf['digest']), 'title_img' => $newsInf['title_img'], 'inf' => addslashes($newsInf['content']), 'create_time' => time()));
        pdoUpdate('news_tbl', array('type' => 'notice'), array('id' => $newsId));
        echo $notice_id;
        exit;
    }
    if (isset($_POST['changeGroupSingle'])) {
        include_once '../wechat/serveManager.php';
        $openid = $_POST['openid'];
//        $inf=getUnionId($openid);
//        echo json_encode($inf,JSON_UNESCAPED_UNICODE);
        $groupid = $_POST['groupid'];
        $userReg = pdoQuery('user_reg_tbl', null, array('openid' => $openid), ' limit 1');
        if ($reg = $userReg->fetch() || $groupid == '0') {
            $data = changeGroup($openid, $groupid);
            $inf = json_decode($data, true);
            if ($inf['errcode'] == 0) {
                pdoUpdate('user_tbl', array('groupid' => $groupid), array('openid' => $openid));
                pdoUpdate('user_reg_tbl', array('groupid' => $groupid), array('openid' => $openid));
            }
            echo $data;
            exit;
        } else {
            echo '{errcode:1}';
            exit;
        }

    }
    if (isset($_POST['ConfirmSendNotice'])) {
        $groupid = $_POST['groupId'];
        $noticeid = $_POST['noticeId'];
        $pre = $_POST['pre'];
        $noticeInf = pdoQuery('notice_tbl', array('situation'), array('id' => $noticeid), ' limit 1');
        $noticeInf = $noticeInf->fetch();
        if (0 == $noticeInf['situation']) {
            include_once '../wechat/serveManager.php';
            pdoUpdate('notice_tbl', array('pre_notice' => addslashes($pre), 'groupid' => $groupid, 'situation' => '1', 'create_time' => time()), array('id' => $noticeid));
            $re = textSandAll($pre, $groupid);
            echo $re;
        } else {
            echo 'not ok';
        }
        exit;

    }
    if (isset($_POST['bbs'])) {
        if (isset($_POST['deleteTopic'])) {
            $t_id = $_POST['deleteTopic'];
            pdoTransReady();
            try {
                pdoDelete('bbs_topic_tbl', array('id' => $t_id));
                pdoDelete('bbs_reply_tbl', array('t_id' => $t_id));
                pdoDelete('bbs_topic_img_tbl', array('t_id' => $t_id));
                pdoDelete('bbs_reply_img_tbl', array('t_id' => $t_id));
                pdoCommit();
            } catch (PDOException $e) {
                pdoRollBack();
            }
            echo 'ok';
            exit;
        }
        if (isset($_POST['altPriority'])) {
            $t_id = $_POST['t_id'];
            pdoUpdate('bbs_topic_tbl', array('priority' => $_POST['altPriority']), array('id' => $t_id));
            echo 'ok';
            exit;
        }
        if (isset($_POST['altPublic'])) {
            $t_id = $_POST['t_id'];
            pdoUpdate('bbs_topic_tbl', array('public' => $_POST['altPublic']), array('id' => $t_id));
            echo 'ok';
            exit;
        }
    }
    if (isset($_POST['std'])) {
        if (isset($_POST['createQuestion'])) {
            $content = $_POST['content'];
            $type = $_POST['type'];
            $option = $_POST['option'];
            pdoTransReady();
            try {
                $id = pdoInsert('std_question_tbl', array('type' => $type, 'content' => addslashes($content), 'create_time' => time()));
                foreach ($option as $row) {
                    $optionList[] = array(
                        'q_id' => $id,
                        'content' => $row['content'],
                        'correct' => $row['correct'],
                        'q_type' => $type
                    );
                }
                pdoBatchInsert('std_option_tbl', $optionList);
                pdoCommit();
            } catch (PDOException $e) {
                mylog($e->getMessage());
                pdoRollBack();
                echo 'error';
                exit;
            }
            echo 'ok';
            exit;
        }
        if (isset($_POST['editQuestion'])) {
            $content = $_POST['content'];
            $q_id = $_POST['q_id'];
            $option = $_POST['option'];
            pdoTransReady();
            try {
                $id = pdoUpdate('std_question_tbl', array('content' => addslashes($content), 'create_time' => time()), array('id' => $q_id));
                foreach ($option as $row) {
                    pdoUpdate('std_option_tbl', array('content' => $row['content'], 'correct' => $row['correct']), array('id' => $row['id']));
                }
                pdoCommit();
            } catch (PDOException $e) {
                mylog($e->getMessage());
                pdoRollBack();
                echo 'error';
                exit;
            }
            echo 'ok';
            exit;
        }
        if (isset($_POST['deleteQuestion'])) {
            $q_id = $_POST['q_id'];
            pdoTransReady();
            try {
                pdoDelete('std_question_tbl', array('id' => $q_id));
                pdoDelete('std_option_tbl', array('q_id' => $q_id));
                pdoCommit();
            } catch (PDOException $e) {
                mylog($e);
                pdoRollBack();
            }
            echo 'ok';
            exit;
        }
    }
    if (isset($_POST['operator'])) {//操作员权限
        if ($_POST['altPms']) {
            if ($_POST['stu'] == 'true') {
                pdoInsert('op_pms_tbl', array('o_id' => $_POST['id'], 'pms' => $_POST['altPms']), 'ignore');
                echo 'ok';
            } else {
                pdoDelete('op_pms_tbl', array('o_id' => $_POST['id'], 'pms' => $_POST['altPms']));
                echo 'ok';
            }
            exit;
        }
        if (isset($_POST['altName'])) {
            pdoUpdate('operator_tbl', array('name' => $_POST['altName']), array('id' => $_POST['id']));
            echo 'ok';
            exit;
        }
        if (isset($_POST['altPwd'])) {
            pdoUpdate('operator_tbl', array('pwd' => $_POST['altPwd'], 'md5' => md5($_POST['altPwd'])), array('id' => $_POST['id']));
            echo 'ok';
            exit;
        }
        if (isset($_POST['new'])) {
            pdoInsert('operator_tbl', array('name' => $_POST['new'], 'pwd' => $_POST['pwd'], 'md5' => md5($_POST['pwd'])), 'ignore');
            echo 'ok';
            exit;
        }
        exit;
    }
    if (isset($_POST['configJmCate'])) {
        mylog();
        $id = $_POST['id'];
        $name = $_POST['name'];
        switch ($_POST['type']) {
            case 'altf': {
                pdoUpdate('jm_cate_tbl', array('name' => $name), array('id' => $id));
                echo 'ok';
                break;
            }
            case 'alts': {
                pdoUpdate('jm_cate_tbl', array('name' => $name), array('id' => $id));
                echo 'ok';
                break;
            }
            case 'addf': {
                pdoInsert('jm_cate_tbl', array('name' => $name));
                echo 'ok';
                break;
            }
            case 'adds': {
                pdoTransReady();
                try {
                    pdoInsert('jm_cate_tbl', array('f_id' => $id, 'name' => $name,));
                    $sql = 'update jm_cate_tbl set sub_num=sub_num+1 where id=' . $id;
                    exeNew($sql);
                    pdoCommit();
                } catch (PDOException $e) {
                    mylog($e->getMessage());
                    pdoRollBack();
                }
                echo 'ok';
                break;
            }
        }
        exit;
    }
    if(isset($_POST['delJmCate'])){
        $id=$_POST['id'];
        $cateinf=pdoQuery('jm_cate_tbl',array('sub_num'),array('id'=>$id),' limit 1');
        $cateinf=$cateinf->fetch();
        if($cateinf['sub_num']==0){
            pdoTransReady();
            try{
                pdoUpdate('jm_news_tbl',array('category'=>-1),array('category'=>$id));
                pdoDelete('jm_cate_tbl',array('id'=>$id));
                pdoCommit();
            }catch(PDOException $e){
                mylog($e->getMessage());
                pdoRollBack();
            }
            echo '1';
            exit;
        }
        echo '0';
        exit;

    }
    if(isset($_POST['altJmType'])){
        $type=$_POST['type'];
        $id=$_POST['id'];
        pdoUpdate('jm_news_tbl',array('type'=>$type),array('id'=>$id));
        echo 'ok';
        exit;
    }
    if(isset($_POST['delJmNews'])){
        $id=$_POST['id'];
        pdoDelete('jm_news_tbl',array('id'=>$id));
        echo 'ok';
        exit;
    }
    if(isset($_POST['getNews'])){
        $mode=$_POST['mode'];
        $id=$_POST['id'];
        if($mode==3){
            $content=pdoQuery('jm_news_tbl',array('content'),array('id'=>$id),' limit 1');
            $content=$content->fetch();
        }
        if($mode!=3){
            $content=pdoQuery('news_tbl',array('content'),array('id'=>$id),' limit 1');
            $content=$content->fetch();
        }
        echo $content['content'];
        exit;
    }
}
?>