<script>
function parent_sector_changed(){
	var sector_obj = document.getElementById('parent_company_sector');
	var offset_selected = sector_obj.selectedIndex;
	if(offset_selected != 0){
		var sector_selected = sector_obj.options[offset_selected].value;
		//fetch the list of industries
		$.post("admin/ajax/industry_list_for_sector.php", {sector: ""+sector_selected+""}, function(data){
				if(data.length >0) {
					$('#parent_company_industry').html(data);
				}
		});
	}
}

function subsidiary_sector_changed(){
	var sector_obj = document.getElementById('buyer_subsidiary_sector');
	var offset_selected = sector_obj.selectedIndex;
	if(offset_selected != 0){
		var sector_selected = sector_obj.options[offset_selected].value;
		//fetch the list of industries
		$.post("admin/ajax/industry_list_for_sector.php", {sector: ""+sector_selected+""}, function(data){
				if(data.length >0) {
					$('#buyer_subsidiary_industry').html(data);
				}
		});
	}
}
</script>
<tr>
<td colspan="2"><strong>Deal Information</strong></td>
</tr>
<tr>
<td colspan="4" class="vseparation"></td>
</tr>

<?php
/**************
remember that not all deals will have extra data, and even then, the date field may contain the default 0 value

This is Bond deal. If BOTH date_announced AND date_closed are not given BUT date_of_deal is there then:
Completed: is date_of_deal
******************/
$date_announced = "";
$date_completed = "";

if(($g_view['deal_data']['date_announced']=="0000-00-00"||$g_view['deal_data']['date_announced']=="")&&($g_view['deal_data']['date_closed']=="0000-00-00"||$g_view['deal_data']['date_closed']=="")&&$g_view['deal_data']['date_of_deal']!="0000-00-00"){
	$date_completed = ymd_to_dmy($g_view['deal_data']['date_of_deal']);
}
?>
<tr>
<td class="left-label">Announced / Filed:</td>
<td class="middle-data">
<?php 
	if($date_announced != ""){
		echo $date_announced;
	}elseif($g_view['deal_data']['date_announced']=="0000-00-00"||$g_view['deal_data']['date_announced']==""){
		echo "n/a";
	}else{
		echo ymd_to_dmy($g_view['deal_data']['date_announced']);
	}
?>
</td>
<td class="separation"></td>
<td class="right-input"><input type="text" name="date_announced" id="date_announced" class="shorttxtinput" />
<script type="text/javascript">
          // <![CDATA[       
            var opts = {                            
                    formElements:{"date_announced":"Y-ds-m-ds-d"},
                    showWeeks:false                   
            };      
            datePickerController.createDatePicker(opts);
          // ]]>
          </script>
</td>
</tr>

<tr>
<td colspan="2" class="vseparation"></td>
<td class="separation"></td>
<td></td>
</tr>

<tr>
<td class="left-label">Closed / Trading:</td>
<td class="middle-data">
<?php
	if($date_completed!=""){
		echo $date_completed;
	}elseif($g_view['deal_data']['date_closed']=="0000-00-00"||$g_view['deal_data']['date_closed']==""){
		echo "n/a";
	}else{
		echo ymd_to_dmy($g_view['deal_data']['date_closed']);
	}?>
</td>
<td class="separation"></td>
<td class="right-input"><input type="text" name="date_closed" id="date_closed" class="shorttxtinput" />
<script type="text/javascript">
          // <![CDATA[       
            var opts = {                            
                    formElements:{"date_closed":"Y-ds-m-ds-d"},
                    showWeeks:false                   
            };      
            datePickerController.createDatePicker(opts);
          // ]]>
          </script>
</td>
</tr>

<tr>
<td colspan="2" class="blockseparation"></td>
<td class="separation"></td>
<td></td>
</tr>

<tr>
<td class="left-label">Name of Parent Company:</td>
<td class="middle-data"><?php echo $g_view['deal_data']['company_name'];?></td>
<td class="separation"></td>
<td class="right-input"><input type="text" name="parent_company_name" class="txtinput" /></td>
</tr>

<tr>
<td colspan="2" class="vseparation"></td>
<td class="separation"></td>
<td></td>
</tr>

<tr>
<td class="left-label">Sector of parent Company:</td>
<td class="middle-data"><?php echo $g_view['deal_data']['sector'];?></td>
<td class="separation"></td>
<td class="right-input">
<select name="parent_company_sector" id="parent_company_sector" onChange="return parent_sector_changed();">
<option value="">Select</option>
<?php
for($i=0;$i<$g_view['sector_count'];$i++){
	?>
	<option value="<?php echo $g_view['sector_list'][$i]['sector'];?>"><?php echo $g_view['sector_list'][$i]['sector'];?></option>
	<?php
}
?>
</select>
</td>

