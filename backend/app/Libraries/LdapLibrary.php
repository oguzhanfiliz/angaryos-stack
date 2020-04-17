<?php

namespace App\Libraries;


class LdapLibrary
{
    public $connection;
    private $baseDn;
    
    public function __construct($host, $username, $password, $baseDn = 'dc=liderahenk,dc=org') 
    {
        $this->baseDn = $baseDn;
        
        $ldap = ldap_connect($host);

        ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);

        $bind = ldap_bind($ldap, $username, $password);
        
        if($bind) $this->connection = $ldap;
    }
    
    public function search($filter, $dn = '')
    {
        if($dn == '') $dn = $this->baseDn;
        
        $result = ldap_search($this->connection, $dn, $filter);
        $entries = ldap_get_entries($this->connection, $result);
        return $this->formatEntries($entries);
    }
    
    public function formatEntries($entries)
    {
        if($entries == NULL) return [];
        if($entries['count'] == 0) return [];
        
        unset($entries['count']);
        
        $data = [];
        
        foreach($entries as $entry)
        {
            $temp = [];
            foreach($entry as $key => $value)
                if(!is_numeric($key) && $key != 'count')
                {
                    if(is_array($value)) 
                    {
                        unset($value['count']);
                        
                        if(count($value) == 1) $value = last($value);
                    }
                    
                    $temp[$key] = $value;
                }
                
            array_push($data, $temp);
        }
        
        return $data;
    }
    
    public function getModifyTime($dn, $deref=LDAP_DEREF_NEVER)
    {
        $attrs = array( 'modifytimestamp' );
        $search = ldap_read( $this->connection, $dn, '(objectClass=*)', $attrs, 0, 0, 0, $deref );
        $entry = ldap_first_entry( $this->connection, $search );
        $attrs = ldap_get_attributes( $this->connection, $entry );
        
        $temp = $attrs['modifyTimestamp'][0];
        $time = new \Carbon\Carbon($temp);
        $time->addHours(3);//TR
        
        return $time;
    }
    
    public function add($entry, $dn = '')
    {
        if($dn == '') $dn = $this->baseDn;

        if(isset($entry['cn']))
            $dn = 'cn='.$entry['cn'].','.$dn;
        else if(isset($entry['uid']))
            $dn = 'uid='.$entry['uid'].','.$dn;
        else
            throw new \Exception('Entry mush has cn or uid column: '.json_encode($entry));
            
        unset($entry['updated_at']);
        
        return ldap_add($this->connection, $dn, $entry);
    }
    
    public function delete($dn)
    {
        ldap_delete($this->connection, $dn);
    }
}