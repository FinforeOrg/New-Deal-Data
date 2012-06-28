<?php
function convert_billion_to_million_for_display($billion){
	return number_format(round($billion*1000,2),2);
}
function convert_billion_to_million_for_display_round($billion){
	return number_format(round($billion*1000));
}
/*******
sng:23/jun/2011
Some values are in million. we need a way to display those, just like billion numbers

should we use the convert_million_for_display for this?
***********/
function convert_million_for_display_round($million){
	return number_format(round($million));
}
/*******************
sng:13/july/2011
A non round version, correct to 2 decimal place

sng:14/july/2011
What if the $million is 0.001? The round change it to 0.
What we need is a check whether the val is less than 0.01
***********/
function convert_million_for_display($million){
	if($million < 0.01){
		return round($million,4);
	}
	return number_format(round($million,2),2);
}
/***
sng:23/july/2010
when downloading to excel, if we use thousandth separator, the numbers
become text, so we need another function here
*******/
function convert_billion_to_million_for_display_round_as_num($billion){
	return round($billion*1000);
}
function convert_deal_value_for_display_round($value_in_billion,$value_range_id,$fuzzy_value){
	if(($value_in_billion==0.0)&&($value_range_id==0)){
		return "Not disclosed";
	}
	if($value_in_billion > 0.0){
		//we have exact value
		//convert billion to million display round
		//in USD since value in billion is in USD
		return "$".convert_billion_to_million_for_display_round($value_in_billion)."m";
	}
	//we do not have exact value but have range id so show fuzzy value
	return $fuzzy_value;
}
/**************
sng:27/feb/2012
Given deal id, get the value to be displayed
***************/
function deal_value_for_display_round_for_deal_id($deal_id){
	$q = "select value_in_billion,t.value_range_id,r.display_text as fuzzy_value from ".TP."transaction as t left join ".TP."transaction_value_range_master as r on(t.value_range_id=r.value_range_id) where t.id='".$deal_id."'";
	$res = mysql_query($q);
	if(!$res){
		return "";
	}
	$count = mysql_num_rows($res);
	if(0==$count){
		return "";
	}
	$row = mysql_fetch_assoc($res);
	return convert_deal_value_for_display_round($row['value_in_billion'],$row['value_range_id'],$row['fuzzy_value']);
}
/*******************************************************************************
23/jan/2012
In deal search result listing, we show the exact value if we have one or show the fuzzy name. If both are
absent, we show Not disclosed
**********/
/*****************************************************************************/
//we are accepting reference so as not to create another array. But be
//careful and DO NOT change anything
function show_deal_type_data(&$deal_data_arr){
	
	//we show the deal type, and then subtype1 and subtype2 if they are not blank or not n/a
	echo $deal_data_arr['deal_cat_name'];
	$deal_subtype = "";
	if(($deal_data_arr['deal_subcat1_name']!="")&&($deal_data_arr['deal_subcat1_name']!="n/a")){
		$deal_subtype.=$deal_data_arr['deal_subcat1_name'];
	}
	if(($deal_data_arr['deal_subcat2_name']!="")&&($deal_data_arr['deal_subcat2_name']!="n/a")){
		if($deal_subtype!=""){
			$deal_subtype.=",";
		}
		$deal_subtype.=$deal_data_arr['deal_subcat2_name'];
	}
	echo " deal";
	if($deal_subtype!=""){
		echo " (".$deal_subtype.")";
	}
	if($deal_data_arr['deal_cat_name']=="M&A"){
		echo " acquired ";
		if($deal_data_arr['target_company_name']!=""){
			echo $deal_data_arr['target_company_name'];
		}else{
			echo "company unknown";
		}
		echo " (";
		if($deal_data_arr['target_country']!=""){
			echo $deal_data_arr['target_country'];
		}else{
			echo "country unknown";
		}
		echo ", ";
		if($deal_data_arr['target_sector']!=""){
			echo $deal_data_arr['target_sector'];
		}else{
			echo "sector unknown";
		}
		echo ")";
		/***
		sng:12/aug/2010
		for M&A deals, it may happen that the target is a part of a larger company and it is the larger company
		that is selling the target. So we need to show the seller if it is there
		**/
		if($deal_data_arr['seller_company_name']!=""){
			echo "<br />Sold by ".$deal_data_arr['seller_company_name'];
			echo "(";
			if($deal_data_arr['seller_country']!=""){
				echo $deal_data_arr['seller_country'];
			}else{
				echo "country unknown";
			}
			echo ", ";
			if($deal_data_arr['seller_sector']!=""){
				echo $deal_data_arr['seller_sector'];
			}else{
				echo "sector unknown";
			}
			echo ")";
		}
	}
}

