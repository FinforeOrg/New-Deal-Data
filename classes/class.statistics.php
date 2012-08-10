<?php
/****
contains methods related to stat computation
*********/
require_once("classes/class.magic_quote.php");
require_once("classes/class.barchart.php");
require_once("classes/class.stat_help.php");
require_once("classes/db.php");

class statistics{
    public function get_home_page_chart_list_paged($start_offset,$num_to_fetch,&$data_arr,&$data_count){
        global $g_mc;
        $q = "select id,name,generated_on from ".TP."charts order by name limit ".$start_offset.",".$num_to_fetch;
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        $data_count = mysql_num_rows($res);
        if($data_count == 0){
            //no recs
            return true;
        }
        //recs so
        while($row = mysql_fetch_assoc($res)){
            $row['name'] = $g_mc->db_to_view($row['name']);
            $data_arr[] = $row;
        }
        return true;
    }
    
    public function get_top_firms_list_paged($start_offset,$num_to_fetch,&$data_arr,&$data_count){
        global $g_mc;
        $q = "select id,caption,company_type,generated_on from ".TP."top_firms_by_criteria order by caption limit ".$start_offset.",".$num_to_fetch;
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        $data_count = mysql_num_rows($res);
        if($data_count == 0){
            //no recs
            return true;
        }
        //recs so
        while($row = mysql_fetch_assoc($res)){
            $row['caption'] = $g_mc->db_to_view($row['caption']);
            $data_arr[] = $row;
        }
        return true;
    }
    
    public function get_home_page_chart_data($id,&$data_arr){
        global $g_mc;
        $q = "select * from ".TP."charts where id='".$id."'";
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        $row = mysql_fetch_assoc($res);
        $data_arr['id'] = $row['id'];
        $data_arr['name'] = $g_mc->db_to_view($row['name']);
        $data_arr['img'] = $row['img'];
        $data_arr['containerId'] = $row['containerId'];
        //unserialize the params, name not required since we got the name
        $temp_arr = unserialize($row['params']);
        $data_arr['partner_type'] = $temp_arr['partner_type'];
        $data_arr['deal_cat_name'] = $temp_arr['deal_cat_name'];
        $data_arr['deal_subcat1_name'] = $temp_arr['deal_subcat1_name'];
        $data_arr['deal_subcat2_name'] = $temp_arr['deal_subcat2_name'];
        $data_arr['year'] = $temp_arr['year'];
        $data_arr['region'] = $temp_arr['region'];
        $data_arr['country'] = $temp_arr['country'];
        $data_arr['sector'] = $temp_arr['sector'];
        $data_arr['industry'] = $temp_arr['industry'];
        $data_arr['ranking_criteria'] = $temp_arr['ranking_criteria'];
        return true;
    }
    
    public function get_top_firms_data($id,&$data_arr){
        global $g_mc;
        $q = "select * from ".TP."top_firms_by_criteria where id='".$id."'";
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        $row = mysql_fetch_assoc($res);
        $data_arr['id'] = $row['id'];
        $data_arr['caption'] = $g_mc->db_to_view($row['caption']);
        $data_arr['company_type'] = $row['company_type'];
        //the firm data are in id1|firm1|stat1]id2|firm2|stat2]
        $firm_data_arr = explode("]",$row['firm_data']);
        $data_arr['firms'] = array();
        $firm_count = count($firm_data_arr);
        for($a=0;$a<$firm_count;$a++){
            $data_arr['firms'][$a] = explode("|",$firm_data_arr[$a]);
            $data_arr['firms'][$a][1] = $g_mc->db_to_view($data_arr['firms'][$a][1]);
        }
        
        //unserialize the params, name not required since we got the name
        $temp_arr = unserialize($row['params']);
        $data_arr['partner_type'] = $temp_arr['partner_type'];
        $data_arr['deal_cat_name'] = $temp_arr['deal_cat_name'];
        $data_arr['deal_subcat1_name'] = $temp_arr['deal_subcat1_name'];
        $data_arr['deal_subcat2_name'] = $temp_arr['deal_subcat2_name'];
        $data_arr['year'] = $temp_arr['year'];
        $data_arr['region'] = $temp_arr['region'];
        $data_arr['country'] = $temp_arr['country'];
        $data_arr['sector'] = $temp_arr['sector'];
        $data_arr['industry'] = $temp_arr['industry'];
        $data_arr['ranking_criteria'] = $temp_arr['ranking_criteria'];
        return true;
    }
    
    public function delete_home_page_chart($chart_id,&$msg){
        /***
        sng:26/may/2010
        Hoame page chart can be assigned to a firm. If so, do not delete.
        This means, we take a ref to msg and set it from here
        *******/
        $q = "select count(*) as cnt from ".TP."firm_chart where chart_id='".$chart_id."'";
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        $row = mysql_fetch_assoc($res);
        if($row['cnt'] > 0){
            $msg = "Cannot delete. The chart is associated with a firm";
            return true;
        }
        /////////////////////////////////////////
        //get chart img first
        $q = "select img from ".TP."charts where id='".$chart_id."'";
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        $row = mysql_fetch_assoc($res);
        $img = $row['img'];
        if(($img!="")&&file_exists(FILE_PATH."/admin/charts/".$img)){
            unlink(FILE_PATH."/admin/charts/".$img);
        }
        //now delete record
        $q = "delete from ".TP."charts where id='".$chart_id."'";
        $result = mysql_query($q);
        if(!$result) return false;
        else{
            $msg = "Chart deleted";
            return true;
        }
    }
    
