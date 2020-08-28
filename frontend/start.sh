echo fs.inotify.max_user_watches=524288 | tee -a /etc/sysctl.conf
ng serve --host 0.0.0.0 --port 4200 --disable-host-check --live-reload false