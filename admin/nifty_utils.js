function confirm_deletion(){
	var  ok = confirm("Do you really want to delete?\n This cannot be undone after this");
	if(ok){
		return true;
	}else{
		return false;
	}
}

function confirm_deletion_msg(msg){
	var  ok = confirm(msg);
	if(ok){
		return true;
	}else{
		return false;
	}
}