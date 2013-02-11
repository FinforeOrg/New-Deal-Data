<?php
/*******************
Debugging

Sending debug message to the browser is not pretty, it mess up the html.
Sometime it is quite problematic, when php code is called via ajax.
Sometime the output is not sent to the browser at all. The code is called in cURL.

Sometime we just want to trap error in php execution.

We decide to log the errors in a file.
**************************/
class debug{
	private $output_file;
	
	public function __construct($output_file){
		$this->output_file = $output_file;
		set_error_handler(array($this,"my_error_handler"));
	}
	public function my_error_handler($error_level,$err_string,$err_file,$err_line){
		$this->log_to_file($err_string,$err_file,$err_line);
	}
	
	public function debug_msg($msg,$err_file,$err_line){
		$this->log_to_file($msg,$err_file,$err_line);
	}
	
	public function print_r($expression){
		$err_msg = print_r($expression,true);
		$err_msg.="\r\n";
		$this->log_to_file($err_msg,"unspecified",0);
	}
	
	private function log_to_file($msg,$file,$line){
		$err_msg = $file." [".$line."]\r\n";
		$err_msg.=$msg."\r\n";
		error_log($err_msg,3,$this->output_file);
	}
}
?>