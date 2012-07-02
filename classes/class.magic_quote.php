<?php
/****
sng:23/oct/2010
It is better to rely on mysql_real_escape_string() than magic quote. We will call mysql_real_escape_string
when about to perform db operation to escape the single quote.
But the thing is, putting mysql_real_escape_string will take time. So why not use enable magic quote in the server and use it?
********/
class magic_quote{
	function view_to_db($data){
		return $data;
	}
	function view_to_view($data){
		return stripslashes($data);
	}
	function db_to_view($data){
		return $data;
	}
}
$g_mc = new magic_quote();
?>