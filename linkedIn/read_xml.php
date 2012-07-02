<?php
//Initialize the XML parser
global $currentTag;
global $profileArray;
$parser=xml_parser_create();

//Function to use at the start of an element
function start($parser,$element_name,$element_attrs) {
	$element_name	=	strtolower($element_name);
	global $currentTag;
	$currentTag	= $element_name;
	/*switch($element_name) {
		case "person":
		$currentTag	= $element_name;
		break;
		case "headline":
		echo "headline: ";
		break;
		case "school-name":
		echo "school-name: ";
		break;
		case "degree":
		echo "degree: ";
		break;
		case "field-of-study":
		echo "field-of-study: ";
	}*/
}

//Function to use at the end of an element
function stop($parser,$element_name) {}

//Function to use when finding character data
function char($parser,$data){
	
	//echo $data;
	global $currentTag;
	global $profileArray;
	switch($currentTag) {
		case "industry":
		if(!isset($profileArray['industry'])) {
			$profileArray['industry']	=	$data;//echo $profileArray['industry'];
		}
		break;
		case "headline":
		if(!isset($profileArray['headline'])) {
			$profileArray['headline']	=	$data;
		}	
		break;
		case "school-name":
		if(!isset($profileArray['school-name'])) {
			$profileArray['school-name']	=	$data;
		}
		break;
		case "degree":
		if(!isset($profileArray['degree'])) {
			$profileArray['degree']	=	$data;
		}
		break;
		case "field-of-study":
		if(!isset($profileArray['field-of-study'])) {
			$profileArray['field-of-study']	=	$data;
		}
		break;
	}
}

//Specify element handler
xml_set_element_handler($parser,"start","stop");

//Specify data handler
xml_set_character_data_handler($parser,"char");

//Open XML file
$fp=fopen("linkedin.xml","r");

//Read data
while ($data=fread($fp,4096)) {
	xml_parse($parser,$data,feof($fp)) or
	die (sprintf("XML Error: %s at line %d",
	xml_error_string(xml_get_error_code($parser)),
	xml_get_current_line_number($parser)));
}

//Free the XML parser
xml_parser_free($parser);
print_r($profileArray);

?>