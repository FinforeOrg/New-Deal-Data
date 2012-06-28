<?php
require_once("league_table_filter_support_js.php");
?>
<?php
/*****************************************
sng:1/oct/2011
we now put these in container view
<script src="js/jquery-ui-1.8.11.custom.min.js" type="text/javascript"></script>  
<script src="js/jquery.ui.selectmenu.js" type="text/javascript"></script>
<link rel="stylesheet" href="css/custom-theme/jquery-ui-1.8.9.custom.css" />
<link rel="stylesheet" href="css/custom-theme/jquery-ui-1.8.9.custom_orange.css" />
<link rel="stylesheet" href="css/custom-theme/jquery.ui.selectmenu.css" /> 
*********************************************/
?>
<?php
/*********************************
sng:27/nov/2010
*******/
?>
<script type="text/javascript">
$(function() {
    $( ".radio" ).buttonset().click(function(){$( ".radio" ).buttonset('refresh')});  ;    
    $( ".radio_subcat" ).buttonset().click(function(){
        $( ".radio_subsubcat :radio" ).each(function(idx){
            $(this).removeAttr('checked');
        })
        $( ".radio_subsubcat" ).buttonset('refresh')        
    });    
    $( ".radio_cat" ).buttonset().click(function(){
        $( ".radio_subcat :radio" ).each(function(idx){
            $(this).removeAttr('checked');
        })
        $( ".radio_subcat" ).buttonset('refresh')
    });  
    $( ".radio_subsubcat" ).buttonset();
    
    /** 
    * Hadle the case when a hidden sub sub cat is checked 
    */
    $('#cats :radio').click(function() {
        $('#cats :checked').each(function(idx){
            if (!$(this).is(':visible')) {
                $(this).removeAttr('checked');
            }
        })         
    }
   
    );      
    
    $('#sector').selectmenu().change(function(idx){
        $.post(
            '/admin/ajax/industry_list_for_sector.php?for=leagueTables',
            {'sector' : $(this).selectmenu('value')},
            function(data) {
                $('#industry').html(data).selectmenu();
            }
        )
        /*alert($(this).selectmenu('value') )*/
    });
    $('#month_division').selectmenu().change(
    function(idx){
        jQuery.post("ajax/month_division_list.php", 
            {'month_div': $(this).selectmenu('value')}, 
            function(data){
                if(data.length >0) {
                    //alert(data);
                    $('#month_division_list').html(data).selectmenu();
                }
            }) 
    });
        /*alert($(this).selectmenu('value') )*/
    //$('button').button().click(function(event){event.preventDefault()});

    $('.content input[type="button"]').button();
    $('.content input[type="submit"]').button();
    $('.content select').selectmenu();
    $('.loading').hide();
    $('#filters').show();
  
});

function categoryChanged(category) {
    $('div.radio_subcat:visible').hide();  
    $('#subCatsForCat' + category).show();
    $('div.radio_subsubcat:visible').hide();
}

function subCategoryChanged(subCategory) {
    $('div.radio_subsubcat:visible').hide();
    $('#subSubCatsForCat' + subCategory).show();    
}

