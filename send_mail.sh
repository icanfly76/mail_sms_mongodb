#!/bin/bash
while true
do
php -f /www/mail_sms_mongodb/send_mail.php  &
sleep 10
done

