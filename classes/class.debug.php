<?php
/*******************
Debugging

Sending debug message to the browser is not pretty, it mess up the html.
Sometime it is quite problematic, when php code is called via ajax.
Sometime the output is not sent to the browser at all. The code is called in cURL.

Sometime we just want to trap error in php execution.

We decide to log the errors in a file.

Also, the coder should be able to just call the methods without bothering about sending the file and line number.
**************************/
class debug{
	private $output_file;
	
	public function __construct($output_file,$append=true){
		/***************
		if not append, delete the file first
		*********************/
		if(!$append){
			if(file_exists($output_file)){
				unlink($output_file);
			}
		}
		$this->output_file = $output_file;
		set_error_handler(array($this,"my_error_handler"));
	}
	
	public function get_output_file(){
		return $this->output_file;
	}
	
	public function my_error_handler($error_level,$err_string,$err_file,$err_line){
		$this->log_to_file($err_string,$err_file,$err_line);
	}
	
	public function debug_msg($msg){
		$err_file = "";
		$err_line = 0;
		$this->get_caller($err_file,$err_line);
		$this->log_to_file($msg,$err_file,$err_line);
	}
	
	public function print_r($expression){
		$err_file = "";
		$err_line = 0;
		$this->get_caller($err_file,$err_line);
		
		$err_msg = print_r($expression,true);
		$err_msg.="\r\n";
		$this->log_to_file($err_msg,$err_file,$err_line);
	}
	
	private function log_to_file($msg,$file,$line){
		$err_msg = $file." [".$line."]\r\n";
		$err_msg.=$msg."\r\n";
		error_log($err_msg,3,$this->output_file);
	}
	
	private function get_caller(&$file,&$line){
		$stack = debug_backtrace();
		//var_dump($stack);
		$caller = $stack[1];
		$file = $caller['file'];
		$line = $caller['line'];
		return;
	}
}
?>