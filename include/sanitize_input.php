<?php
/********
sng:22/oct/2010
include this to sanitise all GET and POST
Strips all html from inputs, since we allow only plain text.
***/
require_once("include/strip_html_tags.php");
foreach($_GET as $get_key=>$get_val){
	$_GET[$get_key] = strip_tags(strip_html_tags($get_val));
}
foreach($_POST as $post_key=>$post_val){
	/*********
	sng:8/jan/2011
	sometines, we send selection from multiple checkboxes. The value is then an array
	if so, we skip, otherwise the array seems to be deleted
	*********/
	if(is_array($post_val)){
		continue;
	}
	$_POST[$post_key] = strip_tags(strip_html_tags($post_val));
}
?>