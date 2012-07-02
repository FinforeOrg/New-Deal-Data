<?php
@session_start();
$logoLocation = dirname(dirname(dirname(__FILE__)))."/uploaded_img/logo/";
/*$sng_root = "http://www.deal-data.com";*/
$sng_root = "";
require_once("../../classes/class.image_util.php");
require_once("../../nifty_functions.php");
if (isset($_REQUEST['action'])) {
    switch ($_REQUEST['action']) {
        case "setDefaultLogo" : 
        foreach ($_SESSION['logos'] as $key=>$logo) {
            $_SESSION['logos'][$key]['default'] = 0;
        }
            $_SESSION['logos'][$_REQUEST['id']]['default'] = 1;
            echo json_encode(array("status"=>"success"));
        break;
        case "deleteLogo" : 
            unset($_SESSION['logos'][$_REQUEST['id']]);
            unlink($logoLocation.$_SESSION['logos'][$_REQUEST['id']]['fileName']);
            echo json_encode(array("status"=>"success"));
        break;
    }
}
if (is_array($_FILES['userfile'])) {
    if (is_uploaded_file($_FILES['userfile']['tmp_name'])) {
        $newFileName = substr(md5(time()),0,16)."_".strtolower(preg_replace("#[^\w]+#",'',$_POST['company'])).substr($_FILES['userfile']['name'],-4);
		/***
		sng:23/sep/2010
		cannot have space in logo file name else problem when downloading to powerpoint
		
		sng:16/aug/2011
		we remove anything that is not alpha numeric or dot
		***/
		$newFileName = clean_filename($newFileName);
        //echo $newFileName .PHP_EOL;
        if (move_uploaded_file($_FILES['userfile']['tmp_name'], $logoLocation.$newFileName)) {
            $img_obj = new image_util();
            $success = $img_obj->create_thumbnail($logoLocation,$newFileName,200,200,$logoLocation."/thumbnails/",false);     
            $_SESSION['logos'][$_SESSION['logosCurrentIndex']] = array('fileName'=>$newFileName,'default'=>0); 
            
            ?>
            <div style="float:left;" id="logo-<?php echo $_SESSION['logosCurrentIndex'] ?>">
                <div style="width:200px;  height:200px; text-align:center;"> 
                    <img src="<?php echo $sng_root;?>/uploaded_img/logo/thumbnails/<?php echo $newFileName ?>" style="width:150;" />
                </div>
                <div style="width:100%; height:40px; text-align:center; clear:both" > 
                     <img src="<?php echo $sng_root;?>/images/delete.png" onclick="return deleteLogo(<?php echo $_SESSION['logosCurrentIndex']?>)" style="cursor:pointer" title="Delete this Logo">
                     <img src="<?php echo $sng_root;?>/images/default.png" onclick="return setDefaultLogo(<?php echo (int) $_SESSION['logosCurrentIndex']?>)" style="cursor:pointer" title="Set this logo as default" class="setDefault">
                </div> 
           </div>
       <?php 
       $_SESSION['logosCurrentIndex'] = $_SESSION['logosCurrentIndex']+1;
        } 
    }
    
}
?>
