<?php

if($params == NULL && helper('db_control', TRUE))
    return FALSE;

$output = shell_exec('php /var/www/artisan db:seed');
if(strstr($output, 'Database seeding completed successfully.'))
    return TRUE;
else
    return $output;