<?php
/****
Mihai
****/
/** Error reporting */
error_reporting(E_ALL);
ini_set("display_errors",1);

/** Include path **/
@session_start();
include("include/global.php");
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__) . '/classes/');

error_reporting(E_ALL);
require_once("classes/class.company.php");
require_once("classes/class.transaction.php");
require_once("classes/class.savedSearches.php");
/******************************
sng:14/nov/2011
we make this open now

if (!@$_SESSION['is_member']) {
    header('Location: index.php');
}
****************************/

require 'PHPPowerPoint.php';

require 'PHPPowerPoint/IOFactory.php';

$objPHPPowerPoint = new PHPPowerPoint();

$objPHPPowerPoint->getProperties()->setCreator("Mihai Ionut Virgil");
$objPHPPowerPoint->getProperties()->setLastModifiedBy("Mihai Ionut Virgil");
$objPHPPowerPoint->getProperties()->setTitle("data-cx.com Credential Slide");
$objPHPPowerPoint->getProperties()->setSubject("data-cx.com Credential Slide");
$objPHPPowerPoint->getProperties()->setDescription("data-cx.com Credential Slide");
$objPHPPowerPoint->getProperties()->setKeywords("data-cx.com Credential Slide");
$objPHPPowerPoint->getProperties()->setCategory("Firm clients");

/*************
sng:27/jan/2012
defined('DD_PATH') or define('DD_PATH',"/var/www/");
This means we do not use /uploaded_img
*************/
define('IMAGESDIR',DD_PATH."uploaded_img/logo/thumbnails/");

$savedSearches = new SavedSearches();
//////////////////////////////////////
$g_view['firm_id'] = $_REQUEST['id'];
//get the firm data
$g_view['company_data'] = array();
$success = $g_company->get_company($g_view['firm_id'],$g_view['company_data']);
if(!$success){
	die("Cannot get company data");
}

//var_dump($_POST);die();
$g_view['data'] = array();
$g_view['data_count'] = 0;

if (isset($_REQUEST['from']) && $_REQUEST['from'] == 'savedSearch') {
     if (isset($_GET['token']) && '' != $_GET['token'] ) {
        $savedSearches->loadIntoPost($_GET['token']);
    }
    if (isset($_SESSION['tombToken']) && '' != $_SESSION['tombToken']) {
        $g_view['data'] = $savedSearches->loadTombstonesFromQuery($_SESSION['tombToken']);
    } else {
        $g_view['data'] = $g_trans->getTombstonesForFirm($g_view['firm_id']);
    }      
    $g_view['data_count'] = sizeOf($g_view['data']);
    $success = true;
} else 
$success = $g_trans->get_showcase_deal_ids_of_firm($g_view['firm_id'],24,$g_view['data'],$g_view['data_count']);
if(!$success){
	die("Cannot get tombstones");
}

