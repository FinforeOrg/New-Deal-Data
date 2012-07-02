<?php
/*****************************************
sng:1/july/2011

Now we have lots of fields which are deal specific. We no longer use this simple form.

Now we show data based on deal (in a popup) and allow admin to use the existing deal add/edit methods to add a deal. Auto creation
will become too complex
***********************************************/
?>
<script type="text/javascript">
function check(){
	//check if deal company id is set
	if(document.getElementById('deal_company_id').value==0){
		alert("The company is not set. Either create the company or reject this request");
		return false;
	}
	////////////////////////////////////
	//check if the banks are set
	var max_bank_law_firm = 9;
	var validation_passed = true;
	for(i=1;i<=max_bank_law_firm;i++){
		field = "bank"+i+"_id";
		var id = document.getElementById(field).value;
		//remember that this can be blank if the bank is not specified
		if(id!=""){
			if(id=="0"){
				validation_passed = false;
			}
		}
	}
	/////////////////////////////////////
	//check if the law firms are set
	for(i=1;i<=max_bank_law_firm;i++){
		field = "law_firm"+i+"_id";
		var id = document.getElementById(field).value;
		//remember that this can be blank if the law firm is not specified
		if(id!=""){
			if(id=="0"){
				validation_passed = false;
			}
		}
	}
	////////////////////////
	if(!validation_passed){
		alert("One or more bank / law firm not found. Please create those or reject this suggestion");
		return false;
	}
	return true;
}
</script>
<table width="100%" cellpadding="5" cellspacing="0" border="0">
<tr>
<td><span class="msg_txt"><?php echo $g_view['msg'];?></span></td>
</tr>
<tr>
<td>
<form method="post" action="" onsubmit="return check();">
<input type="hidden" name="action" value="accept" />
<input type="hidden" name="id" value="<?php echo $g_view['id'];?>" />
<table width="100%" cellpadding="7" cellspacing="0" border="0">
<tr>
<td colspan="2">Deal data</td>
</tr>
<tr>
<td width="22%">Company :</td>
<td width="78%">
<?php echo $g_view['data']['deal_company_name'];?>
<?php
if($g_view['data']['deal_company_id']==0) echo " [Not found]";
else echo " [Found]";
?>
<input type="hidden" name="deal_company_id" id="deal_company_id" value="<?php echo $g_view['data']['deal_company_id'];?>" />
</td>
</tr>


<tr>
<td>Date :</td>
<td>
<?php echo date("M,Y",strtotime($g_view['data']['date_of_deal']));?>
<input type="hidden" name="date_of_deal" value="<?php echo $g_view['data']['date_of_deal'];?>"  />
</td>
</tr>

<tr>
<td>Value in m$ :</td>
<td>
<?php
/****
sng:7/jul/2010
For some deals, the deal value is not disclosed by the company. In that case, the member just enter 0 in place of deal value
If that is the case, we show 'not disclosed'
********/
if($g_view['data']['value_in_billion']==0){
	echo "not disclosed";
}else{
	echo round($g_view['data']['value_in_billion']*1000,2);
}
?>
<input type="hidden" name="value_in_billion" value="<?php echo $g_view['data']['value_in_billion'];?>"  />
</td>
</tr>

<tr>
<td>Currency :</td>
<td>
<?php echo $g_view['data']['currency'];?>
<input type="hidden" name="currency" value="<?php echo $g_view['data']['currency'];?>"  />
</td>
</tr>

<tr>
<td>Exchange Rate :</td>
<td>
<?php echo $g_view['data']['exchange_rate'];?>
<input type="hidden" name="exchange_rate" value="<?php echo $g_view['data']['exchange_rate'];?>"  />
</td>
</tr>

<tr>
<td>Value in local currency :</td>
<td>
<?php echo round($g_view['data']['value_in_billion_local_currency']*1000,2);?>
<input type="hidden" name="value_in_billion_local_currency" value="<?php echo $g_view['data']['value_in_billion_local_currency'];?>"  />
</td>
</tr>

<tr>
<td>Base Fee :</td>
<td>
<?php echo $g_view['data']['base_fee'];?> %
<input type="hidden" name="base_fee" value="<?php echo $g_view['data']['base_fee'];?>"  />
</td>
</tr>

