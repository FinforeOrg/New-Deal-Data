<?php
/*****************
q:Quarterly
h: Semi-Annual (half yearly)
y: Annual (yearly)
*****/
class stat_help{

	public $curr_yr;
	public $curr_month;
	
	public function __construct(){
		$this->curr_yr = date("Y");
		$this->curr_month = date("n");
	}
	public function volume_year_from($month_div){
		/******
		sng:29/nov/2010
		client wants to start from 2008, instead of prev 3 years. I am not sure what will happen in
		2012 when we will have data of 2008 to 2012. Also, what about data from 2005?
		anyway, let us start the year from 2008
		***************/
		$year_from = "2008";
		return $year_from;
	}
	
	public function volume_year_to($month_div){
		/************
		sng:29/nov/2010
		We will also include the running year, quarter, or year half, so the last year will be the current year
		**************/
		
		$year_to = $this->curr_yr;
		return $year_to;
		
	}
	
	public function volume_half_last(){
		//used for half yearly
		$year_to = $this->volume_year_to("h");
		/*****
		sng:29/nov/2010
		we want to include the running year-half also. However, we do not consider the year half that is yet to come
		For feb 2010, the last half will be 1H 2010
		For nov 2010, the last half will be 2H 2010
		*********/
		if($this->curr_month < 7){
			return $year_to."-1";
		}else{
			return $year_to."-2";
		}
	}
	
	public function volume_quarter_last(){
		//used for quarterly
		$year_to = $this->volume_year_to("q");
		/***********
		sng:29/nov/2010
		we want to show the running quarters also. However, we do not consider the quarter that is yet to come
		For feb 2010, the last quarter will be 1Q 2010
		For nov 2010, the last quarter should be 4Q 2010
		**********/
		//Q1 running
		if($this->curr_month < 4){
			return $year_to."-1";
		}
		// month >=4, Q2 running
		if($this->curr_month < 7){
			return $year_to."-2";
		}
		//month >=7, Q3 running
		if($this->curr_month < 10){
			return $year_to."-3";
		}
		//month >=10, Q4 running
		return $year_to."-4";
	}
	
	public function exclude_month_div_entry($month_div){
		/****
		sng:29/nov/2010
		We consider the running year, year half and quarter also
		****/
		if($month_div == "q"){
			if($this->curr_month < 4){
				return $this->curr_yr."-2";
			}
			// month >=4, Q2 running
			if($this->curr_month < 7){
				return $this->curr_yr."-3";
			}
			//month >=7, Q3 running
			if($this->curr_month < 10){
				return $this->curr_yr."-4";
			}
			//return $this->curr_yr."-4";
			return "";
		}
		if($month_div == "h"){
			if($this->curr_month < 7){
				$exclude_term = $this->curr_yr."-2";
			}else{
				//$exclude_term = $this->curr_yr."-2";
				$exclude_term = "";
			}
			return $exclude_term;
		}
		if($month_div == "y"){
			//$exclude_term = $this->curr_yr;
			$exclude_term = "";
			return $exclude_term;
		}
	}
	
	public function volume_get_month_div_entries($month_div,&$value_arr,&$label_arr){
		$value_arr = array();
		$label_arr = array();
		if($month_div == "y"){
			$year_from = $this->volume_year_from($month_div);
			$year_to = $this->volume_year_to($month_div);
			for($i=$year_from;$i<=$year_to;$i++){
				$value_arr[] = $i;
				
				$label_arr[] = $this->convert_to_short_label($i,$month_div);
			}
			return;
		}
		
		if($month_div == "h"){
			
			$year_from = $this->volume_year_from($month_div);
			$year_to = $this->volume_year_to($month_div);
			$last_entry = $this->volume_half_last();
			
			for($y=$year_from;$y<=$year_to;$y++){
				for($h=1;$h<=2;$h++){
					$value = $y."-".$h;
					
					$value_arr[] = $value;
					
					$label_arr[] = $this->convert_to_short_label($value,$month_div);
					if($value==$last_entry){
						return;
					}
				}
			}
		}
		
		if($month_div == "q"){
			$year_from = $this->volume_year_from($month_div);
			$year_to = $this->volume_year_to($month_div);
			$last_entry = $this->volume_quarter_last();
			
			for($y=$year_from;$y<=$year_to;$y++){
				for($q=1;$q<=4;$q++){
					$value = $y."-".$q;
					
					$value_arr[] = $value;
					
					$label_arr[] = $this->convert_to_short_label($value,$month_div);
					if($value==$last_entry){
						return;
					}
				}
			}
		}
	}
	
	public function convert_to_short_label($month_div_entry,$month_div){
		if($month_div == "y"){
			/****
			sng:29/nov/2010
			years are like 2009 etc. We also show current year, with YTD
			*****/
			$str = $month_div_entry;
			if($month_div_entry == $this->curr_yr){
				$str.="YTD";
			}
			return $str;
		}
		$tokens = explode("-",$month_div_entry);
		if($month_div == "h"){
			/****
			sng:29/nov/2010
			half years are like 2009-1 etc. We also show current year nad the running half year, with YTD
			*****/
			$str = $tokens[1]."H ".substr($tokens[0],2,2);
			if($month_div_entry == $this->volume_half_last()){
				$str.="YTD";
			}
			return $str;
		}
		if($month_div == "q"){
			/****
			sng:29/nov/2010
			quarters are like 2009-4 etc. We also show current year nad the running quarter, with YTD
			*****/
			$str = $tokens[1]."Q ".substr($tokens[0],2,2);
			if($month_div_entry == $this->volume_quarter_last()){
				$str.="YTD";
			}
			return $str;
		}
	}
	public function volume_get_month_div_entries_starting_from($month_div,$starting_month_div_entry,&$value_arr,&$label_arr){
		
		$temp_value_arr = NULL;
		$temp_label_arr = NULL;
		$this->volume_get_month_div_entries($month_div,$temp_value_arr,$temp_label_arr);
		
		$start_offset = 0;
		
		$cnt = count($temp_value_arr);
		for($i=0;$i<$cnt;$i++){
			$combo = $temp_value_arr[$i];
			if($combo == $starting_month_div_entry){
				$start_offset = $i;
				break;
			}
		}
		
		for($i=$start_offset;$i<$cnt;$i++){
			$value_arr[] = $temp_value_arr[$i];
			$label_arr[] = $temp_label_arr[$i];
		}
		return;
	}
}
$g_stat_h = new stat_help();
?>