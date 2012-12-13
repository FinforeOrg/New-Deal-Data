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
			/******************
			sng:3/dec/2012
			Ok, here is the problem. For League Table Details (tombstone_saved_searches, search_type of leagueDetail), if we check the
			'notify me' checkbox, the ajax code set the forAlert to 1.
			Problem is, in the listing, the function getForUser() collects only the records for which forAlert is 0.
			That means, the if you check the 'notify me' checkbox, it just vanish.
			
			In the meanwhile, the alert listing code only get the records for 'deal' type.
			What we do is, we get alert for all types
			return $this->getForUser($userId,1, 'deal');
			
			Of course, we cannot touch the forAlert because that field is used by cron job to send notification
			*************************/
			return $this->getForUser($userId,1);
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
	   /******************
		sng:13/dec/2012
		savedSearches::getgetForUser > call with no third arg so only rank is true
		savedSearches::setLeagueTableNotificationAlertState > call with no third arg so only rank is true
		cron/leagueTablePositionchangeNotifications.php > call with no third arg so only rank is true
		
		So basically every caller is expecting a number
		
		We can remove the third arg
		function getCompanyRank($companyId, $savedSearch, $returnOnlyRank = true)
		************/
      function getCompanyRank($companyId, $savedSearch)
      {
	  	require_once("classes/class.statistics.php");
		$stat = new statistics();
		
        if (!is_array($savedSearch)){
          $this->loadIntoPost(base64_encode($savedSearch));
        } else {
           $this->loadIntoPostByParams($savedSearch['parameters']);
        }

        $start_offset = 0;
		$num_to_fetch = 11;
		$data_arr = NULL;
		$data_count = 0;
		$rank = 0;
		
		$ok = $stat->front_generate_league_table_for_firms_paged($_POST,$start_offset,$num_to_fetch,$data_arr,$data_count);
		if(!$ok){
			return $rank;
		}
		
		if($data_count==0){
			return $rank;
		}
		/***********
		sng:13/dec/2012
		If we get an array of records, we will have to scroll through each to get the record for that partner. The rank = counter+1
		Of course, there may not be any record for the partner
		************/
		for($i=0;$i<$data_count;$i++){
			if($data_arr[$i]['partner_id']==$companyId){
				$rank = $i+1;
				break;
			}
		}
		return $rank;
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
