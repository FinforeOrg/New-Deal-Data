<?php
/*******
sng:10/jul/2010
Logged in user can filter via the industry also. This means, when sector is changed, industry has to be updated.
We use the ajax to update.
The problem is, when the user is not logged, the industry drop down is not shown and there is no need for ajax update.
What we do is, use if clause to define the body of the ajax function
********/
?>
<?php
/*********************
sng:1/oct/2011
we now put these in container view
<script src="js/jquery-ui-1.8.11.custom.min.js" type="text/javascript"></script>  
<script src="js/jquery.ui.selectmenu.js" type="text/javascript"></script>
<link rel="stylesheet" href="css/custom-theme/jquery-ui-1.8.9.custom.css" />
<link rel="stylesheet" href="css/custom-theme/jquery.ui.selectmenu.css" /> 
****************************/
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
    $('select').selectmenu();
    $('#sector').selectmenu().change(function(idx){
        $.post(
            'admin/ajax/industry_list_for_sector.php?for=leagueTables',
            {'sector' : $(this).selectmenu('value')},
            function(data) {
                $('#industry').html(data).selectmenu();
            }
        )
        /*alert($(this).selectmenu('value') )*/
    });
    //$('button').button().click(function(event){event.preventDefault()});

    $('.content input[type="button"]').button();
    $('.content input[type="submit"]').button();
    $('.loading').hide();
    $('#deal_search_frm').show();
  
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
function saveSearch(isalert) {
    data = $("#deal_search_frm").serialize();
    isalert = isalert == undefined ? 0 : isalert;
    $.ajax({
       type: "POST",
       url: "saved_searches.php?action=saveSearch&type=deal&alert=" + isalert,
       data: data,
       success: function(obj){
         alert( obj.message );
         if (obj.newLocation != undefined ) {
             window.location.href = obj.newLocation;
         }
       },
       dataType: 'json'
     });    
}

function updateSearch(id, isalert) {
    data = $("#deal_search_frm").serialize();
    isalert = isalert == undefined ? 0 : isalert;
    $.ajax({
       type: "POST",
       url: "saved_searches.php?action=updateSearch&type=deal&id="+id+"&alert="+isalert,
       data:  data,
       success: function(obj){
         alert( obj.message );
             window.location.reload();
       },
       dataType: 'json'
     });  
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
		$.post("admin/ajax/deal_subtype2_list.php", {deal_cat_name: ""+deal_cat_name_selected+"",deal_subcat_name: ""+deal_subcat_name_selected+""}, function(data){
			//alert(data);
				if(data.length >0) {
					$('#deal_subcat2_name').html(data);
				}
		});
	}
}
</script>
<script type="text/javascript">
function get_deal_company(){
	var obj = document.getElementById("top_search_term");
	if(obj!=null){
		var top_search_term_name = obj.value;
		document.getElementById("hidden_top_search_term").value=top_search_term_name;
	}
	return true;
}
</script>

<script type="text/javascript">
function download_searched_deals(){
	var frm_obj = document.getElementById('deal_search_frm');
	frm_obj.action = "download_searched_deals.php";
	frm_obj.target = "_blank";
}

function search_for_deals(){
	var frm_obj = document.getElementById('deal_search_frm');
	frm_obj.action = "deal_search.php<?php if (isset($_REQUEST['action'])) echo "?action=" . $_REQUEST['action'] ?>";
	frm_obj.target = "_self";
}
</script>
<script>
<?php
/*******************
sng:31/oct/2011
Either allow to select ALL/TOP 20/RECENT 10 etc or filter by deal size.
If the dropdown has the value of 'size' we programmatically select an option in the filter by size dropdown
else we select the blank option in the filter by size dropdown
********************/
?>
$(function(){
	$('#number_of_deals').change(function(){
		if($(this).val()=="size"){
			$('#value_range_id').selectmenu("index",1);
		}else{
			$('#value_range_id').selectmenu("index",0);
		}
		/*****************
		sng:7/nov/2011
		check if we need to change the download label
		******************/
		change_download_to_largest();
	});
});
</script>

