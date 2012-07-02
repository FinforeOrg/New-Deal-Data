<?php
/*****************
sng: 8/feb/2012
Used to fetch text boxes to add companies, with proper role preselected.
In fact, for the deal type, we see how many roles are marked as preselected and fetch that many boxes

since this is used by two codes, this containes the common part
********************/
require_once("classes/class.transaction_company.php");
$deal_comp = new transaction_company();

$g_view['roles'] = NULL;
$g_view['role_count'] = 0;
$ok = $deal_comp->get_all_deal_company_roles_for_deal_type($g_view['deal_type'],$g_view['roles'],$g_view['role_count']);
if(!$ok){
	echo "";
	exit;
}
$participant_i = 0;
for($i=0;$i<$g_view['role_count'];$i++){
	if($g_view['roles'][$i]['preset']=='y'){
		?>
		<div class="list-item2" >
		<input type="text" class="participant_company" name="companies[]" style="width: 100%;"><br />
		<select name="company_participant_role_<?php echo $participant_i;?>">
		<option value="">select role</option>
		<?php
		/**************
		now we show all roles
		*************/
		for($role_i=0;$role_i<$g_view['role_count'];$role_i++){
			?>
			<option value="<?php echo $g_view['roles'][$role_i]['role_id'];?>" <?php if($g_view['roles'][$role_i]['role_id']==$g_view['roles'][$i]['role_id']){?>selected="selected"<?php }?>><?php echo $g_view['roles'][$role_i]['role_name'];?></option>
			<?php
		}
		?>
		</select><br />
		<input type="text" name="company_participant_note_<?php echo $participant_i;?>" class="participant_footnote" style="width: 100%;">
		</div>
		<?php
		$participant_i++;
	}
}
?>
<script>
/***********
see suggest_a_deal_view.php. There this var is needed
***************/
_current_company_num = <?php echo $participant_i;?>;
role_count = <?php echo $g_view['role_count'];?>;
role_ids = new Array();
role_names = new Array();
<?php
for($role_i=0;$role_i<$g_view['role_count'];$role_i++){
	?>
	role_ids[<?php echo $role_i;?>]=<?php echo $g_view['roles'][$role_i]['role_id'];?>;
	role_names[<?php echo $role_i;?>]='<?php echo $g_view['roles'][$role_i]['role_name'];?>';
	<?php
}
?>
</script>