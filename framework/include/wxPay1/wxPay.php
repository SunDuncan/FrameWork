<?php 

require_once "wxpay.class.php";

$config = array(
	'wxappid'		=> 'wx123456789876',
	'mch_id'	 	=> '123456789',
	'pay_apikey' 	=> '123456789876123456789876123456789876'
);

$wxpay = new WxPay($config);
$result = $wxpay->paytest();

?>

