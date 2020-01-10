<?php

$srid = DB::select('SELECT Find_SRID(\''.DB_SCHEMA.'\', \''.$params['table'].'\', \''.$params['column'].'\')');
return $srid[0]->find_srid;