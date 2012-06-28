<?php
/*****
sng: Note: this code is used in a page that also require prototype.js, so there is a no conflict jQuery
imihai 2011-01-06: Removed prototype completly from code. Caused issues with jquery plugins

sng:19/jul/2010
Logged in user can filter via the industry also. This means, when sector is changed, industry has to be updated.
We use the ajax to update.
The problem is, when the user is not logged, the industry drop down is not shown and there is no need for ajax update.
What we do is, use if clause to define the body of the ajax function
******/
?>

<script type="text/javascript">

function deal_cat_changed(){
	var type_obj = document.getElementById('deal_cat_name');
	var offset_selected = type_obj.selectedIndex;
	if(offset_selected != 0){
		var deal_cat_name_selected = type_obj.options[offset_selected].value;
		//fetch the list of deal sub categories
		jQuery.post("admin/ajax/deal_subtype1_list.php", {deal_cat_name: ""+deal_cat_name_selected+""}, function(data){
				if(data.length >0) {
					jQuery('#deal_subcat1_name').html(data);
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
		jQuery.post("admin/ajax/deal_subtype2_list.php", {deal_cat_name: ""+deal_cat_name_selected+"",deal_subcat_name: ""+deal_subcat_name_selected+""}, function(data){
			//alert(data);
				if(data.length >0) {
					jQuery('#deal_subcat2_name').html(data);
				}
		});
	}
}
</script>
<script type="text/javascript">
<?php
if($g_account->is_site_member_logged()){
?>
function sector_changed(){
	var sector_obj = document.getElementById('sector');
	var offset_selected = sector_obj.selectedIndex;
	if(offset_selected != 0){
		var sector_selected = sector_obj.options[offset_selected].value;
		//fetch the list of industries
		jQuery.post("admin/ajax/industry_list_for_sector.php", {sector: ""+sector_selected+""}, function(data){
				if(data.length >0) {
					jQuery('#industry').html(data);
				}
		});
	}
}
<?php
}else{
?>
function sector_changed(){
}
<?php
}
?>

</script>