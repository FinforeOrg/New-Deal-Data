<?php
require_once("classes/class.magic_quote.php");
require_once("classes/class.savedSearches.php");
require_once("classes/db.php");
class transaction{

    function __construct() {
        $this->savedSearches = new SavedSearches();
        $this->savedSearches->getFavoriteTombstones();
    }
    
    public function getCategoryTree() {
        $q = "select * from ".TP."transaction_type_master ";
        $res = mysql_query($q);
        $ret = array();
        if (!$res) {
            return $ret;
        }
        while($row = mysql_fetch_assoc($res)) {
            $ret[$row['type']][$row['subtype1']][] = $row['subtype2'];
            //var_dump($row);
        }
        //var_Dump($ret);
        return $ret;
    }    
//////////////////////////////////Select all category type///////////////////////////////////////////////////////////////
    /***
    sng:29/mar/2010
    since we are adding the transaction category name and subtype name for each transaction instead of type id
    we have simplified the transaction type table. We put category, sub category sub sub category in a same table
    This also allows us to put these data from excel file in a more simple manner
    ********/
    public function get_all_category_type($type,&$data_arr,&$data_count){
        $q = "select  DISTINCT ".$type." from ".TP."transaction_type_master ";
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
            $data_arr[] = $row;
        }
        return true;
    }
    
    /***
    sng:19/apr/2010
    This is different, we accept a deal category name and retrieve the sub category entries
    *******/
    public function get_all_category_subtype1_for_category_type($type_name,&$data_arr,&$data_count){
        if($type_name==""){
            //no type specified so no filtering
            $data_count = 0;
            return true;
        }
        $q = "select  DISTINCT subtype1 from ".TP."transaction_type_master where type='".$type_name."'";
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
            $data_arr[] = $row;
        }
        return true;
    }
    
    /***
    sng:19/apr/2010
    we accept a deal category name and deal subcategory name and retrieve the sub sub category entries
    *******/
    public function get_all_category_subtype2_for_category_type($type_name,$subtype_name,&$data_arr,&$data_count){
        if($type_name==""){
            //no type specified so no filtering
            $data_count = 0;
            return true;
        }
        $q = "select  DISTINCT subtype2 from ".TP."transaction_type_master where type='".$type_name."' and subtype1='".$subtype_name."'";
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
            $data_arr[] = $row;
        }
        return true;
    }
    /////////////////////////////////////////////////////////////////////////////
    public function get_all_category_type_subtype(&$data_arr,&$data_count){
        $q = "select  * from ".TP."transaction_type_master order by type, subtype1, subtype2";
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
            $data_arr[] = $row;
        }
        return true;
    }
    
    public function get_user_chosen_logos() {
        if (!isset($_SESSION['mem_id'])) {
            return array();
        }
        $tableName  = TP.'chosen_logos';       
        $q  = "SELECT logos FROM {$tableName} WHERE mem_id = {$_SESSION['mem_id']} LIMIT 1";
        $res = mysql_query($q);
        $result = mysql_fetch_assoc($res);
        if (is_array($result)) {
            return unserialize($result['logos']);
        }
    }
    public function add_category_type_subtype($data_arr,&$validation_passed,&$err_arr){
        $validation_passed = true;
        if($data_arr['type']==""){
            $validation_passed = false;
            $err_arr['type'] = "specify type";
        }
        
        if($data_arr['subtype1']==""){
            $validation_passed = false;
            $err_arr['subtype1'] = "specify sub type";
        }
        
        if($data_arr['subtype2']==""){
            $validation_passed = false;
            $err_arr['subtype2'] = "specify sub sub type or n/a";
        }
        
        //////////////////////////////////////////////////
        if(($data_arr['type']!="")&&($data_arr['subtype1']!="")&&($data_arr['subtype2']!="")){
            
            //check if the trio is already there ot not
            $q = "select count(*) as cnt from ".TP."transaction_type_master where type='".$data_arr['type']."' and subtype1='".$data_arr['subtype1']."' and subtype2='".$data_arr['subtype2']."'";
            $res = mysql_query($q);
            if(!$res){
                return false;
            }
            ////////////////////////////
            $row = mysql_fetch_assoc($res);
            if($row['cnt']!=0){
                //the trio is there
                $validation_passed = false;
                $err_arr['type'] = "These type/subtype are already there";
            }
        }
        //////////////////////////////////////////////////////////////////
        if(!$validation_passed){
            return true;
        }
        ///////////////////////////////
        //insert
        $q = "insert into ".TP."transaction_type_master set type='".$data_arr['type']."', subtype1='".$data_arr['subtype1']."', subtype2='".$data_arr['subtype2']."'";
        $result = mysql_query($q);
        if(!$result){
            return false;
        }
        return true;
    }
    