</tr>

<tr>
<td colspan="2" class="vseparation"></td>
<td class="separation"></td>
<td></td>
</tr>

<tr>
<td class="left-label">Industry of parent Company:</td>
<td class="middle-data"><?php echo $g_view['deal_data']['industry'];?></td>
<td class="separation"></td>
<td class="right-input">
<select name="parent_company_industry" id="parent_company_industry">
<option value=""> Select industry </option>                                             
</select>
</td>
</tr>

<tr>
<td colspan="2" class="vseparation"></td>
<td class="separation"></td>
<td></td>
</tr>

<tr>
<td class="left-label">Country of Headquarters:</td>
<td class="middle-data"><?php echo $g_view['deal_data']['hq_country'];?></td>
<td class="separation"></td>
<td class="right-input">
<select name="country_of_headquarters_buyer">
<option value="">Select</option>
<?php
for($i=0;$i<$g_view['country_count'];$i++){
	?>
	<option value="<?php echo $g_view['country_list'][$i]['name'];?>" ><?php echo $g_view['country_list'][$i]['name'];?></option>
	<?php
}
?>
</select>
</td>
</tr>

<tr>
<td colspan="2" class="blockseparation"></td>
<td class="separation"></td>
<td></td>
</tr>

<tr>
<td class="left-label">Name of subsidiary:</td>
<td class="middle-data"><?php echo $g_view['deal_data']['buyer_subsidiary_name'];?></td>
<td class="separation"></td>
<td class="right-input"><input type="text" name="buyer_subsidiary_name" class="txtinput" /></td>
</tr>

<tr>
<td colspan="2" class="vseparation"></td>
<td class="separation"></td>
<td></td>
</tr>

<tr>
<td class="left-label">Sector of Subsidiary:</td>
<td class="middle-data"><?php echo $g_view['deal_data']['buyer_subsidiary_sector'];?></td>
<td class="separation"></td>
<td class="right-input">
<select name="buyer_subsidiary_sector" id="buyer_subsidiary_sector" onChange="return subsidiary_sector_changed();">
<option value="">Select</option>
<?php
for($i=0;$i<$g_view['sector_count'];$i++){
	?>
	<option value="<?php echo $g_view['sector_list'][$i]['sector'];?>"><?php echo $g_view['sector_list'][$i]['sector'];?></option>
	<?php
}
?>
</select>
</td>
</tr>

<tr>
<td colspan="2" class="vseparation"></td>
<td class="separation"></td>
<td></td>
</tr>

<tr>
<td class="left-label">Industry of Subsidiary:</td>
<td class="middle-data"><?php echo $g_view['deal_data']['buyer_subsidiary_industry'];?></td>
<td class="separation"></td>
<td class="right-input">
<select name="buyer_subsidiary_industry" id="buyer_subsidiary_industry">
<option value=""> Select industry </option>                                             
</select>
</td>
</tr>

<tr>
<td colspan="2" class="vseparation"></td>
<td class="separation"></td>
<td></td>
</tr>

<tr>
<td class="left-label">Country of Headquarters:</td>
<td class="middle-data"><?php echo $g_view['deal_data']['buyer_subsidiary_country'];?></td>
<td class="separation"></td>
<td class="right-input">
<select name="buyer_subsidiary_country">
<option value="">Select</option>
<?php
for($i=0;$i<$g_view['country_count'];$i++){
	?>
	<option value="<?php echo $g_view['country_list'][$i]['name'];?>" ><?php echo $g_view['country_list'][$i]['name'];?></option>
	<?php
}
?>
</select>
</td>
</tr>

<tr>
<td colspan="4" class="blockseparation"></td>
</tr>

<tr>
<td class="left-label">Sources:</td>
<td colspan="3">
<?php
/***
sng:8/jul/2010
If sources are present, we show the sources section.
sources are just urls separated by comma, so, we split and show in a list as hyperlinked item.
The page will open in new window
*********/
if($g_view['deal_data']['sources']!=""){
	$source_urls = explode(",",$g_view['deal_data']['sources']);
	?>
	<ol>
	<?php
	foreach($source_urls as $source){
		$source = trim($source);
		?>
		<li><a href="<?php echo $source;?>" target="_blank"><?php echo $source;?></a></li>
		<?php
	}
	?>
	</ol>
	<?php
}else{
	?>
	None available
	<?php
}
?>
</td>
</tr>
<tr>
<td colspan="4" class="vseparation"></td>
</tr>

