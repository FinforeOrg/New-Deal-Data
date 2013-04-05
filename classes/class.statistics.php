<?php
/****
contains methods related to stat computation
*********/
require_once("classes/class.magic_quote.php");
require_once("classes/class.barchart.php");
require_once("classes/class.stat_help.php");
require_once("classes/db.php");

class statistics{
	/***************
	sng:27/nov/2012
	We no longer use admin created charts in home page
	public function get_home_page_chart_list_paged($start_offset,$num_to_fetch,&$data_arr,&$data_count)
	public function get_home_page_chart_data($id,&$data_arr)
	public function delete_home_page_chart($chart_id,&$msg)
	public function generate_&nbsp;&nbsp;&nbsp;&nbsp;($param_arr,&$validation_passed,&$err_arr)
	public function update_home_page_chart_image($id,$param_arr,&$validation_passed,&$err_arr)
	private function home_page_chart_image($id,$param_arr,&$validation_passed,&$err_arr)
	****************/
    
	
	
	/****************************************************
    sng:01/oct/2010
    A private function to create a preset charts that are to be shown by default in the issuance data page
	private function issuance_page_chart_image($id,$param_arr,&$validation_passed,&$err_arr)
    
    sng:01/oct/2010
    to generate preset issuance charts
	public function generate_issuance_page_chart_image($param_arr,&$validation_passed,&$err_arr)
	
	public function update_issuance_page_chart_image($id,$param_arr,&$validation_passed,&$err_arr)
	
	public function get_issuance_page_chart_list_paged($start_offset,$num_to_fetch,&$data_arr,&$data_count)
	
	public function delete_issuance_page_chart($chart_id,&$msg)
	
	public function get_issuance_page_chart_data($id,&$data_arr)
	
	sng:27/nov/2012
	We no longer show pre-generated issuance data chart in front end so these are not needed
	---------------
	sng:04/oct/2010
	
	sng:27/nov/2012
	Not used anywhere
	public function front_get_random_issuance_charts($num_chart,&$data_arr,&$num_charts_found)
	*********************************************/
    
    
	
	/*************************
	sng:27/nov/2012
	We no longer use admin created charts and assign it to a firm
	public function firm_chart_list_paged($start_offset,$num_to_fetch,&$data_arr,&$data_count)
	public function delete_firm_chart($id,&$msg)
	public function assign_chart_to_firm($chart_id,$param_arr,&$validation_passed,&$err_arr)
	
	sng:22/sep/2011
	Given a chart, we need to show which firms are associated with the chart
	public function firms_associated_with_chart($chart_id,&$data_arr,&$data_count)
	
	sng:26/sep/2011
	Given a chart and associated firms, admin may want to dissociate a firm from a chart (maybe
	after entering new deal data, the chart does not highlight the firm
	public function remove_firm_from_chart($firm_assoc_id)
	
	public function front_get_charts_for_firm($firm_id,&$data_arr,&$data_count)
	******************************/
     
    /////////////////////////////////////////FRONT END FUNCTIONS STARTS/////////////////
	/*********************
	sng:27/nov/2012
	We no longer show two pre-created charts on home page so no longer need this
	public function front_get_home_page_charts(&$data_arr)
	*********************/
    
    
    /*******
    sng:4/jan/2011
    We will show a slideshow of the homepage chart images, so we get all the image and names
	
	sng:27/nov/2012
	We now have League table generator in home page so we no longer need this
	public function front_get_all_home_page_charts(&$data_arr,&$data_count)
    *****/
    
    
    /******************
	sng:27/nov/2012
	we no longer have this concept of top banks per criteria. It is admin who marks some banks as 'top'
	
	type: bank or law firm
	public function front_get_top_firms_per_criteria($type,&$data_arr,&$data_count)
	
	
	public function generate_top_firms($param_arr,&$validation_passed,&$err_arr)
	public function update_top_firms($id,$param_arr,&$validation_passed,&$err_arr)
	
	
    sng:27/may/2010
    This is a support to get top 5 firms given a criteria and store the list. This way, the list can be shown again and again without
    recomputation. Example, a top 5 banks for Equity in year 2009 wil not change
    
    private function top_firms_per_criteria($id,$param_arr,&$validation_passed,&$err_arr)
	
	public function delete_top_firms($id,&$msg)
	
	public function get_top_firms_data($id,&$data_arr)
	
	public function get_top_firms_list_paged($start_offset,$num_to_fetch,&$data_arr,&$data_count)
	*******************/
    
    
    
    
    /////////////////////////////////////////FRONT END FUNCTIONS ENDS///////////////////
    
