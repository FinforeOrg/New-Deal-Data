// JavaScript Document
/*****************
sng:23/feb/2012
The entire preferred logo handling is changed. Now we store the logo filename instead of ordinal number.
Why? because now we have multiple companies and logos instead of logos for a deal
********************/
function updateChosenLogos(deal_id,filename) {
	/***************
	sng:24/feb/2012
	We need to support 'download to powerpoint. For that, the tombstone generation code has
	sent hidden input fields with deal id and filename. However, should user cycle to
	another logo, that field has to be updated.
	see get_tombstone_from_deal_data
	**********************/
	$('#thumb-'+deal_id).val(filename);
    jQuery.get(
        'ajax/save_chosen_logo.php?deal_id='+deal_id+'&logo_file='+filename,
        function (data) {
        }
  )  
}

  
function showNext(id) {
    activeLogoId =  $("#logo-"+id+" img:visible").attr('id');
    c = activeLogoId.match(/logo-\d+-(\d+)/);
    currentId = parseInt(c[1]);
    /* Test if we have a next picture */
    next = $("#logo-"+id+"-" + (currentId + 1) );
    if (next.length == 0) {
        return false;
    } else {
         $("#logo-"+id+" img:visible").css('display','none');
         next.css('display','block');
         /************
		 sng:23/feb/2012
		 use the ordinal number to get the name of the next logo
		 ****************/
         updateChosenLogos(id,jQuery("#logo-"+id+"-"+(currentId+1)).attr('name'));
    }
}
function showPrevious(id) {
    activeLogoId =  $("#logo-"+id+" img:visible").attr('id');
    c = activeLogoId.match(/logo-\d+-(\d+)/);
    currentId = parseInt(c[1]);
    /* Test if we have a previous picture */
    prev = $("#logo-"+id+"-" + (currentId - 1) );
    if (prev.length == 0) {
        return false;
    } else {
         $("#logo-"+id+" img:visible").css('display','none');
         prev.css('display','block');
         updateChosenLogos(id,jQuery("#logo-"+id+"-"+(currentId-1)).attr('name'));
    }

}