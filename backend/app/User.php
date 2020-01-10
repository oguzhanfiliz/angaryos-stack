<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Cache;

class User extends Authenticatable
{
    use Notifiable;
    
    protected $fillable = 
    [
        'tc', 'name', 'surname', 'email', 'password', 'location'
    ];
    
    protected $hidden = 
    [
        'password', 'remember_token',
    ];

    protected $casts = 
    [
        'email_verified_at' => 'datetime',
        'tokens' => 'array',
        'auths' => 'array'
    ];
    
    protected $authTree;


    ////burası çalışmıyor bir menüyü gizlemeyi test et
    public function getMenuArray()
    {
        $return = [];
        
        if(isset($this->auths['tables']) && is_array($this->auths['tables']))
            foreach($this->auths['tables'] as $name => $auth)
            {
                if(isset($auth['option']) && in_array('0', $auth['option']))//menu hide
                    continue;

                $temp['id'] = get_attr_from_cache('tables', 'name', $name, 'id');
                $temp['name'] = $name;
                $temp['display_name'] = get_attr_from_cache('tables', 'name', $name, 'display_name');

                array_push($return, $temp);
            }
        
        return $return;
    }
    
    public function getAuthRecursive($ids)
    {
        if($ids == null) return [];
        
        $i = 0;
        while(count($ids) >= $i + 1)
        {
            if(is_numeric($ids[$i]))
            {
                $temp = get_attr_from_cache ('auth_groups', 'id', $ids[$i], 'auths');
                $temp = json_decode($temp);
                
                $temp = $this->getAuthRecursive($temp);
                
                unset($ids[$i]);
                $i--;
                
                $ids = array_merge($ids, $temp);
            }
            
            $i++;
        }
        
        return $ids;
    }
    
    private function getAuthTree($auths)
    {
        $tree = [];
        foreach($this->getAuthRecursive($auths) as $auth)
        {
            $a = explode(':', $auth);
            if(!isset($tree[$a[0]][$a[1]][$a[2]])) 
                $tree[$a[0]][$a[1]][$a[2]] = [];
            
            array_push($tree[$a[0]][$a[1]][$a[2]], $a[3]);
        }
        
        return $tree;
    }
    
    public function getAuthsAttribute($auths)
    {
        if(!$this->authTree)
        {
            $cacheName = 'tableName:users|id:'.$this->id.'|authTree';
            $this->authTree = Cache::rememberForever($cacheName, function() use($auths)
            {      
                $auths = json_decode($auths);
                return $this->getAuthTree($auths);
            });
        }
        
        return $this->authTree;
    }
    
    public function toSafeArray()
    {
        $importantColumns = 
        [
            'password',
            'email_verified_at',
            'tokens',
            'remember_token',
            'user_id',
            'own_id',
            'created_at',
            'updated_at',
            'location',
            'state'
        ];
        
        $user = $this->toArray();
        
        foreach($importantColumns as $column)
        {
            unset($user->{$column});
            unset($user[$column]);
        }
            
        return $user;
    }
}