    /////////////////////////////////////STAT FOR MEMBERS FRONT STARTS/////////////
    /*************************
    deal value is in billion and is in float
	sng: 20/sep/2012
	Now we have announced/failed deals and inactive deals. We consider those flags too
    ***/
    public function front_get_total_deal_value_of_member($member_id,&$deal_value_in_billion,$last_three_months = false){
		$db = new db();
        $three_month_stamp = strtotime("-3 months");
        $three_month_date = date("Y-m-d",$three_month_stamp);
		
        $q = "SELECT sum( t.value_in_billion ) AS total_deal_value, p.member_id FROM ".TP."transaction_partner_members AS p LEFT JOIN ".TP."transaction AS t ON ( p.transaction_id = t.id ) where member_id = '".$member_id."' and is_active='y' and in_calculation='1'";
		
        if($last_three_months){
            $q.=" and date_of_deal >='".$three_month_date."'";
        }
        $q.=" GROUP BY p.member_id";
		
        $ok = $db->select_query($q);
        if(!$ok){
            return false;
        }
        $row = $db->get_row();
        $deal_value_in_billion = $row['total_deal_value'];
        return true;
    }
    /////////////////////////////////////STAT FOR MEMBERS FRONT ENDS////////////////////
    
    
    /*****
    Generate ranking data. This is basically an array where each element is an assoc array.
    Each assoc array contains 'name' and 'value', since this is the form in which barchart accept data
    The stat params are
        partner_type: can be bank or law firm
        deal_cat_name: main category of the deal like Equity
        deal_subcat1_name: sub category of the transaction
        deal_subcat2_name: sub sub category of the transaction
        year: year of the deal, if nothing is specified then all years
        region: if specified, take only deals by companies whose hq country is in the list of countries
        of that region
        country: if specified, take only deals done by companies whose hq country is the specified country
        NOTE: country override the region
        sng:21/apr/2010: added another stat param:industry. This is just for other functions that calls this method. You will notice that
        this is public now
        industry: industry of the company doing the deal.
        sng:20/may/2010
        added another param, sector. In fact, now home page charts also use sector and industry
        sector: sector of the company doing the deal
    ranking_criteria: 
        num_deals (total number of deals)
        total_deal_value: Total value of the deals in which the bank or law firm was involved
        total_adjusted_deal_value: Total of adjusted value for deals in which the bank or law firm was involved
    
    *****/
    
    
    
    
    
