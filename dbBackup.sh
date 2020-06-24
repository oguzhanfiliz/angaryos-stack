pg_dump -Fc public -U postgres -h 192.168.10.185 -f ./backend/database/backup/`date +%Y-%m-%d_%H:%M`.dump
