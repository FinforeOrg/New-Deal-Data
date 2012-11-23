<?php

/**
 * Util class
 *
 * $Id:$
 *
 * $Rev:  $
 *
 * $LastChangedBy:  $
 *
 * $LastChangedDate: $
 *
 * @author Ionut MIHAI <ionut_mihai25@yahoo.com>
 * @copyright 2011 Ionut MIHAI
 */
class Util {
    
    public static function cleanAndTranslate($data)
    {
        $label = array();
        
        if ( self::idxExists($data, 'region')) {
            $labels[] = $data['region'];
        }

        if (self::idxExists($data, 'country')) {
            $labels[] = $data['country'];
        }

        if (self::idxExists($data, 'partner_type')) {
            if  ('bank' == $data['partner_type']) $labels[] = 'Banks';
            if  ('law firm' == $data['partner_type']) $labels[] = 'Law Firms';
        }
        
        if (self::idxExists($data, 'sector')) {
            $labels[] = $data['sector'];
        }

        if (self::idxExists($data, 'industry')) {
            $labels[] = $data['industry'];
        }
        
        if (self::idxExists($data, 'deal_cat_name' ) && !self::idxExists($data, 'deal_subcat2_name') && !self::idxExists($data, 'deal_subcat1_name')) {
            $labels[] = 'All ' . $data['deal_cat_name'];
        }

        if (self::idxExists($data, 'deal_cat_name') && !self::idxExists($data, 'deal_subcat2_name') && self::idxExists($data, 'deal_subcat1_name')) {
            $labels[] = $data['deal_cat_name'] . ' > All ' . $data['deal_subcat1_name'];
        }

        if (self::idxExists($data, 'deal_cat_name') && self::idxExists($data, 'deal_subcat2_name') && self::idxExists($data, 'deal_subcat1_name')) {
            $labels[] = $data['deal_cat_name'] . ' > ' . $data['deal_subcat1_name'] . ' > ' . $data['deal_subcat2_name'];
        }
        
        /*
         * This should never happen, but we put it here just in case
         */
        if (!self::idxExists($data, 'deal_cat_name') && !self::idxExists($data, 'deal_subcat2_name') && self::idxExists($data, 'deal_subcat1_name')) {
            $labels[] = $data['deal_subcat1_name'];
        }

        if (!self::idxExists($data, 'deal_cat_name') && !self::idxExists($data, 'deal_subcat2_name') && self::idxExists($data, 'deal_subcat1_name')) {
            $labels[] = 'All ' . $data['deal_subcat1_name'];
        }        
  
        if (!self::idxExists($data, 'deal_cat_name') && self::idxExists($data, 'deal_subcat2_name') && !self::idxExists($data, 'deal_subcat1_name')) {
            $labels[] = $data['deal_subcat2_name'];
        }
        
        if (self::idxExists($data, 'ranking_criteria')) {
            switch($data['ranking_criteria']) :
                case 'num_deals':
                    $labels[] = 'Order by Number of Deals';
                    break;
                case 'total_deal_value':
                    $labels[] = 'Order by Total Deal Value';
                    break;
               case 'total_adjusted_deal_value':
                    $labels[] = 'Order by Total Adjusted Deal Value';
                    break;                
            endswitch;
        }
        
        if (self::idxExists($data, 'deal_size')) {
            $tmp = 'Show only deals %s than %s %s';
            if (preg_match('/([><]{0,}[=])([\d\.]+)/', $data['deal_size'], $matches)) {
                $val = ' billions ';
                $multiplier = 1;
                if ((double) $matches[1] < 1) {
                    $val = 'millions';
                    $multiplier = 1000;
                }
                
                switch ($matches[1]) {
                    case '<=':
                            $labels[] = sprintf($tmp, 'smaller ', $matches[2] * $multiplier, $val);
                        break;
                    case '>=':
                            $labels[] = sprintf($tmp, 'bigger ', $matches[2] * $multiplier, $val);
                        break;
                    case '=':
                            $labels[] = sprintf($tmp, 'equal ', $matches[2] * $multiplier, $val);
                        break;
                    default:
                        break;
                }
            }else{
				/***********************
				sng:27/oct/2011
				It may happen that the data is base64 encoded to slip past the sanitizer
				*************************/
				
				self::clean_deal_filter_size(base64_decode($data['deal_size']),$labels);
			}
        }
		/***************************
		sng:27/01/2012
		Now we send deal value range id and we need to get the label
		*************************/
		if (self::idxExists($data, 'value_range_id')) {
			$labels[] = self::get_label_for_deal_value_range_id($data['value_range_id']);
		}
        
        if (isset($data['minimumTransactionValue']) && isset($data['maximumTransactionValue']) && (0 != $data['minimumTransactionValue'] || 0 != $data['maximumTransactionValue'])) {
            $tmp = 'Deals between %s and %s';
            $min = ( (double) $data['minimumTransactionValue'] > 1000) ? number_format(($data['minimumTransactionValue'] / 1000),2) . ' billions' : number_format($data['minimumTransactionValue'], 2) . ' millions';
            $max = ( (double) $data['maximumTransactionValue'] > 1000) ? number_format(($data['maximumTransactionValue'] / 1000),2) . ' billions' : number_format($data['maximumTransactionValue'], 2) . ' millions';
            
            $labels[] = sprintf($tmp, $min, $max);
        }
        
        if (self::idxExists($data, 'year')) {
            /**
             * Let`s put this here just in case
             */
            if (preg_match('/(\d{4,})-(\d{4,})/', $data['year'], $matches)) {
                if ($matches[2] == date('Y')){
                    $data['year'] = $matches[1] . '-' . $matches[2] . 'YTD';
                }
            };
            $labels[] = $data['year'];
        }
        
        if (self::idxExists($data, 'month_division') && self::idxExists($data, 'month_division_list')) {
            $divisions = array('q' => 'Quarterly', 'h' => 'Semi-Annual', 'y' => 'Annual');
            
            if ('q' == $data['month_division'] || 'h' == $data['month_division']) {
                $i = 'H';
                if ('q' == $data['month_division']) {
                    $i = 'Q';
                }
                if (preg_match('/\d{2,2}(\d+)-(\d+)/', $data['month_division_list'], $matches)) {
				/***********************
				sng:20/oct/2011
				using %d makes it like 'starting with 4Q 8' instead of 4Q 08
				*********************/
                    $labels[] = sprintf($divisions[$data['month_division']] . ' starting with %d' . $i . ' %s', $matches[2], $matches[1]);
                }                 
            } else {
                $labels[] = sprintf($divisions[$data['month_division']] . ' starting with %d', $data['month_division_list']);
            }
        }
        
        if (self::idxExists($data, 'number_of_deals')) {
            $tmp = 'Only %s deals';
            switch ($data['number_of_deals']) {
                case 'top:10':
                        $par = 'top ten';
                    break;
                case 'top:25':
                        $par = 'top twenty five';
                    break;
                case 'recent:10':
                        $par = 'recent ten';
                    break;
                case 'recent:25':
                        $par = 'recent twenty five';
                    break;
                default:
                    break;
            }
           
            if (isset($par)) {
                $labels[] = sprintf($tmp, $par);
            }
        }
        
        if (!count($labels)) {
            return 'No parameters were defined.';
        }
        
        return join(', ', $labels);
    }
    