<tr>
<td>Incentive Fee :</td>
<td>
<?php echo $g_view['data']['incentive_fee'];?> %
<input type="hidden" name="incentive_fee" value="<?php echo $g_view['data']['incentive_fee'];?>"  />
</td>
</tr>

<tr>
<td>Category :</td>
<td>
<?php echo $g_view['data']['deal_cat_name'];?>
<input type="hidden" name="deal_cat_name" value="<?php echo $g_view['data']['deal_cat_name'];?>"  />
</td>
</tr>

<tr>
<td>Sub category 1 :</td>
<td>
<?php echo $g_view['data']['deal_subcat1_name'];?>
<input type="hidden" name="deal_subcat1_name" value="<?php echo $g_view['data']['deal_subcat1_name'];?>" />
</td>
</tr>

<tr>
<td>Sub category 2 :</td>
<td>
<?php echo $g_view['data']['deal_subcat2_name'];?>
<input type="hidden" name="deal_subcat2_name" value="<?php echo $g_view['data']['deal_subcat2_name'];?>"  />
</td>
</tr>

<tr>
<td>Coupon :</td>
<td>
<?php echo $g_view['data']['coupon'];?>
<input type="hidden" name="coupon" value="<?php echo $g_view['data']['coupon'];?>" />
</td>
</tr>

<tr>
<td>Maturity date :</td>
<td>
<?php echo $g_view['data']['maturity_date'];?>
<input type="hidden" name="maturity_date" value="<?php echo $g_view['data']['maturity_date'];?>" />
</td>
</tr>

<tr>
<td>Current Rating :</td>
<td>
<?php echo $g_view['data']['current_rating'];?>
<input type="hidden" name="current_rating" value="<?php echo $g_view['data']['current_rating'];?>" />
</td>
</tr>

<tr>
<td>1 day price change :</td>
<td>
<?php echo $g_view['data']['1_day_price_change'];?> %
<input type="hidden" name="1_day_price_change" value="<?php echo $g_view['data']['1_day_price_change'];?>" />
</td>
</tr>

<tr>
<td>Discount to last :</td>
<td>
<?php echo $g_view['data']['discount_to_last'];?> %
<input type="hidden" name="discount_to_last" value="<?php echo $g_view['data']['discount_to_last'];?>" />
</td>
</tr>

<tr>
<td>Discount to TERP :</td>
<td>
<?php echo $g_view['data']['discount_to_terp'];?> %
<input type="hidden" name="discount_to_terp" value="<?php echo $g_view['data']['discount_to_terp'];?>" />
</td>
</tr>

<tr>
<td>Target company :</td>
<td>
<?php echo $g_view['data']['target_company_name'];?>
<input type="hidden" name="target_company_name" value="<?php echo $g_view['data']['target_company_name'];?>" />
</td>
</tr>

<tr>
<td>Target country :</td>
<td>
<?php echo $g_view['data']['target_country'];?>
<input type="hidden" name="target_country" value="<?php echo $g_view['data']['target_country'];?>" />
</td>
</tr>

<tr>
<td>Target sector :</td>
<td>
<?php echo $g_view['data']['target_sector'];?>
<input type="hidden" name="target_sector" value="<?php echo $g_view['data']['target_sector'];?>" />
</td>
</tr>

<tr>
<td>Seller company :</td>
<td>
<?php echo $g_view['data']['seller_company_name'];?>
<input type="hidden" name="seller_company_name" value="<?php echo $g_view['data']['seller_company_name'];?>" />
</td>
</tr>

<tr>
<td>Seller country :</td>
<td>
<?php echo $g_view['data']['seller_country'];?>
<input type="hidden" name="seller_country" value="<?php echo $g_view['data']['seller_country'];?>" />
</td>
</tr>

<tr>
<td>Seller sector :</td>
<td>
<?php echo $g_view['data']['seller_sector'];?>
<input type="hidden" name="seller_sector" value="<?php echo $g_view['data']['seller_sector'];?>" />
</td>
</tr>

<tr>
<td>EV/EBITDA LTM :</td>
<td>
<?php echo $g_view['data']['ev_ebitda_ltm'];?>
<input type="hidden" name="ev_ebitda_ltm" value="<?php echo $g_view['data']['ev_ebitda_ltm'];?>" />
</td>
</tr>

