function hide_explanation(page_code){
	//document.getElementById('explanation').style.visibility="hidden";
	//document.getElementById('hide_explanation').style.visibility="hidden";
	//document.getElementById('cb_hide_explanation').style.visibility="hidden";
	//now we set a cookie so that we remember that we are not showing the explanation in the page
	//identified by page_code
	create_cookie(page_code,'n',365);
}
function create_cookie(name,value,days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";
	document.cookie = name+"="+value+expires+"; path=/";
}
