docker build -t karapazar/laravel-php-fpm:0.52 /var/www/backend/
docker build -t karapazar/laravel-cron:0.17 -f /var/www/backend/DockerfileCron  /var/www/backend/ 
docker build -t karapazar/laravel-jobs:0.4 -f /var/www/backend/DockerfileJobs /var/www/backend/ 
docker build -t karapazar/angular-serve:0.16  /var/www/frontend/ 

docker push karapazar/laravel-php-fpm:0.52
docker push karapazar/laravel-cron:0.17 
docker push karapazar/laravel-jobs:0.4 
docker push karapazar/angular-serve:0.16
