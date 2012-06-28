<?php
include("include/global.php");
/**************
sng:7/apr/2011
this requires login
**********/
$_SESSION['after_login'] = "awards.php";
require_once("check_mem_login.php");

require_once("classes/class.company.php");
require_once("classes/class.transaction.php");
require_once("classes/class.account.php");
require_once("classes/class.country.php");
require_once("classes/class.account.php");
require_once("classes/class.savedSearches.php");
require_once("classes/class.oneStop.php"); 
require_once("classes/class.Awards.php");

$savedSearches = new SavedSearches();
//////////////////////////////////////

$g_view['content_view'] = "awards_view.php"; 

$categories = $g_trans->getCategoryTree();

    //fetch sector types
$awards = new Awards();

$company = $awards->getCompanyById($_SESSION['company_id']);
$sectors = $awards->getDistinctSectors();
$regions = $awards->getDistinctRegions();
$categories = $awards->getDistinctCategories();
$awards = $awards->getAwards();


if (isset($_GET['download'])) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    $images = array();
    if (!sizeOf($awards)) {
        header('Location: awards.php'); exit(0);
    }
    
    foreach ($awards as $award) {
        $images[] = array('logo' => $award['pic'], 'text' => $award['winner'] . "\n" . $award['year']);
    }

    set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__) . '/classes/');
    require_once 'PHPPowerPoint.php';

    require_once 'PHPPowerpoint/IOFactory.php';
    require_once 'PHPPowerpoint/Writer/PowerPoint2007.php';

    $objPHPPowerPoint = new PHPPowerPoint();

    $objPHPPowerPoint->getProperties()->setCreator("Mihai Ionut Virgil");
    $objPHPPowerPoint->getProperties()->setLastModifiedBy("Mihai Ionut Virgil");
    $objPHPPowerPoint->getProperties()->setTitle("deal-data.com Credential Slide");
    $objPHPPowerPoint->getProperties()->setSubject("deal-data.com Credential Slide");
    $objPHPPowerPoint->getProperties()->setDescription("deal-data.com Credential Slide");
    $objPHPPowerPoint->getProperties()->setKeywords("deal-data.com Credential Slide");
    $objPHPPowerPoint->getProperties()->setCategory("Firm clients");
    
    /*
     * do we need more than one slide?
     */    
    $extra = sizeOf($images) % 18 != 0 ? 1 : 0;
    $nrSlides = floor(sizeOf($images)/18)+$extra;
    $start = 0;
    $length = 18;
    define('IMAGESDIR',"uploaded_img/logo/thumbnails/");
    for ($i=1;$i<=$nrSlides;$i++) {
        if ($i == 1)
            $currentSlide = $objPHPPowerPoint->getActiveSlide();
        else {
            $currentSlide = $objPHPPowerPoint->createSlide();
        }
        
        $currImagesArray = array_slice($images, $start, $length, true);

        $start += $length;
        $offsetX = 40;
        $offsetY = 150;
        foreach ($currImagesArray as $key=>$image) {
            $shape = $currentSlide->createDrawingShape();
            $shape->setPath(IMAGESDIR . "itemBk2.png");
            $shape->setWidth(145);
            $shape->setOffsetX($offsetX);
            $shape->setOffsetY($offsetY);
            
            if (file_exists("$image[logo]" )) {
                $shape = $currentSlide->createDrawingShape();
                $shape->setPath("$image[logo]" );
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
            $shape->getAlignment()->setHorizontal( PHPPowerPoint_Style_Alignment::HORIZONTAL_CENTER );
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
        $shape->getAlignment()->setHorizontal( PHPPowerPoint_Style_Alignment::HORIZONTAL_LEFT );
        $title = (isset($_REQUEST['title']) && strlen($_REQUEST['title'])) ? urldecode($_REQUEST['title']) : "[XXXXXXXXXXXXXX]";
        $textRun = $shape->createTextRun($title);
        $textRun->getFont()->setSize(24);
        $textRun->getFont()->setName('Calibri');
        $textRun->getFont()->setColor( new PHPPowerPoint_Style_Color( '00000000' ) );

    }
    $objWriter = PHPPowerPoint_IOFactory::createWriter($objPHPPowerPoint, 'PowerPoint2007');

    $filename = dirname(__FILE__)."/generatedPresentations/awards_".date("His").rand(0,99999).".pptx";
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
}
$g_view['page_heading'] = "Awards";
if (isset($company['name'])) {
    $g_view['page_heading'] .= ' / ' . $company['name'];
}

require("content_view.php");
?>