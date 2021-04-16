<?php

namespace epii\queue;

use epii\factory\pattern\FactorCommon;

class EpiiQueue extends FactorCommon
{
 
    public static function pop($wait = false, $del = false, $proj = "1")
    {
        return self::getDriver()->pop($wait , $del  ,$proj );
    }

    public static function push($data,$proj = "1")
    {
        return self::getDriver()->push($data  ,$proj );
    }

    public static function pushTaskSync($data,$proj = "1"){
        return self::getDriver()->pushTaskSync($data  ,$proj );
    }

    public static function popTask($proj = "1"){
        return self::getDriver()->popTask($proj );
    }
    public static function finishTask($task_id,$data){
        return self::getDriver()->finishTask($task_id,$data );
    }
}