    public function delete_top_firms($id,&$msg){
        
        //delete record
        $q = "delete from ".TP."top_firms_by_criteria where id='".$id."'";
        $result = mysql_query($q);
        if(!$result) return false;
        else{
            $msg = "Deleted";
            return true;
        }
    }
    
    public function delete_firm_chart($id,&$msg){
        
        $q = "delete from ".TP."firm_chart where id='".$id."'";
        $result = mysql_query($q);
        if(!$result) return false;
        else{
            $msg = "Deleted";
            return true;
        }
    }
    
    public function generate_home_page_chart_image($param_arr,&$validation_passed,&$err_arr){
        return $this->home_page_chart_image(0,$param_arr,$validation_passed,$err_arr);
        
    }
    
    public function update_home_page_chart_image($id,$param_arr,&$validation_passed,&$err_arr){
        return $this->home_page_chart_image($id,$param_arr,$validation_passed,$err_arr);
    }
    
    public function generate_top_firms($param_arr,&$validation_passed,&$err_arr){
        return $this->top_firms_per_criteria(0,$param_arr,$validation_passed,$err_arr);
    }
    public function update_top_firms($id,$param_arr,&$validation_passed,&$err_arr){
        return $this->top_firms_per_criteria($id,$param_arr,$validation_passed,$err_arr);
    }
    /***
    sng:01/oct/2010
    to generate preset issuance charts
    ***/
    public function generate_issuance_page_chart_image($param_arr,&$validation_passed,&$err_arr){
        return $this->issuance_page_chart_image(0,$param_arr,$validation_passed,$err_arr);
    }
    public function update_issuance_page_chart_image($id,$param_arr,&$validation_passed,&$err_arr){
        return $this->issuance_page_chart_image($id,$param_arr,$validation_passed,$err_arr);
    }
    public function get_issuance_page_chart_list_paged($start_offset,$num_to_fetch,&$data_arr,&$data_count){
        global $g_mc;
        $q = "select id,name,generated_on from ".TP."issuance_charts order by name limit ".$start_offset.",".$num_to_fetch;
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        $data_count = mysql_num_rows($res);
        if($data_count == 0){
            //no recs
            return true;
        }
        //recs so
        while($row = mysql_fetch_assoc($res)){
            $row['name'] = $g_mc->db_to_view($row['name']);
            $data_arr[] = $row;
        }
        return true;
    }
    public function delete_issuance_page_chart($chart_id,&$msg){
        
        //get issuance chart img first
        $q = "select img from ".TP."issuance_charts where id='".$chart_id."'";
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        $row = mysql_fetch_assoc($res);
        $img = $row['img'];
        if(($img!="")&&file_exists(FILE_PATH."/admin/charts/".$img)){
            unlink(FILE_PATH."/admin/charts/".$img);
        }
        //now delete record
        $q = "delete from ".TP."issuance_charts where id='".$chart_id."'";
        $result = mysql_query($q);
        if(!$result) return false;
        else{
            $msg = "Chart deleted";
            return true;
        }
    }
    public function get_issuance_page_chart_data($id,&$data_arr){
        global $g_mc;
        $q = "select * from ".TP."issuance_charts where id='".$id."'";
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        $row = mysql_fetch_assoc($res);
        $data_arr['id'] = $row['id'];
        $data_arr['name'] = $g_mc->db_to_view($row['name']);
        $data_arr['img'] = $row['img'];
        //unserialize the params, name not required since we got the name
        $temp_arr = unserialize($row['params']);
        
        $data_arr['deal_cat_name'] = $temp_arr['deal_cat_name'];
        $data_arr['deal_subcat1_name'] = $temp_arr['deal_subcat1_name'];
        $data_arr['deal_subcat2_name'] = $temp_arr['deal_subcat2_name'];
        
        $data_arr['region'] = $temp_arr['region'];
        $data_arr['country'] = $temp_arr['country'];
        $data_arr['sector'] = $temp_arr['sector'];
        $data_arr['industry'] = $temp_arr['industry'];
        $data_arr['deal_size'] = $temp_arr['deal_size'];
        /*************
        sng:10/jan/2011
        we have added 2 fields
        *******/
        $data_arr['month_division'] = $temp_arr['month_division'];
        $data_arr['month_division_list'] = $temp_arr['month_division_list'];
        return true;
    }
    /*************************************************/
    
