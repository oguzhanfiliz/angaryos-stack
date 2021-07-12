<?php

if(isset($params['record']->tokens) && is_array($params['record']->tokens))
    foreach($params['record']->tokens as $token)
    {
        if($token['token'] == $params['token'])
            return TRUE;        
    }

return FALSE;