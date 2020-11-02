echo fs.inotify.max_user_watches=524288 | tee -a /etc/sysctl.conf
ionic serve --lab --external --disableHostCheck
