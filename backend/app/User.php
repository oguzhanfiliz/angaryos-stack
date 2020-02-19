<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Cache;
use DB;

class User extends Authenticatable
{
    use Notifiable;
    
    private $tableGroups;
    
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


    
    private function getTableGruops()
    {
        if($this->tableGroups == NULL)
            $this->tableGroups = Cache::rememberForever('tableGroups', function()
            {
                return DB::table('table_groups')->orderBy('order')->get();
            });
            
        return $this->tableGroups;
    }
    
    private function getTableGruop($tableId)
    {
        foreach($this->getTableGruops() as $tableGroup)
        {
            $tableIds = json_decode($tableGroup->table_ids);
            if(in_array($tableId, $tableIds))
                return $tableGroup;
        }
    }
    
    private function getTableListForMenu()
    {
        $return = [];
                
        if(isset($this->auths['tables']) && is_array($this->auths['tables']))
            foreach($this->auths['tables'] as $name => $auth)
            {
                if(isset($auth['option']) && in_array('0', $auth['option']))//menu hide
                    continue;

                $temp['id'] = get_attr_from_cache('tables', 'name', $name, 'id');
                if($temp['id'] == NULL) continue;
                
                $temp['name'] = $name;
                $temp['display_name'] = get_attr_from_cache('tables', 'name', $name, 'display_name');
                
                $tableGroup = $this->getTableGruop($temp['id']);
                if($tableGroup != NULL)
                    $groupId= $tableGroup->id;
                else
                    $groupId = 0;
                
                if(!isset($return[$groupId]))
                    $return[$groupId] = [];
                
                array_push($return[$groupId], $temp);
            }
            
        return $return;
    }
    
    private function getTableGroupListForMenu()
    {
        $tableGroups = [];
        foreach($this->getTableGruops() as $tableGroup)
        {
            $temp = 
            [
                'id' => $tableGroup->id,
                'name_basic' => $tableGroup->name_basic,
                'table_ids' => json_decode($tableGroup->table_ids),
                'image' => @helper('get_url_from_file', json_decode($tableGroup->image))[0],
                'icon' => $tableGroup->icon,
                'order' => $tableGroup->order
            ];
            array_push($tableGroups, $temp);
        }
        
        return $tableGroups;
    }
    
    public function getMenuArray()
    {
        return 
        [
            'tables' => $this->getTableListForMenu(),
            'tableGroups' => $this->getTableGroupListForMenu() 
        ];
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
