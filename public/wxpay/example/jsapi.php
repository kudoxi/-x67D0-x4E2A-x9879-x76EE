<?php 
ini_set('date.timezone','Asia/Shanghai');
//error_reporting(E_ERROR);
require_once "../lib/WxPay.Api.php";
require_once "WxPay.JsApiPay.php";
require_once 'log.php';

//初始化日志
$logHandler= new CLogFileHandler("../logs/".date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);

/* //打印输出数组信息
function printf_info($data)
{
    foreach($data as $key=>$value){
        echo "<font color='#00ff55;'>$key</font> : $value <br/>";
    }
} */
if(! isset ($_GET['order_sn'])){
	
}
//获取活动总金额及订单号，商品详情
$price = $_GET['money'];
$Out_trade_no = $_GET['order_sn'];
$Notify_url = $_GET['Notify_url'];
$type = $_GET['type'];
$nums = $_GET['nums'];
/* $paygoods = json_decode($_GET['paygoods'],true); */

//exit;
//①、获取用户openid
$tools = new JsApiPay();

$openId = $tools->GetOpenid();
//②、统一下单
$input = new WxPayUnifiedOrder();
$input->SetBody('支付');
$input->SetAttach($type);
$input->SetOut_trade_no($Out_trade_no);
$input->SetTotal_fee($price*100);
$input->SetTime_start(date("YmdHis"));
$input->SetTime_expire(date("YmdHis", time() + 600));
$input->SetGoods_tag("test");
$input->SetNotify_url($Notify_url);//回调URL
$input->SetTrade_type("JSAPI");//调用支付方式
$input->SetOpenid($openId);
$order = WxPayApi::unifiedOrder($input);
if($order[return_msg] != 'OK'){
	
}
$log::DEBUG('order:'.json_encode($Notify_url));
/* echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>';
printf_info($order); */
$jsApiParameters = $tools->GetJsApiParameters($order);
$log::DEBUG('jsApi:'.json_encode($jsApiParameters));
//获取共享收货地址js函数参数
$editAddress = $tools->GetEditAddressParameters();


//③、在支持成功回调通知中处理成功之后的事宜，见 notify.php
/**
 * 注意：
 * 1、当你的回调地址不可访问的时候，回调通知会失败，可以通过查询订单来确认支付是否成功
 * 2、jsapi支付时需要填入用户openid，WxPay.JsApiPay.php中有获取openid流程 （文档可以参考微信公众平台“网页授权接口”，
 * 参考http://mp.weixin.qq.com/wiki/17/c0f37d5704f0b64713d5d2c37b468d75.html）
 */
?>

<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" /> 
    <title>订单支付</title>
    <style>
.paybox{padding:10px;}
.paybox_title,.paybox_list{border-bottom:1px solid #f0f0f0;padding:10px 0;}
.paybox_list{padding-top:0;}
.paybox_list li{    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    width: 100%;margin: 15px 0;}
    .paybox_data span{display:block;margin-bottom: 10px;}
    .paybox_data span em{width: 80px;display: inline-block;font-style:normal;}
    .paybox_btnbox{display:flex;justify-content: space-between;align-items: center;margin:40px auto;}
    .paybox_btnbox>span{display:block;padding:10px;background: #aaa;width: 35%;text-align: center;border-radius: 3px;color: #fff;}
	.bg_red{background:#1da1f6 !important;}
	.width80{width:80%;}
    </style>
    <script type="text/javascript">
	//调用微信JS api 支付
	function jsApiCall()
	{
		WeixinJSBridge.invoke(
			'getBrandWCPayRequest',
			<?php echo $jsApiParameters; ?>,
			function(res){
				if(res.err_msg == "get_brand_wcpay_request:ok"){
	                window.location.href ="<?php echo $_REQUEST['codeurl']; ?>";  //支付成功跳转页面
	            }else{
	                //返回跳转到订单详情页面
	                //history.go(-1);
	            }
			}
		);
	}


	function callpay()
	{
		if (typeof WeixinJSBridge == "undefined"){
		    if( document.addEventListener ){
		        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
		    }else if (document.attachEvent){
		        document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
		        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
		    }
		}else{
		    jsApiCall();
		}
	}
	</script>
	<script type="text/javascript">
	//获取共享地址
	/* function editAddress()
	{
		WeixinJSBridge.invoke(
			'editAddress',
			<?php //echo $editAddress; ?>,
			function(res){
				var value1 = res.proviceFirstStageName;
				var value2 = res.addressCitySecondStageName;
				var value3 = res.addressCountiesThirdStageName;
				var value4 = res.addressDetailInfo;
				var tel = res.telNumber;
				
				alert(value1 + value2 + value3 + value4 + ":" + tel);
			}
		);
	}
	
	window.onload = function(){
		if (typeof WeixinJSBridge == "undefined"){
		    if( document.addEventListener ){
		        document.addEventListener('WeixinJSBridgeReady', editAddress, false);
		    }else if (document.attachEvent){
		        document.attachEvent('WeixinJSBridgeReady', editAddress); 
		        document.attachEvent('onWeixinJSBridgeReady', editAddress);
		    }
		}else{
			editAddress();
		}
	}; */
	
	</script>
</head>
<body>
	<div style="margin-left:auto;margin-right:auto" class="paybox">
	   <!-- <div class="paybox_title">商品</div>
	   <ul class="paybox_list"> -->
	   <?php 
	   /* $nums = 0;
	   foreach($paygoods as $v){
	        echo "<li>";
	        echo '<div class="width80">'.$v[name].'</div>';
	        echo "<div>×".$v[num]."</div>";
	        echo "</li>";
	        $nums += intval($v[num]);
    	} */?>
	     
	   <!-- </ul> -->
	   <div class="paybox_data">
	     <span><em>商品件数：</em><?php echo $nums;?>件</span>
	     <span><em>订 单 号：</em><?php 
		 $Out_trade_no2 = explode("_",$Out_trade_no);
		 echo $Out_trade_no2[0];?></span>
	     <span><em>订单金额：</em>¥<?php echo $price;?>元</span>
	   </div>
	   
	   <div class="paybox_btnbox">
	      <span class="bg_red" onclick="callpay()">立即支付</span>
	      <span onclick="history.go(-1)">返回</span>
	   </div>
	</div>
</body>
</html>