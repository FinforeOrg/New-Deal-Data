<?php
include(dirname(dirname(__FILE__))."/include/global.php");
include(dirname(__FILE__)."/cron_util.php");
include(dirname(dirname(__FILE__))."/classes/class.make_me_top.php");
/**********
sng:6/sep/2010
We first check whether there is any paused job that is to be resumed
*********/
/*******************************************************************************
$resume_arr = array();
$resume_count = 0;
$success = $g_maketop->get_all_paused_jobs($resume_arr,$resume_count);
if(!$success){
	log_cron("Cannot fetch paused requests");
	die();
}
if($resume_count > 0){
	for($k=0;$k<$resume_count;$k++){
		$resume_job_id = $resume_arr[$k]['job_id'];
		//first mark it to running
		$success = $g_maketop->resume_job($resume_job_id);
		if(!$success){
			log_cron("error resuming job");
			die();
		}
		//fire a slave with resume option
		exec_in_background("slave_make_me_top_search.php",$resume_job_id." resume");
		log_cron("resumed job ".$resume_job_id);
	}
}else{
	//nothing to resume
	log_cron("No paused job to resume");
	//carry on
}
sng: 16/sep/2010
We have got a dedicated server. there is no limit on script execution. That means
Our self imposed limit is longer needed and saving of execution state and resuming is no longer
needed.
**********************************************************************************************/
/************************************
sng:30/aug/2010
Now when we check the queue, we get all the pending jobs which are not scheduled.
For each, we start a background job and mark it as scheduled
***************************************/
$job_arr = array();
$job_count = 0;
$success = $g_maketop->get_all_pending_requests($job_arr,$job_count);
if(!$success){
	log_cron("Error fetching pending request");
	die();
}
if(0==$job_count){
	log_cron("no pending make me top request");
	exit;
}
//for each, get job id, mark as scheduled and start a background process
//do not mark in progress here
for($i=0;$i<$job_count;$i++){
	$job_id = $job_arr[$i]['job_id'];
	$success = $g_maketop->request_scheduled($job_id);
	if(!$success){
		log_cron("error updating schedule status of a request");
		die();
	}
	/********************
	marked as scheduled, now we can safely run. In the next iteration, even if this job has not started
	it is not scheduled to a slave again, since is_scheduled is y and we fetch jobs where is_scheduled is n.
	 (since we started a slave on it sooner or later the slave will start and mark the job as in progress)
	also entry is added to tombstone_top_search_request_processing_helper and set is_running: y
	The job is not given to another slave to resume (since is-running is y and we only resume where status is: in progress and is_running:n)
	***************/
	
	//fire a slave
	exec_in_background("slave_make_me_top_search.php",$job_id);
	log_cron("scheduled job ".$job_id);
}

function log_cron($msg){
	$time_now = date("Y-m-d H:i:s");
	$q = "insert into ".TP."top_search_scheduling_log set `datetime`='".$time_now."', msg='".$msg."'";
	mysql_query($q);
}
?>