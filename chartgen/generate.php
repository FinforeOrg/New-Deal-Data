<?php

//echo "<pre>";
error_reporting(E_ALL);
ini_set('display_errors',0);
@session_start();


$colors = array('cccccc' /* light gray */
              , '52a3dc' /* light blue */
              , '000000' /* black */ 
              , '928d8d' /* dark gray */
              , 'FFFFFF' /* white with border */);
 
$color = $_POST['color'];
if (!in_array($color,$colors)) 
    die('Invalid request!. Please click the back button and try again.');

if (isset($_REQUEST['type']) && $_REQUEST['type'] == 'leagueTables') {
    $data = $_SESSION['lastGeneratedRankings'];
    $folder = '5';
} else {
   $data = $_SESSION['lastGeneratedGraphData']; 
   $folder = '10';
}

if (!is_array($data)) {
    die("Cannot get data. Did you press generate before downloading ?");
}
$idx = 1;
foreach ($data as $kk=>$val) {
    
    if(!isset($val['short_name'])) {
       continue;  
    }
    if ($_REQUEST['type'] == 'leagueTables') {
        if ($val['short_name'] == "") {
           $val['short_name'] = $val['name']; 
        }
    }
    $newData[$idx] = $val;
    $idx++;

}
$data = $newData;



/*
$color = '52a3dc';  
$data = array(
    1=>array('short_name' => '2010 Q' .rand(1,9), 'value' => rand(1200,3265)),
    2=>array('short_name' => '2010 Q'. rand(1,9), 'value' => rand(1200,3265)),
    3=>array('short_name' => '2010 Q'. rand(1,9), 'value' => rand(1200,3265)),
    4=>array('short_name' => '2010 Q'. rand(1,9), 'value' => rand(1200,3265)),
    5=>array('short_name' => '2010 Q'. rand(1,9), 'value' => rand(1200,3265)),
    6=>array('short_name' => '2010 Q'. rand(1,9), 'value' => rand(1200,3265)),
    7=>array('short_name' => '2010 Q'. rand(1,9), 'value' => rand(1200,3265)),
    8=>array('short_name' => '2010 Q'. rand(1,9), 'value' => rand(1200,3265)),
    9=>array('short_name' => '2010 Q'. rand(1,9), 'value' => rand(1200,3265)),
    10=>array('short_name' => '2010 Q'. rand(1,9), 'value' => rand(1200,3265)),
    11=>array('short_name' => '2010 Q'. rand(1,9), 'value' => rand(1200,3265)),
    12=>array('short_name' => '2010 Q'. rand(1,9), 'value' => rand(1200,3265)),
    13=>array('short_name' => '2010 Q'. rand(1,9), 'value' => rand(1200,3265)),
    6=>array('short_name' => '2010 Q7', 'value' => rand(1200,3265)),
    7=>array('short_name' => '2010 Q8', 'value' => rand(1200,3265)),
    8=>array('short_name' => '2010 Q9', 'value' => rand(1200,3265)),
    9=>array('short_name' => '2010 Q10', 'value' => rand(1200,3265)),

);
                                                                   */

/** When we have to display more than 20 data points we just display the last 20**/ 
if (count($data) > 20) {
    $data = array_slice($data, (count($data) - 20), 21, false);
    unset($data[0]);
}

$dataSize = sizeOf($data);
$title = htmlentities(urldecode($_POST['pptTitle']));
$powerPointTemplateSize = sizeOf($data);
if ($dataSize < 5) {
    $powerPointTemplateSize = 5;
    $remainingItems = 5 - sizeOf($data);
    for($i=$dataSize+1; $i<=5; $i++) {
        $data[$i] = array('short_name' => '', 'value' => 0);   
    }
} 

$presentationName = 'chart' . time() . rand(1, 999) . ".pptx";
$powePointTemplateName = "p$powerPointTemplateSize";

$presentationFolderName = dirname(__FILE__) . "|templates|presentations|$powePointTemplateName|";
$excelInPresentationFolder = "$presentationFolderName|ppt|embeddings|Microsoft_Excel_Worksheet1.xlsx";
$excelInPresentationFolder = str_replace("|", DIRECTORY_SEPARATOR, $excelInPresentationFolder); 
$presentationFolderName = str_replace("|", DIRECTORY_SEPARATOR, $presentationFolderName);

$chartXmlFileName ="$presentationFolderName|ppt|charts|chart1.xml";
$chartXmlFileName = str_replace("|", DIRECTORY_SEPARATOR, $chartXmlFileName);

$oldChartXmlContents = file_get_contents($chartXmlFileName);
$chartXmlContents = $oldChartXmlContents;

 
$excelFolderName =  dirname(__FILE__) .   "|templates|excels|$powePointTemplateName|Microsoft_Excel_Worksheet1";
$excelFolderName = str_replace("|", DIRECTORY_SEPARATOR, $excelFolderName);
$sheetFile =  "$excelFolderName|xl|worksheets|sheet1.xml";
$sheetFile =   str_replace("|", DIRECTORY_SEPARATOR, $sheetFile);

