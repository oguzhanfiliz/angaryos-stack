<?php

namespace App\Libraries;

trait DataEntegratorPGTrait 
{    
    private function EntegratePostgresql($dataSource, $tableRelation, $direction)
    {
        $this->{'EntegratePostgresql'.ucfirst($direction->name)}($dataSource, $tableRelation);
        //uzaktaki kaydı sync yaparsan json bi file a buraya kayıt yap veri kaybı olmasın
        //updated_at_kololnu ilişkisi olmazsa log yaz
        //başladıkça log yaz
    }        
}