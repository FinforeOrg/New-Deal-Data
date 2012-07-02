<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/classes/mobileApp.php');
$mobileApplication = new MobileApp();
  
  if (!isset($_GET['type'])) {
    echo $mobileApplication->getResponseForCode(0);    
  }
  
  switch ($_GET['type']) {
      case 'userLogin':
        ini_set('display_errors',1);
        error_reporting(E_ALL);
        echo $mobileApplication->authenticate();  
        break;
      case 'getCountries':
        echo $mobileApplication->getAllCountries();
        break;      
      case 'getIndustry':
        echo $mobileApplication->getAllIndustries();
        break; 
      case 'getAllMeetingTypes':
        echo $mobileApplication->getAllMeetingTypes();
        break;
      case 'getResult':
        echo $mobileApplication->getResults();
        break;
    case 'getDetails':
        echo $mobileApplication->getDetails();
        break;
    case 'logOut':
            $mobileApplication->logout();
        break;
  }
?>
