<?php

if(!strstr($params, ' as ')) 
    $params = $params . ' as ' . $params;
    //dd('kolon adında as olmalı: ' . $params);
return explode(' as ', $params);