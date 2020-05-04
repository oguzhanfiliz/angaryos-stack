<?php
return \DB::select('select st_Astext(\''.$params.'\') as data')[0]->data;
?>