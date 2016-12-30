<?php
define('DEBUG',true);
//$province=null;
//$city=null;
//$area=null;

function html($text)
{
	return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

function htmlout($text)
{
	echo html($text);
}

function output($string){
    header("Content-Type:text/html; charset=utf-8");
    echo '<p class = "warning">'. $string.'</p>';

}
function formatOutput($string){
//    $str=html($string);
    $str=preg_replace('/___/','<input type="text"/>',$string);
    return $str;
}

function printInf($p){
    echo '<br/>'.'{';
    foreach ($p as $k=>$v) {
       echo $k.':  ';
        if(is_array($v)){
            printInf($v);
        }else{
            echo $v.',';
        }

    }
    echo '}';
}

//debug

function getArrayInf($array){
    $s='{';
    foreach ($array as $k=>$v) {
        $s.=$k.': ';
        if(is_array($v)){
            $s=$s.getArrayInf($v);
        }else{
            $s.=$v.',';
        }
    }
    $s=trim($s,',');
    return $s.'}';

}

function mylog($str='mark'){
    if(DEBUG) {
        $debugInfo=debug_backtrace();
        $message = $debugInfo[0]['file'].$debugInfo[0]['line'];
        $log = date('Y.m.d.H:i:s', time()) . ':'.$message.':' . $str . "\n";
        file_put_contents($GLOBALS['mypath'].'/log.txt', $log, FILE_APPEND);
    }
}

function ajaxBack($data=null,$errcode=0,$errmsg='ok'){
    $back=array('errcode'=>$errcode,'errmsg'=>$errmsg);
    if($data)$back['data']=$data;
    return json_encode($back,JSON_UNESCAPED_UNICODE);
}

//mysql格式转换
function timeUnixToMysql($time){
    return date('Y-m-d H:i:s', $time);

}

function timeMysqlToUnix($time){
    return strtotime($time);
}






//微信用
/**随机字符串生成器
 * @param int $length
 * @return string
 */
function getRandStr($length = 16)
{
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $str = "";
    for ($i = 0; $i < $length; $i++) {
        $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
}

/**
 * 格式化参数格式化成url参数
 */
function ToUrlParams(array $value)
{
    $buff = "";
    foreach ($value as $k => $v)
    {
        if($k != "sign" && $v != "" && !is_array($v)){
            $buff .= $k . "=" . $v . "&";
        }
    }

    $buff = trim($buff, "&");
    return $buff;
}

/**
 * 生成微信所需签名
 * @param $key
 * @return string
 */
function makeSign(array $value,$key)
{
    //签名步骤一：按字典序排序参数
    ksort($value);
    $string =ToUrlParams($value);
    //签名步骤二：在string后加入KEY
    if(''!=$key){
        $string = $string ."&key=".$key;
        //签名步骤三：MD5加密
    }
//    mylog($string);

    $string = md5($string);
    //签名步骤四：所有字符转为大写
    $result = strtoupper($string);
    return $result;
}

/**
 * @param array $values 待转化的数组
 * @return string
 * @throws WxPayException
 */
function toXml(array $values)
{
    if(!is_array($values)
        || count($values) <= 0)
    {
        return '<xml><error>数组错误</error></xml>';
    }

    $xml = "<xml>";
    foreach ($values as $key=>$val)
    {
        if (is_numeric($val)){
            $xml.="<".$key.">".$val."</".$key.">";
        }else{
            $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
        }
    }
    $xml.="</xml>";
    return $xml;
}
function xmlToArray($xmlData){
    $postStr = $xmlData;
    if (!empty($postStr)) {
        libxml_disable_entity_loader(true);
        $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        foreach ($postObj->children() as $child) {
            $msg[$child->getName()] = (string)$child;
        }
        return $msg;
    }
}
function signVerify($array){
    foreach ($array as $k => $v) {
        if('sign'==$k){
            $inValue=$v;
        }else{
            $data[$k]=$v;
        }
    }
    $outValue=makeSign($data,KEY);
    if($outValue==$inValue){
        return true;
    }else{
        return false;
    }

}


