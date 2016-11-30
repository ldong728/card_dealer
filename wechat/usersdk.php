<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/11/28
 * Time: 10:08
 */
include_once 'interfaceHandler.php';

class usersdk
{

    private $openId;

    public function __construct($openId)
    {
        $this->openId = $openId;
    }

    public function getUserInf()
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=ACCESS_TOKEN&openid=' . $this->openId . '&lang=zh_CN';
        $jsonData = interfaceHandler::getHandler()->getByCurl($url);
        $inf = json_decode($jsonData, true);
        if(!isset($inf['errcode'])){
            if (!isset($inf['nickname']) || $inf['nickname'] == '') {
                $inf['nickname'] = '游客';
                $inf['headimgurl'] = 'images/no_img_user.jpg';
                $inf['subscribe'] = 0;
//            $jsonData = json_encode($inf, JSON_UNESCAPED_UNICODE);
            }

//        return json_decode($jsonData, true);
            return $inf;
        }else{
            mylog($jsonData);
            return null;
        }

    }

    public function syncUserInf($userTblName){
//        if(isset)
        $userInfLocal=pdoQuery($userTblName,null,array('openid'=>$this->openId),' limit 1');
        $userInfLocal=$userInfLocal->fetch();
        if(!$userInfLocal||($userInfLocal['update_time']+3600*24)<time()){
            if($inf=$this->getUserInf()){
                $inf['update_time']=time();
                pdoInsert($userTblName,$inf,'update');
                if(!$userInfLocal)$inf['user_level']=0;
                else $inf['user_level']=$userInfLocal['user_level'];
                return $inf;
            }else{
                return null;
            }

        }else{
            return $userInfLocal;
        }


    }

    public function addTag($tagId)
    {
        $openidList = [$this->openId];
        return usersdk::batchAddTag($tagId, $openidList);
    }

    public function removeTag($tagId)
    {
        $openidList = [$this->openId];
        return usersdk::batchremoveTag($tagId, $openidList);
    }

    public function getUserTag()
    {
        $data = array('openid' => $this->openId);
        $back = interfaceHandler::getHandler()->postArrayByCurl('https://api.weixin.qq.com/cgi-bin/tags/getidlist?access_token=ACCESS_TOKEN', $data);
        return json_decode($back, true);
    }

    public static function createTag($name)
    {
        $data = array('tag' => array('name' => $name));
        $back = interfaceHandler::getHandler()->postArrayByCurl('https://api.weixin.qq.com/cgi-bin/tags/create?access_token=ACCESS_TOKEN', $data);
        return json_decode($back, true);
    }

    public static function getTaglist()
    {
        $data = interfaceHandler::getHandler()->getByCurl('https://api.weixin.qq.com/cgi-bin/tags/get?access_token=ACCESS_TOKEN');
        return json_decode($data, true);
    }

    public static function editTag($tagId, $tagName)
    {
        $data = array('tag' => array('id' => $tagId, 'name' => $tagName));
        $back = interfaceHandler::getHandler()->postArrayByCurl('https://api.weixin.qq.com/cgi-bin/tags/update?access_token=ACCESS_TOKEN', $data);
        return json_decode($back, true);
    }

    public static function deleteTag($tagID)
    {
        $data = array('tag' => array('id' => $tagID));
        $back = interfaceHandler::getHandler()->postArrayByCurl('https://api.weixin.qq.com/cgi-bin/tags/delete?access_token=ACCESS_TOKEN', $data);
        return json_decode($back, true);
    }

    public static function getTagUser($tagId, $nextOpenId = 0)
    {
        if ($nextOpenId) {
            $data = array('tagid' => $tagId, 'next_openid' => $nextOpenId);
        } else {
            $data = array('tagid' => $tagId);
        }
        $back = interfaceHandler::getHandler()->postArrayByCurl('https://api.weixin.qq.com/cgi-bin/user/tag/get?access_token=ACCESS_TOKEN', $data);
        return json_decode($back, true);
    }

    public static function batchAddTag($tagId, array $openidList)
    {
        $data = array('openid_list' => $openidList, 'tagid' => $tagId);
        $back = interfaceHandler::getHandler()->postArrayByCurl('https://api.weixin.qq.com/cgi-bin/tags/members/batchtagging?access_token=ACCESS_TOKEN', $data);
        return json_encode($back, true);
    }

    public static function batchRemoveTag($tagId, array $openidList)
    {
        $data = array('openid_list' => $openidList, 'tagid' => $tagId);
        $back = interfaceHandler::getHandler()->postArrayByCurl('https://api.weixin.qq.com/cgi-bin/tags/members/batchuntagging?access_token=ACCESS_TOKEN', $data);
        return json_encode($back, true);
    }


}