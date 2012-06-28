<table width="100%" cellpadding="0" cellspacing="0">


<?php
if($g_view['deal_found']){
	?>
	<tr>
	<td>
		<!--deal company, value data-->
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<tr>
		<td>
		<h4><?php echo $g_view['deal_data']['company_name'];?> (<?php echo $g_view['deal_data']['deal_cat_name'];?>)</h4>
		</td>
		<td>
		<h4><?php if($g_view['deal_data']['value_in_billion']==0) echo "Value not disclosed"; else echo "$".convert_billion_to_million_for_display($g_view['deal_data']['value_in_billion'])."m";?></h4>
		</td>
		<td>
		<h4><?php echo ymd_to_dmy($g_view['deal_data']['date_of_deal']);?></h4>
		</td>
		</tr>
		</table>
		<!--deal company, value data-->
	</td>
	</tr>
	<tr>
	<td>
		<!--deal data-->
		<table cellpadding="0" cellspacing="0">
		<tr>
		<td style="width:200px;">
			<!--tombstone-->
			<?php
			$g_trans->get_tombstone_from_deal_id($g_view['deal_data']['deal_id']);
			?>
			<!--tombstone-->
		</td>
		<td style="width:10px;">&nbsp;</td>
		<td>
			<!--deal data, bankers lawyers-->
			<table cellpadding="0" cellspacing="0">
			<?php
			include("deal_data_banker_lawyer_view.php");
			?>
			</table>
			<!--deal data, bankers lawyers-->
		</td>
		</tr>
		</table>
		<!--deal data-->
	</td>
	</tr>
	
	<tr>
	<td>
	<table width="100%" cellpadding="0" cellspacing="2" class="registercontent">
	<tr>
	<th><?php echo $g_view['deal_partner_data']['name'];?>'s Team</th>
	</tr>
	<tr>
	<td>
	<table width="100%" cellpadding="0" cellspacing="0" class="company">
	<tr>
	<td>Name</td>
	<td>Designation</td>
	<td>Weight</td>
	<td>Credit ($m)</td>
	<td>Adjusted credit for firm ($m)</td>
	<td>Adjusted Credit for the member ($m)</td>
	</tr>
	<?php
	if($g_view['deal_partner_team_data_count'] == 0){
		?>
		<tr>
		<td colspan="6">None found</td>
		</tr>
		<?php
	}else{
		
		for($team_i=0;$team_i<$g_view['deal_partner_team_data_count'];$team_i++){
			?>
			<tr>
			<td><a href="profile.php?mem_id=<?php echo $g_view['deal_partner_team_data'][$team_i]['member_id'];?>"><?php echo $g_view['deal_partner_team_data'][$team_i]['f_name']." ".$g_view['deal_partner_team_data'][$team_i]['l_name'];?></a></td>
			<td><?php echo $g_view['deal_partner_team_data'][$team_i]['designation'];?></td>
			<td><?php echo $g_view['deal_partner_team_data'][$team_i]['deal_share_weight'];?></td>
			<td><?php if($g_view['deal_data']['value_in_billion']==0) echo "Not disclosed"; else echo convert_billion_to_million_for_display($g_view['deal_data']['value_in_billion']);?></td>
			<td><?php echo convert_billion_to_million_for_display($g_view['adjusted_value_for_firm_in_billion']);?></td>
			<td><?php echo convert_billion_to_million_for_display($g_view['deal_partner_team_data'][$team_i]['adjusted_value_in_billion']);?></td>
			</tr>
			<?php
		}
		
	}
	?>
	<tr>
	<td colspan="1">
	<form method="get" action="deal_team_edit.php">
	<input type="hidden" name="deal_id" value="<?php echo $g_view['deal_id'];?>" />
	<input type="hidden" name="partner_id" value="<?php echo $g_view['deal_partner_id'];?>" />
	<input name="submit" type="submit" class="btn_auto" id="button" value="Edit" />
	</form>
	</td>
	<td colspan="5">
	<form method="get" action="deal_detail.php">
	<input type="hidden" name="deal_id" value="<?php echo $g_view['deal_id'];?>" />
	<input name="submit" type="submit" class="btn_auto" id="button" value="Back" />
	</form>
	</td>
	</tr>
	</table>
	</td>
	</tr>
	</table>
	
	</td>
	</tr>
	
	<?php
}else{
	?>
	<tr><td>Deal data not found</td></tr>
	<?php
}
?>
</table>