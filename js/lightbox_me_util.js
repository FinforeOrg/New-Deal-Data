function load_popup(pop_id){
	$('#'+pop_id).lightbox_me({
		zIndex: 1005,
		centered:true
	});
	$('#'+pop_id+'_close').click(function(){
		$('#'+pop_id).trigger('close');
	});
	return false;
}

function load_ajax_popup(pop_id,url){
	var id_close = pop_id+'_close';
	$('#'+pop_id).html("Loading...");
	var callback_f = function(){
		$.get(url,function(data){
			$('#'+pop_id).html('<div style="float:right"><input type="button" value="close" id="'+id_close+'" /></div><div style="clear:both"></div>'+data);
			$('#'+id_close).click(function(){
				$('#'+pop_id).trigger('close');
			});
			$('#'+pop_id).trigger('reposition');
			//otherwise it is not re-centered after loading new content via ajax.
		});
	}
	$('#'+pop_id).lightbox_me({
		centered:true,
		onLoad: callback_f
	});
	return false;
}

function load_iframe_popup(pop_id,url){
	var callback_f = function(){
		$('#'+pop_id+'_iframe').attr('src',url);
	}
	$('#'+pop_id).lightbox_me({
		centered:true,
		zIndex: 1005,
		onLoad: callback_f
	});
	/*******
	zIndex so that it appears above other elements.
	I got this from firebug
	*********/
	$('#'+pop_id+'_close').click(function(){
		$('#'+pop_id).trigger('close');
	});
	
	return false;
}