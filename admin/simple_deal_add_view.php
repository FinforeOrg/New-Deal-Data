<script type="text/javascript" src="../js/jquery-1.2.1.pack.js"></script>

<script type="text/javascript">



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
<script type="text/javascript" src="js/datepicker.js"></script>
<link href="css/datepicker.css" rel="stylesheet" type="text/css" />

<script type="text/javascript">
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
                }
        });
        
    }
}

function deal_subsubcat_changed(){
    var type_obj = document.getElementById('deal_cat_name');
    var offset_selected = type_obj.selectedIndex;
    var type1_obj = document.getElementById('deal_subcat1_name');
    var offset1_selected = type1_obj.selectedIndex;
    var type2_obj = document.getElementById('deal_subcat2_name');
    var offset2_selected = type2_obj.selectedIndex;
    
}

</script>
<table width="100%" cellpadding="5" cellspacing="0" border="0">
<tr>
<td><span class="msg_txt"><?php echo $g_view['msg'];?></span></td>
</tr>
<tr>
<td>
<form method="post" action="">
<input type="hidden" name="action" value="add" />
<table width="100%" cellpadding="5" cellspacing="0" border="0">
<tr>
<td>Company</td>
<td>
<input type="hidden" name="company_id" id="company_id" value="<?php echo $g_view['input']['company_id'];?>" />
Type the first few letters. If the company is found, it will be shown in the list. Please select the company.<br />
<input type="text" name="deal_company_name" id="deal_company_name" class="txtbox" style="width:200px;" value="<?php echo $g_view['input']['deal_company_name'];?>" onkeyup="lookup(this.value);" onblur="hide_suggestion();" autocomplete="off" /><br />
        <span id="deal_company_searching"></span><br />
        <span class="err_txt"><?php echo $g_view['err']['company_id'];?></span>
        <div class="suggestionsBox" id="suggestions" style="display: none;">
        <img src="images/upArrow.png" style="position: relative; top: -18px; left: 30px;" alt="upArrow"  />
        <div class="suggestionList" id="autoSuggestionsList"></div>
        </div></td>
</tr>

<tr>
<td>Date Rumour</td>
<td>
<input name="date_rumour" id="date_rumour" type="text" style="width:200px;" value="<?php echo $g_view['input']['date_rumour'];?>" />

<script type="text/javascript">
      // <![CDATA[       
        var opts = {                            
                formElements:{"date_rumour":"Y-ds-m-ds-d"},
                showWeeks:false                    
        };      
        datePickerController.createDatePicker(opts);
      // ]]>
      </script></td>
</tr>

<tr>
<td>Date Announced</td>
<td>
<input name="date_announced" id="date_announced" type="text" style="width:200px;" value="<?php echo $g_view['input']['date_announced'];?>" />
<script type="text/javascript">
      // <![CDATA[       
        var opts = {                            
                formElements:{"date_announced":"Y-ds-m-ds-d"},
                showWeeks:false                    
        };      
        datePickerController.createDatePicker(opts);
      // ]]>
      </script></td>
</tr>



<tr>
<td>Date Completed</td>
<td>
<input name="date_closed" id="date_closed" type="text" style="width:200px;" value="<?php echo $g_view['input']['date_closed'];?>" /><br />
<span class="err_txt"><?php echo $g_view['err']['date_of_deal'];?></span>
<script type="text/javascript">
      // <![CDATA[       
        var opts = {                            
                formElements:{"date_closed":"Y-ds-m-ds-d"},
                showWeeks:false                    
        };      
        datePickerController.createDatePicker(opts);
      // ]]>
      </script></td>
</tr>




<tr>
<td>Category</td>
<td>
<select name="deal_cat_name" id="deal_cat_name" onchange="return deal_cat_changed();">
<option value="">Select</option>
<?php
for($i=0;$i<$g_view['cat_count'];$i++){
    ?>
    <option value="<?php echo $g_view['cat_list'][$i]['type'];?>" <?php if($g_view['input']['deal_cat_name']==$g_view['cat_list'][$i]['type']){?>selected="selected"<?php }?>><?php echo $g_view['cat_list'][$i]['type'];?></option>
    <?php
}
?>
</select><span class="err_txt"> *</span><br />
<span class="err_txt"><?php echo $g_view['err']['deal_cat_name'];?></span></td>
</tr>

<tr>
<td>Sub category</td>
<td>
<select name="deal_subcat1_name" id="deal_subcat1_name" onchange="return deal_subcat_changed();">
<option value="">Select</option>
<?php
for($i=0;$i<$g_view['subcat1_count'];$i++){
    ?>
    <option value="<?php echo $g_view['subcat1_list'][$i]['subtype1'];?>" <?php if($g_view['input']['deal_subcat1_name']==$g_view['subcat1_list'][$i]['subtype1']){?>selected="selected"<?php }?>><?php echo $g_view['subcat1_list'][$i]['subtype1'];?></option>
    <?php
}
?>
</select><span class="err_txt"> *</span><br />
<span class="err_txt"><?php echo $g_view['err']['deal_subcat1_name'];?></span></td>
</tr>

<tr>
<td>Sub sub category</td>
<td>
<select name="deal_subcat2_name" id="deal_subcat2_name" onchange="return deal_subsubcat_changed();">
<option value="">Select</option>
<?php
for($i=0;$i<$g_view['subcat2_count'];$i++){
    ?>
    <option value="<?php echo $g_view['subcat2_list'][$i]['subtype2'];?>" <?php if($g_view['input']['deal_subcat2_name']==$g_view['subcat2_list'][$i]['subtype2']){?>selected="selected"<?php }?>><?php echo $g_view['subcat2_list'][$i]['subtype2'];?></option>
    <?php
}
?>
</select><span class="err_txt"> *</span><br />
<span class="err_txt"><?php echo $g_view['err']['deal_subcat2_name'];?></span></td>
</tr>
<?php
/****************
sng:14/sep/2011
We need to have the checkbox 'Notify participants' so that when the banks / law firms for this deal is
added, the relevant parties are emailed.

If this deal is being added based on suggestion sent by member and that member wishes to notify others,
admin will check this checkbox
***********************/
?>
<tr>
<td>&nbsp;</td>
<td><input type="checkbox" name="email_participants" value="y" <?php if($g_view['input']['email_participants']=='y'){?>checked="checked"<?php }?>>&nbsp;Email partipating Syndicate Desks/ PR teams</td>
</tr>

<tr>
<td></td>
<td><input type="submit" name="submit" value="Add" /></td>
</tr>
</table>
</form>
</td>
</tr>
</table>

