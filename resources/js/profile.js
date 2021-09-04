if (typeof jQuery === 'undefined') { 
	throw new Error('This JavaScript requires jQuery'); 
}

$.profile = {
	/* user options. Modify these options to suit your implementation */	
	options : {
		opt:false
	},
	table : null,
	crm_table : null
};


/**
 * save user profile 
 */
$.profile.save = function(e)
{
	$.app.toConsole({"fkt":"$.users.save"});
	
	var target 	= $('#form_user').attr( 'action' );
	var form 	= $('#form_user').get(0);
	
	var params 	= new FormData(form)
		params.append('rendermode','json');
	
	$.app.sendAjaxRequest(target, params, function success(result)
	{
		$.app.toConsole({"fkt":"callback save user profile ajax", "data":result});
		$.app.setFormValidationStates("form_user", result.error, result.extra, null);
		
		if (result.error && result.error != null){
			$.dialog.error($.lang.item('error'), result.error);
		}
		else{
			$.dialog.success($.lang.item('done'), $.lang.item('profile_has_been_saved'), function callback(){
				$.app.redirect(baseUrl+"admin/profile/");
			});
		}
	}, true, null, $.lang.item('user_profile_save_progress'), false, false, false);
}

/**
 * initialize user form 
 **/
$.profile.init_form = function()
{
	$.app.toConsole({"fkt":"$.profile.init_form"});
	
	$.app.init_checked_list_box();
	$.app.init_toggle();
	$.app.init_select2();
	
	$("#i_password").val("");
	$("#i_password_repeat").val("");
	
	if ($("#input_upload").length > 0)
	{
		var target = baseUrl+"admin/users/upload_avatar_ajax/";
		var show_buttons = true;
		
		if ($("#i_user_id").val() != "")
		{
			$.app.init_fileinput("#input_upload", target,  ["jpg", "jpeg", "gif", "png"], false, null, null, 1, 1, 10000, {startUpload: 1, user_id:$("#i_user_id").val()}, $("#img_avatar").get(0).outerHTML, true, true, show_buttons, show_buttons, show_buttons, false, false);
			$("#input_upload").on('filebatchuploadsuccess', function(event, files, extra) 
			{
				var response = files.response;
				$('#img_avatar').attr("src", response.img_src);
				
				$("#input_upload").fileinput('refresh', {initialPreview: [ '<img src="'+response.img_src+'" class="file-preview-image">'], showUpload:false});
		    });
		};
	}
	
	
	$('#form_user').submit(function(e) {
		e.preventDefault();
        $.profile.save(e);
	});
	
}

$(document).ready(function()
{
	$.app.toConsole("profile.js ready", "log");
	$.profile.init_form();
	
});