/***
sng:22/july/2010
The coupon can be blank, n/a, or text like floating or a number. 
If it is a number, I assume it is a percentage, never mind what is the type of deal.
If the % symbol is missiong, we put %

sng:12/aug/2010
Debt deals has coupon, maturity date and rating. Rating is a text. It may be there, it may not be there
********/
function show_coupon_data(&$deal_data_arr){
	echo "Coupon: ";
	if(($deal_data_arr['coupon']!="")&&($deal_data_arr['coupon']!="n/a")){
		if(is_numeric($deal_data_arr['coupon'])){
			if(strpos($deal_data_arr['coupon'],"%")===false){
				//%not found
				echo $deal_data_arr['coupon']."%";
			}else{
				//%found
				echo $deal_data_arr['coupon'];
			}
		}else{
			//text like Floating
			echo $deal_data_arr['coupon'];
		}
		if(($deal_data_arr['maturity_date']!="")&&($deal_data_arr['maturity_date']!="n/a")){
			/**
			sng:11/nov/2010
			we are having problem with uk date
			echo " mature on ".date("jS M Y",strtotime($deal_data_arr['maturity_date']));
			***/
			echo " mature on ".date("jS M Y",date_to_timestamp($deal_data_arr['maturity_date']));
			
		}
		//rating
		echo ". Rating ";
		if($deal_data_arr['current_rating']!=""){
			echo $deal_data_arr['current_rating'].".";
		}else{
			echo "not available.";
		}
	}else{
		echo "n/a";
	}
}
function company_id_from_name($company_name,$company_type,&$company_id,&$found){
	/***
	NOTE: please magic quote the company name
	*****/
	$q = "select company_id from ".TP."company where type='".$company_type."' and name='".$company_name."'";
	$res = mysql_query($q);
	if(!$res){
		return false;
	}
	$cnt = mysql_num_rows($res);
	if(0 == $cnt){
		$found = false;
		return true;
	}
	//found
	$row = mysql_fetch_assoc($res);
	$found = true;
	$company_id = $row['company_id'];
	return true;
}
/****
sng:01/oct/2010
Now that we have get_deal_type_for_listing, use that
***/
function show_deal_type_for_listing($type,$subtype1,$subtype2){
	/*$deal_type = $type;
	if(($subtype1!="")&&($subtype1!="n/a")){
		//it must not be same as deal cat name
		if($subtype1!=$type){
			$deal_type.=" : ".$subtype1;
		}
	}
	if(($subtype2!="")&&($subtype2!="n/a")){
		$deal_type.=" : ".$subtype2;
	}
	echo $deal_type;*/
	echo get_deal_type_for_listing($type,$subtype1,$subtype2);
}

/****
sng:01/oct/2010
This is for situations when we want to get the string but nit echo it
*********/
function get_deal_type_for_listing($type,$subtype1,$subtype2){
	$deal_type = $type;
	if(($subtype1!="")&&($subtype1!="n/a")){
		//it must not be same as deal cat name
		if($subtype1!=$type){
			$deal_type.=" : ".$subtype1;
		}
	}
	if(($subtype2!="")&&($subtype2!="n/a")){
		$deal_type.=" : ".$subtype2;
	}
	return $deal_type;
}

