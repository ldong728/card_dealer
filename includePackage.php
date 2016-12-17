<?php
//以下为测试公众号用
//define('APP_ID','wx03393af10613da23');
//define('APP_SECRET','40751854901cc489eddd055538224e8a');
//define('WEIXIN_ID','gh_964192c927cb');
//define('MCH_ID','now is null');
//define('KEY','now is null');
//define("TOKEN", "godlee");
//define('DOMAIN',"mmzrb");
//define('NOTIFY_URL',"now is null");
//define("DB_NAME","gshop_db");
//define("DB_USER","gshopUser");
//define("DB_PSW","cT9vVpxBLQaFQYrh");
//$mypath = $_SERVER['DOCUMENT_ROOT'] . '/'.DOMAIN;   //用于直接部署


define('ADMIN','hll');
define('PASSWORD','admin');
////以下为测试号专用
//define('APP_ID','wx03393af10613da23');
//define('APP_SECRET','40751854901cc489eddd055538224e8a');
//define('WEIXIN_ID','gh_964192c927cb');
//define('MCH_ID','now is null');
//define('KEY','now is null');
//define('AES',false);
//define('ENCODE_KEY','aJXVYzQ9N4KMijdo0rpAo0iwKyw4WQr2WHFaXt9lNIy');


//以下为gooduo网络支付测试专用
define('APP_ID','wx148e8bf82333c4f8');
define('APP_SECRET','a3a20611f28435b167db7f0d141b96ca');
define('WEIXIN_ID','gh_ddf169a00667');
define('MCH_ID','');
define('KEY','');
define('AES',true);
define('ENCODE_KEY','fpze06HXzAuimYRyJcVGP3PlTkkDMMCmKswYDYlkdgL');

define("TOKEN", "godlee");
define('DOMAIN',"/card_dealer");
define('NOTIFY_URL',"now is null");
define('DB_IP','localhost');
define("DB_NAME","gooduophptest");
define("DB_USER","gooduophptest");
define("DB_PSW","gooduophptest");
$mypath = $_SERVER['DOCUMENT_ROOT'] .DOMAIN;   //用于直接部署
$template_key_order='XpZKkl2LFqxN95XpKFRKcR7Dxu1Nh9ZCj3ILRzrbMUY';//模板网购成功通知
$template_key_express='OWQiu_I2B-ZpxPDMrJpxU0al1fNN-onZE7uGeUTtcks';//模板快递物流提醒
$template_key_gainshare='Z_GWJKcBIwvjItkjjiUlQMZCX8CLb9PQR2CQ_HvDujU';//佣金分配提醒
$template_key_readyShip='WVCFi95PeBWyppGfzu5dUflTzfTUu7zaCCT-2nwxVAQ';//订单待发货提醒

include_once $mypath . '/includes/magicquotes.inc.php';
include_once $mypath . '/includes/db.inc.php';
include_once $mypath . '/includes/helpers.inc.php';
//include_once $mypath.'/includes/db.class.php';
include_once $mypath . '/includes/card_deal.php';
header("Content-Type:text/html; charset=utf-8");