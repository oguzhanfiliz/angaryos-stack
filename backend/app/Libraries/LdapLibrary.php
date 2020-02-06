<?php

namespace App\Libraries;


class LdapLibrary
{
    private $connection;
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
    
    public function searchInLdap($filter)
    {
        $result = ldap_search($this->connection, $this->baseDn, $filter);
        $entries = ldap_get_entries($this->connection, $result);
        return $this->formatLdapEntries($entries);
    }
    
    public function formatLdapEntries($entries)
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
    
    
    
    /*private function userCompabilityControl($user)
    {
        if(!isset($user) || !isset($user->tc_no) || strlen($user->tc_no) == 0)
        {
            dd('Kullanıcı TC Bulunamadı', [$user]);
            
            \Log::alert('Kullanıcı TC yok! User: ' . json_encode($user));
            
            return FALSE;
        }
        
        return TRUE;
    }
    
    
    
    public function getUser($user)
    {
        if(!$this->userCompabilityControl($user)) return;      
        
        $filter='(cn='.$user->tc_no.')';
        $entries = $this->searchInLdap($filter);
        
        if(count($entries) == 0) return NULL;
        else return $entries[0];
    }
    
    private function deleteUser($user)
    {
        $ldapUser = $this->getUser($user);
        $r = ldap_delete($this->connection, $ldapUser['dn']);
        $ldapUser['deleted'] = $r;
        
        return $ldapUser;
    }
    
    public function deleteUserIfExist($user)
    {
        if(!$this->userCompabilityControl($user)) return;      
        
        $ldapUser = $this->getUser($user);
        if($ldapUser != NULL) $ldapUser = $this->deleteUser($user);
        
        return $ldapUser;
    }
    
    private function getUserDataForAdd($user)
    {
        $new["uidnumber"] = $user->id;
        $new["gidnumber"] = $user->id;
        $new["cn"] = $user->tc_no;
        $new["sn"] = $user->tc_no;
        $new["mail"] = $user->email;
        $new["uid"] = $user->name. ' ' . $user->surname;
        $new["objectclass"] = ['pardusAccount', 'inetOrgPerson', 'organizationalPerson', 'person', 'top', 'posixAccount', 'shadowAccount'];
        $new["userpassword"] = '{md5}' . base64_encode(pack('H*', md5($user->password)));
        $new["businesscategory"] = getAttributeWithCache('internet_yetkileri', $user->internet_yetki_id, 'name');
        $new["homedirectory"] = '/home/'.$user->tc_no;
        
        return $new;
    }
    
    private function createUser($user)
    {
        if(!$this->userCompabilityControl($user)) return;
        
        $ldapDepartment = $this->createDepartmentOUIfNotExist($user->department_id);
        
        $new = $this->getUserDataForAdd($user);
        
        $dn = 'cn='.$user->tc_no.','.$ldapDepartment['dn'];
        
        $r = ldap_add($this->connection, $dn, $new);
        
        $new['added'] = $r;
        
        return $new;
    }
    
    public function createOrUpdateUser($user)
    {
        if(!$this->userCompabilityControl($user)) return;
        
        $this->deleteUserIfExist($user);
        return $this->createUser($user);
    }
    
    private function getDeaprtmentName($departmentId)
    {
        return getAttributeWithCache('departments', $departmentId, 'name');
    }
        
    public function getDepartmentOU($departmentId)
    {
        $departmentName = $this->getDeaprtmentName($departmentId);
        if(strlen($departmentName) == 0) return NULL;
        
        $filter='(ou='.$departmentName.')';
        $entries = $this->searchInLdap($filter);
        
        if(count($entries) == 0) return NULL;
        else return $entries[0];
    }
    
    private function createDepartmentOU($departmentId)
    {
        $departmentName = $this->getDeaprtmentName($departmentId);
        $dn = 'ou='.$departmentName.',ou=Kullanıcılar,'.$this->baseDn;
        $new["objectclass"] = ['organizationalUnit', 'top'];
        
        $r = ldap_add($this->connection, $dn, $new);
        if(!$r) dd('Depatman eklenemedi!');
        
        $new['ou'] = $departmentName;
        $new['dn'] = $dn;

        return $new;
    }

    public function createDepartmentOUIfNotExist($departmentId)
    {
        $ldapDepartment = $this->getDepartmentOU($departmentId);
        if(count($ldapDepartment) != NULL) return $ldapDepartment;
        
        return $this->createDepartmentOU($departmentId);
    }*/
}