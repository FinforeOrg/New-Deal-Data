<?php
/******************
sng:15/oct/2012

These are for special background process. Only one instance of a particular slave can run at any given time.
For example, at a time, only one process can fetch data from remote server. If two such processes are running, there
will be duplication.

NOTE: This is a LINUX ONLY solution
******************/
class background_slave_controller{
	/******
	check whether the slave with this name is still running or not.
	We get the pid and do a check
	
	slave_name: id name of the slave process we want to check
	running: set to true if the process is running
	
	return false on db error or if slave record not found
	
	sng:30/oct/2012
	If not running, we also need to know when it ran last
	**********/
	public function is_running($slave_name,&$running,&$last_triggered){
		$db = new db();
		$q = "select pid,last_triggered from ".TP."background_slave_monitor where slave_name='".mysql_real_escape_string($slave_name)."'";
		$ok = $db->select_query($q);
		if(!$ok){
			return false;
		}
		if(!$db->has_row()){
			/***
			no such slave record in db
			maybe we forgot to add the record when we wrote the file
			****/
			return false;
		}
		$row = $db->get_row();
		$last_triggered = $row['last_triggered'];
		$pid = $row['pid'];
		if(""==$pid){
			//the process is not running
			$running = false;
			return true;
		}
		/******
		There is a pid stored, so we use unix command to check
		*****/
		$cmd = "ps ".$pid;
    	$cmd_out = array();
    	exec($cmd,$cmd_out);
    	$running = (count($cmd_out) >= 2);
		return true;
	}
	
	/*******
	start a slave code in the background
	do check if it is already running or not
	
	It is assumed that all the codes are in <server file path>/cron folder 
	************/
	public function trigger_slave($slave_name,&$started,&$msg){
		$db = new db();
		$already_running = false;
		$last_triggered = "";
		$ok = $this->is_running($slave_name,$already_running,$last_triggered);
		if(!$ok){
			return false;
		}
		if($already_running){
			$started = false;
			$msg = "The previous run has still not completed";
			return true;
		}
		/**********
		Not started at all or has finished the prev run
		get the code file and fork a process, and store the pid along with the start time
		**********/
		$q = "select code_file from ".TP."background_slave_monitor where slave_name='".mysql_real_escape_string($slave_name)."'";
		$ok = $db->select_query($q);
		if(!$ok){
			return false;
		}
		if(!$db->has_row()){
			/***
			no such slave record in db
			maybe we forgot to add the record when we wrote the file
			****/
			return false;
		}
		$row = $db->get_row();
		$code_file = $row['code_file'];
		if(""==$code_file){
			//which file to run?
			return false;
		}
		$op = array();
		exec('/usr/bin/php -f '.FILE_PATH.'/cron/'.$code_file.' > /dev/null 2>&1 & echo $!',$op);
		$pid = (int)$op[0];
		$updt_q = "update ".TP."background_slave_monitor set pid='".$pid."',last_triggered='".date('Y-m-d H:i:s')."' where slave_name='".mysql_real_escape_string($slave_name)."'";
		$ok = $db->mod_query($updt_q);
		if(!$ok){
			return false;
		}
		$started = true;
		return true;
	}
	
	/**************
	sng:16/oct/2012
	*********/
	public function set_status_note($slave_name,$note){
		$db = new db();
		$updt_q = "update ".TP."background_slave_monitor set status_note='".mysql_real_escape_string($note)."' where slave_name='".mysql_real_escape_string($slave_name)."'";
		$ok = $db->mod_query($updt_q);
		if(!$ok){
			return false;
		}
		return true;
	}
	
}
?>