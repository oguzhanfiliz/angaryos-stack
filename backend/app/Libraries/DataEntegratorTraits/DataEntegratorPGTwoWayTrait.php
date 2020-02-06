<?php

namespace App\Libraries\DataEntegratorTraits;

use DB;

trait DataEntegratorPGTwoWayTrait 
{    
    private function EntegratePostgresqlTwoWayUpdateRecords($remoteConnection, $table, $remoteTable, $columnRelations)
    {
        $this->EntegratePostgresqlToDataSourceUpdateRecords($remoteConnection, $table, $remoteTable, $columnRelations);
        $this->EntegratePostgresqlFromDataSourceUpdateRecords($remoteConnection, $table, $remoteTable, $columnRelations);
    }
}