<script>
/***********************
sng:1/nov/2011
We have switched the order of filter by size and num of deals
So now, if we select any entry in filter by size dropdown, we set the num of deals dropdown
to the dummy value 'size'
*************************/
$(function(){
	$('#value_range_id').change(function(){
		if($(this).val()==""){
			$('#number_of_deals').selectmenu("value","");
		}else{
			$('#number_of_deals').selectmenu("value","size");
		}
		/*****************
		sng:7/nov/2011
		check if we need to change the download label
		******************/
		change_download_to_largest();
	});
});
</script>
<?php
/********************************************
sng:7/nov/2011
if we are not filtering to Top 25 OR Recent 25, we allow to download only the
largest 100 deals that match the filters. We need to change the label of the download button
whenever we change the size filter or number of deals filter.
Basically we check the selected option
*********************************************/
?>
<script>
function change_download_to_largest(){
	var num_deals_selected = $('#number_of_deals').val();
	if((num_deals_selected=="size")||(num_deals_selected=="")){
		$('#download_to_excel').attr('value','Download 100 Largest Deals to Excel');
	}else{
		$('#download_to_excel').attr('value','Download to Excel');
	}
}
$(function(){
	change_download_to_largest();
});
</script>
<script>
function goto_login(){
	document.location.replace("login.php");
}
</script>
<?php
/***
sng:29/apr/2010
we check if there is a field called deal_company. If so, we get the value entered there and
populate a hidden field so that, the deal company name is also sent as POST data

sng:19/may/2010
The default top form is used and so we cannot have hidden field called action, since that confuse IE (see default_search_view.php)
so we use myaction
Also, in the top form, the text field is top_search_term, so here also we change it to top_search_term

sng:20/july
logged in members can download deal search data to excel
**********/
?>
<div class="loading" style="height: 200px; width: 100%;" >
</div>
<form id="deal_search_frm" method="post" action="deal_search.php<?php if (isset($_REQUEST['action'])) echo "?action=" . $_REQUEST['action'] ?>" onsubmit="return get_deal_company();" enctype="application/x-www-form-urlencoded" style="display: none;">
<input type="hidden" name="myaction" value="search" />
<input type="hidden" name="top_search_term" id="hidden_top_search_term" value="<?php $g_view['deal_company_form_input'];?>"  />
<?php
/****************************************
sng:13/jan/2011
Now we may call deal search by passing the partner_id to filter the search, showing the deals
where the firm (partner_id) was involved)
********/
if(isset($_REQUEST['partner_id'])){
	?>
	<input type="hidden" name="partner_id" value="<?php echo $_REQUEST['partner_id'];?>" />
	<?php
}
/*******************************************************/
?>
    <!--<br clear="all" />-->
     <table width="100%" border="0" cellspacing="6" cellpadding="0" style="display:block" id="filters">
      <tr>                                                                   
        <td width="50%" >
            <table width="100%" border="0" cellspacing="6" cellpadding="0">
              <tr>
                <td align="left" valign="top" style="width:120px;">Type of Transaction:</td>
                <td align="left" valign="top">&nbsp;</td>
              </tr>
              <tr>
                <td colspan="2" align="left" valign="top">
                  <div class="radio_cat" style="font-size:10px;">
                    <?php
                        $i = 1;
                        foreach($categories as $categoryName=>$subCats) :?>   
                    <input type="radio" id="deal_cat_name<?php echo $i?>" name="deal_cat_name" value="<?php echo $categoryName?>" onClick="categoryChanged(<?php echo $i?>)" <?php if($_POST['deal_cat_name']==$categoryName){?>checked<?php }?>/><label for="deal_cat_name<?php echo $i?>"><?php echo $categoryName?></label>
                    <?php $i++;endforeach?>
                    </div>
                  <?php 
                        $i = 1; $j = 1;
                        foreach($categories as $subCategoryName=>$subCats) :?>
                  <div class="radio_subcat" style="font-size:10px;margin-top:5px;display: <?php if($_POST['deal_cat_name']==$subCategoryName){?>block<?php } else {?> none <?php }?>;" id="subCatsForCat<?php echo $i?>">   
                    <?php foreach  ($subCats as $subCatName => $subSubCats) : ?>