    public function assign_chart_to_firm($chart_id,$param_arr,&$validation_passed,&$err_arr){
        $validation_passed = true;
        if($param_arr['assign_firm_name']==""){
            $validation_passed = false;
            $err_arr['assign_company_id'] = "Please specify the firm";
        }else{
            if($param_arr['assign_company_id']==""){
                $validation_passed = false;
                $err_arr['assign_company_id'] = "The firm was not found";
            }
        }
        if(!$validation_passed){
            return true;
        }
        /////////////////////////////
        //check if this chart is assigned to this firm or not
        $q = "select count(*) as cnt from ".TP."firm_chart where company_id='".$param_arr['assign_company_id']."' and chart_id='".$chart_id."'";
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        $row = mysql_fetch_assoc($res);
        if($row['cnt']>0){
            $validation_passed = false;
            $err_arr['assign_company_id'] = "The chart has already been assigned to this firm";
            return true;
        }
        /////////////////////////////////
        //insert the record
        $q = "insert into ".TP."firm_chart set company_id='".$param_arr['assign_company_id']."', company_type='".$param_arr['company_type']."', chart_id='".$chart_id."'";
        $result = mysql_query($q);
        if(!$result){
            return false;
        }
        $validation_passed = true;
        return true;
    }
    
	/**************
	sng:22/sep/2011
	Given a chart, we need to show which firms are associated with the chart
	*************/
	public function firms_associated_with_chart($chart_id,&$data_arr,&$data_count){
		global $g_db;
		$q = "select f.id,name from ".TP."firm_chart as f left join ".TP."company as c on(f.company_id=c.company_id) where chart_id='".$chart_id."'";
		$success = $g_db->select_query($q);
		if(!$success){
			return false;
		}
		$data_count = $g_db->row_count();
		if(0 == $data_count){
			//no data
			return true;
		}
		$data_arr = $g_db->get_result_set_as_array();
		return true;
	}
	/*******************
	sng:26/sep/2011
	Given a chart and associated firms, admin may want to dissociate a firm from a chart (maybe
	after entering new deal data, the chart does not highlight the firm
	***********************/
	public function remove_firm_from_chart($firm_assoc_id){
		global $g_db;
		$q = "delete from ".TP."firm_chart where id='".$firm_assoc_id."'";
		$success = $g_db->mod_query($q);
		return $success;
	}
	
    public function firm_chart_list_paged($start_offset,$num_to_fetch,&$data_arr,&$data_count){
        global $g_mc;
        $q = "select f.id,company_type,chart_id,c.name as company_name,r.name as chart_name,r.img, r.containerId, r.id as chartId from ".TP."firm_chart as f left join ".TP."company as c on(f.company_id=c.company_id) left join ".TP."charts as r on(f.chart_id=r.id) order by company_name limit ".$start_offset.",".$num_to_fetch;
        $res = mysql_query($q);
        
        if(!$res){
            //echo mysql_error();
            return false;
        }
        $data_count = mysql_num_rows($res);
        if($data_count == 0){
            //no recs
            return true;
        }
        //recs so
        for($i=0;$i<$data_count;$i++){
            $data_arr[$i] = mysql_fetch_assoc($res);
            $data_arr[$i]['company_name'] = $g_mc->db_to_view($data_arr[$i]['company_name']);
            $data_arr[$i]['chart_name'] = $g_mc->db_to_view($data_arr[$i]['chart_name']);
        }
        return true;
    } 
    /////////////////////////////////////////FRONT END FUNCTIONS STARTS/////////////////
    public function front_get_home_page_charts(&$data_arr){
        global $g_mc;
        //get 2 random charts
        $q = "select id,name,img,generated_on from ".TP."charts order by rand() limit 0,2";
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        //there may not be any charts
        while($row = mysql_fetch_assoc($res)){
            $row['name'] = $g_mc->db_to_view($row['name']);
            $data_arr[] = $row;
        }
        return true;
    }
    
    /*******
    sng:4/jan/2011
    We will show a slideshow of the homepage chart images, so we get all the image and names
    *****/
    public function front_get_all_home_page_charts(&$data_arr,&$data_count){
        global $g_mc;
        
        $q = "select name,img from ".TP."charts order by generated_on desc";
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        $data_count = mysql_num_rows($res);
        if(0 == $data_count){
            //no charts
            return true;
        }
        for($i=0;$i<$data_count;$i++){
            $data_arr[$i] = mysql_fetch_assoc($res);
            $data_arr[$i]['name'] = $g_mc->db_to_view($data_arr[$i]['name']);
        }
        return true;
    }
    
    /***
    type: bank or law firm
    **/
    public function front_get_top_firms_per_criteria($type,&$data_arr,&$data_count){
        global $g_mc;
        $q = "select * from ".TP."top_firms_by_criteria where company_type='".$type."'";
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        $data_count = mysql_num_rows($res);
        if(0==$data_count){
            return true;
        }
        ///////////////////
        for($i=0;$i<$data_count;$i++){
            $row = mysql_fetch_assoc($res);
            $data_arr[$i] = array();
            $data_arr[$i]['caption'] = $g_mc->db_to_view($row['caption']);
            //the firms are in firm_data, separated by ]. We separate each
            $firm_data_tokens = explode("]",$row['firm_data']);
            //how many tokens
            $data_arr[$i]['firm_count'] = count($firm_data_tokens);
            if(0==$data_arr[$i]['firm_count']){
                $data_arr[$i]['firm_data_arr'] = NULL;
                continue;
            }
            $data_arr[$i]['firm_data_arr'] = $firm_data_tokens;
            //each firm data contains id and name, separated by |. We do not tokenize here
        }
        return true;
    }
    
