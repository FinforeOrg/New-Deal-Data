<?php
/********************
sng:29/jan/2013
deal subtype Additional is Secondaries
deal subtype IPO is IPOs
deal subtype Equity is Common Equity
*************************/
?>
<script type="text/javascript" src="../js/jquery-1.2.1.pack.js"></script>
<script type="text/javascript" src="../js/ajaxupload.js"></script>
<script type="text/javascript">

function setDefaultLogo(id) {
    $.get(
        'ajax/_transaction_logo_upload.php?action=setDefaultLogo&id=' + id,
        function(response) {
            $(".setDefault").each(
                function(idx) {
                    $(this).attr("src","<?php echo $g_http_path;?>/images/default.png")
                }
            )
            $("#logo-"+id + " .setDefault").attr("src","<?php echo $g_http_path;?>/images/isdefault.png");
        },
        'json'
    )
}

function deleteLogo(id) {
    $.get(
        'ajax/_transaction_logo_upload.php?action=deleteLogo&id=' + id,
        function(response) {
            $("#logo-"+id).remove();
        },
        'json'
    )
}
$(document).ready(function(){
    var upload = new AjaxUpload($('#uploadLink'), {
        action: 'ajax/_transaction_logo_upload.php',
        //name: 'woohoo-custom-name',
        data: {
            
        },
        autoSubmit: true,
        //responseType: 'json',
        onChange: function(file, ext){
        },
        onSubmit: function(file, ext){
            // Allow only images. You should add security check on the server-side.
            if (ext && /^(jpg|png|jpeg|gif)$/i.test(ext)) {                            
                this.setData({
                    'company' : $("#deal_company_name").val()
                });
            } else {
                alert('not image');
                return false;
            }                            
        },
        onComplete: function(file, response){
            $("#thumbs").append(response);
        }
    });
});

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
        show_hide_deal_type_specific_block();
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
        show_hide_deal_subtype_specific_block();
    }
}

function deal_subsubcat_changed(){
    var type_obj = document.getElementById('deal_cat_name');
    var offset_selected = type_obj.selectedIndex;
    var type1_obj = document.getElementById('deal_subcat1_name');
    var offset1_selected = type1_obj.selectedIndex;
    var type2_obj = document.getElementById('deal_subcat2_name');
    var offset2_selected = type2_obj.selectedIndex;
    if((offset_selected != 0)&&(offset1_selected!=0)&&(offset2_selected!=0)){
        show_hide_deal_subsubtype_specific_block()
    }
}
function hide_all_deal_specific_blocks(){
    document.getElementById("debt_specific").className = "opt_hide";
    $("#debt_specific a.date-picker-control").css("visibility","hidden");
    document.getElementById("ma_specific").className = "opt_hide";
    document.getElementById("equity_ipo_specific").className = "opt_hide";
    document.getElementById("equity_additional_specific").className = "opt_hide";
    document.getElementById("equity_rights_issue_specific").className = "opt_hide";
}
function show_hide_deal_type_specific_block(){
    //deal type changed
    var deal_type = $('#deal_cat_name').val();
    if(deal_type.toLowerCase() == "debt"){
        hide_all_deal_specific_blocks();
        document.getElementById("debt_specific").className = "opt_show";
        $("#debt_specific a.date-picker-control").css("visibility","visible");
        return;
    }
    if(deal_type.toLowerCase() == "equity"){
        hide_all_deal_specific_blocks();
        return;
    }
    if(deal_type.toLowerCase() == "m&a"){
        hide_all_deal_specific_blocks();
        document.getElementById("ma_specific").className = "opt_show";
        return;
    }
}

