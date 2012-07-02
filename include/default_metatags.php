<?php
require_once("classes/class.sitesetup.php");
//get the default metatags
$g_view['meta_data'] = NULL;
$success = $g_site->get_metatags($g_view['meta_data']);
if(!$success){
	die("Cannot get metatag data");
}

$g_view['meta_title'] = $g_view['meta_data']['meta_title'];
$g_view['meta_keywords'] = $g_view['meta_data']['meta_keywords'];
$g_view['meta_description'] = $g_view['meta_data']['meta_description'];
?>