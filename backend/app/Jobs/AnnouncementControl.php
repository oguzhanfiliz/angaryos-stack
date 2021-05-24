<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use DB;

class AnnouncementControl implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $id;
    
    public function __construct($id)
    {
        $this->id = $id;
    }

    private function GetUsers($record)
    {
        $temp = [];

        if($record->all_users) return DB::table('users')->where('state', TRUE)->get();

        if(is_string($record->department_ids)) $record->department_ids = json_decode($record->department_ids);
        if($record->department_ids)
        {
            foreach(DB::table('users')->whereIn('department_id', $record->department_ids)->pluck('id') as $p) $temp[$p] = TRUE;
        }

        if(is_string($record->user_ids)) $record->user_ids = json_decode($record->user_ids);
        if($record->user_ids)
            foreach($record->user_ids as $p) 
                $temp[$p] = TRUE;

        return DB::table('users')->whereIn('id', array_keys($temp))->get();
    }

    public function handle()
    {
        $record = DB::table('announcements')->find($this->id);
        $users = $this->GetUsers($record);
        
        if($record->sms)
        {
            $sms = $record->title . ': '.$record->announcement;
            foreach($users as $user)
                if(strlen($user->phone) > 0)
                {
                    $control = \App\Libraries\MessageLibrary::sendSms($user->phone, $sms, TRUE);
                    if(!isset($control['response'])) 
                        \Log::alert('Announcement send sms error: '.json_encode([$record, $user, $control, \App\Libraries\MessageLibrary::$lastSmsResponse]));
                }
        }
dd(9);
        if($record->mail)
        {
            $emailsWithNames = [];

            foreach($users as $user) 
                if(strlen($user->email) > 0)
                {
                    $temp =
                    [
                        'email' => $user->email,
                        'name' => $user->name_basic.' '.$user->surname
                    ];
                    array_push($emailsWithNames, $temp);
                }
                
            
            if(count($emailsWithNames) > 0)
            {
                $temp = \App\Libraries\MessageLibrary::sendMail($record->title, $record->announcement, $emailsWithNames);
                if(count($temp) > 0) 
                    \Log::alert('Announcement send mail error: '.json_encode([$record, $user, $temp]));
            }
        }

        if($record->notification)
            foreach($users as $user) 
                send_firebese_notify($record->title, $record->announcement, $user);
    }
}