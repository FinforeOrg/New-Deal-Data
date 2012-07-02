<?php
/**************************
sng:17/nov/2011
for data-cx, we do not allow admin to upload case study

sng:19/nov/2011
for data-cx, we do not allow to delete from here.
*****************************/
?>
<table width="100%" cellpadding="5" cellspacing="0" border="1" style="border-collapse:collapse;">
<?php
if($g_view['msg']!=""){
?>
<tr>
<td colspan="6"><span class="msg_txt"><?php echo $g_view['msg'];?></span></td>
</tr>
<?php
}
?>


<tr><td colspan="6" style="height:10px;">&nbsp;</td></tr>
<tr>
<td>
	<table width="100%" cellpadding="5" cellspacing="0" border="0">
	<tr>
	<td>Firm</td>
	<td>Case Study</td>
	<td>Uploaded by</td>
	<td>&nbsp;</td>
	<td colspan="2">&nbsp;</td>
	</tr>
	<?php
	if($g_view['bank_case_studies_cnt'] == 0){
		?>
		<tr><td colspan="6">None uploaded</td></tr>
		<?php
	}else{
		for($i=0;$i<$g_view['bank_case_studies_cnt'];$i++){
			?>
			<tr>
			<td><?php echo $g_view['bank_case_studies'][$i]['company_name'];?></td>
			<td><?php echo $g_view['bank_case_studies'][$i]['caption'];?><br />[<?php echo $g_view['bank_case_studies'][$i]['filename'];?>]</td>
			<td>
			<?php
			if($g_view['bank_case_studies'][$i]['mem_id']!=0){
				echo $g_view['bank_case_studies'][$i]['f_name']." ".$g_view['bank_case_studies'][$i]['l_name'];
			}else{
				echo "admin";
			}
			?>
			</td>
			<td><?php if($g_view['bank_case_studies'][$i]['is_approved']=='y'){?><img src="images/featured.png" /> <?php }?></td>
			<td>
			<form method="post" action="download_case_study.php" target="_blank">
			<input type="hidden" name="case_study_id" value="<?php echo $g_view['bank_case_studies'][$i]['case_study_id'];?>" />
			<input type="submit" name="submit" class="btn_auto" value="Download" />
			</form>
			</td>
			<!--<td>
			<form method="post" action="">
			<input type="hidden" name="action" value="delete" />
			<input type="hidden" name="transaction_id" value="<?php echo $_POST['transaction_id'];?>" />
			<input type="hidden" name="case_study_id" value="<?php echo $g_view['bank_case_studies'][$i]['case_study_id'];?>" />
			<input type="submit" name="submit" value="Delete" />
			</form>
			</td>-->
			</tr>
			<?php
		}
	}
	?>
	</table>
</td>
</tr>
<tr><td colspan="6" style="height:10px;">&nbsp;</td></tr>

<tr><td colspan="6" style="height:10px;">&nbsp;</td></tr>
<tr>
<td>
	<table width="100%" cellpadding="5" cellspacing="0" border="0">
	<tr>
	<td>Firm</td>
	<td>Case Study</td>
	<td>Uploaded by</td>
	<td>&nbsp;</td>
	<td colspan="2">&nbsp;</td>
	</tr>
	<?php
	if($g_view['law_firm_case_studies_cnt'] == 0){
		?>
		<tr><td colspan="6">None uploaded</td></tr>
		<?php
	}else{
		for($i=0;$i<$g_view['law_firm_case_studies_cnt'];$i++){
			?>
			<tr>
			<td><?php echo $g_view['law_firm_case_studies'][$i]['company_name'];?></td>
			<td><?php echo $g_view['law_firm_case_studies'][$i]['caption'];?><br />[<?php echo $g_view['law_firm_case_studies'][$i]['filename'];?>]</td>
			<td>
			<?php
			if($g_view['law_firm_case_studies'][$i]['mem_id']!=0){
				echo $g_view['law_firm_case_studies'][$i]['f_name']." ".$g_view['law_firm_case_studies'][$i]['l_name'];
			}else{
				echo "admin";
			}
			?>
			</td>
			<td><?php if($g_view['law_firm_case_studies'][$i]['is_approved']=='y'){?><img src="images/featured.png" /> <?php }?></td>
			<td>
			<form method="post" action="download_case_study.php" target="_blank">
			<input type="hidden" name="case_study_id" value="<?php echo $g_view['law_firm_case_studies'][$i]['case_study_id'];?>" />
			<input type="submit" name="submit" class="btn_auto" value="Download" />
			</form>
			</td>
			<td>
			<form method="post" action="">
			<input type="hidden" name="action" value="delete" />
			<input type="hidden" name="transaction_id" value="<?php echo $_POST['transaction_id'];?>" />
			<input type="hidden" name="case_study_id" value="<?php echo $g_view['law_firm_case_studies'][$i]['case_study_id'];?>" />
			<input type="submit" name="submit" value="Delete" />
			</form>
			</td>
			</tr>
			<?php
		}
	}
	?>
	</table>
</td>
</tr>
</table>