<tr>
<td class="left-label">Additional sources:</td>
<td colspan="3">
<input type="text" name="regulatory_links1" class="longtxtinput" />
</td>
</tr>
<?php
for($i=2;$i<=4;$i++){
?>
<tr>
<td colspan="4" class="vseparation"></td>
</tr>
<tr>
<td class="left-label">&nbsp;</td>
<td colspan="3">
<input type="text" name="regulatory_links<?php echo $i;?>" class="longtxtinput" />
</td>
</tr>
<?php
}
?>


<tr>
<td colspan="4" class="blockseparation"></td>
</tr>
<tr>
<td colspan="2"><strong>Deal Valuation</strong></td>
</tr>
<tr>
<td colspan="4" class="vseparation"></td>
</tr>

<?php
/*************
If the deal is in USD, the local currency will be blank. In that case, we assume local currency as USD.

sng:30/jun/2011
The input class is made longer because the short input is producing small suggestion area with scrollbar
**************/
if($g_view['deal_data']['currency']==""){
	$deal_local_currency = "USD";
}else{
	$deal_local_currency = $g_view['deal_data']['currency'];
}
?>
<tr>
<td class="left-label">Local Currency (for the deal):</td>
<td class="middle-data"><?php echo $deal_local_currency;?></td>
<td class="separation"></td>
<td class="right-input"><input type="text" name="currency" id="currency" class="txtinput" /></td>
</tr>
<tr>
<td colspan="2" class="vseparation"></td>
<td class="separation"></td>
<td></td>
</tr>
<?php
/**********
if the deal is in USD, local currency is USD, so USD to USD exchange rate is 1
*****************/
?>
<tr>
<td class="left-label">Local Currency per 1 USD:</td>
<td class="middle-data"><?php if($deal_local_currency=="USD") echo 1; else echo $g_view['deal_data']['exchange_rate'];?></td>
<td class="separation"></td>
<td class="right-input"><input type="text" name="exchange_rate" class="shorttxtinput" /></td>
</tr>
<tr>
<td colspan="2" class="vseparation"></td>
<td class="separation"></td>
<td></td>
</tr>

<tr>
<td class="left-label">Facility Size:</td>
<td class="middle-data"><?php if($g_view['deal_data']['value_in_billion_local_currency']==""||$g_view['deal_data']['value_in_billion_local_currency']==0.0) echo "n/a"; else echo convert_billion_to_million_for_display_round($g_view['deal_data']['value_in_billion_local_currency'])."m ".$deal_local_currency;?></td>
<td class="separation"></td>
<td class="right-input"><input type="text" name="value_in_million_local_currency" class="shorttxtinput" /> (in million, local currency)</td>
</tr>
<tr>
<td colspan="2" class="vseparation"></td>
<td class="separation"></td>
<td></td>
</tr>

<tr>
<td class="left-label">Facility Size in USD:</td>
<td class="middle-data"><?php echo convert_deal_value_for_display_round($g_view['deal_data']['value_in_billion'],$g_view['deal_data']['value_range_id'],$g_view['deal_data']['fuzzy_value']);?></td>
<td class="separation"></td>
<td class="right-input"><?php include("deal_page_detail_snippet_value_range.php");?></td>
</tr>

<tr>
<td colspan="4" class="blockseparation"></td>
</tr>
<tr>
<td colspan="2"><strong>Additional Information</strong></td>
</tr>
<tr>
<td colspan="4" class="vseparation"></td>
</tr>

<tr>
<td class="left-label">Tenor:</td>
<td class="middle-data"><?php if($g_view['deal_data']['years_to_maturity']=="") echo "n/a"; else echo $g_view['deal_data']['years_to_maturity'];?></td>
<td class="separation"></td>
<td class="right-input"><input type="text" name="years_to_maturity" class="txtinput" /></td>
</tr>
<tr>
<td colspan="2" class="vseparation"></td>
<td class="separation"></td>
<td></td>
</tr>

<tr>
<td class="left-label">End Date:</td>
<td class="middle-data">
<?php
/************************
sng:18/aug/2011
All the conditions has to be satisfied, and do check for n/a
*********************/
?>
<?php if($g_view['deal_data']['maturity_date']=="0000-00-00"||$g_view['deal_data']['maturity_date']==""||$g_view['deal_data']['maturity_date']=="n/a") echo "n/a"; else echo ymd_to_dmy($g_view['deal_data']['maturity_date']);?></td>
<td class="separation"></td>
<td class="right-input"><input type="text" name="maturity_date" id="maturity_date" class="shorttxtinput" />
<script type="text/javascript">
          // <![CDATA[       
            var opts = {                            
                    formElements:{"maturity_date":"Y-ds-m-ds-d"},
                    showWeeks:false                   
            };      
            datePickerController.createDatePicker(opts);
          // ]]>
          </script></td>
</tr>
<tr>
<td colspan="2" class="vseparation"></td>
<td class="separation"></td>
<td></td>
</tr>