    /********************************************************
    sng:24/july/2010
    *******/
    public function generate_issuance_data($stat_params,&$data_arr,&$max_value,&$num_values){
        global $g_stat_h;
        /****
        It may happen that we do not get data for some quarters. In that case, those will be 0.
        What we do is, we first prefill the data array with default data, (quarter and 0). We also create a lookup
        array so that when we get the data of year quarter, we update the proper record.
        
        We start from the year from and 1Q and keep on adding quarters till we reach the year quarter that is to be excluded.
        
        This means, num_values will not be how many rows are there but how many values to put in lookup array. However, we do check how many
        rows are returned and return from this if no data is there.
        ***********/
        $filter_trans = "";
        if (!sizeOf($stat_params)) {
            $stat_params['deal_cat_name'] = ''; 
            $_POST['deal_cat_name'] = ''; 
            $stat_params['month_division'] = 'h';
            $_POST['month_division'] = 'h';
            $stat_params['month_division_list'] = '2008-1';
            $_POST['month_division_list'] = '2008-1';
             
        }
        //create the filter clauses for transaction
        /****************************************************
        27/nov/2010
        There can be deals with deal value not disclosed. Filter out those
        **********************/
        $filter_trans_clause = " and value_in_billion!='0'";
        if($stat_params['deal_cat_name']!=""){
            $filter_trans_clause.=" and deal_cat_name='".$stat_params['deal_cat_name']."'";
        }
        
        if($stat_params['deal_subcat1_name']!=""){
            $filter_trans_clause.=" and deal_subcat1_name='".$stat_params['deal_subcat1_name']."'";
        }
        
        if($stat_params['deal_subcat2_name']!=""){
            $filter_trans_clause.=" and deal_subcat2_name='".$stat_params['deal_subcat2_name']."'";
        }
        
        /*********************************************
        sng:26/nov/2010
        We can now group by quarter or year halfs or years.
        Member can select the starting year and the quarter or the half
        We use stat_help
        month_division_list holds data like 2009-1, where 2009 is the year and 1 may be year half 1 or quarter 1
        *************************************************/
        $month_div = $stat_params['month_division'];
        $year_month_div = $stat_params['month_division_list'];
        $tokens = explode("-",$year_month_div);
        $year_from = $tokens[0];
        
        $filter_trans_clause.=" and year(date_of_deal)>='".$year_from."'";
        /********
        to block the quarter/year/year half that is running, we will use having caluse after group by
        get the extry to be excluded. This depends on grouping type
        These will be like 2009-2
		
		sng: 4/sep/2012
		In the def of exclude_month_div_entry, there is this comment
		"sng:29/nov/2010
		We consider the running year, year half and quarter also"
		
		This means, we no longer block the running quarter/year half/year, so no need to call this function
        *********
        $exclude_term = $g_stat_h->exclude_month_div_entry($month_div);
		*************************/
        /***************************************************************/
        
        
        /****
        The deal size can be blank or <=valuein billion or >=value in billion
		
		sng:7/sep/2012
		better pass through util::decode_deal_size
        ***/
        if($stat_params['deal_size']!=""){
			$stat_params['deal_size'] = Util::decode_deal_size($stat_params['deal_size']);
            $filter_trans_clause.=" and value_in_billion".$stat_params['deal_size'];
        }
        
        
        /*************************************************************************************
        sng:1/dec/2010
        Now when country is present, we check the transaction::deal_country field
        Same for region
        
        sng:9/aug/2012
        We now have participating companies and we check country/sector/industry of the companies and consider only
		those deals in which the matching companies were participants
		
		We do not need IN clause. We can now use the WHERE clause to filter
		Also, if we have company attributes, we do an INNER join to get only the deals that are in both
        ***************************************************************************************/
        $filter_by_company_attrib = "";
		if(isset($stat_params['country'])&&($stat_params['country']!="")){
			if($filter_by_company_attrib != ""){
				$filter_by_company_attrib = $filter_by_company_attrib." AND ";
			}
			$filter_by_company_attrib.="hq_country='".mysql_real_escape_string($stat_params['country'])."'";
		}else{
			/**********
			might check region. Associated with a region is one or more countries
			We can use IN clause, that is hq_country IN (select country names for the given region), but it seems that
			it is much faster if we first get the country names and then create the condition with OR, that is
			(hq_country='Brazil' OR hq_country='Russia') etc
			***********/
			if(isset($stat_params['region'])&&($stat_params['region']!="")){
				//get the country names for this region name
				$region_q = "SELECT ctrym.name FROM ".TP."region_master AS rgnm LEFT JOIN ".TP."region_country_list AS rcl ON ( rgnm.id = rcl.region_id ) LEFT JOIN ".TP."country_master AS ctrym ON ( rcl.country_id = ctrym.id )
WHERE rgnm.name = '".mysql_real_escape_string($stat_params['region'])."'";
				$region_q_res = mysql_query($region_q);
				if(!$region_q_res){
					
					return false;
				}
				$region_q_res_cnt = mysql_num_rows($region_q_res);
				$region_clause = "";
				if($region_q_res_cnt > 0){
					while($region_q_res_row = mysql_fetch_assoc($region_q_res)){
						$region_clause.="|hq_country='".mysql_real_escape_string($region_q_res_row['name'])."'";
					}
					$region_clause = substr($region_clause,1);
					$region_clause = str_replace("|"," OR ",$region_clause);
					$region_clause = "(".$region_clause.")";
				}
				if($region_clause!=""){
					if($filter_by_company_attrib != ""){
						$filter_by_company_attrib = $filter_by_company_attrib." AND ";
					}
					$filter_by_company_attrib.=$region_clause;
				}
			}
		}
			
		if(isset($stat_params['sector'])&&($stat_params['sector']!="")){
			if($filter_by_company_attrib != ""){
				$filter_by_company_attrib = $filter_by_company_attrib." AND ";
			}
			$filter_by_company_attrib.="sector='".mysql_real_escape_string($stat_params['sector'])."'";
		}
			
		if(isset($stat_params['industry'])&&($stat_params['industry']!="")){
			if($filter_by_company_attrib != ""){
				$filter_by_company_attrib = $filter_by_company_attrib." AND ";
			}
			$filter_by_company_attrib.="industry='".mysql_real_escape_string($stat_params['industry'])."'";
		}
        /*************************
        sng:26/nov/2010
        Now the grouping can be quarterly, year halfly or yearly
        ************************************/
        $q = "SELECT sum( value_in_billion ) AS total_issuance, ";
        if($month_div == "q"){
            $q.="concat( year( date_of_deal ) , '-', quarter( date_of_deal ) ) AS D";
        }
        if($month_div == "h"){
            $q.="concat(year(date_of_deal),'-',((month(date_of_deal)-6) >0) +1) as D";
        }
        if($month_div == "y"){
            $q.="year( date_of_deal )  AS D";
        }
        /**************
		sng:9/aug/2012
		Now do the snippets
		**************/
        $q.=" FROM ".TP."transaction as t ";
		
		if($filter_by_company_attrib!=""){
			$q.="INNER JOIN (SELECT DISTINCT transaction_id from ".TP."transaction_companies as fca_tc left join ".TP."company as fca_c on(fca_tc.company_id=fca_c.company_id) where fca_c.type='company' AND ".$filter_by_company_attrib.") AS fca ON (t.id=fca.transaction_id) ";
		}
		
		$q.="where 1=1";
		
        if($filter_trans_clause!=""){
            $q.=$filter_trans_clause;
        }
        /***********
		sng:4/sep/2012
		consider only active deals
		consider deals which are 'completed' - for Debt/Equity or not marked explicitly to be excluded (M&A) - in_calculation
		************/
		$q.=" and t.is_active='y' and t.in_calculation='1'";
		
        $q.=" GROUP BY D";
        
        $q.=" having D!='".$exclude_term."' order by D";
        /********************************************************/
		

        $res = mysql_query($q);
        if(!$res){
            //echo mysql_error();
            return false;
        }
        
        $row_count = mysql_num_rows($res);
        if($row_count == 0){
            return true;
        }
        /********************************************
        sng:27/nov/2010
        It may happen that some quearters or halfs has no value. But we need to generate all the entries with dummy data
        The problem is, now we have quarterly or year halfly or yearly grouping and there user can select the start point
        We need to generate out data accordingly
        and also generate the labels accordingly
        We can use volume_get_month_div_entries_starting_from of stat help
        *********************************/
        $value_arr = NULL;
        $label_arr = NULL;
        $g_stat_h->volume_get_month_div_entries_starting_from($month_div,$year_month_div,$value_arr,$label_arr);
		
        $j = 0;
        $cnt = count($value_arr);
        $lookup_arr = array();
        
        for($i=0;$i<$cnt;$i++){
            
            $lookup_arr[$value_arr[$i]] = $i;
            $data_arr[$i] = array();
            $data_arr[$i]['short_name'] = $label_arr[$i];
            $data_arr[$i]['value'] = 0;
            
        }
        
        $num_values = count($lookup_arr);
        /*****************************************************************************/
        $max_value = "";

        for($i=0;$i<$row_count;$i++){
            $row = mysql_fetch_assoc($res);
			
			/***********************
			sng:4/sep/2012
			Assume today is sep 2012. The member select option 'Quarterly' start from '3Q 2012'
			The query loads data where deal year >= 2012. This means, data of 1Q and 2Q is also there in the recordset.
			We need to exclude those. How? We check if the tag is there in the lookup arr or not
			**********************/
			if(array_key_exists($row['D'],$lookup_arr)){
				$data_value = $row['total_issuance'];
				/***
				total deal value is in billion and has a high precision, correct to 2 decimal place
				**/
				$data_value = round($data_value,2);
            
				if($max_value == ""){
					$max_value = $data_value;
				}else{
					if($data_value > $max_value){
						$max_value = $data_value;
					}
				}
				/****
				use lookup array to get the offset where to put data
				****/
				$data_offset = $lookup_arr[$row['D']];
				$data_arr[$data_offset]['value'] = $data_value;
			}else{
				continue;
			}
            
			
            
            /***************************************************************
            sng:27/nov/2010
            end of change
            ***************************************************************/
        }
		
            $_SESSION['lastGeneratedGraphData'] =  $data_arr;
        return true;
    }
    /***********************************************
    /***
    sng:9/jul/2010
    Our output is list of names, short names, stat value, so that while generating chart
    if the short name is there, that name is used
    ********/
    public function generate_ranking($stat_params,&$data_arr,&$max_value,&$num_values){
		/*******
		We would like to use front_generate_league_table_for_firms_paged
		how this is different from front_generate_league_table_for_firms_paged($stat_param,$start_offset,$num_to_fetch,&$data_arr,&$data_count)?
		params
		1:year_is_date_range_id
		This param is used by only one function - statistics::home_page_chart_image (see old version of stat class)
		(deleted now since we no longer have pre-created League Table charts for home page).
		
		Therefore, we can ignore this - $use_deal_date = false
		
		companies.short_name: this is not fetched by LT code
		******/
		/****************
		ranking_criteria: if not set, we return false
		*******************/
		if(!isset($stat_params['ranking_criteria'])){
			return false;
		}else{
			if(($stat_params['ranking_criteria']!="num_deals")&&($stat_params['ranking_criteria']!="total_deal_value")&&($stat_params['ranking_criteria']!="total_adjusted_deal_value")){
				return false;
			}
		}
		/**********************
		num_values: ok, this is just data count, so we just pass the var as the 5th arg and we get the data
		We take 5 records using LIMIT
		******************/
		$start_offset = 0;
		$num_to_fetch = 5;
		$temp_data = NULL;
			
		$ok = $this->front_generate_league_table_for_firms_paged($stat_params,$start_offset,$num_to_fetch,$temp_data,$num_values);
		if(!$ok){
			return false;
		}
		if($num_values == 0){
			return true;
		}
		/***********************
		stat_value:
		value: is same as stat_value
		We need only one. LT code selects all 3 
		num_deals - count( tp.transaction_id )
		total_deal_value - sum( t.value_in_billion )
		total_adjusted_deal_value - sum( adjusted_value_in_billion )
		
		This depends upon ranking_criteria. We already checked that it exists and set correctly
		*********************/
		$stat_key = 'nope';
		if($stat_params['ranking_criteria']=="num_deals"){
			$stat_key = "num_deals";
		}elseif($stat_params['ranking_criteria']=="total_deal_value"){
			$stat_key = "total_deal_value";
		}elseif($stat_params['ranking_criteria']=="total_adjusted_deal_value"){
			$stat_key = "total_adjusted_deal_value";
		}
		
		$max_value = "";
		for($i=0;$i<$num_values;$i++){
			$row = $temp_data[$i];
			$data_arr[$i] = array();
			$data_arr[$i]['name'] = $row['firm_name'];
			$data_arr[$i]['short_name'] = $row['short_name'];
			$data_arr[$i]['value'] = $row[$stat_key];
			/************
			If the stat value is total deal value or total adjusted deal value, we already get
			the data in rounded format
			***************/
			
			
			if($max_value == ""){
				$max_value = $data_arr[$i]['value'];
			}else{
				if($data_arr[$i]['value'] > $max_value){
					$max_value = $data_arr[$i]['value'];
				}
			}
		}
		@session_start();
		$_SESSION['lastGeneratedRankings'] = $data_arr;
		
		return true;
	}
    /////////////////////////////////////////////////
    /******
    A function to generate league table for banks / law firms
    *********/
    public function front_generate_league_table_for_firms_paged($stat_param,$start_offset,$num_to_fetch,&$data_arr,&$data_count){
        global $g_mc;
        /********************
		sng:13/aug/2012
		Now we have one or more participants for a deal. We no longer check the deal_country/deal_sector/deal_industry csv fields for a deal.
		We now check the hq_country/sector/industry or the participants and consider only those deals
		*******************/
		$filter_by_company_attrib = "";
		if(isset($stat_param['country'])&&($stat_param['country']!="")){
			if($filter_by_company_attrib != ""){
				$filter_by_company_attrib = $filter_by_company_attrib." AND ";
			}
			$filter_by_company_attrib.="hq_country='".mysql_real_escape_string($stat_param['country'])."'";
		}else{
			/**********
			might check region. Associated with a region is one or more countries
			We can use IN clause, that is hq_country IN (select country names for the given region), but it seems that
			it is much faster if we first get the country names and then create the condition with OR, that is
			(hq_country='Brazil' OR hq_country='Russia') etc
			***********/
			if(isset($stat_param['region'])&&($stat_param['region']!="")){
				//get the country names for this region name
				$region_q = "SELECT ctrym.name FROM ".TP."region_master AS rgnm LEFT JOIN ".TP."region_country_list AS rcl ON ( rgnm.id = rcl.region_id ) LEFT JOIN ".TP."country_master AS ctrym ON ( rcl.country_id = ctrym.id )
WHERE rgnm.name = '".mysql_real_escape_string($stat_param['region'])."'";
				$region_q_res = mysql_query($region_q);
				if(!$region_q_res){
					
					return false;
				}
				$region_q_res_cnt = mysql_num_rows($region_q_res);
				$region_clause = "";
				if($region_q_res_cnt > 0){
					while($region_q_res_row = mysql_fetch_assoc($region_q_res)){
						$region_clause.="|hq_country='".mysql_real_escape_string($region_q_res_row['name'])."'";
					}
					$region_clause = substr($region_clause,1);
					$region_clause = str_replace("|"," OR ",$region_clause);
					$region_clause = "(".$region_clause.")";
				}
				if($region_clause!=""){
					if($filter_by_company_attrib != ""){
						$filter_by_company_attrib = $filter_by_company_attrib." AND ";
					}
					$filter_by_company_attrib.=$region_clause;
				}
			}
		}
			
		if(isset($stat_param['sector'])&&($stat_param['sector']!="")){
			if($filter_by_company_attrib != ""){
				$filter_by_company_attrib = $filter_by_company_attrib." AND ";
			}
			$filter_by_company_attrib.="sector='".mysql_real_escape_string($stat_param['sector'])."'";
		}
			
		if(isset($stat_param['industry'])&&($stat_param['industry']!="")){
			if($filter_by_company_attrib != ""){
				$filter_by_company_attrib = $filter_by_company_attrib." AND ";
			}
			$filter_by_company_attrib.="industry='".mysql_real_escape_string($stat_param['industry'])."'";
		}
        /*************************************************************/
        $q = "SELECT num_deals, partner_id, total_adjusted_deal_value, total_deal_value, name as firm_name FROM ( SELECT count( * ) AS num_deals, partner_id, sum( adjusted_value_in_billion ) AS total_adjusted_deal_value, sum( value_in_billion ) AS total_deal_value FROM ".TP."transaction_partners AS p LEFT JOIN ".TP."transaction AS t ON ( p.transaction_id = t.id )";
		/***************
		sng:13/aug/2012
		snippet
		Why inner join? We want to consider only those deals on the left which satisfy the conditions on the right
		***********/
		if($filter_by_company_attrib!=""){
			$q.=" INNER JOIN (SELECT DISTINCT transaction_id from ".TP."transaction_companies as fca_tc left join ".TP."company as fca_c on(fca_tc.company_id=fca_c.company_id) where fca_c.type='company' AND ".$filter_by_company_attrib.") AS fca ON (t.id=fca.transaction_id) ";
		}
		$q.=" WHERE partner_type = '".$stat_param['partner_type']."'";
		/*************
		sng:23/nov/2012
		Let us add another filter on transaction - exclude_partner_id.
		This allows us to use the same code to get league table for my competitors (we just exclude self)
		Used in class oneStop::getFifthTableResults
		****************/
		if(isset($stat_param['exclude_partner_id'])&&($stat_param['exclude_partner_id']!="")){
			$q.=" and p.partner_id!='".$stat_param['exclude_partner_id']."'";
		}
        //////////////////////////////////////////
        //filter on transaction types
        if(isset($stat_param['deal_cat_name'])&&($stat_param['deal_cat_name']!="")){
            $q.=" and deal_cat_name='".$stat_param['deal_cat_name']."'";
        }
        if(isset($stat_param['deal_subcat1_name'])&&($stat_param['deal_subcat1_name']!="")){
            $q.=" and deal_subcat1_name='".$stat_param['deal_subcat1_name']."'";
        }
        if(isset($stat_param['deal_subcat2_name'])&&($stat_param['deal_subcat2_name']!="")){
            $q.=" and deal_subcat2_name='".$stat_param['deal_subcat2_name']."'";
        }
        
        /***
        sng:11/jun/2010
        The year can be in a range like 2009-2010 or it may be a single like 2009
        *******/
        if(isset($stat_param['year'])&&($stat_param['year']!="")){
            $year_tokens = explode("-",$stat_param['year']);
            $year_tokens_count = count($year_tokens);
            if($year_tokens_count == 1){
                //singleton year
                $q.=" and year(date_of_deal)='".$year_tokens[0]."'";
            }
            if($year_tokens_count == 2){
                //range year
                $q.=" and year(date_of_deal)>='".$year_tokens[0]."' AND year(date_of_deal)<='".$year_tokens[1]."'";
            }
            ////$q.=" and year(date_of_deal)='".$stat_param['year']."'";
        }
        /***
        sng:23/july/2010
        The deal size can be blank or <=valuein billion or >=value in billion
		
		sng:7/sep/2012
		better pass through util::decode_deal_size
		
		sng:13/dec/2012
		better check if set or not
        ********/
        if((isset($stat_param['deal_size']))&&($stat_param['deal_size']!="")){
			$stat_param['deal_size'] = Util::decode_deal_size($stat_param['deal_size']);
            $q.=" and value_in_billion".$stat_param['deal_size'];
        }
        
        
        /*********************************************************************************************/
        if (isset($stat_param['min_date'])) {
            $q .= sprintf(" and t.last_edited >= '%s'", $stat_param['min_date']);
        }

        if (isset($stat_param['max_date'])) {
            $q .= sprintf(" and t.last_edited < '%s'", $stat_param['max_date']);
        }    
        /**************
		sng:5/sep/2012
		We need to exclude inactive deals
		We exclude 'announced' Debt / Equity deals and M&A deals that are explicitly marked (in_calculation=0)
		We use the alias t to mark the transaction table (just to be safe since other tables can have those fields)
		****************/
		$q.=" and t.is_active='y' and t.in_calculation='1'";
        /////////////////////////////////////////////
        $q.=" GROUP BY partner_id";
        ///////////////////////////////////////
        //the ranking ordering
		/**********************
		sng:23/nov/2012
		Let us tweak the ordering.
		By default, let us rank by number of deals, then by total deal value
		(if 2 firms has done same num of deals, then see who made more valuable deals)
		
		If the ranking is by total deal value, then order by total deal value, then by adjusted deal value
		(if 2 firms has made same amount of money, then see who had more, if individual shares are considered)
		
		If the ranking is by total adjusted deal value, then order by total adjusted deal value, then by total deal value
		(if 2 firms has made same amount of money individually, then see who has worked on more valuable deals)
		*******************/
        $ranking_by = "num_deals DESC, total_deal_value DESC";
        if($stat_param['ranking_criteria']=="num_deals") $ranking_by = "num_deals DESC, total_deal_value DESC";
        else if($stat_param['ranking_criteria']=="total_deal_value") $ranking_by = "total_deal_value DESC, total_adjusted_deal_value DESC";
        else if($stat_param['ranking_criteria']=="total_adjusted_deal_value") $ranking_by = "total_adjusted_deal_value DESC, total_deal_value DESC";
        if($ranking_by != ""){
            $q.=" ORDER BY ".$ranking_by;
        }
        $q.=" limit ".$start_offset.",".$num_to_fetch.") AS stat LEFT JOIN ".TP."company AS c ON ( stat.partner_id = c.company_id )";
        $res = mysql_query($q);
        if(!$res){
            //echo mysql_error();
            return false;
        }
        $data_count = mysql_num_rows($res);
        if(0 == $data_count){
            return true;
        }
        /////////////////////////
        for($i = 0;$i<$data_count; $i++){
            $data_arr[$i] = mysql_fetch_assoc($res);
            $data_arr[$i]['firm_name'] = $g_mc->db_to_view($data_arr[$i]['firm_name']);
            //convert the figures correct to 2 dec place, keep in billion
            $data_arr[$i]['total_adjusted_deal_value'] = round($data_arr[$i]['total_adjusted_deal_value'],2);
            $data_arr[$i]['total_deal_value'] = round($data_arr[$i]['total_deal_value'],2);
        }
        return true;
    }
    
