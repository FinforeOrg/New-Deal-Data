    //jQuery.noConflict();
	function lookup(inputString) {
		if(inputString.length == 0) {
			// Hide the suggestion box.
			jQuery('#suggestions').hide();
		} else {
			// post data to our php processing page and if there is a return greater than zero
			// show the suggestions box
			jQuery.post("string_search.php", {mysearchString: ""+inputString+""}, function(data){
				if(data.length >0) {
					jQuery('#suggestions').show();
					jQuery('#autoSuggestionsList').html(data);
				}
			});
		}
	} //end
	
	// if user clicks a suggestion, fill the text box.
	function fill(thisValue) {
		jQuery('#inputString').val(thisValue);
		setTimeout("jQuery('#suggestions').hide();", 200);
	}
    
    /*do not modify --- share popup  */
popupStatus = 0;    
function loadPopup(){
    //loads popup only if it is disabled
    if(popupStatus==0){
        jQuery("#backgroundPopup").css({
            "opacity": "0.7"
        });
        jQuery("#popupShare").css('z-index', 1005);
        jQuery("#backgroundPopup").fadeIn("slow");
        jQuery("#popupShare").fadeIn("slow");
        popupStatus = 1;
    }
}

//disabling popup with jQuery magic!
function disablePopup(){
    //disables popup only if it is enabled
    if(popupStatus==1){
        jQuery("#backgroundPopup").fadeOut("slow");
        jQuery("#popupShare").fadeOut("slow");
        popupStatus = 0;
    }
}

//centering popup
function centerPopup(){
    //request data for centering
    
    var windowWidth = document.documentElement.clientWidth;
    var windowHeight = document.body.clientHeight;
    var popupHeight = jQuery("#popupShare").height();
    var popupWidth = jQuery("#popupShare").width();
    //centering
    jQuery("#popupShare").css({
        "position": "absolute"
        //"top": 200,
        //"left": windowWidth/2-popupWidth/2
    });
    jQuery("#popupShare").center();
    //mv_windowTools.centerElementOnScreen(document.getElementById('popupShare'));  
    jQuery("#backgroundPopup").css({
        "height": windowHeight
    });
    
}

//CONTROLLING EVENTS IN jQuery
jQuery(document).ready(function(){
    
       
    //CLOSING POPUP
    //Click the x event!
    jQuery("#popupShareClose").click(function(){
        disablePopup();
    });
    //Click out event!
    jQuery("#backgroundPopup").click(function(){
        disablePopup();
    });
    //Press Escape event!
    jQuery(document).keypress(function(e){
        if(e.keyCode==27 && popupStatus==1){
            disablePopup();
        }
    });

});

  /* share popup ends here  */
  
