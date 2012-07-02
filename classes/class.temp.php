<?
class temp
{

  ////////////////////////////////////SNG front deal search////////////////////////////////////////////
	/***
	search only for deal
	*****/
	public function front_deal_search_paged($search_data,&$data_arr,&$data_count){
		global $g_mc;
		
		//$search_data = $g_mc->view_to_db($search_data);
		
        
        $q="SELECT * FROM ".TP."transaction WHERE ";
		$q_search=$q.'';
			 
        if($search_data['location']!='')
		{
		  $q_search .= " deal_country='".$search_data['location']."' ";
		}
	  if($search_data['type']!='')
		{
		  if($search_data['location']!='') $q_search .=" AND ";
		  $q_search .=" deal_cat_name='".$search_data['type']."' ";
		}
		if($search_data['sector']!='')
		{
		  if($search_data['type']!='') $q_search .=" AND ";
		  $q_search .=" company_id IN (SELECT company_id FROM ".TP."company WHERE sector='".$search_data['sector']."')";
		}
		if($search_data['date']!='')
		{
		  if($search_data['sector']!='') $q_search .=" AND ";
		  $q_search .=" date_of_deal like '".$search_data['date']."%' ";
		}
		if($search_data['date']=='' && $search_data['location']=='' && $search_data['type']=='' && $search_data['sector']=='')
		{
		  $q_search="SELECT * FROM ".TP."transaction ";
		}
		echo $q_search;
		$res = mysql_query($q_search);
		if(!$res){
			return false;
		}
		$data_count = mysql_num_rows($res);
		if(0==$data_count){
			//no data so get out
			return true;
		}
		for($i=0;$i<$data_count;$i++){
			$data_arr[$i] = mysql_fetch_assoc($res);
			
		}
		return true;
		/*$q = "select * from ".TP."company where type='company' and ( name like '".$search_data."%' or industry like '%".$search_data."%') limit ".$start_offset.",".$num_to_fetch;
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		//////////////////////////////
		$data_count = mysql_num_rows($res);
		if(0==$data_count){
			//no data so get out
			return true;
		}
		////////////////////////////
		for($i=0;$i<$data_count;$i++){
			$data_arr[$i] = mysql_fetch_assoc($res);
			
		}
		return true;*/
	}
	////////////////////////////////////SNG front end company search////////////////////////////////////////////
}
$g_temp = new temp(); 	
?>
