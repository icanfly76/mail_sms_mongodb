<?php
/*
邮件中心：每分钟发送电子邮件
*/

error_reporting(E_ERROR | E_PARSE);
ini_set('default_socket_timeout', -1);
define("MAIL_ROOT",dirname(__FILE__));
if(!class_exists('PHPMailer')) require_once(MAIL_ROOT.'/lib/PHPMailerAutoload.php');

if(get_cfg_var('Host_Type'))
{
	require_once dirname(__FILE__) . '/'.get_cfg_var('Host_Type').'_config.php';
}else
{
	require_once dirname(__FILE__) . '/config.php';
}

/*
向mongodb写入数据
*/

$connection = new MongoClient($mongo_host,$conf['mongodb']);
#$connection = new MongoClient("mongodb://10.66.248.127:27017/admin",
#    array(
#        "username" => "rwuser",
#        "password" => "masonmou0987",
#        "authMechanism" => "MONGODB-CR"
#    )
#);

$mongo_db = $connection->mail_db;
$mongo_collection = $mongo_db->mail_record;

set_time_limit(0);
date_default_timezone_set('Asia/Shanghai');
$date = date('Y-m-d',time());
try {
	$redis = new Redis();
	$redis->connect($conf['redis']['host'],$conf['redis']['port'],5);
	$redis->auth($conf['redis']['pass']);
	$redis->setOption(Redis::OPT_PREFIX,'mail:');
} catch(Exception $e) {
	exit($e->getMessage());
}

//邮件服务器配置处理
$mail_conf = $conf['mail'];
$send_msg = 1;
$email_key = "sendlist";
$list_size = $redis->lSize($email_key);
$len = $list_size > 60 ? 60 : $list_size;
for($i = 0; $i < $len; $i++)
{
	
	$info = $redis->lIndex($email_key,0);
	unset($email,$from,$from_name,$name,$subject,$content);
	list($email,$from,$from_name,$name,$subject,$content) = explode("|||",$info);
	$msg = sendmail($mail_conf,$from,$from_name,$email,$name,$subject,$content);
	if($msg!='ok') {
		$send_msg = 0;
	}
	
	$send_result = array(
		'send_result' =>$send_msg,
		'date' =>$data,
		'from' =>$from,
		'from_name' =>$from_name,
		'email' =>$email,
		'name' =>$name,
		'subject'=>$subject,
		'content'=>$content
	);
	$mongo_collection->save($send_result);
	$redis->lPop($email_key);
	sleep(2);
}

function sendmail($conf,$from,$from_name,$email,$name,$subject,$content) {
	global $mail;

	$msg = '';
	if(true == stripos($email,","))
	{
		$email_arr = explode(",",$email);
	}
	try {
		$mail = new PHPMailer(true);
		$mail->IsSMTP();
		$mail->Host       = $conf['host'];
		$mail->SMTPDebug  = 0;
		$mail->SMTPAuth   = true;
		$mail->Port       = $conf['port'];
		$mail->Username   = $conf['username'];
		$mail->Password   = $conf['password'];
		if($email_arr)
		{
			foreach($email_arr as $k =>$v)
			{
				$mail->AddAddress($v,$v);
			}
		}else{
			$mail->AddAddress($email, $name);
		}
		$mail->From = $from;
		$mail->FromName  = $from_name;
		$mail->Subject = $subject;
		$mail->MsgHTML($content);
		$mail->CharSet = "UTF-8";
		$mail->Send();

		$msg_ori = 'ok';
	} catch(phpmailerException $e) {
		$msg_ori = trim(strip_tags($e->errorMessage()));
	} catch(Exception $e) {
		$msg_ori = trim(strip_tags($e->getMessage()));
	}

	return $msg_ori;
}
