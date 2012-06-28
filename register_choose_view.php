<?php
/***************
sng:29/sep/2011
we have already included jquery in the container view
<script type="text/javascript" src="js/jquery-1.2.1.pack.js"></script>
********************************/
?>
<script type="text/javascript">
function goto_registerClassic() {
    window.location="register.php";
}

function goto_registerLinkedIn() {
    window.location = "linkedIn/oauth_test.php";
}
</script>

<table cellspacing="0" cellpadding="0" border="0" width="100%">
<tbody>
<tr>
<td style="text-align: center;">
<input type="button" onclick="goto_registerClassic()" value="Register Directly" class="btn_auto">&nbsp;&nbsp;<input type="button" onclick="goto_registerLinkedIn()" value="Register via LinkedIn" class="btn_auto"></td>
</tr>
</tbody></table>
