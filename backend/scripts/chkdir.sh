#! /usr/bin/bash
count=0
stat=2
while true
do
if [ `ls /var/www/dialer/calltmp/predicted/ | wc -l` -eq 0 ] 
then
    stat=0
#    echo $stat "Folder empty "
else
    stat=1  
 #   echo $stat "Folder not empty "

    mv -v /var/www/dialer/calltmp/predicted/* /var/spool/asterisk/outgoing/
    sleep 4
fi
done
