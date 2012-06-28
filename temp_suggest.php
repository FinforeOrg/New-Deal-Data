<script type="text/javascript" src="js/jquery-1.2.1.pack.js"></script>
<script type="text/javascript">
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
			$.post("ajax/company_list.php", {search_string: ""+inputString+"",type: ""+type_selected+""}, function(data){
				if(data.length >0) {
					$('#suggestions').show();
					$('#autoSuggestionsList').html(data);
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


<table width="100%" border="0" cellpadding="0" cellspacing="0" class="register" style="width: 450px; margin: 0 auto;">
              <tr>
                <td><table width="100%" border="0" cellpadding="0" cellspacing="0" class="registerinner">
                  <tr>
                    <td><table width="100%" border="0" cellpadding="0" cellspacing="0" class="registercontent">
                      <tr>
                        <th>Deal Data</th>
                      </tr>
                      <tr>
                        <td><span class="msg_txt"><?php echo $g_view['msg'];?></span></td>
                      </tr>
                      <tr>
                        <td>
						<form method="post" action="" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="add"/>
						 
						<table width="100%" border="0" cellspacing="0" cellpadding="0" style="width: auto;">
							  <tr>
								<td>First Name :</td>
								<td><img src="images/spacer.gif" width="50" height="1" alt="" /></td>
								<td><input name="first_name" type="text" class="txtbox" value="<?php echo $g_view['input']['first_name'];?>"/><br />
									<span class="err_txt"><?php echo $g_view['err']['first_name'];?></span>								
								</td>
							  </tr>
							  <tr>
								<td colspan="3"><img src="images/spacer.gif" width="1" height="7" alt="" /></td>
								</tr>
							  <tr>
								<td>Last Name :</td>
								<td>&nbsp;</td>
								<td><input name="last_name" type="text" class="txtbox" value="<?php echo $g_view['input']['last_name'];?>"/><br />
									<span class="err_txt"><?php echo $g_view['err']['last_name'];?></span>								</td>
							  </tr>
							  <tr>
								<td colspan="3"><img src="images/spacer.gif" width="1" height="7" alt="" /></td>
								</tr>
							  <tr>
								<td>Password :</td>
								<td>&nbsp;</td>
								<td><input name="password" type="password" class="txtbox" value="<?php echo $g_view['input']['password'];?>"/><br />
									<span class="err_txt"><?php echo $g_view['err']['password'];?></span>								</td>
							  </tr>
							  <tr>
								<td colspan="3"><img src="images/spacer.gif" width="1" height="7" alt="" /></td>
								</tr>
							  <tr>
								<td>Retype Password :</td>
								<td>&nbsp;</td>
								<td><input name="re_password" type="password" class="txtbox" value="<?php echo $g_view['input']['re_password'];?>"/><br />
									<span class="err_txt"><?php echo $g_view['err']['re_password'];?></span>								</td>
							  </tr>
							  <tr>
								<td colspan="3"><img src="images/spacer.gif" width="1" height="7" alt="" /></td>
								</tr>
							  <tr>
								<td>Membership Type :</td>
								<td>&nbsp;</td>
								<td>
									<select name="type" id="membership_type" class="txtbox" onchange="membership_type_changed();">
									  <option value="">Select</option>
									  <option value="banker" <? if($g_view['input']['type']=="banker"){ ?> selected="selected"<? }?>>Banker</option>
									  <option value="lawyer" <? if($g_view['input']['type']=="lawyer"){ ?> selected="selected"<? }?>>Lawyer</option>
									  <option value="company rep" <? if($g_view['input']['type']=="company rep"){ ?> selected="selected"<? }?>>Company Rep</option>
									</select><br />
											 <span class="err_txt"><?php echo $g_view['err']['type'];?></span>								</td>
							  </tr>
							  <tr>
								<td colspan="3"><img src="images/spacer.gif" width="1" height="7" alt="" /></td>
								</tr>
							  <tr>
								<td>Home e-mail :</td>
								<td>&nbsp;</td>
								<td><input name="home_email" type="text" class="txtbox" value="<?php echo $g_view['input']['home_email'];?>"/><br />
									<span class="err_txt"><?php echo $g_view['err']['home_email'];?></span>								</td>
							  </tr>
							  <tr>
								<td colspan="3"><img src="images/spacer.gif" width="1" height="7" alt="" /></td>
								</tr>
							  <tr>
								<td>Work e-mail :</td>
								<td>&nbsp;</td>
								<td><input name="work_email" type="text" class="txtbox" value="<?php echo $g_view['input']['work_email'];?>"/><br />
									<span class="err_txt"><?php echo $g_view['err']['work_email'];?></span>								</td>
							  </tr>
							  <tr>
								<td colspan="3"><img src="images/spacer.gif" width="1" height="7" alt="" /></td>
								</tr>
								
								<tr>
								<td>Company :</td>
								<td>&nbsp;</td>
								<td><input name="firm_name" id="firm_name" onkeyup="lookup(this.value);" onblur="fill();" type="text" class="txtbox" value="<?php echo $g_view['input']['firm_name'];?>"/><br />
								<span class="err_txt"><?php echo $g_view['err']['firm_name'];?></span>
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
								<td>Division</td>
								<td>&nbsp;</td>
								<td><input name="division" type="text" class="txtbox" value="<?php echo $g_view['input']['division'];?>"/><br />
									<span class="err_txt"><?php echo $g_view['err']['division'];?></span>								</td>
							  </tr>
							  <tr>
								<td colspan="3"><img src="images/spacer.gif" width="1" height="7" alt="" /></td>
								</tr>
							  
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td><input name="submit" type="submit" class="btn_register" value="Submit" /></td>
							  </tr>
							</table>
				         </form>				  </td>
                      </tr>
                    </table></td>
                  </tr>
                </table></td>
              </tr>
            </table>