for($i=0;$i<$g_view['data_count'];$i++){
    $deal_cat_name = '';
    $firm = $g_trans->get_tombstone_from_deal_id($g_view['data'][$i]['transaction_id'],true);
    $deal_type = $firm['deal_cat_name'];
    $deal_subcat2_name = $firm['deal_subcat2_name'];
    $deal_subcat1_name = $firm['deal_subcat1_name'];
    //if sub cat 2 name is there then we do not show sub cat 1 name
    if(($deal_subcat2_name!="")&&($deal_subcat2_name!="n/a")){
        $deal_type.=" : ".$deal_subcat2_name;
    }else{
        //check sub cat 1 name
        if(($deal_subcat1_name!="")&&($deal_subcat1_name!="n/a")){
            //it must not be same as deal cat name
            if($deal_subcat1_name!=$deal_cat_name){
                $deal_type.=" : ".$deal_subcat1_name;
            }
        }
    }
    $date = date("F Y",strtotime($firm['date_of_deal']));
    $deal_value = round($firm['value_in_billion']*1000,2);
	/****
	sng:10/jul/2010
	If the deal value is 0, it means that the value is not disclosed and the stored
	value is 0. Instead of showing US $ 0 million, we show Not disclosed
	**********/
	/*if($firm['value_in_billion'] == 0){
		$images[$i+1] = array('logo'=>$logo,'text'=>$deal_type ."\n". "Not disclosed" ."\n" . $date, "name"=>$firm['company_name']) ;
	}else{
		$images[$i+1] = array('logo'=>$logo,'text'=>$deal_type ."\n". 'US $ '.$deal_value . " million" ."\n" . $date, "name"=>$firm['company_name']) ;
	}*/
	/******************
	sng:25/jan/2012
	The only difference is the deal value. Why not calculate it first.
	Also, now we might have deal range instead of exact value. If that is the case, show value range
	**********************/
	if(($firm['value_in_billion'] == 0)&&($firm['value_range_id']==0)){
		$show_deal_value = "Not disclosed";
	}elseif($firm['value_in_billion'] > 0){
		$show_deal_value = "US $ ".$deal_value." million";
	}else{
		$show_deal_value = $firm['fuzzy_value'];
	}
	
	/**********************************************
	sng:24/feb/2012
	Now we have multiple participants and each company has its own logo.
	Admin can also load logos for a deal via edit deal.  In the tombstone
	listing page, the user can switch to a different logo.
	What we do there is send the deal ids with the logo filename.
	see showcase_firm_view_savedSearches javascript codes
	**************************************************/
	$this_deal_id = $firm['deal_id'];
	if(isset($_POST[$this_deal_id])){
		$logo = $_POST[$this_deal_id];
	}else{
		$logo = "";
	}
	
    $images[$i+1] = array('logo'=>$logo,'text'=>$deal_type ."\n". $show_deal_value ."\n" . $date, "name"=>$firm['company_name']) ;

}  
require_once("PHPPowerPoint/Writer/PowerPoint2007.php");
/**
 * here is how it should en up in order to work
 *
$images = array(
    1=>array('logo'=>"1276624150_areva-logo.jpg",'text'=>"M&A \n US $ 540 milion \n May 2010"),
    2=>array('logo'=>"1276624150_areva-logo.jpg",'text'=>"M&A \n US $ 540 milion \n May 2011"),
    3=>array('logo'=>"1276624150_areva-logo.jpg",'text'=>"M&A \n US $ 540 milion \n May 2011"),
    4=>array('logo'=>"1276624150_areva-logo.jpg",'text'=>"M&A \n US $ 540 milion \n May 2011"),
    5=>array('logo'=>"1276624150_areva-logo.jpg",'text'=>"M&A \n US $ 540 milion \n May 2011"),
    6=>array('logo'=>"1276624150_areva-logo.jpg",'text'=>"M&A \n US $ 540 milion \n May 2011"),
    7=>array('logo'=>"1276624150_areva-logo.jpg",'text'=>"M&A \n US $ 540 milion \n May 2011"),
    8=>array('logo'=>"1276624150_areva-logo.jpg",'text'=>"M&A \n US $ 540 milion \n May 2011"),
    9=>array('logo'=>"1276624150_areva-logo.jpg",'text'=>"M&A \n US $ 540 milion \n May 2011"),
    10=>array('logo'=>"1276624150_areva-logo.jpg",'text'=>"M&A \n US $ 540 milion \n May 2011"),
    11=>array('logo'=>"1276624150_areva-logo.jpg",'text'=>"M&A \n US $ 540 milion \n May 2011"),
    12=>array('logo'=>"1276624150_areva-logo.jpg",'text'=>"M&A \n US $ 540 milion \n May 2011"),
    13=>array('logo'=>"1276624150_areva-logo.jpg",'text'=>"M&A \n US $ 540 milion \n May 2011"),
    14=>array('logo'=>"1276624150_areva-logo.jpg",'text'=>"M&A \n US $ 540 milion \n May 2011"),
    15=>array('logo'=>"1276624150_areva-logo.jpg",'text'=>"M&A \n US $ 540 milion \n May 2011"),
    16=>array('logo'=>"1276624150_areva-logo.jpg",'text'=>"M&A \n US $ 540 milion \n May 2011"),
    17=>array('logo'=>"1276624150_areva-logo.jpg",'text'=>"M&A \n US $ 540 milion \n May 2011"),
    18=>array('logo'=>"1276624150_areva-logo.jpg",'text'=>"M&A \n US $ 540 milion \n May 2011"),
    19=>array('logo'=>"1276624150_areva-logo.jpg",'text'=>"M&A \n US $ 540 milion \n May 2011"),
    20=>array('logo'=>"1276624150_areva-logo.jpg",'text'=>"M&A \n US $ 540 milion \n May 2011"),
);
 */


 /**
 *  adding elements to the begining of the array ?
 * 
 * @param array $arr
 * @param mixed $item
 */
 function array_rpush(&$arr, $item)
{
  $arr = array_pad($arr, -(count($arr) + 1), $item);
}

    if (isset($_REQUEST['extra']) && $_REQUEST['extra']) {
        //array_rpush($images, array('logo'=>"","text"=>''));
        for ($i = 0; $i<=$_REQUEST['extra']; $i++) {
            $temp = array('logo'=>"",'text'=>"Your text here" );
            array_rpush($images, $temp);
        }
        unset($images[0]);
    }

