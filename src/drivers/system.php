<?php

namespace epii\queue\drivers;

use epii\queue\IQueue;

class system  implements IQueue{
    private $_dir= null;
    public  function init($config)
    {
        $dir =  $config["pids_dir"];
 
        if (!$dir) {
            $dir = dirname(__FILE__) . "/pids";
        }
        $this->_dir = $dir;
        if (!is_dir($this->_dir)) {
            mkdir($this->_dir, 0777, true);
        }
    }

    public function require_configs()
    {
        return ["pids_dir"];
    }
    
    private   function get_queue($proj)
    {
      
        if (isset($this->_map[$proj])) {
            return $this->_map[$proj];
        }
        $file = $this->_dir . DIRECTORY_SEPARATOR . $proj . ".pid";
        if (!is_file($file)) {
            file_put_contents($file, "1");
        }
        $msg_key = ftok($file, 1);
        return  msg_get_queue($msg_key, 0666);
    }
    public   function info($proj = "1")
    {
        return msg_stat_queue($this->get_queue($proj));
    }
    public   function pop($wait = false, $del = false, $proj = "1")
    {
        if (!$wait) {
            $info = $this->info($proj);
            if (!$info) return false;
            if ($info["msg_qnum"] == 0) return false;
        }
        $q = $this->get_queue($proj);
        msg_receive($q, 0, $message_type, 1024, $message);
        if ($del) {
            $info = $this->info($proj);
            
            if ($info["msg_qnum"] == 0) {
                msg_remove_queue($q);
                $file = $this->_dir . DIRECTORY_SEPARATOR . $proj . ".pid";
               
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }
        $data = json_decode($message,true);
        return  $data["data"];
    }

    public   function push($data,$proj = "1")
    {
        $data  = ["data"=>$data];
        $out  = msg_send($this->get_queue($proj), 1,  json_encode($data, JSON_UNESCAPED_UNICODE));
        return $out;
    }

    public   function pushTask($data,$proj = "1"){
         $t_data = ["task_id"=>uniqid("epii_"),"data"=>$data];
         $this->push($t_data,$proj);
         return $this->pop(true,true,$t_data["task_id"]);
    }

    public   function popTask($proj = "1"){
        $r = $this->pop(false,false,$proj);
        if(!$r) return false;
        return  $r  ;  
    }
    public   function finishTask($task_id,$data){
        return $this->push($data,$task_id);
    }
}