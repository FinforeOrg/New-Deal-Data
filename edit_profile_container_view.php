<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
<td colspan="3" style="text-align:right"><h3><?php echo $g_view['edit_heading'];?></h3></td>
</tr>
<tr>
<td style="width:250px;">
<!--left menu-->
<table width="250px" cellpadding="5" cellspacing="5" style="border-right:1px solid #CCCCCC;">
<?php
/************************************
sng:19/feb/2011
We put far less menu

sng:5/apr/2011
The items are same for company rep and data partner
**********************/
?>
<tr><td><a href="edit_my_profile.php">Profile</a></td></tr>
<tr><td><a href="edit_profile_works.php">Previous works</a></td></tr>
<?php
if(($_SESSION['member_type']!="company rep")&&($_SESSION['member_type']!="data partner")){
/*************************
sng:12/nov/2011
Since we are not allowing any member to add himself/herself to deal, there is no point in showing this
<tr><td><a href="edit_profile_my_deals.php">My deals</a></td></tr>
*******************************/
}
?>
<?php
if(($_SESSION['member_type']!="company rep")&&($_SESSION['member_type']!="data partner")){
?>
<tr><td><a href="edit_profile_delegates.php">Delegates</a></td></tr>
<?php
}
?>
<?php
if($_SESSION['is_delegate']){
?>
<tr><td><a href="edit_profile_delegates_for.php">Delegates For</a></td></tr>
<?php
}
?>
<?php
if(($_SESSION['member_type']!="company rep")&&($_SESSION['member_type']!="data partner")){
/***************************************
sng:12/nov/2011
Let us hide the recommend/admire section in data-cx since there is no way for a member to see profile of another member

<tr><td><a href="edit_profile_recommended_admired_by_me.php">Members I recommend / admire</a></td></tr>
<tr><td><a href="edit_profile_recommend_admire_me.php">Members recommend / admire me</a></td></tr>
*****************************************/
}else{
/********************************************
sng:20/sep/2011
From now on, only admin can change company description. It cannot be changed from front end
?>
<tr><td><a href="edit_company_desc.php">Company description</a></td></tr>
<?php
***********************************************/
}
/*****************************************************/
?>
</table>
</td>
<td style="padding-left:20px;">
<!--left menu-->
<!--main area-->
<?php
if($g_view['edit_view']!="") include($g_view['edit_view']);
?>
<!--main area-->
</td>
<td style="width:10px">&nbsp;</td>
</tr>
</table>