    public static function idxExists($data, $idx) {
        if (isset($data[$idx]) && strlen($data[$idx])) {
            return true;
        }
        
        return false;
    }
	
	/**********************
	sng:27/oct/2011
	A duplicate code to do some dirty hack
	*************************/
	public static function clean_deal_filter_size($deal_size,&$labels){
		$tmp = 'Show only deals %s than %s %s';
		if (preg_match('/([><]{0,}[=])([\d\.]+)/', $deal_size, $matches)) {
			$val = ' billions ';
			$multiplier = 1;
			if ((double) $matches[1] < 1) {
				$val = 'millions';
				$multiplier = 1000;
			}
			
			switch ($matches[1]) {
				case '<=':
						$labels[] = sprintf($tmp, 'smaller ', $matches[2] * $multiplier, $val);
					break;
				case '>=':
						$labels[] = sprintf($tmp, 'bigger ', $matches[2] * $multiplier, $val);
					break;
				case '=':
						$labels[] = sprintf($tmp, 'equal ', $matches[2] * $multiplier, $val);
					break;
				default:
					break;
			}
		}else{
			/*********************
			sng:14/nov/2011
			maybe the value is just 0.0? see deal_search_filter_form_view
			***************************/
			if($deal_size=="0.0"){
				$labels[] = 'Show only deals with undisclosed value';
			}
		}
	}
	
