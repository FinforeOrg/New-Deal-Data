<?php
/*********************
sng:29/sep/2011
we now put the jquery in the container view
<script type="text/javascript" src="js/jquery-1.2.1.pack.js"></script>
****************************/
?>
<script type="text/javascript">
function lookup(inputString) {
	if(inputString.length == 0) {
		// Hide the suggestion box.
		$('#suggestions').hide();
	} else {
		// post data to our php processing page and if there is a return greater than zero
		// show the suggestions box
		
		var type_selected = "<?php echo $_SESSION['member_type'];?>";
		$('#firm_name_searching').html("searching...");
		$.post("ajax/company_list.php", {search_string: ""+inputString+"",type: ""+type_selected+""}, function(data){
			$('#firm_name_searching').html("");
			if(data.length >0) {
				$('#suggestions').show();
				$('#autoSuggestionsList').html(data);
			}else{
				//no matches found, we hide the suggestion list
				setTimeout("$('#suggestions').hide();", 200);
			}
		});
	}
} //end

function fill(thisValue) {
	$('#firm_name').val(thisValue);
	setTimeout("$('#suggestions').hide();", 200);
}
</script>
<table cellpadding="0" cellspacing="0" style="width:400px;">
<tr>
<td>
<!--add company-->
<form method="post" action="">
<input type="hidden" name="action" value="add" />
<input type="hidden" name="member_type" value="<?php echo $_SESSION['member_type'];?>" />
<table width="100%" cellpadding="0" cellspacing="0" class="company">
<tr><th colspan="2">Add Company</th></tr>
<tr>
<td>Company</td>
<td>
<input name="firm_name" id="firm_name" onkeyup="lookup(this.value);" onblur="fill();" type="text" class="txtbox" value="<?php echo $_POST['firm_name'];?>"/><br /><span id="firm_name_searching"></span><br />
<span class="err_txt"><?php echo $g_view['err']['firm_name'];?></span>
<div class="suggestionsBox" id="suggestions" style="display: none;">
<img src="images/upArrow.png" style="position: relative; top: -18px; left: 30px;" alt="upArrow"  />
<div class="suggestionList" id="autoSuggestionsList"></div>
</div>	
</td>
</tr>

<tr>
<td>Designation</td>
<td>
<select name="designation" id="designation" class="txtbox">
<option value="">Select</option>

<?php

for($j=0;$j<$g_view['designation_count'];$j++){
?>
<option value="<?php echo $g_view['designation_list'][$j]['designation'];?>" <?php if($_POST['designation']==$g_view['designation_list'][$j]['designation']){?>selected="selected"<?php }?>><?php echo $g_view['designation_list'][$j]['designation'];?></option>
<?php
}
?>   
</select><br />
<span class="err_txt"><?php echo $g_view['err']['designation'];?></span>
</td>
</tr>

<tr>
<td>Year From</td>
<td>
<select name="year_from" class="txtbox">
<option value="">Select</option>
<?php
$curr_year = date("Y");

for($year_past = 60;$year_past>=0;$year_past--)
{
?>
<option value="<?php echo $curr_year;?>" <?php if($_POST['year_from']==$curr_year){?>selected="selected"<?php }?>><?php echo $curr_year;?></option>
<?php
$curr_year--;
}
?>
</select>
<br />
<span class="err_txt"><?php echo $g_view['err']['year_from'];?></span>
</td>
</tr>

<tr>
<td>Year To</td>
<td>
<select name="year_to" class="txtbox">
<option value="">Select</option>
<?php
$curr_year = date("Y");

for($year_past = 60;$year_past>=0;$year_past--)
{
?>
<option value="<?php echo $curr_year;?>" <?php if($_POST['year_to']==$curr_year){?>selected="selected"<?php }?>><?php echo $curr_year;?></option>
<?php
$curr_year--;
}
?>
</select>
<br />
<span class="err_txt"><?php echo $g_view['err']['year_to'];?></span>
</td>
</tr>

<tr>
<td>&nbsp;</td>
<td><input type="submit" name="submit" value="Add" class="btn_auto" /></td>
</tr>

</table>
</form>
<!--add company-->
</td>
</tr>
<tr><td style="height:20px;">&nbsp;</td></tr>
<tr>
<td>
<!--list companies-->
<table width="100%" cellpadding="0" cellspacing="0" class="company">
<tr><th colspan="5">Previous Companies</th></tr>
<tr>
<th>Company</th>
<th>Designation</th>
<th>From</th>
<th>To</th>
<th>&nbsp;</th>
</tr>
<?php
if($g_view['work_count']==0){
?>
<tr><td colspan="4">None</td></tr>
<?php
}else{
	for($i=0;$i<$g_view['work_count'];$i++){
		?>
		<tr>
		<td><?php echo $g_view['work_data'][$i]['company_name'];?></td>
		<td><?php echo $g_view['work_data'][$i]['designation'];?></td>
		<td><?php echo $g_view['work_data'][$i]['year_from'];?></td>
		<td><?php echo $g_view['work_data'][$i]['year_to'];?></td>
		<td>
		<form method="post" action="">
		<input type="hidden" name="action" value="delete_work" />
		<input type="hidden" name="work_id" value="<?php echo $g_view['work_data'][$i]['work_id'];?>" />
		<input type="submit" value="Delete" class="btn_auto" />
		</form>
		</td>
		</tr>
		<?php
	}
}
?>
</table>
<!--list companies-->
</td>
</tr>
</table>