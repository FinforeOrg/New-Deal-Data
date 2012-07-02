// JavaScript Document
jQuery(document).ready(function () {
     
    jQuery('#nav li').hover(
        function () {
            //show its submenu
            jQuery('ul', this).slideDown(100);
 
        },
        function () {
            //hide its submenu
            jQuery('ul', this).slideUp(100);        
        }
    );
     
});