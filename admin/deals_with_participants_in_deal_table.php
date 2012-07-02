<?php
require_once("../include/global.php");
require_once ("admin/checklogin.php");

///////////////////////////////////////////////////////
$g_view['msg'] = "";

$g_view['data_count'] = 0;
$g_view['data'] = array();
$g_view['num_to_show'] = 50;
$g_view['start'] = 0;
if(isset($_REQUEST['start'])&&($_REQUEST['start']!="")){
	$g_view['start'] = $_REQUEST['start'];
}
/////////////////
$q = "SELECT id, target_company_name, target_country AS target_company_country, target_sector AS target_company_sector, target_industry AS target_company_industry, seller_company_name, seller_country AS seller_company_country, seller_sector AS seller_company_sector, seller_industry AS seller_company_industry, buyer_subsidiary_name, buyer_subsidiary_country, buyer_subsidiary_sector, buyer_subsidiary_industry, c.name AS buyer_company_name, c.hq_country AS buyer_company_country, c.sector AS buyer_company_sector, c.industry AS buyer_company_industry FROM ".TP."transaction AS t LEFT JOIN (SELECT DISTINCT transaction_id FROM ".TP."transaction_companies) AS p ON ( t.id = p.transaction_id ) LEFT JOIN ".TP."transaction_extra_detail AS ex ON ( t.id = ex.transaction_id ) LEFT JOIN ".TP."company AS c ON ( t.company_id = c.company_id ) WHERE p.transaction_id IS NULL LIMIT ".$g_view['start']." , ".($g_view['num_to_show']+1);

$res = mysql_query($q);
if(!$res){
	echo mysql_error();
	die("Cannot get list");
}
$g_view['data_count'] = mysql_num_rows($res);
while($row = mysql_fetch_assoc($res)){
	$g_view['data'][] = $row;
}
////////////////////////////////////////////////
$g_view['heading'] = "List of deals with participants still in deal table";
$g_view['content_view'] = "admin/deals_with_participants_in_deal_table_view.php";
include("admin/content_view.php");
?>