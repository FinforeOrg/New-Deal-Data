<table width="100%" cellpadding="0" cellspacing="0">
<tr>
<td>
<!--/////////////////////////////details///////////////////////////////////-->
<div id="company-detail">
<p>
Here you can see the details of the firm.
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
<td colspan="2"><strong><?php if($g_view['data']['type']=="bank"){?>Bank<?php }else{?>Law Firm<?php }?> Details:</strong></td>
</tr>

<tr>
<td class="left-label">Name (Short):</td>
<td class="middle-data"><?php echo $g_view['data']['name'];?></td>

</tr>

<tr>
<td colspan="4" class="vseparation"></td>

</tr>

<?php
/*******************
sng:8/may/2012
For now, let us not bother with abbreviated name. The 2 or 3 letter abbreviation
is used as legend for league table chart and we do not use any chart here
*******************/
?>



</table>



</div>
<!--/////////////////////////////details///////////////////////////////////-->
</td>
<td style="width:10px;border-left:1px solid #000000;"></td>
<td style="width:200px;">
<!--//////////////////////////////logo/////////////////////////////////-->
<?php
if($g_view['data']['logo']!=""){
	?><img src="<?php echo LOGO_IMG_URL;?>/<?php echo $g_view['data']['logo'];?>" border="0" /><?php
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