<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/27
 * Time: 9:58
 */
//$data=file_get_contents('php://input');



include_once 'includePackage.php';
session_start();
//pdoInsert('partner_tbl',array('p_code'=>'aaa','password'=>md5('aaa')),'update');
header('location: console/index.php');