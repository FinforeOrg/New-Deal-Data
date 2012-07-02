<?php
class db{
	private $num_rows;
	private $result_set;
	private $mysql_error;
	
	public function error(){
		return $this->mysql_error;
	}
	
	public function mod_query($q_stmt){
		$this->reset();
		$res = mysql_query($q_stmt);
		if(!$res){
			$this->mysql_error = mysql_error();
			return false;
		}
		$this->num_rows = mysql_affected_rows();
		return true;
	}
	
	public function last_insert_id(){
		return mysql_insert_id();
	}
	
	public function select_query($q_stmt){
		$this->reset();
		$result = mysql_query($q_stmt);
		if(!$result){
			$this->mysql_error = mysql_error();
			return false;
		}
		$this->result_set = $result;
		$this->num_rows = mysql_num_rows($this->result_set);
		return true;
	}
	
	public function select_query_limited($q_stmt,$start,$limit){
		$this->reset();
		$q_stmt.=" LIMIT ".$start.",".$limit;
		$result = mysql_query($q_stmt);
		if(!$result){
			$this->mysql_error = mysql_error();
			return false;
		}
		$this->result_set = $result;
		$this->num_rows = mysql_num_rows($this->result_set);
		return true;
	}
	
	public function get_row(){
		//for single row query
		return mysql_fetch_assoc($this->result_set);
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
	
	public function get_result_set(){
		return $this->result_set;
	}
	
	public function get_result_set_as_array(){
		$arr = array();
		for($i=0;$i<$this->num_rows;$i++){
			$arr[$i] = mysql_fetch_assoc($this->result_set);
		}
		return $arr;
	}
	
	public function reset(){
		$this->num_rows = 0;
		$this->result_set = NULL;
		$this->mysql_error = "";
	}
}
$g_db = new db();
?>