<?php
use think\Db;
use think\Session;
use think\Config;
/*
 * 获取用户使用截止日期
 * $productid 产品id
 * $user_id 用户id
 * $state 0:正式，1，试用
 */
function get_user_deadline($productid,$user_id,$state){
	$info = Db::name('product')->where('id',$productid)->find();
	$time = $info['time'] * 60*60 *24;
	$e_date = $info['e_date'];
	$now_date = date('Y-m-d H:i:s',time());
	
	$users = Db::name('users')->alias("u")->field("u.using_product_id,p.is_shiyong,u.deadline")
    ->join(Config::get('prefix').'product p','p.id = u.using_product_id','left')
    ->where("u.id",$user_id)->find();
    //如果用户之前没有买过，或者用户试用,或者买过但是过期了，那么从当前时间开始加
	if($users['using_product_id'] == 0 || $users['is_shiyong'] == 1 || $state == 1 || $users['deadline'] < $now_date){
		$time0 = time();
	}
	//如果用户买过，那么加的时间就是从上次的截止日开始加
	else{
		if($users['deadline'] == "0000-00-00 00:00:00"){
			$time0 = time();
		}else{
			$time0 = strtotime($users['deadline']);
		}
	}
	$get_time = date('Y-m-d H:i:s',$time0 + $time);//用户的截止日
	//判断用户截止日是否超出服务提供的截止日
	if($get_time > $e_date){
		$get_time = $e_date;
	}
	return $get_time;
}

/*
 * 复制文件路径
 */
function copy_dir($from_dir,$to_dir){
    if(!is_dir($from_dir)){
        return false;
    }
    //echo "\r\n from:",$from_dir,'---to',$to_dir;
    $from_files = scandir($from_dir);
    //如果不存在目标目录，则尝试创建
    if(!file_exists($to_dir)){
        @mkdir($to_dir);   
    }
    if(!empty($from_files)){
        foreach ($from_files as $file){
            if($file == '.' || $file == '..' ){
                continue;
            }
             
            if(is_dir($from_dir.'/'.$file)){//如果是目录，则调用自身
                copy_dir($from_dir.'/'.$file,$to_dir.'/'.$file);
            }else{//直接copy到目标文件夹
                copy($from_dir.'/'.$file,$to_dir.'/'.$file);
            }
        }
    }
}
/*
 * 转换时间
 */
function time_turn_to_string($time){
    $string_time = intval($time)."天";
    return $string_time;
}
//这个是【睿风企业号】的公共方法，不要写在这里，返回当前公司的员工数量
/*function getcompanystaffcount($companyid){
     return DB::name('users')->where('company_id',$companyid)->count();
}*/
/*
* 根据公司ID返回其使用时限
* $eid:公司ID
*/
function getCompanyDurationOfUse($eid){
	$eid = cmf_password($eid);
	$eid = explode("###",$eid)[1];
    $xiaoruiqy = Config::get('BG_ROOT');
    if(file_exists($xiaoruiqy."/".$eid."/".'conf/config.php')){
        $rest = include_once $xiaoruiqy."/".$eid."/".'conf/config.php';
        return strtotime($rest['BG_ETIME'])>time() ? ['mes'=>'OK','status'=>true,'tus'=>1] : ['mes'=>'时间过期','status'=>false,'tus'=>2];
    }
    return ['mes'=>'未开通','status'=>false,'tus'=>0];
}
//判断当前用户是选择使用企业微信还是钉钉
function choseusedtype(){
    $userid = Session::get('user_id');
    if(!$userid){
    	return;
    }
    $type = array('1' => '企业号', '2' => '钉钉');
    $info = DB::name('users')->alias('u')->join(Config::get('prefix').'company c','c.id = u.company_id','left')->where("id",$user_id)->value('c.use_way');
    return $type[$info];
}
/*
 * 读取配置文件
 * $file： 路径
 * $ini：配置项名
 * 默认没有第三个参数时，按照字符串读取提取''中或""中的内容 
 * 如果有第三个参数时为int时按照数字int处理。 
 */
