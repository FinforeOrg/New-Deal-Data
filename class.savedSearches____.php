<?php
  class SavedSearches {
      
      
      function  __construct() {
          $this->tableName = TP."saved_searches";
      }
      
      
      function addNew($member_id, $params, $search_type) {
          $params = serialize($params);
          echo $params;
        $q = "INSERT INTO {$this->tableName} (member_id, parameters, search_type) VALUES ('%d','%s','%s')";
            $result = mysql_query(sprintf($q,$member_id, $params, $search_type )) or die("Mysql Error" . mysql_error());
        return $result;
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
      
      function getForUser($userId){
          $q = "SELECT * FROM {$this->tableName} WHERE member_id = %d";
          $res = mysql_query(sprintf($q, $userId));
          $searches = array();
          while ($row = mysql_fetch_assoc($res)) {
              $searches[$row['search_type']][$row['id']] = $this->cleanAndTranslate($row['parameters']);
          }
          return $searches;
      }
      
      function cleanAndTranslate($row) {
          $row = unserialize($row);
          $cleanParams = array();
          if (!is_array($row))
            return false;
          foreach($row as $key=>$param) {
              if ($param != '' && $key != 'myaction') {
                  $cleanParams[] = $param;
              }
          }
          return implode(", ", $cleanParams);
      }
      
      function searchBelongsToTheCurrentUser($search) {
          $q = "SELECT COUNT(*) as number FROM {$this->tableName} WHERE member_id = %d AND id = %d";
          $res = mysql_query(sprintf($q, $_SESSION['mem_id'], $search));
          $result = mysql_fetch_assoc($res);
          var_dump($result);
      }
  }
?>
