<?php

if(get_cfg_var('Host_Type'))
{
    require_once dirname(__FILE__) . '/'.get_cfg_var('Host_Type').'_config.php';
}else
{
    require_once dirname(__FILE__) . '/config.php';
}

function get_client_ip() {
    if(getenv('HTTP_CLIENT_IP')){
        $client_ip = getenv('HTTP_CLIENT_IP');
    } elseif(getenv('HTTP_X_FORWARDED_FOR')) {
        $client_ip = getenv('HTTP_X_FORWARDED_FOR');
    } elseif(getenv('REMOTE_ADDR')) {
        $client_ip = getenv('REMOTE_ADDR');
    } else {
        $client_ip = $_SERVER['REMOTE_ADDR'];
    }
    return $client_ip;
}

$allow_client_ip = $conf['allow_access_ip'];
$client_ip = get_client_ip();
if(!in_array($client_ip,$allow_client_ip))
{
    header('status: 401 Unauthorized');
    exit;
}

try {
    $redis = new Redis();
    $redis->connect($conf['redis']['host'],$conf['redis']['port'],5);
    $redis->auth($conf['redis']['pass']);
    $redis->setOption(Redis::OPT_PREFIX,'mail:');
} catch(Exception $e) {
    exit($e->getMessage());
}

$email = isset($_GET['email'])?$_GET['email']:'';
$from  = isset($_GET['from'])?$_GET['from']:'';
$from_name = isset($_GET['from_name'])?$_GET['from_name']:'monitor';
$name =  isset($_GET['name'])? $_GET['name']:$email;
$subject = isset($_GET['subject'])?$_GET['subject']:'';
$content = isset($_GET['content'])?$_GET['content']:'';

if(empty($email) || empty($subject)|| empty($content))
{
    header('status: 403 Unforbord');
    exit;
}
$data =  array(
    $email,
    $from,
    $from_name,
    $name,
    $subject,
    $content,
);
file_put_contents('/tmp/get_mail.log',json_encode($data)."\n\r",FILE_APPEND);
$data = implode('|||',$data);
$redis_email_key = "sendlist";
$result = $redis->lPush($redis_email_key,$data);
if($result)
{
    header('status: 200 succ');
    exit;
}else{
    header('status: 0 fail');
    exit;
}






