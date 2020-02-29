<?php

$output = shell_exec('php /var/www/artisan db:seed');
if(strstr($output, 'Database seeding completed successfully.'))
{
    \Cache::flush();
    return TRUE;
}
else
    return $output;