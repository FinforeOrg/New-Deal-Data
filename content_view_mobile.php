<?php
@session_start();
$scriptFile = 'index_unlogged.js';
if ($_SESSION['is_member']) {
    switch ($_SESSION['page']) {
        case '1':
            $scriptFile = 'logged_page1.php';    
        break;
        default:
            $scriptFile = 'logged_page1.php';
        break;
    }    
} 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $g_view['meta_title'];?></title>
<meta name="keywords" content="<?php echo $g_view['meta_keywords'];?>" />
<meta name="description" content="<?php echo $g_view['meta_description'];?>" />
<link href="css/style.css" rel="stylesheet" type="text/css" />
<link rel="Shortcut Icon" href="favicon.ico">             
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js" type="text/javascript"></script>
<script src="js/sencha-touch-1.1.0/sencha-touch-debug.js" type="text/javascript"></script>
<script src="js/sencha-touch-1.1.0/scripts/<?php echo $scriptFile; ?>" type="text/javascript"></script>
<link href="js/sencha-touch-1.1.0/resources/css/sencha-touch.css" rel="stylesheet" type="text/css" /> 
<link href="css/style.css" rel="stylesheet" type="text/css" />
</head>
<body data-role="page">
<?php include $g_view['content_view'];?> 
</body>
