<?php
#mongodb 驱动可以用 “MONGODB-CR” 和 “SCRAM-SHA-1” 两种认证方式
$mongo_uri = '';

$conf = array(
    'redis' => array(
        'host' => '',
        'port' =>  '',
        'pass' => ''
    ),

    'access_key' => array(
        ''
    ),
    'allow_access_ip' => array(
        '127.0.0.1',
	'',

    ),
    'mail' => array(
     'host' => 'smtp.qq.com',
        'port' => 25,
        'username' => '',
        'password' => '',
    ),
);
