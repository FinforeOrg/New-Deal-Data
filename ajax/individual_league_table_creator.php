<?php
/****
called in ajax code, to generate league table fro individual
***/
include("../include/global.php");
require_once("classes/class.statistics.php");
///////////////////////////////////////
//the data are in $_POST
//We need the top 10, so we start from 0 and get 10
$g_view['data'] = array();
$g_view['data_count'] = 0;
$success = $g_stat->generate_top_individuals_paged($_POST,0,10,$g_view['data'],$g_view['data_count']);
if(!$success){
	echo "Cannot get the top 10";
	return;
}
////////////////////////
if(0==$g_view['data_count']){
	echo "None found";
	return;
}
/////////////////////////////////////
?>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<?php
for($i=0;$i<$g_view['data_count'];$i++){
	?>
	<tr>
	<td><?php echo $i+1;?></td>
	<td>
	<a href="profile.php?mem_id=<?php echo $g_view['data'][$i]['member_id'];?>"><?php echo $g_view['data'][$i]['f_name'];?> <?php echo $g_view['data'][$i]['l_name'];?></a>
	</td>
	<td><?php echo $g_view['data'][$i]['firm_name'];?></td>
	</tr>
	<?php
}
?>
</table>