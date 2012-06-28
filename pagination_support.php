<?php
/*********
sng:10/nov/2010
********/
$g_view['pagination'] = "";
if($g_view['lastpage'] > 1)
{	
	//previous button
	if ($g_view['page'] > 1){
		$g_view['pagination'].= "<a href=\"".$g_view['target_page']."page=".$g_view['prev']."\">Prev</a>&nbsp;";
	}
	
	//pages	
	if ($g_view['lastpage'] < 7 + ($g_view['adjacents'] * 2))	//not enough pages to bother breaking it up
	{	
		for ($g_view['pagination_counter'] = 1; $g_view['pagination_counter'] <= $g_view['lastpage']; $g_view['pagination_counter']++)
		{
			if($g_view['pagination_counter'] > 1){
				$g_view['pagination'].= "&nbsp;|&nbsp;";
			}
			if ($g_view['pagination_counter'] == $g_view['page']){
				$g_view['pagination'].= "<span class=\"pagination_here\">".$g_view['pagination_counter']."</span>";
			}else{
				$g_view['pagination'].= "<a href=\"".$g_view['target_page']."page=".$g_view['pagination_counter']."\">".$g_view['pagination_counter']."</a>";
			}				
		}
	}
	elseif($g_view['lastpage'] > 5 + ($g_view['adjacents'] * 2))	//enough pages to hide some
	{
		//close to beginning; only hide later pages
		if($g_view['page'] < 1 + ($g_view['adjacents'] * 2))		
		{
			for ($g_view['pagination_counter'] = 1; $g_view['pagination_counter'] < 4 + ($g_view['adjacents'] * 2); $g_view['pagination_counter']++)
			{
				if($g_view['pagination_counter'] > 1){
					$g_view['pagination'].= "&nbsp;|&nbsp;";
				}
				if ($g_view['pagination_counter'] == $g_view['page']){
					$g_view['pagination'].= "<span class=\"pagination_here\">".$g_view['pagination_counter']."</span>";
				}else{
					$g_view['pagination'].= "<a href=\"".$g_view['target_page']."page=".$g_view['pagination_counter']."\">".$g_view['pagination_counter']."</a>";
				}				
			}
			$g_view['pagination'].= "...";
			$g_view['pagination'].= "&nbsp;|&nbsp;<a href=\"".$g_view['target_page']."page=".$g_view['lpm1']."\">".$g_view['lpm1']."</a>";
			$g_view['pagination'].= "&nbsp;|&nbsp;<a href=\"".$g_view['target_page']."page=".$g_view['lastpage']."\">".$g_view['lastpage']."</a>";		
		}
		//in middle; hide some front and some back
		elseif($g_view['lastpage'] - ($g_view['adjacents'] * 2) > $g_view['page'] && $g_view['page'] > ($g_view['adjacents'] * 2))
		{
			$g_view['pagination'].= "<a href=\"".$g_view['target_page']."page=1\">1</a>";
			$g_view['pagination'].= "&nbsp;|&nbsp;<a href=\"".$g_view['target_page']."page=2\">2</a>";
			$g_view['pagination'].= "...";
			for ($g_view['pagination_counter'] = $g_view['page'] - $g_view['adjacents']; $g_view['pagination_counter'] <= $g_view['page'] + $g_view['adjacents']; $g_view['pagination_counter']++)
			{
				if($g_view['pagination_counter'] > $g_view['page'] - $g_view['adjacents']){
					$g_view['pagination'].= "&nbsp;|&nbsp;";
				}
				if ($g_view['pagination_counter'] == $g_view['page'])
					$g_view['pagination'].= "<span class=\"pagination_here\">".$g_view['pagination_counter']."</span>";
				else
					$g_view['pagination'].= "<a href=\"".$g_view['target_page']."page=".$g_view['pagination_counter']."\">".$g_view['pagination_counter']."</a>";					
			}
			$g_view['pagination'].= "...";
			$g_view['pagination'].= "&nbsp;|&nbsp;<a href=\"".$g_view['target_page']."page=".$g_view['lpm1']."\">".$g_view['lpm1']."</a>";
			$g_view['pagination'].= "&nbsp;|&nbsp;<a href=\"".$g_view['target_page']."page=".$g_view['lastpage']."\">".$g_view['lastpage']."</a>";		
		}
		//close to end; only hide early pages
		else
		{
			$g_view['pagination'].= "<a href=\"".$g_view['target_page']."page=1\">1</a>";
			$g_view['pagination'].= "<a href=\"".$g_view['target_page']."page=2\">2</a>";
			$g_view['pagination'].= "...";
			for ($g_view['pagination_counter'] = $g_view['lastpage'] - (2 + ($g_view['adjacents'] * 2)); $g_view['pagination_counter'] <= $g_view['lastpage']; $g_view['pagination_counter']++)
			{
				if($g_view['pagination_counter'] > $g_view['lastpage'] - (2 + ($g_view['adjacents'] * 2))){
					$g_view['pagination'].= "&nbsp;|&nbsp;";
				}
				if ($g_view['pagination_counter'] == $g_view['page'])
					$g_view['pagination'].= "<span class=\"pagination_here\">".$g_view['pagination_counter']."</span>";
				else
					$g_view['pagination'].= "<a href=\"".$g_view['target_page']."page=".$g_view['pagination_counter']."\">".$g_view['pagination_counter']."</a>";					
			}
		}
	}
	//next button
	if ($g_view['page'] < $g_view['pagination_counter'] - 1){
		$g_view['pagination'].= "<a href=\"".$g_view['target_page']."page=".$g_view['next']."\">&nbsp;Next</a>";
	}
			
}
//----End of Pagination Script
?>