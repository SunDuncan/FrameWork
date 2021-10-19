<?php
/*
 * @Description: 
 * @version: 
 * @Author: SunDuncan
 * @Date: 2021-10-18 17:00:36
 * @LastEditors: SunDuncan
 * @LastEditTime: 2021-10-19 18:25:04
 */

 // 连接本地的redis 服务
class ApiController extends Controller {
    private $wechatApi = null;
    private $errModel = null;
    public function __construct()
    {
        $this->wechatApi = new WechatApi();
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
}
