<?php
#定义mongodb host地址
$mongo_host='';

$conf = array(
    'redis' => array(
        'host' => '',
        'port' =>  '6379',
        'pass' => ''
    ),
    #mongodb MONGODB-CR验证方式
    'mongodb' => array(
        'username' =>  'rwuser',
        'password' => '',
        'authMechanism' => 'MONGODB-CR'
    ),

    'access_key' => array(
        ''
    ),
    'allow_access_ip' => array(
        '127.0.0.1',
	'10.154.40.65',

    ),
    'mail' => array(
        'host' => 'smtp.qq.com',
        'port' => 25,
        'username' => '',
        'password' => '',
    ),
);
