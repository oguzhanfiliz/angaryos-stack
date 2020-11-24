<?php

function getESignCount($token, $env)
{
    global $env, $data;
    
    $m = new Memcached();
    $m->addServer($env['MEMCACHED_HOST'], 11211);
    
    $key = 'userToken:'.$token.'.eSingCount';
    
    return $m->get('angaryos_cache:'.$key);
}

?>