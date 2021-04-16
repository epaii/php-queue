<?php
namespace epii\queue;

use epii\factory\pattern\IDriverCommon;

interface IQueue extends IDriverCommon{
    public   function pop($wait = false, $del = false, $proj = "1");
    public   function push($data,$proj = "1");
    public   function pushTaskSync($data,$proj = "1");
    public   function popTask($proj = "1");
    public   function finishTask($task_id,$data);
}