/*
 * do we need more than one slide?
 */    
    $extra = sizeOf($images) % 18 != 0 ? 1 : 0;
    $nrSlides = floor(sizeOf($images)/18)+$extra;
        $start = 0;
        $length = 18;
    for ($i=1;$i<=$nrSlides;$i++) {
        if ($i == 1)
            $currentSlide = $objPHPPowerPoint->getActiveSlide();
        else {
            $currentSlide = $objPHPPowerPoint->createSlide();
        }
        
        $currImagesArray = array_slice($images, $start, $length, true);
        //echo "<pre>".$nrSlides ." ".$i." ". $start." ".$length." ". sizeOf($currImagesArray). "\n";
        $start += $length;
        $offsetX = 40;
        $offsetY = 150;
        foreach ($currImagesArray as $key=>$image) {
            if ($key == 0) {
                continue;
            }
            $shape = $currentSlide->createDrawingShape();
            //$shape->setName('PHPPowerPoint logo');
            //$shape->setDescription('PHPPowerPoint logo');
            $shape->setPath(IMAGESDIR . "itemBk2.png");
            $shape->setWidth(145);
            $shape->setOffsetX($offsetX);
            $shape->setOffsetY($offsetY);
			//files with space in the filename is causing problem in powerpoint
            if (preg_match("/\s+/",$image['logo']))  {
                $newName =  preg_replace("/\s+/","","$image[logo]");
				/**********************
				sng:27/jan/2012
				Each time the code is called, we are making a copy, without checking
				whether a copy is already there or not.
				We need to check for the file (with space removed) first.
				**********************/
				if(file_exists(IMAGESDIR.$newName)){
					$image['logo'] =  $newName;
				}else{
					//does not exists, try to create it
					if (!@copy(IMAGESDIR . "$image[logo]", IMAGESDIR.$newName )){
						//copy failed, so we show a placeholder (the new one)
						//$image['logo'] = "Placeholder2.jpg";
						$image['logo'] = "no_logo_warning_logo.png";
						
					} else {
					  $image['logo'] =  $newName;  
					}
					//
				}  
            }
            
            if ($image['logo'] == "" || !file_exists(IMAGESDIR . "$image[logo]")) {
               //$image['logo'] = "Placeholder2.jpg"; 
			   $image['logo'] = "no_logo_warning_logo.png";
            }
            if (file_exists(IMAGESDIR . "$image[logo]" )) {
                $shape = $currentSlide->createDrawingShape();
                $shape->setPath(IMAGESDIR . "$image[logo]" );
                $shape->setHeight(95);
                $shape->setWidth(70);
                $shape->setOffsetX($offsetX+35);
                $shape->setOffsetY($offsetY+25); 
                $offsetX += 145;               
            } 
             
            if ($key % 6 == 0 and $key != 0) {
               $offsetY += 170;
               $offsetX = 40;
            }
        }

        $offsetX = 50;
        $offsetY = 240;

        foreach ($currImagesArray as $key=>$image) {
            $shape = $currentSlide->createRichTextShape();
            $shape->setWidth(125);
            $shape->setHeight(50);
            $shape->setOffsetX($offsetX);
            $shape->setOffsetY($offsetY);
            $shape->getActiveParagraph()->getAlignment()->setHorizontal( PHPPowerPoint_Style_Alignment::HORIZONTAL_CENTER );
            $textRun = $shape->createTextRun($image['text']);

            $textRun->getFont()->setSize(8);
            $textRun->getFont()->setName('Calibri');
            $textRun->getFont()->setColor( new PHPPowerPoint_Style_Color( '00000000' ) );
               $offsetX += 145;
            if ($key % 6 == 0 and $key != 0) {
               $offsetY += 170;
               $offsetX = 50;
            }
        }

        $offsetX = 40;
        $offsetY = 40;
        $shape = $currentSlide->createRichTextShape();
        $shape->setWidth(500);
        $shape->setHeight(60);
        $shape->setOffsetX($offsetX);
        $shape->setOffsetY($offsetY);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal( PHPPowerPoint_Style_Alignment::HORIZONTAL_LEFT );
        $title = (isset($_REQUEST['title']) && strlen($_REQUEST['title'])) ? urldecode($_REQUEST['title']) : "[Insert Title]";
        $textRun = $shape->createTextRun($title);
        $textRun->getFont()->setSize(24);
        $textRun->getFont()->setName('Calibri');
        $textRun->getFont()->setColor( new PHPPowerPoint_Style_Color( '00000000' ) );

    }



// Save PowerPoint 2007 file
//echo date('H:i:s') . " Write to PowerPoint2007 format\n";
$objWriter = PHPPowerPoint_IOFactory::createWriter($objPHPPowerPoint, 'PowerPoint2007');

$filename = dirname(__FILE__)."/generatedPresentations/showcase_".date("His").rand(0,99999).".pptx";
$objWriter->save($filename);

header("Pragma: public"); // required
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private",false); // required for certain browsers
header("Content-Type: application/vnd.openxmlformats-officedocument.presentationml.presentation");
// change, added quotes to allow spaces in filenames, by Rajkumar Singh
header("Content-Disposition: attachment; filename=\"".basename($filename)."\";" );
header("Content-Transfer-Encoding: binary");
header("Content-Length: ".filesize($filename));
readfile("$filename");
@unlink($filename);
exit();
?>