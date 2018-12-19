<?php
require_once("alipcpay/AopSdk.php");
//构造参数
$aop = new AopClient ();
$aop->gatewayUrl = '[https://openapi.alipay.com/gateway.do](https://openapi.alipay.com/gateway.do)';
$aop->appId = '请填写APPID';
$aop->rsaPrivateKey = '请填写商户私钥';
$aop->apiVersion = '1.0';
$aop->signType = 'RSA2';
$aop->postCharset= 'utf-8';
$aop->format='json';
$request = new AlipayTradePagePayRequest ();
$request->setReturnUrl('请填写您的页面同步跳转地址');
$request->setNotifyUrl('请填写您的异步通知地址');
$request->setBizContent('{"product_code":"FAST_INSTANT_TRADE_PAY","out_trade_no":"20150320010101001","subject":"Iphone6 16G","total_amount":"88.88","body":"Iphone6 16G"}');

//请求
$result = $aop->pageExecute ($request);

//输出
echo $result;