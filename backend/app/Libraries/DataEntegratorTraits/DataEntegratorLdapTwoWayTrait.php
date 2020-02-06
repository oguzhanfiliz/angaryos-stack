<?php

namespace App\Libraries\DataEntegratorTraits;

use DB;

trait DataEntegratorLdapTwoWayTrait 
{    
    private function EntegrateLdapTwoWayUpdateRecords($remoteConnection, $table, $remoteTable, $columnRelations)
    {
        $this->EntegrateLdapToDataSourceUpdateRecords($remoteConnection, $table, $remoteTable, $columnRelations);
        $this->EntegrateLdapFromDataSourceUpdateRecords($remoteConnection, $table, $remoteTable, $columnRelations);
    }
}