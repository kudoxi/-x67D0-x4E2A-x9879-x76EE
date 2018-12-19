<?php
ini_set('date.timezone','Asia/Shanghai');
//error_reporting(E_ERROR);

require_once "../lib/WxPay.Api.php";
require_once "WxPay.NativePay.php";
require_once 'log.php';
use think\Config;
//模式一
/**
 * 流程：
 * 1、组装包含支付信息的url，生成二维码
 * 2、用户扫描二维码，进行支付
 * 3、确定支付之后，微信服务器会回调预先配置的回调地址，在【微信开放平台-微信支付-支付配置】中进行配置
 * 4、在接到回调通知之后，用户进行统一下单支付，并返回支付信息以完成支付（见：native_notify.php）
 * 5、支付完成之后，微信服务器会通知支付成功
 * 6、在支付成功通知中需要查单确认是否真正支付成功（见：notify.php）
 */
$notify = new NativePay();
$SetAttach= $_GET['SetAttach'];
$setBody = $_GET['SetBody'];
$param = $_GET['param'];
$info = json_decode($param,true);
//模式二
/**
 * 流程：
 * 1、调用统一下单，取得code_url，生成二维码
 * 2、用户扫描二维码，进行支付
 * 3、支付完成之后，微信服务器会通知支付成功
 * 4、在支付成功通知中需要查单确认是否真正支付成功（见：notify.php）
 */
$input = new WxPayUnifiedOrder();
$input->SetBody($_GET['SetBody']);
$input->SetAttach($SetAttach);
$input->SetOut_trade_no(WxPayConfig::MCHID.date("YmdHis"));
$input->SetTotal_fee($_GET['SetTotal_fee']*100);
$input->SetTime_start(date("YmdHis"));
$input->SetTime_expire(date("YmdHis", time() + 600));
$input->SetGoods_tag($_GET['SetGoods_tag']);
$input->SetNotify_url(urldecode($info['Notify_url']));
$input->SetTrade_type("NATIVE");
$input->SetProduct_id($_GET['SetProduct_id']);

$result = $notify->GetPayUrl($input);
$url2 = $result["code_url"];
?>

<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1" /> 
    <title>微信支付</title>
</head>
<style type="text/css">
.pay-cancel{
	width: 220px;
	height: 58px;
	background-color: #fff;
	font-size: 14px;
	color: #cccccc;
	text-align: center;
	line-height: 58px;
	margin: 0px auto;
	letter-spacing: 3px;
	cursor: pointer;
}
</style>
<style type="text/css">
body {
background-color: #666;
width: 100%;
height: 100%;
left:0;
top:0;/*FF IE7*/
filter:alpha(opacity=50);/*IE*/
}
.modal{
	position: fixed;
    top: 10%;
    left: 50%;
    z-index: 1050;
    margin-left: -280px;
    background-color: #fff;
    border: 1px solid #999;
    border: 1px solid rgba(0,0,0,0.3);
    -webkit-border-radius: 6px;
    -moz-border-radius: 6px;
    border-radius: 6px;
    outline: 0;
    -webkit-box-shadow: 0 3px 7px rgba(0,0,0,0.3);
    -moz-box-shadow: 0 3px 7px rgba(0,0,0,0.3);
    box-shadow: 0 3px 7px rgba(0,0,0,0.3);
    -webkit-background-clip: padding-box;
    -moz-background-clip: padding-box;
    background-clip: padding-box;
	text-align:center;
}
.modal img{
	margin-top:50px;
	margin-bottom:30px;
}
.modal table td{
	padding:5px 0px 5px 165px;
}
</style>
<body>
	  <!--详情-->
	<div class="container">
	    <div id="detail" class="modal" style="top: 150px;width: 500px;height: 450px;box-sizing:border-box">
			<img alt="模式二扫码支付" src="http://paysdk.weixin.qq.com/example/qrcode.php?data=<?php echo urlencode($url2);?>" style="width:200px;height:200px;"/>
			<table class="table" style="width:100%">
				<tr>
					<td>订单号：<?php echo $info['order_sn'];?></td>
				</tr>
				<tr>
					<td>商品：<?php echo $info['name'];?></td>
				</tr>
				<tr>
					<td>订单金额：<?php echo $_GET['SetTotal_fee'] ;?>元</td>
				</tr>
			</table>
	       <div class="pay-cancel">取消，再看看</div>
	    </div>
	</div>
</body>
<script type="text/javascript" src="../../../public/themes/xiaorui/public/static/js/jquery-2.1.0.js"></script>
<script>
//取消订单
var orderid = "<?php echo $info['id'];?>";
$(".pay-cancel").click(function(){
	$.ajax({
        type : "post",
        data : {orderid:orderid},
        url : "<?php echo urldecode($info['cancelurl']);?>",
        success : function(res){
            alert('订单已取消');
           window.location.href=res[0];
        }
    })
})
agian();
function agian(){
	$.ajax({
        type : "post",
		data : {id:orderid},
        url : "<?php echo urldecode($info['freshurl']);?>",
        success : function(res){
           if(res[0] == 102){
			   setInterval(agian(),5000);
		   }else{
			   window.location.href=res[1];
		   }
        }
    })
}
</script>
</html>