$tableFile = "$excelFolderName|xl|tables|table1.xml";
$tableFile =   str_replace("|", DIRECTORY_SEPARATOR, $tableFile); 

$oldTableFileContents =   file_get_contents($tableFile); 
$tableFileContents =   $oldTableFileContents; 
//echo $oldChartXmlContents;
 
/**
* After editing we need the old contents back
*/
$oldSheetContents = file_get_contents($sheetFile);
$sheetContents =  $oldSheetContents;

$sharedStringsFile =  "$excelFolderName|xl|sharedStrings.xml";
$sharedStringsFile =  str_replace("|", DIRECTORY_SEPARATOR, $sharedStringsFile); 
/**
* After editing we need the old contents back  
*/
$oldShareFileContents =  file_get_contents($sharedStringsFile);
$sharedStringsContents = $oldShareFileContents;


foreach ($data as $key=>$val) {
    //$sharedStringsContents = str_replace("Category$key", $val['short_name'], $sharedStringsContents);
    $catReplacementPattern = '/Category' . $key . '</'; 
    $replacement = $val['short_name'] . "<";
    $sharedStringsContents = preg_replace($catReplacementPattern, $replacement, $sharedStringsContents);
    $chartXmlContents = str_replace("90000$key<", $val['value'] . '<', $chartXmlContents);
    //$chartXmlContents = str_replace("Category$key", $val['short_name'], $chartXmlContents);
    $chartXmlContents = preg_replace($catReplacementPattern, $replacement, $chartXmlContents);
    $sheetContents = str_replace("90000$key<", $val['value'] . '<', $sheetContents);
    //echo "Now replacing Category$key with {$val['short_name']}\n";
    //echo "Now replacing 90000$key with {$val['value']}\n";
}
$sharedStringsContents = str_replace("Chart", $title, $sharedStringsContents);
$tableFileContents = str_replace("Chart", $title, $tableFileContents);
$chartXmlContents = str_replace("<c:v>Chart</c:v>", "<c:v>$title</c:v>", $chartXmlContents);
$chartXmlContents = str_replace('FF0000', $color , $chartXmlContents);
 
//echo $chartXmlContents; 
//echo $sharedStringsContents;
//echo $sheetContents;
file_put_contents($sharedStringsFile, $sharedStringsContents);
file_put_contents($chartXmlFileName, $chartXmlContents);
file_put_contents($sheetFile, $sheetContents);
file_put_contents($tableFile, $tableFileContents);
/**
* Start zipping things to create the presentation. 
*/
Zip($excelFolderName, $destination = dirname(__FILE__) . '/Microsoft_Excel_Worksheet1.xlsx');   
copy($destination, $excelInPresentationFolder); 
unlink($destination);
Zip($presentationFolderName, $presentationName);
unlink($excelInPresentationFolder);
/**
* End zipping, the data should now be ready for the user 
*/

/**
* Clean data up, restore default template
*/
file_put_contents($sharedStringsFile, $oldShareFileContents);
file_put_contents($chartXmlFileName, $oldChartXmlContents);
file_put_contents($sheetFile, $oldSheetContents);
file_put_contents($tableFile, $oldTableFileContents);

header("Pragma: public"); // required
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private",false); // required for certain browsers
header("Content-Type: application/vnd.openxmlformats-officedocument.presentationml.presentation");
// change, added quotes to allow spaces in filenames, by Rajkumar Singh
header("Content-Disposition: attachment; filename=\"".basename($presentationName)."\";" );
header("Content-Transfer-Encoding: binary");
header("Content-Length: ".filesize($presentationName));
readfile("$presentationName");
@unlink($presentationName); 
/**
* Zip folder contents (recursive)
* 
* @param string $source
* @param string $destination
* @return bool
*/
function Zip($source, $destination)
{
    if (extension_loaded('zip') === true)
    {
        if (file_exists($source) === true)
        {
                $zip = new ZipArchive();

                if ($zip->open($destination, ZIPARCHIVE::CREATE) === true)
                {
                        //$source = realpath($source);

                        if (is_dir($source) === true)
                        {

                                $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::LEAVES_ONLY);
                                foreach ($files as $fullFileName => $file)
                                {                               
                                        $file = $fullFileName;
                                        if (is_dir($file) === true)
                                        {
                                                $zip->addEmptyDir($emptyName =  str_replace($source , '', $file . '/'));
                                        }

                                        else if (is_file($file) === true)
                                        {

                                            $file2 = str_replace($source , '', $file);
                                            if (substr($file2,0,1) == "/" OR substr($file2,0,1) == '\\')
                                                $file2 = substr($file2,1,strlen($file2));
                                            //echo $file2 . PHP_EOL;
                                                $zip->addFromString($file2, file_get_contents($file));
                                        }
                                }
                        }

                        else if (is_file($source) === true)
                        {
                                $zip->addFromString(basename($source), file_get_contents($source));
                        }
                } else {
                    echo "Cannot zip the $destination";
                }

                return $zip->close();
        } else {
            echo "Source ($source) Not found";
        }
    } else {
        echo 'Zip extension not loaded';
    }

    return false;
}