/**
* Function for sending HTML e-mail
* 
* @param string $content
* @param string $from
* @param string $to
* @param string $subject
*/
function sendHTMLemail($content,$from,$to,$subject)
{
// First we have to build our email headers
// Set out "from" address

    $headers = "From: $from\r\n"; 

// Now we specify our MIME version

    $headers .= "MIME-Version: 1.0\r\n"; 

// Create a boundary so we know where to look for
// the start of the data

    $boundary = uniqid("HTMLEMAIL"); 
    
// First we be nice and send a non-html version of our email
    
    $headers .= "Content-Type: multipart/alternative;".
                "boundary = $boundary\r\n\r\n"; 

    $headers .= "This is a MIME encoded message.\r\n\r\n"; 

    $headers .= "--$boundary\r\n".
                "Content-Type: text/plain; charset=ISO-8859-1\r\n".
                "Content-Transfer-Encoding: base64\r\n\r\n"; 
                
    $headers .= chunk_split(base64_encode(strip_tags($content))); 

// Now we attach the HTML version

    $headers .= "--$boundary\r\n".
                "Content-Type: text/html; charset=ISO-8859-1\r\n".
                "Content-Transfer-Encoding: base64\r\n\r\n"; 
                
    $headers .= chunk_split(base64_encode($content)); 

// And then send the email ....

   return  mail($to,$subject,"",$headers);
    
}
/*****
sng:11/nov/2010
In http://php.net/manual/en/function.strtotime.php, I found
Stefan Kunstmann
30-Jul-2010 01:32
UK dates (eg. 27/05/1990) won't work with strotime, even with timezone properly set. 
However, if you just replace "/" with "-" it will work fine. 
<?php 
$timestamp = strtotime(str_replace('/', '-', '27/05/1990')); 
?>
**********/
function date_to_timestamp($dt){
	$stamp = strtotime($dt);
	if($stamp===false){
		//may be UK format
		$dt = str_replace("/","-",$dt);
		$stamp = strtotime($dt);
		if($stamp===false){
			return "";
		}else{
			return $stamp;
		}
	}else{
		return $stamp;
	}
}
/*******
util function for converting the date in db to site date format
********/
function ymd_to_dmy($date){
	return date("jS M Y",strtotime($date));
}

/*****************
sng:19/jan/2012
util funciton to convert date in db format 2010-02-25 to display format Feb-10
***********/
function ymd_to_my($date){
	return date("M-y",strtotime($date));
}
/**********
sng:20/jun/2011
util function for session based flash message. A code populate the session, another code (may be called in another web request) display it and blank it out

sng:7/sep/2011
Sometime we just need the flash message, so we add this get_flash() method. This return the message and blank it out.
***********/
function create_flash($key,$message){
	$_SESSION['flash_msg_'.$key] = $message;
}
function display_flash($key){
	echo $_SESSION['flash_msg_'.$key];
	$_SESSION['flash_msg_'.$key] = "";
}
function get_flash($key){
	$temp = $_SESSION['flash_msg_'.$key];
	$_SESSION['flash_msg_'.$key] = "";
	return $temp;
}
/***************************
16/aug/2011
clean a filename. Remove space, non alpha numeric character, but keep the dot character
****************************/
function clean_filename($name){
	$clean = preg_replace("/[^a-zA-Z0-9\.]*/","",$name);
	return $clean;
}
/*********************************
17/nov/2011
A funciton to get the file extension
***********************************/
function get_file_extension($filename){
	$ext = "";
	$pos = strrpos($filename,".");
	if($pos === false){
		return $ext;
	}
	$ext = substr($filename,$pos+1,strlen($filename));
	return $ext;
}

/*************************************
sng:28/dec/2011
Sometime we have the membership type and we need what kind of company is required for that member
***********************/
function company_type_from_membership_type($membership_type){
	$company_type = "";
	if($membership_type=="banker") $company_type = "bank";
	elseif($membership_type=="lawyer") $company_type = "law firm";
	elseif($membership_type=="company rep") $company_type = "company";
	/**************************************************
	sng:5/apr/2011
	data partner also associate with company
	*********/
	elseif($membership_type=="data partner") $company_type="company";
	return $company_type;
}

/**********************
sng:20/mar/2012
The deal source urls are stored as csv. Problem is, the url itself can contain ','.
We need a method to untangle the mess
*********************/
function deal_source_csv_to_array($sources){
	$arr = array();
	if($sources==""){
		return $arr;
	}
	
	$source_urls = explode(",",$sources);
	$cnt = count($source_urls);
	
	for($source_i=0;$source_i<$cnt;$source_i++){
		$source = trim($source_urls[$source_i]);
		//look ahead
		for($next_source_i=$source_i+1;$next_source_i<$cnt;$next_source_i++){
			$next_source = trim($source_urls[$next_source_i]);
			if(strpos($next_source,"http")===false){
				//add the token to the current one (outer loop)
				$source = $source.",".$next_source;
				//skip
				$source_i++;
			}else{
				//valid token
				break;
			}
		}
		$arr[] = $source;
	}
	return $arr;
}
?>
