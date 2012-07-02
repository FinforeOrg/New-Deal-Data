<?php
/****
used by admin to get bankers and lawyers to add to deal team
******/
include("../../include/global.php");
require_once("classes/class.member.php");
require_once("classes/class.magic_quote.php");
/////////////
$f_name = $g_mc->view_to_db($_POST['first_name']);
$l_name = $g_mc->view_to_db($_POST['last_name']);
$member_type = $_POST['type'];
$data_arr = array();
$data_cnt = 0;
$success = $g_mem->admin_get_all_member_by_type($f_name,$l_name,$member_type,$data_arr,$data_cnt);
if(!$success){
	echo "Error";
	exit;
}
if(0==$data_cnt){
	echo "None found";
	exit;
}
//data found so loop and create table structure
?>
<table width="100%" border="1" style="border-collapse:collapse">
<tr>
<td>&nbsp;</td>
<td><strong>First name</strong></td>
<td><strong>Last name</strong></td>
<td><strong>Firm</strong></td>
<td><strong>work email</strong></td>
</tr>
<?php
for($i=0;$i<$data_cnt;$i++){
?>
<tr>
<td><input type="radio" name="member_id" value="<?php echo $data_arr[$i]['mem_id'];?>" /></td>
<td><?php echo $data_arr[$i]['f_name'];?></td>
<td><?php echo $data_arr[$i]['l_name'];?></td>
<td><?php echo $data_arr[$i]['company_name'];?></td>
<td><?php echo $data_arr[$i]['work_email'];?></td>

</tr>
<?php
}
?>
</table>