    public function front_get_charts_for_firm($firm_id,&$data_arr,&$data_count){
        global $g_mc;
        $q = "select f.*,c.name,c.img,c.containerId, c.id from ".TP."firm_chart as f left join ".TP."charts as c on(f.chart_id=c.id) where company_id='".$firm_id."'";
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        $data_count = mysql_num_rows($res);
        if(0==$data_count){
            return true;
        }
        ///////////////////
        for($i=0;$i<$data_count;$i++){
            $data_arr[$i] = mysql_fetch_assoc($res);
            $data_arr[$i]['name'] = $g_mc->db_to_view($data_arr[$i]['name']);
        }
        return true;
    }
    /////////////////////////////////////////FRONT END FUNCTIONS ENDS///////////////////
    
    /////////////////////////////////////STAT FOR MEMBERS FRONT STARTS/////////////
    /***
    deal value is in billion and is in float
    ***/
    public function front_get_total_deal_value_of_member($member_id,&$deal_value_in_billion,$last_three_months = false){
        $three_month_stamp = strtotime("-3 months");
        $three_month_date = date("Y-m-d",$three_month_stamp);
        $q = "SELECT sum( t.value_in_billion ) AS total_deal_value, p.member_id FROM ".TP."transaction_partner_members AS p LEFT JOIN ".TP."transaction AS t ON ( p.transaction_id = t.id ) where member_id = '".$member_id."'";
        if($last_three_months){
            $q.=" and date_of_deal >='".$three_month_date."'";
        }
        $q.=" GROUP BY p.member_id";
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        $row = mysql_fetch_assoc($res);
        $deal_value_in_billion = $row['total_deal_value'];
        return true;
    }
    /////////////////////////////////////STAT FOR MEMBERS FRONT ENDS////////////////////
    
    private function home_page_chart_image($id,$param_arr,&$validation_passed,&$err_arr){
        global $g_mc,$g_barchart;
        //validation
        $validation_passed = true;
        
        if($param_arr['name']==""){
            $validation_passed = false;
            $err_arr['name'] = "Please specify the chart caption";
        }
        if($param_arr['partner_type']==""){
            $validation_passed = false;
            $err_arr['partner_type'] = "Please specify partner type";
        }
        if($param_arr['deal_cat_name']==""){
            $validation_passed = false;
            $err_arr['deal_cat_name'] = "Please specify type of deal";
        }
        //sub cat and sub sub cat are optional
        if($param_arr['year']==""){
            $validation_passed = false;
            $err_arr['year'] = "Please specify the year";
        }
        //country, region, sector, industry optional
        if($param_arr['ranking_criteria']==""){
            $validation_passed = false;
            $err_arr['ranking_criteria'] = "Please specify the ranking criteria";
        }
        if(!$validation_passed){
            //no need to proceed
            return true;
        }
        //////////////////////////////////////////////////////////////////////
        //we need to get the data
        $num_values = 0;
        $max_value = 0;
        $data_arr = array();
        $stat_params = array();
        /******
        sng:29/sep/2010
        this function is called only when admin is adding / updating home page chart
        Now that form sends id of a date range row instead of year
        Problem is, generate_ranking is also used by many other functions and we cannot change the
        behaviour. So we send a flag in the param_arr year_is_date_range_id with value of y
        **********************/
        $param_arr['year_is_date_range_id'] = 'y';
        $today = date("Y-m-d H:i:s");
        require_once(dirname(__FILE__) . '/class.leagueTableChart.php');
        $chart = new leagueTableChart($param_arr);
        $chart->setName(md5($param_arr['name'] . $today));
        //$chart->setTitle($param_arr['name']);
        
        $chartMarkup =  base64_encode($chart->getHtml(true));
        
        $serialized_param = serialize($param_arr);
        //we insert into db if id is 0, else this is an update operation
        
        if($id==0){
            $q = "insert into ".TP."charts ";
        }else{
            $q = "update ".TP."charts ";
        }
        
        $q .= sprintf("set name ='%s', img='%s', params = '%s', generated_on = '%s', containerId = '%s'", $param_arr['name'], $chartMarkup, $serialized_param, $today, md5($param_arr['name'] . $today));
        
        //if this is update, we put the where clause
        if($id!=0){
            $q.=" where id='".$id."'";
        }
         
        $result = mysql_query($q);
        if(!$result){
            return false;
        }
        /////////////////////
        $validation_passed = true;
        return true;
    }
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
    