function getconfig($file, $ini, $type="string") 
{ 
	if(!file_exists($file)) return false; 
	$str = file_get_contents($file); 
	if ($type=="int"){ 
		$config = preg_match("/".preg_quote($ini)."\"=>(.*),/", $str, $res);
		return $res[1]; 
	}else{ 
		$config = preg_match("/".preg_quote($ini)."\"=>\"(.*)\"/",$str,$res);
		if($res[1]==null){ 
			$config = preg_match("/".preg_quote($ini)."'=>'(.*)'/",$str,$res);
		} 
		return $res[1]; 
	} 
} 
/*
 * 修改配置内容
 */
function update_config($file, $ini, $value,$type="string"){
	if(!file_exists($file)) return false; 
		$str = file_get_contents($file); 
		$str2=""; 
	if($type=="int"){ 
		$str2 = preg_replace("/\"".preg_quote($ini)."\"=>(.*),/", "\"".$ini."\"=>".$value.",",$str); 
	}else{ 
		$str2 = preg_replace("/\"".preg_quote($ini)."\"=>\"(.*)\",/", "\"".$ini."\"=>\"".$value."\",",$str); 
	} 
	file_put_contents($file, $str2);
} 
/*
 * 修改文件名带后缀
 */
function change_filename($eid){
	$afix = Config::get('BG_FILENAME_AFIX');
	$rfqy_eid = cmf_password($eid);
	$rfqy_eid = explode("###",$rfqy_eid)[1];
	$newfile = Config::get('BG_ROOT')."/data/".$rfqy_eid.$afix;
	$file = Config::get('BG_ROOT')."/data/".$rfqy_eid;
	if(!file_exists($newfile) && file_exists($file)){
		rename($file,$newfile);
	}else{
		return false;
	}
}
/**
 * 系统邮件发送函数
 * 框架没有发送附件的功能
 * @param string $to    接收邮件者邮箱
 * @param string $name  接收邮件者名称
 * @param string $subject 邮件主题 
 * @param string $body    邮件内容
 * @param string $attachment 附件列表
 * @return boolean 
 */