<?php 
                        $origSubcatName = $subCatName;
                        $value = $subCatName;
                        if($subCatName == 'Pending') {
                            $subCatName = 'Pending & Completed';
                            $value = '';
                        }
                        if($subCatName == 'Completed') {
                            $subCatName = 'Completed only';
                            $value = 'Completed';
                        }
                        ?>
                    <input type="radio" id="deal_subCat_name<?php echo $j?>" name="deal_subcat1_name" value="<?php echo $value?>" onclick="subCategoryChanged(<?php echo $j?>)" <?php if($_POST['deal_subcat1_name']==$origSubcatName){?>checked<?php }?>/><label for="deal_subCat_name<?php echo $j?>"><?php echo $subCatName?></label>                    <?php $j++;endforeach;?> 
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
                  
                  <?php $k++; endforeach;?>            </td>
              </tr>
            </table>        
        </td>
        <td width="51%">
        <table width="100%" border="0" cellspacing="6" cellpadding="0">
          <tr>
            <td colspan="3">Refine Analysis</td>
          </tr>
          
          <tr>
            <td><select name="region" id="region" style="font-size: 10px; width: 200px;">
              <option value="">Any Region</option>
              <?php for($i=0;$i<$g_view['region_count'];$i++) :?>
              <option value="<?php echo $g_view['region_list'][$i]['name'];?>" <?php if($_POST['region']==$g_view['region_list'][$i]['name']){?>selected="selected"<?php }?>><?php echo $g_view['region_list'][$i]['name'];?></option>
              <?php endfor; ?>
            </select></td>
            <td>or</td>
            <td><select name="country" id="country" style="font-size: 10px; width: 200px;">
              <option value="">Any Country</option>
              <?php for($i=0;$i<$g_view['country_count'];$i++):?>
              <option value="<?php echo $g_view['country_list'][$i]['name'];?>" <?php if($_POST['country']==$g_view['country_list'][$i]['name']){?>selected="selected"<?php }?>><?php echo $g_view['country_list'][$i]['name'];?></option>
              <?php endfor?>
            </select></td>
          </tr>
          <tr>
            <td><select name="sector" id="sector" onChange="" style="width: 200px;">
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
				<?php
				/****************************
				sng:27/oct/2011
				We need filter by deal size like in league table
				
				sng:20/jan/2012
				Now we send id of deal size range
				*******************************/
				?>
				<select name="value_range_id" id="value_range_id" style="width: 200px;">
                <option value="">Refine by Size</option>
                <?php for($j=0;$j<$g_view['deal_size_filter_list_count'];$j++):?>
                    <option value="<?php echo $g_view['deal_size_filter_list'][$j]['value_range_id'];?>" <?php if($_POST['value_range_id']==$g_view['deal_size_filter_list'][$j]['value_range_id']){?>selected="selected"<?php } ?> ><?php echo $g_view['deal_size_filter_list'][$j]['display_text'];?></option>
                <?php  endfor; ?>
				<option value="0" <?php if($_POST['value_range_id']=="0"){?>selected="selected"<?php } ?>>undisclosed value</option>
                </select>
			
			</td>
            <td>or</td>
			
			<td>
			<?php
			/********************************
			sng:31/oct/2011
			client wants to show EITHER All/ Top10/25 or refine by size.
			Don�t want to allow people do All deals over $1bn and then search the top 10 of these
			So we have moved this on top as before
			
			We have added another dummy item 'size'. Selecting this causes preselection of an entry in value_range_id dropdown
			
			sng:1/nov/2011
			client wants filter by size dropdown and then the number of deals dropdown
			So, from UI point of view, we renamed the dummy 'size' option to '---'
			***************************************/
			?>
			<select name="number_of_deals" id="number_of_deals" style="width: 200px;">
                    <option value="" selected="selected">Show All</option>
                    <option value="top:10" <?php if($_POST['number_of_deals']=="top:10"){?>selected="selected"<?php }?>>Top 10</option>
                    <option value="top:25" <?php if($_POST['number_of_deals']=="top:25"){?>selected="selected"<?php }?>>Top 25</option>
                    <option value="recent:10" <?php if($_POST['number_of_deals']=="recent:10"){?>selected="selected"<?php }?>>Recent 10</option>
                    <option value="recent:25" <?php if($_POST['number_of_deals']=="recent:25"){?>selected="selected"<?php }?>>Recent 25</option>
					<option value="size" <?php if($_POST['number_of_deals']=="size"){?>selected="selected"<?php }?>>---</option>
                </select>
			</td>
			</tr>
			
		  <tr>
		  <td colspan="3" style="text-align:center;">
		  		<select name="year" id="year" style="width: 200px;">
              <option value="" <?php if(empty($_POST['year'])) echo 'selected="selected"'?>>Refine by Year</option>
              <?php $curr_year = date("Y");
                        for($predate=2;$predate>0;$predate--):?>
              <option value="<?php echo $curr_year-$predate;?>" <?php if( !empty($_POST['year']) && $_POST['year']==$curr_year-$predate){?>selected="selected"<?php }?>><?php echo $curr_year-$predate;?> A</option>
              <?php endfor;?>
              <option value="<?php echo $curr_year;?>" <?php if(!empty($_POST['year']) && $_POST['year']==$curr_year){?>selected="selected"<?php }?>><?php echo $curr_year;?> YTD</option>
              <option value="<?php echo $curr_year-1;?>-<?php echo $curr_year;?>" <?php if(!empty($_POST['year']) && $_POST['year']==sprintf('%s-%s',$curr_year-1,$curr_year)){?>selected="selected"<?php }?>><?php echo $curr_year-1;?>-<?php echo $curr_year;?> YTD</option>
            </select>
		  </td>
		  </tr>
        </table></td>
  </tr>
      <tr>
        <td colspan="2" align="center" style="text-align: right">
        <div class="orange" > 
         </div> 
        </td>
      </tr>
      <tr>
        <td colspan="2" style="text-align: right">
				<input type="submit" id="download_to_excel" class="btn_auto" value="Download to Excel" onclick="download_searched_deals()" />
                 <?php
				 /****************
				 sng:10/nov/2011
				 We allow any user to download deals
				 ********************/
                require_once("classes/class.account.php");
                if($g_account->is_site_member_logged()){
                    $alert = (isset($_REQUEST['action']) && $_REQUEST['action'] == "addAlert") ? "1" : "0";
                    $label = (isset($_REQUEST['action']) && $_REQUEST['action'] == "addAlert") ? "alert" : "search";
					
					?>
						&nbsp;&nbsp;&nbsp;
						<?php if (isset($_GET['token'])) :
                                     if ($savedSearches->searchBelongsToTheCurrentUser(base64_decode($_GET['token']))) : ?>                                                 
                                     <input type="button" class="btn_auto" value="Update <?php echo ucfirst($label) ?>" onclick="return updateSearch(<?php echo base64_decode($_GET['token']) ?>,<?php echo $alert?>)" />&nbsp;&nbsp;&nbsp;
                               <?php elseif($savedSearches->searchCanBeImported(base64_decode($_GET['token']))) : ?>
                                    <input name="importSearch" type="button" class="btn_auto" id="importSearch" value="Import Search" onclick="return saveSearch()"/>
                                <?php endif ?>
                             <?php else : ?>
							 	<?php
								/****************************
								sng:12/nov/2011
								if user specifically wants an alert, we do not show [save search] [save alert], otherwise
								we show both the buttons
								*********************************/
								if(!$alert){
									/*************
									this means Save Search, so we show Save Alert, passing the flag in the javascript
									***********/
									?>
									<input type="button" class="btn_auto" value="Save  Alert" onclick="return saveSearch(1)" />&nbsp;&nbsp;&nbsp;
									<?php
								}
								?>
                                <input type="button" class="btn_auto" value="Save  <?php echo ucfirst($label) ?>" onclick="return saveSearch(<?php echo $alert ?>)" />&nbsp;&nbsp;&nbsp;
                             <?php endif ?>
						<?php
					
                }else{
					?>
					<input type="button" class="btn_auto" value="Login to save the search" onclick="goto_login()" />
					<?php
				}
                ?>
				
                <input name="submit" type="submit" class="btn_auto" id="button" value="SEARCH" onclick="search_for_deals()" />
        </td>
      </tr>
    </table>
</form>