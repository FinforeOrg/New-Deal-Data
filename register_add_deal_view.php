<?php
/***
sng:27/july/2010
if the registration is favoured, the activation email has been sent
We show message accordingly
*******/
if($g_view['is_favoured']){
?>
<p>
The deals are added to your request. An activation email has been sent to your work email. If you activate your account, you will be added as team member for those deals.
</p>
<?php
}else{
?>
<p>
The deals are added to your request. If your membership request is accepted and you activate your account, you will be added as team member for those deals.
</p>
<?php
}
?>
