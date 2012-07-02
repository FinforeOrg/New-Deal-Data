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
		
		d.add(1,0,'Site','');
		d.add(2,1,'Change Password','changepassword.php');
		d.add(3,1,'Change Email','changesaemail.php');
		d.add(4,1,'Meta tags','metatag_edit.php');
		d.add(5,1,'Site Emails','site_emails.php');
		
		d.add(10,0,'Data','');
		//we now add sector , industry pair by script which process an excel file
		//we no longer use domain, we use sector and industry for a company
		//1/sep/2010: we should still keep provision for manual entry, just in case
		//we now use company table, we no longer need company name table
		
		//we now use script to process excel file and populate transaction category/subcategory/sub subcategory master table
		//sng 17/Apr/2010, we should still keep a provision for manual entry, just in case
		d.add(11,10,'Deal type/subtype list','deal_type_subtype_list.php');
		d.add(12,10,'Sector/industry list','sector_industry_list.php');
		d.add(15,10,'Region list','region_list.php');
		d.add(16,10,'Add Region','region_add.php');
		d.add(17,10,'Country list','country_list.php');
		d.add(18,10,'Add country','country_add.php');
		d.add(19,10,'Add designation','designation_add.php');
		d.add(20,10,'List designation','designation_list.php');
		
		d.add(21,10,'Deal date list','deal_date_list.php');
		
		d.add(30,0,'Static Pages','');
		d.add(31,30,'List pages','static_page_list.php');
		
		d.add(40,0,'Blog','');
		d.add(41,40,'List postings','blog_post_list.php');
		d.add(42,40,'Add posting','blog_post_add.php');
		
		d.add(50,0,'Companies','');
		d.add(51,50,'List','company_list.php');
		d.add(52,50,'Add','company_add.php');
		d.add(53,50,'Search','company_search.php');
		
		d.add(60,0,'Banks / Law firms','');
		d.add(61,60,'Add','blf_add.php');
		d.add(62,60,'Search','blf_search.php');
		d.add(63,60,'Top firm categories','blf_top_firms_categories.php');
		/***
		sng:22/july/2010
		We now have categorised top firms and we do not use firms marked as top firms so
		no need to show the list
		****/
		//d.add(64,60,'List default Top Firms','blf_top_firms.php');
		
		d.add(70,0,'Transaction','');
		//listing of deals is useless since there will be some billion data
		d.add(72,70,'Add','deal_add.php');
		d.add(73,70,'Search','deal_search.php');
		d.add(74,70,'Suggestions','deal_suggestion_list.php');
		d.add(75,70,'Disputes','deal_team_flagged_members_list.php');
		d.add(76,70,'Flagged','deals_marked_as_error.php');
		d.add(77,70,'Notes on deals','notes_suggested_on_deals.php');
		
		d.add(90,0,'Member','');
		d.add(91,90,'New Registration','member_newreg_list.php');
		d.add(92,90,'Unactivated registrations','unactivated_registration_list.php');
		d.add(93,90,'List','member_list.php');
		d.add(94,90,'Add favoured email','favoured_email_add.php');
		d.add(95,90,'List favoured emails','favoured_email_list.php');
		
		d.add(96,90,'Unactivated favoured registrations','favoured_unactivated_registration_list.php');
		d.add(97,90,'Profile change history','profile_change_history.php');
		
		d.add(100,0,'Ghost Member','');
		d.add(101,100,'List','ghost_member_list.php');
		d.add(102,100,'Create','ghost_member_add.php');
		
		d.add(200,0,'Home Page Chart','');
		d.add(201,200,'Create','home_page_chart_add.php');
		d.add(202,200,'List','home_page_chart_list.php');
		d.add(203,200,'Firms and charts','firm_chart_list.php');
		
		/****
		sng:01/oct/2010
		we need charts for the issuance page
		***/
		d.add(220,0,'Issuance Page Chart','');
		d.add(221,220,'Create','issuance_page_chart_add.php');
		d.add(222,220,'List','issuance_page_chart_list.php');
		
		d.add(300,0,'Best Firms','');
		d.add(301,300,'Create','top_firms_add.php');
		d.add(302,300,'List','top_firms_list.php');
		
		d.add(500,0,'Presets','');
		d.add(501,500,'Deal type list','preset_deal_type_list.php');
		d.add(502,500,'Sector Industry list','preset_sector_industry_list.php');
		d.add(503,500,'Country list','preset_country_list.php');
		d.add(504,500,'Deal size list','preset_deal_size_list.php');
		d.add(505,500,'Deal date list','preset_deal_date_list.php');
		
		d.add(600,0,'Top Search Options','');
		d.add(602,600,'Deal type','top_search_option_deal_type_list.php');
		d.add(602,600,'Sector Industry','top_search_option_sector_industry_list.php');
		d.add(603,600,'Country','top_search_option_country_list.php');
		
		d.add(650,0,'Top Search Requests','');
		d.add(651,650,'Running','top_search_request_list.php?status=in_progress');
		d.add(652,650,'Finished','top_search_request_list.php?status=finished');
		
		 d.add(700,0, 'PR', '');
        d.add(701,700, 'Page Settings', 'pr.php?action=pageSettings');
        d.add(702,700, 'Add press releases', 'pr.php?action=addPressReleases');
        d.add(703,700, 'Manage press releases', 'pr.php?action=managePressReleases');
		
		d.add(1000,0,'Utilities','');
		d.add(1001,1000,'Upload deal data','script_upload_deal_data.php');
		d.add(1002,1000,'Upload ghost','script_upload_ghost_members.php');
		
		d.add(1004,1000,'Update country list from company data','update_country_list_from_company_data.php');
		d.add(1005,1000,'Update deal type from deal data','update_deal_type_from_deal_data.php');
		d.add(1006,1000,'Update sector/industry from company data','update_sector_industry_from_company_data.php');
		
		
		/****
		sng:4/oct/2010
		some items ar moved to cleanup
		***/
		
		
		/*sng:16/aug/2010
		Mihai is giving a better solution
		d.add(1011,1000,'List extra companies without logo','list_extra_companies_without_logo.php');
		*/
		
		
		
		d.add(2000,0,'Cleanup','');
		d.add(2001,2000,'List m&amp;a targets with %','misc_list_deal_target_company_name_with_percent.php');
		d.add(2002,2000,'List companies with special char','misc_list_company_name_with_sp_char.php');
		d.add(2003,2000,'List banks/law firms with special char','misc_list_blf_name_with_sp_char.php');
		d.add(2004,2000,'List target/seller with special char','misc_list_target_seller_name_with_sp_char.php');
		
		d.add(2005,2000,'List companies with all caps','misc_list_company_name_with_all_cap.php');
		d.add(2006,2000,'List banks/law firms with all caps','misc_list_blf_name_with_all_cap.php');
		
		d.add(2007,2000,'Missing logos','list_companies_without_logo.php');
		
		d.add(2008,2000,'List duplicate firm entries','misc_company_duplicate_check.php');
		d.add(2009,2000,'List probable duplicate firms','misc_probable_duplicate_firms.php');
		d.add(2010,2000,'Companies without deals','list_companies_without_deals.php');
		d.add(2011,2000,'Banks without deals','list_banks_without_deals.php');
		d.add(2012,2000,'Law firms without deals','list_law_firms_without_deals.php');
		
		d.add(2013,2000,'List deals with duplicate bank','misc_list_deal_duplicate_bank.php');
		d.add(2014,2000,'List deals with duplicate law firms','misc_list_deal_duplicate_law_firms.php');
		
		d.add(2015,2000,'List deals with http in notes','misc_list_deal_with_url_in_note.php');
		d.add(2016,2000,'List pending M&amp;A deals','misc_list_pending_ma_deals.php');
		
		d.add(2017,2000,'List conflicting M&amp;A deals','misc_list_conflicting_ma_deals.php');
		
		d.add(2018,2000,'Search deals by sector/industry','misc_search_deals_by_sector_industry.php');
		d.add(2019,2000,'List companies missing info','list_companies_missing_info.php');
		d.add(2020,2000,'List deals missing info','list_deals_missing_info.php');
		
        d.add(3000,0,'One Stop','');
        d.add(3001,3000,'Options','oneStopOptions.php');
		
		
		document.write(d);

		//--> 
	</script>
</div>
