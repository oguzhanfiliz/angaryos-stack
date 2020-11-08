echo fs.inotify.max_user_watches=524288 | tee -a /etc/sysctl.conf
ionic serve --lab --address=0.0.0.0 --disableHostCheck