<?php

namespace App\Libraries;

use Validator;
use Image;
use Storage;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Ftp as Adapter;
use DB;

class FileLibrary
{
    public function imageOperations($image, $path) 
    {
        $this->imageResizeWitoutStamp($image, 200, $path, 's_');
        $this->imageResize($image, 600, $path, 'm_');
        $this->imageResize($image, 1024, $path, 'b_');
    }
    
    public function imageResizeWitoutStamp($image, $size, $path, $prefix)
    {
        return Image::make($image->getRealPath())
                ->resize(null, $size, function ($constraint) 
                {
                    $constraint->aspectRatio();
                })
                ->save($path.'/'.$prefix.$image->getFileName());
    }
    
    public function imageResize($image, $size, $path, $prefix)
    {
        return Image::make($image->getRealPath())
                ->resize(null, $size, function ($constraint) 
                {
                    $constraint->aspectRatio();
                })
                ->text(STAMP_TEXT, 10, 10, function($font) 
                {
                    $font->color(array(255, 255, 255, 0.9));
                    //$font->size(48);
                    //$font->color('#fdf6e3');
                    //$font->align('center');
                    //$font->valign('top');
                })
                ->save($path.'/'.$prefix.$image->getFileName());
    }
    
    public function fileUploaded($input, $rules = null)
    {
        foreach($_FILES[$input]['error'] as $error)
            if($error != 0)
                dd('error var');
        
        $disk = env('FILESYSTEM_DRIVER', 'uploads');
        $tempFolder = 'temps/';
        
        $temp = [];
        $files = \Request::file($input);
        foreach($files as $i => $file) 
        {
            $destinationPath = UPLOAD_PATH.date("/Y/m/d/");
            $fileName = $file->getClientOriginalName();

            $j = 1;
            $list = Storage::disk($disk)->allFiles($destinationPath);
            while(in_array($destinationPath.$fileName, $list))
            {
                $fileName = str_replace('.'.$file->getClientOriginalExtension(), '', $file->getClientOriginalName());
                $fileName .= '_'.($j++).'.'.$file->getClientOriginalExtension();
            }

            $temp[$i]['destinationPath'] = $destinationPath;
            $temp[$i]['fileName'] = $fileName;
            $temp[$i]['disk'] = $disk;
            $temp[$i]['move'] = $file->move($tempFolder.$destinationPath, $fileName);
            
            if(Storage::disk($disk)->put($destinationPath.$fileName, Storage::get($tempFolder.$destinationPath.$fileName)))
            {       
                if(strstr($file->getClientMimeType(), 'image'))
                {
                    $this->imageOperations($temp[$i]['move'], $tempFolder.$destinationPath); 

                    Storage::disk($disk)->put($destinationPath.'/s_'.$fileName, Storage::get($tempFolder.$destinationPath.'/s_'.$fileName));
                    Storage::disk($disk)->put($destinationPath.'/m_'.$fileName, Storage::get($tempFolder.$destinationPath.'/m_'.$fileName));
                    Storage::disk($disk)->put($destinationPath.'/b_'.$fileName, Storage::get($tempFolder.$destinationPath.'/b_'.$fileName));
                }
            }  
        }
        
        return $temp;
    }
    
    private function singleColumnControl($params)
    {
        $singleColumn = \Request::input('single_column');
        if($singleColumn != null && $singleColumn != $params['columnName']);
            return TRUE;
            
        return FALSE;
    }

    public function fileUploadEvent($params)
    {
        $control = $this->singleColumnControl($params);
        if($control == TRUE) return [];
            
        $column = $params['columnName'];

        $old = @\Request::input($column.'_old');
        
        if($old == NULL) $old = [];
        else $old = json_decode($old, TRUE);
        
        if($old == '') $old = []; 
        
        $oldReturn = [ $column => json_encode($old) ];

        if($params['type'] == 'delete') return $oldReturn;
        if($params['type'] == 'clone') return $oldReturn;
        if($params['type'] == 'restore') return $oldReturn;

        if(!isset($_FILES[$column])) return $oldReturn;

        $files = $this->fileUploaded($column);

        $return = [];
        foreach($files as $file)
        {
            $temp['destination_path'] = $file['destinationPath'];
            $temp['file_name'] = $file['fileName'];
            $temp['disk'] = $file['disk'];

            array_push($return, $temp);
        }

        $return = array_merge($old, $return);

        return [ $column => json_encode($return) ];
    }
}
