<?php
    require_once(dirname(dirname(dirname(__FILE__))) . "/include/global.php");  
    require_once(dirname(dirname(dirname(__FILE__))) . "/classes/class.company.php"); 
	require_once(dirname(dirname(dirname(__FILE__))) . "/include/global.php");
	require_once("classes/db.php");
	 
    
    if (!isset($_REQUEST['action']) || empty($_REQUEST['action'])) {
        myJson(array('message' => 'Your request is invalid.'));
    }  

    switch ($_REQUEST['action']) {
        case 'autocomplete' :
            doAutocomplete();
        break;
        case 'submitData' :
            doTakeSubmit();
        break;
    }
                                   
    function myJson($array) {
        echo json_encode($array);
        exit(0);
    }
    
    function doAutocomplete() {
        $return = array();  
        if (!isset($_GET['item'])) {
          return myJson($return);
        }

        $item = $_GET['item'];
        $term =  isset($_GET['term']) ?  $_GET['term'] : '';

        switch ($item) {
          case 'country':
            $q = "SELECT name FROM %s WHERE name LIKE '%%%s%%' LIMIT 5";
            $q = sprintf($q, TP . 'country_master', mysql_escape_string($term));
            if (!$res = mysql_query($q)) {
                myJson($return);
            }
            while ($row = mysql_fetch_assoc($res)) {
                array_push($return, array('label' => $row['name']));
            }
            
            myJson($return);
          break;
          
        }        
    }
    
    function doTakeSubmit() {
		
		require("create_deal_detailed.php");
		return;
    }
	
?>