	/************************************************
	sng:2/nov/2011
	a duplicate
	*************************************************/
	public static function clean_deal_filter_date($date_param,&$labels){
		/**
		 * Let`s put this here just in case
		 */
		if (preg_match('/(\d{4,})-(\d{4,})/', $date_param, $matches)) {
			if ($matches[2] == date('Y')){
				$date_param = $matches[1] . '-' . $matches[2] . 'YTD';
			}
		};
		$labels[] = $date_param;
	}
	/************************************************
	sng:2/nov/2011
	a duplicate, used in download_searched_deals.php, for downloading to excel.
	
	sng:27/jan/2012
	In downloading to excel, if we select size filter, the top:10 etc are deselected.
	Problem is, when we trigger a download, it really set number_of_deals to top:100
	so that the search code is tricked into returning only the largest 100 deals.
	
	That means, in the excel file, we should print Show top hundred. To do that
	we search for top:100 here and create a label.
	*************************************************/
	public static function clean_deal_filter_num_deals($num_param,&$labels){
		$tmp = 'Only %s deals';
		switch ($num_param) {
			case 'top:10':
					$par = 'top ten';
				break;
			case 'top:25':
					$par = 'top twenty five';
				break;
			case 'top:100':
					$par = 'top hundred';
				break;
			case 'recent:10':
					$par = 'recent ten';
				break;
			case 'recent:25':
					$par = 'recent twenty five';
				break;
			default:
				break;
		}
		if (isset($par)) {
        	$labels[] = sprintf($tmp, $par);
        }
	}
	
	/****************************************************
	sng:14/nov/2011
	method to show the deal type in home page. Can be used in other places also
	
	sng:19/jan/2012
	For M&A, now we do not show which company was acquired
	***************************************************/
	public static function get_deal_type_for_home_listing(&$data){
		$str = "";
		$str.= $data['deal_cat_name'];
		if((strtolower($data['deal_cat_name'])=="m&a")&&($data['target_company_name']!="")){
			if(strtolower($data['deal_subcat1_name'])=="completed"){
				//$str.= ". Acquisition of ".$data['target_company_name'];
			}else{
				//$str.= ". Proposed acquisition of ".$data['target_company_name'];
			}
			return $str;
		}
		
		if(strtolower($data['deal_cat_name'])=="equity"){
			/*******************
			show what kind of Equity.
			Check the deal_subcat1_name. If it is Equity again, go for the deal_subcat2_name
			************************/
			if(strtolower($data['deal_subcat1_name'])=="equity"){
				if(($data['deal_subcat2_name']!="")&&($data['deal_subcat2_name']!="n/a")){
					$str = $data['deal_subcat2_name'];
				}else{
					$str = "";
					//the section header identifies this as equity
				}
				return $str;
			}
			/************************
			the deal_subcat1_name is not Equity. so, if it is not blank or n/a, we show the deal_subcat1_name
			**************************/
			if(($data['deal_subcat1_name']!="")&&($data['deal_subcat1_name']!="n/a")){
				$str = $data['deal_subcat1_name'];
			}else{
				$str = "";
			}
			return $str;
		}
		
		if(strtolower($data['deal_cat_name'])=="debt"){
			//for Debt, we show what kind of debt, not Debt, Loan
			if(($data['deal_subcat1_name']!="")&&($data['deal_subcat1_name']!="n/a")){
				$str = $data['deal_subcat1_name'];
			}
			return $str;
		}
		return $str;
	}
	
