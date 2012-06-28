<?php
/****************
sng:29/sep/2011
we now include jquery in the container view
<script type="text/javascript" src="js/jquery-1.2.1.pack.js"></script>
************************/
?>
<script type="text/javascript">
function goto_registerLinkedIn() {
    window.location = "linkedIn/oauth_test.php";
}
function membership_type_changed(){
	var type_obj = document.getElementById('membership_type');
	var offset_selected = type_obj.selectedIndex;
	if(offset_selected != 0){
		var type_selected = type_obj.options[offset_selected].value;
		//fetch the list of designations
		$.post("ajax/designation_list.php", {membership_type: ""+type_selected+""}, function(data){
				if(data.length >0) {
					$('#designation').html(data);
				}
		});
	}
}
function lookup(inputString) {
		if(inputString.length == 0) {
			// Hide the suggestion box.
			$('#suggestions').hide();
		} else {
			// post data to our php processing page and if there is a return greater than zero
			// show the suggestions box
			//get the type of member
			var type_obj = document.getElementById('membership_type');
			var offset_selected = type_obj.selectedIndex;
			if(offset_selected == 0){
				alert("Please select membership type first");
				return;
			}
			var type_selected = type_obj.options[offset_selected].value;
			$('#firm_name_searching').html("searching...");
			$.post("ajax/company_list.php", {search_string: ""+inputString+"",type: ""+type_selected+""}, function(data){
				$('#firm_name_searching').html("");
				if(data.length >0) {
					$('#suggestions').show();
					$('#autoSuggestionsList').html(data);
				}else{
					//no matches found, we hide the suggestion list
					setTimeout("$('#suggestions').hide();", 200);
				}
			});
		}
	} //end
	
	// if user clicks a suggestion, fill the text box.
	function fill(thisValue) {
		$('#firm_name').val(thisValue);
		setTimeout("$('#suggestions').hide();", 200);
	}
</script>
<?php
/****************************
sng:19/dec/2011
We need to reload captcha via ajax else we lose the input data
*****************************/
?>
<script>
function reload_captcha(){
	var captcha_timestamp = new Date().getTime();
	$('#security_code_img').attr('src', 'CaptchaSecurityImages.php?width=100&height=40&characters=5&stamp='+captcha_timestamp);
}
</script>
<style type="text/css">
.sectiongap{
height:10px;
}
</style>

