<?php
/************
sng:7/apr/2011
support for target sector change and seller sector change

sng:16/june/2011
We have jquery support in content_view
**********/
?>
<script src="js/jquery.devbridge.autocomplete.js"></script>
<link type="text/css" rel="stylesheet" href="css/autocomplete.css" />
<script>
function lookup(inputString) {
    if(inputString.length == 0) {
        // Hide the suggestion box.
        $('#suggestions').hide();
    } else {
        // post data to our php processing page and if there is a return greater than zero
        // show the suggestions box
        
        
        $('#deal_company_searching').html("searching...");
        $.post("ajax/get_company_for_deal.php", {name: ""+inputString+"",type: "company"}, function(data){
            $('#deal_company_searching').html("");
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

// if user clicks a suggestion, fill the text box.
function fill(company_id,name) {
    $('#deal_company_name').val(name);
    $('#company_id').val(company_id);
    setTimeout("$('#suggestions').hide();", 200);
}
function hide_suggestion(){
    setTimeout("$('#suggestions').hide();", 200);
}
</script>


<script>
function deal_cat_changed(){
    var type_obj = document.getElementById('deal_cat_name');
    var offset_selected = type_obj.selectedIndex;
    if(offset_selected != 0){
        var deal_cat_name_selected = type_obj.options[offset_selected].value;
        //fetch the list of deal sub categories
        $.post("ajax/deal_subtype1_list.php", {deal_cat_name: ""+deal_cat_name_selected+""}, function(data){
                if(data.length >0) {
                    $('#deal_subcat1_name').html(data);
                    /***
                    sng:9/aug/2010
                    We change the subtype optons but what about the sub sub type? They can be selected only when subtype is selected.
                    So we blank out sub sub type
                    ***/
                    $('#deal_subcat2_name').html("<option value=\"\">Select</option>");
					fetch_deal_data_snippet();
                }
        });
        
    }
}
function deal_subcat_changed(){
    
    var type_obj = document.getElementById('deal_cat_name');
    var offset_selected = type_obj.selectedIndex;
    var type1_obj = document.getElementById('deal_subcat1_name');
    var offset1_selected = type1_obj.selectedIndex;
    
    if((offset_selected != 0)&&(offset1_selected!=0)){
        
        var deal_cat_name_selected = type_obj.options[offset_selected].value;
        var deal_subcat_name_selected = type1_obj.options[offset1_selected].value;
        //fetch the list of deal sub categories
        $.post("ajax/deal_subtype2_list.php", {deal_cat_name: ""+deal_cat_name_selected+"",deal_subcat_name: ""+deal_subcat_name_selected+""}, function(data){
            //alert(data);
                if(data.length >0) {
                    $('#deal_subcat2_name').html(data);
					fetch_deal_data_snippet();
                }
        });
        
    }
}




<?php
/****************
sng:13/feb/2012
We no longer need subsidiary_sector_changed,target_sector_changed, seller_sector_changed
****************/
?>
/*****************************************************************
sng:13/july/2011
support for sector change (of underlying security)

sng:14/feb/2012
No longer needed
******/

/*************************************************/
</script>
<script type="text/javascript" src="util.js"></script>
<script type="text/javascript" src="js/datepicker.js"></script>
<link href="css/datepicker.css" rel="stylesheet" type="text/css" />

<script>
function fetch_deal_data_snippet(){
	/**********
	see what is selected in deal type and sub type and sub-sub type and fetch that snipppet
	********/
	var deal_cat_name_selected = jQuery('#deal_cat_name :selected').val();
	var deal_subcat1_name_selected = jQuery('#deal_subcat1_name :selected').val();
	var deal_subcat2_name_selected = jQuery('#deal_subcat2_name :selected').val();
	
	jQuery.get("ajax/fetch_deal_data_snippet.php",{deal_id: <?php echo $_POST['deal_id'];?>,deal_cat_name: deal_cat_name_selected,deal_subcat1_name: deal_subcat1_name_selected,deal_subcat2_name: deal_subcat2_name_selected},function(data){
		jQuery('#snip').html(data);
	});
}
jQuery(function(){
	fetch_deal_data_snippet();
});
</script>
<script>
function fetch_correction_suggestion(field){
	var show_in_id = "suggestion_"+field;
	jQuery('#'+show_in_id).html("fetching...");
	//fire ajax
	jQuery.post('ajax/fetch_suggestion_for_deal_field.php',{deal_id: <?php echo $_POST['deal_id'];?>,data_name: field},function(data){
		jQuery('#'+show_in_id).html(data);
	});
	
	return false;
}
</script>

<table width="100%" cellpadding="5" cellspacing="0" border="0">
<tr>
<td><span class="msg_txt"><?php echo $g_view['msg'];?></span></td>
</tr>
<tr>
<td>
<form method="post" action="">
<input type="hidden" name="action" value="edit" />
<input type="hidden" name="deal_id" value="<?php echo $_POST['deal_id'];?>" />
<?php
/***
sng:15/may/2010
we send the current value also in case admin change the deal value and we have to update the adjusted values
for banks and law firms and the team members
********/
?>
<input type="hidden" name="current_value_in_billion" value="<?php echo $g_view['data']['value_in_billion'];?>" />
<table width="100%" cellpadding="5" cellspacing="0" border="0">
<?php
/***********************
sng:1/sep/2011
We show the deal id. This will be useful for admin. Ex, in deal suggestion detail popup,
admin accept a file by typing the deal id and clicking accept
***********************/
?>
<tr>
<td colspan="7"><strong>Deal ID: <?php echo $_POST['deal_id'];?></strong></td>
</tr>
<tr>
<td colspan="7"><strong>Type of deal</strong></td>
</tr>
<tr>
<td>Category</td>
<td>
<select name="deal_cat_name" id="deal_cat_name" onChange="return deal_cat_changed();">
<option value="">Select</option>
<?php
for($i=0;$i<$g_view['cat_count'];$i++){
    ?>
    <option value="<?php echo $g_view['cat_list'][$i]['type'];?>" <?php if($g_view['data']['deal_cat_name']==$g_view['cat_list'][$i]['type']){?>selected="selected"<?php }?>><?php echo $g_view['cat_list'][$i]['type'];?></option>
    <?php
}
?>
</select><span class="err_txt"> *</span><br />
<span class="err_txt"><?php echo $g_view['err']['deal_cat_name'];?></span>
</td>

<td>Sub category</td>
<td>
<select name="deal_subcat1_name" id="deal_subcat1_name" onChange="return deal_subcat_changed();">
<option value="">Select</option>
<?php
for($i=0;$i<$g_view['subcat1_count'];$i++){
    ?>
    <option value="<?php echo $g_view['subcat1_list'][$i]['subtype1'];?>" <?php if($g_view['data']['deal_subcat1_name']==$g_view['subcat1_list'][$i]['subtype1']){?>selected="selected"<?php }?>><?php echo $g_view['subcat1_list'][$i]['subtype1'];?></option>
    <?php
}
?>
</select><span class="err_txt"> *</span><br />
<span class="err_txt"><?php echo $g_view['err']['deal_subcat1_name'];?></span>
</td>

<td>Sub sub category</td>
<td>
<select name="deal_subcat2_name" id="deal_subcat2_name" onChange="return fetch_deal_data_snippet();">
<option value="">Select</option>
<?php
for($i=0;$i<$g_view['subcat2_count'];$i++){
    ?>
    <option value="<?php echo $g_view['subcat2_list'][$i]['subtype2'];?>" <?php if($g_view['data']['deal_subcat2_name']==$g_view['subcat2_list'][$i]['subtype2']){?>selected="selected"<?php }?>><?php echo $g_view['subcat2_list'][$i]['subtype2'];?></option>
    <?php
}
?>
</select><span class="err_txt"> *</span>&nbsp;<a href="#" onclick="return fetch_correction_suggestion('deal_type');"><img src="has_deal_suggestion.php?deal_id=<?php echo $_POST['deal_id'];?>&data_name=deal_type" /></a><br />
<span class="err_txt"><?php echo $g_view['err']['deal_subcat2_name'];?></span>
</td>
</tr>
<tr><td colspan="7" id="suggestion_deal_type"></td></tr>
<tr><td colspan="7"><hr noshade="noshade" /></td></tr>
</table>
<table width="100%" cellpadding="5" cellspacing="0" border="0">
<tr>
<td>Participants</td>
<td><a href="" onClick="return deal_participant_popup('<?php echo $_POST['deal_id'];?>');">Manage Participants</a></td>
</tr>
</table>
<?php
/*********************
sng:13/feb/2012
We do not need the company here or the subsidiaries. We will now have participant list with company name, role, footnote
********************/
?>
<?php
/************************
area for deal type specific input snippet
*************************/
?>
<div id="snip">
</div>

<table width="100%" cellpadding="5" cellspacing="0" border="0">
<tr><td colspan="7"><hr noshade="noshade" /></td></tr>
<?php
/*****
sng:30/nov/2010
we need a text area to store the countries of a deal
**********/
?>
<tr>
<td style="vertical-align:top;">Deal Countries</td>
<td>
Enter the countries here. If you are entering more than one country, separate each by a comma even if you put each country in a line by itself.<br />
<textarea name="deal_country" style="width:500px; height:100px;"><?php echo $g_view['data']['deal_country'];?></textarea>
</td>
</tr>
<?php
/*****
sng:2/dec/2010
we need a text area to store the sectors of the companies for a deal
**********/
?>
<tr>
<td style="vertical-align:top;">Deal Sectors</td>
<td>
Enter the sectors here. If you are entering more than one sector, separate each by a comma even if you put each sector in a line by itself.<br />
<textarea name="deal_sector" style="width:500px; height:100px;"><?php echo $g_view['data']['deal_sector'];?></textarea>
</td>
</tr>
<?php
/*****
sng:2/dec/2010
we need a text area to store the industries of the companies for a deal
**********/
?>
<tr>
<td style="vertical-align:top;">Deal Industries</td>
<td>
Enter the industries here. If you are entering more than one industry, separate each by a comma even if you put each industry in a line by itself.<br />
<textarea name="deal_industry" style="width:500px; height:100px;"><?php echo $g_view['data']['deal_industry'];?></textarea>
</td>
</tr>
<tr><td colspan="2"><hr noshade="noshade" /></td></tr>
<tr>
<td style="vertical-align:top;">Sources</td>
<td>
Enter only urls here. If you are entering more than one url, separate each by a comma even if you put each url in a line by itself.<br />
<textarea name="sources" style="width:500px; height:100px;"><?php echo $g_view['data']['sources'];?></textarea>&nbsp;<a href="#" onclick="return fetch_correction_suggestion('sources');"><img src="has_deal_suggestion.php?deal_id=<?php echo $_POST['deal_id'];?>&data_name=sources" /></a>
</td>
</tr>
<tr><td colspan="2" id="suggestion_sources"></td></tr>

<?php
/*****************
sng:2/aug/2011
We now use a popup to show the different notes for the deal, as suggested by members
The current system is ok for one liners but not for notes. Beside, each kind of deal has different kinds
of notes
*********************/
?>
<tr>
<td style="vertical-align:top;">Note</td>
<td>
<textarea name="note" style="width:300px; height:100px;"><?php echo $g_view['data']['note'];?></textarea>&nbsp;<a href="#" onclick="return deal_suggestion_note_popup('<?php echo $_POST['deal_id'];?>');"><img src="has_deal_note_suggestion.php?deal_id=<?php echo $_POST['deal_id'];?>" /></a>
</td>
</tr>


<?php
/**************
sng:4/feb/2011
support for private note by admin
************/
?>
<tr>
<td style="vertical-align:top;">Private Admin</td>
<td>
<textarea name="deal_private_note" style="width:300px; height:100px;"><?php echo $g_view['data']['deal_private_note'];?></textarea>
</td>
</tr>
<tr><td colspan="2"><hr noshade="noshade" /></td></tr>
<tr>
<td>&nbsp;</td>
<td><input type="checkbox" name="email_participants" value="y" <?php if($g_view['data']['email_participating_syndicates']=='y'){?>checked="checked"<?php }?>>&nbsp;Email partipating Syndicate Desks/ PR teams</td>
</tr>
</table>

<table width="100%" cellpadding="5" cellspacing="0" border="0">
<tr>
<td colspan="2"><a href="" onClick="return deal_bank_popup('<?php echo $_POST['deal_id'];?>');">Manage Banks</a></td>
</tr>
<tr>
<tr>
<td colspan="2"><a href="" onClick="return deal_lawfirm_popup('<?php echo $_POST['deal_id'];?>');">Manage Law Firms</a>&nbsp;<a href="#" onclick="return fetch_correction_suggestion('additional_partners');"><img src="has_deal_suggestion.php?deal_id=<?php echo $_POST['deal_id'];?>&data_name=additional_partners" /></a></td>
</tr>
<tr><td colspan="2" id="suggestion_additional_partners"></td></tr>
<?php 
/**
* 15.08.2010 13:45 
* imihai added support for multiple logos
*/
/**********************
sng:14/mar/2012
Now we have one or more companies associated with a deeal. We use the logos of those companies
instead of storing logos with deal
**************************/
?>


<?php
/******************
sng:2/mar/2012
Now deals can be created by members. If it is created by non privileged member
the deal record is inactive till admin marks it as active.

Also, at some point of time, admin needs to check the deal data. If admin has checked everything
then admin mark it as admin verified
****************************/
?>
<tr><td colspan="2" style="height:20px;"></td></tr>
<tr>
<td colspan="2"><input name="admin_verified" type="checkbox" value="y" <?php if($g_view['data']['admin_verified']=='y'){?>checked="checked"<?php }?> />&nbsp;Verified by admin&nbsp;&nbsp;&nbsp;<input name="is_active" type="checkbox" value="y" <?php if($g_view['data']['is_active']=='y'){?>checked="checked"<?php }?> />&nbsp;Active&nbsp;&nbsp;<input type="submit" name="submit" value="Update" /></td>
</tr>


</table>
</form>
</td>
</tr>
</table>