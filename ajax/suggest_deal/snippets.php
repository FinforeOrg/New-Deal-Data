<?php
    $snippet = $_GET['snippet'];
	/********
	sng:8/jun/2011
	Sometime we will have to use dynamic code here, so we check for a corresponding .php file first
	****************/
	if (!empty($snippet) && file_exists($file = dirname(__FILE__) . '/snippets/' . $snippet . '.php')) {
        require $file;
    }else{
		if (!empty($snippet) && file_exists($file = dirname(__FILE__) . '/snippets/' . $snippet . '.html')) {
        	require $file;
    	}
	}
?>