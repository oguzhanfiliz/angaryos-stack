<?php
    $kucuk = array('ç', 'ğ', 'i', 'ı', 'ö', 'ş', 'ü');
    $buyuk = array('Ç', 'Ğ', 'İ', 'I', 'Ö', 'Ş', 'Ü'); 
    
    return strtolower(str_replace($buyuk, $kucuk, $params));