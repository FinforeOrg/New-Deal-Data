<?php
echo $g_view['page_content'];
/*************
sng:30/sep/2011
trap mailto links and use the in page contact us popup to send send email.
The code set the subject line and the send_contact_to field
********************/
?>
<script>
$(function(){
	var mail_link_obj = $('a[href^="mailto:"]');
	//var mail_link_obj = $('a.mailpop');
	mail_link_obj.each(function(index){
		var mail_link_attr = jQuery(this).attr('href');
		var match = /mailto:([^?]+)\?subject=(.+)/i.exec(mail_link_attr);
		var email = match[1];
		var subject = match[2];
		jQuery(this).bind('click',{email: email,subject: subject},function(event){
			send_mail(event.data.email,event.data.subject);
			return false;
		});
	});
});
function send_mail(email,subject){
	jQuery('#contact_subject').val(subject);
	jQuery('#send_contact_to').val(email);
	center_contact_Popup();
	load_contact_Popup();
}
</script>