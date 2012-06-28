<?php
/****************
for M&A deals, we need the subcategories so that we can show whether the deal is Complete or Pending
***/
$g_view['subcat1_list'] = array();
$g_view['subcat1_count'] = 0;
$success = $g_trans->get_all_category_subtype1_for_category_type($g_view['deal_data']['deal_cat_name'],$g_view['subcat1_list'],$g_view['subcat1_count']);
if(!$success){
	die("Cannot get subcategory1 list");
}
/**********************
we need list of M&A merger type
********/
$g_view['merger_list'] = array();
$g_view['merger_list_count'] = 0;
$success = $deal_support->ma_merger_types($g_view['merger_list'],$g_view['merger_list_count']);
if(!$success){
	die("Cannot get merger type list");
}
?>
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
function target_sector_changed(){
	var sector_obj = document.getElementById('target_sector');
	var offset_selected = sector_obj.selectedIndex;
	if(offset_selected != 0){
		var sector_selected = sector_obj.options[offset_selected].value;
		//fetch the list of industries
		$.post("admin/ajax/industry_list_for_sector.php", {sector: ""+sector_selected+""}, function(data){
				if(data.length >0) {
					$('#target_industry').html(data);
				}
		});
	}
}
function seller_sector_changed(){
	var sector_obj = document.getElementById('seller_sector');
	var offset_selected = sector_obj.selectedIndex;
	if(offset_selected != 0){
		var sector_selected = sector_obj.options[offset_selected].value;
		//fetch the list of industries
		$.post("admin/ajax/industry_list_for_sector.php", {sector: ""+sector_selected+""}, function(data){
				if(data.length >0) {
					$('#seller_industry_name').html(data);
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

<tr>
<td class="left-label">Marked as:</td>
<td class="middle-data"><?php echo $g_view['deal_data']['deal_subcat1_name'];?></td>
<td class="separation"></td>
<td class="right-input">
<select name="deal_subcat1_name" id="deal_subcat1_name">
<option value="">Select</option>
<?php
for($i=0;$i<$g_view['subcat1_count'];$i++){
    ?>
    <option value="<?php echo $g_view['subcat1_list'][$i]['subtype1'];?>"><?php echo $g_view['subcat1_list'][$i]['subtype1'];?></option>
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

<?php
/**************
remember that not all deals will have extra data, and even then, the date field may contain the default 0 value

This is M&A deal. If BOTH date_announced AND date_closed are not given BUT date_of_deal is there then:
if Completed, Completed: is date_of_deal
If Pending, Announced is date_of_deal
******************/
$date_announced = "";
$date_completed = "";

if(($g_view['deal_data']['date_announced']=="0000-00-00"||$g_view['deal_data']['date_announced']=="")&&($g_view['deal_data']['date_closed']=="0000-00-00"||$g_view['deal_data']['date_closed']=="")&&$g_view['deal_data']['date_of_deal']!="0000-00-00"){
	if(strtolower($g_view['deal_data']['deal_subcat1_name'])=="pending"){
		$date_announced = ymd_to_dmy($g_view['deal_data']['date_of_deal']);
	}
	if(strtolower($g_view['deal_data']['deal_subcat1_name'])=="completed"){
		$date_completed = ymd_to_dmy($g_view['deal_data']['date_of_deal']);
	}
}
?>
<tr>
<td class="left-label">Rumoured:</td>
<td class="middle-data">
<?php if($g_view['deal_data']['date_rumour']=="0000-00-00"||$g_view['deal_data']['date_rumour']=="") echo "n/a"; else echo ymd_to_dmy($g_view['deal_data']['date_rumour']);?>
</td>
<td class="separation"></td>
<td class="right-input"><input type="text" name="date_rumour" id="date_rumour" class="shorttxtinput" />
<script type="text/javascript">
          // <![CDATA[       
            var opts = {                            
                    formElements:{"date_rumour":"Y-ds-m-ds-d"},
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
<td class="left-label">Announced:</td>
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
<td class="left-label">Closed:</td>
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
<td class="left-label">Name of Parent Company (BUYER):</td>
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
<td class="left-label">Name of subsidiary (BUYER), if relevant :</td>
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
<td colspan="2" class="blockseparation"></td>
<td class="separation"></td>
<td></td>
</tr>

<tr>
<td class="left-label">Name of TARGET Company / Asset:</td>
<td class="middle-data"><?php echo $g_view['deal_data']['target_company_name'];?></td>
<td class="separation"></td>
<td class="right-input"><input type="text" name="target_company_name" class="txtinput" /></td>
</tr>

<tr>
<td colspan="2" class="vseparation"></td>
<td class="separation"></td>
<td></td>
</tr>

<tr>
<td class="left-label">Sector of TARGET Company / Asset:</td>
<td class="middle-data"><?php echo $g_view['deal_data']['target_sector'];?></td>
<td class="separation"></td>
<td class="right-input">
<select name="target_sector" id="target_sector" onChange="return target_sector_changed();">
<option value=""> Select sector </option>
<?php for($j=0;$j<$g_view['sector_count'];$j++):?>
<option value="<?php echo $g_view['sector_list'][$j]['sector'];?>" ><?php echo $g_view['sector_list'][$j]['sector'];?></option>
<?php endfor; ?>                                            
</select>
</td>
</tr>

<tr>
<td colspan="2" class="vseparation"></td>
<td class="separation"></td>
<td></td>
</tr>

<tr>
<td class="left-label">Industry of TARGET Company / Asset:</td>
<td class="middle-data"><?php echo $g_view['deal_data']['target_industry'];?></td>
<td class="separation"></td>
<td class="right-input">
<select name="target_industry" id="target_industry">
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
<td class="middle-data"><?php echo $g_view['deal_data']['target_country'];?></td>
<td class="separation"></td>
<td class="right-input">
<select name="target_country">
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
<td class="left-label">Name of SELLER (if relevant):</td>
<td class="middle-data"><?php echo $g_view['deal_data']['seller_company_name'];?></td>
<td class="separation"></td>
<td class="right-input"><input type="text" name="seller_company_name" class="txtinput" /></td>
</tr>

<tr>
<td colspan="2" class="vseparation"></td>
<td class="separation"></td>
<td></td>
</tr>

<tr>
<td class="left-label">Sector of SELLER:</td>
<td class="middle-data"><?php echo $g_view['deal_data']['seller_sector'];?></td>
<td class="separation"></td>
<td class="right-input">
<select name="seller_sector" id="seller_sector" onChange="return seller_sector_changed();">
<option value=""> Select sector </option>
<?php for($j=0;$j<$g_view['sector_count'];$j++):?>
<option value="<?php echo $g_view['sector_list'][$j]['sector'];?>" ><?php echo $g_view['sector_list'][$j]['sector'];?></option>
<?php endfor; ?>                                            
</select>
</td>
</tr>

<tr>
<td colspan="2" class="vseparation"></td>
<td class="separation"></td>
<td></td>
</tr>

<tr>
<td class="left-label">Industry of SELLER:</td>
<td class="middle-data"><?php echo $g_view['deal_data']['seller_industry'];?></td>
<td class="separation"></td>
<td class="right-input">
<select name="seller_industry" id="seller_industry_name">
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
<td class="middle-data"><?php echo $g_view['deal_data']['seller_country'];?></td>
<td class="separation"></td>
<td class="right-input">
<select name="seller_country">
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

<?php
/*********************
sng:15/mar/2012
Now we will have only one note box
**********************/
?>

<tr>
<td colspan="2"><strong>Deal Valuation</strong></td>
</tr>
<tr>
<td colspan="4" class="vseparation"></td>
</tr>

<tr>
<td class="left-label">Payment Type:</td>
<td class="middle-data">
<?php if($g_view['deal_data']['payment_type']==""){
	echo "n/a";
}else{
	if($g_view['deal_data']['payment_type']=="cash") echo "Cash";
	elseif($g_view['deal_data']['payment_type']=="equity") echo "Equity";
	elseif($g_view['deal_data']['payment_type']=="part_cash_part_quity") echo "Part Cash / Part Equity";
}
?>
</td>
<td class="separation"></td>
<td class="right-input">
<input type="radio" name="payment_type" value="cash" onclick="show_equity_percent(false)">Cash&nbsp;&nbsp;
<input type="radio" name="payment_type" value="equity" onclick="show_equity_percent(false)">Equity&nbsp;&nbsp;
<input type="radio" name="payment_type" value="part_cash_part_quity" onclick="show_equity_percent(true)">Part Cash/ Part Equity
</td>
</tr>
<tr>
<td colspan="2" class="vseparation"></td>
<td class="separation"></td>
<td></td>
</tr>

<tr>
<td class="left-label">Equity Payment %:</td>
<td class="middle-data">
<?php
/**********************
sng:21/july/2011
This is applicable only if payment type is part_cash_part_quity, otherwise not applicable
*************************/
if($g_view['deal_data']['payment_type']=="part_cash_part_quity"){
	if($g_view['deal_data']['equity_payment_percent']==""||$g_view['deal_data']['equity_payment_percent']==0.0) echo "n/a"; else echo $g_view['deal_data']['equity_payment_percent']."%";
}else{
	?>Not applicable<?php
}
?>
</td>
<td class="separation"></td>
<td class="right-input"><input type="text" name="equity_payment_percent" id="equity_payment_percent" class="shorttxtinput" disabled="disabled" /> %</td>
</tr>
<tr>
<td colspan="2" class="vseparation"></td>
<td class="separation"></td>
<td></td>
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
<td class="left-label">Local Currency per 1 USD (G):</td>
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
<td class="left-label">Target Publicly Listed:</td>
<td class="middle-data"><?php if($g_view['deal_data']['target_listed_in_stock_exchange'] == 'y'){?>Yes<?php }else{?>No<?php }?></td>
<td class="separation"></td>
<td class="right-input">
<input type="radio" name="target_listed_in_stock_exchange" value="y" onclick="show_target_listed_fields(true)">Yes&nbsp;&nbsp;
<input type="radio" name="target_listed_in_stock_exchange" value="n" onclick="show_target_listed_fields(false)">No
</td>
</tr>
<tr>
<td colspan="2" class="vseparation"></td>
<td class="separation"></td>
<td></td>
</tr>
<?php
/***************************************
sng:21/july/2011
The following are applicable only if the target is listed in stock exchange
*********************/
?>
<tr>
<td class="left-label">Name of the stock exchange:</td>
<td class="middle-data">
<?php if($g_view['deal_data']['target_listed_in_stock_exchange'] == 'y'){
	echo $g_view['deal_data']['target_stock_exchange_name'];
}else{
	?>Not applicable<?php
}
?>
</td>
<td class="separation"></td>
<td class="right-input"><input type="text" name="target_stock_exchange_name" id="target_stock_exchange_name" class="txtinput" disabled="disabled" /></td>
</tr>
<tr>
<td colspan="2" class="vseparation"></td>
<td class="separation"></td>
<td></td>
</tr>
<?php
/**********
if currency_price_per_share is given then no issue. If not, then it is the local currency of the deal
***********/
if($g_view['deal_data']['currency_price_per_share']==""){
	$currency_price_per_share = $deal_local_currency;
}else{
	$currency_price_per_share = $g_view['deal_data']['currency_price_per_share'];
}
?>
<tr>
<td class="left-label">Local Currency of Share Price:</td>
<td class="middle-data">
<?php
if($g_view['deal_data']['target_listed_in_stock_exchange'] == 'y'){
	echo $g_view['deal_data']['currency_price_per_share'];
}else{
	?>Not applicable<?php
}
?>
</td>
<td class="separation"></td>
<td class="right-input"><input type="text" name="currency_price_per_share" id="currency_price_per_share" class="txtinput" disabled="disabled" /></td>
</tr>
<tr>
<td colspan="2" class="vseparation"></td>
<td class="separation"></td>
<td></td>
</tr>

<tr>
<td class="left-label">Deal price per share (I):</td>
<td class="middle-data">
<?php
if($g_view['deal_data']['target_listed_in_stock_exchange'] == 'y'){
	if($g_view['deal_data']['deal_price_per_share']==""||$g_view['deal_data']['deal_price_per_share']=="0.0") echo "n/a"; else echo convert_million_for_display_round($g_view['deal_data']['deal_price_per_share'])." ".$currency_price_per_share;
}else{
	?>Not applicable<?php
}
?>
</td>
<td class="separation"></td>
<td class="right-input"><input type="text" name="deal_price_per_share" id="deal_price_per_share" class="shorttxtinput" disabled="disabled" /> (in local currency, per share)</td> 
</tr>
<tr>
<td colspan="2" class="vseparation"></td>
<td class="separation"></td>
<td></td>
</tr>

<tr>
<td class="left-label">Share price prior to announcement:</td>
<td class="middle-data">
<?php
if($g_view['deal_data']['target_listed_in_stock_exchange'] == 'y'){
	if($g_view['deal_data']['price_per_share_before_deal_announcement']==""||$g_view['deal_data']['price_per_share_before_deal_announcement']=="0.0") echo "n/a"; else echo convert_million_for_display_round($g_view['deal_data']['price_per_share_before_deal_announcement'])." ".$currency_price_per_share;
}else{
	?>Not applicable<?php
}
?>
</td>
<td class="separation"></td>
<td class="right-input"><input type="text" name="price_per_share_before_deal_announcement" id="price_per_share_before_deal_announcement" class="shorttxtinput" disabled="disabled" /> (in local currency, per share)</td> 
</tr>
<tr>
<td colspan="2" class="vseparation"></td>
<td class="separation"></td>
<td></td>
</tr>

<tr>
<td class="left-label">Date of share price, prior to announcement:</td>
<td class="middle-data">
<?php 
if($g_view['deal_data']['target_listed_in_stock_exchange'] == 'y'){
	if($g_view['deal_data']['date_price_per_share_before_deal_announcement']=="0000-00-00"||$g_view['deal_data']['date_price_per_share_before_deal_announcement']=="") echo "n/a"; else echo ymd_to_dmy($g_view['deal_data']['date_price_per_share_before_deal_announcement']);
}else{
	?>Not applicable<?php
}
?>
</td>
<td class="separation"></td>
<td class="right-input"><input type="text" name="date_price_per_share_before_deal_announcement" id="date_price_per_share_before_deal_announcement" class="shorttxtinput" disabled="disabled" />
<script type="text/javascript">
          // <![CDATA[       
            var opts = {                            
                    formElements:{"date_price_per_share_before_deal_announcement":"Y-ds-m-ds-d"},
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
<td class="left-label">Implied Premium:</td>
<td class="middle-data">
<?php
if($g_view['deal_data']['target_listed_in_stock_exchange'] == 'y'){
	if($g_view['deal_data']['implied_premium_percentage']==""||$g_view['deal_data']['implied_premium_percentage']==0.0) echo "n/a"; else echo $g_view['deal_data']['implied_premium_percentage']."%";
}else{
	?>Not applicable<?php
}
?>
</td>
<td class="separation"></td>
<td class="right-input"><input type="text" name="implied_premium_percentage" id="implied_premium_percentage" class="shorttxtinput" disabled="disabled" /> %</td>
</tr>
<tr>
<td colspan="2" class="vseparation"></td>
<td class="separation"></td>
<td></td>
</tr>

<tr>
<td class="left-label">Total Shares Outstanding (II):</td>
<td class="middle-data">
<?php
if($g_view['deal_data']['target_listed_in_stock_exchange'] == 'y'){
	if($g_view['deal_data']['total_shares_outstanding_million']==""||$g_view['deal_data']['total_shares_outstanding_million']==0.0) echo "n/a"; else echo convert_million_for_display_round($g_view['deal_data']['total_shares_outstanding_million'])." million";
}else{
	?>Not applicable<?php
}
?>
</td>
<td class="separation"></td>
<td class="right-input"><input type="text" name="total_shares_outstanding_million" id="total_shares_outstanding_million" class="shorttxtinput" disabled="disabled" /> (million)</td>
</tr>
<tr>
<td colspan="2" class="vseparation"></td>
<td class="separation"></td>
<td></td>
</tr>

<tr>
<td class="left-label">Implied Equity Value (A):<br />(I x II)</td>
<td class="middle-data"><?php if($g_view['deal_data']['implied_equity_value_in_million_local_currency']==""||$g_view['deal_data']['implied_equity_value_in_million_local_currency']==0.0) echo "n/a"; else echo convert_million_for_display_round($g_view['deal_data']['implied_equity_value_in_million_local_currency'])."m ".$deal_local_currency;?></td>
<td class="separation"></td>
<td class="right-input"><input type="text" name="implied_equity_value_in_million_local_currency" class="shorttxtinput" /> (in million, local currency)</td>
</tr>
<tr>
<td colspan="2" class="vseparation"></td>
<td class="separation"></td>
<td></td>
</tr>

<tr>
<td class="left-label">Net Debt (B):</td>
<td class="middle-data"><?php if($g_view['deal_data']['net_debt_in_million_local_currency']==""||$g_view['deal_data']['net_debt_in_million_local_currency']==0.0) echo "n/a"; else echo convert_million_for_display_round($g_view['deal_data']['net_debt_in_million_local_currency'])."m ".$deal_local_currency;?></td>
<td class="separation"></td>
<td class="right-input"><input type="text" name="net_debt_in_million_local_currency" class="shorttxtinput" /> (in million, local currency)</td>
</tr>
<tr>
<td colspan="2" class="vseparation"></td>
<td class="separation"></td>
<td></td>
</tr>

<tr>
<td class="left-label">Enterprise Value (C):<br />(A + B)</td>
<td class="middle-data"><?php if($g_view['deal_data']['enterprise_value_million_local_currency']==""||$g_view['deal_data']['enterprise_value_million_local_currency']==0.0) echo "n/a"; else echo convert_million_for_display_round($g_view['deal_data']['enterprise_value_million_local_currency'])."m ".$deal_local_currency;?></td>
<td class="separation"></td>
<td class="right-input"><input type="text" name="enterprise_value_million_local_currency" class="shorttxtinput" /> (in million, local currency)</td>
</tr>
<tr>
<td colspan="2" class="vseparation"></td>
<td class="separation"></td>
<td></td>
</tr>

<tr>
<td class="left-label">Acquisition of what % (D):</td>
<td class="middle-data"><?php if($g_view['deal_data']['acquisition_percentage']==""||$g_view['deal_data']['acquisition_percentage']==0.0) echo "n/a"; else echo $g_view['deal_data']['acquisition_percentage']."%";?></td>
<td class="separation"></td>
<td class="right-input"><input type="text" name="acquisition_percentage" class="shorttxtinput" /> %</td>
</tr>
<tr>
<td colspan="2" class="vseparation"></td>
<td class="separation"></td>
<td></td>
</tr>

<tr>
<td class="left-label">Dividend on Top of Equity (E):</td>
<td class="middle-data"><?php if($g_view['deal_data']['dividend_on_top_of_equity_million_local_curency']==""||$g_view['deal_data']['dividend_on_top_of_equity_million_local_curency']==0.0) echo "n/a"; else echo convert_million_for_display_round($g_view['deal_data']['dividend_on_top_of_equity_million_local_curency'])."m ".$deal_local_currency;?></td>
<td class="separation"></td>
<td class="right-input"><input type="text" name="dividend_on_top_of_equity_million_local_curency" class="shorttxtinput" /> (in million, local currency)</td>
</tr>
<tr>
<td colspan="2" class="vseparation"></td>
<td class="separation"></td>
<td></td>
</tr>

<tr>
<td class="left-label">Implied Deal Size (F):<br />(if D < 50 := A)<br />(if D > 50 := C + E)</td>
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
<td class="left-label">Implied Deal Size in USD:<br />(F / G)</td>
<td class="middle-data"><?php echo convert_deal_value_for_display_round($g_view['deal_data']['value_in_billion'],$g_view['deal_data']['value_range_id'],$g_view['deal_data']['fuzzy_value']);?></td>
<td class="separation"></td>
<td class="right-input"><?php include("deal_page_detail_snippet_value_range.php");?></td>
</tr>
<tr>
<td colspan="2" class="vseparation"></td>
<td class="separation"></td>
<td></td>
</tr>

<tr>
<td class="left-label">Implied Enterprise Value in USD:<br />(C / G)</td>
<td class="middle-data"><?php if($g_view['deal_data']['enterprise_value_million']==""||$g_view['deal_data']['enterprise_value_million']==0.0) echo "n/a"; else echo convert_million_for_display_round($g_view['deal_data']['enterprise_value_million'])."m USD";?></td>
<td class="separation"></td>
<td class="right-input"><input type="text" name="enterprise_value_million" class="shorttxtinput" /> (in million, USD)</td>
</tr>


<?php
/*********************
sng:15/mar/2012
Now we will have only one note box
**********************/
?>



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
<td class="left-label">Merger Type:</td>
<td class="middle-data"><?php echo $g_view['deal_data']['takeover_name'];?></td>
<td class="separation"></td>
<td class="right-input">
<select name="takeover_id">
<option value="">Select</option>
<?php
for($i=0;$i<$g_view['merger_list_count'];$i++){
    ?>
    <option value="<?php echo $g_view['merger_list'][$i]['takeover_id'];?>"><?php echo $g_view['merger_list'][$i]['takeover_name'];?></option>
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
<td class="left-label">Termination Fee:</td>
<td class="middle-data"><?php if($g_view['deal_data']['termination_fee_million']==""||$g_view['deal_data']['termination_fee_million']==0.0) echo "n/a"; else echo convert_million_for_display_round($g_view['deal_data']['termination_fee_million'])."m ".$deal_local_currency;?></td>
<td class="separation"></td>
<td class="right-input"><input type="text" name="termination_fee_million" class="shorttxtinput" /> (in million, local currency)</td>
</tr>
<tr>
<td colspan="2" class="vseparation"></td>
<td class="separation"></td>
<td></td>
</tr>

<tr>
<td class="left-label">End Date for Termination Fee:</td>
<td class="middle-data">
<?php if($g_view['deal_data']['end_date_termination_fee']=="0000-00-00"||$g_view['deal_data']['end_date_termination_fee']=="") echo "n/a"; else echo ymd_to_dmy($g_view['deal_data']['end_date_termination_fee']);?>
</td>
<td class="separation"></td>
<td class="right-input"><input type="text" name="end_date_termination_fee" id="end_date_termination_fee" class="shorttxtinput" />
<script type="text/javascript">
          // <![CDATA[       
            var opts = {                            
                    formElements:{"end_date_termination_fee":"Y-ds-m-ds-d"},
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
<td class="left-label">Fee (%) to Sellside Advisors:</td>
<td class="middle-data"><?php if($g_view['deal_data']['fee_percent_to_sellside_advisor']==""||$g_view['deal_data']['fee_percent_to_sellside_advisor']==0.0) echo "n/a"; else echo $g_view['deal_data']['fee_percent_to_sellside_advisor']."%";?></td>
<td class="separation"></td>
<td class="right-input"><input type="text" name="fee_percent_to_sellside_advisor" class="shorttxtinput" /> %</td>
</tr>
<tr>
<td colspan="2" class="vseparation"></td>
<td class="separation"></td>
<td></td>
</tr>

<tr>
<td class="left-label">Fee (%) to Buyside Advisors:</td>
<td class="middle-data"><?php if($g_view['deal_data']['fee_percent_to_buyside_advisor']==""||$g_view['deal_data']['fee_percent_to_buyside_advisor']==0.0) echo "n/a"; else echo $g_view['deal_data']['fee_percent_to_buyside_advisor']."%";?></td>
<td class="separation"></td>
<td class="right-input"><input type="text" name="fee_percent_to_buyside_advisor" class="shorttxtinput" /> %</td>
</tr>


<?php
/*********************
sng:15/mar/2012
Now we will have only one note box
**********************/
?>

<tr>
<td colspan="4" class="blockseparation"></td>
</tr>

<tr>
<td colspan="2"><strong>Financial Metrics</strong></td>
</tr>
<tr>
<td colspan="4" class="vseparation"></td>
</tr>

<tr>
<td class="left-label">Revenues Last 12 Months:</td>
<td class="middle-data"><?php if($g_view['deal_data']['revenue_ltm_million']==""||$g_view['deal_data']['revenue_ltm_million']==0.0) echo "n/a"; else echo convert_million_for_display_round($g_view['deal_data']['revenue_ltm_million'])."m ".$deal_local_currency;?></td>
<td class="separation"></td>
<td class="right-input"><input type="text" name="revenue_ltm_million" class="shorttxtinput" /> (in million, local currency)</td>
</tr>
<tr>
<td colspan="2" class="vseparation"></td>
<td class="separation"></td>
<td></td>
</tr>

<tr>
<td class="left-label">Revenues Most Recent Year:</td>
<td class="middle-data"><?php if($g_view['deal_data']['revenue_mry_million']==""||$g_view['deal_data']['revenue_mry_million']==0.0) echo "n/a"; else echo convert_million_for_display_round($g_view['deal_data']['revenue_mry_million'])."m ".$deal_local_currency;?></td>
<td class="separation"></td>
<td class="right-input"><input type="text" name="revenue_mry_million" class="shorttxtinput" /> (in million, local currency)</td>
</tr>
<tr>
<td colspan="2" class="vseparation"></td>
<td class="separation"></td>
<td></td>
</tr>

<tr>
<td class="left-label">Revenues Next Year:</td>
<td class="middle-data"><?php if($g_view['deal_data']['revenue_ny_million']==""||$g_view['deal_data']['revenue_ny_million']==0.0) echo "n/a"; else echo convert_million_for_display_round($g_view['deal_data']['revenue_ny_million'])."m ".$deal_local_currency;?></td>
<td class="separation"></td>
<td class="right-input"><input type="text" name="revenue_ny_million" class="shorttxtinput" /> (in million, local currency)</td>
</tr>
<tr>
<td colspan="2" class="vseparation"></td>
<td class="separation"></td>
<td></td>
</tr>

<tr>
<td class="left-label">EBITDA Last 12 Months:</td>
<td class="middle-data"><?php if($g_view['deal_data']['ebitda_ltm_million']==""||$g_view['deal_data']['ebitda_ltm_million']==0.0) echo "n/a"; else echo convert_million_for_display_round($g_view['deal_data']['ebitda_ltm_million'])."m ".$deal_local_currency;?></td>
<td class="separation"></td>
<td class="right-input"><input type="text" name="ebitda_ltm_million" class="shorttxtinput" /> (in million, local currency)</td>
</tr>
<tr>
<td colspan="2" class="vseparation"></td>
<td class="separation"></td>
<td></td>
</tr>

<tr>
<td class="left-label">EBITDA Most Recent Year:</td>
<td class="middle-data"><?php if($g_view['deal_data']['ebitda_mry_million']==""||$g_view['deal_data']['ebitda_mry_million']==0.0) echo "n/a"; else echo convert_million_for_display_round($g_view['deal_data']['ebitda_mry_million'])."m ".$deal_local_currency;?></td>
<td class="separation"></td>
<td class="right-input"><input type="text" name="ebitda_mry_million" class="shorttxtinput" /> (in million, local currency)</td>
</tr>
<tr>
<td colspan="2" class="vseparation"></td>
<td class="separation"></td>
<td></td>
</tr>

<tr>
<td class="left-label">EBITDA Next Year:</td>
<td class="middle-data"><?php if($g_view['deal_data']['ebitda_ny_million']==""||$g_view['deal_data']['ebitda_ny_million']==0.0) echo "n/a"; else echo convert_million_for_display_round($g_view['deal_data']['ebitda_ny_million'])."m ".$deal_local_currency;?></td>
<td class="separation"></td>
<td class="right-input"><input type="text" name="ebitda_ny_million" class="shorttxtinput" /> (in million, local currency)</td>
</tr>
<tr>
<td colspan="2" class="vseparation"></td>
<td class="separation"></td>
<td></td>
</tr>

<tr>
<td class="left-label">Net Income Last 12 Months:</td>
<td class="middle-data"><?php if($g_view['deal_data']['net_income_ltm_million']==""||$g_view['deal_data']['net_income_ltm_million']==0.0) echo "n/a"; else echo convert_million_for_display_round($g_view['deal_data']['net_income_ltm_million'])."m ".$deal_local_currency;?></td>
<td class="separation"></td>
<td class="right-input"><input type="text" name="net_income_ltm_million" class="shorttxtinput" /> (in million, local currency)</td>
</tr>
<tr>
<td colspan="2" class="vseparation"></td>
<td class="separation"></td>
<td></td>
</tr>

<tr>
<td class="left-label">Net Income Most Recent Year:</td>
<td class="middle-data"><?php if($g_view['deal_data']['net_income_mry_million']==""||$g_view['deal_data']['net_income_mry_million']==0.0) echo "n/a"; else echo convert_million_for_display_round($g_view['deal_data']['net_income_mry_million'])."m ".$deal_local_currency;?></td>
<td class="separation"></td>
<td class="right-input"><input type="text" name="net_income_mry_million" class="shorttxtinput" /> (in million, local currency)</td>
</tr>
<tr>
<td colspan="2" class="vseparation"></td>
<td class="separation"></td>
<td></td>
</tr>

<tr>
<td class="left-label">Net Income Next Year:</td>
<td class="middle-data"><?php if($g_view['deal_data']['net_income_ny_million']==""||$g_view['deal_data']['net_income_ny_million']==0.0) echo "n/a"; else echo convert_million_for_display_round($g_view['deal_data']['net_income_ny_million'])."m ".$deal_local_currency;?></td>
<td class="separation"></td>
<td class="right-input"><input type="text" name="net_income_ny_million" class="shorttxtinput" /> (in million, local currency)</td>
</tr>

<tr>
<td colspan="2" class="vseparation"></td>
<td class="separation"></td>
<td></td>
</tr>


<tr>
<td class="left-label">Year-End Date of Most Recent Financial Year:</td>
<td class="middle-data">
<?php if($g_view['deal_data']['date_year_end_of_recent_financial_year']=="0000-00-00"||$g_view['deal_data']['date_year_end_of_recent_financial_year']=="") echo "n/a"; else echo ymd_to_dmy($g_view['deal_data']['date_year_end_of_recent_financial_year']);?>
</td>
<td class="separation"></td>
<td class="right-input"><input type="text" name="date_year_end_of_recent_financial_year" id="date_year_end_of_recent_financial_year" class="shorttxtinput" />
<script type="text/javascript">
          // <![CDATA[       
            var opts = {                            
                    formElements:{"date_year_end_of_recent_financial_year":"Y-ds-m-ds-d"},
                    showWeeks:false                   
            };      
            datePickerController.createDatePicker(opts);
          // ]]>
          </script>
</td>
</tr>

<tr>
<td colspan="4" class="blockseparation"></td>
</tr>

<tr>
<td class="left-label">Enter additional details here  (e.g. termination fees, conditions):</td>
<td colspan="3">
<textarea name="additional_deal_details_note" class="txtareainput"></textarea></td>
</tr>

<tr>
<td colspan="4" class="blockseparation"></td>
</tr>

<tr>
<td class="left-label">Additional comments on list of banks and law firms:</td>
<td colspan="3">
<textarea name="additional_partners" class="txtareainput"></textarea>
</td>
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
jQuery('#target_stock_exchange_name').autocomplete({
	serviceUrl:'admin/ajax/fetch_stock_exchange_list.php',
	minChars:1,
	noCache: true,
	onSelect: function(value, data){
		jQuery('#target_stock_exchange_name').val(data);
	}
});
jQuery('#currency_price_per_share').autocomplete({
	serviceUrl:'admin/ajax/fetch_currency_list.php',
	minChars:1,
	noCache: true,
	onSelect: function(value, data){
		jQuery('#currency_price_per_share').val(data);
	}
});

function show_equity_percent(enable){
	if(enable){
		jQuery("#equity_payment_percent").attr("disabled",false);
	}else{
		jQuery("#equity_payment_percent").attr("disabled",true);
	}
}

function show_target_listed_fields(enable){
	if(enable){
		jQuery("#target_stock_exchange_name").attr("disabled",false);
		jQuery("#currency_price_per_share").attr("disabled",false);
		jQuery("#deal_price_per_share").attr("disabled",false);
		jQuery("#price_per_share_before_deal_announcement").attr("disabled",false);
		jQuery("#date_price_per_share_before_deal_announcement").attr("disabled",false);
		jQuery("#implied_premium_percentage").attr("disabled",false);
		jQuery("#total_shares_outstanding_million").attr("disabled",false);
	}else{
		jQuery("#target_stock_exchange_name").attr("disabled",true);
		jQuery("#currency_price_per_share").attr("disabled",true);
		jQuery("#deal_price_per_share").attr("disabled",true);
		jQuery("#price_per_share_before_deal_announcement").attr("disabled",true);
		jQuery("#date_price_per_share_before_deal_announcement").attr("disabled",true);
		jQuery("#implied_premium_percentage").attr("disabled",true);
		jQuery("#total_shares_outstanding_million").attr("disabled",true);
	}
}
</script>