    /*********
    sng:01/oct/2010
    A private function to create a preset charts that are to be shown by default in the issuance data page
    *********/
    private function issuance_page_chart_image($id,$param_arr,&$validation_passed,&$err_arr){
        global $g_mc,$g_barchart;
        //validation
        $validation_passed = true;
        
        if($param_arr['name']==""){
            $validation_passed = false;
            $err_arr['name'] = "Please specify the chart caption";
        }
        
        if($param_arr['deal_cat_name']==""){
            $validation_passed = false;
            $err_arr['deal_cat_name'] = "Please specify type of deal";
        }
        //sub category, sub sub category, region, country, sector, industry, deal size are optional
        if(!$validation_passed){
            //no need to proceed
            return true;
        }
        //////////////////////////////////////////////////////////////////////
        //we need to get the data
        $num_values = 0;
        $max_value = 0;
        $data_arr = array();
        
        $success = $this->generate_issuance_data($param_arr,$data_arr,$max_value,$num_values);
        if(!$success){
            return false;
        }
        if($num_values==0){
            return true;
        }
        
        ////////////////////////////////////////////////////////////
        //name will be magic quoted before it is inserted in database
        //caption cannot have any \'
        $caption = $g_mc->view_to_view($param_arr['name']);
        //////////////////////////////////////////////////////////////
        //create the chart
        $g_barchart->show_legend_detail(false);
        $font_path_name = FILE_PATH."/font/tahoma.ttf";
        $g_barchart->set_font($font_path_name);
        $g_barchart->set_dimension(500,275);
        $g_barchart->set_bar_gap(30);
        $g_barchart->set_bar_width(40);
        $g_barchart->set_stat_value_label_format("$%nbn");
        
        $img_name = "issuance_".time().".png";
        $store_image_path_name = FILE_PATH."/admin/charts/".$img_name;
        $g_barchart->render($data_arr,$max_value,$num_values,true,$store_image_path_name);
        ///////////////////////////////////////////
        //we need to store the clauses
        $param_arr['name'] = $g_mc->view_to_db($param_arr['name']);
        $serialized_param = serialize($param_arr);
        //if this is an update, before inserting into db, we need the prev image name and delete that
        if($id!=0){
            $q = "select img from ".TP."issuance_charts where id='".$id."'";
            $res = mysql_query($q);
            if(!$res){
                return false;
            }
            $row = mysql_fetch_assoc($res);
            $img = $row['img'];
        
            if(($img!="")&&file_exists(FILE_PATH."/admin/charts/".$img)){
                unlink(FILE_PATH."/admin/charts/".$img);
            }
        }
        //we insert into db if id is 0, else this is an update operation
        $today = date("Y-m-d H:i:s");
        if($id==0){
            $q = "insert into ".TP."issuance_charts ";
        }else{
            $q = "update ".TP."issuance_charts ";
        }
        
        $q.= "set name='".$param_arr['name']."',img='".$img_name."',params='".$serialized_param."', generated_on='".$today."'";
        //if this is update, we put the where clause
        if($id!=0){
            $q.=" where id='".$id."'";
        }
         
        $result = mysql_query($q);
        if(!$result){
            return false;
        }
        /////////////////////
        $validation_passed = true;
        return true;
    }
    
