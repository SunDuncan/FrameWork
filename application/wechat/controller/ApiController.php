<?php
/*
 * @Description: 
 * @version: 
 * @Author: SunDuncan
 * @Date: 2021-10-18 17:00:36
 * @LastEditors: SunDuncan
 * @LastEditTime: 2021-10-26 15:30:03
 */

 // 连接本地的redis 服务
class ApiController extends Controller {
    private $wechatApi = null;
    private $errModel = null;
    public function __construct()
    {
        // $this->wechatApi = new WechatApi();
        $this->errModel = new ErrorModel();
    }
    public function getRedis() {
        echo phpinfo();
    }


    public function getAccessToken() {
        $access_token_res = $this->wechatApi->accessToken();
        $this->output($access_token_res);
    }


    // =======> h5 授权 <=======
    /**
     * 获取网页授权的openid
     */
    public function getWebAccessToken() {
        /**
         * 会有access_token  不过好像这次项目不需要用到这个access_token 主要是这个openid
         */
        $code = isset($_POST['code']) ? $_POST['code'] : "";
        if (!$code) {
            $this->output("缺少参数/参数有问题", 8901);
        }
        
        $getTokenResp = $this->wechatApi->webAccessToken($code);
        if (isset($getTokenResp['errcode'])) {
            $insertData['title'] = "网页获取授权的openid";
            $insertData['result'] = json_encode($getTokenResp);
            $insertData['create_time'] = date("Y-m-d H:i:s");
            $res = $this->errModel->add($insertData);
            if (!$res) {
                $this->output("新增err_log出错", 8905);
            }
        }

        // 获取一个token通过这个token,去请求接下来的接口
        
        $this->output($getTokenResp);
    }

    /**
     * 缔昂的微信h5的授权
     */
    /**
      * 授权
      */
      public function oauthWechat() {
        $partner_appid = $GLOBALS['config']['wxosrv']['partner_appid'];
        $target_url = $_POST['target_url'];
        $target_url = urlencode($GLOBALS['config']['wxosrv']['target_url'] . "?target_url=$target_url");
        $time = time();
        $sign = $this->createSign([
            'partner_appid' => $partner_appid,
            'target_url' => $target_url,
            'timestamp' => $time
        ]);
        
        $url = "https://wxosrv.teown.com/wco/oauth2/redirect?partner_appid={$partner_appid}&target_url={$target_url}&timestamp={$time}&sign={$sign}";
        header("Location:{$url}");
        exit;
        // $this->output($url);
    }


    // 回调
    public function oauthIndex() {
        $openid = isset($_GET['openid']) ? $_GET['openid'] : "";
        $target_url = isset($_GET['target_url']) ? $_GET['target_url'] : "";
        if (!$target_url) {
          $this->output("缺少必要参数target_url", 8092);
        }
        if (!$openid) {
          $this->output("缺少必要参数", 8091);
        }

        $userModel = new UserModel();
        $is_exist = $userModel->find(['openid' => $openid]);
        if (!$is_exist) {
            $add['openid'] = $openid;
            $add['create_time'] = time();
            $add['create_time_format'] = date("Y-m-d H:i:s");
            $add['status'] = 1;
            $res = $userModel->add($add);
            if (!$res) {
              $this->output("授权失败", 1000);
            }
            $uid = $res;
        } else {
            $uid = $is_exist['id'];
        }
        
        if (isset($_SESSION[$openid])) {
            $token = $_SESSION[$openid];
        } else {
          $token = Utils::createUid();
          $_SESSION[$openid] = $token;
          $_SESSION[$token] = $uid;
        }
        $header_url = $target_url . "?token=" . $token;
       
        header("Location: {$header_url}");
        exit;
    }

     /**
     * 获取jdk的签名
     */
    public function jssdkSignature() {
        $partner_appid = $GLOBALS['config']['wxosrv']['partner_appid'];
        $target_url = $_POST['url'];
        // $target_url = urlencode($GLOBALS['config']['wxosrv']['target_url'] . "?target_url=$target_url");
        $time = time();
        $sign = $this->createSign([
            'partner_appid' => $partner_appid,
            'url' => $target_url,
            'timestamp' => $time
        ]);
        
        $data = [
            'sign' => $sign,
            'partner_appid' => $partner_appid,
            'timestamp' => $time,
            'url' =>  $target_url
        ];
        $url = "https://wxosrv.teown.com/wco/jssdk/signature";
        $response = Utils::sendRequest($url, $data, "post");
        $this->output(json_decode($response));
    }
}
