var left_menu = {};

left_menu.last_item = null;

left_menu.section_toggled = function(section_id,section_obj){
	if(this.last_item!=null){
		//check if we are toggling the same item or not
		if(this.last_item != section_obj){
			this.last_item.hide();
		}
	}
	//updae the variable to hold the curent one
	jQuery.cookie("VerColMenu",section_id);
	this.last_item = section_obj;
}

left_menu.init = function(){
	jQuery('#VerColMenu ul').hide();
	jQuery('#VerColMenu > li > a').click(function(myevent) {
		//prevent the browser from following the link
		//make sure you do this for the direct menu item. If you just write
		//'#VerColMenu li a', jQuery applies the rule for the inner menu items. That means
		//the menu box opens but the links does not work
		myevent.preventDefault();
		var open_section_obj = jQuery(this).next();
		open_section_obj.slideToggle('normal');
		//store the reference to the item you are toggling
		//so that when you click on another section, th eprev section could be hidden
		left_menu.section_toggled(jQuery(this).attr('id'),open_section_obj);
	});
	var opened_section_id = jQuery.cookie("VerColMenu");
	if(opened_section_id != ""){
		//open it
		var open_section_obj = jQuery('#'+opened_section_id).next();
		open_section_obj.slideToggle('normal');
		left_menu.section_toggled(opened_section_id,open_section_obj);
	}
}
jQuery(document).ready(function() {
	// Collapse everything but the first menu:
	//$("#VerColMenu > li > a").not(":first").find("+ ul").slideUp(1);
	// Expand or collapse:
	//$("#VerColMenu > li > a").click(function() {
	//	$(this).find("+ ul").slideToggle("fast");
	//});
	left_menu.init();
});