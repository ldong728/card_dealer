<?php


class interfaceHandler
{
    private $currentToken = '';
    private $gettedTime = 0;
    private $expiresIn = 7200;
    private static $handler;
    private $diable = false; //access token 作废标识，如返回码为40001（access token无效）则此标识为真
    private $tryCount = 0;

    public function __construct($id)
    {
        $this->weixinId = $id;
        $this->reflashAccessToken();
    }

    public function reflashAccessToken()
    {
        if ($this->currentToken == '' || $this->gettedTime == 0) {
            $this->getTokenLocal();

        }
        if ($this->gettedTime + $this->expiresIn < time() - 100) {
            $this->diable = false;
            $this->getTokenOnLine();
        } elseif ($this->diable && (time() - $this->gettedTime > 5)) {
            $this->diable = false;
            $this->getTokenOnLine();
        }


    }

    private function getTokenOnLine()
    {
        $jsonToken = file_get_contents('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . APP_ID . '&secret=' . APP_SECRET);
        $geted = json_decode($jsonToken, true);
        $geted['gettedTime'] = time();
        $this->currentToken = $geted['access_token'];
        $this->gettedTime = $geted['gettedTime'];
        $this->expiresIn = $geted['expires_in'];
        $reJson = json_encode($geted);
        file_put_contents($GLOBALS['mypath'] . '/tokens/token.json', $reJson);
//        mylog($this->weixinId.': getTokenOnLine');
    }

    private function getTokenLocal()
    {
        $tokenFileData = file_get_contents($GLOBALS['mypath'] . '/tokens/token.json');
        $token = json_decode($tokenFileData, true);
        $this->currentToken = $token['access_token'];
        $this->gettedTime = $token['gettedTime'];
        $this->expiresIn = $token['expires_in'];
    }

    private function clearToken()
    {
        $this->currentToken = '';
        $this->gettedTime = 0;
        $this->expiresIn = 7200;
    }

    public function sendPost($url, $request_data)
    {
        $url = $this->replaceAccessToken($url);
        $postdata = http_build_query($request_data);
        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-type:application/x-www-form-urlencoded',
                'content' => $postdata,
                'timeout' => 15 * 60 // 超时时间（单位:s）
            )
        );
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        return $result;
    }

    public function sendGet($str)
    {
        $str = $this->replaceAccessToken($str);
        $getted = file_get_contents($str);
        return $getted;
    }

    public function getByCurl($url)
    {
        $str = $this->replaceAccessToken($url);
//        wxlog($url);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_URL, $str);
        $data = curl_exec($curl);
        curl_close($curl);


        return $data;
    }

    private function replaceAccessToken($url)
    {
        $this->reflashAccessToken();
        $token=$this->currentToken;
//        $result = preg_replace('/ACCESS_TOKEN/',$token, $url);
        $result=str_replace('ACCESS_TOKEN',$token,$url);
        return $result;
    }

    public function postByCurl($remote_server, $post_string)
    {
        $replaced_server = $this->replaceAccessToken($remote_server);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $replaced_server);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        $data = curl_exec($ch);
        curl_close($ch);
        $dataArray = json_decode($data, true);
        if (40001 == $dataArray['errcode']) {
            if ($this->tryCount < 4) {
                $this->tryCount++;
                $this->diable = true;
                $this->clearToken();
                return $this->postByCurl($remote_server, $post_string);
            }
        } else {
            $this->tryCount = 0;
        }
        return $data;
    }

    public function postJsonByCurl($remote_server, $json_string)
    {
        $replaced_server = $this->replaceAccessToken($remote_server);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $replaced_server);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($json_string))
        );
        $data = curl_exec($ch);
        curl_close($ch);
        $dataArray = json_decode($data, true);
        if (40001 == $dataArray['errcode']) {
            if ($this->tryCount < 4) {
                mylog($this->tryCount);
                $this->tryCount++;
                $this->diable = true;
                $this->clearToken();
                $temp=$this->postJsonByCurl($remote_server, $json_string);
                return $temp;
            }
        } else {
            $this->tryCount = 0;
        }
        return $data;
    }

    public function postArrayByCurl($remote_server, $sArray)
    {
        $replaced_server = $this->replaceAccessToken($remote_server);
        $jsonData = json_encode($sArray, JSON_UNESCAPED_UNICODE);
        $data = $this->postJsonByCurl($replaced_server, $jsonData);
        return $data;
    }

    public function uploadFileByCurl($remote_server, $file, $field = 'media', $exraInf = null)
    {
        $replaced_server = $this->replaceAccessToken($remote_server);
        $fields[$field] = '@' . $file;
        if ($exraInf != null) {
            foreach ($exraInf as $k => $v) {
                $fields[$k] = $v;
            }
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $replaced_server);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        curl_close($ch);
        $dataArray = json_decode($data, true);
        if (40001 == $dataArray['errcode']) {
            if ($this->tryCount < 4) {
                $this->tryCount++;
                $this->diable = true;
                $this->clearToken();
                return $this->uploadFileByCurl($remote_server, $file, $field = 'media', $exraInf = null);
            }
        } else {
            $this->tryCount = 0;
        }
        return $data;

//        return $data;

    }

    public static function getHandler()
    {
        if (!interfaceHandler::$handler) {
            return new interfaceHandler(WEIXIN_ID);
        } else {
            return interfaceHandler::$handler;
        }
    }


} 