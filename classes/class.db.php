<?php
/*******************
sng:21/jan/2013

This is an improved db class. This now use the connection link identifier in the queries.
That feature is needed when there are 2 codes running and both run insert query and then one call last_insert_id.
I am not sure whether there will be a problem or not but it is better to be explicit.

Also, mysql_real_escape_string need the connection link identifier in the queries

Also, this object now create its own connection during construction. No more global $conn (which create coupling)
I mean, what if it was $cg_conn? Also, what if the db connection was not opened? So, let us do our own stuff here.

Also, we create alias for mysql_real_escape_string

Also, we use the mysqli instead since mysql is being depricated

No object creation here. Let the users create their own instances

NOTE: need to provide same API with the db class as defined in classes/db.php

sng:22/jan/2013
We need a creation method otherwise creating the object and calling instance is a drag
*********************/
class db{
	private $num_rows;
	private $result_obj;
	private $error;
	
	private $link;
	
	private function __construct(){
		/*********
		to stop creation of instance directly
		*************/
	}
	
	/***********
	return the db obj or false on connection error
	**********/
	public static function create($db_host,$db_user,$db_user_pass,$db_name){
		$obj = new db();
		$link = mysqli_connect($db_host,$db_user,$db_user_pass,$db_name);
		if(!$link){
			return false;
		}
		$obj->link = $link;
		return $obj;
	}
	
	public static function connection_error(){
		return mysqli_connect_error();
	}
	
	
	public function error(){
		return $this->error;
	}
	
	public function mod_query($q_stmt){
		$this->reset();
		$ok = mysqli_query($this->link,$q_stmt);
		/*****
		remember that update,delete query can affect 0 rows (depending upon where clause). That does not mean that there is erro r in the query
		so we explicitly check for false
		********/
		if($ok===false){
			$this->error = mysqli_error($this->link);
			return false;
		}
		$this->num_rows = mysqli_affected_rows($this->link);
		return true;
	}
	/*****
	If the number is greater than maximal int value, mysqli_insert_id() will return a string.
	There is a gotcha when running extended inserts
	see http://in3.php.net/manual/en/mysqli.insert-id.php#74923
	$q = "insert into trash (name) values('beta'),('gamma')";
	Returns the id created for beta, instead of id created for gamma
	******/
	public function last_insert_id(){
		return mysqli_insert_id($this->link);
	}
	
	public function select_query($q_stmt){
		$this->reset();
		$result = mysqli_query($this->link,$q_stmt);
		if($result===false){
			$this->error = mysqli_error($this->link);
			return false;
		}
		$this->result_obj = $result;
		$this->num_rows = mysqli_num_rows($this->result_obj);
		return true;
	}
	
	public function select_query_limited($q_stmt,$start,$limit){
		$q_stmt.=" LIMIT ".$start.",".$limit;
		return $this->select_query($q_stmt);
	}
	
	public function has_row(){
		if($this->num_rows > 0){
			return true;
		}else{
			return false;
		}
	}
	
	public function row_count(){
		return $this->num_rows;
	}
	
	public function get_row(){
		//for single row query
		return mysqli_fetch_assoc($this->result_obj);
	}
	/************
	we should not use this now. The caller will not know that we are using
	mysqli result and require mysqli_fetch_assoc.
	rather, we use another class for this
	*************/
	public function get_result_set(){
		//return $this->result_obj;
		return new db_result($this->result_obj);
	}
	
	public function get_result_set_as_array(){
		$arr = array();
		for($i=0;$i<$this->num_rows;$i++){
			$arr[$i] = mysqli_fetch_assoc($this->result_obj);
		}
		return $arr;
	}
	
	public function reset(){
		$this->num_rows = 0;
		$this->result_obj = NULL;
		$this->error = "";
	}
	
	public function escape_string($str){
		return mysqli_real_escape_string($this->link,$str);
	}
}

class db_result{
	private $result_obj;
	private $num_rows;
	
	public function __construct($obj){
		$this->result_obj = $obj;
		$this->num_rows = mysqli_num_rows($this->result_obj);
	}
	public function row_count(){
		return $this->num_rows;
	}
	public function get_row(){
		//for single row query
		return mysqli_fetch_assoc($this->result_obj);
	}
}
?>