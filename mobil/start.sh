echo fs.inotify.max_user_watches=524288 | tee -a /etc/sysctl.conf 
cat /proc/sys/fs/inotify/max_user_watches
sysctl -p
ionic serve --lab --address=0.0.0.0 --disableHostCheck --ssl 