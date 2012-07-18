<?php
require_once("include/global.php");
require_once("classes/class.company.php");
require_once("classes/class.misc.php");
require_once("classes/class.transaction.php");
require_once("classes/class.country.php");
require_once("classes/class.account.php");
require_once("classes/class.statistics.php");
//require_once("classes/class.preset.php"); 
require_once("classes/class.stat_help.php");

ini_set('display_errors',0);
error_reporting(E_ALL);
  class oneStop {
      
      private static $_country;
      private static $_industry;
      private static $_dealType;
      private static $_otherDealTypes;
      private static $_sizeCaptions;
      private static $_dateSubmitted;
      
      public static function saveOptions($options = array()) {
        $tableName = TP . 'oneStop_options';
        $options = sizeOf($options) ? $options : self::getOptions();
        /**
        * Make sure that we don`t get duplicates
        */
        mysql_query("TRUNCATE TABLE $tableName"); 
        $q = "INSERT INTO `mytombstones`.`tombstone_oneStop_options` (
            `idOption` ,
            `group` ,
            `option` ,
            `value` ) VALUES ";
        foreach ($options as $option) {
            $v[] = " (NULL, '{$option['group']}', '{$option['option']}', '{$option['value']}' ) ";
        }
        
        $q .= implode(", ", $v);

        return mysql_query($q);         
      }
      
      public static function getOptions() {
         $newOptions = array(
           array(
             'group' => 'table1',
             'option' => 'year1',
             'value' => $_POST['year1'] 
           ),
           array(
             'group' => 'table1',
             'option' => 'year2',
             'value' => $_POST['year2'] 
           ),
            array(
             'group' => 'table1',
             'option' => 'deal_size',
             'value' => $_POST['deal_size'] 
           ),
           array(
             'group' => 'table4',
             'option' => 'year3',
             'value' => $_POST['year3'] 
           ),
           array(
             'group' => 'table5',
             'option' => 'year4',
             'value' => $_POST['year4'] 
           ),
           array(
             'group' => 'table6',
             'option' => 'year5',
             'value' => $_POST['year5'] 
           )
         ); 
         
         return $newOptions;   
      }
      
      public static function loadOptions($table = false) {
         $tableName = TP . 'oneStop_options';
         $q = "SELECT * FROM $tableName";
         if ($table) {
             $q .= " WHERE `group` = '$table'";
         }
         //dump($q);
         $res = mysql_query($q);
         $rows = array();
         while ($row = mysql_fetch_assoc($res)) {
             $rows[] = $row; 
             
             if ($row['option'] == 'deal_size') {
                 $q = 'SELECT caption FROM tombstone_transaction_size_filter_master WHERE condition = "' .  $row['value'] .'" LIMIT 1';
                 if ($res = mysql_query($q)) {
                     $result =  mysql_fetch_assoc($res);
                     $row['deal_size_caption'] = $result['caption'];
                 }
             } 
             
             $_POST[$row['option']] = $row['value'];    
             
         } 
         return $rows;
      }
      
      public static function getPostTranslation() {
          $countryID = $_POST['country'];
          $industryID = $_POST['industry'];
          $dealITypeID = $_POST['dealType'];
          $userID = $_SESSION['mem_id'];
          $q  = "SELECT c.name, i.industry, ttm.subtype2
                FROM tombstone_country_master c, tombstone_sector_industry_master i, tombstone_transaction_type_master ttm
                WHERE c.id = %d
                AND i.id = %d
                AND ttm.id = %d
                LIMIT 1";
         $q = sprintf($q, $countryID, $industryID,  $dealITypeID);
         //dump($q);
         if (!$res = mysql_query($q)) {
             return false;
         }
         
         return mysql_fetch_assoc($res);
      }

      public static function getRequestsForCurrentUser() {
          $memberId = @$_SESSION['mem_id'];
        $q =   "SELECT r . * , cm.name AS countryName, tm.sector AS industrySector, tm.industry AS industryName, 

ttm.type AS dealType, ttm.subtype2 AS dealSubtype2, ttm.subtype1 as dealSubtype1
                FROM `tombstone_oneStop_requests` r
                LEFT JOIN tombstone_country_master cm ON r.countryID = cm.id
                LEFT JOIN tombstone_sector_industry_master tm ON r.industryID = tm.id
                LEFT JOIN tombstone_transaction_type_master ttm ON r.dealITypeID = ttm.id
                WHERE r.userID = $memberId 
                ORDER BY dateSubmitted DESC
                LIMIT 0 , 5 ";
         $res = mysql_query($q);
         if (!$res) {
             return false;
         }  
         $currentUserRequests = array();        
         while ($row = mysql_fetch_assoc($res)) {
             if ($row['dealITypeID'] == 0) {
                 $row['dealType'] = 'M&A';
                 $row['dealSubtype2'] = '';
                 $row['dealSubtype1'] = 'All';
             }
             $currentUserRequests[] = $row;
         }
         
         return  $currentUserRequests;
      }
            
      public static function saveRequest() {
          $tableName = TP . 'oneStop_requests';
          if (!isset($_POST['submit']) || !$_SESSION['is_member']) {
              return false;
          }
          $countryID = $_POST['country'];
          $industryID = $_POST['industry'];
          $dealITypeID = $_POST['dealType'];
          $userID = $_SESSION['mem_id'];
          
          if (empty($countryID) || empty($industryID)) {
              header('Location: oneStop.php');
              exit;
          }
          $q = "INSERT INTO `$tableName` 
          (`id` ,  `countryID` ,  `industryID` ,  `dealITypeID` , `userID` ,  `dateSubmitted`   )
            VALUES ( NULL, '%d', '%d', '%d', '%d', NOW( ) );" ;
          $q = sprintf($q, $countryID, $industryID, $dealITypeID, $userID);
          if (!(isset($_REQUEST['action']) && strlen($_REQUEST['action']))) { 
              if (!mysql_query($q)){
                  return false;
              }
          }            
          $dealITypeID2 = $dealITypeID; 
          if ($dealITypeID == 0) {
              $dealITypeID2 = 18;
          }
          
          $q = sprintf("SELECT * FROM tombstone_transaction_type_master WHERE id = %d", $dealITypeID2);
          if ($res = mysql_query($q)) {
              self::$_dealType = mysql_fetch_assoc($res);
          }
         
         //dump(self::$_dealType);
            self::$_dealType['userType'] =  self::$_dealType['subtype2'] ;
          if (self::$_dealType['subtype2'] == 'n/a') {
              self::$_dealType['subtype2'] = '';              
              self::$_dealType['userType'] = self::$_dealType['subtype1'];              
          }
          
          if ($dealITypeID == 0) {
              self::$_dealType['subtype1'] = '';
              self::$_dealType['subtype2'] = '';
          }
          
                    
          $q = sprintf("SELECT industry, sector FROM tombstone_sector_industry_master WHERE id=%d", $industryID );
          if ($res = mysql_query($q)) {
              self::$_industry = mysql_fetch_assoc($res);
          }                    
          $q = sprintf("SELECT tcl.*, rm.name as regionName, cm.name as countryName FROM `tombstone_region_country_list` tcl LEFT JOIN tombstone_region_master rm ON (rm.id = tcl.region_id) LEFT JOIN tombstone_country_master cm ON (cm.id = tcl.country_id) WHERE country_id = %d LIMIT 1", $countryID );
          if ($res = mysql_query($q)) {
              self::$_country = mysql_fetch_assoc($res);
          }
          
          $q = sprintf("SELECT DISTINCT type FROM tombstone_transaction_type_master WHERE type <> (SELECT  type FROM tombstone_transaction_type_master WHERE id = %d LIMIT 1)", $dealITypeID2);
          if ($res = mysql_query($q)) {
              while ($row = mysql_fetch_assoc($res)) {
                self::$_otherDealTypes[] = $row;   
              }
              
          }          
          //dump($q);
          $q = "SELECT * FROM tombstone_transaction_size_filter_master WHERE TRUE";
          if ($res = mysql_query($q)) {
              while ($row = mysql_fetch_assoc($res)) {
                  self::$_sizeCaptions[$row['condition']] = $row['caption'];
              }
          }
          return mysql_insert_id();
      }

      static function viewRequest() {
        global $g_view;           
        $requestID = $_REQUEST['requestID'];
        $g_view['page_heading'] = "One Stop: Request view";
        $g_view['top_search_view'] = "all_search_view.php";
        $g_view['content_view'] = "oneStop_resultsView.php";          
      }
      
      static function getDisplayResults() {
          
      }
      
      static function getLabels() {

      }
      static function getFirstTableResults() {
          
          $data = array('failed'=>array(),'success'=>array());

          $rankingLabels = array(
             'num_deals' => 'No of deals',
             'total_deal_value' => 'Total value $bn',
             'total_adjusted_deal_value' => 'Adjusted value $bn'
          );
          
          self::loadOptions('table1');
          
          $pCountry = self::$_country['countryName'];
          $pRegion = self::$_country['regionName'];
          $pRegionID = self::$_country['region_id'];
          $pIndustry = self::$_industry['industry'];
          $pSector = self::$_industry['sector'];
          $pDealType = self::$_dealType['type'];
          $pDealSubType2  = self::$_dealType['subtype2'];          
          $pDealSubType1  = (self::$_dealType['subtype1'] == '') ? 'Completed' :  self::$_dealType['subtype1'] ;
       
          

          $year1 = $_POST['year1'];
          $year2 = $_POST['year2'];
          $size = $_POST['deal_size'];                    
          $variations = array(
                array(
                    'country' => $pCountry,
                    'industry' =>  $pIndustry,
                    'deal_subcat2_name' =>  $pDealSubType2,
                    'year' =>  $year1,
                    'ranking_criteria' => 'num_deals',
                    'deal_size' => $size,
                    /**
                    * extra fields for M&A support
                    */
                    'deal_subcat1_name' => $pDealSubType1,                     
                    'deal_cat_name' => $pDealType,                     
                    'sector' => $pSector,
                    'label' => "$pCountry, $pIndustry, " . (($pDealSubType2 == '') ?  "All $pDealSubType1" : $pDealSubType2). ", $year1"                     
                ),
                array(
                    'country' => $pCountry,
                    'industry' =>  $pIndustry,
                    'deal_subcat2_name' =>  $pDealSubType2,
                    'year' =>  $year1,
                    'ranking_criteria' => 'total_deal_value',
                    'deal_size' => $size, 
                    /**
                    * extra fields for M&A support
                    */
                    'deal_subcat1_name' => $pDealSubType1,                     
                    'deal_cat_name' => $pDealType, 
                    'sector' => $pSector, 
                    'label' => "$pCountry, $pIndustry, " . (($pDealSubType2 == '') ?  "All $pDealSubType1" : $pDealSubType2). ", $year1"                     
                ),
                array(
                    'country' => $pCountry,
                    'industry' =>  $pIndustry,
                    'deal_subcat2_name' =>  $pDealSubType2,
                    'year' =>  $year1,
                    'ranking_criteria' => 'total_adjusted_deal_value',
                    'deal_size' => $size, 
                    /**
                    * extra fields for M&A support
                    */
                    'deal_subcat1_name' => $pDealSubType1,                     
                    'deal_cat_name' => $pDealType,                     
                    'sector' => $pSector,
                    'label' => "$pCountry, $pIndustry, " . (($pDealSubType2 == '') ?  "All $pDealSubType1" : $pDealSubType2). ", $year1"                     
                ),
                array(
                    'country' => $pCountry,
                    'industry' =>  $pIndustry,
                    'deal_subcat2_name' =>  $pDealSubType2,
                    'year' =>  $year2,
                    'ranking_criteria' => 'num_deals',
                    'deal_size' => $size, 
                    /**
                    * extra fields for M&A support
                    */
                    'deal_subcat1_name' => $pDealSubType1,                     
                    'deal_cat_name' => $pDealType,                     
                    'sector' => $pSector,
                    'label' => "$pCountry, $pIndustry, " . (($pDealSubType2 == '') ?  "All $pDealSubType1" : $pDealSubType2). ", $year2"                     
                ),
                array(
                    'country' => $pCountry,
                    'industry' =>  $pIndustry,
                    'deal_subcat2_name' =>  $pDealSubType2,
                    'year' =>  $year2,
                    'ranking_criteria' => 'total_deal_value',
                    'deal_size' => $size, 
                    /**
                    * extra fields for M&A support
                    */
                    'deal_subcat1_name' => $pDealSubType1,                     
                    'deal_cat_name' => $pDealType,                     
                    'sector' => $pSector,
                    'label' => "$pCountry, $pIndustry, " . (($pDealSubType2 == '') ?  "All $pDealSubType1" : $pDealSubType2). ", $year2"                     
                ),
                array(
                    'country' => $pCountry,
                    'industry' =>  $pIndustry,
                    'deal_subcat2_name' =>  $pDealSubType2,
                    'year' =>  $year2,
                    'ranking_criteria' => 'total_adjusted_deal_value',
                    'deal_size' => $size, 
                    /**
                    * extra fields for M&A support
                    */
                    'deal_subcat1_name' => $pDealSubType1,                     
                    'deal_cat_name' => $pDealType,                     
                    'sector' => $pSector,
                    'label' => "$pCountry, $pIndustry, " . (($pDealSubType2 == '') ?  "All $pDealSubType1" : $pDealSubType2). ", $year2"                     
                ),
                /**-
                * Six more tries as requested on 11-12-2011                
                */
                array(
                    'country' => $pCountry,
                    'industry' =>  '',
                    'deal_subcat2_name' =>  '',
                    'year' =>  $year1,
                    'ranking_criteria' => 'num_deals',
                    'deal_size' => $size,
                    /**
                    * extra fields for M&A support
                    */
                    'deal_subcat1_name' => '',                     
                    'deal_cat_name' => $pDealType,                     
                    'sector' => $pSector,
                    'label' => "$pCountry, $pSector, $pDealType, $year1"                     
                ),
                array(
                    'country' => $pCountry,
                    'industry' =>  '',
                    'deal_subcat2_name' =>  '',
                    'year' =>  $year1,
                    'ranking_criteria' => 'total_deal_value',
                    'deal_size' => $size, 
                    /**
                    * extra fields for M&A support
                    */
                    'deal_subcat1_name' => '',                     
                    'deal_cat_name' => $pDealType, 
                    'sector' => $pSector, 
                    'label' => "$pCountry, $pSector, $pDealType, $year1"                     
                ),
                array(
                    'country' => $pCountry,
                    'industry' =>  '',
                    'deal_subcat2_name' =>  '',
                    'year' =>  $year1,
                    'ranking_criteria' => 'total_adjusted_deal_value',
                    'deal_size' => $size, 
                    /**
                    * extra fields for M&A support
                    */
                    'deal_subcat1_name' => '',                     
                    'deal_cat_name' => $pDealType,                     
                    'sector' => $pSector,
                    'label' => "$pCountry, $pSector, $pDealType , $year1"                     
                ),
                array(
                    'country' => $pCountry,
                    'industry' =>  '',
                    'deal_subcat2_name' =>  '',
                    'year' =>  $year2,
                    'ranking_criteria' => 'num_deals',
                    'deal_size' => $size, 
                    /**
                    * extra fields for M&A support
                    */
                    'deal_subcat1_name' => '',                     
                    'deal_cat_name' => $pDealType,                     
                    'sector' => $pSector,
                    'label' => "$pCountry, $pSector, $pDealType, $year2"
                ),
                array(
                    'country' => $pCountry,
                    'industry' =>  '',
                    'deal_subcat2_name' =>  '',
                    'year' =>  $year2,
                    'ranking_criteria' => 'total_deal_value',
                    'deal_size' => $size, 
                    /**
                    * extra fields for M&A support
                    */
                    'deal_subcat1_name' => '',                     
                    'deal_cat_name' => $pDealType,                     
                    'sector' => $pSector,
                    'label' => "$pCountry, $pSector, $pDealType, $year2"
                ),
                array(
                    'country' => $pCountry,
                    'industry' =>  '',
                    'deal_subcat2_name' =>  '',
                    'year' =>  $year2,
                    'ranking_criteria' => 'total_adjusted_deal_value',
                    'deal_size' => $size, 
                    /**
                    * extra fields for M&A support
                    */
                    'deal_subcat1_name' => '',                     
                    'deal_cat_name' => $pDealType,                     
                    'sector' => $pSector,
                    'label' => "$pCountry, $pSector, $pDealType, $year2"
                ),               
          ) ;
          //echo "<div style='display:none'> <pre>" . print_r($variations,1) . "</pre></div>";           
          //var_dump($variations);
          $oldPost = $_POST;
          $stat = new statistics(); 
          foreach ($variations as $key=>$variation) {
              unset($_POST);
              $_POST['submit'] = 'Update';
              $_POST['partner_type'] = 'bank';
              foreach ($variation as $fieldName=>$fieldValue) {
                if ($fieldName == 'label')
                    continue;
                $_POST[$fieldName] =  $fieldValue;          
              }
            //dump(implode(',', $_POST));
            $data = array();
            $success = $stat->front_generate_league_table_for_firms_paged($_POST,0,10,$data,$g_view['data_count']);       
            //dump( $data);
            $c = self::getRankFromResults($data);
            $rankLabel =  $rankingLabels[$variation['ranking_criteria']];
            $dealLabel = '';
            if ($pDealType == "M&A") {
                if ($pDealSubType1 == '') {
                    $dealLabel = 'M&A All';  
                } else {
                    $dealLabel =   "M&A $pDealSubType1";
                }
            } else {
                $dealLabel =  ($variation['deal_subcat2_name'] == '') ? (($variation['deal_subcat1_name'] == '') ?  $pDealType : 'All ' . $variation['deal_subcat1_name'] ) : $pDealSubType2; 
            }
            
            
            if (is_array($c) && sizeOf($c)) {
                $d = array(
                    'rank' => $c['rank'],
                    'deal_country' => $pCountry,
                    'deal_industry' => (($variation['industry'] == '') ? $pSector : $variation['industry']),
                    'deal_subcat2_name' => $dealLabel,
                    'sizeR' => self::getSizeCaption($size),
                    'date' => $_POST['year'],
                    'rankingCritKey' => $variation['ranking_criteria'],
                    'total_adjusted_deal_value' => $c['total_adjusted_deal_value'],
                    'total_deal_value' => $c['total_deal_value'],
                    'firm_name' => $c['firm_name'],
                    'num_deals' => $c['num_deals'],
                    'rank_label' => $rankLabel,
                    'dataForPost' => base64_encode(serialize($_POST)),
                );
                $ret['success'][] = $d; 
            } else {
                $sizeLabel = self::getSizeCaption($size);
                $ret['failed'][] = "Based on the critieria: '{$variation['label']}, $sizeLabel, {$rankLabel}',  there are no useful League Table charts that showcase your firm generated by our database.";
            } 
          }  
          return $ret;            
      }
      
      static function getSizeCaption($filter) {
          return @self::$_sizeCaptions[$filter];
      }
      
      static function getSecondTableResults() 
      {
          $companyID = @$_SESSION['company_id'];
          $data = array();
          $pCountry = self::$_country['countryName'];
          $pRegion = self::$_country['regionName'];
          $pRegionID = self::$_country['region_id'];
          $pIndustry = self::$_industry['industry'];
          $pSector = self::$_industry['sector'];
          $pDealType = self::$_dealType['type'];
          $pDealSubType2  = self::$_dealType['subtype2'];
          $pDealSubType1  = (self::$_dealType['subtype1'] == '') ? 'Completed' :  self::$_dealType['subtype1'] ;
      
         
             $variations = array(
              array(
                'country' => $pCountry,
                'industry' => $pIndustry,
                'deal_cat_name' =>$pDealType,
                'deal_subcat2_name' =>$pDealSubType2,           
                'deal_subcat1_name' =>$pDealSubType1,
                'dealLabel' =>  $pDealSubType2,         
              ),
              array(
                'country' => $pCountry,
                'industry' => $pIndustry,
                'deal_cat_name' =>$pDealType,         
                'deal_subcat1_name' =>'',
                'deal_subcat2_name' =>'',
              ),
              array(
                'country' => $pCountry,
                'sector' => $pSector,
                'deal_cat_name' =>$pDealType,
                'deal_subcat2_name' =>$pDealSubType2,           
                'deal_subcat1_name' =>$pDealSubType1,        
              ),
              array(
                'country' => $pCountry,
                'sector' => $pSector,
                'deal_cat_name' =>$pDealType,
                'deal_subcat1_name' =>'',
                'deal_subcat2_name' =>'',         
              ),
              array(
                'region' => $pRegion,
                'industry' => $pIndustry,
                'deal_cat_name' =>$pDealType,
                'deal_subcat2_name' =>$pDealSubType2,
                'deal_subcat1_name' =>$pDealSubType1,            
              ),
               array(
                'region' => $pRegion,
                'industry' => $pIndustry,
                'deal_cat_name' =>$pDealType,
                'deal_subcat1_name' =>'',
                'deal_subcat2_name' =>'',          
              ),
              array(
                'region' => $pRegion,
                'sector' => $pSector,
                'deal_cat_name' =>$pDealType,
                'deal_subcat2_name' =>$pDealSubType2,
                'deal_subcat1_name' =>$pDealSubType1,            
              ),

              array(
                'region' => $pRegion,
                'sector' => $pSector,
                'deal_cat_name' =>$pDealType,
                'deal_subcat1_name' =>'',
                'deal_subcat2_name' =>'',           
              ),
              array(
                'industry' => $pIndustry,
                'deal_cat_name' =>$pDealType,
                'deal_subcat2_name' =>$pDealSubType2,
                'deal_subcat1_name' =>$pDealSubType1,            
              ),
              array(
                'industry' => $pIndustry,
                'deal_cat_name' =>$pDealType,
                'deal_subcat1_name' =>'',
                'deal_subcat2_name' =>'',          
              ),
              array(
                'sector' => $pSector,
                'deal_cat_name' =>$pDealType,
                'deal_subcat2_name' =>$pDealSubType2,
                'deal_subcat1_name' =>$pDealSubType1,            
              ),

              array(
                'sector' => $pSector,
                'deal_cat_name' =>$pDealType,
                'deal_subcat1_name' =>'',
                'deal_subcat2_name' =>'',            
              ),
          );

          if ($pDealType == 'M&A' && $pDealSubType1 == '' && $pDealSubType2 == 'n/a') {
              $spDealSubType1 = 'Completed';
              $variations = array(
                  array(
                    'country' => $pCountry,
                    'industry' => $pIndustry,
                    'deal_cat_name' =>$pDealType,
                    'deal_subcat1_name' => 'Completed'           
                  ),
                  array(
                    'country' => $pCountry,
                    'industry' => $pIndustry,
                    'deal_cat_name' =>$pDealType,
                    'deal_subcat1_name' =>'', 
                  ),
                  array(
                    'country' => $pCountry,
                    'sector' => $pSector,
                    'deal_cat_name' =>$pDealType,
                    'deal_subcat1_name' => 'Completed'           
                  ),
                  array(
                    'country' => $pCountry,
                    'sector' => $pSector,
                    'deal_cat_name' =>$pDealType,
                    'deal_subcat1_name' =>'', 
                  ),
                  array(
                    'region' => $pRegion,
                    'industry' => $pIndustry,
                    'deal_cat_name' =>$pDealType,
                    'deal_subcat1_name' =>'Completed',            
                  ),
                  array(
                    'region' => $pRegion,
                    'industry' => $pIndustry,
                    'deal_cat_name' =>$pDealType,
                    'deal_subcat1_name' =>'',
                  ),
                  array(
                    'region' => $pRegion,
                    'sector' => $pSector,
                    'deal_cat_name' =>$pDealType,
                    'deal_subcat1_name' =>'Completed',            
                  ),
                  array(
                    'region' => $pRegion,
                    'sector' => $pSector,
                    'deal_cat_name' =>$pDealType,
                    'deal_subcat1_name' =>'',
                  ),
                  array(
                    'industry' => $pIndustry,
                    'deal_cat_name' =>$pDealType,
                    'deal_subcat1_name' =>'Completed',            
                  ),
                  array(
                    'industry' => $pIndustry,
                    'deal_cat_name' =>$pDealType,
                    'deal_subcat1_name' =>'',  
                  ),
                  array(
                    'sector' => $pSector,
                    'deal_cat_name' =>$pDealType,
                    'deal_subcat1_name' =>'Completed',            
                  ),
                  array(
                    'sector' => $pSector,
                    'deal_cat_name' =>$pDealType,
                    'deal_subcat1_name' =>'', 
                  ),
              );
          }
                    
          $oldPost = $_POST;
          $stats = new statistics(); 
          $i = 0;         
          foreach ($variations as $key=>$variation) {
              //var_dump($variation);
              $i += 1;
              unset($_POST);
                $_POST['submit'] =  'Filter';
                $_POST['myaction'] =  'filter';
              //self::fillEmptyPostForLeagueTables();
              foreach ($variation as $fieldName=>$fieldValue)  {
                  $_POST[$fieldName] = $fieldValue;
              }
              $trans  = new transaction();
              $tombst = $trans->getTombstonesForFirm($_SESSION['company_id'], 0, 999, false);
              //dump(implode("|", $_POST) . sizeOf($tombst));
              $ret[$key]['nrTombstones'] = is_array($tombst) ? sizeOf($tombst) : 0;
              //$ret[$key]['country'] = sizeOf($_POST['country']) ? $_POST['country'] : sizeOf($_POST['region']) ? $_POST['region'] : 'Global';
              if (isset($_POST['country']) && sizeOf($_POST['country'])) {
                $ret[$key]['country'] =  $_POST['country'];   
              } elseif (isset($_POST['region']) && sizeOf($_POST['region'])) {
                $ret[$key]['country'] = $_POST['region'];    
              } else {
                 $ret[$key]['country'] = 'Global';    
              }
              if (isset($_POST['industry']) && sizeOf($_POST['industry'])) {
                $ret[$key]['industry'] =  $_POST['industry'];   
              } elseif (isset($_POST['sector']) && sizeOf($_POST['sector'])) {
                $ret[$key]['industry'] = $_POST['sector'];    
              } else {
                 $ret[$key]['industry'] = '';    
              }
              
              $ret[$key]['deal_cat_name'] =  $_POST['deal_cat_name'];
              
              if ($_POST['deal_cat_name'] == 'M&A') {
                  if ($_POST['deal_subcat1_name'] != '') {
                    $ret[$key]['deal_cat_name'] =  $_POST['deal_cat_name'] . ' ' . $_POST['deal_subcat1_name'];   
                  } else {
                    $ret[$key]['deal_cat_name'] = 'All '  . $_POST['deal_cat_name'];  
                  }
              } else {
                  if (strlen($_POST['deal_subcat2_name'])) {
                    $ret[$key]['deal_cat_name'] =  $_POST['deal_subcat2_name']; 
                  } else
                  if (strlen($_POST['deal_subcat1_name'])) {
                    $ret[$key]['deal_cat_name'] =  'All ' . $_POST['deal_subcat1_name']; 
                  }                
              } 
                           


              //$ret[$key]['deal_cat_name'] = isset($_POST['deal_cat_name']) ? $_POST['deal_cat_name'] : isset($_POST['deal_subcat2_name']) ? $_POST['deal_subcat2_name'] : '';
              $ret[$key]['dataForPost'] = base64_encode(serialize($_POST));     
              //echo "<div style='display:none'> <pre>" .print_r($_POST,1) . "</pre></div>";       
          }
          //dump($ret);
          //exit;
          $_POST =  $oldPost;
          //ini_set('display_errors',1);
          //error_reporting(E_ALL);
          //dump($ret);
         return  $ret;
      }
      
      static function getThirdTableResults() {
            $data = array();
            $pCountry = self::$_country['countryName'];
            $pRegion = self::$_country['regionName'];
            $pRegionID = self::$_country['region_id'];
            $pIndustry = self::$_industry['industry'];
            $pSector = self::$_industry['sector'];
            $pDealType = self::$_dealType['type'];
            $pDealSubType2  = self::$_dealType['subtype2'];
            $pDealSubType1  = self::$_dealType['subtype1'];
            $variations = array(
                array(
                    'country' =>  $pCountry,
                    'industry' => $pIndustry,
                    'deal_subcat2_name' => $pDealSubType2,
                    'deal_subcat1_name' => $pDealSubType1
                ),
                array(
                    'country' =>  $pCountry,
                    'sector' => $pSector,
                    'deal_cat_name' => $pDealType
                ),
                array(
                    'region' =>  $pRegion,
                    'industry' => $pIndustry,
                    'deal_subcat2_name' => $pDealSubType2,
                    'deal_subcat1_name' => $pDealSubType1
                ),
                array(
                    'region' =>  $pRegion,
                    'sector' => $pSector,
                    'deal_cat_name' => $pDealType
                ),
                array(
                    'industry' => $pIndustry,
                    'deal_subcat2_name' => $pDealSubType2,
                    'deal_subcat1_name' => $pDealSubType1
                ),                                
                array(
                    'sector' => $pSector,
                    'deal_cat_name' => $pDealType
                )                

            );             

            //dump($variations);             
            if (self::$_dealType['type'] == 'M&A') {
                if ($pDealSubType1 == '') {
                    $pDealSubType1 = 'Completed';
                }
                $variations = array(
                    array(
                        'country' =>  $pCountry,
                        'sector' => $pSector,
                        'deal_cat_name' => $pDealType
                    ),
                    array(
                        'country' =>  $pCountry,
                        'industry' => $pIndustry,
                        'deal_subcat2_name' => $pDealSubType2,
                        'deal_subcat1_name' => $pDealSubType1
                    ),
                    array(
                        'region' =>  $pRegion,
                        'sector' => $pSector,
                        'deal_cat_name' => $pDealType
                    ),
                    array(
                        'region' =>  $pRegion,
                        'industry' => $pIndustry,
                        'deal_subcat2_name' => $pDealSubType2,
                        'deal_subcat1_name' => $pDealSubType1
                    ),
                    array(
                        'sector' => $pSector,
                        'deal_cat_name' => $pDealType
                    ), 
                    array(
                        'industry' => $pIndustry,
                        'deal_subcat2_name' => $pDealSubType2,
                        'deal_subcat1_name' => $pDealSubType1
                    ),                                
                );                   
            }           

          $stat = new stat_help();            
            foreach ($variations as $variation) {
                $myPost = array();
                foreach ($variation as $key=>$value) {
                    $myPost[$key] = $value;
                    //$myPost['month_division'] = 'h';
                }
                $myPost['month_division'] = 'h';
                $myPost['month_division_list'] = '2008-1';
                            
                unset($_SESSION['lastGeneratedGraphData']);
                statistics::generate_issuance_data($myPost,$data_arr,$max_value,$num_values);
                
                if(!isset($myPost['region']) && !isset($myPost['country'])) {
                    array_unshift($myPost, 'Global');
                }
                $dataForPost = base64_encode(serialize($myPost));
                
                if (self::$_dealType['type'] == 'M&A') {
                    if (isset($myPost['deal_subcat1_name'])) {
                        $myPost['deal_subcat2_name'] = $myPost['deal_subcat1_name'] . ' M&A';
                    }
                    else {
                        $myPost['deal_cat_name'] = 'All M&A';
                    }
                        
                }
                if (!strlen($myPost['deal_subcat2_name']) && strlen($myPost['deal_subcat1_name'])) {
                     unset($myPost['deal_subcat2_name']);
                     $myPost['deal_cat_name'] =  'All ' . $myPost['deal_subcat1_name'];
                } 
                
                unset($myPost['deal_subcat1_name']);   
        /* if ($myPost['deal_cat_name'] == 'M&A' && isset($myPost['deal_subcat1_name'])) {
                    $myPost['deal_subcat2_name'] = 'M&A ' . $myPost['deal_subcat1_name'];
                    unset($myPost['deal_subcat1_name']);    
                } */
                
                unset($myPost['month_division']);
                unset($myPost['month_division_list']);
                //dump($_SESSION['lastGeneratedGraphData']);
                if (isset($_SESSION['lastGeneratedGraphData']) && sizeOf($_SESSION['lastGeneratedGraphData'])){
                    array_push($data,array('data'=>$_SESSION['lastGeneratedGraphData'], 'info' => $myPost, 'dataForPost' => $dataForPost));
                    //$_SESSION['lastGeneratedGraphDataSample'] = $_SESSION['lastGeneratedGraphData'];
                }
                else {
                    //self::getEmptyGraphData();
                    array_push($data,array('data'=>self::getEmptyGraphData(), 'info' => $myPost, 'dataForPost' => $dataForPost));
                }
            }
            return $data;
      } 
      
      static function getEmptyGraphData() {
          $stat = new stat_help();            
          $stat->volume_get_month_div_entries('h',$value_arr,$label_arr);  
          foreach ($label_arr as $key=>$val) {
               $ret[] = array('short_name' => $val, 'value' => '0.0');
          } 
          return $ret;   
      }
      static function getFourthTableResults() {
          self::loadOptions('table4');
          $pCountry = self::$_country['countryName'];
          $pRegion = self::$_country['regionName'];
          $pRegionID = self::$_country['region_id'];
          $pIndustry = self::$_industry['industry'];
          $pSector = self::$_industry['sector'];
          $pDealType = self::$_dealType['type'];
          $pDealSubType2  = self::$_dealType['subtype2'];          
          $pDealSubType1  = self::$_dealType['subtype1'];
          $year = $_POST['year3'];
          $trans = new transaction() ;
                              
          /**
          * First table
          * 
          * @var mixed
          */
          $firstTablePost = array(
            'country' =>  $pCountry,
            'deal_cat_name' => $pDealType, 
            'deal_subcat1_name' => $pDealSubType1, 
            'deal_subcat2_name' =>  $pDealSubType2,
            'industry' => $pIndustry,
            'myaction' => 'search', 
            'number_of_deals' => 'top:10', 
            'submit' => 'Search',  
            'top_search_term' => '',  
            'year' => $_POST['year3']
          );
         
          if ($pDealType == 'M&A') { 
            $deal_type = "M&A $pDealSubType1";    
          } else {
            $deal_type =  ($pDealSubType2 == '') ? "All $pDealSubType1" : $pDealSubType2;   
          }
          
          $success = $trans->front_deal_search_paged($firstTablePost, 0, 10, $data, $count);
          //dump($data);
          $returnData['table1']['data'] = $data;  
          $returnData['table1']['label'] = "$pCountry, $pIndustry, $deal_type, {$_POST['year3']}";
          $returnData['table1']['dataForPost'] = base64_encode(serialize($firstTablePost));

          $secondTablePost = array(
            'region' =>  $pRegion,
            'deal_cat_name' => $pDealType, 
            'sector' => $pSector,
            'myaction' => 'search', 
            'number_of_deals' => 'top:10', 
            'submit' => 'Search',  
            'top_search_term' => '',  
            'year' => $_POST['year3']
          );
          
          $success = $trans->front_deal_search_paged($secondTablePost, 0, 10, $data2, $count);

          $returnData['table2']['data'] = $data2;
          //dump($data2);
          $returnData['table2']['label'] = "$pRegion, $pSector, $pDealType, {$_POST['year3']}";
          $returnData['table2']['dataForPost'] = base64_encode(serialize($secondTablePost));

          return $returnData;        
      }    
      /**
      * TODO find a better way to find rank
      * 
      * @param mixed $results
      */
      static function getRankFromResults($results) {
         $rank = 0;
         if (is_array($results) && sizeOf($results)) {
             foreach ($results as $key=>$res) {
                if ($res['partner_id'] == @$_SESSION['company_id']) {
                    $results[$key]['rank'] = $key+1;
                    //$results[$key]['date'] = $rank;
                    return $results[$key];
                }  
             }
         }
         return 0; 
      }
      
      static function loadIntoPost($data) {
          $data = unserialize(base64_decode($data));
          //var_dump($data); 
          foreach ($data as $key=>$val) {
              $_POST[$key] = $val;
          }
      }
      
      static function getFifthTableResults() {
          self::loadOptions('table5');
          
          $dateFilter = '';
          if (preg_match("/(\d+)-(\d+)/", $_POST['year4'], $matches )) {
            $dateFilter = " AND year( t.date_of_deal )>='{$matches[1]}' AND year(date_of_deal)<='{$matches[2]}'";  
            //var_dump($matches);  
          } elseif(strlen($_POST['year4'])) {
            $dateFilter = "AND year( t.date_of_deal )='{$_POST['year4']}'";   
          } 
          
          $deal_subcat2_name = self::$_dealType['subtype2']; 
          $deal_subcat1_name = self::$_dealType['subtype1']; 
          if ($deal_subcat1_name == '') {
            $deal_subcat1_name = 'Completed';  
          }
          $deal_industry = self::$_industry['industry'];
          $deal_country = self::$_country['countryName'];       
          $userCompanyId = @$_SESSION['company_id'];
          $catFilter = " AND deal_subcat2_name = '$deal_subcat2_name' ";
           if (self::$_dealType['subtype2'] == '') {
                $catFilter =  " AND deal_subcat1_name = '$deal_subcat1_name'";
           }           
          $q1 = "SELECT 
                num_deals, 
                partner_id,
                partner_type, 
                total_adjusted_deal_value, 
                total_deal_value, 
                name AS firm_name,
                deal_country,
                deal_industry, 
                deal_subcat2_name
            FROM (

            SELECT count( * ) AS num_deals, partner_id, partner_type, sum( adjusted_value_in_billion ) AS 
            total_adjusted_deal_value, sum( value_in_billion ) AS total_deal_value, deal_country, deal_industry, deal_subcat2_name
            FROM __TP__transaction_partners AS p
            LEFT JOIN __TP__transaction AS t ON ( p.transaction_id = t.id )
            WHERE partner_type = (SELECT type FROM __TP__company WHERE company_id = $userCompanyId LIMIT 1)
             $catFilter             
            AND deal_industry LIKE '$deal_industry'
            AND deal_country LIKE '$deal_country'
            $dateFilter
            AND partner_id <> $userCompanyId
            GROUP BY partner_id
            ORDER BY total_adjusted_deal_value DESC, total_deal_value DESC
            LIMIT 0 , 3
            ) AS stat
            LEFT JOIN tombstone_company AS c ON ( stat.partner_id = c.company_id ) ";
          //dump($q1);   
          if ($res = query($q1)) {
               while($row = mysql_fetch_assoc($res)) {
                   $dt[] =  $row;
               }
               if (self::$_dealType['type'] == 'M&A') {
                    $deal_subcat2_name = $deal_subcat1_name . ' M&A';    
               }
               if (self::$_dealType['subtype2'] == '') {
                    $deal_subcat2_name = $deal_subcat1_name;
               }
               $returnData['table1']['data'] = $dt;
               $returnData['table1']['label'] = "$deal_country, $deal_industry, $deal_subcat2_name, {$_POST['year4']}";
          }
          
          //dump($q1);
          $deal_type = self::$_dealType['type']; 
          $deal_sector = self::$_industry['sector'];
          $deal_region = self::$_country['regionName'];              
            
            $q2 = "SELECT 
                num_deals, 
                partner_id, 
                total_adjusted_deal_value, 
                total_deal_value, 
                name AS firm_name,
                deal_country,
                deal_industry,
                partner_type, 
                deal_subcat2_name
            FROM (

            SELECT count( * ) AS num_deals, partner_id, partner_type, sum( adjusted_value_in_billion ) AS 
            total_adjusted_deal_value, sum( value_in_billion ) AS total_deal_value, deal_country, deal_industry, deal_subcat2_name
            FROM tombstone_transaction_partners AS p
            LEFT JOIN __TP__transaction AS t ON ( p.transaction_id = t.id )
            LEFT JOIN __TP__company AS c ON ( t.company_id = c.company_id )
            LEFT JOIN __TP__country_master cm ON ( c.hq_country = cm.name )
            LEFT JOIN __TP__region_country_list rcl ON ( cm.id = rcl.country_id )
            LEFT JOIN __TP__region_master rm ON ( rcl.region_id = rm.id )            
            WHERE partner_type = (SELECT type FROM tombstone_company WHERE company_id = $userCompanyId LIMIT 1)
            AND deal_cat_name = '$deal_type'
            AND deal_sector LIKE '$deal_sector'
            AND rm.name LIKE '$deal_region'
            $dateFilter
            AND partner_id <> $userCompanyId
            GROUP BY partner_id
            ORDER BY total_adjusted_deal_value  DESC, total_deal_value DESC
            LIMIT 0 , 3
            ) AS stat
            LEFT JOIN __TP__company AS c ON ( stat.partner_id = c.company_id ) ";
          //dump($q2);
          if ($res2 = query($q2)) {
               while($row = mysql_fetch_assoc($res2)) {
                   $dt2[] =  $row;
               }
               if (self::$_dealType['type'] == 'M&A') {
                    $deal_subcat2_name = self::$_dealType['subtype1'] . ' M&A';    
               }               
               $returnData['table2']['data'] = $dt2;
               $returnData['table2']['label'] = "$deal_region, $deal_sector, $deal_type, {$_POST['year4']}";

          }
         return $returnData;                           
      }
      
      static function loadRequestById($id) {
          $sql = "SELECT countryID as country, industryID as industry, dealITypeID as dealType FROM tombstone_oneStop_requests WHERE id = %d and userID = %d";
          $sql = sprintf($sql, $id, $_SESSION['mem_id']);
          if (!$res = mysql_query($sql)) {
              return false;
          }
          $data =  mysql_fetch_assoc($res);
          if (!$data) return false;
          foreach ($data as $key => $val) {
              $_POST[$key] = $val;
          }
          return true;
      } 
      
      static function getSixthTableResults() {
        self::loadOptions('table6');
        $pCountry = self::$_country['countryName'];
        $pRegion = self::$_country['regionName'];
        $pRegionID = self::$_country['region_id'];
        $pIndustry = self::$_industry['industry'];
        $pSector = self::$_industry['sector'];
        $pDealType = self::$_dealType['type'];
        $pDealSubType2  = self::$_dealType['subtype2'];       
        $userCompanyId = @$_SESSION['company_id'];
        $trans = new transaction();
        $oldPost = $_POST;
        unset($_POST);
        $ret = array();
        foreach (self::$_otherDealTypes as $dealType) {
            $transactions = array();
            $_POST['region'] = $pRegion;
            $_POST['deal_cat_name'] = $dealType['type'];
            $_POST['sector'] =  $pSector;
            $_POST['year'] = $oldPost['year5'];
            $_POST['myaction'] = 'filter';            
            $c = $trans->getTombstonesForFirm(@$_SESSION['company_id'], 0,5);
            if (sizeOf($c)) {
                foreach ($c as $deal) {
                    $trans->front_get_deal_detail($deal['transaction_id'], $details, $qqq);
                    $transactions[] = $details;
                }
            }
            $ret[$dealType['type']]['data'] =  $transactions;
            unset($_POST['myaction']);
            $ret[$dealType['type']]['label'] =  $_POST;
            $ret[$dealType['type']]['dataForPost'] =  base64_encode(serialize($_POST));
        }

        
        $_POST = $oldPost;
        return $ret;
      }
      
      static function getPost() {
          return $_POST;
      }  
      
      static function fillEmptyPostForLeagueTables() {
          unset($_POST);
          $fields = array(
            'deal_cat_name'=>'',
            'deal_subcat1_name'=>'',
            'country'=>'',
            'deal_size'=>'',
            'deal_subcat2_name'=>'',
            'industry'=>'',
            'partner_type'=>'bank',
            'ranking_criteria'=>'num_deals',
            'region'=>'',
            'sector'=>'',
            'submit'=>'Filter',
            'myaction'=>'filter',
            'year'=>'',
            
          );
          
          foreach ($fields as $fieldName=>$value) {
              $_POST[$fieldName] = (isset($value) && $value != "") ? $value : '';
          }
      }
      
      static function getResultsInfo() {
          $pSubtype1 =   self::$_dealType['subtype1'];
          //dump(self::$_dealType);
          $myDeal = (self::$_dealType['subtype2'] == '') ? "All $pSubtype1" : self::$_dealType['subtype2'];
          return array(
            'country' => self::$_country['countryName'],
            'industry' => self::$_industry['industry'],
            'deal' => (self::$_dealType['type'] == 'M&A') ? (self::$_dealType['subtype1'] . ' M&A') : $myDeal,
          );
      }
  }
?>