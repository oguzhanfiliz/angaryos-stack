<?php
$key = 'allAuths';

//Cache::flush();

return Cache::rememberForever($key, function()
{      
    $geometryColumnTypes = ['point', 'linestring', 'polygon', 'multipoint', 'multilinestring', 'multipolygon'];
    
    $displays =
    [
        'creates' => 'Ekleme',
        'edits' => 'Güncelleme', 
        'lists' => 'Liste', 
        'deleteds' => 'Silinen',
        'queries' => 'Sorgu',
        'shows' => 'Bilgi Kartı'
    ];

    $filterDisplays =
    [
        'list' => 'Liste',
        'update' => 'Güncelleme',
        'delete' => 'Silme', 
        'restore' => 'Geri Yükleme', 
        'show' => 'Bilgi Kartı', 
        'export' => 'Dışa Aktarma'
    ];
    
    $auths = [];

    
    
    /****        ****/
    
    $auths['admin:userImitation:0:0'] = 'Kullanıcı taklit';
    $auths['admin:authWizard:0:0'] = 'Yetki oluşturma yardımcısı';
    $auths['admin:dataEntegrator:0:0'] = 'Veri Aktarıcı';

    
    $auths['map:0:0:0'] = 'Harita';    
    $auths['map:kmz:upload:0'] = 'Upload KMZ';

    
    
    /****        ****/
    
    $dataFilters = \DB::table('data_filters')->where('state', TRUE)->get();
    foreach($dataFilters as $i => $dataFilter)
        $dataFilters[$i]->type = get_attr_from_cache('data_filter_types', 'id', $dataFilter->data_filter_type_id, 'name');

    foreach(\DB::table('tables')->where('state', TRUE)->get() as $table)
    {
        $source = 'dashboards:RecordCount:'.$table->name.':0';
        $display = 'Göstergeler Kayıt Sayısı '.$table->display_name;
        $auths[$source] = $display;
        
        
        $source = 'tables:'.$table->name.':option:0';
        $display = 'Tablolar ' . $table->display_name. ' Özellik Menü Gizle';
        $auths[$source] = $display;
        
        
        $columnIds = json_decode($table->column_ids);
        foreach($columnIds as $columnId)
        {
            $columnDbTypeId = get_attr_from_cache('columns', 'id', $columnId, 'column_db_type_id');
            $columnDbType = get_attr_from_cache('column_db_types', 'id', $columnDbTypeId, 'name');
            
            if(!in_array($columnDbType, $geometryColumnTypes)) continue;

            $source = 'tables:'.$table->name.':maps:0';
            $display = 'Tablolar ' . $table->display_name. ' Harita Tüm Yetkiler';
            $auths[$source] = $display;
            
            $source = 'tables:'.$table->name.':maps:1';
            $display = 'Tablolar ' . $table->display_name. ' Harita Katman';
            $auths[$source] = $display;
            
            $source = 'tables:'.$table->name.':maps:2';
            $display = 'Tablolar ' . $table->display_name. ' Harita Arama';
            $auths[$source] = $display;
            
            $source = 'tables:'.$table->name.':maps:3';
            $display = 'Tablolar ' . $table->display_name. ' Harita Filtre';
            $auths[$source] = $display;
            
            break;
        }
        
        
        $source = 'tables:'.$table->name.':delete:0';
        $display = 'Tablolar ' . $table->display_name. ' Kayıt Sil';
        $auths[$source] = $display;
        
        $source = 'tables:'.$table->name.':restore:0';
        $display = 'Tablolar ' . $table->display_name. ' Kayıt Geri Yükle';
        $auths[$source] = $display;
            
        foreach(['creates', 'lists', 'queries', 'edits', 'shows', 'deleteds'] as $type)
        {
            $source = 'tables:'.$table->name.':'.$type.':0';
            $display = 'Tablolar ' . $table->display_name. ' ' . $displays[$type] . ' Tüm Kolonlar';
            $auths[$source] = $display;
        }

        foreach(\DB::table('column_arrays')->where('table_id', $table->id)->get() as $columnArray)
            foreach(['lists', 'queries', 'deleteds'] as $type)
            {
                $source = 'tables:'.$table->name.':'.$type.':'.$columnArray->id;
                $display = 'Tablolar ' . $table->display_name. ' ' . $displays[$type] . ' ' . $columnArray->name_basic . ' (id: '.$columnArray->id.')';
                $auths[$source] = $display;
            }

        foreach(\DB::table('column_sets')->where('table_id', $table->id)->get() as $columnSet)
            foreach(['creates', 'edits', 'shows'] as $type)
            {
                $source = 'tables:'.$table->name.':'.$type.':'.$columnSet->id;
                $display = 'Tablolar ' . $table->display_name. ' ' . $displays[$type] . ' ' . $columnSet->name_basic. ' (id: '.$columnSet->id.')';
                $auths[$source] = $display;
            }

        foreach($dataFilters as $dataFilter)
        {
            $source = 'filters:'.$table->name.':'.$dataFilter->type.':'.$dataFilter->id;
            $display = 'Tablolar ' . $table->display_name. ' ' . $filterDisplays[$dataFilter->type] . ' Filtresi ' . $dataFilter->name_basic. ' (id: '.$dataFilter->id.')';
            $auths[$source] = $display;
        }
    }
    
    foreach(\DB::table('missions')->where('state', TRUE)->get() as $mission)
    {
        $source = 'missions:'.$mission->id.':0:0';
        $display = 'Görevler ' . $mission->name. ' Tetikleme';
        $auths[$source] = $display;
    }
    
    foreach(\DB::table('external_layers')->where('state', TRUE)->get() as $layer)
    {
        $source = 'external_layers:'.$layer->id.':0:0';
        $display = 'Ek Katman ' . $layer->name;
        $auths[$source] = $display;
    }
    
    foreach(\DB::table('custom_layers')->where('state', TRUE)->get() as $layer)
    {
        $source = 'custom_layers:'.$layer->id.':0:0';
        $display = 'Revize Katman ' . $layer->name;
        $auths[$source] = $display;
    }
    
    foreach(\DB::table('table_groups')->where('state', TRUE)->get() as $group)
    {
        $source = 'table_groups:0:0:'.$group->id;
        $display = 'Tablo Grubu ' . $group->name_basic;
        $auths[$source] = $display;
    }
    
    foreach(\DB::table('auth_groups')->where('state', TRUE)->get() as $auth)
        $auths[$auth->id] = $auth->name_basic;
    

    
    return $auths;        
});