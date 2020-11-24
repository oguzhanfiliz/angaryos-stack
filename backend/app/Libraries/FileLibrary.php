<?php

namespace App\Libraries;

use Validator;
use Image;
use Storage;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Ftp as Adapter;
use DB;

class CustomBase64Image
{
    public $fileName = '';
    public $realPath = '';

    public function getFileName()
    {
        return $this->fileName;
    }

    public function getRealPath()
    {
        return $this->realPath;
    }
}

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
    
    public function fileUploaded($input)
    {
        foreach($_FILES[$input]['error'] as $error)
            if($error != 0)
                dd('error var');
        
        $disk = env('FILESYSTEM_DRIVER', 'uploads');
        $tempPath = '/var/www/public/temps/';

        $temp = [];
        $files = \Request::file($input);
        foreach($files as $i => $file) 
        {
            $destinationYear = date("Y");
            $destinationMonth = date("m");
            $destinationDay = date("d");
            $destinationPath = $destinationYear.'/'.$destinationMonth.'/'.$destinationDay.'/';

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
            $temp[$i]['move'] = $file->move($tempPath.$destinationPath, $fileName);
            
            if(Storage::disk($disk)->put($destinationPath.$fileName, Storage::disk('temps')->get($destinationPath.$fileName)))
            {
                $conn_id = ftp_connect(env('FILE_HOST', 'ftp.url'));
                $login_result = ftp_login($conn_id, env('FILE_USER', 'user'), env('FILE_PASSWORD', 'password'));
                
                ftp_chmod($conn_id, 0777, env('FILE_ROOT', '/').$destinationYear);
                ftp_chmod($conn_id, 0777, env('FILE_ROOT', '/').$destinationYear.'/'.$destinationMonth);
                ftp_chmod($conn_id, 0777, env('FILE_ROOT', '/').$destinationYear.'/'.$destinationMonth.'/'.$destinationDay);
                @ftp_chmod($conn_id, 0777, env('FILE_ROOT', '/').$destinationYear.'/'.$destinationMonth.'/'.$destinationDay.'/'.$fileName);

                if(strstr($file->getClientMimeType(), 'image') || strstr($file->getClientMimeType(), 'octet-stream'))
                {
                    $this->imageOperations($temp[$i]['move'], $tempPath.$destinationPath); 
                    
                    Storage::disk($disk)->put($destinationPath.'/s_'.$fileName, Storage::disk('temps')->get($destinationPath.'/s_'.$fileName));
                    Storage::disk($disk)->put($destinationPath.'/m_'.$fileName, Storage::disk('temps')->get($destinationPath.'/m_'.$fileName));
                    Storage::disk($disk)->put($destinationPath.'/b_'.$fileName, Storage::disk('temps')->get($destinationPath.'/b_'.$fileName));

                    ftp_chmod($conn_id, 0777, env('FILE_ROOT', '/').$destinationPath.'s_'.$fileName);
                    ftp_chmod($conn_id, 0777, env('FILE_ROOT', '/').$destinationPath.'m_'.$fileName);
                    ftp_chmod($conn_id, 0777, env('FILE_ROOT', '/').$destinationPath.'b_'.$fileName);
                }

                ftp_close($conn_id);
            }  
        }
        
        return $temp;
    }

    private function base64Validation($base64Images)
    {
        $columns = ['name', 'image'];
        if(!is_array($base64Images)) custom_abort('base64.files.is.must.be.array');
        foreach($base64Images as $base64Image)
        {
            if(!is_array($base64Image)) custom_abort('base64.file.is.must.be.array');
            
            $keys = array_keys($base64Image);

            foreach($columns as $column)
                if(!in_array($column, $keys)) 
                    custom_abort('base64.file.is.have.column:'.$column);
        }
    }

    public function fileUploadedBase64Image($input)
    {
        $base64Images = \Request::input($input.'_base64_image');
        $this->base64Validation($base64Images);

        $disk = env('FILESYSTEM_DRIVER', 'uploads');
        $tempPath = '/var/www/public/temps/';

        $temp = [];

        
        $destinationYear = date("Y");
        $destinationMonth = date("m");
        $destinationDay = date("d");
        $destinationPath = $destinationYear.'/'.$destinationMonth.'/'.$destinationDay.'/';
        
        foreach($base64Images as $i => $base64Image)
        {
            $fileName = $base64Image['name'];
            
            $j = 1;
            $list = Storage::disk($disk)->allFiles($destinationPath); 
            while(in_array($destinationPath.$fileName, $list))
            {
                $fileName = str_replace('.'.$file->getClientOriginalExtension(), '', $file->getClientOriginalName());
                $fileName .= '_'.($j++).'.'.$file->getClientOriginalExtension();
            }

            $data = helper('base64_to_image', $base64Image['image']);
            if($data == NULL) custom_abort('base64.image.not.uploaded');

            if(!strstr($fileName, $data['ext'])) $fileName .= '.'.$data['ext'];
            
            $temp[$i]['destinationPath'] = $destinationPath;
            $temp[$i]['fileName'] = $fileName;
            $temp[$i]['disk'] = $disk;
            $temp[$i]['move'] = $destinationPath.$fileName;

            $f = new CustomBase64Image();
            $f->fileName = $temp[$i]['fileName']; 
            $f->realPath = $tempPath.$temp[$i]['move']; 
            
            if(!Storage::disk('temps')->put($temp[$i]['move'], $data['image'])) custom_abort('base64.image.not.moved.to.temp');
            
            if(Storage::disk($disk)->put($destinationPath.$fileName, Storage::disk('temps')->get($destinationPath.$fileName)))
            {
                $conn_id = ftp_connect(env('FILE_HOST', 'ftp.url'));
                $login_result = ftp_login($conn_id, env('FILE_USER', 'user'), env('FILE_PASSWORD', 'password'));

                ftp_chmod($conn_id, 0777, env('FILE_ROOT', '/').$destinationYear);
                ftp_chmod($conn_id, 0777, env('FILE_ROOT', '/').$destinationYear.'/'.$destinationMonth);
                ftp_chmod($conn_id, 0777, env('FILE_ROOT', '/').$destinationYear.'/'.$destinationMonth.'/'.$destinationDay);
                ftp_chmod($conn_id, 0777, env('FILE_ROOT', '/').$destinationYear.'/'.$destinationMonth.'/'.$destinationDay.'/'.$fileName);

                
                $this->imageOperations($f, $tempPath.$destinationPath); 
                    
                Storage::disk($disk)->put($destinationPath.'/s_'.$fileName, Storage::disk('temps')->get($destinationPath.'/s_'.$fileName));
                Storage::disk($disk)->put($destinationPath.'/m_'.$fileName, Storage::disk('temps')->get($destinationPath.'/m_'.$fileName));
                Storage::disk($disk)->put($destinationPath.'/b_'.$fileName, Storage::disk('temps')->get($destinationPath.'/b_'.$fileName));

                ftp_chmod($conn_id, 0777, env('FILE_ROOT', '/').$destinationPath.'s_'.$fileName);
                ftp_chmod($conn_id, 0777, env('FILE_ROOT', '/').$destinationPath.'m_'.$fileName);
                ftp_chmod($conn_id, 0777, env('FILE_ROOT', '/').$destinationPath.'b_'.$fileName);

                ftp_close($conn_id);
            }  
        }
        
        return $temp;
    }
    
    private function singleColumnControl($params)
    {
        $singleColumn = read_from_response_data('single_column');
        
        if($singleColumn != NULL && $singleColumn != $params['columnName'])
            return TRUE;
        
        return FALSE;
    }

    public function fileUploadEvent($params)
    {
        if($params['type'] == 'clone') return NULL;
        
        $control = $this->singleColumnControl($params);
        if($control == TRUE) return [];
            
        $column = $params['columnName'];

        $old = read_from_response_data($column.'_old');
        $base64Image = read_from_response_data($column.'_base64_image');
        
        if($old == NULL) $old = [];
        else $old = json_decode($old, TRUE);
        
        if($old == '' || $old == NULL) $old = []; 
        
        $oldReturn = [ $column => json_encode($old) ];

        if($params['type'] == 'delete') return $oldReturn;
        if($params['type'] == 'clone') return $oldReturn;
        if($params['type'] == 'restore') return $oldReturn;

        if($base64Image == NULL)
        {
            if(!isset($_FILES[$column])) return $oldReturn;
        
            $files = $this->fileUploaded($column);
        }
        else $files = $this->fileUploadedBase64Image($column);
        
        
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