<tr>
<td class="left-label">Margin:</td>
<td class="middle-data"><?php if($g_view['deal_data']['coupon']=="") echo "n/a"; else echo $g_view['deal_data']['coupon'];?></td>
<td class="separation"></td>
<td class="right-input"><input type="text" name="coupon" class="txtinput" /></td>
</tr>
<tr>
<td colspan="2" class="vseparation"></td>
<td class="separation"></td>
<td></td>
</tr>

<tr>
<td class="left-label">Margin (including ratchet):</td>
<td class="middle-data"><?php if($g_view['deal_data']['margin_including_ratchet']=="") echo "n/a"; else echo $g_view['deal_data']['margin_including_ratchet'];?></td>
<td class="separation"></td>
<td class="right-input"><input type="text" name="margin_including_ratchet" class="txtinput" /></td>
</tr>
<tr>
<td colspan="2" class="vseparation"></td>
<td class="separation"></td>
<td></td>
</tr>

<tr>
<td class="left-label">Collateral:</td>
<td class="middle-data"><?php if($g_view['deal_data']['collateral']=="") echo "n/a"; else echo $g_view['deal_data']['collateral'];?></td>
<td class="separation"></td>
<td class="right-input"><input type="text" name="collateral" class="txtinput" /></td>
</tr>
<tr>
<td colspan="2" class="vseparation"></td>
<td class="separation"></td>
<td></td>
</tr>

<tr>
<td class="left-label">Seniority:</td>
<td class="middle-data"><?php if($g_view['deal_data']['seniority']=="") echo "n/a"; else echo $g_view['deal_data']['seniority'];?></td>
<td class="separation"></td>
<td class="right-input"><input type="text" name="seniority" class="txtinput" /></td>
</tr>
<tr>
<td colspan="2" class="vseparation"></td>
<td class="separation"></td>
<td></td>
</tr>

<tr>
<td class="left-label">Fee upfront:</td>
<td class="middle-data"><?php if($g_view['deal_data']['fee_upfront']==""||$g_view['deal_data']['fee_upfront']==0.0) echo "n/a"; else echo $g_view['deal_data']['fee_upfront']."%";?></td>
<td class="separation"></td>
<td class="right-input"><input type="text" name="fee_upfront" class="shorttxtinput" /> %</td>
</tr>
<tr>
<td colspan="2" class="vseparation"></td>
<td class="separation"></td>
<td></td>
</tr>

<tr>
<td class="left-label">Fee commitment:</td>
<td class="middle-data"><?php if($g_view['deal_data']['fee_commitment']==""||$g_view['deal_data']['fee_commitment']==0.0) echo "n/a"; else echo $g_view['deal_data']['fee_commitment']."%";?></td>
<td class="separation"></td>
<td class="right-input"><input type="text" name="fee_commitment" class="shorttxtinput" /> %</td>
</tr>
<tr>
<td colspan="2" class="vseparation"></td>
<td class="separation"></td>
<td></td>
</tr>

<tr>
<td class="left-label">Fee utilisation:</td>
<td class="middle-data"><?php if($g_view['deal_data']['fee_utilisation']==""||$g_view['deal_data']['fee_utilisation']==0.0) echo "n/a"; else echo $g_view['deal_data']['fee_utilisation']."%";?></td>
<td class="separation"></td>
<td class="right-input"><input type="text" name="fee_utilisation" class="shorttxtinput" /> %</td>
</tr>
<tr>
<td colspan="2" class="vseparation"></td>
<td class="separation"></td>
<td></td>
</tr>

<tr>
<td class="left-label">Fee arrangement:</td>
<td class="middle-data"><?php if($g_view['deal_data']['fee_arrangement']==""||$g_view['deal_data']['fee_arrangement']==0.0) echo "n/a"; else echo $g_view['deal_data']['fee_arrangement']."%";?></td>
<td class="separation"></td>
<td class="right-input"><input type="text" name="fee_arrangement" class="shorttxtinput" /> %</td>
</tr>
<tr>
<td colspan="4" class="vseparation"></td>
</tr>

<tr>
<td class="left-label">Enter additional details here (e.g. terms, conditions):</td>
<td colspan="3">
<textarea name="additional_deal_details_note" class="txtareainput"></textarea></td>
</tr>

<tr>
<td colspan="4" class="blockseparation"></td>
</tr>

<tr>
<td class="left-label">Additional comments on list of banks and law firms:</td>
<td colspan="3">
<textarea name="additional_partners" class="txtareainput"></textarea></td>
</tr>

<script>
jQuery('#currency').autocomplete({
	serviceUrl:'admin/ajax/fetch_currency_list.php',
	minChars:1,
	noCache: true,
	onSelect: function(value, data){
		jQuery('#currency').val(data);
	}
});

</script>