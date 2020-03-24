<?php

$find = array('Ç', 'Ş', 'Ğ', 'Ü', 'İ', 'Ö', 'ç', 'ş', 'ğ', 'ü', 'ö', 'ı', '+', '#');
$replace = array('c', 's', 'g', 'u', 'i', 'o', 'c', 's', 'g', 'u', 'o', 'i', 'plus', 'sharp');

$params = strtolower(str_replace($find, $replace, $params));
$params = preg_replace("@[^A-Za-z0-9\-_\.\+]@i", ' ', $params);
$params = trim(preg_replace('/\s+/', ' ', $params));
$params = str_replace(' ', '_', $params);

return $params;