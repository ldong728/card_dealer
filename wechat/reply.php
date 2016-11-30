<?php


function normalReply($weixin, $msg)
{
    mylog('nomal reply');
    sendCardByKfMessage($msg['from'],'pubtTtxxhsxjGKHHuEWnrXVdpvcg');
//    if ($msg['MsgType'] == 'voice') {
//        sendKFMessage($msg['FromUserName'], '已为您接入人工客服，请稍候');
//        $weixin->toKFMsg();
//        updateWechatMode($msg['from'], 'kf');
//    } elseif ($msg['MsgType'] == 'img') {
////         mylog('type:'.$msg['MsgType']);
////         $weixin->replyText('你好');
//    } else {
//        $content = $msg['content'];
//        if (preg_match('/^dy[0-9]\d*$/', $content)) {
//            $weixin->replyText(expressQuery($msg, $content));
//        }
//
//    }

}


function expressQuery($msg, $str)
{
    $query = pdoQuery('user_express_query_view', null, array('id' => $str), ' limit 1');
    $content = '订单' . $str;
    if ($row = $query->fetch()) {
        if ($row['c_id'] == $msg['from']) {
            if ($row['express_order'] != null) {
                $name = $row['express_name'];
                $eorder = $row['express_order'];
                $content .= '已发货' . "/n" . '物流公司：' . $name . "/n" . '物流单号：' . $eorder;
            } else {
                $content .= '尚未发货';
            }
        } else {
            $content = '无法查询他人创建的订单';
        }
    } else {
        $content .= '不存在，请检查输入';
    }
    return $content;
}