<?php

use Illuminate\Database\Seeder;
use App\BaseModel;
use \Carbon\Carbon;

class BaseSeeder extends Seeder
{
    private $host;
    private $user;
    private $publicUser;
    private $robotUser;
    
    private function clear_cache()
    {
        $m = new Memcached();
        $m->addServer(env('MEMCACHED_HOST', 'localhost'), 11211);

        $m->flush();
    }
    
    private function clear_db()
    {
        DB::select('drop schema if exists public cascade');
        DB::select('create schema public');
    }
    
    private function postgis_enable()
    {
        DB::select("CREATE EXTENSION postgis;");
        echo 'PostGIS OK'."\n";
    }
    
    private function add_custom_projection()
    {
        $sql = "insert into spatial_ref_sys values("
               ."7932, 'EPSG', 7932, "
               .'\'PROJCS["ITRF96 / TM30", GEOGCS["ITRF 1996", DATUM["International Terrestrial Reference Frame 1996", SPHEROID["GRS 1980", 6378137, 298.257222101, AUTHORITY["EPSG", "7019"]], AUTHORITY["EPSG", "6654"]], PRIMEM["Greenwich", 0, AUTHORITY["EPSG", "8901"]], UNIT["Degree", 0.017453292519943295, AUTHORITY["EPSG", "9102"]]], PROJECTION["Transverse_Mercator", AUTHORITY["EPSG", "9807"]], PARAMETER["Central_Meridian", 30], PARAMETER["Latitude_Of_Origin", 0], PARAMETER["Scale_Factor", 1], PARAMETER["False_Easting", 500000], PARAMETER["False_Northing", 0], UNIT["Metre", 1, AUTHORITY["EPSG", "9001"]], AUTHORITY["EPSG", "7932"]]\','
               .'\'+proj=tmerc +lat_0=0 +lon_0=30 +k=1 +x_0=500000 +y_0=0 +ellps=GRS80 +units=m +no_defs\')';
        
        DB::select($sql); 
        echo 'Costum Proj OK'."\n";
    }


    private function postgis_operations()
    {
        $this->postgis_enable();
        $this->add_custom_projection();
    }
    
    private function clear()
    {
        $this->clear_cache();
        $this->clear_db();
        
        echo 'Clear OK'."\n";
    }
    
    private function migrate()
    {
        $exitCode = Artisan::call('migrate');
        if($exitCode != 0)
        {
            echo 'Migrate Hata: ' .$exitCode."\n";
            exit(0);
        }
        else 
            echo 'Migrate OK'."\n";
    }
    
    private function insert_data()
    {
        echo 'Insert Started'."\n";
        require('data/index.php');
        echo 'Insert Finished'."\n";
    }
    
    public function confirmation()
    {
        $onay = $this->command->ask('Bu işlem tüm veri tabanını siler! Eminseniz "ONAYLIYORUM" yazın?', "NO");
        if($onay != "ONAYLIYORUM") exit(0);
    }
    
    private function get_base_record()
    {
        //$now = new Carbon();
        
        return [
            'state' => TRUE,
            'user_id' => $this->user->id,
            'own_id' => $this->user->id,
            //'created_at' => $now,
            //'updated_at' => $now
        ];
    }
    
    private function get_type_column_relation_data($tables, $columns, $table_name)
    {
        return [
            'relation_table_id' => $tables[$table_name]->id,
            'relation_source_column_id' => $columns['id']->id,
            'relation_display_column_id' => $columns['name']->id,
        ];
    }
    
    public function fill_infos()
    {
        $this->user = new BaseModel('users');
        $this->user->id = 1;
        
        $this->publicUser = new BaseModel('users');
        $this->publicUser->id = 2;
        
        $this->robotUser = new BaseModel('users');
        $this->robotUser->id = 3;
        
        //$this->host = $this->command->ask('Host bilgisi giriniz (Örneğin omertest.kozid.local)', "omertest.kozid.local");
        $this->host = 'omertest.kozid.local';
    }
    
    private function geoServerOperations()
    {
        $helper = new \App\Libraries\GeoServerLibrary(
                                                env('GEOSERVER_URL', 'http://geoserver:8080/geoserver/'),
                                                env('GEOSERVER_USER', 'admin'),
                                                env('GEOSERVER_PASSWORD', 'geoserver'));
        
        $helper->workspaceName = env('GEOSERVER_WORKSPACE', 'angaryos');
        $helper->dataStoreName = env('GEOSERVER_DATA_STORE', 'angaryos');
        
        $this->geoserverWorkspaceOperation($helper);
        $this->geoserverDataStoreOperation($helper);
    }
    
    private function geoserverWorkspaceOperation($helper)
    {
        $workspaces = $helper->listWorkspaces();
        if($workspaces == NULL) dd('Geoserver unavilable');
        
        $control = TRUE;
        if($workspaces->workspaces != '')
            foreach($workspaces->workspaces->workspace as $w)
                if($w->name == $helper->workspaceName)
                {
                    $control = FALSE;
                    break;
                }

        if($control)
            $helper->createWorkspace($helper->workspaceName);
        
        echo 'GeoServer Workspace OK'."\n";
    }
    
    private function geoserverDataStoreOperation($helper)
    {        
        $dataStores = $helper->listDatastores($helper->workspaceName);
        if($dataStores == NULL) dd('Geoserver unavilable');
        
        $control = TRUE;
        if($dataStores->dataStores != '')
            foreach($dataStores->dataStores->dataStore as $d)
                if($d->name == $helper->dataStoreName)
                {
                    $control = FALSE;
                    break;
                }
                
        if($control)
            $helper->createPostGISDataStore(
                    $helper->dataStoreName, 
                    $helper->workspaceName, 
                    env('DB_DATABASE', 'postgres'), 
                    env('DB_USERNAME', 'postgres'), 
                    env('DB_PASSWORD', '1234Aa.'), 
                    env('DB_HOST', '1234Aa.'));
        
        echo 'GeoServer Data Store OK'."\n";
    }

    public function run()
    {
        //$this->confirmation();        
        $this->fill_infos();
        
        DB::beginTransaction();
        
        $this->clear();
        $this->postgis_operations();
        $this->migrate();
        
        $this->insert_data();
        
        $this->geoServerOperations();
        
        DB::commit();
    }
}
