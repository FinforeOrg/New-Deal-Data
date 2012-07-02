<?php
class probe{
	public function front_get_all_top_search_request(&$data_arr,&$data_count,$cutoff_date){
		global $g_mc;
		
		$q = "select req.job_id,req.mem_id,req.submitted_on,req.company_id,req.type,req.option_country,req.option_deal_type,req.option_sector_industry,req.rank_requested,req.extended_search,req.status,req.is_scheduled,req.started_on,req.dbg_last_processing_time,req.finished_on,req.dbg_status,c.name as country_name,d.name as deal_name,i.name as industry_name from ".TP."top_search_request as req left join ".TP."top_search_option_country as c on(req.option_country=c.option_id) left join ".TP."top_search_option_deal_type as d on(req.option_deal_type=d.option_id) left join ".TP."top_search_option_sector_industry as i on(req.option_sector_industry=i.option_id)";
		if($cutoff_date!=""){
			$q.=" where req.submitted_on >= '".$cutoff_date." 00:00:00'";
		}else{
			//get all todays jobs
			$today = date("Y-m-d");
			$q.=" where req.submitted_on >= '".$today." 00:00:00'";
		}
		$q.= " order by submitted_on desc";
		
		$res = mysql_query($q);
		if(!$res){
			return false;
		}
		$data_count = mysql_num_rows($res);
		if(0==$data_count){
			return true;
		}
		for($i=0;$i<$data_count;$i++){
			$data_arr[$i] = mysql_fetch_assoc($res);
			$data_arr[$i]['country_name'] = $g_mc->db_to_view($data_arr[$i]['country_name']);
			$data_arr[$i]['deal_name'] = $g_mc->db_to_view($data_arr[$i]['deal_name']);
			$data_arr[$i]['industry_name'] = $g_mc->db_to_view($data_arr[$i]['industry_name']);
		}
		return true;
	}
}
$g_probe = new probe();
?>