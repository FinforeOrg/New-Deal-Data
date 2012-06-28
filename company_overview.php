<table width="100%" cellpadding="0" cellspacing="0">
<tr>
<td>
<!--/////////////////////////////details///////////////////////////////////-->
<div id="company-detail">
<p>
Here you can see the details of the company and can send your suggestions / corrections if required.
</p>
<div style="height:20px;"></div>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
<td colspan="3"><h2>Detail</h2></td>
</tr>
<tr>
<td colspan="4" class="vseparation"></td>
</tr>

<tr>
<td colspan="2"><strong>Company Details:</strong></td>
</tr>

<tr>
<td class="left-label">Name (Short):</td>
<td class="middle-data"><?php echo $g_view['company_data']['name'];?></td>
</tr>

<tr>
<td colspan="2" class="vseparation"></td>

</tr>

<tr>
<td class="left-label">Country of HQ:</td>
<td class="middle-data"><?php echo $g_view['company_data']['hq_country'];?></td>

</tr>

<tr>
<td colspan="2" class="vseparation"></td>

</tr>

<tr>
<td class="left-label">Sector:</td>
<td class="middle-data"><?php echo $g_view['company_data']['sector'];?></td>

</tr>

<tr>
<td colspan="2" class="vseparation"></td>

</tr>

<tr>
<td class="left-label">Industry:</td>
<td class="middle-data"><?php echo $g_view['company_data']['industry'];?></td>

</tr>

<tr>
<td colspan="4" class="vseparation"></td>
</tr>

<tr>
<td colspan="2"><strong>Company Identifiers:</strong></td>
</tr>

<?php
for($j=0;$j<$g_view['identifiers_cnt'];$j++){
	$field_name = "identifier_id_".$g_view['identifiers'][$j]['identifier_id'];
	?>
	<tr>
	<td class="left-label"><?php echo $g_view['identifiers'][$j]['name'];?>:</td>
	<td class="middle-data"><?php if($g_view['identifiers'][$j]['value']==NULL) echo "n/a"; else echo $g_view['identifiers'][$j]['value'];?></td>
	
	</tr>
	<?php
	if($j < ($g_view['identifiers_cnt']-1)){
		?>
		<tr>
		<td colspan="2" class="vseparation"></td>
		
		</tr>
		<?php
	}
}
?>




</table>



</div>
<!--/////////////////////////////details///////////////////////////////////-->
</td>
<td style="width:10px;border-left:1px solid #000000;"></td>
<td style="width:200px;">
<!--//////////////////////////////logo/////////////////////////////////-->
<?php
if($g_view['company_data']['logo']!=""){
	?><img src="<?php echo LOGO_IMG_URL;?>/<?php echo $g_view['company_data']['logo'];?>" border="0" /><?php
}else{
	?>
	Logo not found
	<?php
}
?>
<!--//////////////////////////////logo/////////////////////////////////-->
</td>
</tr>
</table>