<?php
/*************
sng:18/sep/2012

sng: 14/jan/2013
Since there are other bankers and lawyers shown here, this is a good place to show the admire/recommend links.
Of course, we just put the 'like' button. If it is a colleague, it is added to my recommendation list, if it a member of another firm
it is added to my admire list.

However, I must be logged in to see this

There are certain points here
1) Banker can only admire/recommend banker, lawyer can only admire/recommend lawyer.
So, if I am a banker, the column will appear only in the banker section etc.

2) If I already admire or has recommended the member, the link will not appear. Just a text admire/recommend will appear
So, we just get all the recommend / admire list of this member in an array and check against that

3) Clicking the link sends ajax request. The code checks for duplicate.

4) Upon completion, a status message is shown - X is now in your admiration / recommended list.
**************/
require_once("../include/global.php");
require_once("classes/class.account.php");
require_once("classes/class.transaction_member.php");

$trans_mem = new transaction_member();

$g_view['deal_id'] = (int)$_GET['deal_id'];

$g_view['deal_bankers'] = NULL;
$g_view['deal_bankers_count'] = 0;
$ok = $trans_mem->get_all_deal_members_by_type($g_view['deal_id'],'banker',$g_view['deal_bankers'],$g_view['deal_bankers_count']);
if(!$ok){
	?>
	<p>Error while fetching records</p>
	<?php
	return;
}

$g_view['deal_lawyers'] = NULL;
$g_view['deal_lawyers_count'] = 0;
/********************
sng:3/apr/2013
the type is lawyer not lawyers
****************/
$ok = $trans_mem->get_all_deal_members_by_type($g_view['deal_id'],'lawyer',$g_view['deal_lawyers'],$g_view['deal_lawyers_count']);
if(!$ok){
	?>
	<p>Error while fetching records</p>
	<?php
	return;
}

/***************
sng:14/jan/2013
Need the recommend/admire list for this member, assuming that he/she is logged in
****************/
require_once("classes/class.member.php");
$recommed_admire_list = array();
$recommed_admire_count = 0;
if($g_account->is_site_member_logged()){
	$ok = $g_mem->front_get_recommended_admired_id_list($_SESSION['mem_id'],$recommed_admire_list,$recommed_admire_count);
	if(!$ok){
		?>
		<p>Error while fetching recommend / admire list</p>
		<?php
		return;
	}
}
?>
<h2>Bankers involved in this deal</h2>
<table cellpadding="0" cellspacing="0" class="company" style="width:100%;">
<tr>
<th>Name</th>
<th>Title</th>
<th>Firm</th>
<th>Credit ($m)</th>
<th>Adjusted Credit for the member ($m)</th>
<?php
/************
sng: 14/jan/2013
The admire/recommend part
****************/
if($g_account->is_site_member_logged()){
	if($_SESSION['member_type']=="banker"){
		?>
		<th>Admire / Recommend</th>
		<?php
	}
}
?>
</tr>
<?php
if(0==$g_view['deal_bankers_count']){
?>
<tr><td colspan="6">None found</td></tr>
<?php
}else{
	for($i=0;$i<$g_view['deal_bankers_count'];$i++){
		?>
		<tr>
		<td><a href="profile.php?mem_id=<?php echo $g_view['deal_bankers'][$i]['member_id'];?>"><?php echo $g_view['deal_bankers'][$i]['f_name']." ".$g_view['deal_bankers'][$i]['l_name'];?></a></td>
		<td><?php echo $g_view['deal_bankers'][$i]['designation'];?></td>
		<td><?php echo $g_view['deal_bankers'][$i]['firm_name'];?></td>
		<td><?php echo convert_billion_to_million_for_display($g_view['deal_bankers'][$i]['value_in_billion']);?></td>
		<td><?php echo convert_billion_to_million_for_display($g_view['deal_bankers'][$i]['adjusted_value_in_billion']);?></td>
		<?php
		/************
		sng: 14/jan/2013
		The admire/recommend part
		we check if this member is already in the list or not
		
		sng: 15/jan/2013
		We must also check that I am not liking myself
		****************/
		if($g_account->is_site_member_logged()){
			if($_SESSION['member_type']=="banker"){
				?>
				<td>
				<?php
				if(in_array($g_view['deal_bankers'][$i]['member_id'],$recommed_admire_list)){
					?>Already admired/recommended<?php
				}elseif($g_view['deal_bankers'][$i]['member_id']!=$_SESSION['mem_id']){
					?><input onclick="return recommend_admire_member(<?php echo $g_view['deal_bankers'][$i]['member_id'];?>);" class="btn_auto" type="button" value="LIKE" /><?php
				}else{
					//This is my record
				}
				?>
				</td>
				<?php
			}
		}
		?>
		</tr>
		<?php
	}
}
?>
</table>

