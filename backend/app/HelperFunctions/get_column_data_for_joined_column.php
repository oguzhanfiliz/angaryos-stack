<?php

if(!strstr($params, ' as ')) dd('kolon adında as olmalı: ' . $params);
return explode(' as ', $params);