<?php
  class SavedSearches {
      
      public $favoriteTableName = '';
      public $memeberTable = '';
      public $transTable = '';
      public $transaction = null;
      public $statHelp = null;
      
      function __construct() {
          global  $g_stat_h, $g_trans;
          
          $this->tableName = TP."saved_searches";
          $this->favoriteTableName = TP."favorite_tombstones"; 
          $this->memeberTable = TP."member";
          $this->transTable = TP."transaction";
          if (!isset($g_stat_h))
            require_once('classes/class.stat_help.php'); 
          if (!isset($g_trans))
            require_once('classes/class.transaction.php');
          $this->transaction = $g_trans;
          $this->statHelp = $g_stat_h;
      }
      
      
      function addNew($member_id, $params, $search_type, $isAlert) {
	  	/*********************************
		sng:10/nov/2011
		client: I think we should have Alerts that are not date sensitive
		me: if I save as alert, the Year portion should not be saved? That is I want to save alert for M&A deals in Argentina, 
		year 2010-2011YTD, the code will only store M&A and Argentina?
		client: Yes, exactly
		***********************************/
		if($isAlert){
			$params['year'] = "";
		}
        $params = serialize($params);
        if ($isAlert) {
            $q = "INSERT INTO {$this->tableName} (member_id, parameters, search_type, forAlert,receiveAlerts,lastAlertId) VALUES ('%d','%s','%s','1','1', (SELECT id FROM `{$this->transTable}` WHERE 1 ORDER BY id DESC LIMIT 1))";
        } else {
            $q = "INSERT INTO {$this->tableName} (member_id, parameters, search_type) VALUES ('%d','%s','%s')";
        }
        $result = mysql_query(sprintf($q,$member_id, $params, $search_type )) or die("Mysql Error" . mysql_error());
        return mysql_insert_id();
      }
      
      function loadIntoPost($savedSearchToken) {
          $token = base64_decode($savedSearchToken);
          $q = "SELECT * FROM $this->tableName WHERE id = %d ";
          $res = mysql_query(sprintf($q, $token)); 
          $result = mysql_fetch_assoc($res);
          if (!$result) {
              return false;
          }
          if (!is_array($result))
            return false;
          $savedSearch = unserialize($result['parameters']);
          foreach ($savedSearch as $key=>$value) {
              $_POST[$key] = $value;
          }  
          return true;
      }
      
      function getLastIdForAlert($alertToken) {
          $alertToken = base64_decode($alertToken);
          $q = "SELECT lastAlertId FROM {$this->tableName} WHERE id = {$alertToken}";
          if (!$res = mysql_query($q))
            return false;
          else {
              $result = mysql_fetch_assoc($res);
              return $result['lastAlertId'];
          }
            
      }
      
      function getForUser($userId, $doAlerts = 0, $savedSearchType = null){
          
          $extraAnd = '';
		  /*****************************
		  sng:12/nov/2011
		  The alerts are nothing but saved searches with forAlert=1
		  In saved search page, we have provision to show items that has alert flag on. So I think
		  that in the saved searches section, we show the items whose alert flag in 0
		  ********************************/
          if ($doAlerts) {
              $extraAnd .= "and forAlert = $doAlerts";
          }else{
		  	$extraAnd .= "and forAlert = 0";
		  }
          
          if (!is_null($savedSearchType)) {
              $extraAnd .= ' and search_type = "' . $savedSearchType . '"';
          }
          
          $q = "SELECT * FROM {$this->tableName} WHERE member_id = %d $extraAnd";
          $res = mysql_query($q2 = sprintf($q, $userId));
          //echo $q2;
          $searches = array('tombstones'=>array(),'deals'=>array(),'league'=>array(), 'leagueDetail'=>array());
          while ($row = mysql_fetch_assoc($res)) {
              $searches[$row['search_type']][$row['id']] = $this->cleanAndTranslate($row['parameters']);
              if ('leagueDetail' == $row['search_type']) {
                  $searches['enabledNotifications'][$row['id']] = $row['forAlert'];
                  $searches['currentRanks'][$row['id']] = $this->getCompanyRank($_SESSION['company_id'], $row);
              }
          }
          return $searches;
      }
      
      function getAlertForUser($userId) {
          return $this->getForUser($userId,1, 'deal');
      }
      
      function getById($searchId) {
          $q = "SELECT * FROM {$this->tableName} WHERE member_id = %d AND id = %d LIMIT 1";
          $res = mysql_query($q2 = sprintf($q, $_SESSION['mem_id'],$searchId)); 
          return mysql_fetch_assoc($res);     
      }
      
      function loadTombstonesFromQuery($token) {
          $res = mysql_query(base64_decode($token));
          $ret = false;
          while ($row = mysql_fetch_assoc($res)) {
              $ret[] = $row;
          }
          return $ret;
      }
      
      function cleanAndTranslate($row) {
          $row = unserialize($row);
          if (!is_array($row))
            return false;
          
          return Util::cleanAndTranslate($row);
      }
      
      function searchBelongsToTheCurrentUser($search) {
          $q = "SELECT COUNT(*) as number FROM {$this->tableName} WHERE member_id = %d AND id = %d";
          $res = mysql_query(sprintf($q, $_SESSION['mem_id'], $search));
          if($res) {
              $result = mysql_fetch_assoc($res);
              if ($result['number'])
                return true;
              else
                return false;              
          } else {
              return false;
          }
      }
      
      function searchCanBeImported($search) {
          return false;
          
          /**
          * Import is not needed as when the user hits the link he gets presented the option to save
          */
      }
      
      function updateSearch($id, $newParams, $alert = 0) {
          if ($alert) {
              $q = "UPDATE {$this->tableName} SET parameters = '%s', lastAlertId = (SELECT id FROM `{$this->transTable}` WHERE 1 ORDER BY id DESC LIMIT 1) WHERE id = %d";
          } else 
          $q = "UPDATE {$this->tableName} SET parameters = '%s' WHERE id = %d";
          return mysql_query(sprintf($q, $newParams, $id));
      }
      
      function currentUserCanStillAdd($type,$alert = 0) {
	  		/****************
			sng:7/oct/2011
			members can now save more than 3 searches
			
          $q = "SELECT COUNT(*) as number FROM {$this->tableName} WHERE member_id = %d AND search_type = '%s' AND forAlert = '$alert'";
          $res = mysql_query(sprintf($q, $_SESSION['mem_id'], $type));
          $result = mysql_fetch_assoc($res);
          return $result['number'] < 3;
		  **************************/
		  return true;
      }
      
      function deleteSearch($id, $type) {
          $q= "DELETE FROM {$this->tableName} WHERE member_id = %d AND search_type = '%s' AND id= %d";
          return mysql_query(sprintf($q, $_SESSION['mem_id'],$type, $id ));
      }
      
      function getFavoriteTombstones() {
          $favorites = false;
          if (!isset($_SESSION['mem_id'])) 
            return false;
          $q = "SELECT * FROM {$this->favoriteTableName} WHERE member_id = %d";
          $res = mysql_query($qq  = sprintf($q,$_SESSION['mem_id']));
          if (!$res) 
            return false;
          while ($row = mysql_fetch_assoc($res)) 
            $favorites[] = $row['tombstone_id'];
          $this->favoriteTombstones = $favorites;
          return $favorites;
      }
      
      function tombstoneIsFavorite($id) {
          return @in_array($id,$this->favoriteTombstones);
      }
      
      function deleteFavoriteTombstone($id) {
          if (!isset($_SESSION['mem_id'])) {
              return false;
          }
          $q = "DELETE FROM  {$this->favoriteTableName} WHERE tombstone_id = %d AND member_id = %d";
          $q = sprintf($q, $id, $_SESSION['mem_id']);
          return mysql_query($q);
      }
      function addFavoriteTombstone($id) {

          $q = "INSERT INTO   {$this->favoriteTableName}  ( member_id, tombstone_id ) VALUES (%d, %d)";
          $q = sprintf($q, $_SESSION['mem_id'],$id);
          $res = mysql_query($q);
          if ($res)
            return mysql_insert_id();
          return false;
      }
      
      function setLeagueTableNotificationAlertState($notifId, $state, $userId) {
          $rank = $this->getCompanyRank($_SESSION['company_id'], $notifId);
          $q = sprintf("UPDATE {$this->tableName} SET forAlert = %d, currentRank = %d, last_alert_date = NOW() WHERE id = %d AND member_id = %d", $state, $rank, $notifId, $userId) ;
          //echo $q;
          return mysql_query($q);
      }   
      
      function loadIntoPostByParams($params) {
          $params = unserialize($params);
          if (!is_array($params) || !sizeOf($params)) {
              return false;
          }
          unset($_POST);
          foreach ($params as $key=>$value) {
              $_POST[$key] = $value;
          }
          
      }
      /**
       * Method used to fetch company rank for the provided saved search
       * 
       * @param integer $companyId
       * @param integer $savedSearch
       * @return array 
       */
      function getCompanyRank($companyId, $savedSearch, $returnOnlyRank = true)
      {
        if (!is_array($savedSearch)){
          $this->loadIntoPost(base64_encode($savedSearch));
        } else {
           $this->loadIntoPostByParams($savedSearch['parameters']);
        }

        $queryWhereClauses = '';
        //////////////////////////////////////////
        //filter on transaction types
        if($_POST['deal_cat_name'] != ''){
            $queryWhereClauses .= " and deal_cat_name='".$_POST['deal_cat_name']."'";
        }
        if($_POST['deal_subcat1_name']!=""){
            $queryWhereClauses .= " and deal_subcat1_name='".$_POST['deal_subcat1_name']."'";
        }
        if($_POST['deal_subcat2_name']!=""){
            $queryWhereClauses .= " and deal_subcat2_name='".$_POST['deal_subcat2_name']."'";
        }
		/**************
		sng:16/aug/2012
		Now that we have participants for a deal, we do not check the deal_sector, deal_industry csv fields for the deal.
		We check the sector/industry/country of the participants and consider only those deals
		***************/
        if($_POST['year'] != ''){
            $year_tokens = explode('-',$_POST['year']);
            $year_tokens_count = count($year_tokens);
            if($year_tokens_count == 1){
                //singleton year
                $queryWhereClauses .= " and year(date_of_deal)='".$year_tokens[0]."'";
            }
            if($year_tokens_count == 2){
                //range year
                $queryWhereClauses .= " and year(date_of_deal)>='".$year_tokens[0]."' AND year(date_of_deal)<='".$year_tokens[1]."'";
            }
        }
		/***********
		sng:6/sep/2012
		The deal size is like >=x or <=y
		**************/
		
        if($_POST['deal_size']!=""){
			$_POST['deal_size'] = Util::decode_deal_size($_POST['deal_size']);
            $queryWhereClauses.=" and value_in_billion".$_POST['deal_size'];
        }
        /**************
		sng:16/aug/2012
		Now that we have participants for a deal, we do not check the deal_country csv fields for the deal.
		We check the hq_country of the participants and consider only those deals
		
		Also we checked but $company_filter is not used
		***************/
        $filter_by_company_attrib = "";
		if(isset($_POST['country'])&&($_POST['country']!="")){
			if($filter_by_company_attrib != ""){
				$filter_by_company_attrib = $filter_by_company_attrib." AND ";
			}
			$filter_by_company_attrib.="hq_country='".mysql_real_escape_string($_POST['country'])."'";
		}else{
			/**********
			might check region. Associated with a region is one or more countries
			We can use IN clause, that is hq_country IN (select country names for the given region), but it seems that
			it is much faster if we first get the country names and then create the condition with OR, that is
			(hq_country='Brazil' OR hq_country='Russia') etc
			***********/
			if(isset($_POST['region'])&&($_POST['region']!="")){
				//get the country names for this region name
				$region_q = "SELECT ctrym.name FROM ".TP."region_master AS rgnm LEFT JOIN ".TP."region_country_list AS rcl ON ( rgnm.id = rcl.region_id ) LEFT JOIN ".TP."country_master AS ctrym ON ( rcl.country_id = ctrym.id )
WHERE rgnm.name = '".mysql_real_escape_string($_POST['region'])."'";
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
			
		if(isset($_POST['sector'])&&($_POST['sector']!="")){
			if($filter_by_company_attrib != ""){
				$filter_by_company_attrib = $filter_by_company_attrib." AND ";
			}
			$filter_by_company_attrib.="sector='".mysql_real_escape_string($_POST['sector'])."'";
		}
			
		if(isset($_POST['industry'])&&($_POST['industry']!="")){
			if($filter_by_company_attrib != ""){
				$filter_by_company_attrib = $filter_by_company_attrib." AND ";
			}
			$filter_by_company_attrib.="industry='".mysql_real_escape_string($_POST['industry'])."'";
		}
		/********************************************************/
        $queryWhereClauses.=" GROUP BY partner_id";

        $ranking_by = '';
        if ($_POST['ranking_criteria'] != '') {
            $ranking_by = $_POST['ranking_criteria'];
        }

        if($ranking_by != ''){
            $queryWhereClauses.=" ORDER BY ".$ranking_by." DESC";
        }

        $query = '
            select * from (
                SELECT num_deals, partner_id, total_adjusted_deal_value, total_deal_value, name AS firm_name,  @rownum := @rownum + 1 AS rank
                FROM (
                    SELECT count( * ) AS num_deals, 
                            partner_id, 
                            sum( adjusted_value_in_billion ) AS total_adjusted_deal_value, 
                            sum( value_in_billion ) AS total_deal_value  
                   FROM __TP__transaction_partners AS p
                   LEFT JOIN __TP__transaction AS t ON ( p.transaction_id = t.id )';
		/***************
		sng:13/aug/2012
		snippet
		Why inner join? We want to consider only those deals on the left which satisfy the conditions on the right
		***********/
		if($filter_by_company_attrib!=""){
			$query.=" INNER JOIN (SELECT DISTINCT transaction_id from ".TP."transaction_companies as fca_tc left join ".TP."company as fca_c on(fca_tc.company_id=fca_c.company_id) where fca_c.type='company' AND ".$filter_by_company_attrib.") AS fca ON (t.id=fca.transaction_id) ";
		}
                   $query.=' WHERE partner_type = "%s"
                       %s
                   LIMIT 0, 11
                )   AS stat
                LEFT JOIN __TP__company AS c ON ( stat.partner_id = c.company_id )
            ) as finalResult
            WHERE partner_id = %d
        ';
        $query = sprintf($query, $_POST['partner_type'], $queryWhereClauses, $companyId);
        $query = str_replace('__TP__', TP, $query);
        mysql_query('set @rownum := 0');

        if (!$res = mysql_query($query)) {
		echo $query;
            echo mysql_error();
            return 0;
        }
        
        $result = mysql_fetch_assoc($res);
        if ($returnOnlyRank) {
            return  $result['rank'];
        }
        
        return $result;
      }
      
      
      public function getTransactionsAddedAfter($savedSearch)
      {
        if (!is_array($savedSearch)){
          $this->loadIntoPost(base64_encode($savedSearch));
        } else {
           $this->loadIntoPostByParams($savedSearch['parameters']);
        }
        $g_view['start_offset'] = 0;
        $g_view['num_to_show'] = 10;
        
        $_POST['last_alert_date']  = $savedSearch['last_alert_date'];

        $this->transaction->front_deal_search_paged($_POST, $g_view['start_offset'], $g_view['num_to_show']+1, $g_view['data'], $g_view['data_count']); 
        
        return $g_view['data'];
      }
      
      public function updateTable($data, $id = null) {
          if (!is_array($data) || is_null($id)) {
              return false;
          }
          $fields = array();
          foreach ($data as $key=>$value) {
              $fields[] = sprintf("%s = '%s'", $key, $value); 
          }
          $fields = implode(', ', $fields);
          $q = 'UPDATE %s 
                SET %s
                WHERE id = %d';
          $q = sprintf($q, $this->tableName, $fields, $id);
          
          return mysql_query($q);
      }
      
      public function getNotificationsForUser($userId = null) 
      {
          $data = array();
          
          if (is_null($userId)) {
              if (!isset($_SESSION['mem_id'])) {
                  return $data;
              } else {
                  $userId = $_SESSION['mem_id'];
              }
          }
          
          $q = 'SELECT * FROM __TP__saved_searches_history
              WHERE mem_id = ' . (int) $userId . ' ORDER BY id DESC' ;
          
          if (!$res = query($q)) {
              return $data;
          }
          
          while ($row = mysql_fetch_assoc($res)) {
              $data[$row['id']] = $row;
              $data[$row['id']]['old_rank'] = $this->decToOrd($row['old_rank']);
              $data[$row['id']]['new_rank'] = $this->decToOrd($row['new_rank']);
              $data[$row['id']]['parameters'] = $this->cleanAndTranslate($row['parameters']);
              if ($row['places'] > 0) {
                  $data[$row['id']]['class'] = 'positive';
              } else {
                  $data[$row['id']]['class'] = 'negative';
              }
          }
          
          return $data;
      }
      
      public function decToOrd($nr) 
      {
          return sprintf( "%d%s", $nr, array_pop( array_slice( array_merge( array( "th","st","nd","rd"), array_fill( 4,6,"th")), $nr%10, 1)));
      }
      
      public function getNotificationFromHistory($notificationId) 
      {
          $q = 'SELECT * FROM __TP__saved_searches_history
              WHERE id = ' . (int) $notificationId . ' ORDER BY id DESC';
          
          if (!$res = query($q)) {
              return false;
          }
          
          return mysql_fetch_assoc($res);
      }
  }
?>
