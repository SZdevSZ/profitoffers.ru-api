#! /usr/bin/bash
count=0
stat=2
while true
do
if [ `ls /var/www/predicted/calltmp/ | wc -l` -eq 0 ] 
then
    stat=0
  #  echo $stat "Folder empty "
else
    stat=1  
 #  echo $stat "Folder not empty "

    mv -v /var/www/predicted/calltmp/* /var/spool/asterisk/outgoing/
    sleep 2
fi
done