    /***
    sng:27/may/2010
    This is a support to get top 5 firms given a criteria and store the list. This way, the list can be shown again and again without
    recomputation. Example, a top 5 banks for Equity in year 2009 wil not change
    ********/
    private function top_firms_per_criteria($id,$param_arr,&$validation_passed,&$err_arr){
        global $g_mc,$g_barchart;
        //validation
        $validation_passed = true;
        
        if($param_arr['caption']==""){
            $validation_passed = false;
            $err_arr['caption'] = "Please specify the caption";
        }
        if($param_arr['partner_type']==""){
            $validation_passed = false;
            $err_arr['partner_type'] = "Please specify company type";
        }
        if($param_arr['deal_cat_name']==""){
            $validation_passed = false;
            $err_arr['deal_cat_name'] = "Please specify type of deal";
        }
        //sub cat and sub sub cat are optional
        if($param_arr['year']==""){
            $validation_passed = false;
            $err_arr['year'] = "Please specify the year";
        }
        //country, region, sector, industry optional
        if($param_arr['ranking_criteria']==""){
            $validation_passed = false;
            $err_arr['ranking_criteria'] = "Please specify the ranking criteria";
        }
        if(!$validation_passed){
            //no need to proceed
            return true;
        }
        //////////////////////////////////////////////////////////////////////
        //we need to get the data
        $num_values = 0;
        $max_value = 0;
        $data_arr = array();
        $stat_params = array();
        
        $success = $this->generate_ranking($param_arr,$data_arr,$max_value,$num_values);
        if(!$success){
            return false;
        }
        //even if there is no list, we do not return
        //note: the generate ranking return only name value pair, that is, item name and stat value
        //Here we need to get the id of the firm also. We do not change generate_ranking code
        //but make db call
        
        $data_stream = "";
        for($k=0;$k<$num_values;$k++){
            $data_stream_firm_name = $data_arr[$k]['name'];
            $data_stream_firm_value = $data_arr[$k]['value'];
            //we magic quote for view and then add slashes
            $temp_company_name = addslashes($g_mc->db_to_view($data_stream_firm_name));
            //get the id from name, remember to search with company name and type (since some firm acts as bank and law firm)
            $company_q = "select company_id from ".TP."company where type='".$param_arr['partner_type']."' and name='".$temp_company_name."'";
            $company_q_res = mysql_query($company_q);
            if(!$company_q_res){
                return false;
            }
            $company_q_res_cnt = mysql_num_rows($company_q_res);
            if(0 == $company_q_res_cnt){
                //strange, the firm's id cannot be found
                return false;
            }
            //found
            $company_q_res_row = mysql_fetch_assoc($company_q_res);
            $data_stream_firm_id = $company_q_res_row['company_id'];
            ///////////////////////////
            //we magic quote to view
            $item_data = $data_stream_firm_id."|".$g_mc->db_to_view($data_stream_firm_name)."|".$data_stream_firm_value;
            $data_stream.="]".$item_data;
        }
        if($data_stream != ""){
            //remove the first ]
            $data_stream = substr($data_stream,1);
        }
        ///////////////////////////////////////////////////////////////////////
        //we need to store the clauses
        $param_arr['caption'] = $g_mc->view_to_db($param_arr['caption']);
        $serialized_param = serialize($param_arr);
        
        //we insert into db if id is 0, else this is an update operation
        $today = date("Y-m-d H:i:s");
        if($id==0){
            $q = "insert into ".TP."top_firms_by_criteria ";
        }else{
            $q = "update ".TP."top_firms_by_criteria ";
        }
        
        $q.= "set caption='".$param_arr['caption']."',firm_data='".$data_stream."',company_type='".$param_arr['partner_type']."',params='".$serialized_param."', generated_on='".$today."'";
        //if this is update, we put the where clause
        if($id!=0){
            $q.=" where id='".$id."'";
        }
         
        $result = mysql_query($q);
        if(!$result){
            return false;
        }
        /////////////////////
        $validation_passed = true;
        return true;
    }
    
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
        *********/
        $exclude_term = $g_stat_h->exclude_month_div_entry($month_div);
        /***************************************************************/
        
        
        /****
        The deal size can be blank or <=valuein billion or >=value in billion
        ***/
        if($stat_params['deal_size']!=""){
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
        
        $q.=" GROUP BY D";
        
        $q.=" having D!='".$exclude_term."' order by D";
        /********************************************************/
        //echo $q;die();

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
            
            /***
            use lookup array to get the offset where to put data
            ***/
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
            $data_offset = $lookup_arr[$row['D']];
            
            $data_arr[$data_offset]['value'] = $data_value;
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
        global $g_mc;
        /*********************
        sng:29/sep/2010
        watch out for $stat_params['year_is_date_range_id']. If set and y, then deal year is not year but date range id.
        get the date range values and update the query to use deal date instead of deal year and set a flag
        ****************/
        if(isset($stat_params['year_is_date_range_id'])&&($stat_params['year_is_date_range_id']=='y')){
            $use_deal_date = true;
            $deal_range_q = "select date_from,date_to from ".TP."date_range_master where id='".$stat_params['year']."'";
            $deal_range_q_res = mysql_query($deal_range_q);
            if(!$deal_range_q_res){
                return false;
            }
            $deal_range_q_res_cnt = mysql_num_rows($deal_range_q_res);
            if(0 == $deal_range_q_res_cnt){
                return false;
            }
            $deal_range_q_res_row = mysql_fetch_assoc($deal_range_q_res);
            $use_deal_date_from = $deal_range_q_res_row['date_from'];
            $use_deal_date_to = $deal_range_q_res_row['date_to'];
        }else{
            $use_deal_date = false;
        }
        $filter_trans = "";
        $filter_trans_clause = "";
        if(isset($stat_params['deal_cat_name'])&&($stat_params['deal_cat_name']!="")){
            $filter_trans_clause.=" and deal_cat_name='".$stat_params['deal_cat_name']."'";
        }
        if(isset($stat_params['deal_subcat1_name'])&&($stat_params['deal_subcat1_name']!="")){
            $filter_trans_clause.=" and deal_subcat1_name='".$stat_params['deal_subcat1_name']."'";
        }
        if(isset($stat_params['deal_subcat2_name'])&&($stat_params['deal_subcat2_name']!="")){
            $filter_trans_clause.=" and deal_subcat2_name='".$stat_params['deal_subcat2_name']."'";
        }
        /***********************************************************
        sng:3/nov/2010
        Now when sector or industry is specified, we search in transaction table
        ***************/
        if(isset($stat_params['sector'])&&($stat_params['sector']!="")){
            $filter_trans_clause.=" and deal_sector like '%".$stat_params['sector']."%'";
        }
        if(isset($stat_params['industry'])&&($stat_params['industry']!="")){
            $filter_trans_clause.=" and deal_industry like '%".$stat_params['industry']."%'";
        }
        /*********************************************************************/
        /***
        sng:11/jun/2010
        The year can be in a range like 2009-2010 or it may be a single like 2009
        *******/
        if(isset($stat_params['year'])&&($stat_params['year']!="")){
            /*****
            sng:29/sep/2010
            if use_deal_date is true, it means, date range id was sent.
            in that case do not use year in the stat_param
            **********/
            if($use_deal_date){
                if($use_deal_date_from!="0000-00-00"){
                    $filter_trans_clause.=" and date_of_deal>='".$use_deal_date_from."'";
                }
                if($use_deal_date_to!="0000-00-00"){
                    $filter_trans_clause.=" AND date_of_deal<='".$use_deal_date_to."'";
                }
            }else{
                $year_tokens = explode("-",$stat_params['year']);
                $year_tokens_count = count($year_tokens);
                if($year_tokens_count == 1){
                    //singleton year
                    $filter_trans_clause.=" and year(date_of_deal)='".$year_tokens[0]."'";
                }
                if($year_tokens_count == 2){
                    //range year
                    $filter_trans_clause.=" and year(date_of_deal)>='".$year_tokens[0]."' AND year(date_of_deal)<='".$year_tokens[1]."'";
                }
            }
            ///$filter_trans_clause.=" and year(date_of_deal)='".$stat_params['year']."'";
        }
        /***
        sng:23/july/2010
        The deal size can be blank or <=valuein billion or >=value in billion
        ********/
        if(isset($stat_params['deal_size'])&&($stat_params['deal_size']!="")){
            $filter_trans_clause.=" and value_in_billion".$stat_params['deal_size'];
        }
        /**************************************************************************************
        sng:1/dec/2010
        Now when country is present, we check the transaction::deal_country field
        Same for region
        *********************/
        $country_filter = "";
        if(isset($stat_params['country'])&&($stat_params['country']!="")){
            //country specified, we do not consider region
            $country_filter.="deal_country LIKE '%".$stat_params['country']."%'";
        }else{
            //country not specified, check for region
            if(isset($stat_params['region'])&&($stat_params['region']!="")){
                //get the country names for this region name
                $region_q = "select cm.name from ".TP."region_master as rm left join ".TP."region_country_list as rc on(rm.id=rc.region_id) left join ".TP."country_master as cm on(rc.country_id=cm.id) where rm.name='".$stat_params['region']."'";
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
            $filter_trans_clause.=" and ".$country_filter;
        }
        /*********************************************************************************************/
        
		/**********************
		sng:10/aug/2012
        Since we use the filter clause only against transaction table, why not use it directly instead of using IN clause
		**********************/
        if (isset($stat_params['max_date'])) {
            $filter_trans_clause .= sprintf(' and last_edited < "%s" ', $stat_params['max_date']); 
        }
        /////////////////////////////////////
        if(isset($stat_params['ranking_criteria'])&&($stat_params['ranking_criteria'] == "num_deals")){
            $q = "SELECT deals_banks.num_deals as stat_value, .
                    companies.company_id, 
                    companies.name,companies.short_name 
                  FROM (
                    SELECT count( transaction_id ) AS num_deals, 
                        partner_id FROM ".TP."transaction_partners tp
                        LEFT JOIN " . TP . "transaction t 
                            ON t.id = tp.transaction_id                            
                    WHERE partner_type = '".$stat_params['partner_type']."'";
            if($filter_trans_clause != ""){
				/************
				sng:10/aug/2012
				since we already have AND
				****/
                $q.=$filter_trans_clause;
            }
            $q.=" GROUP BY partner_id ORDER BY num_deals DESC LIMIT 0, 5) AS deals_banks LEFT JOIN (SELECT company_id, name, short_name FROM ".TP."company WHERE TYPE = '".$stat_params['partner_type']."') AS companies ON ( deals_banks.partner_id = companies.company_id )";
        }else{
            /////////////////////////////////////////
            if(isset($stat_params['ranking_criteria'])&&($stat_params['ranking_criteria'] == "total_deal_value")){
                $q = "SELECT deals_assoc.total_deal_value AS stat_value, 
                        companies.company_id, companies.name, 
                        companies.short_name
                        FROM (
                            SELECT sum( t.value_in_billion ) AS total_deal_value, 
                                tp.partner_id FROM ".TP."transaction_partners AS tp 
                            LEFT JOIN ".TP."transaction AS t ON ( tp.transaction_id = t.id ) 
                            WHERE tp.partner_type = '".$stat_params['partner_type']."'";
							
                /************
				sng:10/aug/2012
				we reuse
				****/
                if($filter_trans_clause !=""){
                    $q.=$filter_trans_clause;
                }
               
                                
                
                /////////////////////////////////////////
                $q.=" GROUP BY partner_id ORDER BY total_deal_value DESC LIMIT 0, 5) AS deals_assoc LEFT JOIN (SELECT company_id, name, short_name FROM ".TP."company WHERE TYPE = '".$stat_params['partner_type']."') AS companies ON ( deals_assoc.partner_id = companies.company_id )";
                /////////////////////////////////////////////////////////////////////////
                //die($q);
            }else{
                if(isset($stat_params['ranking_criteria'])&&($stat_params['ranking_criteria'] == "total_adjusted_deal_value")){
                    $q = "SELECT deals_assoc.total_adjusted_deal_value as stat_value, 
                            companies.company_id, companies.name, 
                            companies.short_name 
                            FROM (
                                SELECT sum( adjusted_value_in_billion ) AS total_adjusted_deal_value, 
                                partner_id FROM ".TP."transaction_partners tp
                                LEFT JOIN " . TP . "transaction t 
                                    ON t.id = tp.transaction_id
                            WHERE partner_type = '".$stat_params['partner_type']."'";
							/************
				sng:10/aug/2012
				we reuse
				****/
                    if($filter_trans_clause != ""){
                        $q.=$filter_trans_clause;
                    }
                                         
                    $q.=" GROUP BY partner_id ORDER BY total_adjusted_deal_value DESC LIMIT 0, 5) AS deals_assoc LEFT JOIN (SELECT company_id, name, short_name FROM ".TP."company WHERE TYPE = '".$stat_params['partner_type']."') AS companies ON ( deals_assoc.partner_id = companies.company_id )";
                }else{
                    //unknown stat
                    return false;
                }
            }
        }
        ///////////////////////////////////////////////////////////////////////////
        $res = mysql_query($q);
        if(!$res){
			//echo $q;
            //echo mysql_error();
            return false;
        }
        $num_values = mysql_num_rows($res);
        if($num_values == 0){
            return true;
        }
        $max_value = "";
        for($i=0;$i<$num_values;$i++){
            $row = mysql_fetch_assoc($res);
            $data_arr[$i] = array();
            $data_arr[$i]['name'] = $g_mc->db_to_view($row['name']);
            $data_arr[$i]['short_name'] = $g_mc->db_to_view($row['short_name']);
            $data_arr[$i]['value'] = $row['stat_value'];
            /***
            sng:20/apr/2010
            if the stat is deal value, then it is in billion and has a high precision, correct
            to 2 decimal place
            ***/
            if($stat_params['ranking_criteria'] == "total_deal_value"){
                //$data_arr[$i]['value'] = $data_arr[$i]['value']*1000;
                $data_arr[$i]['value'] = round($data_arr[$i]['value'],2);
            }
            /***
            sng:20/apr/2010
            if the stat is deal value, then it is in billion and has a high precision,
            correct to 2 decimal place
            The values here are smaller than total deal value
            ***/
            if($stat_params['ranking_criteria'] == "total_adjusted_deal_value"){
                //$data_arr[$i]['value'] = $data_arr[$i]['value']*1000;
                $data_arr[$i]['value'] = round($data_arr[$i]['value'],2);
            }
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
        /*********************************************************************************
        sng:3/dec/2010
        now if sector is present, we will search in transaction::deal_sector
        if($stat_param['sector']!=""){
            $company_filter_clause.=" and sector='".$stat_param['sector']."'";
        }
        *************************************************************************************/
        /***
        sng:17/jul/2010
        add industry
        ***/
        /*********************************************************************************
        sng:3/dec/2010
        now if industry is present, we will search in transaction::deal_industry
        if($stat_param['industry']!=""){
            $company_filter_clause.=" and industry='".$stat_param['industry']."'";
        }
        ********************************************************************************/
        if($company_filter_clause != ""){
            $company_filter.=" and company_id IN (select company_id from ".TP."company where 1=1".$company_filter_clause.")";
        }
        ///////////////////////////////////////////////
        
        $q = "SELECT num_deals, partner_id, total_adjusted_deal_value, total_deal_value, name as firm_name FROM ( SELECT count( * ) AS num_deals, partner_id, sum( adjusted_value_in_billion ) AS total_adjusted_deal_value, sum( value_in_billion ) AS total_deal_value FROM ".TP."transaction_partners AS p LEFT JOIN ".TP."transaction AS t ON ( p.transaction_id = t.id ) WHERE partner_type = '".$stat_param['partner_type']."'";
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
        /*********************************************************************************
        sng:3/dec/2010
        now if sector is present, we will search in transaction::deal_sector
        if industry is present, we will search in transaction::deal_industry
        ****************/
        if($stat_param['sector']!=""){
            $q.=" and deal_sector like '%".$stat_param['sector']."%'";
        }
        if($stat_param['industry']!=""){
            $q.=" and deal_industry like '%".$stat_param['industry']."%'";
        }
        /***
        sng:11/jun/2010
        The year can be in a range like 2009-2010 or it may be a single like 2009
        *******/
        if($stat_param['year']!=""){
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
        ********/
        if($stat_param['deal_size']!=""){
            $q.=" and value_in_billion".$stat_param['deal_size'];
        }
        
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
        
        if (isset($stat_param['min_date'])) {
            $q .= sprintf(" and t.last_edited >= '%s'", $stat_param['min_date']);
        }

        if (isset($stat_param['max_date'])) {
            $q .= sprintf(" and t.last_edited < '%s'", $stat_param['max_date']);
        }    
        
        /////////////////////////////////////////////
        $q.=" GROUP BY partner_id";
        ///////////////////////////////////////
        //the ranking ordering
        $ranking_by = "";
        if($stat_param['ranking_criteria']=="num_deals") $ranking_by = "num_deals";
        else if($stat_param['ranking_criteria']=="total_deal_value") $ranking_by = "total_deal_value";
        else if($stat_param['ranking_criteria']=="total_adjusted_deal_value") $ranking_by = "total_adjusted_deal_value";
        if($ranking_by != ""){
            $q.=" ORDER BY ".$ranking_by." DESC";
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
    
    /*****
    generate league table for individuals
    ****/
    public function generate_top_individuals_paged($stat_param,$start_offset,$num_to_fetch,&$data_arr,&$data_count){
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
    
    /*************************
    sng:04/oct/2010
    *********************/
    public function front_get_random_issuance_charts($num_chart,&$data_arr,&$num_charts_found){
        global $g_mc;
        $q = "select id,name,img,generated_on from ".TP."issuance_charts order by rand() limit 0,".$num_chart;
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        $num_charts_found = mysql_num_rows($res);
        if(0==$num_charts_found){
            return true;
        }
        for($i=0;$i<$num_charts_found;$i++){
            $data_arr[$i] = mysql_fetch_assoc($res);
            $data_arr[$i]['name'] = $g_mc->db_to_view($data_arr[$i]['name']);
        }
        return true;
    }
}
$g_stat = new statistics();
?>