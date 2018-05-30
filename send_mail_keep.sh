#!/bin/bash
#########################################################################  
# File Name: sms_keep.sh  
# Author: mougong 
#########################################################################  
#!/bin/bash  
num=1  
iNum=1 

#echo $$  
while(( $num < 5 ))  
do  
sn=`ps -ef | grep send_mail.sh | grep -v grep |awk '{print $2}'`  
#echo $sn  
if [ "${sn}" = "" ]    #如果为空,表示进程未启动
then  
let "iNum++"  
#echo $iNum  
#cp statsms.log /www/mail_sms/log/statsms_$iNum.log.bak  
#rm statsms.log  
nohup  /www/mail_sms/send_mail.sh > /www/mail_sms/log/statmail.log 2>&1 & #后台启动进程  
#echo start ok !  
#else  
#echo running  
fi  
sleep 5  
done 