<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
<td><h1>Member Registration</h1></td>
<td style="text-align:right;"><input type="button" onclick="goto_registerLinkedIn()" value="Register via LinkedIn" class="btn_auto"></td>
</tr>
</table>
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="register" style="width: 500px; margin: 0 auto;">
              <tr>
                <td><table width="100%" border="0" cellpadding="0" cellspacing="0" class="registerinner">
                  <tr>
                    <td><table width="100%" border="0" cellpadding="0" cellspacing="0" class="registercontent">
                      <tr>
                        <th>Profile Data</th>
                      </tr>
                      <tr>
                        <td><span class="msg_txt"><?php echo $g_view['msg'];?></span></td>
                      </tr>
                      <tr>
                        <td>
						<div>The inputs marked as <span class="err_txt">*</span> are mandatory</div>
						<form method="post" action="" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="add"/>
						 
						<table width="100%" border="0" cellspacing="0" cellpadding="0" style="width: auto;">
							  <tr>
								<td>First Name :</td>
								<td><img src="images/spacer.gif" width="50" height="1" alt="" /></td>
								<td><input name="first_name" type="text" class="txtbox" value="<?php echo $g_view['input']['first_name'];?>"/><span class="err_txt">&nbsp;*</span><br />
									<span class="err_txt"><?php echo $g_view['err']['first_name'];?></span>								
								</td>
							  </tr>
							  <tr>
								<td colspan="3"><img src="images/spacer.gif" width="1" height="7" alt="" /></td>
								</tr>
							  <tr>
								<td>Last Name :</td>
								<td>&nbsp;</td>
								<td><input name="last_name" type="text" class="txtbox" value="<?php echo $g_view['input']['last_name'];?>"/><span class="err_txt">&nbsp;*</span><br />
									<span class="err_txt"><?php echo $g_view['err']['last_name'];?></span>								</td>
							  </tr>
							  <tr>
								<td colspan="3" class="sectiongap">&nbsp;</td>
								</tr>
								
								<tr>
								<td>Work email (for login) :</td>
								<td>&nbsp;</td>
								<td><input name="work_email" type="text" class="txtbox" value="<?php echo $g_view['input']['work_email'];?>"/><span class="err_txt">&nbsp;*</span><br />
									<span class="err_txt"><?php echo $g_view['err']['work_email'];?></span>								</td>
							  </tr>
							  <tr>
								<td colspan="3"><img src="images/spacer.gif" width="1" height="7" alt="" /></td>
								</tr>
								
							  <tr>
								<td>Password :</td>
								<td>&nbsp;</td>
								<td><input name="password" type="password" class="txtbox" value="<?php echo $g_view['input']['password'];?>"/><span class="err_txt">&nbsp;*</span><br />
									<span class="err_txt"><?php echo $g_view['err']['password'];?></span>								</td>
							  </tr>
							  <tr>
								<td colspan="3"><img src="images/spacer.gif" width="1" height="7" alt="" /></td>
								</tr>
							  <tr>
								<td>Retype Password :</td>
								<td>&nbsp;</td>
								<td><input name="re_password" type="password" class="txtbox" value="<?php echo $g_view['input']['re_password'];?>"/><span class="err_txt">&nbsp;*</span><br />
									<span class="err_txt"><?php echo $g_view['err']['re_password'];?></span>								</td>
							  </tr>
							  <tr>
								<td colspan="3" class="sectiongap">&nbsp;</td>
								</tr>
							  <tr>
								<td>Membership Type :</td>
								<td>&nbsp;</td>
								<td>
									<select name="type" id="membership_type" class="txtbox" onchange="membership_type_changed();">
									  <option value="">Select</option>
									  <option value="banker" <?php if($g_view['input']['type']=="banker"){ ?> selected="selected"<?php }?>>Banker</option>
									  <option value="lawyer" <?php if($g_view['input']['type']=="lawyer"){ ?> selected="selected"<?php }?>>Lawyer</option>
									  <option value="company rep" <?php if($g_view['input']['type']=="company rep"){ ?> selected="selected"<?php }?>>Company Rep</option>
									  <?php
									  /*************************
									  sng:5/apr/2011
									  added new role
									  ***********************/
									  ?>
									  <option value="data partner" <?php if($g_view['input']['type']=="data partner"){ ?> selected="selected"<?php }?>>Financial Journalist / Data Provider</option>
									</select><span class="err_txt">&nbsp;*</span><br />
											 <span class="err_txt"><?php echo $g_view['err']['type'];?></span>								</td>
							  </tr>
							  <tr>
								<td colspan="3"><img src="images/spacer.gif" width="1" height="7" alt="" /></td>
								</tr>
							  
							  
								
								<tr>
								<td>Company :</td>
								<td>&nbsp;</td>
								<td><input name="firm_name" id="firm_name" onkeyup="lookup(this.value);" onblur="fill();" type="text" class="txtbox" value="<?php echo $g_view['input']['firm_name'];?>" autocomplete="off"/><span class="err_txt">&nbsp;*</span><br />
								<span class="err_txt"><?php echo $g_view['err']['firm_name'];?></span>
								<div id="firm_name_searching"></div>
								<div class="suggestionsBox" id="suggestions" style="display: none;">
								<img src="images/upArrow.png" style="position: relative; top: -18px; left: 30px;" alt="upArrow"  />
								<div class="suggestionList" id="autoSuggestionsList"></div>
								</div>
								
								</td>
								</tr>
								
								<tr>
								<td colspan="3"><img src="images/spacer.gif" width="1" height="7" alt="" /></td>
								</tr>
								
								<tr>
								<td>Division</td>
								<td>&nbsp;</td>
								<td><input name="division" type="text" class="txtbox" value="<?php echo $g_view['input']['division'];?>"/><br />
									<span class="err_txt"><?php echo $g_view['err']['division'];?></span>								</td>
							  </tr>
							  
								<tr>
								<td colspan="3" class="sectiongap">&nbsp;</td>
								</tr>
							  <tr>
								<td>Designation :</td>
								<td>&nbsp;</td>
								<td>
								<select name="designation" id="designation" class="txtbox">
									   <option value="">Select</option>
									   
									   <?php
									   /***
									   sng:6/apr/2010
									   We now show a filtered designation list by default.
									   Initially, membeship type is not selected so this will be blank
									   *******/
										for($j=0;$j<$g_view['designation_count'];$j++){
											?>
											<option value="<?php echo $g_view['designation_list'][$j]['designation'];?>" <?php if($g_view['input']['designation']==$g_view['designation_list'][$j]['designation']){?>selected="selected"<?php }?>><?php echo $g_view['designation_list'][$j]['designation'];?></option>
											<?php
										}
										?>   
								   </select><br />
									<span class="err_txt"><?php echo $g_view['err']['designation'];?></span>								
									</td>
									
							  </tr>
							  
							  <tr>
								<td colspan="3"><img src="images/spacer.gif" width="1" height="7" alt="" /></td>
								</tr>
							  <tr>
								<td>Year Joined :</td>
								<td>&nbsp;</td>
								<td>
								<select name="join_date" class="txtbox">
									   <option value="">Select</option>
									   <?php
									   $curr_year = date("Y");
									   
										  for($year_past = 60;$year_past>=0;$year_past--)
										  {
										  ?>
											<option value="<?php echo $curr_year;?>" <?php if($g_view['input']['join_date']==$curr_year){?>selected="selected"<?php }?>><?php echo $curr_year;?></option>
											<?php
											$curr_year--;
										}
										?>
								   </select>
								    <br />
									<span class="err_txt"><?php echo $g_view['err']['join_date'];?></span>								
								</td>
							  </tr>
							  <tr>
								<td colspan="3"><img src="images/spacer.gif" width="1" height="7" alt="" /></td>
								</tr>
							  <tr>
								<td>Location :</td>
								<td>&nbsp;</td>
								<td>
									<select name="location" class="txtbox">
									   <option value="">Select</option>
									   <?php
										for($i=0;$i<$g_view['country_count'];$i++){
											?>
											<option value="<?php echo $g_view['country_list'][$i]['name'];?>" <?php if($g_view['input']['location']==$g_view['country_list'][$i]['name']){?>selected="selected"<?php }?>><?php echo $g_view['country_list'][$i]['name'];?></option>
											<?php
										}
										?>
								   </select><br />
									<span class="err_txt"><?php echo $g_view['err']['location'];?></span>								</td>
							  </tr>
							  
							  
							  <tr>
								<td colspan="3"><img src="images/spacer.gif" width="1" height="7" alt="" /></td>
								</tr>
							  
							  <tr>
								<td>Home email :</td>
								<td>&nbsp;</td>
								<td><input name="home_email" type="text" class="txtbox" value="<?php echo $g_view['input']['home_email'];?>"/><br />
									<span class="err_txt"><?php echo $g_view['err']['home_email'];?></span>								</td>
							  </tr>
							  
								
							  <tr><td colspan="3">&nbsp;</td></tr>
							  <?php
							  /****
							  sng:5/jun/2010
							  we now allow the user to add deals during registration
							  If the option is checked, the user is taken to the next page
							  We also show the list of deal categories
							  ***/
							  ?>
							  <!--/////////////////////////////////////////////////////////////
							  sng:13/jan/2011
							  We do not allow the user to specify deals during registration
							  <tr>
							  <td>&nbsp;</td>
							  <td>&nbsp;</td>
							  <td>
							  <input type="checkbox" name="add_deal" value="add_deal" />&nbsp;I want to add lastest deals to my profile (only for lawyers and bankers)<br />
							  <?php
							  for($i=0;$i<$g_view['cat_count'];$i++){
							  ?>
							  <input type="checkbox" name="deal_cat_name[]" value="<?php echo $g_view['cat_list'][$i]['type'];?>" />&nbsp;<?php echo $g_view['cat_list'][$i]['type'];?><br />
							  <?php
							  }
							  ?>
							  </td>
							  </tr>
							  ////////////////////////////////////////////////////////////////////////////-->
							  <tr><td colspan="3">&nbsp;</td></tr>
							  
							  <tr>
								<td colspan="3"><img src="images/spacer.gif" width="1" height="7" alt="" /></td>
								</tr>
							  
							   
							  <tr>
							  <td colspan="3">
							  <?php echo recaptcha_get_html($recaptcha_public_key);?>
							  </td>
							  </tr>
							  <tr>
								<td colspan="3"><img src="images/spacer.gif" width="1" height="7" alt="" /></td>
								</tr>
							  <tr><td colspan="3"><span class="err_txt"><?php echo $g_view['err']['security_code'];?></span></td></tr>
							  <tr>
								<td colspan="3"><img src="images/spacer.gif" width="1" height="7" alt="" /></td>
								</tr>
							  <tr>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td><input name="submit" type="submit" class="btn_auto" value="Submit" /></td>
							  </tr>
							</table>
				         </form>				  </td>
                      </tr>
                    </table></td>
                  </tr>
                </table></td>
              </tr>
            </table>