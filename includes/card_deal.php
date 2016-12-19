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
function setOrderStu($orderId,$stu,$operator=-1,$paymode=0){
    pdoUpdate('card_order_tbl', array('status' => $stu), array('card_order_id' => $orderId));
    pdoInsert('order_record_tbl',array('order_id'=>$orderId,'event'=>$stu,'pay_mode'=>$paymode,'operator_id'=>$operator));
}

/**将长期未评价和未付款的订单给出默认好评，或取消
 * @param int $time
 * @return bool
 */

function clearOrders($time=TIME_OUT){
    pdoUpdate('order_tbl',array('stu'=>'7'),array('stu'=>'0'),' and to_days(order_time)<to_days(now())-'.$time);
    $dealed=pdoQuery('user_order_view',null,array('stu'=>'2'),' and to_days(order_time)<to_days(now())-'.$time);
    foreach ($dealed as $row) {
        $values[]=array(
            'c_id'=>$row['c_id'],
            'order_id'=>$row['o_id'],
            'g_id'=>$row['g_id'],
            'd_id'=>$row['d_id'],
            'score'=>'5',
            'public'=>1
        );
    }
    pdoUpdate('order_tbl',array('stu'=>'3'),array('stu'=>'2'),' and to_days(order_time)<to_days(now())-'.$time);
    if(isset($values)){
        pdoBatchInsert('review_tbl',$values);
    }
    return true;
}
function getProvince($pro){
    $datafile = 'config/province.inc.php';
    if(file_exists($datafile)){
        $config = include($datafile);
        return $config[$pro];
    }
}
function printViewMobile($addr,$title='abc',$hasInput=false){

    $mypath= $GLOBALS['mypath'];
    if($hasInput){
        include $mypath.'/mobile/templates/headerJs.html.php';

    }else{
        include $mypath.'/mobile/templates/header.html.php';
    }
//    echo 'header OK';

    include $mypath.'/'.$addr;
    include $mypath.'/mobile/templates/footer.html.php';
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
function getReview($g_id,$index=0,$limit=3){
    $query=pdoQuery('user_output_review_view',null,array('g_id'=>$g_id,'father_v_id'=>'-1'),
        ' and (c_id="'.$_SESSION['customerId'].'" or public=1) order by priority asc,review_time desc limit '.$index.','.$limit*5);
    $numquery=pdoQuery('review_tbl',array('count(*) as num'),array('g_id'=>$g_id,'father_v_id'=>'-1'),' and (public=1 or c_id="'.$_SESSION['customerId'].'")');
    $count=$numquery->fetch();
    $reviewcount=0;
    foreach ($query as $row) {
        if(!isset($review[$row['id']])){
            $review[$row['id']]=$row;
            $reviewcount++;
            if($reviewcount>$limit-1)break;
        }
        $review[$row['id']]['img'][]=$row['url'];
    }
    if(!isset($review))$review=array();
    if(!isset($count['num']))$count['num']=0;
    $back['num']=$count['num'];
    $back['inf']=$review;
    return $back;

}

function getGoodsPar($g_id,$sc_id){
    $back=array();
    $parmKeyQuery=pdoQuery('par_col_tbl',null,array('sc_id'=>$sc_id),' limit 25');
    $parmQuery=pdoQuery('parameter_tbl',null,array('g_id'=>$g_id),' limit 1');
    if($parm=$parmQuery->fetch()){
        foreach($parmKeyQuery as $parRow){
            $value=empty($parm[$parRow['col_name']])? $parRow['dft_value']:$parm[$parRow['col_name']];
            $back[$parRow['par_category']][]=array('col'=>$parRow['col_name'],'name'=>$parRow['name'],'value'=>$value);
        }
    }else{
        foreach($parmKeyQuery as $parRow){
            $back[$parRow['par_category']][]=array('col'=>$parRow['col_name'],'name'=>$parRow['name'],'value'=>$parRow['dft_value']);
        }
    }
    if(!isset($back))$back['']=array();
    return $back;
}
function getWechatMode($customerId){
    $query=pdoQuery('wechat_mode_tbl',null,array('c_id'=>$customerId),' limit 1');
    if($row=$query->fetch()){
        $mode=$row['mode'];
    }else{
        $mode='normal';
        pdoInsert('wechat_mode_tbl',array('c_id'=>$customerId,'mode'=>$mode),'ignore');
    }
    return $mode;
}
//function updateWechatMode($customerId,$mode){
//    pdoUpdate('wechat_mode_tbl',array('mode'=>$mode),array('c_id'=>$customerId));
//}
function getConfig($path){
    $data=file_get_contents($path);
    return json_decode($data,true);
}
function saveConfig($path,array $config){
    $data=json_encode($config);
    file_put_contents($path,$data);
}

function sdpPrice(array $list){//将数组中的price字段对应价格替换为分销商设置价格
    if(isset($_SESSION['sdp']['manage'])&&$_SESSION['sdp']['manage']['switch']=='on'){//开启进货模式，价格为供货商管理员设置价格或分销商折扣率
        if(isset($_SESSION['sdp']['wholesale'][$list['g_id']])){
            $list['price']=$_SESSION['sdp']['wholesale'][$list['g_id']];
        }else{
            $list['price']=$list['sale']*$_SESSION['sdp']['manage']['discount'];
        }
    }else{
        if(isset($_SESSION['sdp']['price'][$list['g_id']])) $list['price']=$_SESSION['sdp']['price'][$list['g_id']];//分销商自定义价格
        else $list['price']=$list['sale'];
    }
    return $list;
}
function sdpPartPrice(array $list,$idColName='g_id',$priceColName='part_sale'){//将数组中的price字段对应价格替换为分销商设置价格
    if(isset($_SESSION['sdp']['manage'])&&$_SESSION['sdp']['manage']['switch']=='on'){//开启进货模式，价格为供货商管理员设置价格或分销商折扣率
        if(isset($_SESSION['sdp']['wholesale'][$list[$idColName]])){
            $list[$priceColName]=$_SESSION['sdp']['wholesale'][$list[$idColName]];
        }else{
            $list[$priceColName]=$list[$priceColName]*$_SESSION['sdp']['manage']['discount'];
        }
    }else{
        if(isset($_SESSION['sdp']['price'][$list[$idColName]])) $list[$priceColName]=$_SESSION['sdp']['price'][$list[$idColName]];//分销商自定义价格
    }
    return $list;
}


function getSdpInf($index,$size,$level=0,array $filter=null){
        $orderby=isset($filter['order']) ? $filter['order']:'create_time';
        $rule=isset($filter['rule']) ? $filter['rule']:'desc';

    if(0==$level){
        $levelQuery=pdoQuery('sdp_level_tbl',array('level_id'),null,' where level_id>1');
        foreach ($levelQuery as $row) {
            mylog();
            $levelList[]=$row['level_id'];
        }
        $where['level']=$levelList;
    }else{
        $where['level']=$level;
        $whereStr='';
    }
    if(isset($filter['where'])){
        foreach ($filter['where'] as $k=>$v) {
            $where[$k]=$v;
        }
    }

    $count=pdoQuery('sdp_user_full_inf_view',array('count(*) as total_num'),$where,$whereStr);
    $c=$count->fetch();
    $return['count']=$c['total_num'];
    if($level>1){
        $infQuery=pdoQuery('sdp_root_full_inf_view',null,$where,$whereStr."order by $orderby $rule limit $index,$size");
    }else{
        $infQuery=pdoQuery('sdp_user_full_inf_view',null,$where,$whereStr."order by $orderby $rule limit $index,$size");
    }
    foreach ($infQuery as $row) {
        $return['sdp'][]=$row;
    }
    if(!isset($return['sdp']))$return['sdp']=array();
    return $return;

}
function searchSdp($keyWord,$index,$size,$level='0'){
    if($level>1){
        $infQuery=pdoQuery('sdp_root_full_inf_view',null,null,"where nickname like \"%$keyWord%\" or name like \"%$keyWord%\" or phone like \"%$keyWord%\" limit $index,$size");
    }else{
        $infQuery=pdoQuery('sdp_user_full_inf_view',null,null,"where nickname like \"%$keyWord%\" or name like \"%$keyWord%\" or phone like \"%$keyWord%\" limit $index,$size");
    }
    foreach ($infQuery as $row) {
        $return['sdp'][]=$row;
    }
    if(!isset($return['sdp']))$return['sdp']=array();
    return $return;
}
function getSubSdp(array $fullList,array $sdpList){
    $return=array();
    foreach ($fullList as $f => $s) {
        if(in_array($f,$sdpList)){
            $return[]=$s;
        }
    }
    if(count($return)>0){
        return array_merge($sdpList,getSubSdp($fullList,$return));
    }else{
        return $sdpList;
    }
}

/**账户验证函数
 * @param $sdp_id
 */
function verifyAccount($sdp_id){
    $recordQuery=pdoQuery('sdp_record_tbl',null,array('sdp_id'=>$sdp_id),' order by creat_time desc limit 1');
    $record=$recordQuery->fetch();
    $accountQuery=pdoQuery('sdp_account_tbl',null,array('sdp_id'=>$sdp_id),' limit 1');
    $account=$accountQuery->fetch();
    if($record['type']=='out')$record['fee']=-$record['fee'];
    $verify=md5($record['order_id'].$record['fee'].$account['total_balence'].SDP_KEY);
//    mylog($record['fee']);
//    mylog($account['total_balence']);
//    mylog($record['order_id'].$record['fee'].$account['total_balence'].SDP_KEY);
//    mylog($verify);
//    mylog($account['verify']);
    if($verify==$account['verify']){
        return $account['total_balence']>0?$account['total_balence']:true;
    }else{
        return false;
    }
}

/**获取佣金设置
 * @param string $root 分销商id
 * @param int $g_id 商品ID
 * @return array 返回佣金设置列表
 */

function getGainshareConfig($root="root",$g_id=-1){
    $gainshareQuery=pdoQuery('sdp_gainshare_tbl',null,null,' where root in ("root","'.$root.'") and (g_id=-1 or g_id='.$g_id.')  order by rank asc');

    foreach ($gainshareQuery as $gainshareRow) {
        $glist[$gainshareRow['g_id']][$gainshareRow['root']][]=$gainshareRow;
    }
    if(isset($glist[$g_id][$root])){
        $pre=$glist[$g_id][$root];
    }elseif(isset($glist[$g_id]['root'])){
        $pre=$glist[$g_id]['root'];
    }elseif(isset($glist[-1][$root])){
        $pre=$glist[-1][$root];
    }else{
        $pre=$glist[-1]['root'];
    }
    foreach ($pre as $prrow){
        $usedglist[]=array(
            'rank'=>$prrow['rank'],
            'value'=>$prrow['value']
        );
    }
    if(!$usedglist)$usedglist=array();
    return $usedglist;
}
function getsdpWholesale($level){
    $levelInf=pdoQuery('sdp_level_tbl',null,array('level_id'=>$level),' limit 1');
    $levelInf=$levelInf->fetch();
    $wsQuery=pdoQuery('sdp_wholesale_tbl',null,array('level_id'=>$level),null);
    foreach ($wsQuery as $row) {
        $wslist[$row['g_id']]=$row;
    }
    $gList=pdoQuery('user_tmp_list_view',null,null,' group by g_id');
    foreach ($gList as $row) {
        if(isset($wslist[$row['g_id']])){
            $ws= $wslist[$row['g_id']]['price'];
            $min=isset($wslist[$row['g_id']]['min_sell'])?$wslist[$row['g_id']]['min_sell']:$levelInf['min_sell']*$row['sale'];
            $max=isset($wslist[$row['g_id']]['max_sell'])?$wslist[$row['g_id']]['max_sell']:$levelInf['max_sell']*$row['sale'];
        }else{
            $ws=$levelInf['discount']*$row['sale'];
            $min=$levelInf['min_sell']*$row['sale'];
            $max=$levelInf['max_sell']*$row['sale'];
        }
        $wholesale[]=array(
            'g_id'=>$row['g_id'],
            'made_in'=>$row['made_in'],
            'produce_id'=>$row['produce_id'],
            'url'=>$row['url'],
            'sale'=>$row['sale'],
            'wholesale'=>$ws,
            'min_sell'=>$min,
            'max_sell'=>$max
        );
    }
//    mylog(getArrayInf($wholesale));
    return $wholesale;
}

function twoBfilter($sdp_id,$g_id){
    $sale=pdoQuery('g_detail_tbl',array('sale'),array('g_id'=>$g_id),' limit 1');
    $sale=$sale->fetch();
    $wholeSale=pdoQuery('sdp_wholesale_tbl',null,array('level_id'=>$_SESSION['sdp']['level'],'g_id'=>$g_id),' limit 1');
    if($ws=$wholeSale->fetch()){
        $cost=$ws['price'];
    }else{
        $cost=$sale['sale']*$_SESSION['sdp']['manage']['discount'];
    }
    $priceQuery=pdoQuery('sdp_price_tbl',null,array('sdp_id'=>sdp_id,'g_id'=>g_id),' limit 1');
    if($p=$priceQuery->fetch()){
        $price=$p['price'];
    }else{
        $price=$sale['sale'];
    }
    $gainshareList=getGainshareConfig($sdp_id,$g_id);
    $totalGs=0;
    foreach ($gainshareList as $row) {
     $totalGs+=$row['value'];
    }
    $cCost=(1-$totalGs)*$price;
    if($cCost<$cost){
        pdoDelete('sdp_gainshare_tbl',array('root'=>$sdp_id,'g_id'=>$g_id));
        return false;
    }else{
        return true;
    }
}

/**双向证书验证CURL
 * @param $url
 * @param $vars
 * @param int $second
 * @param array $aHeader
 * @return bool|mixed
 */
function curl_post_ssl($url, $vars, $second = 30, $aHeader = array())
{
    $ch = curl_init();
    //超时时间
    curl_setopt($ch, CURLOPT_TIMEOUT, $second);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //这里设置代理，如果有的话
    //curl_setopt($ch,CURLOPT_PROXY, '10.206.30.98');
    //curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    //以下两种方式需选择一种

    //第一种方法，cert 与 key 分别属于两个.pem文件
    //默认格式为PEM，可以注释
    curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
    curl_setopt($ch, CURLOPT_SSLCERT, '../cert/apiclient_cert.pem');
    //默认格式为PEM，可以注释
//    curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
    curl_setopt($ch, CURLOPT_SSLKEY, '../cert/apiclient_key.pem');
    curl_setopt($ch, CURLOPT_CAINFO, '../cert/rootca.pem');

    //第二种方式，两个文件合成一个.pem文件
//    curl_setopt($ch,CURLOPT_SSLCERT,getcwd().'/all.pem');

    if (count($aHeader) >= 1) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
    }

    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);
    $data = curl_exec($ch);
    if ($data) {
        curl_close($ch);
        return $data;
    } else {
        $error = curl_errno($ch);
        echo "call faild, errorCode:$error\n";
        curl_close($ch);
        return false;
    }
}
