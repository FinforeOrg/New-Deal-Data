<?php
/*************
used in admin to get the corrections for a deal, for a particular field
***********/
require_once("../../include/global.php");

require_once("classes/class.account.php");
if(!$g_account->is_admin_logged()){
	echo "admin not logged";
	return;
}
require_once("classes/class.company.php");

$company_id = $_POST['company_id'];
$company_field = $_POST['data_name'];
$result_arr = array();
$result_count = 0;
$success = $g_company->admin_fetch_data_correction_on_company($company_id,$company_field,$result_arr,$result_count);

if(!$success){
	echo "Error fetching correction data";
	return;
}
if(0 == $result_count){
	echo "None found";
	return;
}
?>
<table cellspacing="0" bordercolor="#CCCCCC"; border="1" style="border-collapse:collapse;">
<?php
for($i=0;$i<$result_count;$i++){
	?>
	<tr>
	<td style="padding:2px 10px 2px 10px"><strong><?php echo $result_arr[$i]['data'];?></strong></td>
	<td style="padding:2px 10px 2px 10px"><?php echo $result_arr[$i]['f_name'];?> <?php echo $result_arr[$i]['l_name'];?> [<?php echo $result_arr[$i]['designation'];?>] <?php echo $result_arr[$i]['work_company'];?></td>
	<td style="padding:2px 10px 2px 10px"><?php echo date("Y-m-d",strtotime($result_arr[$i]['date_suggested']));?></td>
	</tr>
	<?php
}
?>
</table>