</script>
<script type="text/javascript">
function goto_issuance_data(){
	//just go to issuance table
	var frm_obj = document.getElementById('issuance_table_filter');
	frm_obj.action = "issuance_data.php";
	frm_obj.target = "_self";
}
function download_issuance_data_detail(){
	var frm_obj = document.getElementById('issuance_table_filter');
	frm_obj.action = "download_issuance_data_detail.php";
	frm_obj.target = "_blank";
}
function update_issuance_data_detail(){
	var frm_obj = document.getElementById('issuance_table_filter');
	frm_obj.action = "issuance_data_detail.php";
	frm_obj.target = "_self";
}
function clear_error_msgs(){
	/*document.getElementById("err_deal_cat_name").innerHTML = "";
	**********
	sng:27/nov/2010
	we now have month division adn month division starting. We need to check
	********
	document.getElementById("err_month_division").innerHTML = "";
	document.getElementById("err_month_division_list").innerHTML = "";   */
}
function validate(){
    return true;
/*	//initially we assume that everything will be ok
	var validation_passed = true;
	//clear_error_msgs();
	                  
    if ($('input[name="deal_cat_name"]:checked').val() == undefined) {
       $('#error').html('Please select deal type').show(); 
       return false; 
    }

	**********
	sng:27/nov/2010
	we now have month division adn month division starting. We need to check
	*********/
	/*var month_division_obj = document.getElementById("month_division");
	if(month_division_obj.options[month_division_obj.selectedIndex].value==""){
		document.getElementById("err_month_division").innerHTML = "Please select";
		validation_passed = false;
	}else{
		//selected, so see if the start is selected or not
		var month_division_list_obj = document.getElementById("month_division_list");
		if(month_division_list_obj.options[month_division_list_obj.selectedIndex].value==""){
			document.getElementById("err_month_division_list").innerHTML = "Please select";
			validation_passed = false;
		}
	}
	return validation_passed; */
}
</script>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
<td>
<!--filter area-->
<form method="post" action="" id="issuance_table_filter" target="_self" onsubmit="return validate();">
<?php
/***************
sng:7/jan/2011
Client now wants that when the data is submitted to issuance_data, the user will see the chart
Problem is, how the code in the issuance_data will know that it is comming from issuance_data_detail?
We send a myaction here, and if that is present, the code in issuance_data does not fetch a random chart
and the code in the view page triggers a chart creation.
**********/
?>
<input type="hidden" name="myaction" value="gen_chart" />
<div class="loading" style="height: 200px; width: 100%;" >
</div>
<table width="100%" border="0" cellspacing="6" cellpadding="0"  id="filters" style="display: none;">
  <tr>                                                                   
    <td width="50%">
        <table width="100%" border="0" cellspacing="6" cellpadding="0">
          <tr>
            <td align="left" valign="top">Type of LT:</td>
            <td align="left" valign="top">&nbsp;</td>
          </tr>
          <tr>
            <td colspan="2" align="left" valign="top">
              <div class="radio_cat" style="font-size:10px;">
                <?php
                    $i = 1;
                    foreach($categories as $categoryName=>$subCats) :?>   
                <input type="radio" id="deal_cat_name<?php echo $i?>" name="deal_cat_name" value="<?php echo $categoryName?>" onclick="categoryChanged(<?php echo $i?>)" <?php if($_POST['deal_cat_name']==$categoryName){?>checked<?php }?>/><label for="deal_cat_name<?php echo $i?>"><?php echo $categoryName?></label>
                <?php $i++;endforeach?>
                </div>
              <?php 
                    $i = 1; $j = 1;
                    foreach($categories as $subCategoryName=>$subCats) :?>
              <div class="radio_subcat" style="font-size:10px;margin-top:5px;display: <?php if($_POST['deal_cat_name']==$subCategoryName){?>block<?php } else {?> none <?php }?>;" id="subCatsForCat<?php echo $i?>">   
                <?php foreach  ($subCats as $subCatName => $subSubCats) : ?>
                <?php 
                            $origSubcatName = $subCatName;
                            if($subCatName == 'Pending') {
                            $subCatName = 'All';
                        }?>
                <input type="radio" id="deal_subCat_name<?php echo $j?>" name="deal_subcat1_name" value="<?php echo ($subCatName == 'All') ? ''  : $subCatName?>" onclick="subCategoryChanged(<?php echo $j?>)" <?php if($_POST['deal_subcat1_name']==$origSubcatName){?>checked<?php }?>/><label for="deal_subCat_name<?php echo $j?>"><?php echo $subCatName?></label>
                <?php $j++;endforeach;?> 
                </div>
              <?php $i++; endforeach;?>
              
              <?php 
                    $i = 1;$j = 1;$k = 1;
                    foreach($categories as $subCategoryName=>$subCats) :?>
              <?php foreach  ($subCats as $subCatName => $subSubCats) : ?>
              <div class="radio_subsubcat <?php echo "parent_$k"?>" style="font-size:10px;margin-top:5px; display:<?php if($_POST['deal_cat_name']==$subCategoryName && $_POST['deal_subcat1_name']==$subCatName){?>block<?php } else {?> none <?php }?>;" id="subSubCatsForCat<?php echo $j?>">   
                <?php foreach ($subSubCats as $key=>$name) : ?>
                <?php if ($name == 'n/a') continue ?>
                <input type="radio" id="deal_subSubCat_name<?php echo $i?>" name="deal_subcat2_name" value="<?php echo $name?>" <?php if($_POST['deal_subcat2_name']==$name){?>checked<?php }?>/><label for="deal_subSubCat_name<?php echo $i?>"><?php echo $name?></label>
                <?php $i++;endforeach; ?> 
                </div>
              <?php $j++;endforeach;?> 
              
              <?php $k++; endforeach;?>        </td>
          </tr>
        </table>
    </td>
    <td width="51%">
    <table width="100%" border="0" cellspacing="6" cellpadding="0">
      <tr>
        <td colspan="3">Refine Analysis</td>
      </tr>
      <tr>
        <td><select name="region" id="region" style="width: 200px;">
          <option value="">Any Region</option>
          <?php for($i=0;$i<$g_view['region_count'];$i++) :?>
          <option value="<?php echo $g_view['region_list'][$i]['name'];?>" <?php if($_POST['region']==$g_view['region_list'][$i]['name']){?>selected="selected"<?php }?>><?php echo $g_view['region_list'][$i]['name'];?></option>
          <?php endfor; ?>
        </select></td>
        <td>or</td>
        <td><select name="country" id="country" style="width: 200px;">
          <option value="">Any Country</option>
          <?php for($i=0;$i<$g_view['country_count'];$i++):?>
          <option value="<?php echo $g_view['country_list'][$i]['name'];?>" <?php if($_POST['country']==$g_view['country_list'][$i]['name']){?>selected="selected"<?php }?>><?php echo $g_view['country_list'][$i]['name'];?></option>
          <?php endfor?>
        </select></td>
      </tr>
      <tr>
        <td><select name="sector" id="sector" onchange="" style="width: 200px;">
          <option value="">Any Sector</option>
          <?php for($j=0;$j<$g_view['sector_count'];$j++):?>
          <option value="<?php echo $g_view['sector_list'][$j]['sector'];?>" <?php if($_POST['sector']==$g_view['sector_list'][$j]['sector']){?>selected="selected"<?php }?> ><?php echo $g_view['sector_list'][$j]['sector'];?></option>
          <?php endfor; ?>
        </select></td>
        <td>and</td>
        <td><select name="industry" id="industry" style="width: 200px;">
          <option value="">Any Industry</option>
          <?php for($j=0;$j<$g_view['industry_count'];$j++):?>
          <option value="<?php echo $g_view['industry_list'][$j]['industry'];?>" <?php if($_POST['industry']==$g_view['industry_list'][$j]['industry']){?>selected="selected"<?php }?> ><?php echo $g_view['industry_list'][$j]['industry'];?></option>
          <?php endfor?>
        </select></td>
      </tr>
      <tr>
        <td>
            <select name="month_division" id="month_division" style="width:200px">
                <option value="" <?php if($_POST['month_division']==""){?>selected="selected"<?php }?>>Select</option>
                <option value="q" <?php if($_POST['month_division']=="q"){?>selected="selected"<?php }?>>Quarterly</option>
                <option value="h" <?php if($_POST['month_division']=="h"){?>selected="selected"<?php }?>>Semi-Annual</option>
                <option value="y" <?php if($_POST['month_division']=="y"){?>selected="selected"<?php }?>>Annual</option>
            </select>        
        </td>
        <td>start<br />with</td>
        <td align="left" valign="middle">                
            <select name="month_division_list" id="month_division_list"  style="width:200px">
                <option value="" selected="selected">Select</option>
                <?php
                for($j=0;$j<$g_view['month_div_cnt'];$j++){
                    ?>
                    <option value="<?php echo $g_view['month_div']['value_arr'][$j];?>" <?php if($_POST['month_division_list']==$g_view['month_div']['value_arr'][$j]){?>selected="selected"<?php }?>><?php echo $g_view['month_div']['label_arr'][$j];?></option>
                    <?php
                }
                ?>
            </select>
          </td>
      </tr>
      <tr>
        <td colspan="3">
