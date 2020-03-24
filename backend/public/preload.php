<?php

/****    Global Functions    ****/

function getMemcachedKeys($host = 'memcached', $port = 11211)
{
    $mem = @fsockopen($host, $port);
    if ($mem === FALSE) return -1;

    // retrieve distinct slab
    $r = @fwrite($mem, 'stats items' . chr(10));
    if ($r === FALSE) return -2;

    $slab = array();
    while (($l = @fgets($mem, 1024)) !== FALSE) {
        // sortie ?
        $l = trim($l);
        if ($l == 'END') break;

        $m = array();
        // <STAT items:22:evicted_nonzero 0>
        $r = preg_match('/^STAT\sitems\:(\d+)\:/', $l, $m);
        if ($r != 1) return -3;
        $a_slab = $m[1];

        if (!array_key_exists($a_slab, $slab)) $slab[$a_slab] = array();
    }

    // recuperer les items
    reset($slab);
    foreach ($slab AS $a_slab_key => &$a_slab) {
        $r = @fwrite($mem, 'stats cachedump ' . $a_slab_key . ' 100' . chr(10));
        if ($r === FALSE) return -4;

        while (($l = @fgets($mem, 1024)) !== FALSE) {
            // sortie ?
            $l = trim($l);
            if ($l == 'END') break;

            $m = array();
            // ITEM 42 [118 b; 1354717302 s]
            $r = preg_match('/^ITEM\s([^\s]+)\s/', $l, $m);
            if ($r != 1) return -5;
            $a_key = $m[1];

            $a_slab[] = $a_key;
        }
    }

    // close
    @fclose($mem);
    unset($mem);

    // transform it;
    $keys = array();
    reset($slab);
    foreach ($slab AS &$a_slab) {
        reset($a_slab);
        foreach ($a_slab AS &$a_key) $keys[] = $a_key;
    }
    unset($slab);

    return $keys;
}



ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('BASE_PATH', '/var/www/backend/');
$pipe['logRandom'] = date('dymHis').rand(100, 500);
$pipe['laravelStart'] = microtime(true);