/////////////////////////////////////Select all category type end/////////////////////////////////////////////////////////////////

    /*********************************************************************************
    sng:23/july/2010
    There is now filter on deal size
	
	sng:20/jan/2012
	We no longer use the size_filter_master to get the conditions as options.
	Those work only with deals having exact value. Now we can have deals that are tagged with
	range id, like 3 (greater than 1 billion).
	We now use the transaction_value_range_master and the function deal_support::front_get_deal_value_range_list
    ********/
    public function front_get_deal_size_filter_list(&$data_arr,&$data_count){
        $q = "select * from ".TP."transaction_size_filter_master";
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
            $data_arr[] = $row;
        }
        return true;
    }
    /********************************************************************************/
    
    
    /***
    function to search for deal record
    This is for ADMIN
    sng:12/may/2010, full search
	
	sng:20/jun/2011
	Admin can now type the deal id to search for the deal
	
	sng:14/feb/2012
	We now have value range id for each deal that show the fuzzy deal value. These are predefined.
	Sometime, we only have value range id and deal value is 0
	If both deal value and value range id is 0, the deal value is undisclosed.
	
	We no longer have a single company associated with a deal. Now we have multiple companies
    ***/
    public function admin_search_for_deal($search_params_arr,&$data_arr,&$data_count){
        global $g_mc;
        
       /* $q = "SELECT t.id,t.value_in_billion,t.date_of_deal,deal_cat_name,deal_subcat1_name,deal_subcat2_name,c.name as company_name FROM ".TP."transaction AS t LEFT JOIN ".TP."company AS c ON ( t.company_id = c.company_id )";*/
		
		$q = "SELECT t.id,t.value_in_billion,t.date_of_deal,deal_cat_name,deal_subcat1_name,deal_subcat2_name,t.value_range_id,vrm.short_caption as fuzzy_value_short_caption,vrm.display_text as fuzzy_value FROM ".TP."transaction AS t LEFT JOIN ".TP."transaction_value_range_master as vrm ON (t.value_range_id=vrm.value_range_id)";
		
		$search_name = mysql_real_escape_string($search_params_arr['company_name']);
        if($search_name!=""){
			$q.= " right join (select distinct trc.transaction_id from ".TP."transaction_companies as trc left join ".TP."company as com on(trc.company_id=com.company_id) where com.name like '".$search_name."%') as p on(t.id=p.transaction_id)";
            /**$q.=" and c.name LIKE '".$search_name."%'";*/
        }
		
		$where_clause = "WHERE 1=1";
		
		if(isset($search_params_arr['deal_id'])&&$search_params_arr['deal_id']!=""){
            $where_clause.=" and t.id = '".$search_params_arr['deal_id']."'";
        }
		
        if($search_params_arr['deal_cat_name']!=""){
            $where_clause.=" and t.deal_cat_name = '".$search_params_arr['deal_cat_name']."'";
        }
        if($search_params_arr['deal_subcat1_name']!=""){
            $where_clause.=" and t.deal_subcat1_name = '".$search_params_arr['deal_subcat1_name']."'";
        }
        if($search_params_arr['deal_subcat2_name']!=""){
            $where_clause.=" and t.deal_subcat2_name = '".$search_params_arr['deal_subcat2_name']."'";
        }
        if($search_params_arr['year']!=""){
            $where_clause.=" and year(t.date_of_deal) = '".$search_params_arr['year']."'";
        }
        /******
        sng:13/sep/2010
        we also need to search on deal value
        ***/
        if($search_params_arr['value_from']!=""){
            $where_clause.=" and t.value_in_billion >= '".$search_params_arr['value_from']."'";
        }
        if($search_params_arr['value_to']!=""){
            $where_clause.=" and t.value_in_billion <= '".$search_params_arr['value_to']."'";
        }
        /***
        sng:31/aug/2010
        We also need to search on sector
        ***/
        /************************************************************************
        if($search_params_arr['sector']!=""){
            $q.=" and c.sector = '".$search_params_arr['sector']."'";
        }
        if($search_params_arr['industry']!=""){
            $q.=" and c.industry = '".$search_params_arr['industry']."'";
        }
        ******************************************************************************/
        /**********
        sng:8/jan/2011
        Transaction table now store sector and industry in csv format, so we no longer search for company sector/industry
        *******/
        if($search_params_arr['sector']!=""){
            $where_clause.=" and t.deal_sector like '%".$search_params_arr['sector']."%'";
        }
        if($search_params_arr['industry']!=""){
            $where_clause.=" and t.deal_industry like '%".$search_params_arr['industry']."%'";
        }
        /******************/
        /**********
		sng:14/feb/2012
		We now have list of participants
		*************/
        /***
        country and region
        if country is specified, region is overridden
        **/
        $country_filter = "";
        if($search_params_arr['country']!=""){
            /***************
            sng:8/jan/2011
            No more the country of the HQ of the company doing the deal. Now use deal_country (which is a csv)
            $country_filter = "c.hq_country='".$search_params_arr['country']."'";
            ********/
            $country_filter = "t.deal_country LIKE '%".$search_params_arr['country']."%'";
        }else{
            if($search_params_arr['region']!=""){
                //get the country names for this region name
                $region_q = "select cm.name from ".TP."region_master as rm left join ".TP."region_country_list as rc on(rm.id=rc.region_id) left join ".TP."country_master as cm on(rc.country_id=cm.id) where rm.name='".$search_params_arr['region']."'";
                $region_q_res = mysql_query($region_q);
                if(!$region_q_res){
                    return false;
                }
                /***********************************************************************
                sng:8/jan/2011
                No more the country of the HQ of the company doing the deal. Now use deal_country (which is a csv)
                So now that we have got the individual countries of the region. let us create a OR clause and
                for each country of the region, try to match it in deal_country. Since any one country from the region needs to
                match, we use a OR
                So say, region is BRIC. Then country filter is 
                (deal_country like '%Brazil%' OR deal_country like '%Russia%' OR deal_country like '%India%' OR deal_country like '%China%')
                
                $region_country_csv = "";
                $region_q_res_cnt = mysql_num_rows($region_q_res);
                if($region_q_res_cnt > 0){
                    while($region_q_res_row = mysql_fetch_assoc($region_q_res)){
                        $region_country_csv.=",'".$region_q_res_row['name']."'";
                    }
                    $region_country_csv = substr($region_country_csv,1);
                    $country_filter = "c.hq_country IN(".$region_country_csv.")";
                }
                *********/
                $region_q_res_cnt = mysql_num_rows($region_q_res);
                $region_clause = "";
                if($region_q_res_cnt > 0){
                    while($region_q_res_row = mysql_fetch_assoc($region_q_res)){
                        $region_clause.="|t.deal_country LIKE '%".$region_q_res_row['name']."%'";
                    }
                    $region_clause = substr($region_clause,1);
                    $region_clause = str_replace("|"," OR ",$region_clause);
                    $country_filter = "(".$region_clause.")";
                }
                /*******************************************************************/
            }
        }
        if($country_filter!=""){
            $where_clause.=" and ".$country_filter;
        }
        
		$q = $q." ".$where_clause;
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        //////////////////////////////////////////////////
        $data_count = mysql_num_rows($res);
        if(0==$data_count){
            return true;
        }
        //////////////////////////////////
		require_once("classes/class.transaction_company.php");
		$g_trans_comp = new transaction_company();
        for($i=0;$i<$data_count;$i++){
            $data_arr[$i] = mysql_fetch_assoc($res);
            /**
            sng:13/apr/2010
            we magic quote company name
            **/
            $data_arr[$i]['company_name'] = $g_mc->db_to_view($data_arr[$i]['company_name']);
            //set bankers and law firms
            $transaction_id = $data_arr[$i]['id'];
            $data_arr[$i]['banks'] = array();
            $data_cnt = 0;
            $success = $this->get_all_partner($transaction_id,"bank",$data_arr[$i]['banks'],$data_cnt);
            if(!$success){
                return false;
            }
            ///////////////////////////
            $data_arr[$i]['law_firms'] = array();
            $data_cnt = 0;
            $success = $this->get_all_partner($transaction_id,"law firm",$data_arr[$i]['law_firms'],$data_cnt);
            if(!$success){
                return false;
            }
			
			if(($data_arr[$i]['value_in_billion']==0)&&($data_arr[$i]['value_range_id']==0)){
				$data_arr[$i]['fuzzy_value'] = "Not disclosed";
				$data_arr[$i]['fuzzy_value_short_caption'] = "n/d";
			}
			
			/**************************
			sng:1/feb/2012
			get the deal participants, just the names
			*************************/
			$data_arr[$i]['participants'] = NULL;
			$success = $g_trans_comp->get_deal_participants($transaction_id,$data_arr[$i]['participants']);
			if(!$success){
				return false;
			}
        }
        return true;
    }
    
    /***
    sng:21/may/2010
    We also need transaction notes. since it will be text, we put that in separate table so that the transaction table can be
    traversed quickly.
    But then, we need to run another query to get the note
    
    sng:8/jul/2010
    Same observation for sources
	
	sng:17/jun/2011
	support for extra data
    *******/
    public function get_deal_edit($deal_id,&$data_arr,&$data_count){
        global $g_mc;
        /***********************
		sng:4/feb/2011
		support for deal private note
		************/
        $q = "select t.*,e.*,c.name as company_name,'note','deal_private_note' from ".TP."transaction as t left join ".TP."transaction_extra_detail as e on(t.id=e.transaction_id) left join ".TP."company as c on(t.company_id=c.company_id) where t.id='".$deal_id."'";
        //$q = "select * from ".TP."transaction where company_id='".$company_id."'";
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        $data_count = mysql_num_rows($res);
        if($data_count == 0){
            //no such company
            return false;
        }
        //recs so
        $data_arr = mysql_fetch_assoc($res);
        $_SESSION['logos'] = unserialize($data_arr['logos']);
        $_SESSION['logosCurrentIndex'] = is_array($_SESSION['logos']) ? sizeOf($_SESSION['logos']) : 0;
        /***
        sng: 13/apr/2010
        The company name is aliased to company_name
        **/
        $data_arr['company_name'] = $g_mc->db_to_view($data_arr['company_name']);
        $data_arr['target_company_name'] = $g_mc->db_to_view($data_arr['target_company_name']);
        $data_arr['seller_company_name'] = $g_mc->db_to_view($data_arr['seller_company_name']);
        //////////////////////////////////////
        //get the note if exists
        $note_q = "select note from ".TP."transaction_note where transaction_id='".$data_arr['id']."'";
        $note_q_res = mysql_query($note_q);
        if(!$note_q_res){
            return false;
        }
        $note_q_res_count = mysql_num_rows($note_q_res);
        if(0==$note_q_res_count){
            $data_arr['note']="";
        }else{
            $note_q_res_row = mysql_fetch_assoc($note_q_res);
            $data_arr['note'] = $g_mc->db_to_view($note_q_res_row['note']);
        }
		/*****************************************************
		sng:4/feb/2011
		support for deal private note
		**********/
		//get the note if exists
        $priv_note_q = "select note from ".TP."transaction_private_note where transaction_id='".$data_arr['id']."'";
        $priv_note_q_res = mysql_query($priv_note_q);
        if(!$priv_note_q_res){
            return false;
        }
        $priv_note_q_res_count = mysql_num_rows($priv_note_q_res);
        if(0==$priv_note_q_res_count){
            $data_arr['deal_private_note']="";
        }else{
            $priv_note_q_res_row = mysql_fetch_assoc($priv_note_q_res);
            $data_arr['deal_private_note'] = $g_mc->db_to_view($priv_note_q_res_row['note']);
        }
		/***********************************************/
        /****
        sng:8/jul/2010
        *******/
        //get the sources if exists
        $source_q = "select sources from ".TP."transaction_sources where transaction_id='".$data_arr['id']."'";
        $source_q_res = mysql_query($source_q);
        if(!$source_q_res){
            return false;
        }
        $source_q_res_count = mysql_num_rows($source_q_res);
        if(0==$source_q_res_count){
            $data_arr['sources']="";
        }else{
            $source_q_res_row = mysql_fetch_assoc($source_q_res);
            $data_arr['sources'] = $g_mc->db_to_view($source_q_res_row['sources']);
        }
        
        return true;
    }
    
    /***
    sng:31/aug/2010
    when we add the deal, we also want to add the banks and law firms. The easiest way to do this
    is to return the transaction_id so that we can show another page and allow the admin to add banks, law firms
    using the popups already created.
    
    sng:30/nov/2010
    Now we reintroduce the field deal country to hold multiple countries for a deal, a csv
    
    sng: 2/dec/2010
    We now use deal_sector, deal_industry to store sector , industry for a deal, a csv
	
	sng:14/feb/2012
	We have changed the workflow. We now allow admin to enter a min detail and then go to edit
    ***/
    public function add_deal($data_arr,&$validation_passed,&$new_transaction_id,&$err_arr){
		die("do not use");
        @session_start();
        global $g_mc;
        //$company_name = $g_mc->view_to_db($data_arr['name']);
        //validation
        /***
        sng:8/may/2010
        We now allow admin to type the deal company name. That opens a hint list and admin select a name
        This set the hidden company_id
        Now, if no name is typed, no company id is set so we first check for name.
        If the name is there, we check if the id is there or not since the company may not
        be in the database
        ****/
        $validation_passed = true;
        if($data_arr['deal_company_name'] == ""){
            $err_arr['company_id'] = "Please specify the company name";
            $validation_passed = false;
        }else{
            if($data_arr['company_id'] == ""){
                $err_arr['company_id'] = "The company was not found. Create it first.";
                $validation_passed = false;
            }
        }
        
        if($data_arr['value_in_billion'] == ""){
            $err_arr['value_in_billion'] = "Please specify the deal value";
            $validation_passed = false;
        }
        
        if($data_arr['date_of_deal'] == ""){
            $err_arr['date_of_deal'] = "Please specify the date of deal";
            $validation_passed = false;
        }
        if($data_arr['deal_cat_name'] == ""){
            $err_arr['deal_cat_name'] = "Please specify the deal category";
            $validation_passed = false;
        }
        if($data_arr['deal_subcat1_name'] == ""){
            $err_arr['deal_subcat1_name'] = "Please specify the deal subcategory1";
            $validation_passed = false;
        }
        if($data_arr['deal_subcat2_name'] == ""){
            $err_arr['deal_subcat2_name'] = "Please specify the deal subcategory2";
            $validation_passed = false;
        }
        /****
        sng:1/apr/2010
        we check target country, industry, company only in case of mergers and acquisitions deal
        **/
        if(($data_arr['deal_cat_name']=="M and A")||($data_arr['deal_cat_name']=="M&A")){
            if($data_arr['target_company_id']==""){
                //check if the name is specified or not
                if($data_arr['target_company_name']==""){
                    $err_arr['target_company_name'] = "Please specify the target company";
                    $validation_passed = false;
                }
            }
            //else target company selected
            if($data_arr['target_country'] == ""){
                $err_arr['target_country'] = "Please specify the target country";
                $validation_passed = false;
            }
            if($data_arr['target_sector'] == ""){
                $err_arr['target_sector'] = "Please specify the target sector";
                $validation_passed = false;
            }
        }
        /***
        sng:18/may/2010
        Coupon may be there, may not be there, so no need to check
        *********/
        /****
        if($data_arr['coupon'] == ""){
            $err_arr['coupon'] = "Please specify the coupon";
            $validation_passed = false;
        }
        *********/
        /////////////////////////////////////
        if(!$validation_passed){
            //no need to proceed
            return true;
        }
        ///////////////////////////////////////////////////////
        /****
        sng: 30/mar/2010
        We no longer use domain, we use sector and industry, so we do not use domain any more here
        ***********/
        /* check for defaul logos */
        
        /* first make sure logos are added in consecutive order */
        $defFound = false;
        $newLogos = array();
        if (is_array($_SESSION['logos'])) {
           foreach ($_SESSION['logos'] as $key=>$logo) {
               $newLogos[] = array('fileName'=>$logo['fileName'], 'default'=>$logo['default']);
               if ($logo['default'] == 1) {
                   $defFound = true;
               }
           } 
            if (!$defFound) {
               foreach ($newLogos as $key=>$logo) {
                   $newLogos[$key]['default'] =1; 
                   break;
               }  
            }
        }

        
        //insert data
        $q = "insert into ".TP."transaction set 
              company_id='".$data_arr['company_id']."', 
              value_in_billion='".$data_arr['value_in_billion']."',
              deal_country='".$data_arr['deal_country']."',
              deal_sector='".$data_arr['deal_sector']."',
              deal_industry='".$data_arr['deal_industry']."',
              currency='".$data_arr['currency']."',
              exchange_rate='".$data_arr['exchange_rate']."',
              value_in_billion_local_currency='".$data_arr['value_in_billion_local_currency']."',
              date_of_deal='".$data_arr['date_of_deal']."',
              deal_cat_name='".$data_arr['deal_cat_name']."',
              deal_subcat1_name='".$data_arr['deal_subcat1_name']."',
              deal_subcat2_name='".$data_arr['deal_subcat2_name']."',
              coupon='".$data_arr['coupon']."',
              base_fee='".$data_arr['base_fee']."',
              incentive_fee='".$data_arr['incentive_fee']."',
              current_rating='".$data_arr['current_rating']."',
              ev_ebitda_ltm='".$data_arr['ev_ebitda_ltm']."',
              ev_ebitda_1yr='".$data_arr['ev_ebitda_1yr']."',
              30_days_premia='".$data_arr['30_days_premia']."',
              1_day_price_change='".$data_arr['1_day_price_change']."',
              discount_to_last='".$data_arr['discount_to_last']."',
              discount_to_terp='".$data_arr['discount_to_terp']."',
              maturity_date='".$data_arr['maturity_date']."',
              target_company_id='".$data_arr['target_company_id']."',
              target_company_name='".$g_mc->view_to_db($data_arr['target_company_name'])."',
              target_country='".$data_arr['target_country']."',
              target_sector='".$data_arr['target_sector']."',
              seller_company_name='".$g_mc->view_to_db($data_arr['seller_company_name'])."',
              seller_country='".$data_arr['seller_country']."',
              seller_sector='".$data_arr['seller_sector']."',
              logos='".serialize($newLogos)."'
              ";
        $result = mysql_query($q);
        if(!$result){
            //echo $q;
            //echo mysql_error();
            return false;
        }
        /////////////////
        //data inserted
        /********************************************
        sng:21/may/2010
        try to update the note
        **********************************/
        $deal_id = mysql_insert_id();
        $new_transaction_id = $deal_id;
        $this->update_note($deal_id,$data_arr['note']);
        //never mind if there is error, this is not that important
		/*******************************
		sng:4/feb/2011
		try to update the private note
		********/
		$this->update_private_note($deal_id,$data_arr['deal_private_note']);
		//never mind if error
        /*******************
        sng:8/jul/2010
        try to update the sources
        ****************/
        $this->update_sources($deal_id,$data_arr['sources']);
        //never mind if there is error, this is not that important
        $validation_passed = true;
        return true;
    }
	
	/****************
	sng:27/jun/2011
	We now use a min set of data in deal add and do the rest in deal edit
	
	sng:18/feb/2012
	ONLY TO BE USED IN ADMIN SECTION
	*****************/
	public function add_deal_simple($data_arr,&$validation_passed,&$new_transaction_id,&$err_arr){
        
        global $g_mc;
        
        /***
        sng:8/may/2010
        We now allow admin to type the deal company name. That opens a hint list and admin select a name
        This set the hidden company_id
        Now, if no name is typed, no company id is set so we first check for name.
        If the name is there, we check if the id is there or not since the company may not
        be in the database
        ****/
        $validation_passed = true;
        if($data_arr['deal_company_name'] == ""){
            $err_arr['company_id'] = "Please specify the company name";
            $validation_passed = false;
        }else{
            if($data_arr['company_id'] == ""){
                $err_arr['company_id'] = "The company was not found. Create it first.";
                $validation_passed = false;
            }
        }
        
        $date_of_deal = "";
		
		if($data_arr['date_closed'] != ""){
            $date_of_deal = $data_arr['date_closed'];
        }elseif($data_arr['date_announced'] != ""){
			$date_of_deal = $data_arr['date_announced'];
		}
        
        if($date_of_deal == ""){
            $err_arr['date_of_deal'] = "Please specify either the announced date or completed date";
            $validation_passed = false;
        }
        if($data_arr['deal_cat_name'] == ""){
            $err_arr['deal_cat_name'] = "Please specify the deal category";
            $validation_passed = false;
        }
        if($data_arr['deal_subcat1_name'] == ""){
            $err_arr['deal_subcat1_name'] = "Please specify the deal subcategory1";
            $validation_passed = false;
        }
        if($data_arr['deal_subcat2_name'] == ""){
            $err_arr['deal_subcat2_name'] = "Please specify the deal subcategory2";
            $validation_passed = false;
        }
        
        /////////////////////////////////////
        if(!$validation_passed){
            //no need to proceed
            return true;
        }
        ///////////////////////////////////////////////////////
        //insert data
		/*******************************************************
		sng:9/sep/2011
		We now need to keep track of when the deal is added. It will be useful for
		writing queries like 'what has changed since ...'
		
		sng:14/sep/2011
		Now we have 2 fields added_on, last_edited
		when we add/edit the deal, we update last_edited.
		
		We set added_on when we add the deal and then never change it. This is a sort of
		record keeping
		
		sng:14/sep/2011
		We now store the field email_participating_syndicates in the simple add itself
		because when we create a deal based on member suggestion and the member wishes to notify
		others, we want the flag email_participating_syndicates stored as soon as the deal is created and
		do not wait for edit.
		********************************************************/
		$date_time_now = date("Y-m-d H:i:s");
		if(isset($data_arr['email_participants'])&&$data_arr['email_participants']=='y'){
			$email_participants = 'y';
		}else{
			$email_participants = 'n';
		}
		/************
		sng:14/feb/2012
		We no longer need the company_id here since we have a separate table for that
		
		sng:18/feb/2012
		Since admin is adding the deal, mark it as verified
		
		sng:20/feb/2012
		Since admin is adding the deal, mark it as active
		****************/
        $q = "insert into ".TP."transaction set 
              date_of_deal='".$date_of_deal."',
              deal_cat_name='".$data_arr['deal_cat_name']."',
              deal_subcat1_name='".$data_arr['deal_subcat1_name']."',
              deal_subcat2_name='".$data_arr['deal_subcat2_name']."',
			  email_participating_syndicates='".$email_participants."',
			  added_on='".$date_time_now."',
			  last_edited='".$date_time_now."',
			  admin_verified='y',
			  is_active='y'";
        $result = mysql_query($q);
        if(!$result){
            //echo $q;
            //echo mysql_error();
            return false;
        }
        /////////////////
        //data inserted
        //insert extra data
        $deal_id = mysql_insert_id();
        $new_transaction_id = $deal_id;
		
		/**************
		sng:14/feb/2012
		We now add the deal company in the transaction_companies table
		*****************/
		$q = "insert into ".TP."transaction_companies set
			transaction_id='".$new_transaction_id."',
			company_id='".$data_arr['company_id']."'";
		$result = mysql_query($q);
		if(!$result){
			return false;
			//this is where transaction is required. You could have deleted the old record
		}
		
		$q = "insert into ".TP."transaction_extra_detail set transaction_id = '".$deal_id."',
		date_rumour = '".$data_arr['date_rumour']."',
		date_announced = '".$data_arr['date_announced']."',
		date_closed = '".$data_arr['date_closed']."'";
		
		mysql_query($q);
        
        $validation_passed = true;
        return true;
    }
	/***************
	sng:18/feb/2012
	Used to create deal from simple deal suggestion directly for privileged members
	
	sng:20/feb/2012
	It seems, deal is to be created from suggestions from non privileged members. However, those will be inactive
	and will not appear in search or listing.
	
	This way, others can see the deal and can send correction on it and we can leverage the existing code for deals.
	
	To do that, we add another flag to the argument.
	deal_active
	true, set deal is_active to y else n
	*******************/
	public function front_create_deal_from_simple_suggestion($mem_id,$data,$deal_active,&$deal_created,&$msg){
		global $g_db;
		
		require_once("classes/class.deal_support.php");
		$deal_support = new deal_support();
		/********
		We must validate it here again. Just simple flag
		at least deal_type has to be specified
		date of deal
		value: either the exact value has to be specified or a range has to be specified, even if 'undisclosed'
		companies: at least one
		thing is, even if no company is specified, the companies array is posted, with blank elements
		ditto for banks
		***********/
		$deal_created = false;
		if(!isset($data['deal_cat_name'])||($data['deal_cat_name']=="")){
			$msg = "One or more mandatory information was not specified";
			$deal_created = false;
			return true;
		}
		
		if($data['deal_date']==""){
			$msg = "One or more mandatory information was not specified";
			$deal_created = false;
			return true;
		}
		
		if($data['deal_value']==""){
			//check if range is specified or not
			if(!isset($data['value_range_id'])){
				$msg = "One or more mandatory information was not specified";
				$deal_created = false;
				return true;
			}
		}else{
			//deal value specified
		}
		
		$company_count = count($data['companies']);
		$has_company = false;
		for($company_i=0;$company_i<$company_count;$company_i++){
			if($data['companies'][$company_i]!=""){
				$has_company = true;
				//there is a company so break
				break;
			}
		}
		if(!$has_company){
			$msg = "One or more mandatory information was not specified";
			$deal_created = false;
			return true;
		}
		
		$bank_count = count($data['banks']);
		$has_bank = false;
		for($bank_i=0;$bank_i<$bank_count;$bank_i++){
			if($data['banks'][$bank_i]!=""){
				$has_bank = true;
				//there is a bank so break
				break;
			}
		}
		if(!$has_bank){
			$msg = "One or more mandatory information was not specified";
			$deal_created = false;
			return true;
		}
		/*************************************************************************/
		$date_time_now = date("Y-m-d H:i:s");
		/***********
		see add_deal_simple for explanation on added_on, last_edited
		since admin is not adding this deal, mark it as unverified
		******************/
		$q = "insert into ".TP."transaction set
			date_of_deal='".$data['deal_date']."',
			deal_cat_name='".$data['deal_cat_name']."'";
		if(isset($data['deal_subcat1_name'])&&($data['deal_subcat1_name']!="")){
			$q.=",deal_subcat1_name='".$data['deal_subcat1_name']."'";
		}else{
			$q.=",deal_subcat1_name='n/a'";
		}
		if(isset($data['deal_subcat2_name'])&&($data['deal_subcat2_name']!="")){
			$q.=",deal_subcat2_name='".$data['deal_subcat2_name']."'";
		}else{
			$q.=",deal_subcat2_name='n/a'";
		}
		/******************
		if deal_value is not given, we check for value range id. If that is not given
		treat it as 0, treat value as 0 and if given, store it with deal value of 0
		
		Special case: if deal_value is 0 then we consider value_range_id = 0
		
		if deal_value (in million) is given, we convert it to billion and store it
		we also ignore value_range_id and calculate the range id from value
		**********************/
		if($data['deal_value']==""){
			$q.=",value_in_billion='0.0'";
			
			if(isset($data['value_range_id'])){
				$q.=",value_range_id='".$data['value_range_id']."'";
			}else{
				//no deal value, no value range, treat all as 0
				$q.=",value_range_id='0'";
			}
		}elseif($data['deal_value']<=0.0){
			$q.=",value_in_billion='0.0'";
			//treat the deal as undisclosed
			$q.=",value_range_id='0'";
		}else{
			//deal value given and it is not 0
			//convert to billion
			//ignore what is given as range id and calculate from value
			$value_in_billion = $data['deal_value']/1000;
			$q.=",value_in_billion='".$value_in_billion."'";
			$value_range_id = 0;
			$ok = $deal_support->front_get_value_range_id_from_value($data['deal_value'],$value_range_id);
			if(!$ok){
				return false;
			}
			$q.=",value_range_id='".$value_range_id."'";
		}
		$q.=",added_by_mem_id='".$mem_id."'";
		$q.=",added_on='".$date_time_now."'";
		$q.=",last_edited='".$date_time_now."'";
		$q.=",admin_verified='n'";
		if($deal_active){
			$is_active='y';
		}else{
			$is_active='n';
		}
		$q.=",is_active='".$is_active."'";
		//we handle notification later
		$ok = $g_db->mod_query($q);
		if(!$ok){
			return false;
		}
		/************
		deal data added, now add the extra detail
		************/
		$new_transaction_id = $g_db->last_insert_id();
		
		$q = "insert into ".TP."transaction_extra_detail set transaction_id = '".$new_transaction_id."'";
		if($data['deal_date_type']=="date_announced"){
			$q.=",date_announced='".$data['deal_date']."'";
		}elseif($data['deal_date_type']=="date_completed"){
			$q.=",date_closed='".$data['deal_date']."'";
		}else{
			$q.=",date_announced='".$data['deal_date']."'";
		}
		$ok = $g_db->mod_query($q);
		//we ignore any errors here
		/*****************************
		additional details is stored as a note in transaction_note
		********************************/
		$this->update_note($new_transaction_id,$mem_id,$date_time_now,$data['additional_details']);
		/**********************
		regulatory links are converted to csv and stored in transaction_sources
		even if no regulatory link is specified, the regulatory_links array is posted with blank elements
		
		sng:2/may/2012
		Now we no longer convert source urls to csv. We store each in its own row
		***********/
		require_once("classes/class.transaction_source.php");
		$trans_source = new transaction_source();
		$ok = $trans_source->front_set_sources_for_deal($new_transaction_id,$data['regulatory_links'],$mem_id,$date_time_now);
		
		
		/****************
		companies
		****************/
		require_once("classes/class.company.php");
		$comp = new company();
		/***********
		sng:6/apr/2012
		*************/
		require_once("classes/class.transaction_suggestion.php");
		$trans_suggestion = new transaction_suggestion();
		
		/**********************
		sng:18/apr/2012
		We now use the transaction_company::front_set_participants_for_deal to add the deal participant.
		That method takes care of storing the companies as original suggestion in the transaction_companies_suggestions table
		****************************/
		require_once("classes/class.transaction_company.php");
		$trans_company = new transaction_company();
		
		$ok = $trans_company->front_set_participants_for_deal($new_transaction_id,$data,$mem_id,$date_time_now);
		
		/************************************************
		banks and law firms
		
		sng:18/apr/2012
		We now use the front_set_partners_for_deal
		************/
		$ok = $this->front_set_partners_for_deal($new_transaction_id,$data,$mem_id,$date_time_now);
		
		
		/****************************
		Files
		The files are loaded using ajax before the user submit the form.
		The handler ajax/fileuploader.php store the filenames in table and store the
		ids in session.
		We check for the session variable and update the records
		Since suggestion from both privileged/non-privileged members are stored as a deal record
		and not as suggestion record, we update the transaction_id field.
		*********************************/
		if(isset($_SESSION['suggestion_files_id'])){
			$id_csv = "";
			foreach($_SESSION['suggestion_files_id'] as $file_id){
				$id_csv.= ",'".$file_id."'";
			}
			$id_csv = substr($id_csv,1);
			$suggestion_file_q = "update ".TP."transaction_files SET transaction_id='".$new_transaction_id."' where file_id IN(".$id_csv.")";
			$g_db->mod_query($suggestion_file_q);
			//never mind if this is not a success, remove the ids from session
			unset($_SESSION['suggestion_files_id']);
		}
		
		$deal_created = true;
		$msg = "The deal data has been stored and accepted";
		/**********************
		sng:27/feb/2012
		We need to notify the members whose email has been specified. This can be blank.
		The emails are separated by ','
		There is also that checkbox 'Do not attribute email notification to my account' - not_mine
		**************************/
		global $g_http_path;
		
		if($data['notification_email_list']!=""){
			$email_data = array();
			if(isset($data['not_mine'])){
				$email_data['use_poster_email'] = false;
			}else{
				$email_data['use_poster_email'] = true;
			}
			//we need the member data
			require_once("classes/class.member.php");
			$member = new member();
			$mem_data = NULL;
			$ok = $member->front_get_profile_data($mem_id,$mem_data);
			if($ok){
				$email_data['company_name'] = $mem_data['company_name'];
				$email_data['work_email'] = $_SESSION['work_email'];
				$email_data['member_type'] = $_SESSION['member_type'];
				$email_data['deal_link'] = $g_http_path."/deal_detail.php?deal_id=".$new_transaction_id;
				//deal details
				$email_data['deal_type']=$data['deal_cat_name'];
				if(isset($data['deal_subcat1_name'])&&($data['deal_subcat1_name']!="")){
					$email_data['deal_type'].=", ".$data['deal_subcat1_name'];
				}
				if(isset($data['deal_subcat2_name'])&&($data['deal_subcat2_name']!="")){
					$email_data['deal_type'].=", ".$data['deal_subcat2_name'];
				}
				$email_data['deal_value'] = deal_value_for_display_round_for_deal_id($new_transaction_id);
				$email_data['companies'] = implode(", ",$data['companies']);
				$email_data['banks'] = implode(", ",$data['banks']);
				$email_data['law_firms'] = implode(", ",$data['law_firms']);
				
				require_once("classes/class.mailer.php");
				$mailer = new mailer();
				$subject = "New Deal Notification";
				$email_msg = $mailer->mail_from_template("emailTemplates/simple_deal_creation_user_notification.php",$email_data);
				
				$emails = explode(",",$data['notification_email_list']);
				$mail_cnt = count($emails);
				for($i=0;$i<$mail_cnt;$i++){
					$to = trim($emails[$i]);
					/***********
					sng:1/mar/2012
					clear the prev TO
					************/
					$mailer->clear_recipients();
					//now send
					$mailer->html_mail($to,$subject,$email_msg);
					//noting to do if error
				}
			}
			//no action if not found
		}
		return true;
	}
    ////////////////////////////////////////////////////////////////////////////////////////
    public function member_suggest_deal($mem_id,$data_arr,&$validation_passed,&$err_arr){
        global $g_mc;
        //$company_name = $g_mc->view_to_db($data_arr['name']);
        //validation
        $validation_passed = true;
        if($data_arr['deal_company_name'] == ""){
            $err_arr['deal_company_name'] = "Please specify the company name";
            $validation_passed = false;
        }
        
        if($data_arr['value_in_billion'] == ""){
            $err_arr['value_in_billion'] = "Please specify the deal value";
            $validation_passed = false;
        }
        
        if($data_arr['date_of_deal'] == ""){
            $err_arr['date_of_deal'] = "Please specify the date of deal";
            $validation_passed = false;
        }
        if($data_arr['deal_cat_name'] == ""){
            $err_arr['deal_cat_name'] = "Please specify the deal category";
            $validation_passed = false;
        }
        if($data_arr['deal_subcat1_name'] == ""){
            $err_arr['deal_subcat1_name'] = "Please specify the deal subcategory1";
            $validation_passed = false;
        }
        if($data_arr['deal_subcat2_name'] == ""){
            $err_arr['deal_subcat2_name'] = "Please specify the deal subcategory2";
            $validation_passed = false;
        }
        /****
        sng:1/apr/2010
        we check target country, industry, company only in case of mergers and acquisitions deal
        **/
        if(($data_arr['deal_cat_name']=="M and A")||($data_arr['deal_cat_name']=="M&A")){
            if($data_arr['target_company_id']==""){
                //check if the name is specified or not
                if($data_arr['target_company_name']==""){
                    $err_arr['target_company_name'] = "Please select the target company or specify it's name";
                    $validation_passed = false;
                }
            }
            //else target company selected
            if($data_arr['target_country'] == ""){
                $err_arr['target_country'] = "Please specify the target country";
                $validation_passed = false;
            }
            /***
            sng:22/may/2010
            We accept target sector for m and a deals
            ***/
            if($data_arr['target_sector'] == ""){
                $err_arr['target_sector'] = "Please specify the target sector";
                $validation_passed = false;
            }
        }
        /***
        sng:22/may/2010
        Coupon may be there, may not be there, so no need to check
        *********/
        /*if($data_arr['coupon'] == ""){
            $err_arr['coupon'] = "Please specify the coupon";
            $validation_passed = false;
        }*/
        
        /////////////////////////////////////
        if(!$validation_passed){
            //no need to proceed
            return true;
        }
        ///////////////////////////////////////////////////////
        /****
        sng: 30/mar/2010
        We no longer use domain, we use sector and industry, so we do not use domain any more here
        
        sng:22/may/2010
        We put support for currency, exchange rate, value in local currency, note.
        Also, now we put target sector for M and A in target_sector and do not use target industry
        
        sng:6/aug/2010
        Support for base fee, incentive fee. Both are %value
        ***********/
        
        //insert data
        $id = $mem_id."-".time();
        $q = "insert into ".TP."transaction_suggested set 
            id='".$id."',
              deal_company_name='".$data_arr['deal_company_name']."', 
              suggested_by='".$mem_id."',
              date_suggested='".date("Y-m-d H:i:s")."',
              value_in_billion='".$data_arr['value_in_billion']."',
              currency='".$data_arr['currency']."',
              exchange_rate='".$data_arr['exchange_rate']."',
              value_in_billion_local_currency='".$data_arr['value_in_billion_local_currency']."',
              deal_note='".$data_arr['deal_note']."',
              deal_sources='".$data_arr['deal_sources']."',
              date_of_deal='".$data_arr['date_of_deal']."',
              base_fee='".$data_arr['base_fee']."',
              incentive_fee='".$data_arr['incentive_fee']."',
              deal_cat_name='".$data_arr['deal_cat_name']."',
              deal_subcat1_name='".$data_arr['deal_subcat1_name']."',
              deal_subcat2_name='".$data_arr['deal_subcat2_name']."',
              coupon='".$data_arr['coupon']."',
              maturity_date='".$data_arr['maturity_date']."',
              current_rating='".$data_arr['current_rating']."',
              1_day_price_change='".$data_arr['1_day_price_change']."',
              discount_to_last='".$data_arr['discount_to_last']."',
              discount_to_terp='".$data_arr['discount_to_terp']."',
              target_company_name='".$g_mc->view_to_db($data_arr['target_company_name'])."',
              target_country='".$data_arr['target_country']."',
              target_sector='".$data_arr['target_sector']."',
              seller_company_name='".$g_mc->view_to_db($data_arr['seller_company_name'])."',
              seller_country='".$data_arr['seller_country']."',
              seller_sector='".$data_arr['seller_sector']."',
              ev_ebitda_ltm='".$data_arr['ev_ebitda_ltm']."',
              ev_ebitda_1yr='".$data_arr['ev_ebitda_1yr']."',
              30_days_premia='".$data_arr['30_days_premia']."',";
              
              $bank_q = "";
              for($i=1;$i<=9;$i++){
                  $col_name = "bank".$i;
                $key_name = "bank".$i;
                $bank_q.=$col_name."='".$data_arr[$key_name]."',";
              }
              $q.=$bank_q;
              $law_q = "";
              for($i=1;$i<=9;$i++){
                  $col_name = "law_firm".$i;
                $key_name = "law_firm".$i;
                $law_q.=$col_name."='".$data_arr[$key_name]."'";
                if($i<9){
                    $law_q.=",";
                }
              }
              $q.=$law_q;
        $result = mysql_query($q);
        if(!$result){
            //echo $q;
            echo mysql_error();
            return false;
        }
        /////////////////
        //data inserted
        $validation_passed = true;
        return true;
    }
    ////////////////////////////////////////////////////////////////////////////////////////
    
    /***
    sng:22/may/2010
    we now store sector of the acquired company in target_sector instead of target_industry
    
    sng:3/jun/2010
    We allow admin to change the company doing the deal. Since companies and company reps do not get
    tombstone points, this should not be a problem
    
    We now allow admin to type the deal company name. That opens a hint list and admin select a name
    This set the hidden company_id
    Now, if no name is typed, no company id is set so we first check for name.
    If the name is there, we check if the id is there or not since the company may not
    be in the database
    
    This means, we need to update company_id
    
    sng:30/nov/2010
    Now we reintroduce the field deal country to hold multiple countries for a deal, a csv
    
    sng: 2/dec/2010
    We now use deal_sector, deal_industry to store sector , industry for a deal, a csv
	
	sng: 7/apr/2011
	I am not sure whether target sector and seller sector is used or not. I have put support for it in the front end, so
	let us add the support here
	
	sng: 17/jun/2011
	Do not use this. Now the steps are replicated in admin/edit_deal_data.php
    ******/
    public function edit_deal($deal_id,$data_arr,&$validation_passed,&$err_arr){
		$validation_passed = false;
		return;
		
        global $g_mc;
        
        $validation_passed = true;
        
        if($data_arr['deal_company_name'] == ""){
            $err_arr['company_id'] = "Please specify the company name";
            $validation_passed = false;
        }else{
            if($data_arr['company_id'] == ""){
                $err_arr['company_id'] = "The company was not found. Create it first.";
                $validation_passed = false;
            }
        }
        
        if($data_arr['value_in_billion'] == ""){
            $err_arr['value_in_billion'] = "Please specify the deal value";
            $validation_passed = false;
        }
        
        if($data_arr['date_of_deal'] == ""){
            $err_arr['date_of_deal'] = "Please specify the date of deal";
            $validation_passed = false;
        }
        if($data_arr['deal_cat_name'] == ""){
            $err_arr['deal_cat_name'] = "Please specify the deal category";
            $validation_passed = false;
        }
        if($data_arr['deal_subcat1_name'] == ""){
            $err_arr['deal_subcat1_name'] = "Please specify the deal subcategory1";
            $validation_passed = false;
        }
        if($data_arr['deal_subcat2_name'] == ""){
            $err_arr['deal_subcat2_name'] = "Please specify the deal subcategory2";
            $validation_passed = false;
        }
        /****
        sng:1/apr/2010
        we check target country, industry, company only in case of mergers and acquisitions deal
        **/
        if(($data_arr['deal_cat_name']=="M and A")||($data_arr['deal_cat_name']=="M&A")){
            if($data_arr['target_company_id']==""){
                //check if the name is specified or not
                if($data_arr['target_company_name']==""){
                    $err_arr['target_company_name'] = "Please select the target company or specify it's name";
                    $validation_passed = false;
                }
            }
            if($data_arr['target_country'] == ""){
                $err_arr['target_country'] = "Please specify the target country";
                $validation_passed = false;
            }
            if($data_arr['target_sector'] == ""){
                $err_arr['target_sector'] = "Please specify the target sector";
                $validation_passed = false;
            }
        }
        /***
        sng:18/may/2010
        coupon may be there or may not be there, so no need to validate
        *******/
        /******
        if($data_arr['coupon'] == ""){
            $err_arr['coupon'] = "Please specify the coupon";
            $validation_passed = false;
        }
        **********/
        /////////////////////////////////////
        if(!$validation_passed){
            //no need to proceed
            return true;
        }
        $newLogos = array();
        if (is_array($_SESSION['logos'])) {
            $defaultSet = false;
           foreach ($_SESSION['logos'] as $key=>$logo) {
               if ($logo['default']) {
                  $defaultSet = true; 
               }
           }
           if (!$defaultSet) {
               foreach ($_SESSION['logos'] as $key=>$logo) {
                  $_SESSION['logos'][$key]['default'] = true;
                  break; 
               }
           } 
                       
           foreach ($_SESSION['logos'] as $key=>$logo) {
               $newLogos[] = array('fileName'=>$logo['fileName'], 'default'=>$logo['default']);
           }     
        }
        ///////////////////////////////////////////////////////
        //update data
        $q = "update ".TP."transaction set
                company_id='".$data_arr['company_id']."',
              value_in_billion='".$data_arr['value_in_billion']."',
              deal_country='".$data_arr['deal_country']."',
              deal_sector='".$data_arr['deal_sector']."',
              deal_industry='".$data_arr['deal_industry']."',
              currency='".$data_arr['currency']."',
              exchange_rate='".$data_arr['exchange_rate']."',
              value_in_billion_local_currency='".$data_arr['value_in_billion_local_currency']."',
              date_of_deal='".$data_arr['date_of_deal']."',
              base_fee='".$data_arr['base_fee']."',
              incentive_fee='".$data_arr['incentive_fee']."',
              deal_cat_name='".$data_arr['deal_cat_name']."',
              deal_subcat1_name='".$data_arr['deal_subcat1_name']."',
              deal_subcat2_name='".$data_arr['deal_subcat2_name']."',
              coupon='".$data_arr['coupon']."',
              maturity_date='".$data_arr['maturity_date']."',
              current_rating='".$data_arr['current_rating']."',
              1_day_price_change='".$data_arr['1_day_price_change']."',
              discount_to_last='".$data_arr['discount_to_last']."',
              discount_to_terp='".$data_arr['discount_to_terp']."',
              target_company_id='".$data_arr['target_company_id']."',
              target_company_name='".$g_mc->view_to_db($data_arr['target_company_name'])."',
              target_country='".$data_arr['target_country']."',
              target_sector='".$data_arr['target_sector']."',
			  target_industry='".$data_arr['target_industry']."',
              seller_company_name='".$g_mc->view_to_db($data_arr['seller_company_name'])."',
              seller_country='".$data_arr['seller_country']."',
              seller_sector='".$data_arr['seller_sector']."',
			  seller_industry='".$data_arr['seller_industry']."',
              ev_ebitda_ltm='".$data_arr['ev_ebitda_ltm']."',
              ev_ebitda_1yr='".$data_arr['ev_ebitda_1yr']."',
              30_days_premia='".$data_arr['30_days_premia']."',
              logos='".serialize($newLogos)."'
              where id='".$deal_id."'";
              
        $result = mysql_query($q);
        if(!$result){
            //echo mysql_error();
            return false;
        }
        /////////////////
        //data inserted
        /********************************************
        sng:21/may/2010
        try to update the note
        **********************************/
        $this->update_note($deal_id,$data_arr['note']);
        //never mind if there is error, this is not that important
        /*******************
        sng:8/jul/2010
        try to update the sources
        ****************/
        $this->update_sources($deal_id,$data_arr['sources']);
        //never mind if there is error, this is not that important
        /**************************************************
        sng:15/5/2010
        If the deal value is changed, update the adjusted values
        **********/
        if($data_arr['value_in_billion']!=$data_arr['current_value_in_billion']){
            $success = $this->update_adjusted_values_for_deal($deal_id);
            if(!$success){
                return false;
            }
        }
        $validation_passed = true;
        return true;
    }
    public function update_adjusted_values_for_deal($deal_id){
        //update adjusted values for all the partners of type bank
        //for that we need the value of the deal and number of partners for this type
        $deal_q = "select value_in_billion from ".TP."transaction where id='".$deal_id."'";
        $deal_q_res = mysql_query($deal_q);
        if(!$deal_q_res){
            return false;
        }
        $deal_q_res_row = mysql_fetch_assoc($deal_q_res);
        $value_in_billion = $deal_q_res_row['value_in_billion'];
        //now we need the partners of this type for this deal
        $partner_q = "select partner_id from ".TP."transaction_partners where transaction_id='".$deal_id."' and partner_type='bank'";
        $partner_q_res = mysql_query($partner_q);
        if(!$partner_q_res){
            return false;
        }
        $num_partners = mysql_num_rows($partner_q_res);
        if($num_partners > 0){
            $partner_adjusted_value_in_billion = $value_in_billion/$num_partners;
            //update all the partners for this deal for this type
            $partner_update_q = "update ".TP."transaction_partners set adjusted_value_in_billion='".$partner_adjusted_value_in_billion."' where transaction_id='".$deal_id."' and partner_type='bank'";
            $result = mysql_query($partner_update_q);
            if(!$result){
                return false;
            }
            //now that the partners are updated, we need to update the team members for these partners for this deal
            while($partner_q_res_row = mysql_fetch_assoc($partner_q_res)){
                $deal_partner_id = $partner_q_res_row['partner_id'];
                $success = $this->update_deal_team_members_adjusted_value($deal_id,$deal_partner_id);
                if(!$success){
                    return false;
                }
            }
        }
        /////////now law firm
        $partner_q = "select partner_id from ".TP."transaction_partners where transaction_id='".$deal_id."' and partner_type='law firm'";
        $partner_q_res = mysql_query($partner_q);
        if(!$partner_q_res){
            return false;
        }
        $num_partners = mysql_num_rows($partner_q_res);
        if($num_partners > 0){
            $partner_adjusted_value_in_billion = $value_in_billion/$num_partners;
            //update all the partners for this deal for this type
            $partner_update_q = "update ".TP."transaction_partners set adjusted_value_in_billion='".$partner_adjusted_value_in_billion."' where transaction_id='".$deal_id."' and partner_type='law firm'";
            $result = mysql_query($partner_update_q);
            if(!$result){
                return false;
            }
            //now that the partners are updated, we need to update the team members for these partners for this deal
            while($partner_q_res_row = mysql_fetch_assoc($partner_q_res)){
                $deal_partner_id = $partner_q_res_row['partner_id'];
                $success = $this->update_deal_team_members_adjusted_value($deal_id,$deal_partner_id);
                if(!$success){
                    return false;
                }
            }
        }
        //////////////////////
        return true;
    }
    /////////////////////////////////////////////////////Transaction Partner////////////////////////////////////////////////////
	/***********
	sng:9/mar/2012
	add one or more banks and law firms from front end
	
	sng:16/mar/2012
	we now have another checkbox, to mark if a bank is insignificant
	and a dropdown, role
	
	sng:10/apr/2012
	We add two more arguments to support the provision that we need to log the addition of banks and law firms
	****************/
	public function front_set_partners_for_deal($deal_id,$data,$suggestion_mem_id,$deal_add_date_time){
		require_once("classes/class.company.php");
		$comp = new company();
		/**************
		bank /law firm names and whether sellside advisor
		["banks"]=> array(4) { 
		[0]=> string(4) "Citi"  (bank 1)
		[1]=> string(8) "JPMorgan" (bank 2 sellside)
		[2]=> string(11) "BNP Paribas" (bank 3)
		[3]=> string(13) "Credit Suisse" (bank 4 sellside)
		} 
		["sellside_advisors_2"]=> string(2) "on" 
		["sellside_advisors_4"]=> string(2) "on"
		
		['bank_is_insignificant_2"]=>string(2)	"on"
		['bank_role_id_1']	6
		['bank_role_id_2']	0 (no option selected)
		['bank_role_id_3']	0
		
		["law_firms"]=> array(4) { 
		[0]=> string(20) "Mello Jones & Martin" (firm 1)
		[1]=> string(0) "" (firm 2)
		[2]=> string(0) "" (firm 3)
		[3]=> string(11) "Bredin Prat" (firm 4 sellside)
		} 
		["law_sellside_advisors_4"]=> string(2) "on"
		************************************************/
		/*************
		sng:6/apr/2012
		************/
		$suggestion_data_arr = array();
		
		$add_bank_validation_passed = false;
		$err_arr = array();
		$bank_count = count($data['banks']);
		$bank_found = false;
		$bank_id = 0;
		for($bank_i=0;$bank_i<$bank_count;$bank_i++){
			//name can be blank so
			if($data['banks'][$bank_i]!=""){
				$ok = company_id_from_name($data['banks'][$bank_i],'bank',$bank_id,$bank_found);
				if(!$ok){
					continue;
				}else{
					if(!$bank_found){
						//create it
						$ok = $comp->front_quick_create_company_blf($suggestion_mem_id,$data['banks'][$bank_i],'bank',$bank_id);
						if(!$ok){
							continue;
						}
					}
					//we have bank
					/****************************
					sng:14/mar/2012
					we need to check for sellside advisor flag
					for bank[0] it will be sellside_advisors_1: on
					it may or may not be there
					
					sng:23/mar/2012
					We no longer need this sellside, since we now have role like 'Advisor, Sellside'
					
					sng:16/mar/2012
					ditto for bank[1] bank_is_insignificant_2: on
					it may or may not be there
					
					sng:23/mar/2012
					We no longer need this insignificant since we now have role like 'Junior Advisor'
					
					for bank[0] bank_role_id_1: is non zero if the corresponding dropdown is selected
					*************************/
					/****************
					sng:23/mar/2012
					We now have role like 'Advisor, Sellside', so we no longer require the
					sellside flag. We have removed that from the detailed deal submission
					*********************/
					/******************
					sng:23/mar/2012
					We now have role like 'Junior Advisor, so we no longer use the checkbox 'Not lead advisor'
					so let us remove the 'bank_is_insignificant_'
					*************************/
					
					
					$record_arr = array();
					$record_arr['firm_name'] = $data['banks'][$bank_i];
					$record_arr['partner_id'] = $bank_id;
					$record_arr['transaction_id'] = $deal_id;
					/***********
					we no longer use is_sellside
					We no longer use is_insignificant
					*************/
					$record_arr['role_id'] = $data['bank_role_id_'.($bank_i+1)];
					$this->add_partner($record_arr,'bank',$add_bank_validation_passed,$err_arr);
					/********************************************
					sng:6/apr/2012
					if the partners are added, we keep a record in the suggestion table.
					This is part of suggestion tracking where we need to know the original partners submitted with the deal and their roles.
					Just prepare the array, do not add yet
					***************/
					if($add_bank_validation_passed){
						$suggestion_data_arr[] = array('partner_name'=>$data['banks'][$bank_i],'partner_type'=>'bank','role_id'=>$record_arr['role_id']);
					}
					/*****************************************************/
				}
			}
		}
		/*********************************************************/
		$add_law_firm_validation_passed = false;
		$err_arr = array();
		$law_firm_count = count($data['law_firms']);
		$law_firm_found = false;
		$law_firm_id = 0;
		for($law_firm_i=0;$law_firm_i<$law_firm_count;$law_firm_i++){
			if($data['law_firms'][$law_firm_i]!=""){
				
				
				$ok = company_id_from_name($data['law_firms'][$law_firm_i],'law firm',$law_firm_id,$law_firm_found);
				if(!$ok){
					continue;
				}else{
					if(!$law_firm_found){
						//create it
						$ok = $comp->front_quick_create_company_blf($suggestion_mem_id,$data['law_firms'][$law_firm_i],'law firm',$law_firm_id);
						if(!$ok){
							continue;
						}
					}
					/****************
					sng:23/mar/2012
					We now have role like 'Advisor, Sellside', so we no longer require the
					sellside flag. We have removed that from the detailed deal submission
					*********************/
					
					$record_arr = array();
					$record_arr['firm_name'] = $data['law_firms'][$law_firm_i];
					$record_arr['partner_id'] = $law_firm_id;
					$record_arr['transaction_id'] = $deal_id;
					/******
					we no longer use sellside advisor checkbox
					*********/
					$record_arr['role_id'] = $data['law_firm_role_id_'.($law_firm_i+1)];
					$this->add_partner($record_arr,'law firm',$add_law_firm_validation_passed,$err_arr);
					/********************************************
					sng:6/apr/2012
					if the partners are added, we keep a record in the suggestion table.
					This is part of suggestion tracking where we need to know the original partners submitted with the deal and their roles
					***************/
					if($add_law_firm_validation_passed){
						$suggestion_data_arr[] = array('partner_name'=>$data['law_firms'][$law_firm_i],'partner_type'=>'law firm','role_id'=>$record_arr['role_id']);
					}
					/********************************/
				}
			}
		}
		/***********
		6/apr/2012
		**********/
		require_once("classes/class.transaction_suggestion.php");
		$trans_suggestion = new transaction_suggestion();
		$trans_suggestion->partners_added_via_deal_submission($deal_id,$suggestion_mem_id,$deal_add_date_time,$suggestion_data_arr);
		/************************************************************/
		return true;
	}
	
    /*****
    sng:26/Oct/2010
    ordering the entries by the firm name
    ***/
    public function get_all_partner($transaction_id,$type,&$data_arr,&$data_count){
        global $g_mc;
        /***
        sng:13/apr/2010
        This code is used in lots of places, so i cannot change c.* as well as company name
        */
        $q = "select t.*,c.*,c.name as company_name from ".TP."transaction_partners as t left join ".TP."company as c on(t.partner_id=c.company_id) where t.transaction_id='".$transaction_id."' AND t.partner_type='".$type."' order by company_name";
        if (isset($_REQUEST['debug']))
            echo $q . PHP_EOL;
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        ///////////////////////////
        $data_count = mysql_num_rows($res);
        if(0 == $data_count){
            //no data to return so
            return true;
        }
        /////////////////
        for($i=0;$i<$data_count;$i++){
            $data_arr[$i] = mysql_fetch_assoc($res);
            /***
            sng:13/apr/2010
            magic quoted company name
            **/
            $data_arr[$i]['company_name'] = $g_mc->db_to_view($data_arr[$i]['company_name']);
            $data_arr[$i]['name'] = $g_mc->db_to_view($data_arr[$i]['name']);
        }
        return true;
    }
    
    /*****
    sng:27/oct/2010
    We need a list of banks / law firms which are duplicated for the given deal, order by firm name
    *********/
    public function get_all_duplicate_partners($transaction_id,$type,&$data_arr,&$data_count){
        global $g_mc;
        
        $q = "select t.id,t.transaction_id,t.partner_id,t.partner_type,count(partner_id) as partner_cnt,c.name as company_name from ".TP."transaction_partners as t left join ".TP."company as c on(t.partner_id=c.company_id) where t.transaction_id='".$transaction_id."' AND t.partner_type='".$type."' group by transaction_id,partner_id having partner_cnt>1 order by company_name";
        
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        ///////////////////////////
        $data_count = mysql_num_rows($res);
        if(0 == $data_count){
            //no data to return so
            return true;
        }
        /////////////////
        for($i=0;$i<$data_count;$i++){
            $data_arr[$i] = mysql_fetch_assoc($res);
            $data_arr[$i]['company_name'] = $g_mc->db_to_view($data_arr[$i]['company_name']);
        }
        return true;
    }
    
    /****
    sng:12/may/2010
    When we add a partner bank/law firm, we need to update the adjusted deal value for the banks or law firms
    for that deal
    
    sng:20/may/2010
    Now admin type the bank / law firm name and hint appears and admin select a firm
    This sets an id. So if id is not sent, it either means no firm is selected or something is typed
    which does not exists
	
	sng:17/jun/2011
	Now we send the is_sellside_advisor flag. If it is there and is y, we set the flag to y else the flag is n
	
	sng:15/sep/2011
	We now notify the interested persons of the firm that their firm has been added to this deal
    ****/
    public function add_partner($data_arr,$type,&$validation_passed,&$err_arr){
        global $g_mc;
        
        
        //$company_name = $g_mc->view_to_db($data_arr['name']);
        //validation
        $validation_passed = true;
        //first check if name is sent or not
        if($data_arr['firm_name']==""){
            $err_arr['partner_id'] = "Please specify the ".$type." name";
            $validation_passed = false;
            return true;
        }
        //if it comes here, something was typed
        if($data_arr['partner_id'] == ""){
            $err_arr['partner_id'] = "The ".$type." name was not found";
            $validation_passed = false;
        }else{
            //partner of this id and type cannot be added to this deal twice, so check
            $q = "select count(id) as cnt from ".TP."transaction_partners where transaction_id='".$data_arr['transaction_id']."' and partner_id='".$data_arr['partner_id']."' and partner_type='".$type."'";
            $res = mysql_query($q);
            if(!$res){
                return false;
            }
            $row = mysql_fetch_assoc($res);
            if($row['cnt']!=0){
                //this partner of this type is already in this transaction, so
                $err_arr['partner_id'] = "This has already been added";
                $validation_passed = false;
            }
        }
        
        /////////////////////////////////////
        if(!$validation_passed){
            //no need to proceed
            return true;
        }
        ///////////////////////////////////////////////////////
        
        //insert data
		/*************
		sng:23/mar/2012
		We no longer use this feature but let's keep the code anyway
		
		$is_sellside_advisor = 'n';
		if(isset($data_arr['is_sellside_advisor'])&&$data_arr['is_sellside_advisor']=='y'){
			$is_sellside_advisor = 'y';
		}
		**************/
		
		/***************
		sng:16/mar/2012
		Now we check for is_insignificant
		and also set role id
		
		sng:23/mar/2012
		Now we have role like 'Junior Adviser' We no longer need the is_insignificant
		checkbox. Problem is , that flag is related to adjusted tombstone value for the partner.
		We keep this code here for now.
		******************/
		$is_insignificant = 'n';
		if(isset($data_arr['is_insignificant'])&&$data_arr['is_insignificant']=='y'){
			$is_insignificant = 'y';
		}
		$role_id = 0;
		if(isset($data_arr['role_id'])){
			$role_id = $data_arr['role_id'];
		}
		
        /********************************************************************
		sng:23/mar/2012
		We no longer use this feature but let's keep the code anyway
		$q = "insert into ".TP."transaction_partners set 
              partner_id='".$data_arr['partner_id']."',
			  is_sellside_advisor='".$is_sellside_advisor."',
			  is_insignificant='".$is_insignificant."',
			  role_id='".$role_id."',
              transaction_id='".$data_arr['transaction_id']."', 
              partner_type='".$type."'";
		*********************************************************************/
		
		$q = "insert into ".TP."transaction_partners set 
              partner_id='".$data_arr['partner_id']."',
			  is_insignificant='".$is_insignificant."',
			  role_id='".$role_id."',
              transaction_id='".$data_arr['transaction_id']."', 
              partner_type='".$type."'";
			  
        $result = mysql_query($q);
        if(!$result){
            //echo mysql_error();
            return false;
        }
        /////////////////
        //data inserted, update adjusted values for all the partners of same type
        //for that we need the value of the deal and number of partners for this type
        $deal_q = "select value_in_billion from ".TP."transaction where id='".$data_arr['transaction_id']."'";
        $deal_q_res = mysql_query($deal_q);
        if(!$deal_q_res){
            return false;
        }
        $deal_q_res_row = mysql_fetch_assoc($deal_q_res);
        $value_in_billion = $deal_q_res_row['value_in_billion'];
        //now we need the partners of this type for this deal
        $partner_q = "select partner_id from ".TP."transaction_partners where transaction_id='".$data_arr['transaction_id']."' and partner_type='".$type."'";
        $partner_q_res = mysql_query($partner_q);
        if(!$partner_q_res){
            return false;
        }
        $num_partners = mysql_num_rows($partner_q_res);
        if($num_partners > 0){
            $partner_adjusted_value_in_billion = $value_in_billion/$num_partners;
            //update all the partners for this deal for this type
            $partner_update_q = "update ".TP."transaction_partners set adjusted_value_in_billion='".$partner_adjusted_value_in_billion."' where transaction_id='".$data_arr['transaction_id']."' and partner_type='".$type."'";
            $result = mysql_query($partner_update_q);
            if(!$result){
                return false;
            }
            //now that the partners are updated, we need to update the team members for these partners for this deal
            while($partner_q_res_row = mysql_fetch_assoc($partner_q_res)){
                $deal_partner_id = $partner_q_res_row['partner_id'];
                $success = $this->update_deal_team_members_adjusted_value($data_arr['transaction_id'],$deal_partner_id);
                if(!$success){
                    return false;
                }
            }
        }
        $validation_passed = true;
		/*****************************************************
		sng:15/sep/2011
		*********/
		require_once("classes/class.deal_support.php");
		$support = new deal_support();
		$support->notify_participants($data_arr['partner_id'],$data_arr['transaction_id']);
		//since this is just a notification, never mind if this is success or failure
		/********************************************************/
        return true;
    }
    
    /****
    remove a bank or law firm from a deal. If the partner is removed, the adjusted value for all the partners
    of same type for that deal has to be recomputed.
    After that, we have to delete all the members associated with that partner for that deal and then
    recompute the adjusted value for all the members associated with the deal since the adjusted value
    of their firm change
    *****/
    public function remove_partner($data_arr,$type,&$msg){
        
        if($data_arr['partner_id'] == ""){
            $msg = "Please select a ".$type;
            return true;
        }
        //check if this partner id of this type is there in the deal or not
        $q = "select count(*) as cnt from ".TP."transaction_partners where transaction_id='".$data_arr['transaction_id']."' and partner_id='".$data_arr['partner_id']."' and partner_type='".$type."'";
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        $row = mysql_fetch_assoc($res);
        if($row['cnt']==0){
            $msg = "This ".type." is not associated with the transaction";
            return true;
        }
        //first delete the members associated with this bank for this deal
        $q = "delete from ".TP."transaction_partner_members where transaction_id='".$data_arr['transaction_id']."' and partner_id='".$data_arr['partner_id']."'";
        $result = mysql_query($q);
        if(!$result){
            //echo mysql_error();
            return false;
        }
        //we just removed the whole team, so no need to update adjusted values yet
        
        //now remove this bank from the deal 
        $q = "delete from ".TP."transaction_partners where transaction_id='".$data_arr['transaction_id']."' and partner_id='".$data_arr['partner_id']."' and partner_type='".$type."'";
        $result = mysql_query($q);
        if(!$result){
            //echo mysql_error();
            return false;
        }
        //data deleted, update adjusted values for all the partners of same type
        //for that we need the value of the deal and number of partners for this type

        $deal_q = "select value_in_billion from ".TP."transaction where id='".$data_arr['transaction_id']."'";
        $deal_q_res = mysql_query($deal_q);
        if(!$deal_q_res){
            return false;
        }
        $deal_q_res_row = mysql_fetch_assoc($deal_q_res);
        $value_in_billion = $deal_q_res_row['value_in_billion'];
        //now we need the partners of this type for this deal
        $partner_q = "select partner_id from ".TP."transaction_partners where transaction_id='".$data_arr['transaction_id']."' and partner_type='".$type."'";
        $partner_q_res = mysql_query($partner_q);
        if(!$partner_q_res){
            return false;
        }
        $num_partners = mysql_num_rows($partner_q_res);
        if($num_partners > 0){
            $partner_adjusted_value_in_billion = $value_in_billion/$num_partners;
            //update all the partners for this deal for this type
            $partner_update_q = "update ".TP."transaction_partners set adjusted_value_in_billion='".$partner_adjusted_value_in_billion."' where transaction_id='".$data_arr['transaction_id']."' and partner_type='".$type."'";
            $result = mysql_query($partner_update_q);
            if(!$result){
                return false;
            }
            //now that the partners are updated, we need to update the team members for these partners for this deal
            while($partner_q_res_row = mysql_fetch_assoc($partner_q_res)){
                $deal_partner_id = $partner_q_res_row['partner_id'];
                $success = $this->update_deal_team_members_adjusted_value($data_arr['transaction_id'],$deal_partner_id);
                if(!$success){
                    return false;
                }
            }
        }
        return true;
    }
    
	/*****************************
	sng:17/jun/2011
	function to set sellside flag
	***************************/
	public function partner_sellside_flag($data_arr,$type,&$msg){
		$db = new db();
		//first check if is_sellside_advisor is there and is y. If so, set the flag to y else set the flag to n
		$flag = 'n';
		if(isset($data_arr['is_sellside_advisor'])&&$data_arr['is_sellside_advisor']=='y'){
			$flag = 'y';
		}
		$q = "update ".TP."transaction_partners set is_sellside_advisor='".$flag."' where transaction_id='".$data_arr['transaction_id']."' and partner_id='".$data_arr['partner_id']."' and partner_type='".$type."'";
		$result = $db->mod_query($q);
		if($result){
			if($db->has_row()){
				//record updated
				$msg = "updated";
			}
		}
		return $result;
	}
	/*****************************
	sng:27/sep/2011
	function to set is_insignificant flag
	***************************/
	public function partner_is_insignificant_flag($data_arr,$type,&$msg){
		$db = new db();
		//first check if is_insignificant is there and is y. If so, set the flag to y else set the flag to n
		$flag = 'n';
		if(isset($data_arr['is_insignificant'])&&$data_arr['is_insignificant']=='y'){
			$flag = 'y';
		}
		$q = "update ".TP."transaction_partners set is_insignificant='".$flag."' where transaction_id='".$data_arr['transaction_id']."' and partner_id='".$data_arr['partner_id']."' and partner_type='".$type."'";
		$result = $db->mod_query($q);
		if($result){
			if($db->has_row()){
				//record updated
				$msg = "updated";
			}
		}
		return $result;
	}
    /****
    sng:27/oct/2010
    remove a bank or law firm (which has been duplicated) from a deal.
    Since we are changing the number of the banks / law firms, we need to compute the adjusted value for the
    remaining banks / law firms
    We do not have to remove bankers / lawyers with the deal for this bank / law firm because we are merely removing the
    duplicate tuple (transaction_id, partner_id), keeping the first one (and in the tombstone_transaction_partner_members
    the bankers / lawyers are associated with transaction_id, partner_id so no fear of deletion of record but keeping a ghost ponter in another table.
    Then recompute the adjusted value for all the members associated with the deal since the adjusted value
    of their firm change
    
    This code takes code from remove_partner ,the updation of adjusted values
    *****/
    public function remove_duplicate_partner($data_arr,$type,&$msg){
        
        if($data_arr['partner_id'] == ""){
            $msg = "Please select a ".$type;
            return true;
        }
        //check if this partner id of this type is there in the deal or not
        $q = "select count(*) as cnt from ".TP."transaction_partners where transaction_id='".$data_arr['transaction_id']."' and partner_id='".$data_arr['partner_id']."' and partner_type='".$type."'";
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        $row = mysql_fetch_assoc($res);
        if($row['cnt']==0){
            $msg = "This ".type." is not associated with the transaction";
            return true;
        }
        //also check that if this is duplicated or not. If only one instance found then not a duplicate
        if(1==$row['cnt']){
            $msg = "This ".type." is not duplicated for the transaction";
            return true;
        }
        //we are removing the duplicate (transaction_id, partner_id). One instance of (transaction_id, partner_id) still remains, 
        //so no need to delete from the tombstone_transaction_partner_members
        
        //get the first instance of the tuple (transaction_id, partner_id)
        $q = "select id from ".TP."transaction_partners where transaction_id='".$data_arr['transaction_id']."' and partner_id='".$data_arr['partner_id']."' and partner_type='".$type."' order by id";
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        //the first row
        //we already checked that there is data
        $row = mysql_fetch_assoc($res);
        $id_to_keep = $row['id'];
        //now delete the rest of (transaction_id, partner_id), making sure that you keep at least one instance. Delete instances with id different from
        //the id_to_keep
        $q = "delete from ".TP."transaction_partners where transaction_id='".$data_arr['transaction_id']."' and partner_id='".$data_arr['partner_id']."' and partner_type='".$type."' and id!='".$id_to_keep."'";
        
        $result = mysql_query($q);
        if(!$result){
            //echo mysql_error();
            return false;
        }
        //data deleted, update adjusted values for all the partners of same type for this transaction
        //for that we need the value of the deal and number of partners for this type
        $deal_q = "select value_in_billion from ".TP."transaction where id='".$data_arr['transaction_id']."'";
        $deal_q_res = mysql_query($deal_q);
        if(!$deal_q_res){
            return false;
        }
        $deal_q_res_row = mysql_fetch_assoc($deal_q_res);
        $value_in_billion = $deal_q_res_row['value_in_billion'];
        //now we need the partners of this type for this deal
        $partner_q = "select partner_id from ".TP."transaction_partners where transaction_id='".$data_arr['transaction_id']."' and partner_type='".$type."'";
        $partner_q_res = mysql_query($partner_q);
        if(!$partner_q_res){
            return false;
        }
        $num_partners = mysql_num_rows($partner_q_res);
        if($num_partners > 0){
            $partner_adjusted_value_in_billion = $value_in_billion/$num_partners;
            //update all the partners for this deal for this type
            $partner_update_q = "update ".TP."transaction_partners set adjusted_value_in_billion='".$partner_adjusted_value_in_billion."' where transaction_id='".$data_arr['transaction_id']."' and partner_type='".$type."'";
            $result = mysql_query($partner_update_q);
            if(!$result){
                return false;
            }
            //now that the partners are updated, we need to update the team members for these partners for this deal
            while($partner_q_res_row = mysql_fetch_assoc($partner_q_res)){
                $deal_partner_id = $partner_q_res_row['partner_id'];
                $success = $this->update_deal_team_members_adjusted_value($data_arr['transaction_id'],$deal_partner_id);
                if(!$success){
                    return false;
                }
            }
        }
        return true;
    }
    /////////////////////////////////////////////////////Transaction Partner////////////////////////////////////////////////////
    /****************
	sng:3/feb/2012
	We now have multiple companies associated with a deal. We no longer use the company_id of the transaction table
	We join with transaction_companies where company_id in transaction_companies is used to filter
	
	sng:2/mar/2012
	Do not include inactive deals
	*****************/
    public function front_get_recent_transactions($company_id,$num_to_fetch,&$deal_data_arr,&$deal_data_count){
        global $g_mc;
        
        $q = "select t.*,vrm.short_caption as fuzzy_value_short_caption,vrm.display_text as fuzzy_value from ".TP."transaction_companies as trc left join ".TP."transaction as t on(trc.transaction_id=t.id) left join ".TP."transaction_value_range_master as vrm on (t.value_range_id=vrm.value_range_id) where t.is_active='y' AND trc.company_id='".$company_id."' order by date_of_deal desc limit 0,".$num_to_fetch;
        $res = mysql_query($q);
        if(!$res){
			//echo mysql_error();
            return false;
        }
        //////////////////////////////////////////////////
        $deal_data_count = mysql_num_rows($res);
        if(0==$deal_data_count){
            return true;
        }
        //////////////////////////////////
        for($i=0;$i<$deal_data_count;$i++){
            $deal_data_arr[$i] = mysql_fetch_assoc($res);
            //set bankers and law firms
            $transaction_id = $deal_data_arr[$i]['id'];
            $deal_data_arr[$i]['banks'] = array();
            $data_cnt = 0;
            $success = $this->get_all_partner($transaction_id,"bank",$deal_data_arr[$i]['banks'],$data_cnt);
            if(!$success){
                return false;
            }
            ///////////////////////////
            $deal_data_arr[$i]['law_firms'] = array();
            $data_cnt = 0;
            $success = $this->get_all_partner($transaction_id,"law firm",$deal_data_arr[$i]['law_firms'],$data_cnt);
            if(!$success){
                return false;
            }
            
            $deal_data_arr[$i]['target_company_name'] = $g_mc->db_to_view($deal_data_arr[$i]['target_company_name']);
        }
        return true;
    }
    
    /***
    sng:13/apr/2010
    This is used for home page of company rep member. In that home page, we show the most recent deal done by a rival company.
    We accept the rival company id and get the deal data.
    This is ONLY to get deal data from companies
    ********/
    public function front_home_get_last_deal_data_of_company($company_id,&$deal_data_arr,&$deal_found){
		return false;
        /***************
		sng:2/mar/2012
		Not used
		***************/
    }
    
    public function front_get_random_deal_data(&$deal_data_arr,&$deal_found){
		return false;
        global $g_mc;
        //first get the random transaction id
        //otherwise a join query and order by rand takes lots of time
		/****************
		sng:2/mar/2012
		exclude inactive deals
		******************/
        $q = "SELECT id FROM ".TP."transaction where is_active='y' ORDER BY rand( ) LIMIT 0 , 1";
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        $deal_data_count = mysql_num_rows($res);
        if(0==$deal_data_count){
            $deal_found = false;
            return true;
        }
        /////////////////////
        $row = mysql_fetch_assoc($res);
        $trans_id = $row['id'];
        ///////////////////////////////////
        //now get the detail of this
        /******
        sng:8/oct/2010
        We also need subtype and sub sub type
        *****/
        $q = "select t.id as deal_id,t.company_id,value_in_billion,date_of_deal,deal_cat_name,deal_subcat1_name,deal_subcat2_name,c.name as company_name,c.hq_country,c.industry from ".TP."transaction as t left join ".TP."company as c on(t.company_id=c.company_id) where t.id='".$trans_id."'";
        
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        $deal_data_count = mysql_num_rows($res);
        if(0==$deal_data_count){
            $deal_found = false;
            return true;
        }
        //////////////////////////////////
        $row = mysql_fetch_assoc($res);
        $deal_found = true;
        
        //deal data
        $deal_data_arr['deal_id'] = $row['deal_id'];
        $deal_data_arr['value_in_billion'] = $row['value_in_billion'];
        $deal_data_arr['date_of_deal'] = $row['date_of_deal'];
        $deal_data_arr['deal_cat_name'] = $row['deal_cat_name'];
        $deal_data_arr['deal_subcat1_name'] = $row['deal_subcat1_name'];
        $deal_data_arr['deal_subcat2_name'] = $row['deal_subcat2_name'];
        //company data
        $deal_data_arr['company_name'] = $g_mc->db_to_view($row['company_name']);
        $deal_data_arr['company_id'] = $row['company_id'];
        $deal_data_arr['hq_country'] = $row['hq_country'];
        if($deal_data_arr['hq_country']=="") $deal_data_arr['hq_country'] = "hq unknown";
        $deal_data_arr['industry'] = $row['industry'];
        if($deal_data_arr['industry']=="") $deal_data_arr['industry'] = "industry unknown";
        ////////////////////////////////////////////
        //banks data
        $deal_data_arr['banks'] = array();
        $q_bank = "select c.name from ".TP."transaction_partners as t left join ".TP."company as c on(t.partner_id=c.company_id) where t.transaction_id='".$deal_data_arr['deal_id']."' AND t.partner_type='bank'";
        $q_bank_res = mysql_query($q_bank);
        if(!$q_bank_res){
            return false;
        }
        ///////////////////////////
        $q_bank_res_count = mysql_num_rows($q_bank_res);
        for($bank_i=0;$bank_i<$q_bank_res_count;$bank_i++){
            $deal_data_arr['banks'][$bank_i] = mysql_fetch_assoc($q_bank_res);
            $deal_data_arr['banks'][$bank_i]['name'] = $g_mc->db_to_view($deal_data_arr['banks'][$bank_i]['name']);
        }
        //////////////////////////////////////////////////////////
        //law firm data
        $deal_data_arr['law_firms'] = array();
        $q_law = "select c.name from ".TP."transaction_partners as t left join ".TP."company as c on(t.partner_id=c.company_id) where t.transaction_id='".$deal_data_arr['deal_id']."' AND t.partner_type='law firm'";
        $q_law_res = mysql_query($q_law);
        if(!$q_law_res){
            return false;
        }
        ///////////////////////////
        $q_law_res_count = mysql_num_rows($q_law_res);
        for($law_i=0;$law_i<$q_law_res_count;$law_i++){
            $deal_data_arr['law_firms'][$law_i] = mysql_fetch_assoc($q_law_res);
            $deal_data_arr['law_firms'][$law_i]['name'] = $g_mc->db_to_view($deal_data_arr['law_firms'][$law_i]['name']);
        }
        //////////////////////////////////////////////////////////
        return true;
    }
    
    public function front_deal_search_paged($search_data,$start_offset,$num_to_fetch,&$data_arr,&$data_count, $lastIdForAlerts = 0){
        global $g_mc;
        /***
        sng:22/july/2010
        We get the name of the target company also, incase it is an M&A deal and we have to show the acquired company.
        
        sng:23/july/2010
        We need country, sector, industry of the company doing the deal
        
        sng:28/july/2010
        sometime, in case of M&A deals, we need to see if the subtype is Completed or not. So we take deal_subcat1_name
        
        sng:20/aug/2010
        For M&A deals, now we have seller also, so we take that
        
        sng:1/oct/2010
        When downloading the deal data to excel, we need to show the deal type, sub type, sub sub type
        
        sng:30/nov/2010
        Now when we use the deal_country attribute of transaction for country matching
        
        sng:2/dec/2010
        Now when sector and industry is given, we use the deal_sector, deal_industry attribute of transaction
        *********/
        /**********************************************************************
        sng:13/jan/2011
        We can send the partner_id via POST. If sent, show deals in which the partner firm was associated
        
        $q = "SELECT t.id as deal_id,t.company_id,value_in_billion,date_of_deal,deal_cat_name,deal_subcat1_name,deal_subcat2_name,target_company_name,seller_company_name,c.name as company_name,c.hq_country,c.sector,c.industry FROM ".TP."transaction AS t LEFT JOIN ".TP."company AS c ON ( t.company_id = c.company_id ) WHERE 1=1";
        ***/
		
		/**************************************
		sng:15/sep/2011
		A very bad kludge
		
		I have a set of condition, and deal_id. I want to know if the deal match the condition. This is used to send notification to the interested parties
		
		sng:27/oct/2011
		We now filter by deal size also
		
		sng:17/jan/2012
		We now have value range id for each deal that show the fuzzy deal value. These are predefined.
		Sometime, we only have value range id and deal value is 0
		If both deal value and value range id is 0, the deal value is undisclosed.
		
		sng:1/feb/2012
		We no longer have a single company associated with a deal. Now we have multiple companies
		
		sng:2/mar/2012
		Exclude inactive deals
		
		sng:5/mar/2012
		Now we need to know whether the deal has been verified by admin or not. Depending on that
		we show an icon in the listing
		*************************************/
        /*$q = "SELECT t.id as deal_id,t.company_id,value_in_billion,t.value_range_id,vrm.short_caption as fuzzy_value_short_caption,vrm.display_text as fuzzy_value,date_of_deal,deal_cat_name,deal_subcat1_name,deal_subcat2_name,target_company_name,seller_company_name,c.name as company_name,c.hq_country,c.sector,c.industry FROM ".TP."transaction AS t LEFT JOIN ".TP."company AS c ON ( t.company_id = c.company_id ) LEFT JOIN ".TP."transaction_value_range_master as vrm ON (t.value_range_id=vrm.value_range_id)";*/
		
		$q = "SELECT t.id as deal_id,value_in_billion,t.value_range_id,t.admin_verified,vrm.short_caption as fuzzy_value_short_caption,vrm.display_text as fuzzy_value,date_of_deal,deal_cat_name,deal_subcat1_name,deal_subcat2_name,target_company_name,seller_company_name FROM ".TP."transaction AS t  LEFT JOIN ".TP."transaction_value_range_master as vrm ON (t.value_range_id=vrm.value_range_id)";
		
		/***************
		sng:3/feb/2012
		get the deals that have the matching company as participant. The distinct clause eliminate duplicates.
		the right join restrict the other joins to the selected deal ids only
		********************/
		if(isset($search_data['top_search_term'])&&($search_data['top_search_term']!="")){
			$q.= "right join (select distinct trc.transaction_id from ".TP."transaction_companies as trc left join ".TP."company as com on(trc.company_id=com.company_id) where com.name like '".mysql_real_escape_string($search_data['top_search_term'])."%') as p on(t.id=p.transaction_id)";
            
        }
		
        if(isset($search_data['partner_id'])&&($search_data['partner_id']!="")){
            $q.=" left join ".TP."transaction_partners as part on(t.id=part.transaction_id)";
        }
        $q.=" WHERE t.is_active='y'";
		
		if(isset($search_data['deal_id'])&&($search_data['deal_id']!="")){
            $q.= " and t.id='".$search_data['deal_id']."'";
        }
		
        if(isset($search_data['partner_id'])&&($search_data['partner_id']!="")){
            $q.= " and part.partner_id='".$search_data['partner_id']."'";
        }
        /************************************************************************************/
        if(isset($search_data['deal_cat_name'])&&($search_data['deal_cat_name']!="")){
            $q.=" and t.deal_cat_name = '".$search_data['deal_cat_name']."'";
        }
        if(isset($search_data['deal_subcat1_name'])&&($search_data['deal_subcat1_name']!="")){
            $q.=" and t.deal_subcat1_name = '".$search_data['deal_subcat1_name']."'";
        }
        if(isset($search_data['deal_subcat2_name'])&&($search_data['deal_subcat2_name']!="")){
            $q.=" and t.deal_subcat2_name = '".$search_data['deal_subcat2_name']."'";
        }
        /******************************************
        sng:20/jul/2010
        The year is no longer a simple text. Now it can be blank, a year or a range
        ***/
        
        //if($search_data['year']!=""){
        //    $q.=" and year(t.date_of_deal) = '".$search_data['year']."'";
        //}
        if(isset($search_data['year'])&&($search_data['year']!="")){
            $year_tokens = explode("-",$search_data['year']);
            $year_tokens_count = count($year_tokens);
            if($year_tokens_count == 1){
                //singleton year
                $q.=" and year(t.date_of_deal)='".$year_tokens[0]."'";
            }
            if($year_tokens_count == 2){
                //range year
                $q.=" and year(t.date_of_deal)>='".$year_tokens[0]."' AND year(t.date_of_deal)<='".$year_tokens[1]."'";
            }
        }
        /********************************************************************************
		sng: 27/oct/2011
		filter by deal_size. The value is either blank or like >=deal value in billion or <=deal value in billion
		
		sng:12/nov/2011
		Once we get into the concept of deal size, we filter out deals with undisclosed value (stored as 0.0)
		
		sng:14/nov/2011
		Assuming that the user is not searching for deals with undisclosed value. If user is searching for Undisclosed
		we show only deals whose value is 0.0
		see deal_search_filter_form_view.php for the options
		***/
		if(isset($search_data['deal_size'])&&($search_data['deal_size']!="")){
			if($search_data['deal_size']=="0.0"){
				$q.=" and t.value_in_billion=0.0";
			}else{
				$q.=" and t.value_in_billion".$search_data['deal_size']." and t.value_in_billion!=0.0";
			}
        }
		/*******************************************************************
		sng: 20/jan/2012
		Now we have value range id for deal search.
		
		sng:18/july/2012
		I think this should be taken from $search_data
		**********/
		if(isset($search_data['value_range_id'])&&($search_data['value_range_id']!="")){
			if($search_data['value_range_id']=="0"){
				$q.=" and t.value_in_billion=0.0 AND t.value_range_id=0";
			}else{
				$q.=" and t.value_range_id='".$search_data['value_range_id']."'";
			}
        }
        /************************************************************************
        sng:2/dec/2010
        No more sector or industry of the company, we now search for these in deal_sector, deal_industry in transaction table
        if($search_data['sector']!=""){
            $q.=" and c.sector = '".$search_data['sector']."'";
        }
        /***
        10/jul/2010
        Logged in members can filter by industry also
        **/
        /**
        if($search_data['industry']!=""){
            $q.=" and c.industry = '".$search_data['industry']."'";
        }
        ****/
        if(isset($search_data['sector'])&&($search_data['sector']!="")){
            $q.=" and t.deal_sector like '%".$search_data['sector']."%'";
        }
        if(isset($search_data['industry'])&&($search_data['industry']!="")){
            $q.=" and t.deal_industry like '%".$search_data['industry']."%'";
        }
		/***********
		sng:13/sep/2011
		The above works because all sector and industry names are different. We do not have sector= abc and sector =abcd
        /******************************************************************************/
        /***
        sng:12/may/2010
        we magic quote the company name
        sng:19/may/2010
        The top search box is now changed and the param is top_search_term
        
        sng:20/aug/2010
        Now it can happen that top search term refer to the name of the target or seller. We check for the
        parameter search_target='y'
        see search_all.php
		
		sng:3/feb/2012
		We no longer seller or companies with the deal. Now we have multiple companies for a deal with roles.
		Let us discard the concept of search_target.
		Now we search for the company name in all the participants for a deal.
		In fact, since it require a join, we put this code above, before partners
		if($search_data['top_search_term']!=""){}
        ***/
        
        /***
        country and region
        if country is specified, region is overridden
        **/
        $country_filter = "";
        if(isset($search_data['country'])&&($search_data['country']!="")){
            /*******************************************************
            sng:30/Nov/2010
            No more the country of the HQ of the company doing the deal. Now use deal_country (which is a csv)
            $country_filter = "c.hq_country='".$search_data['country']."'";
            ***********/
            $country_filter = "t.deal_country LIKE '%".$search_data['country']."%'";
            /*******************************************/
        }else{
            if(isset($search_data['region'])&&($search_data['region']!="")){
                //get the country names for this region name
                $region_q = "select cm.name from ".TP."region_master as rm left join ".TP."region_country_list as rc on(rm.id=rc.region_id) left join ".TP."country_master as cm on(rc.country_id=cm.id) where rm.name='".$search_data['region']."'";
                $region_q_res = mysql_query($region_q);
                if(!$region_q_res){
                    return false;
                }
                
                /*****************************************************************
                sng:30/Nov/2010
                No more the country of the HQ of the company doing the deal. Now use deal_country (which is a csv)
                So now that we have got the individual countries of the region. let us create a OR clause and
                for each country of the region, try to match it in deal_country. Since any one country from the region needs to
                match, we use a OR
                So say, region is BRIC. Then country filter is 
                (deal_country like '%Brazil%' OR deal_country like '%Russia%' OR deal_country like '%India%' OR deal_country like '%China%')
                
                $region_country_csv = "";
                $region_q_res_cnt = mysql_num_rows($region_q_res);
                
                if($region_q_res_cnt > 0){
                    while($region_q_res_row = mysql_fetch_assoc($region_q_res)){
                        $region_country_csv.=",'".$region_q_res_row['name']."'";
                    }
                    $region_country_csv = substr($region_country_csv,1);
                    $country_filter = "c.hq_country IN(".$region_country_csv.")";
                }
                ****/
                $region_q_res_cnt = mysql_num_rows($region_q_res);
                $region_clause = "";
                if($region_q_res_cnt > 0){
                    while($region_q_res_row = mysql_fetch_assoc($region_q_res)){
                        $region_clause.="|t.deal_country LIKE '%".$region_q_res_row['name']."%'";
                    }
                    $region_clause = substr($region_clause,1);
                    $region_clause = str_replace("|"," OR ",$region_clause);
                    $country_filter = "(".$region_clause.")";
                }
                /************************************************************************/
            }
        }
        if($country_filter!=""){
            $q.=" and ".$country_filter;
        }
        
        if ($lastIdForAlerts) {
            $q.=" and t.id > $lastIdForAlerts" ;
        }        
		/*****************************************************
        sng:20/july/2010
        Now user can choose to see all deals, top 10 or 25 deals (by deal value size), or
        most recent 10 or 25 deals (by deal date), see deal_search_filter_form_view for
        description of the field
        Here of course, we do not try to restrict the limit. Let the caller manage that, whether
        to show 10 or 25 (otherwise we mess up pagination and many other codes).
        ***********/
        
        /**
         * If the proper parameter is set we need to fetch the deals in the last 2 weelks
         * This is used for 2WeeksNow
         */
        
        if (isset($search_data['miniumDateForDeals'])) {
            $q .=  sprintf('and t.date_of_deal > date_sub("%s", INTERVAL 14 DAY)', $search_data['miniumDateForDeals']);
        }

        if (isset($search_data['last_alert_date'])) {
            $q .=  sprintf(' and t.last_edited >= "%s" ', $search_data['last_alert_date']);
        }        
      
        if (isset($search_data['last_alert_date_max'])) {
            $q .=  sprintf(' and t.last_edited < "%s" ', $search_data['last_alert_date_max']);
        }         
        /**
        * 15.08.2010 00:37
        * imihai for alerts we don`t need to show the deals in the default order
        * but the new ones first. (we don`t want the users to get bored scrolling)
        */
        if (!isset($_REQUEST['lid'])) {
			/********************************
			sng:31/oct/2011
			We have put another dummy in number_of_deals called 'size'. We need to check for that also
			***********************************/
            if(($search_data['number_of_deals']!="")&&($search_data['number_of_deals']!="size")){
                //top:25 or recent:10
                $order_tokens = explode(":",$search_data['number_of_deals']);
                if($order_tokens[0] == "top"){
                    $q .= " order by value_in_billion desc,t.id desc";
                }
                if($order_tokens[0] == "recent"){
                    $q .= " order by date_of_deal desc,t.id desc";
                }
            }else{
                /***
                sng:30/aug/2010
                this is not recent or top. Still, we show most recent deal first
                ***/
                $q.=" order by date_of_deal desc,t.id desc";
            }
        } else {
            if (isset($search_data['last_alert_date'])) {
                $q .= " ORDER BY date_of_deal DESC";
            } else {
                $q .= " ORDER BY t.id DESC";
            }
        }
        
        
        /***
        The ordering by data of transaction in descending order is ok but what happens when the dates are same? It seems that the ordering then is random. So we
        add another tie breaker - the order in which the deals were entered
        /*******************************************************/
        $q.=" limit ".$start_offset.",".$num_to_fetch;
        //echo "<div style='display:none'><pre>$q</pre></div>";
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        
        $data_count = mysql_num_rows($res);
        if(0==$data_count){
            //no data, no need to proceed
            return true;
        }
        

        //////////////////////////////////
        /**
        * 12-08-2010 imihai
        * $data_count-1 because mysql_num_rows does not return the number of deals 
        * starting from 0 but from 1
        */
		require_once("classes/class.transaction_company.php");
		$g_trans_comp = new transaction_company();
		
        for($k=0;$k<=$data_count-1;$k++){
            $data_arr[$k] = mysql_fetch_assoc($res);
            $data_arr[$k]['company_name'] = $g_mc->db_to_view($data_arr[$k]['company_name']);
            $data_arr[$k]['target_company_name'] = $g_mc->db_to_view($data_arr[$k]['target_company_name']);
            $data_arr[$k]['seller_company_name'] = $g_mc->db_to_view($data_arr[$k]['seller_company_name']);
			/**************
			sng:17/jan/2012
			if we do not have exact value or fuzzy value, the short caption is n/d for undisclosed
			***************/
			if(($data_arr[$k]['value_in_billion']==0)&&($data_arr[$k]['value_range_id']==0)){
				$data_arr[$k]['fuzzy_value'] = "Not disclosed";
				$data_arr[$k]['fuzzy_value_short_caption'] = "n/d";
			}
            //set bankers and law firms
            $transaction_id = $data_arr[$k]['deal_id'];
            /**
            * 15.08.2010 00.27 
            * there seems to be a problem with the queries run in get_all_partner
            * for searches that return lots of deals so for alerts just ignore partners
            */
            if (!$lastIdForAlerts) {
                $data_arr[$k]['banks'] = array();
                $data_cnt = 0;
                $success = $this->get_all_partner($transaction_id,"bank",$data_arr[$k]['banks'],$data_cnt);
                if(!$success){
                    return false;
                }
                ///////////////////////////
                $data_arr[$k]['law_firms'] = array();
                $data_cnt = 0;
                $success = $this->get_all_partner($transaction_id,"law firm",$data_arr[$k]['law_firms'],$data_cnt);
                if(!$success){
                    return false;
                }
				
				/**************************
				sng:1/feb/2012
				get the deal participants, just the names
				*************************/
				$data_arr[$k]['participants'] = NULL;
				$success = $g_trans_comp->get_deal_participants($transaction_id,$data_arr[$k]['participants']);
                if(!$success){
                    return false;
                }
            }
        }
        //echo $q;
        return true;
    }
    
    public function get_make_me_top_search_result_deals($job_id,$result_id,$num_to_fetch,&$data_arr,&$data_count){
        global $g_mc;
        //get the query used to generate this search
        $q = "select query,ranking_criteria from ".TP."top_search_request_hits where id='".$result_id."'";
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        $cnt = mysql_num_rows($res);
        if(0==$cnt){
            return false;
        }
        $row = mysql_fetch_assoc($res);
        $query = $row['query'];
        
        //from this, strip the group by clause
        $pos = stripos($query,"group by");
        if($pos!==false){
            $query = substr($query,0,$pos);
        }
        //select the first 'and'
        $pos = stripos($query,"and");
        if($pos!==false){
            $query = substr($query,$pos+3);
        }
        //do not try to change to lower case. If you do, it change the parameter values
        
        //replace the 'and company_id' with 'and t.company_id'
        //because we will use the prefix t. when we create the total query
        $query = str_replace("and company_id","and t.company_id",$query);
        //die($query);
        $q = "SELECT t.id as deal_id,t.company_id,value_in_billion,date_of_deal,deal_cat_name,deal_subcat1_name,target_company_name,seller_company_name,c.name as company_name,c.hq_country,c.sector,c.industry FROM ".TP."transaction AS t LEFT JOIN ".TP."company AS c ON ( t.company_id = c.company_id ) WHERE ".$query." order by value_in_billion desc,t.id desc limit 0,".$num_to_fetch;
        
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        
        $data_count = mysql_num_rows($res);
        if(0==$data_count){
            //no data, no need to proceed
            return true;
        }
        
        //////////////////////////////////
        /**
        * 12-08-2010 imihai
        * $data_count-1 because mysql_num_rows does not return the number of deals 
        * starting from 0 but from 1
        */
        for($k=0;$k<=$data_count-1;$k++){
            $data_arr[$k] = mysql_fetch_assoc($res);
            $data_arr[$k]['company_name'] = $g_mc->db_to_view($data_arr[$k]['company_name']);
            $data_arr[$k]['target_company_name'] = $g_mc->db_to_view($data_arr[$k]['target_company_name']);
            $data_arr[$k]['seller_company_name'] = $g_mc->db_to_view($data_arr[$k]['seller_company_name']);
            //set bankers and law firms
            $transaction_id = $data_arr[$k]['deal_id'];
            /**
            * 15.08.2010 00.27 
            * there seems to be a problem with the queries run in get_all_partner
            * for searches that return lots of deals so for alerts just ignore partners
            */
            if (!$lastIdForAlerts) {
                $data_arr[$k]['banks'] = array();
                $data_cnt = 0;
                $success = $this->get_all_partner($transaction_id,"bank",$data_arr[$k]['banks'],$data_cnt);
                if(!$success){
                    return false;
                }
                ///////////////////////////
                $data_arr[$k]['law_firms'] = array();
                $data_cnt = 0;
                $success = $this->get_all_partner($transaction_id,"law firm",$data_arr[$k]['law_firms'],$data_cnt);
                if(!$success){
                    return false;
                }
            }
        }
        
        return true;
    }
    /**********
	This function use the transaction table only, so no extra data. We now use the function given below in the latest code
	**************/
    public function front_get_deal_detail($deal_id,&$deal_data_arr,&$deal_found){
		return false;
        /***************
		sng:2/mar/2012
		not used anywhere
		*************/
    }
	/**********
	This funciton is the way to go now.
	It use left joins instead of multi queries
	
	sng:21/jun/2011
	We need sector of the company doing the deal
	
	sng:2/sep/2011
	We need the documnet files for this transaction (if any). Since those are stored as individual records. we create a placeholder
	in the query and fill it later
	
	sng:31/jan/2012
	Now we do not have a single company associated with a deal but multiple companies. We create a placeholder and fill it out later
	
	sng:20/mar/2012
	we need to know who posted the deal and when.
	***********/
	public function front_get_deal_detail_extra($deal_id,&$deal_data_arr,&$deal_found){
		global $g_mc;
		$db = new db();
        
        if($deal_id==""){
            $deal_found = false;
            return true;
        }
        
		/*****
		sng:31/jan/2012
		We no longer have one company with a deal and do not use the company id in transaction table. We fill those via participants
		$q = "select t.id as deal_id,t.*,c.name as company_name,c.hq_country,c.sector,c.industry,c.logo,e.*,n.note,s.sources,takeover_name,'participants' as `participants`,'banks' as `banks`,'law_firms' as `law_firms`,'docs' as `docs`,vrm.short_caption as fuzzy_value_short_caption,vrm.display_text as fuzzy_value from ".TP."transaction as t left join ".TP."company as c on(t.company_id=c.company_id) left join ".TP."transaction_extra_detail as e on(t.id=e.transaction_id) left join ".TP."transaction_note as n on(t.id=n.transaction_id) left join ".TP."transaction_sources as s on(t.id=s.transaction_id) left join ".TP."takeover_type_master as k on(e.takeover_id=k.takeover_id) LEFT JOIN ".TP."transaction_value_range_master as vrm ON (t.value_range_id=vrm.value_range_id) where t.id='".$deal_id."'";
		***************/
		/***********************
		sng:2/may/2012
		We now store the source urls in multiple rows. We need the technique used for banks or law firms
		***********************/
		$q = "select t.id as deal_id,t.*,c.logo,e.*,n.note,takeover_name,'participants' as `participants`,'banks' as `banks`,'law_firms' as `law_firms`,'sources' as `sources`,'docs' as `docs`,vrm.short_caption as fuzzy_value_short_caption,vrm.display_text as fuzzy_value,m.work_email,m.member_type from ".TP."transaction as t left join ".TP."company as c on(t.company_id=c.company_id) left join ".TP."transaction_extra_detail as e on(t.id=e.transaction_id) left join ".TP."transaction_note as n on(t.id=n.transaction_id) left join ".TP."takeover_type_master as k on(e.takeover_id=k.takeover_id) LEFT JOIN ".TP."transaction_value_range_master as vrm ON (t.value_range_id=vrm.value_range_id) left join ".TP."member as m on(t.added_by_mem_id=m.mem_id) where t.id='".$deal_id."'";
         
        $result = $db->select_query($q);
        
        if(!$result){
			//echo $db->error();
            return false;
        }
		
		if(!$db->has_row()){
            $deal_found = false;
            return true;
        }
        //////////////////////////////////
        $deal_data_arr = $db->get_row();
        $deal_found = true;
        
        
        /////////////////////////////////////////////////////////////////////////////////
		/**************
		sng:1/feb/2012
		participating companies data
		Use the transaction_company method
		****************/
		$deal_data_arr['participants'] = NULL;
		require_once("classes/class.transaction_company.php");
		$g_trans_comp = new transaction_company();
		
		$ok = $g_trans_comp->get_deal_participants_detailed($deal_data_arr['deal_id'],$deal_data_arr['participants']);
		
		if(!$ok){
            return false;
        }
		
		
		/*****************
		sng:17/jun/2011
		now we get the is_sellside_advisor flag also
		
		sng:26/sep/2011
		now we get the is_insignificant flag also
		
		sng:27/sep/2011
		we need to show the is_insignificant firms below the main firms. Let us try
		by ordering on that field. The field is enum y,n so y is 0, n is 1. We need desc
		to show n first
		
		sng:27/sep/2011
		partners can have roles in a deal
		
		sng:23/mar/2012
		we also need the role id, it is used elsewhere
		***********************/
        //banks data
        $deal_data_arr['banks'] = array();
        $q_bank = "select t.partner_id,t.role_id,t.is_sellside_advisor,t.is_insignificant,t.adjusted_value_in_billion,c.name,rm.role_name from ".TP."transaction_partners as t left join ".TP."company as c on(t.partner_id=c.company_id) left join ".TP."transaction_partner_role_master as rm on(t.role_id=rm.role_id) where t.transaction_id='".$deal_data_arr['deal_id']."' AND t.partner_type='bank' order by t.is_insignificant desc";
        $q_bank_res = mysql_query($q_bank);
        if(!$q_bank_res){
            return false;
        }
        ///////////////////////////
        $q_bank_res_count = mysql_num_rows($q_bank_res);
        for($bank_i=0;$bank_i<$q_bank_res_count;$bank_i++){
            $deal_data_arr['banks'][$bank_i] = mysql_fetch_assoc($q_bank_res);
            $deal_data_arr['banks'][$bank_i]['name'] = $g_mc->db_to_view($deal_data_arr['banks'][$bank_i]['name']);
        }
        //////////////////////////////////////////////////////////
        //law firm data
        $deal_data_arr['law_firms'] = array();
        $q_law = "select t.partner_id,t.role_id,t.is_sellside_advisor,t.is_insignificant,t.adjusted_value_in_billion,c.name,rm.role_name from ".TP."transaction_partners as t left join ".TP."company as c on(t.partner_id=c.company_id) left join ".TP."transaction_partner_role_master as rm on(t.role_id=rm.role_id) where t.transaction_id='".$deal_data_arr['deal_id']."' AND t.partner_type='law firm' order by t.is_insignificant desc";
        $q_law_res = mysql_query($q_law);
        if(!$q_law_res){
            return false;
        }
        ///////////////////////////
        $q_law_res_count = mysql_num_rows($q_law_res);
        for($law_i=0;$law_i<$q_law_res_count;$law_i++){
            $deal_data_arr['law_firms'][$law_i] = mysql_fetch_assoc($q_law_res);
            $deal_data_arr['law_firms'][$law_i]['name'] = $g_mc->db_to_view($deal_data_arr['law_firms'][$law_i]['name']);
        }
        //////////////////////////////////////////////////////////
		/**************
		sng:2/may/2012
		sources
		****************/
		$deal_data_arr['sources'] = NULL;
		require_once("classes/class.transaction_source.php");
		$trans_source = new transaction_source();
		
		$ok = $trans_source->get_deal_sources($deal_data_arr['deal_id'],$deal_data_arr['sources']);
		
		if(!$ok){
            return false;
        }
		/*******************************
		sng:2/sep/2011
		get the files for this deal
		
		sng:6/sep/2011
		we list only those files that are approved by admin
		
		sng:22/feb/2012
		we now use method in transaction_doc to get the docs associated with a transaction
		**********************/
		require_once("classes/class.transaction_doc.php");
		$trans_doc = new transaction_doc();
		$deal_data_arr['docs'] = NULL;
		$temp_count = 0;
		$ok = $trans_doc->front_get_all_documents_for_deal($deal_id,$deal_data_arr['docs'],$temp_count);
		if(!$ok){
			return false;
		}
		return true;
	}
    
    public function get_deal_partner_team_data($deal_id,$deal_partner_id,&$deal_partner_team_data_arr,&$deal_partner_team_data_count){
        $q = "select p.*,m.f_name,m.l_name from ".TP."transaction_partner_members as p left join ".TP."member as m on(p.member_id=m.mem_id) where transaction_id='".$deal_id."' and partner_id='".$deal_partner_id."' order by p.adjusted_value_in_billion desc";
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        $deal_partner_team_data_count = mysql_num_rows($res);
        if(0==$deal_partner_team_data_count){
            return true;
        }
        //////////////////////////////////
        for($i=0;$i<$deal_partner_team_data_count;$i++){
            $deal_partner_team_data_arr[$i] = mysql_fetch_assoc($res);
        }
        return true;
    }
    /***
    an ugly hack to support the listing of top 3 team member for each bank/law firm in detail page
    get the top members. The order is by share value
    *********/
    public function get_deal_partner_members($deal_id,$deal_partner_id,$num_to_fetch,&$deal_partner_team_data_arr,&$deal_partner_team_data_count){
        $q = "select p.*,m.f_name,m.l_name from ".TP."transaction_partner_members as p left join ".TP."member as m on(p.member_id=m.mem_id) where transaction_id='".$deal_id."' and partner_id='".$deal_partner_id."' order by adjusted_value_in_billion desc limit 0,".$num_to_fetch;
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        $deal_partner_team_data_count = mysql_num_rows($res);
        if(0==$deal_partner_team_data_count){
            return true;
        }
        //////////////////////////////////
        for($i=0;$i<$deal_partner_team_data_count;$i++){
            $deal_partner_team_data_arr[$i] = mysql_fetch_assoc($res);
        }
        return true;
    }
    /***
    sng:17/apr/2010
    adding a member to a deal team of a bank/law firm that was associated with the deal
    check:
    if that partner company is actually with the deal. If so
    check whether this member is already a part of the deal team or not.
    A member can be added only once, never mind the his/her company.
    If not present, the member can be added. But, when the deal was closed
    did the member worked for that partner company? We code a simpler validation.
    The member is either working for the partner company or worked for the parter company
    **********/
    public function add_deal_partner_team_member($deal_id,$deal_partner_id,$mem_id,&$mem_added,&$msg){
        //check if the partner company is actually associated with the deal
        $q = "select count(*) as cnt from ".TP."transaction_partners where transaction_id='".$deal_id."' and partner_id='".$deal_partner_id."'";
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        $row = mysql_fetch_assoc($res);
        if($row['cnt'] == 0){
            //this partner was not found for the deal
            $mem_added = false;
            $msg = "This firm was not associated with the deal";
            return true;
        }
        ///////////////////////////////////////////////////////////////////////////
        //check if the member is already added or not
        $q = "select count(*) as cnt from ".TP."transaction_partner_members where transaction_id='".$deal_id."' and member_id='".$mem_id."'";
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        $row = mysql_fetch_assoc($res);
        if($row['cnt']!=0){
            //this member found for this deal
            $mem_added = false;
            $msg = "This member is present in the deal team";
            return true;
        }
        /////////////////////////////////////////////////////////////////
        //if the partner work for the partner company, the partner id and od of the
        //member's company will match, get the designation and weight
         $q = "select designation,member_type from ".TP."member where mem_id='".$mem_id."' and company_id='".$deal_partner_id."'";
         $res = mysql_query($q);
        if(!$res){
        
            return false;
        }
        $cnt = mysql_num_rows($res);
        if($cnt==0){
            //either the member is not found or the member does not work for the deal partner company
            //we assume that member does not work there, so we check the history
            //whether the member worked in that company at all
            //we try to get the last postition at that company
            $q1 = "select member_type,designation from ".TP."member_work_history where mem_id='".$mem_id."' and company_id='".$deal_partner_id."' order by year_from desc limit 0,1";
            $res1 = mysql_query($q1);
            if(!$res1){
            
                return false;
            }
            $cnt1 = mysql_num_rows($res1);
            if($cnt1==0){
                //not found in hostory so
                $mem_added = false;
                $msg = "Association with the firm was not found";
                return true;
            }else{
                //found
                $row1 = mysql_fetch_assoc($res1);
                $designation = $row1['designation'];
                $mem_type = $row1['member_type'];
            }
        }else{
            //member works, so get the designation and wight
            $row = mysql_fetch_assoc($res);
            $designation = $row['designation'];
            $mem_type = $row['member_type'];
        }
        //////////////////////////////////////////////
        //now get the weight for this designation for this member type
        $q = "select deal_share_weight from ".TP."designation_master where designation='".$designation."' and member_type='".$mem_type."'";
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        $cnt = mysql_num_rows($res);
        if($cnt==0){
            //no such designation for this member type, use default of 1
            $deal_share_weight = 1;
        }else{
            $row = mysql_fetch_assoc($res);
            $deal_share_weight = $row['deal_share_weight'];
        }
        /////////////////////////////////
        //insert
        $q = "insert into ".TP."transaction_partner_members set transaction_id='".$deal_id."', partner_id='".$deal_partner_id."', member_id='".$mem_id."',member_type='".$mem_type."',designation='".$designation."',deal_share_weight='".$deal_share_weight."'";
        $result = mysql_query($q);
        if($result){
            $mem_added = true;
            $msg = "Added to the deal team";
            //update deal team members adjusted value
            $success = $this->update_deal_team_members_adjusted_value($deal_id,$deal_partner_id);
            
            return true;
        }else{
            return false;
        }
        
    }
    
    /***
    sng:25/may/2010
    adding a member to a deal team of a bank/law firm that was associated with the deal
    since admin is adding this, we do not keep any check here, but we do check
    if the bank / law firm is associated with the deal or not
    If the member is already added or not
    **********/
    public function admin_add_deal_partner_team_member($deal_id,$deal_partner_id,$mem_id,&$mem_added,&$msg){
        if($deal_partner_id==""){
            $mem_added = false;
            $msg = "Firm not specified";
            return true;
        }
        if($mem_id==""){
            $mem_added = false;
            $msg = "Member not specified";
            return true;
        }
        //check if the partner company is actually associated with the deal
        $q = "select count(*) as cnt from ".TP."transaction_partners where transaction_id='".$deal_id."' and partner_id='".$deal_partner_id."'";
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        $row = mysql_fetch_assoc($res);
        if($row['cnt'] == 0){
            //this partner was not found for the deal
            $mem_added = false;
            $msg = "This firm was not associated with the deal";
            return true;
        }
        ///////////////////////////////////////////////////////////////////////////
        //check if the member is already added or not
        $q = "select count(*) as cnt from ".TP."transaction_partner_members where transaction_id='".$deal_id."' and member_id='".$mem_id."'";
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        $row = mysql_fetch_assoc($res);
        if($row['cnt']!=0){
            //this member found for this deal
            $mem_added = false;
            $msg = "This member is present in the deal team";
            return true;
        }
        /////////////////////////////////////////////////////////////////
        //get member type and designation
        $q = "select member_type,designation from ".TP."member where mem_id='".$mem_id."'";
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        $q_cnt = mysql_num_rows($res);
        if(0==$q_cnt){
            //member not found
            $mem_added = false;
            $msg = "This member is not found";
            return true;
        }
        $row = mysql_fetch_assoc($res);
        $designation = $row['designation'];
        $mem_type = $row['member_type'];
        //////////////////////////////////////////////
        //now get the weight for this designation for this member type
        $q = "select deal_share_weight from ".TP."designation_master where designation='".$designation."' and member_type='".$mem_type."'";
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        $cnt = mysql_num_rows($res);
        if($cnt==0){
            //no such designation for this member type, use default of 1
            $deal_share_weight = 1;
        }else{
            $row = mysql_fetch_assoc($res);
            $deal_share_weight = $row['deal_share_weight'];
        }
        /////////////////////////////////
        //insert
        $q = "insert into ".TP."transaction_partner_members set transaction_id='".$deal_id."', partner_id='".$deal_partner_id."', member_id='".$mem_id."',member_type='".$mem_type."',designation='".$designation."',deal_share_weight='".$deal_share_weight."'";
        $result = mysql_query($q);
        if($result){
            $mem_added = true;
            $msg = "Added to the deal team";
            //update deal team members adjusted value
            $success = $this->update_deal_team_members_adjusted_value($deal_id,$deal_partner_id);
            
            return true;
        }else{
            return false;
        }
        
    }
    
    public function remove_deal_partner_team_member($deal_id,$deal_partner_id,$mem_id,&$mem_removed,&$msg){
        //delete the required row from transaction partner member and update the adjusted
        //deal value for the remaining members of that partner company for that deal
        $q = "delete from ".TP."transaction_partner_members where transaction_id='".$deal_id."' and member_id='".$mem_id."'";
        $result = mysql_query($q);
        if(!$result){
            return false;
        }
        $mem_removed = true;
        $msg = "You have been removed from the deal team";
        //update deal team members adjusted value
        $success = $this->update_deal_team_members_adjusted_value($deal_id,$deal_partner_id);
        return true;
    }
    
    public function flag_deal_partner_team_members($deal_id,$deal_partner_id,$mem_id_csv,$flagged_by,&$msg){
        $mem_ids = explode(",",$mem_id_csv);
        $cnt = count($mem_ids);
        $quoted_csv = "";
        for($i=0;$i<$cnt;$i++){
            $quoted_csv.=",'".$mem_ids[$i]."'";
        }
        $quoted_csv = substr($quoted_csv,1);
        $date_today = date("Y-m-d");
        $q = "update ".TP."transaction_partner_members set is_flagged='Y', date_flagged='".$date_today."', flagged_by='".$flagged_by."' where transaction_id='".$deal_id."' and partner_id='".$deal_partner_id."' and member_id IN (".$quoted_csv.")";
        $result = mysql_query($q);
        if(!$result){
            return false;
        }
        //////////////
        $msg = "Members flagged";
        return true;
    }
    
    public function update_deal_team_members_adjusted_value($deal_id,$deal_partner_id){
        //get the adjusted value for the partner company
        $q = "select adjusted_value_in_billion from ".TP."transaction_partners where transaction_id='".$deal_id."' and partner_id='".$deal_partner_id."'";
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        $cnt = mysql_num_rows($res);
        if($cnt == 0){
            //no such deal and partner
            return false;
        }
        ////////////////////////////////////
        $row = mysql_fetch_assoc($res);
        $partner_adjusted_value_in_billion = $row['adjusted_value_in_billion'];
        ///////////////////////////////////////////////
        //now get the sum of weight for all the members for this deal and partner
        $q = "select sum(deal_share_weight) as sum_weight from ".TP."transaction_partner_members where transaction_id='".$deal_id."' and partner_id='".$deal_partner_id."'";
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        $row = mysql_fetch_assoc($res);
        $sum_wt = $row['sum_weight'];
        /***
        sng:12/may/2010
        if there are no members then there is no weights and sum is zero, so in that case we do not proceed
        ********/
        if(0==$sum_wt){
            return true;
        }
        $ratio = $partner_adjusted_value_in_billion/$sum_wt;
        //update the members
        $q = "update ".TP."transaction_partner_members set adjusted_value_in_billion=deal_share_weight*".$ratio." where transaction_id='".$deal_id."' and partner_id='".$deal_partner_id."'";
        
        $result = mysql_query($q);
        if(!$result){
            return false;
        }
        return true;
    }
    
    public function get_disputed_deal_team_members_paged($start_offset,$num_to_fetch,&$data_arr,&$data_count){
        global $g_mc;
        
        $q = "SELECT t. * , deal_company.name as deal_company_name FROM (
SELECT transaction_id, trans.company_id AS deal_company_id, year( trans.date_of_deal ) as deal_year , partner_id, assoc.name AS associate, member_id AS flagged_mem_id, m2.f_name AS flagged_f_name, m2.l_name AS flagged_l_name, date_flagged, flagged_by, m.f_name AS flagger_f_name, m.l_name AS flagger_l_name
FROM ".TP."transaction_partner_members AS pm
LEFT JOIN ".TP."member AS m ON ( pm.flagged_by = m.mem_id ) 
LEFT JOIN ".TP."member AS m2 ON ( pm.member_id = m2.mem_id ) 
LEFT JOIN ".TP."company AS assoc ON ( pm.partner_id = assoc.company_id ) 
LEFT JOIN ".TP."transaction AS trans ON ( pm.transaction_id = trans.id ) 
WHERE is_flagged = 'Y'
) AS t
LEFT JOIN ".TP."company AS deal_company ON ( t.deal_company_id = deal_company.company_id ) 
ORDER BY t.date_flagged DESC 
LIMIT ".$start_offset." , ".$num_to_fetch;

        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        /////////////////////////////
        $data_count = mysql_num_rows($res);
        if(0==$data_count){
            //no data
            return true;
        }
        ////////////////////////////
        for($i=0;$i<$data_count;$i++){
            $data_arr[$i] = mysql_fetch_assoc($res);
            $data_arr[$i]['deal_company_name'] = $g_mc->db_to_view($data_arr[$i]['deal_company_name']);
            $data_arr[$i]['associate'] = $g_mc->db_to_view($data_arr[$i]['associate']);
            $data_arr[$i]['flagged_f_name'] = $g_mc->db_to_view($data_arr[$i]['flagged_f_name']);
            $data_arr[$i]['flagged_l_name'] = $g_mc->db_to_view($data_arr[$i]['flagged_l_name']);
            $data_arr[$i]['flagger_f_name'] = $g_mc->db_to_view($data_arr[$i]['flagger_f_name']);
            $data_arr[$i]['flagger_l_name'] = $g_mc->db_to_view($data_arr[$i]['flagger_l_name']);
        }
        return true;
    }
    
    public function unflag_deal_partner_team_members($deal_id,$deal_partner_id,$mem_id){
        $q = "update ".TP."transaction_partner_members set is_flagged='N' where transaction_id='".$deal_id."' and partner_id='".$deal_partner_id."' and member_id='".$mem_id."'";
        $result = mysql_query($q);
        if(!$result){
            return false;
        }
        //////////
        return true;
    }
    ////////////////////////////////////////////////////////////////////////////
    /***
    get the recent deals in which the member participated
    This is used in the member's profile page
    It may happen that the member changed company and some deals were with JP Morgan, some with Citi. So take the firm name
    and designation from the transaction partner member.
    
    sng:26/apr/2010: if we also get the logo of the deal company, then we can create tombstone by using this function to get
    the required data and generate the tombstone.
    
    sng:29/apr/2010: we also need the id of the deal in case we need to show the deal detail
    we also need the id of the deal making company in case we want to link to that company
    
    we will not use the function front_get_deals_of_member_paged. That function might have different requirement
    ***/
    public function front_get_recent_deals_of_member($member_id,$num_deals,&$data_arr,&$data_count){
        global $g_mc;
        $q = "SELECT t.date_of_deal,t.id as deal_id, t.deal_cat_name,t.deal_subcat1_name,t.deal_subcat2_name, t.value_in_billion, pm.designation, firm.name AS firm_name, c.name AS deal_company_name,c.logo,c.company_id as deal_company_id FROM ".TP."transaction_partner_members AS pm LEFT JOIN ".TP."transaction AS t ON ( pm.transaction_id = t.id ) LEFT JOIN ".TP."company AS firm ON ( pm.partner_id = firm.company_id ) LEFT JOIN ".TP."company AS c ON ( t.company_id = c.company_id ) WHERE member_id = '".$member_id."' ORDER BY t.date_of_deal DESC LIMIT 0 , ".$num_deals;
        
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        /////////////////////////
        $data_count = mysql_num_rows($res);
        if(0 == $data_count){
            //no deals done by this member
            return true;
        }
        ///////////////////////////
        for($i=0;$i<$data_count;$i++){
            $data_arr[$i] = mysql_fetch_assoc($res);
            $data_arr[$i]['firm_name'] = $g_mc->db_to_view($data_arr[$i]['firm_name']);
            $data_arr[$i]['deal_company_name'] = $g_mc->db_to_view($data_arr[$i]['deal_company_name']);
            if(($data_arr[$i]['deal_subcat1_name']!="")&&($data_arr[$i]['deal_subcat1_name']!="n/a")){
                if($data_arr[$i]['deal_subcat1_name']!=$data_arr[$i]['deal_cat_name']){
                    $data_arr[$i]['deal_cat_name'].=", ".$data_arr[$i]['deal_subcat1_name'];
                }
            }
            if(($data_arr[$i]['deal_subcat2_name']!="")&&($data_arr[$i]['deal_subcat2_name']!="n/a")){
                $data_arr[$i]['deal_cat_name'].=", ".$data_arr[$i]['deal_subcat2_name'];
            }
        }
        return true;
    }
    /****
    function to get all the deals of this member.
    Used in profile edit, so see all deal in which the member participated
    *************/
    public function front_get_deals_of_member_paged($member_id,$num_to_fetch,$start_offset,&$data_arr,&$data_count){
        global $g_mc;
        $q = "SELECT t.date_of_deal,t.id as deal_id, t.deal_cat_name,t.deal_subcat1_name,t.deal_subcat2_name, t.value_in_billion, pm.designation, firm.name AS firm_name, c.name AS deal_company_name,c.logo,c.company_id as deal_company_id FROM ".TP."transaction_partner_members AS pm LEFT JOIN ".TP."transaction AS t ON ( pm.transaction_id = t.id ) LEFT JOIN ".TP."company AS firm ON ( pm.partner_id = firm.company_id ) LEFT JOIN ".TP."company AS c ON ( t.company_id = c.company_id ) WHERE member_id = '".$member_id."' ORDER BY t.date_of_deal DESC LIMIT ".$start_offset." , ".$num_to_fetch;
        
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        /////////////////////////
        $data_count = mysql_num_rows($res);
        if(0 == $data_count){
            //no deals done by this member
            return true;
        }
        ///////////////////////////
        for($i=0;$i<$data_count;$i++){
            $data_arr[$i] = mysql_fetch_assoc($res);
            $data_arr[$i]['firm_name'] = $g_mc->db_to_view($data_arr[$i]['firm_name']);
            $data_arr[$i]['deal_company_name'] = $g_mc->db_to_view($data_arr[$i]['deal_company_name']);
            if(($data_arr[$i]['deal_subcat1_name']!="")&&($data_arr[$i]['deal_subcat1_name']!="n/a")){
                if($data_arr[$i]['deal_subcat1_name']!=$data_arr[$i]['deal_cat_name']){
                    $data_arr[$i]['deal_cat_name'].=", ".$data_arr[$i]['deal_subcat1_name'];
                }
            }
            if(($data_arr[$i]['deal_subcat2_name']!="")&&($data_arr[$i]['deal_subcat2_name']!="n/a")){
                $data_arr[$i]['deal_cat_name'].=", ".$data_arr[$i]['deal_subcat2_name'];
            }
        }
        return true;
    }
    
    /****
    sng:22/nov/2010
    function to get all the deals of this member.
    Used by admin to get the deals of a ghost member and then remove the ghost member from that deal
    *************/
    public function admin_get_deals_of_member_paged($member_id,$num_to_fetch,$start_offset,&$data_arr,&$data_count){
        global $g_mc;
        $q = "SELECT t.date_of_deal,t.id as deal_id, t.deal_cat_name,t.deal_subcat1_name,t.deal_subcat2_name, t.value_in_billion, pm.designation,pm.partner_id,firm.name AS firm_name, c.name AS deal_company_name,c.logo,c.company_id as deal_company_id FROM ".TP."transaction_partner_members AS pm LEFT JOIN ".TP."transaction AS t ON ( pm.transaction_id = t.id ) LEFT JOIN ".TP."company AS firm ON ( pm.partner_id = firm.company_id ) LEFT JOIN ".TP."company AS c ON ( t.company_id = c.company_id ) WHERE member_id = '".$member_id."' ORDER BY t.date_of_deal DESC LIMIT ".$start_offset." , ".$num_to_fetch;
        
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        /////////////////////////
        $data_count = mysql_num_rows($res);
        if(0 == $data_count){
            //no deals done by this member
            return true;
        }
        ///////////////////////////
        for($i=0;$i<$data_count;$i++){
            $data_arr[$i] = mysql_fetch_assoc($res);
            $data_arr[$i]['firm_name'] = $g_mc->db_to_view($data_arr[$i]['firm_name']);
            $data_arr[$i]['deal_company_name'] = $g_mc->db_to_view($data_arr[$i]['deal_company_name']);
            if(($data_arr[$i]['deal_subcat1_name']!="")&&($data_arr[$i]['deal_subcat1_name']!="n/a")){
                if($data_arr[$i]['deal_subcat1_name']!=$data_arr[$i]['deal_cat_name']){
                    $data_arr[$i]['deal_cat_name'].=", ".$data_arr[$i]['deal_subcat1_name'];
                }
            }
            if(($data_arr[$i]['deal_subcat2_name']!="")&&($data_arr[$i]['deal_subcat2_name']!="n/a")){
                $data_arr[$i]['deal_cat_name'].=", ".$data_arr[$i]['deal_subcat2_name'];
            }
        }
        return true;
    }
    
    /***
    sng: 26/apr/2010
    function to create deal tombstone image.
    we really do not create image, but create a html div element
    We may get transaction id, or deal data, so we name our method accordingly
    
    sng:29/apr/2010
    we also take the company name so that if the logo is blank, the company name is used in the tombstone
    ********/
    public function get_tombstone_from_deal_id($deal_id, $returnRow = false, $withFavorites = false){
        //get the details
        $q = "select t.id as deal_id,t.value_range_id,vrm.display_text as fuzzy_value, value_in_billion,date_of_deal,deal_cat_name,deal_subcat1_name,deal_subcat2_name,c.logo,c.name as company_name, t.logos from ".TP."transaction as t left join ".TP."company as c on(t.company_id=c.company_id) left join ".TP."transaction_value_range_master as vrm on (t.value_range_id=vrm.value_range_id) where t.id='".$deal_id."'";
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        $cnt = mysql_num_rows($res);
        if(0==$cnt){
            //no such deal
            return false;
        }
        $row = mysql_fetch_assoc($res);
        if ($returnRow == true){
            return $row;
        }
        $isTombstoneFavorite = $this->savedSearches->tombstoneIsFavorite($deal_id);
        $this->get_tombstone_from_deal_data($row['logo'],$row['company_name'],$row['deal_cat_name'],$row['deal_subcat1_name'],$row['deal_subcat2_name'],$row['value_in_billion'],$row['value_range_id'],$row['fuzzy_value'],$row['date_of_deal'], $withFavorites, $isTombstoneFavorite, $row['deal_id'], $row['logos']);
        return true;
    }

    public function get_tombstone_from_deal_ids($deal_ids){
        //get the details
        $q = "
        SELECT t.id AS deal_id, value_in_billion, date_of_deal, deal_cat_name, deal_subcat1_name, deal_subcat2_name, c.logo, c.name AS company_name, t.logos
        FROM ".TP."transaction AS t
        LEFT JOIN ".TP."company AS c ON ( t.company_id = c.company_id )
        WHERE t.id IN ( " . join(',', $deal_ids).  ")
        LIMIT 0 , 30 ";
        //echo $q;
        //$q = "select t.id as deal_id, value_in_billion,date_of_deal,deal_cat_name,deal_subcat1_name,deal_subcat2_name,c.logo,c.name as company_name, t.logos from ".TP."transaction as t left join ".TP."company as c on(t.company_id=c.company_id) where t.id='".$deal_id."'";
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        $cnt = mysql_num_rows($res);
        if(0==$cnt){
            //no such deal
            return false;
        }
        $ret = false;
        while($row = mysql_fetch_assoc($res)) {
            $ret[] = $row;
        }
        
        return $ret;
    }
        
    public function get_tombstone_from_deal_data($logo,$company_name,$deal_cat_name,$deal_subcat1_name,$deal_subcat2_name,$value_in_billion,$value_range_id ,$fuzzy_value,$date_of_deal, $withfavorites = false, $isTombstoneFavorite = false, $dealId,$logos = ""){
        global $g_http_path;
        ?>
        <table class="tombstone_display" style="clear:both"> 
                    <?php if ($withfavorites)  : ?>
                       <tr> 
                         <td>   
                         <?php 
                         $favoriteIcon =  $isTombstoneFavorite ? "favorite" : "not-favorite";
                         ?>
                         <a href="#" onclick="return updateFavoriteStatus(<?php echo "$dealId" ?>)">
                            <img src="images/<?php echo $favoriteIcon ?>.png" style="float:right; padding-right:10px;padding-top:10px;" id="favStat<?php echo $dealId?>"/>
                         </a>
                         </td>
                       </tr>
                    <?php endif ?>            
            <tr>
                <td class="tombstone_company" onclick="goto_deal_detail(<?php echo $dealId?>)" style="cursor: pointer; text-align: center;" id="logo-<?php echo $dealId?>">
                <a href="deal_detail.php?deal_id=<?php echo $dealId;?>" style="text-decoration:none; cursor:pointer;<?php /*clear:both; width: 100%; height: 100%; width:200px; display:block;*/?>" onclick="goto_deal_detail(<?php echo $dealId;?>)" >
                    <?php
					/*******************************************************************************************************
					sng:23/feb/2012
					Now we have multiple participants and each company has its own logo.
					Since there is no concept of 'default' or 'main' company, we show the first logo.
					
					For multiple logos, we number the logos as logo-<deal_id>-0,1,2 etc and a name attribute
					which store the file name.
					This way, when we show the tombstone, we can cycle through the logos
					and as we change the ordinal number, we get the filename and store it as preferred logo
					for this deal
					
					We also get the preferred logos by the member. It may happen that the member is not logged.
					In that case we get blank
					
					sng:24/feb/2012
					We also need support for 'download to powerpoint'. Since the logo can come from multiple sources,
					what we do is echo a hidden field with deal id and file name. Of course, if we cycle through logos for
					a tombstone, we will have to update this field.
					************************************/
					require_once("classes/class.transaction_company.php");
					require_once("classes/class.deal_support.php");
					$trans_com = new transaction_company();
					$deal_support = new deal_support();
					
					$logo_file_shown = "";
					
					$participants_logos = array();
					//a default blank array
					$ok = $trans_com->get_deal_participants_logos($dealId,$participants_logos);
					//even if there is error, we have a blank array
					$logo_count = count($participants_logos);
					$chosenLogos = $deal_support->get_user_chosen_logos();
					/****************
					Admin can also load logos for a deal via edit deal. We need those also. However
					we do not use the 'default' flag for those.
					**************************/
					$deal_logos = unserialize($logos);
					if (is_array($deal_logos) && sizeOf($deal_logos)) {
						$deal_logo_count = count($deal_logos);
						$next_index = $logo_count;
						for($i=0;$i<$deal_logo_count;$i++){
							
							$participants_logos[$next_index] = array('logo'=>$deal_logos[$i]['fileName']);
							$next_index++;
						}
					}
					//since there could be addition, we update the count
					
					$logo_count = count($participants_logos);
					
					if($logo_count > 0){
						for($logo_i=0;$logo_i<$logo_count;$logo_i++){
							$display = 'none';
							if(isset($chosenLogos[$dealId])){
								//we have a preferred logo for this deal, it is this one?
								if($chosenLogos[$dealId]==$participants_logos[$logo_i]['logo']){
									//yes, show
									$display = 'block';
									$logo_file_shown = $participants_logos[$logo_i]['logo'];
								}else{
									//not this one, we wait
								}
							}else{
								//we do not have a preferred logo. Is this the 0th one?
								if(0==$logo_i){
									$display = 'block';
									$logo_file_shown = $participants_logos[$logo_i]['logo'];
								}
							}
							?><img src="<?php echo LOGO_IMG_URL;?>/<?php echo $participants_logos[$logo_i]['logo'];?>" name="<?php echo $participants_logos[$logo_i]['logo'];?>"  style="margin:0 auto;display:<?php echo $display?>" id="logo-<?php echo $dealId; ?>-<?php echo $logo_i;?>"  align="middle"/><?php
						}
						
					}else {
                        if ($logo=="") {
							/*********************
							sng:2/mar/2012
							Now we do not have a single company for a deal. So the old concept of
							showing the company name no longer works.
							We might have to show a no image logo
							echo $company_name;
							
							sng:3/mar/2012
							In the logo/thumb folder we have a no image logo. We ue that
							*************************/
                           $logo = "no_logo_warning_logo.png";
						   echo '<img src="'.LOGO_IMG_URL.'/' . $logo .'" />'; 
						   $logo_file_shown = $logo;
                        } else {
                           echo '<img src="'.LOGO_IMG_URL.'/' . $logo .'" />'; 
						   $logo_file_shown = $logo;
                        }
                    }
					
                    
                    ?>
                    </a>
                </td>
            </tr>
			<?php
			/******************
			sng:24/feb/2012
			support for 'download to ppt
			*******************/
			?>
			<input type="hidden" name="<?php echo $dealId ?>" value="<?php echo $logo_file_shown;?>" id="thumb-<?php echo $dealId ?>" class="thumb-val"/>
            <tr>
                <td style="width: 40px; text-align: center;" align="center">
                <?php if ($logo_count > 1) : ?>
                    <img src="images/left.png" alt="See previous" width="16" height="16" style="cursor:pointer;" onclick="return showPrevious(<?php echo $dealId ?>);" />
                    <img src="images/right.png" alt="See next" width="16" height="16" style="cursor:pointer;" onclick="return showNext(<?php echo $dealId ?>);"/>
                    
                    <?php endif ?> &nbsp;
                </td>
            </tr>
			<?php
			/*************
			sng:23/feb/2012
			end multi logo support
			***************************************************************************************/
			?>
            <tr>
                <td class="tombstone_deal" onclick="goto_deal_detail(<?php echo $dealId?>)" style="cursor: pointer;">
                    <?php
                    $deal_type = $deal_cat_name;
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
                    $deal_value = round($value_in_billion*1000,2);
                    ?>
                    <?php echo $deal_type;?><br /><br />
                    <?php
                    /***
                    sng:10/jul/2010
                    deal value may be unknon or undisclosed. In that case, 0 is stored.
                    if that is the case then show 'not disclosed'
					
					sng:24/jan/2012
					Now we have deal range if deal value is not known
					Of course, if both are 0 it means, value is not known, exact or otherwise.
					However, if we have exact deal value, that takes priority.
                    *********/
                    if(($deal_value == 0)&&($value_range_id==0)){
                        echo "Not disclosed";
                    }elseif($deal_value > 0){
                        ?>
                        US $ <?php echo $deal_value;?> million
                        <?php
                    }else{
						echo $fuzzy_value;
					}
                    ?>
                    <br /><br />
                    <?php echo date("F Y",strtotime($date_of_deal));?>
                </td>
            </tr>
        </table>
        <?php
    }
    
    /***
    get the list of suggeasted deals for admin
	
	sng:1/july/2011
	Now we have more detailed suggestion table. Since the table holds entries for a new deal suggestion and entries for correction on an existing deal
	we check for that. New deal suggestion has deal id 0
    ***/
    public function get_deal_suggestion_paged($start_offset,$num_to_fetch,&$data_arr,&$data_count){
        global $g_mc;
        
        $q = "select id,deal_company_name,value_in_million,date_announced,date_closed,deal_cat_name,deal_subcat1_name,deal_subcat2_name,date_suggested,m.f_name,m.l_name,m.designation,w.name as work_company from ".TP."transaction_suggestions as s left join ".TP."member as m on(s.suggested_by=m.mem_id) left join ".TP."company as w on(m.company_id=w.company_id) where deal_id='0' order by date_suggested desc limit ".$start_offset.",".$num_to_fetch;
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        $data_count = mysql_num_rows($res);
        if(0 == $data_count){
            return true;
        }
        ////////////////////////////////////////
        for($i=0;$i<$data_count;$i++){
            $data_arr[$i] = mysql_fetch_assoc($res);
            $data_arr[$i]['deal_company_name'] = $g_mc->db_to_view($data_arr[$i]['deal_company_name']);
        }
        return true;
    }
    /***************************************
    get the detail of the suggested deal
	
	sng:1/july/2011
	This is now complete rewrite. We get suggestion from tombstone_transaction_suggestions where deal_id=0 for new
	deal suggestion.
	Also, the banks and lawfirms are now obtained from tombstone_transaction_suggestion_partners, with a new field is_sellside_advisor
	
	NO LONGER IN USE, i THINK, SINCE NOW SUBMISSION FROM FRONT END CREATE THE DEAL DIRECTLY
    ********************************************/
    public function get_deal_suggestion_detail($id,&$data){
        global $g_mc;
        $q = "SELECT s.*, m.f_name,m.l_name,m.designation,w.name as work_company,t.takeover_name,'banks' as `banks`,'law_firms' as `law_firms` from ".TP."transaction_suggestions as s left join ".TP."member as m on(s.suggested_by=m.mem_id) left join ".TP."company as w on(m.company_id=w.company_id) left join ".TP."takeover_type_master as t on(s.takeover_id=t.takeover_id) where s.id='".$id."'";
        
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        $data = mysql_fetch_assoc($res);
        //////////////////////////////////////////////////
		/*****************
		sng:17/jun/2011
		now we get the is_sellside_advisor flag also
		***********************/
        //banks data
        $data['banks'] = array();
		
        $q_bank = "select * from ".TP."transaction_suggestions_partners where suggestion_id='".$id."' AND partner_type='bank'";
        $q_bank_res = mysql_query($q_bank);
        if(!$q_bank_res){
            return false;
        }
        ///////////////////////////
        $q_bank_res_count = mysql_num_rows($q_bank_res);
        for($bank_i=0;$bank_i<$q_bank_res_count;$bank_i++){
            $data['banks'][$bank_i] = mysql_fetch_assoc($q_bank_res);
        }
        //////////////////////////////////////////////////////////
        //law firm data
        $data['law_firms'] = array();
		
		$q_law = "select * from ".TP."transaction_suggestions_partners where suggestion_id='".$id."' AND partner_type='law firm'";
        $q_law_res = mysql_query($q_law);
        if(!$q_law_res){
            return false;
        }
        ///////////////////////////
        $q_law_res_count = mysql_num_rows($q_law_res);
        for($law_i=0;$law_i<$q_law_res_count;$law_i++){
            $data['law_firms'][$law_i] = mysql_fetch_assoc($q_law_res);
        }
		/***********************************************
		sng:1/sep/2011
		There might be one or more files along with this new deal suggestion.
		NOTE: we only fetch those that are yet to be associated with a deal
		
		sng:22/feb/2012
		we now use method in transaction_doc to get the docs associated with a transaction suggestion
		**************************************************/
		require_once("classes/class.transaction_doc.php");
		$trans_doc = new transaction_doc();
		
		$data['docs'] = NULL;
		$temp_count = 0;
		
		$ok = $trans_doc->front_get_all_documents_for_deal_suggestion($id,$data['docs'],$temp_count);
		if(!$ok){
			return false;
		}
        return true;
    }
    
	/********************************************
	sng:1/july/2011
	We now store the deal suggestions in tombstone_transaction_suggestions and the corresponding banks and law firms in
	tombstone_transaction_suggestions_partners
	
	sng:1/sep/2011
	do not delete any unaccepted files (for the suggestion)
	********************/
    public function reject_suggested_deal($id,&$msg){
        $q = "delete from ".TP."transaction_suggestions_partners where suggestion_id='".$id."'";
        $result = mysql_query($q);
        if(!$result){
            return false;
        }
		
		$q = "delete from ".TP."transaction_suggestions where id='".$id."'";
        $result = mysql_query($q);
        if(!$result){
            return false;
        }
        $msg = "deleted";
        return true;
    }
	
	/*******************************
	sng:1/july/2011
	We now allow admin to delete the suggestion from the deal suggestion detail popup via ajax.
	Admin may see that the deal suggestion is almost duplicate, so may delete it or may take data from it
	and create new deal. In that case, admin has the option of marking the suggestion as accepted
	
	accepted: y or n
	Right now, ignore
	
	sng:1/sep/2011
	do not delete any unaccepted files (for the suggestion)
	*****************/
	public function ajax_delete_suggested_deal($id,$accepted){
		
		$q = "delete from ".TP."transaction_suggestions_partners where suggestion_id='".$id."'";
        $result = mysql_query($q);
        if(!$result){
            return false;
        }
		
		$q = "delete from ".TP."transaction_suggestions where id='".$id."'";
        $result = mysql_query($q);
        if(!$result){
            return false;
        }
		return true;
	}
	/**************************
	sng:22/feb/2012
	ajax_accept_deal_suggestion_file($file_id,$transaction_id,&$msg)
	has been moved to classes/class.transaction_doc.php
	*****************************/
    ///////////////////////////////////////////////////
    private function company_id_from_name($company_name,$company_type,&$company_id,&$found){
        //since this is internal, we assume that caller has magic quoted the name
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
    ///////////////////////////////////////////////////////////
    /***
    sng:7/may/2010
    an ugly function to create a deal from a deal suggestion from member
    
	sng:1/july/2011
	No longer needed. Now there are so many fields, we just show the suggestion in a popup and admin just manually enter the data
    ***********/
    public function accept_deal_suggestion($deal_suggestion_id,$suggestion_data_arr,&$deal_suggestion_accepted){
        return false;
    }
    
    public function ajax_mark_deal_as_error($deal_id,$reported_by,$reporting_date,$report,&$msg){
        //for a deal, there can be multiple reports
        global $g_mc;
        //validation
        //check if this member has reported this deal or not
        $q = "select id from ".TP."transaction_error_reports where deal_id='".$deal_id."' and reported_by='".$reported_by."'";
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        $cnt = mysql_num_rows($res);
        if($cnt > 0){
            $msg = "You have already reported this deal";
            return true;
        }
        if($report==""){
            $msg = "Please specify the error";
            return true;
        }
        $q = "insert into ".TP."transaction_error_reports set deal_id='".$deal_id."',reported_by='".$reported_by."',reporting_date='".$reporting_date."',report='".$g_mc->view_to_db($report)."'";
        $result = mysql_query($q);
        if(!$result){
            return false;
        }
        return true;
    }
    /****************
	sng:20/mar/2012
	Now we no longer need this function. Members now send corrections on all
	aspect of a deal
	function ajax_make_suggestion_on_deal($deal_id,$reported_by,$reporting_date,$suggestion,&$msg)
	************************/
    
    
    public function get_error_deals_paged($start_offset,$num_to_fetch,&$data_arr,&$data_count){
        global $g_mc;
		/************************************
		sng:28/jun/2011
		We now allow the members to specify corrections for each fields of a deal. This means, we no longer
		use tombstone_transaction_error_reports and no longer show a simple report for a deal and who posted it.
	
		Also, there can be more than one corrections suggested for a deal. What we do is, show only the deal that
		has one or more corrections and allow admin to edit the deal. In the edit page we show the corrections and who posted it.

        $q = "select r.report,r.id as report_id,r.deal_id,t.company_id,value_in_billion,date_of_deal,deal_cat_name,deal_subcat1_name,deal_subcat2_name,c.name as deal_company_name,m.f_name,m.l_name,m.designation,w.name as work_company from ".TP."transaction_error_reports as r left join ".TP."transaction as t on(r.deal_id=t.id) left join ".TP."company as c on(t.company_id=c.company_id) left join ".TP."member as m on(r.reported_by=m.mem_id) left join ".TP."company as w on(m.company_id=w.company_id) order by t.id desc limit ".$start_offset.",".$num_to_fetch;
		******************************************/
		$q = "select t.id as deal_id,t.company_id,value_in_billion,date_of_deal,deal_cat_name,deal_subcat1_name,deal_subcat2_name,c.name as deal_company_name from (SELECT DISTINCT deal_id FROM ".TP."transaction_suggestions WHERE deal_id != '0') as r left join ".TP."transaction as t on(r.deal_id=t.id) left join ".TP."company as c on(t.company_id=c.company_id) order by t.id desc limit ".$start_offset.",".$num_to_fetch;
		
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        //////////////////
        $data_count = mysql_num_rows($res);
        if(0==$data_count){
            return true;
        }
        /////////////////
        for($i=0;$i<$data_count;$i++){
            $data_arr[$i] = mysql_fetch_assoc($res);
            $data_arr[$i]['deal_company_name'] = $g_mc->db_to_view($data_arr[$i]['deal_company_name']);
            
        }
        return true;
    }
    
    public function get_suggested_notes_on_deals_paged($start_offset,$num_to_fetch,&$data_arr,&$data_count){
		/*********************
		sng:22/sep/2011
		NO LONGER USED. In the new deal detail page, we send the note to transaction_suggestion
		***********************/
        global $g_mc;
        $q = "select r.suggestion,r.id as note_id,r.deal_id,t.company_id,value_in_billion,date_of_deal,deal_cat_name,deal_subcat1_name,deal_subcat2_name,c.name as deal_company_name,m.f_name,m.l_name,m.designation,w.name as work_company from ".TP."transaction_note_suggestions as r left join ".TP."transaction as t on(r.deal_id=t.id) left join ".TP."company as c on(t.company_id=c.company_id) left join ".TP."member as m on(r.reported_by=m.mem_id) left join ".TP."company as w on(m.company_id=w.company_id) order by t.id desc limit ".$start_offset.",".$num_to_fetch;
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        //////////////////
        $data_count = mysql_num_rows($res);
        if(0==$data_count){
            return true;
        }
        /////////////////
        for($i=0;$i<$data_count;$i++){
            $data_arr[$i] = mysql_fetch_assoc($res);
            $data_arr[$i]['deal_company_name'] = $g_mc->db_to_view($data_arr[$i]['deal_company_name']);
            $data_arr[$i]['suggestion'] = nl2br($g_mc->db_to_view($data_arr[$i]['suggestion']));
            $data_arr[$i]['f_name'] = $g_mc->db_to_view($data_arr[$i]['f_name']);
            $data_arr[$i]['l_name'] = $g_mc->db_to_view($data_arr[$i]['l_name']);
            $data_arr[$i]['work_company'] = $g_mc->db_to_view($data_arr[$i]['work_company']);
        }
        return true;
    }
    
    public function delete_deal_error_report($report_id){
        $q = "delete from ".TP."transaction_error_reports where id='".$report_id."'";
        $result = mysql_query($q);
        if(!$result){
            return false;
        }
        return true;
    }
    /********************
	sng:20/mar/2012
	We now have the concept of members sending corrections for deal.
	We will use some other way to delete suggestions for notes
	function delete_note_suggested_on_deal($note_id)
	***********************/
    
    /****
    sng:21/may2010
    The note is in different table with same transaction id
    if the id is found, update, else insert
	
	sng:17/jun/2011
	Made this public. We will access this from other points also. We also use mysql_real_escape_string instead of magic quote
	
	sng:30/apr/2012
	we need to notify suggestion that we are adding a deal and this is the original submission for note.
	We need two extra arguments - mem id who added the deal and the date of addition
    **********/
    public function update_note($deal_id,$member_id,$deal_added_on,$note){
        
        $q = "select count(*) as cnt from ".TP."transaction_note where transaction_id='".$deal_id."'";
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        $row = mysql_fetch_assoc($res);
        if(0==$row['cnt']){
            //not found, insert
            $note_q = "insert into ".TP."transaction_note set transaction_id='".$deal_id."', note='".mysql_real_escape_string($note)."'";
        }else{
            $note_q = "update ".TP."transaction_note set note='".mysql_real_escape_string($note)."' where transaction_id='".$deal_id."'";
        }
        $result = mysql_query($note_q);
        if(!$result){
            return false;
        }
		
		require_once("classes/class.transaction_suggestion.php");
		$trans_suggest = new transaction_suggestion();
		$ok = $trans_suggest->note_added_via_deal_submission($deal_id,$member_id,$deal_added_on,$note);
		/*********
		never mind if error
		**********/
        return true;
    }
	/**************************
	sng:27/apr/2012
	We need a way to allow members to add to the note.
	************************/
	public function front_append_to_note($deal_id,$note){
        $db = new db();
		
        $q = "select note from ".TP."transaction_note where transaction_id='".$deal_id."'";
        $ok = $db->select_query($q);
		if(!$ok){
			return false;
		}
		if(!$db->has_row()){
			//not found, insert
			$note_q = "insert into ".TP."transaction_note set transaction_id='".$deal_id."', note='".mysql_real_escape_string($note)."'";
		}else{
			//there is existing note, we get the note and append the suggestion to it and store
			$row = $db->get_row();
			$curr_note = $row['note'];
			$new_note = $curr_note."\r\n".$note;
			$note_q = "update ".TP."transaction_note set note='".mysql_real_escape_string($new_note)."' where transaction_id='".$deal_id."'";
		}
		
		
        $ok = $db->mod_query($note_q);
        if(!$ok){
            return false;
        }
        return true;
    }
	/****
    sng:4/feb/2011
    The private note is in different table with same transaction id
    if the id is found, update, else insert
	
	sng:17/jun/2011
	Made this public, will access from other points
    **********/
    public function update_private_note($deal_id,$note){
        global $g_mc;
        $q = "select count(*) as cnt from ".TP."transaction_private_note where transaction_id='".$deal_id."'";
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        $row = mysql_fetch_assoc($res);
        if(0==$row['cnt']){
            //not found, insert
            $note_q = "insert into ".TP."transaction_private_note set transaction_id='".$deal_id."', note='".$g_mc->view_to_db($note)."'";
        }else{
            $note_q = "update ".TP."transaction_private_note set note='".$g_mc->view_to_db($note)."' where transaction_id='".$deal_id."'";
        }
        $result = mysql_query($note_q);
        if(!$result){
            return false;
        }
        return true;
    }
    
    /****
    sng:08/jul/2010
    The sources is in different table with same transaction id
    if the id is found, update, else insert
	
	sng:17/jun/2011
	Made this public, will access from other points
    **********/
    public function update_sources($deal_id,$data){
        die("do not use");
        $q = "select count(*) as cnt from ".TP."transaction_sources where transaction_id='".$deal_id."'";
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        $row = mysql_fetch_assoc($res);
        if(0==$row['cnt']){
            //not found, insert
            $source_q = "insert into ".TP."transaction_sources set transaction_id='".$deal_id."', sources='".mysql_real_escape_string($data)."'";
        }else{
            $source_q = "update ".TP."transaction_sources set sources='".mysql_real_escape_string($data)."' where transaction_id='".$deal_id."'";
        }
        $result = mysql_query($source_q);
        if(!$result){
            return false;
        }
        return true;
    }
    /**n
    * @desc Get tombstones for specific firms based on POST
    * 
    * @param integer $firmId
    * @param integer $start
    * @param integer $end
    */
    /**********
    sng:9/oct/2010
    the sector and industry is not a property of transaction. It is property of the company that is doing the deals.
    So when we select sector and industry, we are searching for deals done by companies in that sector and industry.
	
	sng:12/nov/2011
	There is only one code, showcase_firm.php that use the min/max query to show the size slider.
	Since I am removing it, no need to clutter this class. 
    ***************/
    public function getTombstonesForFirm($firmId, $start = 0, $end = 60) {
        $tablePrefix = TP;
        $ret = array();
        
        /***
        country and region
        if country is specified, region is overridden
        **/
        $country_filter = "";
        if($_POST['country'] != ""){
            /*******************************************************
            sng:30/Nov/2010
            No more the country of the HQ of the company doing the deal. Now use deal_country (which is a csv)
            $country_filter = "c.hq_country='".$search_data['country']."'";
            ***********/
            $country_filter = " AND t.deal_country LIKE '%".$_POST['country']."%'";
            /*******************************************/
        }else{
            if($_POST['region'] != ""){
                //get the country names for this region name
                $region_q = "select cm.name from ".TP."region_master as rm left join ".TP."region_country_list as rc on(rm.id=rc.region_id) left join ".TP."country_master as cm on(rc.country_id=cm.id) where rm.name='".$_POST['region']."'";
                $region_q_res = mysql_query($region_q);
                //echo "<div style='display:none'><pre>$region_q</pre></div>"; 
                if(!$region_q_res){
                    return false;
                }

                $region_q_res_cnt = mysql_num_rows($region_q_res);
                $region_clause = "";
                if($region_q_res_cnt > 0){
                    while($region_q_res_row = mysql_fetch_assoc($region_q_res)){
                        $region_clause.="|t.deal_country LIKE '%".$region_q_res_row['name']."%'";
                    }
                    $region_clause = substr($region_clause,1);
                    $region_clause = str_replace("|"," OR ",$region_clause);
                    $country_filter = "(".$region_clause.")";
                }
                $country_filter = " AND  $country_filter";
                 //echo "<div style='display:none'><pre>$country_filter</pre></div>";  
                /************************************************************************/
            }
        }
        
        //echo "<div style='display:none'><pre>$country_filter</pre></div>";
                
        /****
        sng:9/oct/2010
        We have 'and' for both country and region/countries now, so this is not needed
        if($country_filter!=""){
            $country_filter = " and ".$country_filter;
        }
        ****/
        /*****
        sng:9/oct/2010
        Now that we have the companies filtered by country/region, let us check the sector and industry
        **********/
        if($_POST['sector']!=""){
            $country_filter .= " and t.deal_sector like '%".$_POST['sector']."%'";
        }
        
        if($_POST['industry']!=""){
            $country_filter .= " and t.deal_industry like '%".$_POST['industry']."%'";
        }

        /**
        * Post data is not received as we want to so we take care of translating
        * post vars into valid field names
        **/
        /*****
        sng:9/oct/2010
        These are for transaction attribute. Sector and industry are part of company and the query clause are in $country_filter.
        So we remove these two from here
        ****
        $translations = array(
          'deal_cat_name' => 'deal_cat_name',
          'deal_subcat1_name' => 'deal_subcat1_name',
          'deal_subcat2_name' => 'deal_subcat2_name',
          'sector' => 'target_sector',
          'industry' => 'target_industry',
          'year' => 'date_of_deal',
        );
        ***/
        $translations = array(
          'deal_cat_name' => 'deal_cat_name',
          'deal_subcat1_name' => 'deal_subcat1_name',
          'deal_subcat2_name' => 'deal_subcat2_name',
          'year' => 'date_of_deal',
        );
        $dataReceived = array();
        if (isset($_POST['myaction']) && 'filter' == $_POST['myaction'] ) {
            foreach ($_POST as $key=>$postElement) {
                /*
                 *   if the data from post is not in the translation array
                 *    just drop it
                 */
                if (isset($translations[$key]) && !empty($postElement)) {
                   $dataReceived[$translations[$key]] = $postElement;
                }
            }
        }
        
        $where = "";
        $where .= $country_filter;
        foreach ($dataReceived as $field=>$value) {
            if ($field != 'date_of_deal')
                $where .= " AND $field = '$value'";
            else {
                $year = explode("-",$value);
                $year_count = count($year);
                if($year_count == 1){
                    //single year
                    $where .= sprintf(" AND YEAR(t.date_of_deal) = '%d'", $year[0] );
                }
                if($year_count == 2){
                    //range year
                    $where .= sprintf("and year(t.date_of_deal)>='%d' AND year(t.date_of_deal)<='%d'", $year[0], $year[1] ) ;
                }            
            }
        }
        
        //$where .= $country_filter;
		/**********
		sng:12/nov/2011
		if tie, show the deal entered later
		*****************/
        $order = ' ORDER by t.date_of_deal desc,t.id DESC';
        
       // $join = " LEFT JOIN {$tablePrefix}favorite_tombstones"
        if('' != $_POST['number_of_deals']){
            //top:num /recent:num /all:favorites
            $order_tokens = explode(":",$_POST['number_of_deals']);
            if($order_tokens[0] == "top"){
                $end = $order_tokens[1];
                $order  = " ORDER BY value_in_billion desc,t.id DESC";
            }
            if($order_tokens[0] == "recent"){
                $end = $order_tokens[1];
                $order = " ORDER BY date_of_deal desc,t.id DESC";
            }
            
            /**
            * Ugly hack for favorites
            * TODO : find another way to handle this
            */
			/********************************
			sng:12/nov/2011
			We now use a checkbox and send value of y via show_favourites
            if($_POST['number_of_deals'] == "all:favorites"){
                $favoriteList = $this->get_favorite_tombstones($_SESSION['mem_id'], true);
                if ($favoriteList)
                    $where .= " AND t.id IN  ($favoriteList)";
            }
			************************************/ 
        }
		if(isset($_POST['show_favourites'])&&('y'==$_POST['show_favourites'])){
			$favoriteList = $this->get_favorite_tombstones($_SESSION['mem_id'], true);
			if ($favoriteList){
				$where .= " AND t.id IN  ($favoriteList)";
			}
		}        
        /***********************************************************************************
		sng:3/feb/2011
		I changes the condition to include the boundaries also.
		Also, when converting to billion, not changing to int since it makes 3.1 billion to 3
		
		sng:12/nov/2011
		Now we send deal_size whose values are like >=xxx (in billion) etc
		**********/
         /**else {
            
            * 2011-02-04 iMihai
            * We wanna hide the undisclosed transactions
            
            $where .= " AND value_in_billion >  0";
        }
        */ 
		/****************************************************************
		sng:12/nov/2011
		filter by deal_size. The value is either blank or like >=deal value in billion or <=deal value in billion
		
		sng:12/nov/2011
		Once we get into the concept of deal size, we filter out deals with undisclosed value (stored as 0.0)
		
		sng:15/nov/2011
		Assuming that the user is not searching for deals with undisclosed value. If user is searching for Undisclosed
		we show only deals whose value is 0.0
		***/
		if($_POST['deal_size']!=""){
			if($_POST['deal_size']=="0.0"){
				$where.=" and value_in_billion=0.0";
			}else{
				$where.=" and value_in_billion".$_POST['deal_size']." and t.value_in_billion!=0.0";
			}
        }
		/*******************************************************************
		sng: 20/jan/2012
		Now we have value range id for deal search.
		**********/
		if($_POST['value_range_id']!=""){
			if($_POST['value_range_id']=="0"){
				$where.=" and value_in_billion=0.0 AND value_range_id=0";
			}else{
				$where.=" and value_range_id='".$_POST['value_range_id']."'";
			}
        }
        /******************end sng:3/feb/2011**********************************************************/
		/************
		sng:2/mar/2012
		We only get active deals
		***************/
        $q = "SELECT transaction_id from {$tablePrefix}transaction_partners AS p 
                LEFT JOIN {$tablePrefix}transaction as t on(p.transaction_id=t.id) 
                LEFT JOIN {$tablePrefix}company AS c ON ( t.company_id = c.company_id )
                WHERE t.is_active='y' AND partner_id='$firmId' $where  
                $order
                limit $start,$end  ";
        //echo "<div style='display:none'><pre> $q </pre></div>";        
        
        //$q = sprintf($q,$firmId,$start, $end);
        //echo "<div style='display:none'><pre> $q $start $end</pre></div>";
        //echo "<div style='display:none'> <pre> " . $q . "</pre></div>";
        //echo "<div style='display:none'> <pre> " . $minMaxQ . "</pre></div>";
        //echo "<div style='display:none'><pre>$q</pre></div>";
        $res = mysql_query( $q );
        $_SESSION['tombToken'] = base64_encode($q);
        if(!$res){
            return false;
        }
        while ($row = mysql_fetch_assoc($res)) {
          $ret[] = $row;  
        }
        return $ret;
    }
    
    public function get_favorite_tombstones($userid, $returnList = false) {
        $tablePrefix = TP;
        $ret = array();
        $q = "SELECT  tombstone_id FROM {$tablePrefix}favorite_tombstones WHERE member_id = $userid";
        $res = mysql_query($q);
        if (!$res) 
            return false;
        while ($row = mysql_fetch_assoc($res)) {
            $ret[] = $row['tombstone_id'];
        }
        if ($returnList)
         return implode(", ", $ret);
        else 
         return $ret;
    }
    /***
    sng:26/may/2010
    a function to get the id of special deals for a bank or law firm.
    The ids are then used to get the tombstones.
    Right now, we get the latest deals in which the firm was a partner
	
	sng:2/mar/2012
	Do not include inactive deals
    *******/
    public function get_showcase_deal_ids_of_firm($firm_id,$num_to_fetch,&$data_arr,&$data_count){
        $q = "select transaction_id from ".TP."transaction_partners as p left join ".TP."transaction as t on(p.transaction_id=t.id) where t.is_active='y' AND partner_id='".$firm_id."' order by t.date_of_deal desc limit 0,".$num_to_fetch;
        $res = mysql_query($q);
        if(!$res){
            //echo mysql_error();
            return false;
        }
        //////////////////////
        $data_count = mysql_num_rows($res);
        if(0==$data_count){
            return true;
        }
        ////////////////////
        for($i=0;$i<$data_count;$i++){
            $data_arr[$i] = mysql_fetch_assoc($res);
        }
        return true;
    }
    
    /***
    sng:22/july/2010
    Alsthough this looks like get_showcase_deal_ids_of_firm but this gets the recent deals and is not to be changed
    *********/
    public function get_recent_deal_ids_of_firm($firm_id,$num_to_fetch,&$data_arr,&$data_count){
        return false;
		/************
		sng:2/mar/2012
		not used anywhere
		**************/
    }
    
    /***
    5/jun/2010
    a function to suggest deals to member during registration
    the user can select one or more deal categories
    Get the deals of last 3 years
    ********/
    public function suggest_deal_during_registration($firm_id,$deal_cat_arr,&$data_arr,&$data_count){
        return false;
		/***********
		sng:2/mar/2012
		not used anywhere
		****************/
    }
    
    /****
    sng:12/jun/2010
    
    sng: 7/jul/2010
    The odering by data of transaction in descending order is ok but what happens when the dates are same? It seems that the ordering then is random. So we
    add another tie breaker - the order in which the deals were entered
    *****/
    public function front_get_recent_deals_of_firm($firm_id,$num_to_fetch,&$data_arr,&$data_count){
        return false;
		/******************
		sng:2/mar/2012
		not used anywhere
		******************/
    }
    
    /****
    sng:7/jul/2010
    function to get the deals for a firm. This is just like the front_get_recent_deals_of_firm. However
    this gets all the deals and has pagination support.
    
    The odering by data of transaction in descending order is ok but what happens when the dates are same? It seems that the ordering then is random. So we
    add another tie breaker - the order in which the deals were entered
    
    sng:10/jul/2010
    This function has to support filter conditions
    *****/
    public function front_get_all_deals_of_firm_paged($firm_id,$filter_arr,$start_offset,$num_to_fetch,&$data_arr,&$data_count){
        global $g_mc;
        $q = "select c.company_id,c.name,t.id,t.value_in_billion,t.date_of_deal,t.deal_cat_name,t.deal_subcat1_name,t.deal_subcat2_name,target_company_name from ".TP."transaction_partners as p left join ".TP."transaction as t on(p.transaction_id=t.id) left join ".TP."company as c on(t.company_id=c.company_id) where partner_id='".$firm_id."'";
        
        //filter on transaction types
        if($filter_arr['deal_cat_name']!=""){
            $q.=" and deal_cat_name='".$filter_arr['deal_cat_name']."'";
        }
        if($filter_arr['deal_subcat1_name']!=""){
            $q.=" and deal_subcat1_name='".$filter_arr['deal_subcat1_name']."'";
        }
        if($filter_arr['deal_subcat2_name']!=""){
            $q.=" and deal_subcat2_name='".$filter_arr['deal_subcat2_name']."'";
        }
        /***
        The year can be in a range like 2009-2010 or it may be a single like 2009
        *******/
        if($filter_arr['year']!=""){
            $year_tokens = explode("-",$filter_arr['year']);
            $year_tokens_count = count($year_tokens);
            if($year_tokens_count == 1){
                //singleton year
                $q.=" and year(date_of_deal)='".$year_tokens[0]."'";
            }
            if($year_tokens_count == 2){
                //range year
                $q.=" and year(date_of_deal)>='".$year_tokens[0]."' AND year(date_of_deal)<='".$year_tokens[1]."'";
            }
        }
        /***
        sng:23/july/2010
        filter deal_size. The value is either blank or like >=deal value in billion or <=deal value in billion
        ***/
        if($filter_arr['deal_size']!=""){
            $q.=" and value_in_billion".$filter_arr['deal_size'];
        }
        /*********************************************************************************
        sng:4/dec/2010
        we no longer use the country of the company. We use the deal_country or transaction.
        Same for sector and industry
        ****************/
        $country_filter = "";
        if($filter_arr['country']!=""){
            //country specified, we do not consider region
            $country_filter.="deal_country LIKE '%".$filter_arr['country']."%'";
        }else{
            //country not specified, check for region
            if($filter_arr['region']!=""){
                //get the country names for this region name
                $region_q = "select cm.name from ".TP."region_master as rm left join ".TP."region_country_list as rc on(rm.id=rc.region_id) left join ".TP."country_master as cm on(rc.country_id=cm.id) where rm.name='".$filter_arr['region']."'";
                $region_q_res = mysql_query($region_q);
                if(!$region_q_res){
                    return false;
                }
                
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
        
        if($filter_arr['sector']!=""){
            $q.=" and deal_sector like '%".$filter_arr['sector']."%'";
        }
        if($filter_arr['industry']!=""){
            $q.=" and deal_industry like '%".$filter_arr['industry']."%'";
        }
        /**********************************************************************************/
        ///////////////////////////////////////////
        //filter on company of the transaction
        $company_filter = "";
        $company_filter_clause = "";
        /***************************************************************************************
        sng:4/dec/2010
        we no longer use the country of the company. We use the deal_country or transaction.
        Same for sector and industry
        
        if($filter_arr['country']!=""){
            $company_filter_clause.=" and hq_country='".$filter_arr['country']."'";
        }else{
            //hq country not specified, so we can check for region
            if($filter_arr['region']!=""){
                $company_filter_clause.=" and hq_country IN (SELECT cm.name FROM ".TP."region_master AS rm LEFT JOIN ".TP."region_country_list AS rcl ON ( rm.id = rcl.region_id ) LEFT JOIN ".TP."country_master AS cm ON ( rcl.country_id = cm.id ) WHERE rm.name = '".$filter_arr['region']."')";
            }
        }
        
        if($filter_arr['sector']!=""){
            $company_filter_clause.=" and sector='".$filter_arr['sector']."'";
        }
        
        if($filter_arr['industry']!=""){
            $company_filter_clause.=" and industry='".$filter_arr['industry']."'";
        }
        ********************************************************************************************/
        if($company_filter_clause != ""){
            $company_filter.=" and t.company_id IN (select company_id from ".TP."company where 1=1".$company_filter_clause.")";
        }
        
        //////////////////////

        if($company_filter!=""){
            $q.=$company_filter;
        }
        
        $q.=" order by t.date_of_deal desc, t.id DESC limit ".$start_offset.",".$num_to_fetch;
        //echo $q;
        $res = mysql_query($q);
        if(!$res){
            //echo mysql_error();
            return false;
        }
        //////////////////////
        $data_count = mysql_num_rows($res);
        if(0==$data_count){
            return true;
        }
        ////////////////////
        for($i=0;$i<$data_count;$i++){
            $data_arr[$i] = mysql_fetch_assoc($res);
            $data_arr[$i]['name'] = $g_mc->db_to_view($data_arr[$i]['name']);
            $data_arr[$i]['target_company_name'] = $g_mc->db_to_view($data_arr[$i]['target_company_name']);
            //set bankers and law firms
            $transaction_id = $data_arr[$i]['id'];
            $data_arr[$i]['banks'] = array();
            $data_cnt = 0;
            $success = $this->get_all_partner($transaction_id,"bank",$data_arr[$i]['banks'],$data_cnt);
            if(!$success){
                return false;
            }
            ///////////////////////////
            $data_arr[$i]['law_firms'] = array();
            $data_cnt = 0;
            $success = $this->get_all_partner($transaction_id,"law firm",$data_arr[$i]['law_firms'],$data_cnt);
            if(!$success){
                return false;
            }
        }
        return true;
    }
    
    /***************************
    sng:24/july/2010
    admin utility to get the deals that are missing certain data.
    The function accepts the missing data name and build the query accordingly
	
	sng:14/sep/2011
	Since the code now search for deals using the deal_country/deal_sector/deal_industry, we want admin to find any deals
	that is missing this info
    **********/
    public function get_all_deals_missing_info_paged($missing_info_name,$start_offset,$num_to_fetch,&$data_arr,&$data_count){
        global $g_mc;
        
        $q = "SELECT t.id,t.value_in_billion,t.date_of_deal,deal_cat_name,deal_subcat1_name,deal_subcat2_name,c.name as company_name FROM ".TP."transaction AS t LEFT JOIN ".TP."company AS c ON ( t.company_id = c.company_id )";
        
        if($missing_info_name == "target_sector"){
            $q.=" where deal_cat_name='M&A' and target_sector=''";
        }else if($missing_info_name == "target_country"){
            $q.=" where deal_cat_name='M&A' and target_country=''";
        }else if($missing_info_name == "source"){
            //this require a different approach
            //there may not be source record for the deal or, the source may be empty
            $q.= " left join ".TP."transaction_sources AS s ON ( t.id = s.transaction_id ) where sources IS NULL or sources=''";
        }else if($missing_info_name == "deal_country"){
			$q.=" where deal_country IS NULL or deal_country=''";
		}else if($missing_info_name == "deal_sector"){
			$q.=" where deal_sector IS NULL or deal_sector=''";
		}else if($missing_info_name == "deal_industry"){
			$q.=" where deal_industry IS NULL or deal_industry=''";
		}
        
        $q.=" limit ".$start_offset.",".$num_to_fetch;
        
        $res = mysql_query($q);
        if(!$res){
            //die(mysql_error());
            return false;
        }
        $data_count = mysql_num_rows($res);
        if(0==$data_count){
            return true;
        }
        while($row = mysql_fetch_assoc($res)){
            $row['company_name'] = $g_mc->db_to_view($row['company_name']);
            $data_arr[] = $row;
        }
        return true;
    }
    /*********
	sng:14/feb/2012
	If you add any new angle, update this
	**************/
    public function delete_transaction($transaction_id){
        //delete any note suggestions for the deal
		/***********
		sng:30/apr/2012
		We now have transaction_note_suggestions
		**************/
        $q = "delete from ".TP."transaction_note_suggestions where deal_id='".$transaction_id."'";
        $success = mysql_query($q);
        if(!$success){
            return false;
        }
        /**************************************************************/
        //delete any notes for the deal
        $q = "delete from ".TP."transaction_note where transaction_id='".$transaction_id."'";
        $success = mysql_query($q);
        if(!$success){
            return false;
        }
        /**************************************************************/
        //delete any error reports for the deal
        $q = "delete from ".TP."transaction_error_reports where deal_id='".$transaction_id."'";
        $success = mysql_query($q);
        if(!$success){
            return false;
        }
        /*****************************************************************/
        //delete any sources for the deal
        $q = "delete from ".TP."transaction_sources where transaction_id='".$transaction_id."'";
        $success = mysql_query($q);
        if(!$success){
            return false;
        }
        /*******************************************************************/
        //delete partner members assiciated with this deal
        $q = "delete from ".TP."transaction_partner_members where transaction_id='".$transaction_id."'";
        $success = mysql_query($q);
        if(!$success){
            return false;
        }
        /*************************************************************************/
        //delete partner banks/law firms assiciated with this deal
        $q = "delete from ".TP."transaction_partners where transaction_id='".$transaction_id."'";
        $success = mysql_query($q);
        if(!$success){
            return false;
        }
        /*************************************************************************/
        //delete the transaction/tombstone from tombstone_favorite_tombstones
        //tombstone_id is same as transaction id
        $q = "delete from ".TP."favorite_tombstones where tombstone_id='".$transaction_id."'";
        $success = mysql_query($q);
        if(!$success){
            return false;
        }
        /******************************************************************************/
        /**********
        sng:22/sep/2010
        since there can be multiple logos for a transaction, need to delete the images first
        the logos field has the multiple logo images in serialised form
        an array each of which is an array (fileName, default)
        
        also, if those logos are chosen, those have to be deleted [tombstone_chosen_logos ]
        since this is a serialised list, how to update?
        This part should be handled by Mihai
        ************/
        $q = "select logos from ".TP."transaction where id='".$transaction_id."'";
        $res = mysql_query($q);
        if($res){
            $cnt = mysql_num_rows($res);
            if($cnt > 0){
                $row = mysql_fetch_assoc($res);
                $logos = $row['logos'];
                if($logos!=NULL){
                    $logos_arr = unserialize($logos);
                    $logos_cnt = count($logos_arr);
                    if($logos_cnt > 0){
                        foreach($logos_arr as $key=>$value){
                            @unlink(FILE_PATH."uploaded_img/logo/".$value['fileName']);
                            @unlink(FILE_PATH."uploaded_img/logo/thumbnails/".$value['fileName']);
                        }
                    }
                }
            }
        }
        /***************************************
        sng:22/nov/2010
        Now it can happen that press release entry may store the deal id if that press release item talks about this deal
        So, before deleting this deal, we will set the deal id in press release to blank (because we cannot delete the press release, that is
        independent of deal)
        **********/
        $q = "update ".TP."press_releases set deal_id='0' where deal_id='".$transaction_id."'";
        $success = mysql_query($q);
        if(!$success){
            return false;
        }
        /************************************************************
		sng:1/july/2011
		Delete any private note for the deal
        ******************/
        $q = "delete from ".TP."transaction_private_note where transaction_id='".$transaction_id."'";
        $success = mysql_query($q);
        if(!$success){
            return false;
        }
		/*************************************************************
		sng:1/july/2011
		Delete any discussion for this deal
		************/
		$q = "delete from ".TP."transaction_discussion where transaction_id='".$transaction_id."'";
        $success = mysql_query($q);
        if(!$success){
            return false;
        }
		/************************************************************
		sng:1/july/2011
		Delete any case studies for the deal. Before deleting the case study, get the filename and delete it first
		*****************************/
		$q = "select filename from ".TP."transaction_case_studies where transaction_id='".$transaction_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		while($row = mysql_fetch_assoc($res)){
			$filename = $row['filename'];
			if(($filename!="")&&file_exists(FILE_PATH."case_studies/".$filename)){
				unlink(FILE_PATH."case_studies/".$filename);
			}
		}
		//now delete the records
		$q = "delete from ".TP."transaction_case_studies where transaction_id='".$transaction_id."'";
        $success = mysql_query($q);
        if(!$success){
            return false;
        }
		/**********************************************************
		sng:1/july/2011
		Delete any transaction correction for this deal. Before deleting it, delete the correcponding records
		from transaction suggestion partner
		*************/
		$q = "select id from ".TP."transaction_suggestions where deal_id!='0' AND deal_id='".$transaction_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		while($row = mysql_fetch_assoc($res)){
			$suggestion_id = $row['id'];
			$del_q = "delete from ".TP."transaction_suggestions_partners where suggestion_id='".$suggestion_id."'";
			$success = mysql_query($del_q);
        	if(!$success){
            	return false;
        	}
		}
		//now delete the records
		$q = "delete from ".TP."transaction_suggestions where deal_id!='0' AND deal_id='".$transaction_id."'";
        $success = mysql_query($q);
        if(!$success){
            return false;
        }
		/**********************************************************************
		sng:1/july/2011
		Delete the transaction extra detail for the deal
		********************/
		$q = "delete from ".TP."transaction_extra_detail where transaction_id='".$transaction_id."'";
        $success = mysql_query($q);
        if(!$success){
            return false;
        }
		/************************************************************/
        //now delete the transaction
        $q = "delete from ".TP."transaction where id='".$transaction_id."'";
        $success = mysql_query($q);
        if(!$success){
            return false;
        }
        /************************************************************************/
        return true;
    }
    /*******************************************************************
    sng:29/sep/2010
    Deal date range support
    ********/
    public function admin_get_all_date_range(&$data_arr,&$data_count){
        global $g_mc;
        $q = "select * from ".TP."date_range_master";
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
    public function admin_add_deal_date($data_arr,&$validation_passed,&$err_arr){
        
        global $g_mc;
        
        $validation_passed = true;
        if($data_arr['name']==""){
            $validation_passed = false;
            $err_arr['name'] = "Please specify name";
        }else{
            //check if duplicate
            $q = "select count(*) as cnt from ".TP."date_range_master where name='".$g_mc->view_to_db($data_arr['name'])."'";
            $res = mysql_query($q);
            if(!$res){
                return false;
            }
            $row = mysql_fetch_assoc($res);
            if(0==$row['cnt']){
                //not found
            }else{
                //found
                $validation_passed = false;
                $err_arr['name'] = "This name exists";
            }
        }
        //From must be specified
        if($data_arr['date_from']==""){
            $validation_passed = false;
            $err_arr['date_from'] = "Please specify date from";
        }
        //To can be blank
        
        
        //if both specified, the To must be greater than From
        if(($data_arr['date_from']!="")&&($data_arr['date_to']!="")){
            if($data_arr['date_to'] <= $data_arr['date_from']){
                $validation_passed = false;
                $err_arr['date_to'] = "This must be greater than From";
            }
        }
        if(!$validation_passed){
            //no need to proceed
            return true;
        }
        //all ok
        $q = "insert into ".TP."date_range_master set name='".$g_mc->view_to_db($data_arr['name'])."',date_from='".$data_arr['date_from']."',date_to='".$data_arr['date_to']."'";
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        //inserted
        return true;
    }
    public function admin_get_deal_date($id,&$data_arr){
        global $g_mc;
        $q = "select * from ".TP."date_range_master where id='".$id."'";
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        $data_count = mysql_num_rows($res);
        if($data_count == 0){
            //no such recs, problem
            return false;
        }
        //recs so
        $data_arr = mysql_fetch_assoc($res);
        $data_arr['name'] = $g_mc->db_to_view($data_arr['name']);
        return true;
    }
    public function admin_edit_deal_date($id,$data_arr,&$validation_passed,&$err_arr){
        global $g_mc;
        
        $validation_passed = true;
        if($data_arr['name']==""){
            $validation_passed = false;
            $err_arr['name'] = "Please specify name";
        }else{
            //check if duplicate. other than this
            $q = "select count(*) as cnt from ".TP."date_range_master where name='".$g_mc->view_to_db($data_arr['name'])."' and id!='".$id."'";
            $res = mysql_query($q);
            if(!$res){
                return false;
            }
            $row = mysql_fetch_assoc($res);
            if(0==$row['cnt']){
                //not found
            }else{
                //found
                $validation_passed = false;
                $err_arr['name'] = "This name exists";
            }
        }
        //From must be specified
        if($data_arr['date_from']==""){
            $validation_passed = false;
            $err_arr['date_from'] = "Please specify date from";
        }
        //To can be blank
        
        
        //if both specified, the To must be greater than From
        if(($data_arr['date_from']!="")&&($data_arr['date_to']!="")){
            if($data_arr['date_to'] <= $data_arr['date_from']){
                $validation_passed = false;
                $err_arr['date_to'] = "This must be greater than From";
            }
        }
        if(!$validation_passed){
            //no need to proceed
            return true;
        }
        //all ok
        $q = "update ".TP."date_range_master set name='".$g_mc->view_to_db($data_arr['name'])."',date_from='".$data_arr['date_from']."',date_to='".$data_arr['date_to']."' where id='".$id."'";
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        //updated
        return true;
    }
    public function admin_delete_deal_date($id){
        
        $q = "delete from ".TP."date_range_master where id='".$id."'";
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        return true;
    }
    
    public function front_get_all_date_range(&$data_arr,&$data_count){
        global $g_mc;
        $q = "select * from ".TP."date_range_master order by date_from,date_to";
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
    
	
	/***************************CASE STUDY FOR DEALS*************************************************/
	/****************************************
	sng:17/nov/2011
	Need to get the accepted file extensions
	***************************/
	public function file_extensions_for_case_studies(){
		$filetype_arr = array();
		$filetype_arr['ppt'] = "PowerPoint";
		$filetype_arr['pptx'] = "PowerPoint";
		$filetype_arr['pdf'] = "PDF file";
		return $filetype_arr;
	}
	/*****************************************
	sng:18/nov/2011
	Need to get the access rules
	*********************/
	public function access_rules_for_case_studies(){
		$rules_arr = array();
		$rules_arr[] = array('rule_code'=>'','rule_name'=>"All registered members",'is_default'=>1);
		$rules_arr[] = array('rule_code'=>'only_my_firm','rule_name'=>"Only the members of my firm",'is_default'=>0);
		$rules_arr[] = array('rule_code'=>'my_firm_and_data_provider','rule_name'=>"The members of my firm and data providers/journalists",'is_default'=>0);
		
		return $rules_arr;
	}
	/************
	sng:7/mar/2011
	add a case study
	mem_id: id of the member who posted the case study. For admin, this is 0
	$is_approved y or n
	
	sng:18/nov/2011
	We now need to store the date on which the case study was uploaded.
	*********/
	public function add_case_study_via_file($deal_id,$mem_id,$partner_id,$partner_type,$caption,$access_rule_code,$file_field_name,$file_destination_path,$is_approved,&$validation_passed,&$err_arr){
		$validation_passed = true;
		
		//validation
		if($partner_id == ""){
			$validation_passed = false;
			$err_arr['partner_id'] = "Please specify the partner";
		}
		if($caption == ""){
			$validation_passed = false;
			$err_arr['caption'] = "Please specify a caption";
		}
		if($_FILES[$file_field_name]['name']==""){
			$validation_passed = false;
			$err_arr['filename'] = "Please specify the file";
		}else{
			/*******************************************************************
			sng:17/Nov/2011
			filename specified, get the extension and check against the list
			of accepted extensions
			***********/
			$file_extension = get_file_extension($_FILES[$file_field_name]['name']);
			$valid_filetypes = $this->file_extensions_for_case_studies();
			if(!array_key_exists($file_extension,$valid_filetypes)){
				$validation_passed = false;
				$err_arr['filename'] = "Files of this type is not accepted";
			}
		}
		if(!$validation_passed){
			return true;
		}
		//basic validation passed passed, now check if the firm was in the deal
		$q = "select count(*) as cnt from ".TP."transaction_partners where transaction_id='".$deal_id."' and partner_id='".$partner_id."' and partner_type='".$partner_type."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$row = mysql_fetch_assoc($res);
		if(0 == $row['cnt']){
			//not found, this firm did not worked in this deal
			$validation_passed = false;
			$err_arr['partner_id'] = "This firm was not involved in the deal";
		}
		//check again
		if(!$validation_passed){
			return true;
		}
		//validation passed
		//be careful with space in file name
		$upload_file_name = time()."_".clean_filename(basename($_FILES[$file_field_name]['name']));
		$upload_path = $file_destination_path."/".$upload_file_name;
		$upload_src = $_FILES[$file_field_name]['tmp_name'];
		$success = move_uploaded_file($upload_src,$upload_path);
		if(!$success){
			return false;
		}
		//file uploaded, insert data
		$q = "insert into ".TP."transaction_case_studies set transaction_id='".$deal_id."',mem_id='".$mem_id."',partner_id='".$partner_id."',partner_type='".$partner_type."',caption='".$caption."',access_rule_code='".$access_rule_code."',filename='".$upload_file_name."',uploaded_on='".date('Y-m-d')."',is_approved='".$is_approved."'";
		$result = mysql_query($q);
		if(!$result){
			//echo mysql_error();
			return false;
		}
		//data inserted
		/*******************************
		sng:18/nov/2011
		Notify admin that a case study has been uploaded. Problem is, we have many admins.
		So let us send the email to a site email. We cannot use the curent admin id because
		now admin no longer upload case study. This code is triggered by front end.
		*******************************/
		
		require_once("classes/class.sitesetup.php");
		global $g_site;
		$site_emails = NULL;
		$success = $g_site->get_site_emails($site_emails);
		if(!$success){
			//do not bother
			return true;
		}
		$to = $site_emails['contact_email'];
		$from = $site_emails['mem_related_email'];
		
		$headers = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/plain;charset=iso-8859-1' . "\r\n";
		$headers .= "From: ".$from."\r\n";
		
		$subject = "data-cx.com case study uploaded";
		
		
		$msg = "A case study has been uploaded. The details are:\r\n";
		$msg.="Caption: ".$caption."\r\n";
		$msg.="Filename: ".$upload_file_name."\r\n";
		
		require_once("classes/class.mailer.php");
		$mailer = new mailer();
		
		$to = $work_email;
		/**********
		sng:18/nov/2011
		Ignore mailer exception
		**************/
		try{
			$mailer->mail($to,$subject,$msg);
		}catch(Exception $e){}
		return true;
	}
	/******************************************
	sng:19/nov/2011
	Members can flag a case study. They also specify the reason but we do not
	make the reason mandatory.
	We store the entry and update the flag counter for the case study
	Then we fire a mail to admin.
	Also, a member can flag a case study more than once.
	********************************/
	public function flag_case_study($case_study_id,$member_id,$reason){
	
		$q = "insert into ".TP."transaction_case_studies_disputes set case_study_id='".$case_study_id."',
		mem_id='".$member_id."',
		date_flagged='".date("Y-m-d")."',
		flag_reason='".mysql_real_escape_string($reason)."'";
		
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		//inserted, now update flag count
		$q = "update ".TP."transaction_case_studies set flag_count=flag_count+1 where case_study_id='".$case_study_id."'";
		mysql_query($q);
		//never mind if this fails
		/*******************************
		Notify admin that a case study has been flagged. Problem is, we have many admins.
		So let us send the email to a site email. We cannot use the curent admin id because
		now admin no longer upload case study. This code is triggered by front end.
		*******************************/
		
		require_once("classes/class.sitesetup.php");
		global $g_site;
		$site_emails = NULL;
		$success = $g_site->get_site_emails($site_emails);
		if(!$success){
			//do not bother
			return true;
		}
		$to = $site_emails['contact_email'];
		$from = $site_emails['mem_related_email'];
		
		$headers = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/plain;charset=iso-8859-1' . "\r\n";
		$headers .= "From: ".$from."\r\n";
		
		$subject = "data-cx.com case study flagged";
		
		
		$msg = "A case study has been flagged. Please login to admin panel to review it.\r\n";
		
		require_once("classes/class.mailer.php");
		$mailer = new mailer();
		
		$to = $work_email;
		/**********
		sng:18/nov/2011
		Ignore mailer exception
		**************/
		try{
			$mailer->mail($to,$subject,$msg);
		}catch(Exception $e){}
		
		return true;
	}
	
	public function get_all_case_studies_for_partner_type($transaction_id,$type,&$data_arr,&$data_count){
		global $g_mc;
		$q = "select cs.*,c.name as company_name,m.f_name,m.l_name from ".TP."transaction_case_studies as cs left join ".TP."company as c on(cs.partner_id=c.company_id) left join ".TP."member as m on(cs.mem_id=m.mem_id) where cs.transaction_id='".$transaction_id."' and cs.partner_type='".$type."' order by company_name";
		$res = mysql_query($q);
        if(!$res){
            return false;
        }
		$data_count = mysql_num_rows($res);
        if(0 == $data_count){
            //no data to return so
            return true;
        }
		for($i=0;$i<$data_count;$i++){
            $data_arr[$i] = mysql_fetch_assoc($res);
            $data_arr[$i]['company_name'] = $g_mc->db_to_view($data_arr[$i]['company_name']);
			$data_arr[$i]['f_name'] = $g_mc->db_to_view($data_arr[$i]['f_name']);
			$data_arr[$i]['l_name'] = $g_mc->db_to_view($data_arr[$i]['l_name']);
        }
        return true;
	}
	/************************
	sng:18/nov/2011
	We rename the function to front_get_case_studies
	public function front_get_case_studies_for_partner($transaction_id,$partner_id,&$data_arr,&$data_count){
	******************************/
	public function front_get_case_studies($transaction_id,&$data_arr,&$data_count){
		global $g_mc;
		
		/**********
		sng:11/june/2011
		We now see the extension of the file and send the type, to be shown in frontend. This way, if I want the case study for
		a presentation, I download the powerpoint version, if I want to email, I download the pdf
		
		We use a lookup array to map the extensions to types
		In the query, we put a dummy field
		
		We could have updated the table and the insertion code but ok, a minor point
		
		sng:11/nov/2011
		allow to see all case studies for the deal, never mind who uploaded it.
	
		sng:17/nov/2011
		Now we have a function to return the list of extensions. 
		
		sng:17/nov/2011
		We no longer need admin's approval to show the case studies in data-cx.
		****************/
		$filetype_arr = $this->file_extensions_for_case_studies();
		
		//$q = "select *,'unknown' as file_type from ".TP."transaction_case_studies where transaction_id='".$transaction_id."' and partner_id='".$partner_id."' and is_approved='y'";
		//$q = "select *,'unknown' as file_type from ".TP."transaction_case_studies where transaction_id='".$transaction_id."' and is_approved='y'";
		$q = "select *,'unknown' as file_type from ".TP."transaction_case_studies where transaction_id='".$transaction_id."'";
		
		$res = mysql_query($q);
        if(!$res){
            return false;
        }
		$data_count = mysql_num_rows($res);
        if(0 == $data_count){
            //no data to return so
            return true;
        }
		$i = 0;
		for($j=0;$j<$data_count;$j++){
			/**********************
			sng:18/nov/2011
			We now have access rule code. We filter according to that. Just a hack
			****************/
			$temp = mysql_fetch_assoc($res);
			$access_rule_code = $temp['access_rule_code'];
			
			if($access_rule_code == "only_my_firm"){
				//only visible to members of the firm who uploaded this case study
				if($_SESSION['company_id']!=$temp['partner_id']){
					//the viewer is a member of another firm, so skip
					continue;
				}
			}
			
			if($access_rule_code == "my_firm_and_data_provider"){
				//only visible to members of the firm or data partners
				if($_SESSION['company_id']!=$temp['partner_id']){
					//is this a data partner
					if($_SESSION['member_type']!="data partner"){
						//nope, so skip
						continue;
					}
				}
			}
            $data_arr[$i] = $temp;
			/**********
			sng:11/jun/2011
			get extension, search for the last dot, extract substring
			use lookup array to get the stype
			***********/
			$pos = strrpos($data_arr[$i]['filename'],".");
			if($pos === false){
				$data_arr[$i]['file_type'] = "Unknown";
			}else{
				$ext = substr($data_arr[$i]['filename'],$pos+1,strlen($data_arr[$i]['filename']));
				if(!array_key_exists($ext,$filetype_arr)){
					$data_arr[$i]['file_type'] = "Unknown";
				}else{
					$data_arr[$i]['file_type'] = $filetype_arr[$ext];
				}
			}
			$i++;
        }
		/*****************
		sng:18/nov/2011
		This is the filtered count
		*****************/
		$data_count = $i;
		return true;
	}
	
	public function delete_case_study($case_study_id){
		/******************
		sng:19/nov/2011
		since we can flag a case study and send feedbacks, we need to clear those
		before we delete the case study
		****************/
		$success = $this->clear_flagged_case_study($case_study_id);
		if(!$success){
			return false;
		}
		//get the case study file
		$q = "select filename from ".TP."transaction_case_studies where case_study_id='".$case_study_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$cnt = mysql_num_rows($res);
		if(0 == $cnt){
			//no such case study, this should not happen
			return false;
		}
		$row = mysql_fetch_assoc($res);
		$filename = $row['filename'];
		unlink(CASE_STUDY_PATH."/".$filename);
		//now delete the record
		$q = "delete from ".TP."transaction_case_studies where case_study_id='".$case_study_id."'";
		$result = mysql_query($q);
		if(!$result){
			return false;
		}
		return true;
	}
	
	/************************************
	sng:19/nov/2011
	Get the feedbacks for all the flags for a case study
	***************************/
	public function case_study_flag_details($case_study_id,&$result_arr,&$result_count){
		$q = "select date_flagged,flag_reason,m.f_name,m.l_name,m.designation,w.name as work_company from ".TP."transaction_case_studies_disputes as d  left join ".TP."member as m on(d.mem_id=m.mem_id) left join ".TP."company as w on(m.company_id=w.company_id) where case_study_id='".$case_study_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$result_count = mysql_num_rows($res);
		if(0==$result_count){
			//no data
			return true;
		}
		for($i=0;$i<$result_count;$i++){
			$result_arr[$i] = mysql_fetch_assoc($res);
		}
		return true;
	}
	
	/********************
	sng:19/nov/2011
	Case studies can be flagged and the reasons are stored in database. Admin need a way
	to remove those and set flag count to 0
	********************/
	public function clear_flagged_case_study($case_study_id){
		$q = "delete from ".TP."transaction_case_studies_disputes where case_study_id='".$case_study_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		//not reset the flag counter
		$q = "update ".TP."transaction_case_studies set flag_count=0 where case_study_id='".$case_study_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		return true;
	}
	
	public function approve_case_study($case_study_id){
		$q = "update ".TP."transaction_case_studies set is_approved='y' where case_study_id='".$case_study_id."'";
		$result = mysql_query($q);
		if(!$result){
			return false;
		}
		return true;
	}
	
	/******************
	sng:17/nov/2011
	Called by funcitons in the front end when a case study is downloaded. We increase the download count
	**********************/
	public function front_case_study_downloaded($case_study_id){
		$q = "update ".TP."transaction_case_studies set download_count=download_count+1 where case_study_id='".$case_study_id."'";
		$result = mysql_query($q);
		if(!$result){
			return false;
		}
		return true;
	}
	
	public function reject_case_study($data,&$case_study_rejected,&$err){
		global $g_mc;
		$case_study_id = $data['case_study_id'];
		//get the member data of the user
		$q = "select caption,f_name,work_email from ".TP."transaction_case_studies as c left join ".TP."member as m on(c.mem_id=m.mem_id) where case_study_id='".$case_study_id."'";
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$row = mysql_fetch_assoc($res);
		$f_name = $g_mc->db_to_view($row['f_name']);
		$work_email = $row['work_email'];
		$case_study_caption = $g_mc->db_to_view($row['caption']);
		
		require_once("classes/class.account.php");
		global $g_account;
		$admin_id = $_SESSION['admin_id'];
		$admin_data = array();
		$success = $g_account->get_email_of_admin($admin_id,$admin_data);
		if(!$success){
			return false;
		}
		$admin_email = $admin_data['email'];
		
		$headers = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/plain;charset=iso-8859-1' . "\r\n";
		$headers .= "From: ".$admin_email."\r\n";
		
		$subject = "deal-data.com case study";
		
		$msg = "Dear ".$f_name.",\r\n\r\n";
		
		$msg = "Your case study was rejected. The reason is\r\n";
		$msg.=$g_mc->view_to_view($data['reason']);
		
		require_once("classes/class.mailer.php");
		$mailer = new mailer();
		
		$to = $work_email;
		$mailer->mail($to,$subject,$msg);
		
		//delete the case study and email a reason to the member who posted it.
		$this->delete_case_study($case_study_id);
		
		$case_study_rejected = true;
		$err = "";
		return true;
	}
	
	public function is_firm_associated_with_deal($deal_id,$deal_partner_firm_id,&$is_associated){
        //check if the partner company is actually associated with the deal
        $q = "select count(*) as cnt from ".TP."transaction_partners where transaction_id='".$deal_id."' and partner_id='".$deal_partner_firm_id."'";
        $res = mysql_query($q);
        if(!$res){
            return false;
        }
        $row = mysql_fetch_assoc($res);
        if($row['cnt'] == 0){
            //this partner firm was not found for the deal
            $is_associated = false;
		}else{
			$is_associated = true;
		}
		return true;
	}
	
	public function get_suggested_case_studies_on_deals_paged($start_offset,$num_to_fetch,&$data_arr,&$data_count){
        global $g_mc;
        $q = "select cs.case_study_id,cs.caption,cs.filename,t.company_id,value_in_billion,date_of_deal,deal_cat_name,deal_subcat1_name,deal_subcat2_name,c.name as deal_company_name,f.name as partner_name,m.f_name,m.l_name from ".TP."transaction_case_studies as cs left join ".TP."transaction as t on(cs.transaction_id=t.id) left join ".TP."company as f on(cs.partner_id=f.company_id) left join ".TP."company as c on(t.company_id=c.company_id) left join ".TP."member as m on(cs.mem_id=m.mem_id) where is_approved='n' order by cs.case_study_id desc limit ".$start_offset.",".$num_to_fetch;
        $res = mysql_query($q);
        if(!$res){
			//echo mysql_error();
            return false;
        }
        //////////////////
        $data_count = mysql_num_rows($res);
        if(0==$data_count){
            return true;
        }
        /////////////////
        for($i=0;$i<$data_count;$i++){
            $data_arr[$i] = mysql_fetch_assoc($res);
            $data_arr[$i]['deal_company_name'] = $g_mc->db_to_view($data_arr[$i]['deal_company_name']);
            $data_arr[$i]['partner_name'] = $g_mc->db_to_view($data_arr[$i]['partner_name']);
			$data_arr[$i]['f_name'] = $g_mc->db_to_view($data_arr[$i]['f_name']);
			$data_arr[$i]['l_name'] = $g_mc->db_to_view($data_arr[$i]['l_name']);
        }
        return true;
    }
	
	/****************************
	sng:17/nov/2011
	get all case studies. In data-cx, we no longer require admin to approve case studies
	
	sng:18/nov/2011
	Well, on second thought, allow admin to approve. That will show 'approved' icon on front end
	We also show the date on which the case study was uploaded, and how many times it has been downloaded.
	We also show the flagged count
	We also show the access rules. This is a hack
	
	sng:9/dec/2011
	we need to filter and order
	filterby: flagged - show only flagged case studies
	orderby: downloaded - order by number of times downloaded
	orderby: date_uploaded - order by date of upload
	*****************************/
	public function get_all_case_studies_paged($filterby,$orderby,$start_offset,$num_to_fetch,&$data_arr,&$data_count){
        global $g_mc;
        $q = "select cs.case_study_id,cs.caption,cs.filename,cs.uploaded_on,cs.access_rule_code,cs.is_approved,cs.download_count,cs.flag_count,t.company_id,value_in_billion,date_of_deal,deal_cat_name,deal_subcat1_name,deal_subcat2_name,c.name as deal_company_name,f.name as partner_name,m.f_name,m.l_name from ".TP."transaction_case_studies as cs left join ".TP."transaction as t on(cs.transaction_id=t.id) left join ".TP."company as f on(cs.partner_id=f.company_id) left join ".TP."company as c on(t.company_id=c.company_id) left join ".TP."member as m on(cs.mem_id=m.mem_id) where 1=1";
		
		if($filterby=="flagged"){
			$q.=" and cs.flag_count > 0";
		}
		
		if($orderby=="downloaded"){
			$order_clause = " order by cs.download_count desc,cs.case_study_id desc";
		}elseif($orderby=="date_uploaded"){
			$order_clause = " order by cs.uploaded_on desc,cs.case_study_id desc";
		}else{
			$order_clause = " order by cs.case_study_id desc";
		}
		$q.=$order_clause." limit ".$start_offset.",".$num_to_fetch;
        $res = mysql_query($q);
        if(!$res){
			//echo mysql_error();
            return false;
        }
        //////////////////
        $data_count = mysql_num_rows($res);
        if(0==$data_count){
            return true;
        }
        /////////////////
        for($i=0;$i<$data_count;$i++){
            $data_arr[$i] = mysql_fetch_assoc($res);
            $data_arr[$i]['deal_company_name'] = $g_mc->db_to_view($data_arr[$i]['deal_company_name']);
            $data_arr[$i]['partner_name'] = $g_mc->db_to_view($data_arr[$i]['partner_name']);
			$data_arr[$i]['f_name'] = $g_mc->db_to_view($data_arr[$i]['f_name']);
			$data_arr[$i]['l_name'] = $g_mc->db_to_view($data_arr[$i]['l_name']);
        }
        return true;
    }
	/***************************END CASE STUDY FOR DEALS*********************************************/
	
	/***************************DOCUMENTS FOR DEALS**************************************************/
	/******************
	sng:22/feb/2012
	get_all_documents($transaction_id,&$data_arr,&$data_count)
	
	delete_document($doc_id)
	
	add_document($deal_id,$mem_id,$is_approved,&$validation_passed,&$err_arr)
	
	has been moved to transaction_doc class
	********************************/
	
	
	
	
	/***************************END DOCUMENTS FOR DEALS**********************************************/
	
	/******************************DEAL ACTIVE / INACTIVE************************************************/
	public function admin_get_inactive_deals_paged($start_offset,$num_to_fetch,&$data_arr,&$data_count){
		$db = new db();
        
		$q = $q = "SELECT t.id,t.value_in_billion,t.date_of_deal,deal_cat_name,deal_subcat1_name,deal_subcat2_name,t.value_range_id,t.added_on,vrm.display_text as fuzzy_value ,m.f_name,m.l_name,m.designation,w.name as work_company FROM ".TP."transaction AS t LEFT JOIN ".TP."transaction_value_range_master as vrm ON (t.value_range_id=vrm.value_range_id) LEFT JOIN ".TP."member as m on(t.added_by_mem_id=m.mem_id) left join ".TP."company as w on(m.company_id=w.company_id) where is_active='n' order by added_on limit ".$start_offset.",".$num_to_fetch;
		
		$ok = $db->select_query($q);
		if(!$ok){
			return false;
		}
		$data_count = $db->row_count();
		if(0==$data_count){
			//empty
			return true;
		}
		$data_arr = $db->get_result_set_as_array();
		
		require_once("classes/class.transaction_company.php");
		$g_trans_comp = new transaction_company();
		
		for($i=0;$i<$data_count;$i++){
			$transaction_id = $data_arr[$i]['id'];
			$data_arr[$i]['participants'] = NULL;
			//get the participants, just names
			$success = $g_trans_comp->get_deal_participants($transaction_id,$data_arr[$i]['participants']);
			if(!$success){
				return false;
			}
		}
        
        return true;
	}
	
	/*************************
	sng:20/mar/2012
	we now send this function to class.transaction_verification.php
	function admin_get_admin_unverified_deals_paged($start_offset,$num_to_fetch,&$data_arr,&$data_count)
	*******************************/
	
	/******************************DEAL ACTIVE / INACTIVE************************************************/
}
$g_trans = new transaction();


?>
