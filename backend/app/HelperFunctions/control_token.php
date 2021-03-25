<?php

foreach($params['record']->tokens as $token)
{
    if($token['token'] == $params['token'])
        return TRUE;        
}

return FALSE;