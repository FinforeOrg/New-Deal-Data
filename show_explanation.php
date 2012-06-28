<?php
/*************
The pages that show explanation has a checkbox that hides the explanation in that page.
when the visitor visits that page again, the explanation in that page must remain hidden
To do this, a cookie with value n is set in browser. The name of the cookie is in $g_view['explanation_page']

That means, use $g_view['explanation'] so that the view pages can decide whether to show the explanation or not
*********/
if(isset($_COOKIE[$g_view['explanation_page']])&&($_COOKIE[$g_view['explanation_page']]=='n')){
	$g_view['explanation'] = false;
}else{
	$g_view['explanation'] = true;
}
?>