	/*************************
	sng:27/jan/2012
	When we are downloading to excel, we only have the deal size range id. From that we need the label to show.
	We query the db to get it.
	
	Note: if this is 0 it means 'undisclosed' see deal_search_filter_form_view.php
	
	This is inefficient and costly. If we need deal range label as part of deal listing, we are better off by using a join query
	to get the label for the deals. This is why we do not use this function in other places
	******************************/
	public static function get_label_for_deal_value_range_id($value_range_id){
		if($value_range_id == 0){
			return "Not disclosed";
		}
		
		require_once("classes/db.php");
		$db = new db();
		$q = "select display_text from ".TP."transaction_value_range_master where value_range_id='".$value_range_id."'";
		$ok = $db->select_query($q);
		if(!$ok){
			return "";
			//we are not going to hang the download
		}
		if(!$db->has_row()){
			return "";
		}
		$row = $db->get_row();
		return $row['display_text'];
	}
	/**********************
	Now that we have multiple companies for a deal, we might want to show it as a csv.
	Normally we also create a hyperlink to the company detail page.
	If we have an array, we can create the csv
	**********************/
	public static function deal_participants_to_csv_with_links($participant_list){
		$cnt = count($participant_list);
		if(0==$cnt){
			return "";
		}
		$data = "";
		for($i=0;$i<$cnt;$i++){
			$data.=", "."<a href='company.php?show_company_id=".$participant_list[$i]['company_id']."'>".$participant_list[$i]['company_name']."</a>";
		}
		$data = substr($data,2);
		return $data;
	}
	
	/**********************
	Now that we have multiple companies for a deal, we might want to show it as a csv.
	In some cases, we just need the company name (like when we are downloading)
	If we have an array, we can create the csv
	**********************/
	public static function deal_participants_to_csv($participant_list){
		$cnt = count($participant_list);
		if(0==$cnt){
			return "";
		}
		$data = "";
		for($i=0;$i<$cnt;$i++){
			$data.=", ".$participant_list[$i]['company_name'];
		}
		$data = substr($data,2);
		return $data;
	}
	/**********************
	sng:18/oct/2012
	Now that we have multiple companies for a deal, we might want to show it as a csv.
	There are codes that used to show the company name, its country, sector, industry.
	With participants list, we cannot have list of companies in one cell and list of sectors in another cell. We ned to show
	the participating companies along with sector/industry
	
	sng:20/nov/2012
	We also add the role (if there is one)
	**********************/
	public static function deal_participants_to_csv_with_detail($participant_list){
		$cnt = count($participant_list);
		if(0==$cnt){
			return "";
		}
		$data = "";
		for($i=0;$i<$cnt;$i++){
		
			$name = $participant_list[$i]['company_name'];
			$hq_country = $participant_list[$i]['hq_country'];
			$sector = $participant_list[$i]['sector'];
			$industry = $participant_list[$i]['industry'];
			
			$data.=", ".$participant_list[$i]['company_name'];
			$extra = "";
			if(""!=$hq_country){
				$extra.=", ".$hq_country;
			}
			if(""!=$sector){
				$extra.=", ".$sector;
			}
			if(""!=$industry){
				$extra.=", ".$industry;
			}
			if(""!=$extra){
				$extra = substr($extra,2);
				$data.="[".$extra."]";
			}
			/*********
			sng:20/nov/2012
			We check with role_id. That is more accurate
			*********/
			$role_id = $participant_list[$i]['role_id'];
			$role_name = $participant_list[$i]['role_name'];
			if($role_id!=0){
				$data.=": ".$role_name;
			}
		}
		$data = substr($data,2);
		return $data;
	}
	/**************************
	sng:7/sep/2012
	filter deal_size:
	The value is either blank or like >=deal value in billion or <=deal value in billion
	This is base64_encoded so that it can slip past the sanitizer and then decoded before it is used
	in query
	Problem is, we can forgot to decode it and then we will be in problem. On the other hand, blindly
	decoding is also a problem. We do not know whether it has already been decoded or not.
	
	What we do is get the first char and check it. If it is > or < we return it as it is else decode it.
	****************************/
	public static function decode_deal_size($data){
		/*******
		cannot have variable like $char
		******/
		$c = substr($data,0,1);
		if(($c=='>')||($c=='<')){
			//all ok, return
			return $data;
		}else{
			return base64_decode($data);
		}
	}
}

