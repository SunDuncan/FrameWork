<?php
/*
 * @Description: 
 * @version: 
 * @Author: SunDuncan
 * @Date: 2021-10-21 17:12:35
 * @LastEditors: SunDuncan
 * @LastEditTime: 2021-10-26 15:33:22
 */
class PayController extends Controller {
    /**
     * 测试退款
     */
    private $config = null;
    public function __construct()
    {
        $this->config = [
            'appid'			=> $GLOBALS['config']['wxpay']['appid'],
            'mch_id'	 	=> $GLOBALS['config']['wxpay']['mch_id'],
            'pay_apikey' 	=> $GLOBALS['config']['wxpay']['pay_apikey'],
            'notify_url'  => $GLOBALS['config']['wxpay']['notify_url'],
            'api_cert' => $GLOBALS['config']['wxpay']['api_cert'],
            'api_key' => $GLOBALS['config']['wxpay']['api_key']
        ];
    }

    public function testRefund() {
        $this->output(Utils::creatOrderNo());
    }
    /**
     * 下单设置订单
     */
    public function setOrder() {
        /**
         * 首先判断这个用户是不是会员
         */
        $config = [
            'appid'			=> $GLOBALS['config']['wxpay']['appid'],
            'mch_id'	 	=> $GLOBALS['config']['wxpay']['mch_id'],
            'pay_apikey' 	=> $GLOBALS['config']['wxpay']['pay_apikey'],
            'notify_url'  => $GLOBALS['config']['wxpay']['notify_url']
        ];

        $openid = "";
        $order_sn = Utils::creatOrderNo();
        $wxPay = new WxPay($config);
        $response = $wxPay->wxpay($openid, $GLOBALS['config']['wxpay']['paymoney'], "左鹿注册费用", $order_sn);
        // $this->output($response);
        echo $response;
    }


    public function notify() {
        /**
         * 这边还是缺少一个回滚的机制
         */
            // $xml = $GLOBALS['HTTP_RAW_POST_DATA'];		//获取微信支付服务器返回的数据
            $xml = file_get_contents('php://input');
            // 这句file_put_contents是用来查看服务器返回的XML数据 测试完可以删除了
            // file_put_contents('./log.txt',$xml,FILE_APPEND);
            //将服务器返回的XML数据转化为数组
            $data = $this->xml2array($xml);
            // 保存微信服务器返回的签名sign
            $data_sign = $data['sign'];
            // sign不参与签名算法
            unset($data['sign']);
            $sign = $this->makeSign($data);
            
            if ( ($sign===$data_sign) && ($data['return_code']=='SUCCESS') && ($data['result_code']=='SUCCESS')) {
                $result = $data;
                //获取服务器返回的数据
                $order_sn = $data['out_trade_no'];			//订单单号
                $openid = $data['openid'];					//付款人openID
                $total_fee = $data['total_fee'];			//付款金额
                $transaction_id = $data['transaction_id']; 	//微信支付流水号
                
                //在此更新数据库
                // 这边做一些逻辑处理
                
                       

            }else{
                $result = false;
            }
            // 返回状态给微信服务器
            if ($result) {
                $str='<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
            }else{
                $str='<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[签名失败]]></return_msg></xml>';
            }
            echo $str;
            return $result;
    }

    function xml2array($xml){   
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $result= json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);        
        return $result;
    }
    
    function makeSign($data){
        //微信支付秘钥
        $key = $GLOBALS['config']['wxpay']['pay_apikey'];
        // 去空
        $data=array_filter($data);
        //签名步骤一：按字典序排序参数
        ksort($data);
        $string_a=http_build_query($data);
        $string_a=urldecode($string_a);
        //签名步骤二：在string后加入KEY
        //$config=$this->config;
        $string_sign_temp=$string_a."&key=".$key;
        //签名步骤三：MD5加密
        $sign = md5($string_sign_temp);
        // 签名步骤四：所有字符转为大写
        $result=strtoupper($sign);
        return $result;
    }
}
