<?php
/******************************
sng:8/mar/2011
Need to find the case studies for this deal by the firm of the user. Of course
if the user is not logged in, there is no case studies

sng:17/nov/2011
This is now ajax

sng:17/nov/2011
Now we have access rights for each case studies of a deal. However, it is agreed that
non logged in user cannot view case study.
**********************************/
require_once("../include/global.php");
require_once("classes/class.account.php");
/*************************************************/
if(!$g_account->is_site_member_logged()){
	echo "You need to login first";
	exit;
}
/*****************************************************/
require_once("classes/class.transaction.php");

$g_view['deal_id'] = $_GET['deal_id'];
$g_view['case_study_count'] = 0;
$g_view['case_study'] = array();

//$success = $g_trans->front_get_case_studies_for_partner($g_view['deal_id'],$_SESSION['company_id'],$g_view['case_study'],$g_view['case_study_count']);
$success = $g_trans->front_get_case_studies($g_view['deal_id'],$g_view['case_study'],$g_view['case_study_count']);
if(!$success){
	echo "Cannot get case studies";
	exit;
}
/***************************************************
sng:8/mar/2011
case study section
********/
?>
<table width="100%" cellpadding="0" cellspacing="0">
<?php
if($g_view['case_study_count'] > 0){
	?>
	<tr><td style="height:10px;">&nbsp;</td></tr>
	<tr>
	<td>
		<table cellpadding="0" cellspacing="0" class="registercontent">
			<tr>
				<th>Case Studies</th>
			</tr>
			<tr>
			<td>
			<table cellpadding="5" cellspacing="5" class="company">
			<?php
			for($cs = 0;$cs < $g_view['case_study_count'];$cs++){
				?>
				<tr>
				<td style="width:50px;">
				<?php if($g_view['case_study'][$cs]['flag_count'] > 0){?><img src="images/icon_red_flag.gif" />&nbsp;<?php echo $g_view['case_study'][$cs]['flag_count']; }?>
				</td>
				<td style="width:20px;"><?php if('y'==$g_view['case_study'][$cs]['is_approved']){?><img src="images/approved.png" /><?php }?></td>
				<td><?php echo $g_view['case_study'][$cs]['caption'];?></td>
				
				<td><?php echo $g_view['case_study'][$cs]['file_type'];?></td>
				<td style="width:100px;">
				<form method="post" action="download_case_study.php" target="_blank">
				<input type="hidden" name="case_study_id" value="<?php echo $g_view['case_study'][$cs]['case_study_id'];?>" />
				<input type="submit" name="submit" class="btn_auto" value="Download" />
				</form>
				</td>
				
				<td style="width:50px;">
				<input type="button" class="btn_auto" value="Flag" onclick="open_flag_popup(<?php echo $g_view['case_study'][$cs]['case_study_id'];?>)" />
				</td>
				
				</tr>
				<?php
			}
			?>
			</table>
			</td>
			</tr>
			<tr><td><img src="images/approved.png" />&nbsp;Verified Case Study</td></tr>
			<tr><td><img src="images/icon_red_flag.gif" />&nbsp;Flagged for Review</td></tr>
			<tr><td style="height:10px;">&nbsp;</td></tr>
		</table>
	</td>
	</tr>
	<?php
}else{
	?>
	<tr><td style="height:10px;">&nbsp;</td></tr>
	<tr><td>No participating bank or law firm has added a case study, that is available for you to download</td></tr>
	<tr><td style="height:10px;">&nbsp;</td></tr>
	<?php
}
/*********************************************************************************/
?>
</table>
<script>
$('.btn_auto').button();
</script>