<table>
                    <tr>
                        <td style="width: 30%">&nbsp;</td>
                        <td><select name="deal_size" id="deal_size" style="width: 200px;">
                          <option value="">Refine by Deal Size</option>
                          <?php for($j=0;$j<$g_view['deal_size_filter_list_count'];$j++):?>
                          <option value="<?php echo $g_view['deal_size_filter_list'][$j]['condition'];?>" <?php if($_POST['deal_size']==$g_view['deal_size_filter_list'][$j]['condition']){?>selected="selected"<?php } else { if ($g_view['deal_size_filter_list'][$j]['condition'] == '>=0.100') echo "selected='selected'"; }?> ><?php echo $g_view['deal_size_filter_list'][$j]['caption'];?></option>
                          <?php  endfor; ?>
                        </select></td>
                    </tr>
                </table>        
        </td>
        </tr>
    </table></td>
  </tr>
  <tr>
    <td>
        <div style="display: none; float:right " id="error"> 
        </div> 
        </td>
        <td style="text-align: right">   
        <input name="chart" type="submit" class="btn_auto" value="Chart" onclick="return goto_issuance_data();" />&nbsp;&nbsp;&nbsp;<input name="submit" type="submit" class="btn_auto" value="Update" onclick="update_issuance_data_detail()" />&nbsp;&nbsp;&nbsp;<input type="submit" class="btn_auto" value="Download to Excel" onclick="download_issuance_data_detail()" />                <?php 
        if($g_account->is_site_member_logged()){
            if ($savedSearches->searchBelongsToTheCurrentUser(base64_decode($_GET['token']))) : ?>
                <input  type="button" class="btn_auto" id="updateSearch" value="Update search" onclick="return updateVolumesDetailSearch(<?php echo base64_decode($_GET['token'])?>);"/>
            <?php elseif($savedSearches->searchCanBeImported(base64_decode($_GET['token']))) : ?>
                <input type="button" class="btn_auto" id="importSearch" value="Import search" />
            <?php else : ?>
                <input type="button" class="btn_auto" id="saveSearchButton" value="Save search" onclick="return saveVolumesDetailSearch();" />
            <?php endif; 
        } ?>    
    
    </td>
  </tr>