<h2>Lawyers involved in this deal</h2>
<table cellpadding="0" cellspacing="0" class="company" style="width:100%;">
<tr>
<th>Name</th>
<th>Title</th>
<th>Firm</th>
<th>Credit ($m)</th>
<th>Adjusted Credit for the member ($m)</th>
<?php
/************
sng: 14/jan/2013
The admire/recommend part
****************/
if($g_account->is_site_member_logged()){
	if($_SESSION['member_type']=="lawyer"){
		?>
		<th>Admire / Recommend</th>
		<?php
	}
}
?>
</tr>
<?php
if(0==$g_view['deal_lawyers_count']){
?>
<tr><td colspan="6">None found</td></tr>
<?php
}else{
	for($i=0;$i<$g_view['deal_lawyers_count'];$i++){
		?>
		<tr>
		<td><a href="profile.php?mem_id=<?php echo $g_view['deal_lawyers'][$i]['member_id'];?>"><?php echo $g_view['deal_lawyers'][$i]['f_name']." ".$g_view['deal_lawyers'][$i]['l_name'];?></a></td>
		<td><?php echo $g_view['deal_lawyers'][$i]['designation'];?></td>
		<td><?php echo $g_view['deal_lawyers'][$i]['firm_name'];?></td>
		<td><?php echo convert_billion_to_million_for_display($g_view['deal_lawyers'][$i]['value_in_billion']);?></td>
		<td><?php echo convert_billion_to_million_for_display($g_view['deal_lawyers'][$i]['adjusted_value_in_billion']);?></td>
		<?php
		/************
		sng: 14/jan/2013
		The admire/recommend part
		we check if this member is already in the list or not
		
		sng: 15/jan/2013
		We must also check that I am not liking myself
		****************/
		if($g_account->is_site_member_logged()){
			if($_SESSION['member_type']=="lawyer"){
				?>
				<td>
				<?php
				if(in_array($g_view['deal_lawyers'][$i]['member_id'],$recommed_admire_list)){
					?>Already admired/recommended<?php
				}elseif($g_view['deal_lawyers'][$i]['member_id']!=$_SESSION['mem_id']){
					?><input onclick="return recommend_admire_member(<?php echo $g_view['deal_lawyers'][$i]['member_id'];?>);" class="btn_auto" type="button" value="LIKE" /><?php
				}else{
					//This is my record
				}
				?>
				</td>
				<?php
			}
		}
		?>
		</tr>
		<?php
	}
}
?>
</table>
<?php
/*************
sng:14/jan/2013
Need an area to show result of admire/recommend
**************/
?>
<div class="msg_txt" id="admire_recommend_result"></div>

<?php
/*******************
sng:19/sep/2012
In previous workflow, we added ourself to the team of specific bank/lawfirm.
Here we are just adding to the deal. We assume that we want to add ourself as a team member of our current firm. Of course, it can happen that
our current firm is not associated with the deal.
Also, we will not be able to add ourself as a team member of a firm where I used to work and that firm is associated with the deal.

sng:16/jan/2013
If I am added myself to the deal, I should not see the button
**********************/
?>
<div style="text-align:center; margin-top:10px;">
<?php
if($g_account->is_site_member_logged()){
	if(!is_member_in_deal($_SESSION['mem_id'])){
		?><input onclick="return add_self_to_deal(<?php echo $g_view['deal_id'];?>,<?php echo $_SESSION['company_id'];?>);" class="btn_auto" type="button" value="ADD YOURSELF TO THIS DEAL" /><?php
	}else{
		//already in team
	}
}else{
?>
<p>You need to login to add yourself to deal</p>
<?php
}
?>
<div class="msg_txt" id="add_self_to_deal_result"></div>
</div>

<?php
/******************************
sng:22/sep/2012
Add a colleague to deal
We send the work email as hidden value because that is unique
***********************/
if($g_account->is_site_member_logged()){
?>
<div>
<input type="hidden" id="team_member_id" value="" />
<p><strong>Add a colleague to the team</strong></p>
<p><input type="text" name="team_member_name" id="team_member_name" class="txtbox" /></p>
<p><input type="button" value="Add" class="btn_auto" onclick="add_colleague_to_deal(<?php echo $g_view['deal_id'];?>,<?php echo $_SESSION['company_id'];?>)" /></p>
</div>
<div>Type the first few letters. If the member is found, it will be shown in the list. Please select the member you wish to add to the team.</div>
<?php
}
?>
<div class="msg_txt" id="add_colleague_to_deal_result"></div>


<script>
$('.btn_auto').button();

jQuery('#team_member_name').devbridge_autocomplete({
	serviceUrl:'ajax/fetch_colleague_list.php',
	minChars:1,
	noCache: true,
	width:'100%',
	onSelect: function(value, data){
		var tokens = data.split("|");
		$('#team_member_id').val(tokens[1]);
		jQuery('#team_member_name').val(tokens[0]);
	}
});
</script>
<?php
/****************
sng:16/jan/2013
We need a way to see whether a particular member is already in the deal team or not.
For now, we do not write any method in the member class. We just check the list here.
If the list is empty, then of course, we assume that the member is not in the deal
******************/
function is_member_in_deal($mem_id){
	global $g_view;
	
	for($i=0;$i<$g_view['deal_bankers_count'];$i++){
		if($g_view['deal_bankers'][$i]['member_id']==$mem_id){
			return true;
		}
	}
	for($i=0;$i<$g_view['deal_lawyers_count'];$i++){
		if($g_view['deal_lawyers'][$i]['member_id']==$mem_id){
			return true;
		}
	}
	//not found in any list so
	return false;
}