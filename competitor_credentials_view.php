<?php
/****************************
sng:27/oct/2011
We now use auto-complete to get the firm name and directly go to the cred page
*****************************/

//if($_SESSION['member_type']=="banker"){
//	$company_type = "bank";
//}else{
//	$company_type = "law firm";
//}
?>
<script src="admin/js/jquery.devbridge.autocomplete.js"></script>
<link type="text/css" rel="stylesheet" href="admin/css/autocomplete.css" />
<script>
$(function(){
	$('#firm_name').autocomplete({
		serviceUrl:'ajax/fetch_firm_list.php',
		minChars:2,
		noCache: true,
		onSelect: function(value, data){
			$('#firm_name').val(value);
			g_firm_id = data;
		}
	})
});

var g_firm_id = 0;

function goto_cred(){
	window.location.replace("showcase_firm.php?id="+g_firm_id+"&from=savedSearches");
}
</script>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
<td>&nbsp;</td>
<td style="text-align:center; width:550px">
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="registerinner">
		<tr>
			<td>
				<table width="100%" border="0" cellpadding="0" cellspacing="0" class="registercontent">
				<tr>
				<th>Search for a Competitor</th>
				</tr>
				<tr>
				<td style="padding:2px 20px 20px 20px;">
				<?php
				//if(($_SESSION['member_type']=="banker")||($_SESSION['member_type']=="lawyer")){
					?>
					
					<table cellpadding="0" cellspacing="0">
					<tr>
					<td>
					<p>
					To see the credentials of a competitor, please type the name of the firm. As you type, a list of matching firms will appear. Select one and click the button to see the credentials of that firm.
					</p>
					</td>
					</tr>
					<tr>
					<td>
					
					<input type="text" style="width:300px;" id="firm_name" autocomplete="off" />&nbsp;&nbsp;<input type="button" class="btn_auto" value="View Credentials" onclick="goto_cred();" />
					</td>
					</tr>
					<tr><td style="height:10px;">&nbsp;</td></tr>
					</table>
					
				<?php
				//}else{
					?>
					<!--<p>Only bankers and lawyers can search for competitor's credentials</p>-->
					<?php
				//}
				?>
				</td>
				</tr>
				</table>
			</td>
		</tr>
	</table>

</td>
<td>&nbsp;</td>
</tr>
</table>

<div style="height:20px;"></div>
<h2>Frequent Searches</h2>

<table width="100%" cellpadding="0" cellspacing="5" class="registercontent">

<tr>
<th style="width:50%">Top Banks</th>
<th>Top Law Firms</th>
</tr>
<tr>
<td>
<?php
if($g_view['bank_data_count']==0){
	?>
	None found
	<?php
}else{
	?>
	<table width="100%" cellpadding="0" cellspacing="10">
	<tr>
	<?php
	$col_cnt = 0;
	for($i=0;$i<$g_view['bank_data_count'];$i++){
		?>
		<td>
		<a href="showcase_firm.php?id=<?php echo $g_view['bank_data'][$i]['company_id'];?>&from=savedSearch">
		<?php
		if($g_view['bank_data'][$i]['logo']!=""){
			?>
			<img src="<?php echo LOGO_IMG_URL;?>/<?php echo $g_view['bank_data'][$i]['logo'];?>" style="width:150px" />
			<?php
		}else{
			echo $g_view['bank_data'][$i]['name'];
		}
		?>
		</a>
		</td>
		<?php
		$col_cnt++;
		if($col_cnt == 2){
			$col_cnt = 0;
			?>
			</tr><tr>
			<?php
		}
	}
	?>
	</tr>
	</table>
	<?php
}
?>
</td>
<td>
<?php
if($g_view['lawfirm_data_count']==0){
	?>
	None found
	<?php
}else{
	?>
	<table width="100%" cellpadding="0" cellspacing="10">
	<tr>
	<?php
	$col_cnt = 0;
	for($i=0;$i<$g_view['lawfirm_data_count'];$i++){
		?>
		<td>
		<a href="showcase_firm.php?id=<?php echo $g_view['lawfirm_data'][$i]['company_id'];?>&from=savedSearch">
		<?php
		if($g_view['lawfirm_data'][$i]['logo']!=""){
			?>
			<img src="<?php echo LOGO_IMG_URL;?>/<?php echo $g_view['lawfirm_data'][$i]['logo'];?>" style="width:150px" />
			<?php
		}else{
			echo $g_view['lawfirm_data'][$i]['name'];
		}
		?>
		</a>
		</td>
		<?php
		$col_cnt++;
		if($col_cnt == 2){
			$col_cnt = 0;
			?>
			</tr><tr>
			<?php
		}
	}
	?>
	</tr>
	</table>
	<?php
}
?>
</td>
</tr>
</table>