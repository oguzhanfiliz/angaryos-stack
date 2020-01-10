<?php

foreach($params['user']->tokens as $token)
{
    if($token['token'] == $params['token'])
        return TRUE;        
}

return FALSE;