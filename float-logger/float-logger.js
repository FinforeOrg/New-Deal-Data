// JavaScript Document
function create_float_logger(){
	var logger = document.createElement('div');
    logger.id = 'float-logger';
	logger.className = 'float-logger-expand';
    document.body.appendChild(logger);
	
	jQuery('#float-logger').click(function(){
		jQuery(this).toggleClass('float-logger-collapsed');
	});
}
function show_in_float_logger(data){
	var logger = document.getElementById('float-logger');
	if (!logger) {
		create_float_logger();
		var logger = document.getElementById('float-logger');
	}

	var pre = document.createElement('pre');
	pre.innerHTML = data;
	logger.appendChild(pre);
}