function think_send_mail($to, $name, $subject = '', $body = '', $attachment = null){
    /*$config = C('THINK_EMAIL');*/
    vendor('phpmailer.class#phpmailer'); //从PHPMailer目录导class.phpmailer.php类文件
    $mail             = new PHPMailer(); //PHPMailer对象
    $smtpSetting = cmf_get_option('smtp_setting');
    $mail->CharSet    = 'UTF-8'; //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
    $mail->IsSMTP();  // 设定使用SMTP服务
    $mail->SMTPDebug  = 0;                  // 关闭SMTP调试功能
                                               // 1 = errors and messages
                                               // 2 = messages only
    $mail->SMTPAuth   = true;                  // 启用 SMTP 验证功能
    $mail->SMTPSecure = 'ssl';                 // 使用安全协议
    $mail->Host       = $smtpSetting['host'];  // SMTP 服务器
    $mail->Port       = empty($smtpSetting['port']) ? "25" : $smtpSetting['port'];  // SMTP服务器的端口号
    $mail->Username   = $smtpSetting['username'];  // SMTP服务器用户名
    $mail->Password   = $smtpSetting['password'];  // SMTP服务器密码
    $mail->SetFrom($smtpSetting['from'], $smtpSetting['from_name']);
    $replyEmail       = $smtpSetting['from'];
    $replyName        = $smtpSetting['from_name'];
    $mail->AddReplyTo($replyEmail, $replyName);
    $mail->Subject    = $subject;
    $mail->MsgHTML($body);
    $mail->AddAddress($to, $name);
    if(is_array($attachment)){ // 添加附件
        foreach ($attachment as $file){
            is_file($file) && $mail->AddAttachment($file);
        }
    }
    return $mail->Send() ? true : $mail->ErrorInfo;
}
//在小睿办公data目录下创建配置文件
function establishFile($company){
    $txt = '<?php	return array (
    "BG_COMPANY_NAME"=>"'.$company['name'].'",//公司名
    "BG_COMPANY_EID"=>"'.$company['eid'].'",//公司标识
    "BG_USERID"=>"'.$company['user_id'].'",//开通人ID
    "BG_USERNAME"=>"'.$company['user_name'].'",//开通人姓名
    "BG_PHONE"=>"'.$company['user_phone'].'",//开通手机号
    "BG_EMAIL"=>"'.$company['user_email'].'",//开通邮箱
    "BG_STIME"=>"'.$company['s_date'].'",//开通时间
    "BG_ETIME"=>"'.$company['e_date'].'",//服务的终止时间
    "BG_PENDTIME"=>"'.$company['e_date'].'",//产品的终止时间（服务终止时间总是<= 产品的终止时间）
    "BG_NUMBER"=>"'.$company['number'].'",//人数限制
    "BG_USINGWAY"=>"",//当前使用途径
    "BG_SHIYONG"=>"'.$company['type'].'",//是否试用
    "BG_TYPE"=>"'.$company['use_way'].'",//1微信,2钉钉
);';
    $rit = "<?php return array(
	'SHENPI_DEFAULT_USER' => 'lijun',//默认审批人
	'LEAVE_SHENPI' => '4',
    'OVER_SHENPI' => '4',
    'BAOXIAO_SHENPI' => '4',
    'WAIQING_SHENPI' => '4',
    'CAIGOU_SHENPI' => '4',
    'LEAVE_CHAOSONG' => '4',
    'OVER_CHAOSONG' => '4',
    'BAOXIAO_CHAOSONG' => '4',
    'WAIQING_CHAOSONG' => '4',
    'CAIGOU_CHAOSONG' => '4',
);";
    $db = "<?php return array(
    'DB_TYPE' => 'mysql',
    'DB_HOST' => 'localhost',
    'DB_NAME' => 'qiye',
    'DB_USER' => 'root',
    'DB_PWD' => 'root',
    'DB_PORT' => '3306',
    'DB_PREFIX' => 'rf_',
    //密钥
    'AUTHCODE' => 'ShaqnglPZU2XHPKo39',
    //cookies
    'COOKIE_PREFIX' => 'lEfdc4_',
);";
    $route = "<?php return array (
);";
    $sys ='<?php