function show_hide_deal_subtype_specific_block(){
    hide_all_deal_specific_blocks();
    var deal_type = $('#deal_cat_name').val();
    if(deal_type.toLowerCase() == "debt"){
        hide_all_deal_specific_blocks();
        document.getElementById("debt_specific").className = "opt_show";
        $("#debt_specific a.date-picker-control").css("visibility","visible");
        return;
    }
    if(deal_type.toLowerCase() == "m&a"){
        hide_all_deal_specific_blocks();
        document.getElementById("ma_specific").className = "opt_show";
        return;
    }
    /**********************************************
    sng:11/nov/2010
    If Equity Convertible or Equity Preferred, show debt type area: coupon, maturity, rating
    ******/
    var deal_subtype = $('#deal_subcat1_name').val();
    if(deal_type.toLowerCase() == "equity"){
        if((deal_subtype.toLowerCase() == "convertible")||(deal_subtype.toLowerCase() == "preferred")){
            hide_all_deal_specific_blocks();
            document.getElementById("debt_specific").className = "opt_show";
            $("#debt_specific a.date-picker-control").css("visibility","visible");
            return;
        }
    }
    /*************************************/
}
function show_hide_deal_subsubtype_specific_block(){
    
    hide_all_deal_specific_blocks();
    var deal_type = $('#deal_cat_name').val();
    deal_type = deal_type.toLowerCase();
    var deal_subtype = $('#deal_subcat1_name').val();
    deal_subtype = deal_subtype.toLowerCase();
    var deal_subsubtype = $('#deal_subcat2_name').val();
    deal_subsubtype = deal_subsubtype.toLowerCase();
    
    if(deal_type=="equity"){
        if(deal_subtype=="common equity"){
            if(deal_subsubtype=="ipos"){
                document.getElementById("equity_ipo_specific").className = "opt_show";
                return;
            }
            if(deal_subsubtype=="secondaries"){
                document.getElementById("equity_additional_specific").className = "opt_show";
                return;
            }
            if(deal_subsubtype=="rights issue"){
                document.getElementById("equity_rights_issue_specific").className = "opt_show";
                return;
            }
            return;
        }
        //for other equity subtypes, no need to show
        /**********************************************
        sng:11/nov/2010
        If Equity Convertible or Equity Preferred, show debt type area: coupon, maturity, rating
        ******/
        if((deal_subtype.toLowerCase() == "convertible")||(deal_subtype.toLowerCase() == "preferred")){
            hide_all_deal_specific_blocks();
            document.getElementById("debt_specific").className = "opt_show";
            $("#debt_specific a.date-picker-control").css("visibility","visible");
            return;
        }
        /****************************************************/
        return;
    }
    if(deal_type=="debt"){
        document.getElementById("debt_specific").className = "opt_show";
        $("#debt_specific a.date-picker-control").css("visibility","visible");
        return;
    }
    if(deal_type=="m&a"){
        document.getElementById("ma_specific").className = "opt_show";
        return;
    }
    
}
function show_hide_all_deal_specific_blocks(){
    /***
    sng:9/aug/2010
    if there is validation error, we also need to show these depending on
    deal type/subtype/sub sub type selected
    ***/
    hide_all_deal_specific_blocks();
    var deal_type = "<?php echo $g_view['input']['deal_cat_name'];?>";
    deal_type = deal_type.toLowerCase();
    var deal_subtype = "<?php echo $g_view['input']['deal_subcat1_name'];?>";
    deal_subtype = deal_subtype.toLowerCase();
    var deal_subsubtype = "<?php echo $g_view['input']['deal_subcat2_name'];?>";
    deal_subsubtype = deal_subsubtype.toLowerCase();
    
    if(deal_type=="debt"){
        document.getElementById("debt_specific").className = "opt_show";
        $("#debt_specific a.date-picker-control").css("visibility","visible");
        return;
    }
    if(deal_type=="m&a"){
        document.getElementById("ma_specific").className = "opt_show";
        return;
    }
    if(deal_type=="equity"){
        if(deal_subtype=="common equity"){
            if(deal_subsubtype=="ipos"){
                document.getElementById("equity_ipo_specific").className = "opt_show";
                return;
            }
            if(deal_subsubtype=="secondaries"){
                document.getElementById("equity_additional_specific").className = "opt_show";
                return;
            }
            if(deal_subsubtype=="rights issue"){
                document.getElementById("equity_rights_issue_specific").className = "opt_show";
                return;
            }
            return;
        }
        //for other equity subtypes, no need to show
        /**********************************************
        sng:11/nov/2010
        If Equity Convertible or Equity Preferred, show debt type area: coupon, maturity, rating
        ******/
        if((deal_subtype.toLowerCase() == "convertible")||(deal_subtype.toLowerCase() == "preferred")){
            hide_all_deal_specific_blocks();
            document.getElementById("debt_specific").className = "opt_show";
            $("#debt_specific a.date-picker-control").css("visibility","visible");
            return;
        }
        /****************************************************/
        return;
    }
    
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
<input type="text" name="deal_company_name" id="deal_company_name" class="txtbox" style="width:200px;" value="<?php echo $g_view['input']['deal_company_name'];?>" onkeyup="lookup(this.value);" onblur="hide_suggestion();" /><br />
        <span id="deal_company_searching"></span><br />
        <span class="err_txt"><?php echo $g_view['err']['company_id'];?></span>
        <div class="suggestionsBox" id="suggestions" style="display: none;">
        <img src="images/upArrow.png" style="position: relative; top: -18px; left: 30px;" alt="upArrow"  />
        <div class="suggestionList" id="autoSuggestionsList"></div>
        </div>
</td>
</tr>


<tr>
<td>Deal Value</td>
<td>
<input name="value_in_billion" type="text" style="width:200px;" value="<?php echo $g_view['input']['value_in_billion'];?>" /><span class="err_txt"> *</span>(in billion USD)<br />
(Type 0 if the deal value is unknown or not disclosed)<br />
<span class="err_txt"><?php echo $g_view['err']['value_in_billion'];?></span>
</td>
</tr>

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
<textarea name="deal_country" style="width:500px; height:100px;"><?php echo $g_view['input']['deal_country'];?></textarea>
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
<textarea name="deal_sector" style="width:500px; height:100px;"><?php echo $g_view['input']['deal_sector'];?></textarea>
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
<textarea name="deal_industry" style="width:500px; height:100px;"><?php echo $g_view['input']['deal_industry'];?></textarea>
</td>
</tr>

<tr>
<td>Date</td>
<td>
<input name="date_of_deal" id="date_of_deal" type="text" style="width:200px;" value="<?php echo $g_view['input']['date_of_deal'];?>" /><span class="err_txt"> *</span><br />
<span class="err_txt"><?php echo $g_view['err']['date_of_deal'];?></span>
<script type="text/javascript">
      // <![CDATA[       
        var opts = {                            
                formElements:{"date_of_deal":"Y-ds-m-ds-d"},
                showWeeks:false                    
        };      
        datePickerController.createDatePicker(opts);
      // ]]>
      </script>
</td>
</tr>

<tr>
<td colspan="2"><span class="err_txt"> (Only for deals in currency other than USD)</span></td>
</tr>

<tr>
<td>Currency</td>
<td>
<input name="currency" type="text" style="width:200px;" value="<?php echo $g_view['input']['currency'];?>" />
</td>
</tr>
<tr>
<td>Exchange Rate</td>
<td>
<input name="exchange_rate" type="text" style="width:200px;" value="<?php echo $g_view['input']['exchange_rate'];?>" />
</td>
</tr>
<tr>
<td>Value in the<br />specified currency</td>
<td>
<input name="value_in_billion_local_currency" type="text" style="width:200px;" value="<?php echo $g_view['input']['value_in_billion_local_currency'];?>" /> (in billion)
</td>
</tr>
<tr><td colspan="2"><hr noshade="noshade" /></td></tr>

<tr>
<td>Base Fee</td>
<td>
<input name="base_fee" type="text" style="width:200px;" value="<?php echo $g_view['input']['base_fee'];?>" /> %
</td>
</tr>

<tr>
<td>Incentive Fee</td>
<td>
<input name="incentive_fee" type="text" style="width:200px;" value="<?php echo $g_view['input']['incentive_fee'];?>" /> %
</td>
</tr>

<tr>
    <td style="vertical-align:top;">Sources</td>
    <td>
    Enter only urls here. If you are entering more than one url, separate each by a comma even if you put each url in a line by itself.<br />
        <textarea name="sources" style="width:500px; height:100px;"><?php echo $g_view['input']['sources'];?></textarea>
    </td>
</tr>


<tr>
    <td style="vertical-align:top;">Note</td>
    <td>
        <textarea name="note" style="width:300px; height:100px;"><?php echo $g_view['input']['note'];?></textarea>
    </td>
</tr>

<tr><td colspan="2"><hr noshade="noshade" /></td></tr>

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
<span class="err_txt"><?php echo $g_view['err']['deal_cat_name'];?></span>
</td>
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
<span class="err_txt"><?php echo $g_view['err']['deal_subcat1_name'];?></span>
</td>
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
<span class="err_txt"><?php echo $g_view['err']['deal_subcat2_name'];?></span>
</td>
</tr>
<tr><td colspan="2"><hr noshade="noshade" /></td></tr>

<tr>
<td colspan="2">
    <div id="debt_specific" class="opt_hide">
    <table>
    <tr>
    <td>Coupon</td>
    <td>
    <input name="coupon" type="text" style="width:200px;" value="<?php echo $g_view['input']['coupon'];?>" /><br />
    <span class="err_txt"><?php echo $g_view['err']['coupon'];?></span>
    </td>
    </tr>
    
    <tr>
    <td>Maturity Date</td>
    <td>
    <input name="maturity_date" id="maturity_date" type="text" style="width:200px;" value="<?php echo $g_view['input']['maturity_date'];?>" /> (select if applicable)
    <script type="text/javascript">
          // <![CDATA[       
            var opts = {                            
                    formElements:{"maturity_date":"Y-ds-m-ds-d"},
                    showWeeks:false                    
            };      
            datePickerController.createDatePicker(opts);
          // ]]>
          </script>
    </td>
    </tr>
    
    <tr>
    <td>Current Rating</td>
    <td>
    <input name="current_rating" type="text" style="width:200px;" value="<?php echo $g_view['input']['current_rating'];?>" /><br />(ex: Moodys AAA, Fitch A3)
    </td>
    </tr>
    </table>
    </div>
    <div id="ma_specific" class="opt_hide">
    <table>
    <tr>
    <td>Target Company</td>
    <td>
    <?php
    /****
    sng:18/may/2010
    We do not use the target company id, just the name, so we get rid of the list
    Even if we use the company id, we should use the hint and not a looooong list
    ***/
    ?>
    <input name="target_company_name" type="text" style="width:200px;" value="<?php echo $g_view['input']['target_company_name'];?>" /><br />
    <span class="err_txt"><?php echo $g_view['err']['target_company_name'];?></span>
    </td>
    </tr>
    
    <tr>
    <td>Target Country</td>
    <td>
    <select name="target_country">
    <option value="">Select</option>
    <?php
    for($i=0;$i<$g_view['country_count'];$i++){
        ?>
        <option value="<?php echo $g_view['country_list'][$i]['name'];?>" <?php if($g_view['input']['target_country']==$g_view['country_list'][$i]['name']){?>selected="selected"<?php }?>><?php echo $g_view['country_list'][$i]['name'];?></option>
        <?php
    }
    ?>
    </select><br />
    <span class="err_txt"><?php echo $g_view['err']['target_country'];?></span>
    </td>
    </tr>
    
    <?php
    /***
    sng:12/may/2010
    for m and a deals, there will be target sector but in the table, it will be called target industry
    ***/
    ?>
    <tr>
    <td>Target Sector</td>
    <td>
    <select name="target_sector">
    <option value="">Select</option>
    <?php
    for($i=0;$i<$g_view['sector_count'];$i++){
        ?>
        <option value="<?php echo $g_view['sector_list'][$i]['sector'];?>" <?php if($g_view['input']['target_sector']==$g_view['sector_list'][$i]['sector']){?>selected="selected"<?php }?>><?php echo $g_view['sector_list'][$i]['sector'];?></option>
        <?php
    }
    ?>
    </select><br />
    <span class="err_txt"><?php echo $g_view['err']['target_sector'];?></span>
    </td>
    </tr>
    
    
    <tr>
    <td>Seller Company</td>
    <td>
    <?php
    /****
    sng:18/may/2010
    We do not use the target company id, just the name, so we get rid of the list
    Even if we use the company id, we should use the hint and not a looooong list
    ***/
    ?>
    <input name="seller_company_name" type="text" style="width:200px;" value="<?php echo $g_view['input']['seller_company_name'];?>" /><br />
    <span class="err_txt"><?php echo $g_view['err']['seller_company_name'];?></span>
    </td>
    </tr>
    
    <tr>
    <td>Seller Country</td>
    <td>
    <select name="seller_country">
    <option value="">Select</option>
    <?php
    for($i=0;$i<$g_view['country_count'];$i++){
        ?>
        <option value="<?php echo $g_view['country_list'][$i]['name'];?>" <?php if($g_view['input']['seller_country']==$g_view['country_list'][$i]['name']){?>selected="selected"<?php }?>><?php echo $g_view['country_list'][$i]['name'];?></option>
        <?php
    }
    ?>
    </select><br />
    <span class="err_txt"><?php echo $g_view['err']['seller_country'];?></span>
    </td>
    </tr>
    
    <?php
    /***
    sng:12/may/2010
    for m and a deals, there will be target sector but in the table, it will be called target industry
    ***/
    ?>
    <tr>
    <td>Seller Sector</td>
    <td>
    <select name="seller_sector">
    <option value="">Select</option>
    <?php
    for($i=0;$i<$g_view['sector_count'];$i++){
        ?>
        <option value="<?php echo $g_view['sector_list'][$i]['sector'];?>" <?php if($g_view['input']['seller_sector']==$g_view['sector_list'][$i]['sector']){?>selected="selected"<?php }?>><?php echo $g_view['sector_list'][$i]['sector'];?></option>
        <?php
    }
    ?>
    </select><br />
    <span class="err_txt"><?php echo $g_view['err']['seller_sector'];?></span>
    </td>
    </tr>
    
    
    <tr>
    <td>EV/EBITDA LTM</td>
    <td>
    <input name="ev_ebitda_ltm" type="text" style="width:200px;" value="<?php echo $g_view['input']['ev_ebitda_ltm'];?>" />
    </td>
    </tr>
    
    <tr>
    <td>EV/EBITDA +1yr</td>
    <td>
    <input name="ev_ebitda_1yr" type="text" style="width:200px;" value="<?php echo $g_view['input']['ev_ebitda_1yr'];?>" />
    </td>
    </tr>
    
    <tr>
    <td>Premia (30 days)</td>
    <td>
    <input name="30_days_premia" type="text" style="width:200px;" value="<?php echo $g_view['input']['30_days_premia'];?>" /> %
    </td>
    </tr>
    
    </table>
    </div>
    <div id="equity_ipo_specific" class="opt_hide">
    <table>
    <tr>
    <td>1 day price change</td>
    <td>
    <input name="1_day_price_change" type="text" style="width:200px;" value="<?php echo $g_view['input']['1_day_price_change'];?>" />
    </td>
    </tr>
    </table>
    </div>
    <div id="equity_additional_specific" class="opt_hide">
    <table>
    <tr>
    <td>Discount to last</td>
    <td>
    <input name="discount_to_last" type="text" style="width:200px;" value="<?php echo $g_view['input']['discount_to_last'];?>" />
    </td>
    </tr>
    </table>
    </div>
    <div id="equity_rights_issue_specific" class="opt_hide">
    <table>
    <tr>
    <td>Discount to TERP</td>
    <td>
    <input name="discount_to_terp" type="text" style="width:200px;" value="<?php echo $g_view['input']['discount_to_terp'];?>" />
    </td>
    </tr>
    </table>
    </div>
    
</td>
</tr>


<?php 
/**
* 15.08.2010 13:45 
* imihai added support for multiple logos
*/
?>
<tr>
<td colspan="2"> Logos for this deal (Leave empty to use default logos)</td>
</tr>
<tr>
<td style="cursor: pointer"><img id="uploadLink" style="cursor: pointer; display: block;" src="<?php echo $g_http_path;?>/images/upload.png"/></td>
<td id='thumbs'>
<?php if (is_array($_SESSION['logos'])) {
  foreach ($_SESSION['logos'] as $key=>$logo) : ?>
             <div style="float:left;" id="logo-<?php echo $key ?>">
                <div style="width:200px;  height:200px; text-align:center;"> 
                    <img src="http://<?php echo $_SERVER['HTTP_HOST'];?>/uploaded_img/logo/thumbnails/<?php echo $logo['fileName'] ?>" style="width:150;" />
                </div>
                <div style="width:100%; height:40px; text-align:center; clear:both" > 
                     <img src="http://<?php echo $_SERVER['HTTP_HOST'];?>/images/delete.png" onclick="return deleteLogo(<?php echo $key?>)" style="cursor:pointer" title="Delete this Logo">
                     <img src="http://<?php echo $_SERVER['HTTP_HOST'];?>/images/<?php if ($logo['default'] == 1) echo 'isdefault.png'; else echo 'default.png' ?>" onclick="return setDefaultLogo(<?php echo $key?>)" style="cursor:pointer" title="Set this logo as default" class="setDefault">
                </div> 
           </div>   
    
<?php endforeach ?> 
<?php } ?>
</td>
</tr>

<?php 
/**
* End support for multiple logos
*/
?>
<?php
/***
sng:31/aug/2010
need a checkbox to know whether admin wants to add banks and law firms.
This way, we can redirect to another page with transaction_id and reuse the add banks/law firms popups
*************/
?>
<tr>
<td></td>
<td><input name="add_partner" type="checkbox" value="y" <?php if(isset($_POST['add_partner'])){?>checked="checked"<?php }?> /> Add banks and law firms</td>
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
<script type="text/javascript">
show_hide_all_deal_specific_blocks();
</script>
