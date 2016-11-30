<?php
include_once '../includePackage.php';
include_once 'upload.class.php';
session_start();
if(isset($_SESSION['login'])) {
    if(isset($_FILES['title-img-up'])){
        $uploader=new uploader('title-img-up');
        $md5=md5_file($_FILES['title-img-up']['tmp_name']);
        $uploader->upFile($md5);
        $inf=$uploader->getFileInfo();
        $jsonInf=json_encode($inf,JSON_UNESCAPED_UNICODE);
        echo $jsonInf;
        exit;
    }
    if(isset($_FILES['parts-img-up'])){
        $uploader=new uploader('parts-img-up');
        $uploader->upFile($_GET['g_id'].'_'.time().rand(1000,9999));
        $inf=$uploader->getFileInfo();
        $jsonInf=json_encode($inf,JSON_UNESCAPED_UNICODE);

        if('SUCCESS'==$inf['state']) {
            $temp=pdoQuery('g_image_tbl',null,array('g_id'=>$_GET['g_id']),'limit 1');
            if(!$row=$temp->fetch()){
                pdoInsert('g_image_tbl', array('g_id' => $_GET['g_id'], 'url' => $inf['url'], 'remark' => $inf['md5'],'front_cover'=>'1'), 'ignore');
//                mylog("create record");
            }else{
                pdoUpdate('g_image_tbl',array('remark'=>$inf['md5'],'url'=>$inf['url'],'front_cover'=>'1'),array('g_id'=>$_GET['g_id']));
                $query=pdoQuery('image_view',null,array('remark'=>$row['remark']), ' limit 1');
                if(!$t=$query->fetch()){
                    unlink('../'.$row['url']);
//                    mylog('unlink"../'.$row['url']);
                }else{
//                    mylog('not unlink');
                }

            }

        }
//        mylog($jsonInf);
        echo $jsonInf;
        exit;
    }
    if(isset($_GET['proImgUp'])){
        foreach($_FILES as $k=>$v) {
            $uploader = new uploader($k);
            $uploader->upFile($k);
            $inf = $uploader->getFileInfo();
            $jsonInf = json_encode($inf, JSON_UNESCAPED_UNICODE);
            if ('SUCCESS' == $inf['state']) {
                pdoUpdate('promotions_tbl', array( 'img' => $inf['url']), array('id' => $_GET['proImgUp']));
                echo $jsonInf;
            }
        }
        exit;
    }
    if(isset($_GET['index_remark_img'])){
        foreach($_FILES as $k=>$v) {
            $uploader = new uploader($k);
            $uploader->upFile($k);
            $inf = $uploader->getFileInfo();
            $jsonInf = json_encode($inf, JSON_UNESCAPED_UNICODE);
//            mylog($jsonInf);
            if ('SUCCESS' == $inf['state']) {
                pdoUpdate('index_remark_tbl', array( 'img' => $inf['url']), array('id' => $_GET['index_remark_img']));
                echo $jsonInf;
            }
        }
        exit;
    }
    if(isset($_FILES['card-img-up'])){
        $uploader = new uploader('card-img-up');
        $uploader->upFile('cardLogo');
        $inf=$uploader->getFileInfo();
        include_once '../wechat/cardManager.php';
        $logo=uploadLogo($GLOBALS['mypath'].'/'.$inf['url']);
        if($logo!='error'){
            $inf['logo']=$logo;

            echo json_encode($inf);
        }else{
            $inf['state']='logo Error';
            echo json_encode($inf);
        }
    }
    exit;
}
function fileFilter($file, array $type, $size)
{
    if (in_array($file['type'], $type) && $file['size'] < $size) {
        if ($file['error'] > 0) {
            return false;
        } else {
            return true;
        }
    } else {
        return false;
    }
}
?>