return array(
	//考勤模块
	"sys_morning_time_h"=>"09",//早上上班时间 时
	"sys_morning_time_m"=>"00",//早上上班时间 分
	"sys_morning_time_i"=>"00",//早上上班时间 秒
	
	"sys_morning_out_time_h"=>"12",//早上下班时间 时
	"sys_morning_out_time_m"=>"00",//早上下班时间 分
	"sys_morning_out_time_i"=>"00",//早上下班时间 秒
	
	"sys_noon_time_h"=>"13",//下午上班时间 时
	"sys_noon_time_m"=>"00",//下午上班时间 分
	"sys_noon_time_i"=>"00",//下午上班时间 秒
	
	"sys_noon_out_time_h"=>"18",//下午下班时间 时
	"sys_noon_out_time_m"=>"00",//下午下班时间 分
	"sys_noon_out_time_i"=>"00",//下午下班时间 秒
	
	"sys_latetime"=>"60",//迟到n分钟算旷工m天
	"sys_latetime_kg"=>"0.5",//迟到n分钟算旷工m天
	"sys_has_limit_latetime"=>"1",//迟到时长是否有上限 1：是 0 否
	"sys_limit_latetime"=>"180",//大于上限时长x一律算旷工y天
	"sys_limit_latetime_k"=>"1",//大于上限时长x一律算旷工y天
	
	"sys_in_latetime_lj"=>"60",//迟到小于a分钟，计入迟到累计数
	"sys_latetime_lj"=>"30",//迟到n分钟算旷工m天
	"sys_latetime_lj_kg"=>"0.5",//迟到n分钟算旷工m天
	
	"sys_earlytime"=>"60",//早退超过n分钟算旷工m天
	"sys_earlytime_kg"=>"0.5",//早退超过n分钟算旷工m天
	"sys_lt_earlytime_kg"=>"60",//早退时长小于n分钟，直接扣除调休m天
	"sys_earlytime_tx"=>"0.5",//早退时长小于n分钟，直接扣除调休m天
	
	"sys_absenteeism_wx"=>"3",//旷工1天算无效出勤m天，
	
	"sys_participation_tx"=>"1",//无效出勤1次扣调休m天
	
	//假期模块
	"sys_leave"=>"",//每请假n小时扣调休m天
	"sys_has_limit_leave"=>"1",//是否有限制
	"sys_limit_leave"=>"3",//请假时长小于n小时时，一律算作没请假
	
	"sys_tx"=>"2",//调休
	"sys_nj"=>"7",//年假
	"sys_hj"=>"10",//婚假
	"sys_cj"=>"128",//产假
	"sys_bj"=>"0",//病假
	"sys_sj"=>"0",//事假
	"sys_pc"=>"10",//陪产假
	"sys_fz"=>"0.5",//非直系丧假
	"sys_zx"=>"1",//直系丧假期
	"sys_lc"=>"",//流产假
	"sys_br"=>"",//哺乳假
	
	//加班模块
	"sys_overtime"=>"3",//加班超过n小时算调休m天，获得餐补x元
	"sys_overtime_tx"=>"0.5",//加班超过n小时算调休m天，获得餐补x元
	"sys_overtime_cb"=>"20",//加班超过n小时算调休m天，获得餐补x元
	"sys_has_limit_overtime"=>"1",//是否有上限
	"sys_limit_overtime"=>"6",//超出上限n小时，一律算调休m天，获得餐补x元
	"sys_limit_overtime_tx"=>"1",//超出上限n小时，一律算调休m天，获得餐补x元
	"sys_limit_overtime_cb"=>"40",//超出上限n小时，一律算调休m天，获得餐补x元
	
	//工作量模块
	"sys_overtime_rt"=>"8",//加班x小时，算1人天工作量
	"sys_workload_rt"=>"8",//工作x小时，算1人天工作量
	
	//费用模块
	"sys_use_moren"=>"1",//是否使用默认公式 不用我们的公式，就让他自己填
	"sys_jiangjin_xs"=>"0.15",//奖金系数  经理奖金 = 实际奖金总额 - 实际奖金总额 * 系数n
	"sys_jiangjin_xs2"=>"60",//实际奖金 = 预算奖金-[（实际人天工作量-预计人天工作量）* n ]
	
	//费用模块
	"sys_workmoney_ff"=>"20",//工资默认发放日，小于n号为上月数据
	"sys_workmoney_xs"=>"0.2",//法定最低扣款 = 工资 * 系数n
	"sys_workmoney_low"=>"3500",//最低工资
	
    //费用模块
    //（如打车，聚餐等，）这个可以点击按钮动态添加输入框， |数字输入框   |- |字段输入框a|，提交后，插入rf_options表，{"数字"=>a,"数字"=>b,"数字"=>c}
    
    //费用模块
    //如（现金，支票，支付宝...），也可以动态添加，和报销一样，存入数据表
    
    //项目模块
    //如（普通项目，行政项目，其他项目）
);';
    writeConf('config.php',$company['eid_jm'],$txt);
    writeConf('config_rit.php',$company['eid_jm'],$rit);
    writeConf('config_sys.php',$company['eid_jm'],$sys);
    writeConf('db.php',$company['eid_jm'],$db);
    writeConf('route.php',$company['eid_jm'],$route);
}
//写入文件
function writeConf($fileName,$eid_jm,$txt){
    $dir = iconv("UTF-8", "GBK",Config::get('BG_TEMPLATE_CONFIG').DS. $eid_jm.DS.'conf');
    if (!file_exists($dir)){
        mkdir ($dir,0777,true);
    }
    $myfile = fopen(Config::get('BG_TEMPLATE_CONFIG').DS. $eid_jm.DS.'conf'.DS.$fileName, "w") or die("Unable to open file!");
    fwrite($myfile, $txt);
    fclose($myfile);
}
?>