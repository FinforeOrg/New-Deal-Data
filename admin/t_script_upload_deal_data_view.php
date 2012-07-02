<script type="text/javascript" src="../js/prototype.js"></script>
<script type="text/javascript">
var g_updator;
function process_deals(){
	//get the data
	var data_file = document.getElementById('data_file').value;
	var num_bank_cols = document.getElementById('num_bank_cols').value;
	var num_law_firm_cols = document.getElementById('num_law_firm_cols').value;
	//validation
	var validation_passed = true;
	if(data_file == ""){
		document.getElementById('err_data_file').innerHTML = "Please specify the data filename";
		validation_passed = false;
	}
	if(num_bank_cols == ""){
		document.getElementById('err_num_bank_cols').innerHTML = "Please specify the number of bank columns";
		validation_passed = false;
	}
	if(num_law_firm_cols == ""){
		document.getElementById('err_num_law_firm_cols').innerHTML = "Please specify the number of law firm columns";
		validation_passed = false;
	}
	if(!validation_passed){
		return;
	}
	////////////////////////////////////////////
	//validation passed
	var stamp = new Date().getTime();
	var processor_url = "ajax/bulk_deal_data_upload.php?t="+stamp;
	new Ajax.Request(processor_url, {
		method: 'post',
		parameters: $('frm_process_deal_data').serialize(true),
		onSuccess: function(transport){
			document.getElementById("msg").innerHTML = transport.responseText;
			//the stats are sent as rows_scanned|company_count|bank_count|law_count|deals_count
			var stat = transport.responseText;
			var stat_tokens = stat.split("|");
			document.getElementById('rows_scanned').innerHTML = stat_tokens[0];
			document.getElementById('company_count').innerHTML = stat_tokens[1];
			document.getElementById('bank_count').innerHTML = stat_tokens[2];
			document.getElementById('law_count').innerHTML = stat_tokens[3];
			document.getElementById('deals_count').innerHTML = stat_tokens[4];
		},
		onFailure: function(){
			document.getElementById("msg").innerHTML = "Error";
		}
	});
	document.getElementById('rows_scanned').innerHTML = "";
	document.getElementById('company_count').innerHTML = "";
	document.getElementById('bank_count').innerHTML = "";
	document.getElementById('law_count').innerHTML = "";
	document.getElementById('deals_count').innerHTML = "";
	document.getElementById("msg").innerHTML = "Processing...";
	
}
</script>
<table width="100%" cellpadding="5" cellspacing="0" border="0">
<tr>
<td>
Enter the filename where the deal list is present.<br />
<strong>Note:</strong> Please upload the file in the admin/data folder first
</td>
</tr>
<tr>
<td>
<form method="post" action="" id="frm_process_deal_data">
<input type="hidden" name="action" value="extract_deals" />
<table width="100%" cellpadding="5" cellspacing="0">
<tr>
<td>Filename</td>
<td><input type="text" name="data_file" id="data_file" value="" size="30"  /><br />
<span class="err_txt" id="err_data_file"></span>
</td>
<tr>
<tr>
<td>Number of bank columns</td>
<td><input type="text" name="num_bank_cols" id="num_bank_cols" value="" size="30"  /><br />
<span class="err_txt" id="err_num_bank_cols"></span></td>
</tr>
<tr>
<td>Number of law firm columns</td>
<td><input type="text" name="num_law_firm_cols" id="num_law_firm_cols" value="" size="30"  /><br />
<span class="err_txt" id="err_num_law_firm_cols"></span></td>
</tr>
</table>

<input type="button" value="Process" onClick="process_deals()" />
</form>
</td>
</tr>
<tr>
<td>
<p>Rows scanned <span id="rows_scanned"></span></p>
<p>Companies entered <span id="company_count"></span></p>
<p>Banks entered <span id="bank_count"></span></p>
<p>Law firms entered <span id="law_count"></span></p>
<p>Deals entered <span id="deals_count"></span></p>
</td>
</tr>
<tr>
<td><div id="msg" style="width:400px; height:300px; border: 1px solid #000000;"></div></td>
</tr>
</table>