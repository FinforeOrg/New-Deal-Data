<?php
/****
example
xlsBOF();   // begin Excel stream 
xlsWriteLabel(0,0,"This is a label");  // write a label in A1, use for dates too 
xlsWriteNumber(0,1,9999);  // write a number B1 
xlsEOF(); // close the stream
******/
class simple_excel_writer{
	// Excel begin of file header 
	function xlsBOF() { 
		echo pack("ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0);  
		return; 
	} 
	// Excel end of file footer 
	function xlsEOF() { 
		echo pack("ss", 0x0A, 0x00); 
		return; 
	} 
	// Function to write a Number (double) into Row, Col
	//row, col is zero based 
	function xlsWriteNumber($Row, $Col, $Value) { 
		echo pack("sssss", 0x203, 14, $Row, $Col, 0x0); 
		echo pack("d", $Value); 
		return; 
	} 
	// Function to write a label (text) into Row, Col 
	function xlsWriteLabel($Row, $Col, $Value ) { 
		$L = strlen($Value); 
		echo pack("ssssss", 0x204, 8 + $L, $Row, $Col, 0x0, $L); 
		echo $Value; 
		return; 
	} 
}
$g_exl_writer = new simple_excel_writer();
?>