</table>
</form>
<!--filter area-->
</td>
</tr>
<tr>
<td>
<!--listing data-->
<table width="100%" cellpadding="0" cellspacing="0" class="company">
<tr>
<th>Quarter / year</th>
<th>Total Issuance (in bn $)</th>

</tr>
<?php
if(0==$g_view['data_count']){
	?>
	<tr>
	<td colspan="5">
	None found.
	</td>
	</tr>
	<?php
}else{
	
	////////////////////////////////////////////////////////////////////
	
	for($j=0;$j<$g_view['data_count'];$j++){
		?>
		<tr>
		<td><?php echo $g_view['data'][$j]['short_name'];?></td>
		<td><?php echo $g_view['data'][$j]['value'];?></td>
		</tr>
		<?php
	}
	//////////////////////////////////////
	
}
?>
</table>
<!--listing data-->
</td>
</tr>
</table>

<script type="text/javascript">
function updateVolumesDetailSearch(id){
    
    jQuery.post(
        'saved_searches.php?action=updateSearch&type=volumesDetail&id=' + id,
         $('#issuance_table_filter').serialize(true),
         function(response, status) {
             window.location.href = window.location.href;    
         },
         'json'
    )    
}

function saveVolumesDetailSearch(){
    jQuery.post(
        'saved_searches.php?action=saveSearch&type=volumesDetail',
         $('#issuance_table_filter').serialize(true),
         function(response, status) {
            alert(response.message);
            //window.location.href = response.newLocation;    
         },
         'json'
    )

}

</script>