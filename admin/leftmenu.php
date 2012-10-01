   <table width="100%" border="0" cellpadding="4" cellspacing="0"> 
  <tr > 
    <!-- <td><img src="images/folder.gif"></td>  -->
    <td width="100%"><a href="javascript: d.openAll();"> 
      Open All 
      </a> | <a href="javascript: d.closeAll();"> 
      Close All 
      </a></td> 
  </tr> 
</table>
<br> 
<div class="dtree" style="margin-left: 2px;"> 
  <script type="text/javascript">
    
        <!--

        d = new dTree('d');

        d.add(0,-1,'<b>Home</b>','index.php');
        
		var block_level = 1;
		var child_level = block_level+1;
		
        d.add(block_level,0,'Site','');
        d.add(child_level,block_level,'Change Password','changepassword.php');child_level++;
        d.add(child_level,block_level,'Change Email','changesaemail.php');child_level++;
        d.add(child_level,block_level,'Meta tags','metatag_edit.php');child_level++;
        d.add(child_level,block_level,'Site Emails','site_emails.php');child_level++;
		/*********
		sng:24/mar/2011
		added a menu item to manage smtp server setting
		
		sng:21/sep/2011
		this is not needed since the mail() function in the server works
		d.add(6,1,'SMTP','site_smtp.php');
		***********/
        
		block_level = child_level;
		child_level = block_level+1;
		
        d.add(block_level,0,'Data','');
        //we now add sector , industry pair by script which process an excel file
        //we no longer use domain, we use sector and industry for a company
        //1/sep/2010: we should still keep provision for manual entry, just in case
        //we now use company table, we no longer need company name table
        
        //we now use script to process excel file and populate transaction category/subcategory/sub subcategory master table
        //sng 17/Apr/2010, we should still keep a provision for manual entry, just in case
        d.add(child_level,block_level,'Deal type/subtype list','deal_type_subtype_list.php');child_level++;
        d.add(child_level,block_level,'Sector/industry list','sector_industry_list.php');child_level++;
        d.add(child_level,block_level,'Region list','region_list.php');child_level++;
        d.add(child_level,block_level,'Add Region','region_add.php');child_level++;
        d.add(child_level,block_level,'Country list','country_list.php');child_level++;
        d.add(child_level,block_level,'Add country','country_add.php');child_level++;
        d.add(child_level,block_level,'Add designation','designation_add.php');child_level++;
        d.add(child_level,block_level,'List designation','designation_list.php');child_level++;
        
        
		d.add(child_level,block_level,'Currency list','currency_list.php');child_level++;
		d.add(child_level,block_level,'Stock exchange list','stock_exchange_list.php');child_level++;
		d.add(child_level,block_level,'Deal Partner Roles','deal_partner_role_list.php');child_level++;
		d.add(child_level,block_level,'Deal Company Roles','deal_company_role_list.php');child_level++;
        
		block_level = child_level;
		child_level = block_level+1;
		
        d.add(block_level,0,'Static Pages','');
        d.add(child_level,block_level,'List pages','static_page_list.php');child_level++;
        
		block_level = child_level;
		child_level = block_level+1;
		
        d.add(block_level,0,'Blog','');
        d.add(child_level,block_level,'List postings','blog_post_list.php');child_level++;
        d.add(child_level,block_level,'Add posting','blog_post_add.php');child_level++;
		
		block_level = child_level;
		child_level = block_level+1;
        
        d.add(block_level,0,'Companies','');
        d.add(child_level,block_level,'List','company_list.php');child_level++;
        d.add(child_level,block_level,'Add','company_add.php');child_level++;
        d.add(child_level,block_level,'Search','company_search.php');child_level++;
		d.add(child_level,block_level,'Suggestions','company_suggestion_list.php');child_level++;
		d.add(child_level,block_level,'Corrections','company_correction_list.php');child_level++;
        
		block_level = child_level;
		child_level = block_level+1;
		
        d.add(block_level,0,'Banks / Law firms','');
        d.add(child_level,block_level,'Add','blf_add.php');child_level++;
        d.add(child_level,block_level,'Search','blf_search.php');child_level++;
        d.add(child_level,block_level,'Top firm categories','blf_top_firms_categories.php');child_level++;
        /***
        sng:22/july/2010
        We now have categorised top firms and we do not use firms marked as top firms so
        no need to show the list
        ****/
        //d.add(64,60,'List default Top Firms','blf_top_firms.php');
		d.add(child_level,block_level,'Suggestions','blf_suggestion_list.php');child_level++;
		d.add(child_level,block_level,'Corrections','blf_correction_list.php');child_level++;
        
		block_level = child_level;
		child_level = block_level+1;
		
        d.add(block_level,0,'Transaction','');
        //listing of deals is useless since there will be some billion data
        /*****************************************************************
		sng:27/jun/2011
		Now we have different info to enter for each kind of transaction.
		We just show a minimum entry common to all type of deals and then show the edit form, where we
		are keeping all the complexities.
		d.add(72,70,'Add','deal_add.php');
		sng:1/oct/2012
		For now, we do not allow admin to add deal. In future, even if we allow, it will be the detailed template of front end
		d.add(child_level,block_level,'Add','simple_deal_add.php');child_level++;
		*****************/
        d.add(child_level,block_level,'Search','deal_search.php');child_level++;
		d.add(child_level,block_level,'Inactive','inactive_deal_list.php');child_level++;
		d.add(child_level,block_level,'Unverified by admin','unverified_by_admin_deal_list.php');child_level++;
        /************
		sng:25/apr/2012
		No longer needed since we create the deal record directly from member suggestions
		d.add(child_level,block_level,'Suggestions','deal_suggestion_list.php');child_level++;
		**************/
        d.add(child_level,block_level,'Disputes','deal_team_flagged_members_list.php');child_level++;
        d.add(child_level,block_level,'Flagged','deals_marked_as_error.php');child_level++;
        /*****************
		sng:22/sep/2011
		We no longer use this
		In the new deal detail page, we send the note to transaction_suggestion
		d.add(77,70,'Notes on deals','notes_suggested_on_deals.php');
		********************/
		/***************************
		sng:17/nov/2011
		In data-cx, we no longer need admin approval to show case studies, so we hide this for now. Instead we introduce
		list case studies
		d.add(78,70,'Submitted case studies','case_studies_suggested_on_deals.php');
		********************************/
		d.add(child_level,block_level,'List case studies','list_case_studies.php');child_level++;
		d.add(child_level,block_level,'List documents','list_deal_docs.php');child_level++;
		d.add(child_level,block_level,'Participant notification','manage_deal_participant_notification.php');child_level++;
		
		block_level = child_level;
		child_level = block_level+1;
		/*******************
		sng:14/oct/2011
		M&A metrics
		*******************/
		d.add(block_level,0,'M&A Metrics','');
		d.add(child_level,block_level,'Data Series','ma_list_data_series.php');child_level++;
        
		block_level = child_level;
		child_level = block_level+1;
		
        d.add(block_level,0,'Member','');
        d.add(child_level,block_level,'New Registration','member_newreg_list.php');child_level++;
        d.add(child_level,block_level,'Unactivated registrations','unactivated_registration_list.php');child_level++;
        d.add(child_level,block_level,'List','member_list.php');child_level++;
        d.add(child_level,block_level,'Add favoured email','favoured_email_add.php');child_level++;
        d.add(child_level,block_level,'List favoured emails','favoured_email_list.php');child_level++;
		/**************************************
		sng:14/dec/2011
		******/
		d.add(child_level,block_level,'List unfavoured emails','unfavoured_email_list.php');child_level++;
        
        d.add(child_level,block_level,'Unactivated favoured registrations','favoured_unactivated_registration_list.php');child_level++;
        d.add(child_level,block_level,'Profile change history','profile_change_history.php');child_level++;
		
		/******************************************
		sng:24/jan/2011
		
		sng:14/feb/2011
		********/
		d.add(child_level,block_level,'Company/email change requests','company_email_change_request_list.php');child_level++;
		d.add(child_level,block_level,'Unactivated company/email change requests','unactivated_company_email_change_request_list.php');child_level++;
		/**************************************/
        
		block_level = child_level;
		child_level = block_level+1;
		
		/**************
        d.add(block_level,0,'Ghost Member','');
        d.add(child_level,block_level,'List','ghost_member_list.php');child_level++;
         
        block_level = child_level;
		child_level = block_level+1;
		***************/
        
        d.add(block_level,0,'Cleanup','');
        d.add(child_level,block_level,'List m&amp;a targets with %','misc_list_deal_target_company_name_with_percent.php');child_level++;
        d.add(child_level,block_level,'List companies with special char','misc_list_company_name_with_sp_char.php');child_level++;
        d.add(child_level,block_level,'List banks/law firms with special char','misc_list_blf_name_with_sp_char.php');child_level++;
        d.add(child_level,block_level,'List target/seller with special char','misc_list_target_seller_name_with_sp_char.php');child_level++;
        
        d.add(child_level,block_level,'List companies with all caps','misc_list_company_name_with_all_cap.php');child_level++;
        d.add(child_level,block_level,'List banks/law firms with all caps','misc_list_blf_name_with_all_cap.php');child_level++;
        
        d.add(child_level,block_level,'Missing logos','list_companies_without_logo.php');child_level++;
        
        d.add(child_level,block_level,'List duplicate firm entries','misc_company_duplicate_check.php');child_level++;
        d.add(child_level,block_level,'List probable duplicate firms','misc_probable_duplicate_firms.php');child_level++;
        d.add(child_level,block_level,'Companies without deals','list_companies_without_deals.php');child_level++;
        d.add(child_level,block_level,'Banks without deals','list_banks_without_deals.php');child_level++;
        d.add(child_level,block_level,'Law firms without deals','list_law_firms_without_deals.php');child_level++;
        
        d.add(child_level,block_level,'List deals with duplicate bank','misc_list_deal_duplicate_bank.php');child_level++;
        d.add(child_level,block_level,'List deals with duplicate law firms','misc_list_deal_duplicate_law_firms.php');child_level++;
        
        d.add(child_level,block_level,'List deals with http in notes','misc_list_deal_with_url_in_note.php');child_level++;
        d.add(child_level,block_level,'List pending M&amp;A deals','misc_list_pending_ma_deals.php');child_level++;
        
        d.add(child_level,block_level,'List conflicting M&amp;A deals','misc_list_conflicting_ma_deals.php');child_level++;
        
        d.add(child_level,block_level,'Search deals by sector/industry','misc_search_deals_by_sector_industry.php');child_level++;
        d.add(child_level,block_level,'List companies missing info','list_companies_missing_info.php');child_level++;
        d.add(child_level,block_level,'List deals missing info','list_deals_missing_info.php');child_level++;
		/*****sng 1/feb/2011**************/
		d.add(child_level,block_level,'List probable duplicate deals','misc_probable_duplicate_deals.php');child_level++;
		/*************sng*****************/
        
        block_level = child_level;
		child_level = block_level+1;
		
		d.add(block_level,0,'One Stop','');
        d.add(child_level,block_level,'Options','oneStopOptions.php');  child_level++;
		
		block_level = child_level;
		child_level = block_level+1;
		
		d.add(block_level,0,'MMT Presets','');
        d.add(child_level,block_level,'Deal type list','preset_deal_type_list.php');child_level++;
        d.add(child_level,block_level,'Sector Industry list','preset_sector_industry_list.php');child_level++;
        d.add(child_level,block_level,'Country list','preset_country_list.php');child_level++;
        d.add(child_level,block_level,'Deal size list','preset_deal_size_list.php');child_level++;
        d.add(child_level,block_level,'Deal date list','preset_deal_date_list.php');child_level++;
		
		block_level = child_level;
		child_level = block_level+1;
		
		d.add(block_level,0,'MMT Top Search Options','');
        d.add(child_level,block_level,'Deal type','top_search_option_deal_type_list.php');child_level++;
        d.add(child_level,block_level,'Sector Industry','top_search_option_sector_industry_list.php');child_level++;
        d.add(child_level,block_level,'Country','top_search_option_country_list.php');child_level++;
		
		block_level = child_level;
		child_level = block_level+1;
		
		d.add(block_level,0,'MMT Top Search Requests','');
        d.add(child_level,block_level,'Running','top_search_request_list.php?status=in_progress');child_level++;
        d.add(child_level,block_level,'Finished','top_search_request_list.php?status=finished');child_level++;
        
		/**************************************
        d.add(block_level,0,'Hits','');
        d.add(child_level,block_level,'Show hits','hits.php');  child_level++;
        **********************************/
                      
        document.write(d);

        //--> 
    </script>
</div>