<tr>
<td>EV/EBITDA +1yr :</td>
<td>
<?php echo $g_view['data']['ev_ebitda_1yr'];?>
<input type="hidden" name="ev_ebitda_1yr" value="<?php echo $g_view['data']['ev_ebitda_1yr'];?>" />
</td>
</tr>

<tr>
<td>Premia (30 days) :</td>
<td>
<?php echo $g_view['data']['30_days_premia'];?> %
<input type="hidden" name="30_days_premia" value="<?php echo $g_view['data']['30_days_premia'];?>" />
</td>
</tr>

<tr>
<td>Note :</td>
<td>
<?php echo nl2br($g_view['data']['deal_note']);?>
<input type="hidden" name="deal_note" value="<?php echo $g_view['data']['deal_note'];?>" />
</td>
</tr>

<tr>
<td>Sources :</td>
<td>
<?php echo nl2br($g_view['data']['deal_sources']);?>
<input type="hidden" name="deal_sources" value="<?php echo $g_view['data']['deal_sources'];?>" />
</td>
</tr>

<tr>
<td>Banks</td>
<td>law firms</td>
</tr>

<tr>
<td width="50%">
<!--list of banks-->
<table width="100%" cellpadding="10" cellspacing="0" border="1" style="border-collapse:collapse;">
<?php
$max_bank_law_firm = 9;
for($i=1;$i<=$max_bank_law_firm;$i++){
	$field = "bank".$i;
	$key = $field."_id";
	?>
	<tr>
	<td><?php echo $i;?></td>
	<td><?php echo $g_view['data'][$field];?>
	<?php
	if($g_view['data'][$field]!=""){
		?>
		[
		<?php
		if($g_view['data'][$key]==0) echo "Not Found";
		else echo "Found";
		?>
		]
		<input type="hidden" name="<?php echo $key;?>"  id="<?php echo $key;?>" value="<?php echo $g_view['data'][$key];?>" />
		<?php
	}else{
		//if the bank is not specified then there is no question of getting or not getting the id.
		//in that case, just set the id to blank
		?>
		<input type="hidden" name="<?php echo $key;?>" id="<?php echo $key;?>" value="" />
		<?php
	}
	?>
	</td>
	</tr>
	<?php
}
?>
</table>
<!--list of banks-->
</td>
<td width="50%">
<!--list of law firms-->
<table width="100%" cellpadding="10" cellspacing="0" border="1" style="border-collapse:collapse;">
<?php
$max_bank_law_firm = 9;
for($i=1;$i<=$max_bank_law_firm;$i++){
	$field = "law_firm".$i;
	$key = $field."_id";
	?>
	<tr>
	<td><?php echo $i;?></td>
	<td><?php echo $g_view['data'][$field];?>
	<?php
	if($g_view['data'][$field]!=""){
		?>
		[
		<?php
		if($g_view['data'][$key]==0) echo "Not Found";
		else echo "Found";
		?>
		]
		<input type="hidden" name="<?php echo $key;?>" id="<?php echo $key;?>" value="<?php echo $g_view['data'][$key];?>" />
		<?php
	}else{
		//if the law firm is not specified then there is no question of getting or not getting the id.
		//in that case, just set the id to blank
		?>
		<input type="hidden" name="<?php echo $key;?>" id="<?php echo $key;?>" value="" />
		<?php
	}
	?>
	</td>
	</tr>
	<?php
}
?>
</table>
<!--list of law firms-->
</td>
</tr>

<tr>
<td colspan="2"><strong>Suggested by</strong><br />
<?php echo $g_view['data']['f_name'];?> <?php echo $g_view['data']['l_name'];?>, <?php echo $g_view['data']['designation'];?>, <?php echo $g_view['data']['member_company_name'];?>
</td>
</tr>

<tr>
<td></td>
<td><input type="submit" name="submit" value="Accept" /></td>
</tr>
</table>
</form>
</td>
</tr>

<tr>
<td>
<form method="post" action="deal_suggestion_list.php?start=<?php echo $g_view['from'];?>">
<input type="hidden" name="action" value="reject" />
<input type="hidden" name="id" value="<?php echo $g_view['id'];?>" />
<table width="100%">
<tr>
<td colspan="2">If this data is questionable, you can reject the suggested transaction.</td>
</tr>

<tr>
<td>&nbsp;</td>
<td><input type="submit" name="submit" value="Reject" />
</tr>
</table>
</form>
</td>
</tr>
</table>
