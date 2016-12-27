<?php
include_once '../includePackage.php';
include_once 'upload.class.php';
session_start();
if(isset($_SESSION['login'])&&DOMAIN==$_SESSION['login']) {
    if(isset($_FILES['logo-up'])){
        $uploader = new uploader('logo-up');
        $uploader->upFile('cardLogo');
        $inf=$uploader->getFileInfo();
        mylog(getArrayInf($inf));
        include_once '../wechat/cardManager.php';
        $logo=uploadLogo($GLOBALS['mypath'].'/'.$inf['url']);
            $inf['logo']=$logo;
            echo json_encode($inf);
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