<?php
/***********
q:Quarterly
h: Semi-Annual (half yearly)
y: Annual (yearly)
see class stat help
*******/
include("../include/global.php");
require_once("classes/class.stat_help.php");
$value_arr = NULL;
$label_arr = NULL;


$g_stat_h->volume_get_month_div_entries($_POST['month_div'],$value_arr,$label_arr);
$cnt = count($value_arr);
/**********************************
sng:18/oct/2011
if quarterly is selected, there are lots of data points. Client wants to start from
1Q 2010. So we want to preselect 1Q 2010
But actually, this is just a ploy to show the last 8 to 10 data.
A better idea is to see how many points are there, and put the selected in such a way
that only last 8 quarters are shown in the chart
************************************/
//default
$preselect_offset = 0;
if($cnt > 8){
	$preselect_offset = $cnt-8;
}
for($i=0;$i<$cnt;$i++){
	?>
	<option value="<?php echo $value_arr[$i];?>" <?php if($i==$preselect_offset){?>selected="selected"<?php }?>><?php echo $label_arr[$i];?></option>
	<?php
}
return;
?>