	/*********************
    sng:5/apr/2013
    Our output is list of names, stat value. We show the full name of the member
    ********/
    public function generate_member_ranking($stat_params,&$data_arr,&$max_value,&$num_values){
		/*******
		We would like to use generate_top_individuals_paged
		******/
		/****************
		ranking_criteria: if not set, we return false
		*******************/
		if(!isset($stat_params['ranking_criteria'])){
			return false;
		}else{
			if(($stat_params['ranking_criteria']!="num_deals")&&($stat_params['ranking_criteria']!="total_deal_value")&&($stat_params['ranking_criteria']!="total_adjusted_deal_value")){
				return false;
			}
		}
		/**********************
		num_values: ok, this is just data count, so we just pass the var as the 5th arg and we get the data
		We take 10 records using LIMIT
		******************/
		$start_offset = 0;
		$num_to_fetch = 10;
		$temp_data = NULL;
			
		$ok = $this->generate_top_individuals_paged($stat_params,$start_offset,$num_to_fetch,$temp_data,$num_values);
		if(!$ok){
			return false;
		}
		if($num_values == 0){
			return true;
		}
		/***********************
		stat_value:
		value: is same as stat_value
		We need only one. LT code selects all 3 
		num_deals - count( tp.transaction_id )
		total_deal_value - sum( t.value_in_billion )
		total_adjusted_deal_value - sum( adjusted_value_in_billion )
		
		This depends upon ranking_criteria. We already checked that it exists and set correctly
		*********************/
		$stat_key = 'nope';
		if($stat_params['ranking_criteria']=="num_deals"){
			$stat_key = "num_deals";
		}elseif($stat_params['ranking_criteria']=="total_deal_value"){
			$stat_key = "total_deal_value";
		}elseif($stat_params['ranking_criteria']=="total_adjusted_deal_value"){
			$stat_key = "total_adjusted_deal_value";
		}
		
		$max_value = "";
		for($i=0;$i<$num_values;$i++){
			$row = $temp_data[$i];
			$data_arr[$i] = array();
			$data_arr[$i]['name'] = $row['f_name']." ".$row['l_name'];
			$data_arr[$i]['value'] = $row[$stat_key];
			/************
			If the stat value is total deal value or total adjusted deal value, we already get
			the data in rounded format
			***************/
			
			
			if($max_value == ""){
				$max_value = $data_arr[$i]['value'];
			}else{
				if($data_arr[$i]['value'] > $max_value){
					$max_value = $data_arr[$i]['value'];
				}
			}
		}
		@session_start();
		$_SESSION['lastGeneratedRankings'] = $data_arr;
		
		return true;
	}
    /*****
    generate league table for individuals
	sng:5/apr/2013
	This will have to be rewritten since the tables changed.
    ****/
    public function generate_top_individuals_paged($stat_param,$start_offset,$num_to_fetch,&$data_arr,&$data_count){
		$data_count = 10;
		$data_arr = array();
		
		$data_arr[] = array("f_name"=>"loi","l_name"=>"kae","num_deals"=>"34");
		$data_arr[] = array("f_name"=>"jou","l_name"=>"nue","num_deals"=>"40");
		
		$data_arr[] = array("f_name"=>"loi","l_name"=>"kae","num_deals"=>"34");
		$data_arr[] = array("f_name"=>"jou","l_name"=>"nue","num_deals"=>"40");
		
		$data_arr[] = array("f_name"=>"loi","l_name"=>"jet","num_deals"=>"50");
		
		$data_arr[] = array("f_name"=>"jou","l_name"=>"nue","num_deals"=>"40");
		
		$data_arr[] = array("f_name"=>"loi","l_name"=>"kae","num_deals"=>"34");
		$data_arr[] = array("f_name"=>"jou","l_name"=>"nue","num_deals"=>"40");
		
		$data_arr[] = array("f_name"=>"loi","l_name"=>"kae","num_deals"=>"34");
		$data_arr[] = array("f_name"=>"jou","l_name"=>"nue","num_deals"=>"40");
		return true;
		
        global $g_mc;
        ///////////////////////////////////////////////////////
        //filter on company of the transaction
        $company_filter = "";
        $company_filter_clause = "";
        /*************************************************************************************
        sng:1/dec/2010
        Now when country is present, we check the transaction::deal_country field
        Same for region
        so we do not check the company of the country doing the deal
        
        if($stat_param['country']!=""){
            $company_filter_clause.=" and hq_country='".$stat_param['country']."'";
        }else{
            //hq country not specified, so we cna check for region
            if($stat_param['region']!=""){
                $company_filter_clause.=" and hq_country IN (SELECT cm.name FROM ".TP."region_master AS rm LEFT JOIN ".TP."region_country_list AS rcl ON ( rm.id = rcl.region_id ) LEFT JOIN ".TP."country_master AS cm ON ( rcl.country_id = cm.id ) WHERE rm.name = '".$stat_param['region']."')";
            }
        }
        ***************************************************************************************/
        /*************************************************************************************
        sng:3/dec/2010
        Now when sector and industry is present, we search in the transaction table
        if($stat_param['sector']!=""){
            $company_filter_clause.=" and sector='".$stat_param['sector']."'";
        }
        ****************************************************************************************/
        if($company_filter_clause != ""){
            $company_filter.=" and company_id IN (select company_id from ".TP."company where 1=1".$company_filter_clause.")";
        }
        
        ///////////////////////////////////////////////
        //remember that a banker can change firm, so do not do anything with partner id, but group by member id
        
        $q = "SELECT num_deals, member_id, total_adjusted_deal_value, total_deal_value, f_name,l_name,profile_img,c.name as firm_name,c.company_id as firm_id FROM ( SELECT count( * ) AS num_deals, member_id,sum( adjusted_value_in_billion ) AS total_adjusted_deal_value, sum( value_in_billion ) AS total_deal_value FROM ".TP."transaction_partner_members AS p LEFT JOIN ".TP."transaction AS t ON ( p.transaction_id = t.id ) WHERE member_type = '".$stat_param['member_type']."'";
        //////////////////////////////////////////
        //filter on transaction types
        if($stat_param['deal_cat_name']!=""){
            $q.=" and deal_cat_name='".$stat_param['deal_cat_name']."'";
        }
        if($stat_param['deal_subcat1_name']!=""){
            $q.=" and deal_subcat1_name='".$stat_param['deal_subcat1_name']."'";
        }
        if($stat_param['deal_subcat2_name']!=""){
            $q.=" and deal_subcat2_name='".$stat_param['deal_subcat2_name']."'";
        }
        if($stat_param['year']!=""){
            $q.=" and year(date_of_deal)='".$stat_param['year']."'";
        }
        /***************************************************************************
        sng:3/dec/2010
        Now when sector or industry is prsent, we search in the transaction table
        *************/
        if($stat_param['sector']!=""){
            $q." and deal_sector like '%".$stat_param['sector']."%'";
        }
        if($stat_param['industry']!=""){
            $q." and deal_industry like '%".$stat_param['industry']."%'";
        }
        /********************************************************************************/
        /**************************************************************************************
        sng:1/dec/2010
        Now when country is present, we check the transaction::deal_country field
        Same for region
        *********************/
        $country_filter = "";
        if($stat_param['country']!=""){
            //country specified, we do not consider region
            $country_filter.="deal_country LIKE '%".$stat_param['country']."%'";
        }else{
            //country not specified, check for region
            if($stat_param['region']!=""){
                //get the country names for this region name
                $region_q = "select cm.name from ".TP."region_master as rm left join ".TP."region_country_list as rc on(rm.id=rc.region_id) left join ".TP."country_master as cm on(rc.country_id=cm.id) where rm.name='".$stat_param['region']."'";
                $region_q_res = mysql_query($region_q);
                if(!$region_q_res){
                    return false;
                }
                
                /*****************
                sng:1/Dec/2010
                No more the country of the HQ of the company doing the deal. Now use deal_country (which is a csv)
                So now that we have got the individual countries of the region. let us create a OR clause and
                for each country of the region, try to match it in deal_country. Since any one country from the region needs to
                match, we use a OR
                So say, region is BRIC. Then country filter is 
                (deal_country like '%Brazil%' OR deal_country like '%Russia%' OR deal_country like '%India%' OR deal_country like '%China%')
                
                ****/
                $region_q_res_cnt = mysql_num_rows($region_q_res);
                $region_clause = "";
                if($region_q_res_cnt > 0){
                    while($region_q_res_row = mysql_fetch_assoc($region_q_res)){
                        $region_clause.="|deal_country LIKE '%".$region_q_res_row['name']."%'";
                    }
                    $region_clause = substr($region_clause,1);
                    $region_clause = str_replace("|"," OR ",$region_clause);
                    $country_filter = "(".$region_clause.")";
                }
            }
        }
        if($country_filter!=""){
            $q.=" and ".$country_filter;
        }
        /*********************************************************************************************/
        
        //////////////////////

        if($company_filter!=""){
            $q.=$company_filter;
        }
        /////////////////////////////////////////////
        $q.=" GROUP BY member_id";
        ///////////////////////////////////////
        //the ranking ordering
        $ranking_by = "";
        if($stat_param['ranking_criteria']=="num_deals") $ranking_by = "num_deals";
        else if($stat_param['ranking_criteria']=="total_deal_value") $ranking_by = "total_deal_value";
        else if($stat_param['ranking_criteria']=="total_adjusted_deal_value") $ranking_by = "total_adjusted_deal_value";
        if($ranking_by != ""){
            $q.=" ORDER BY ".$ranking_by." DESC";
        }
        $q.=" limit ".$start_offset.",".$num_to_fetch.") AS stat LEFT JOIN ".TP."member AS m ON ( stat.member_id = m.mem_id ) left join ".TP."company as c on(m.company_id=c.company_id)";
        //echo $q;
        $res = mysql_query($q);
        if(!$res){
            //echo mysql_error();
            return false;
        }
        $data_count = mysql_num_rows($res);
        if(0 == $data_count){
            return true;
        }
        /////////////////////////
        for($i = 0;$i<$data_count; $i++){
            $data_arr[$i] = mysql_fetch_assoc($res);
            $data_arr[$i]['firm_name'] = $g_mc->db_to_view($data_arr[$i]['firm_name']);
            $data_arr[$i]['f_name'] = $g_mc->db_to_view($data_arr[$i]['f_name']);
            $data_arr[$i]['l_name'] = $g_mc->db_to_view($data_arr[$i]['l_name']);
            //convert the figures correct to 2 dec place, do not convert to million
            $data_arr[$i]['total_adjusted_deal_value'] = round($data_arr[$i]['total_adjusted_deal_value'],2);
            $data_arr[$i]['total_deal_value'] = round($data_arr[$i]['total_deal_value'],2);
        }
        return true;
    }
    
    
}
$g_stat = new statistics();
?>