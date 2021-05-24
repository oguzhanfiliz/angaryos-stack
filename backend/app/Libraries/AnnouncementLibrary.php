<?php

namespace App\Libraries;

use DB;

class AnnouncementLibrary 
{
    public function Control()
    {
        send_log('info', 'Announcement tick start');

        DB::beginTransaction();
        
        $ayar = 'En son duyuru';
        $temp = DB::table('settings')->where('name', $ayar)->first();
     
        if(!$temp)
        {
            \Log::alert('"'.$ayar.'" ayarı bulunamadı!');
            return FALSE;
        }

        $last = new \Carbon\Carbon($temp->value);
        $now = new \Carbon\Carbon();
        $now->addMinute(-1);

        $i = 0;
        while($last <= $now)
        {
            $t1 = $last->toDateTimeString();
            $last->addMinute();
            $t2 = $last->toDateTimeString();
            
            $where = "(start_time >= '$t1' and start_time < '$t2')"; 
            $announcements = DB::table('announcements')->where('state', TRUE)->whereRaw($where)->get();
            foreach($announcements as $announcement) 
                \App\Jobs\AnnouncementControl::dispatchNow($announcement->id);

            if(++$i >= 30)
            {
                break;
                $last->addMinute(-1);
            }
        }

        DB::table('settings')->where('id', $temp->id)->update(['value' => $last->toDateTimeString()]);
        DB::commit();

        send_log('info', 'Announcement tick OK');
    }
}