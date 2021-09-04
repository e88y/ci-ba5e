if (typeof jQuery === 'undefined') { 
	throw new Error('This JavaScript requires jQuery'); 
}

$.login = {
		options : {
	}
};

$.login.authenticate = function(frm_id)
{
	$.app.toConsole({"fkt":"$.login.authenticate"});
	
	if ($('#'+frm_id).length > 0)
	{
		var params	= $('#'+frm_id).serializeArray();
		
		$.app.sendAjaxRequest(baseUrl+"admin/login/ajax_authenticate", params, function success(result)
		{
			$.app.toConsole({"fkt":"userlogin ajax callback ", "data":result});
			
			$.app.setFormValidationStates(frm_id, result.error, result.extra, null);

			if (result.error != ""){
				$.dialog.error($.lang.item('msg_login_failed'), result.error);
			}
			else{
				$.dialog.success($.lang.item('welcome'), $.lang.item('msg_login_success'), function callback(){
					$.app.redirect(baseUrl + result.data.redirect_to);
				});
			}
		}, true, null, $.lang.item('progress_login'));

	}
	else{
		$.app.toConsole(" - form not found");
	}
}

$.login.preload = function()
{
	$.post(baseUrl+'main/Material_search/preload_materials', function( data ) {
		
		
	});
	
	/*
	$.app.sendAjaxRequest(baseUrl+'main/Material_search/preload_materials', [], function success(result)
  	{
  		// do nothing
		console.log("materials pre-loaded");
	}, false, null, "");
	*/
};


$(document).ready(function()
{
	$.app.toConsole("login.js ready");

	$('#form_login').submit(function(e) {
        e.preventDefault();
        $.login.authenticate("form_login");
	});
});