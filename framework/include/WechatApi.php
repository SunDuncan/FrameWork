<?php
/*
 * @Description: 引用
 * @version: 
 * @Author: SunDuncan
 * @Date: 2021-10-19 15:25:00
 * @LastEditors: SunDuncan
 * @LastEditTime: 2021-10-19 15:47:46
 */
/**
 * Created by PhpStorm.
 * User: chunningxu
 * Date: 1/15/15
 * Time: 9:56 PM
 */

class WechatApi
{
    //START.

    public $appID = NULL;
    public $appSecret = NULL;

    /**
     * @param string $appID
     * @param string $appSecret
     */
    public function __construct($appID = null,$appSecret = null)
    {
        $this->appID = $appID ? $appID : $GLOBALS['config']['wechat']['app_id'];
        $this->appSecret = $appSecret ? $appSecret : $GLOBALS['config']['app_secret'];
    }

    /**
     * @param string $uri
     * @param array $data
     * @param string $method
     * @throws Exception
     * @return stdClass
     */
    private function sendRequest($uri,$data,$method = 'get')
    {
        $response = null;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        if(strtolower($method) == 'post')
        {
            curl_setopt($ch, CURLOPT_URL, $uri);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, Utils::arrayToKeyValPair($data));

        }else{
            $url = Utils::urlJoint($uri,$data);
            curl_setopt($ch, CURLOPT_URL, $url);
        }

        $response = curl_exec($ch);
        curl_close($ch);

        if($response != FALSE)
        {
            $response = json_decode($response,true);

            if(isset($response['errcode']) && intval($response['errcode']) != 0)
            {
                throw new Exception($uri.': '.$response['errmsg'],intval($response['errcode']));
            }
        }else{
            throw new Exception('Error occur when invoke wechat api "$uri": Unknown error.');
        }

        return $response;
    }

    /**
     * @return stdClass
     */
    public function accessToken()
    {
        $url = "https://api.weixin.qq.com/cgi-bin/token";
        $data = array
                        (
                            'grant_type'=>'client_credential',
                            'appid'=>$this->appID,
                            'secret'=>$this->appSecret
                        );

        $response = $this->sendRequest($url,$data);

        return $response;
    }

    /**
     * @param string $accessToken
     * @return stdClass
     */
    public function jsTicket($accessToken)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket";
        $data = array
        (
            'access_token'=>$accessToken,
            'type'=>'jsapi'
        );

        $response = $this->sendRequest($url,$data);

        return $response;
    }

    public function cardTicket($accessToken)
    {
    	$url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket';
    	$data = array
	    (
		    'access_token'=>$accessToken,
		    'type'=>'wx_card'
	    );
	    $response = $this->sendRequest($url,$data);
	    return $response;
    }


    public function getMedia($accessToken,$mediaId)
    {
    	$url = 'https://api.weixin.qq.com/cgi-bin/media/get';
    	$data = array
	    (
	    	'access_token'=>$accessToken,
		    'media_id'=>$mediaId
	    );
    	$response = $this->sendRequest($url,$data,'get');
    	return $response;
    }

    /**
     * @param int $len
     * @return string
     */
    private function randomString($len)
    {
        $dict = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

        $randStr = '';
        while(strlen($randStr) < $len)
        {
            $randStr = $randStr.substr($dict,rand(0,strlen($dict) - 1),1);
        }

        return $randStr;
    }

    /**
     * @param string $jsTicket
     * @param string $jsURL
     * @return array
     */
    public function signatureJSTicket($jsTicket,$jsURL)
    {
        $nonceStr = $this->randomString(16);
        $timestamp = strval(time());
        $signature = "jsapi_ticket=$jsTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$jsURL";
        $signature = sha1($signature);

        $output = array('appId'=>$this->appID,'timestamp'=>$timestamp,'nonceStr'=>$nonceStr,'signature'=>$signature);

        return $output;
    }

	/**
	 * @param $cardTicket
	 * @param $cardIds
	 * @param $code
	 * @param $openId
	 * @return array
	 */
	public function signatureCardTicket($cardTicket,$cardIds,$openId,$code)
	{
		$extList = array();
		$cardIds = is_null($cardIds) ? array(null) : $cardIds;
		foreach($cardIds as $cardId)
		{
			$nonceStr = $this->randomString(16);
			$timestamp = strval(time());
			$ext = array
			(
				'nonce_str'=>$nonceStr,
				'timestamp'=>$timestamp,
				'api_ticket'=>$cardTicket
			);

			if(is_null($cardId) == false && strlen($cardId) > 0)
			{
				$ext['card_id'] = $cardId;
			}

			if(is_null($code) == false)
			{
				$ext['code'] = $code;
			}

			if(is_null($openId) == false)
			{
				$ext['openid'] = $openId;
			}

			$values = array();
			foreach($ext as $value)
			{
				array_push($values,strval($value));
			}
			asort($values,SORT_STRING);
			$source = implode($values,'');

			$sign = sha1($source);
			$ext['signature'] = $sign;
			unset($ext['api_ticket']);

			array_push($extList,$ext);
		}


		return $extList;
	}


	public static   $SCOPE_BASE = 'snsapi_base';
    public static   $SCOPE_USER_INFO = 'snsapi_userinfo';

    /**
     * @param string $redirectURI
     * @param string $scope
     * @param string|null $state
     * @return string
     */
    public function authURL($redirectURI,$scope,$state = NULL)
    {
        $url = 'https://open.weixin.qq.com/connect/oauth2/authorize'.'?appid='.$this->appID.'&redirect_uri='.urlencode($redirectURI).'&response_type=code&scope='.$scope;
        if($state != NULL && strlen($state) > 0)
        {
            $url = $url.'&state='.$state.'#wechat_redirect';
        }else{
            $url = $url.'#wechat_redirect';
        }

        return $url;
    }

    /**
     * @param string $code
     * @return stdClass
     */
    public function webAccessToken($code)
    {
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token';
        $data = array
        (
            'code'=>$code,
            'appid'=>$this->appID,
            'secret'=>$this->appSecret,
            'grant_type'=>'authorization_code'
        );

        $response = $this->sendRequest($url,$data);

        return $response;
    }

    public function outhUserInfo($webAccessToken,$openID,$lang='zh_CN')
    {
        $url = 'https://api.weixin.qq.com/sns/userinfo';
        $data = array
        (
            'access_token'=>$webAccessToken,
            'openid'=>$openID,
            'lang'=>$lang
        );

        $response = $this->sendRequest($url,$data);

        return $response;
    }

    /**
     * @param string $accessToken
     * @param string $openID
     * @param string $lang
     * @return stdClass
     */
    public function userInfo($accessToken,$openID,$lang='zh_CN')
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info';
        $data = array
        (
            'access_token'=>$accessToken,
            'openid'=>$openID,
            'lang'=>$lang
        );

        $response = $this->sendRequest($url,$data);

        return $response;
    }

    //END.
}
