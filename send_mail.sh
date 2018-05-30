#!/bin/bash
while true
do
php -f /www/mail_sms/send_mail.php  &
sleep 10
done

