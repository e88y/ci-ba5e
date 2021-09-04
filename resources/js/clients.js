if (typeof jQuery === 'undefined') { 
	throw new Error('This JavaScript requires jQuery'); 
}

$.clients = {
	/* client options. Modify these options to suit your implementation */	
	table : null
};

$.clients.edit = function(id)
{
	$.app.toConsole({"fkt":"$.client.edit", "id":id});
	
	var params = [
  		{"name":"client_id", "value":id},
  		{"name":"rendermode", "value":"ajax"}
  	];
	
	var target = baseUrl+'root/clients/edit/'+id;
	$.app.sendAjaxRequest(target, params, function success(result)
	{
		if (result.error && result.error != ""){
			$.dialog.error($.lang.item('error'), result.error);
		}
		else{
			$.app.replaceContent(result.data, $.clients.init_form, undefined, target);
			$.app.replaceContent(result.extra.breadcrumb, undefined, "breadcrumb");
		}
	}, true, null, $.lang.item('msg_wait') );
}

$.clients.save = function(e)
{
	$.app.toConsole("client.save", "log");
	
	var params	= $('#form_clients').serializeArray();
		params.push({"name":"settings", "value":$('#form_client_settings').serialize()});
		params.push({"name":"deleted", "value":$('#i_deleted').checked});
		params.push({"name":"rendermode", "value":"json"})
	
	var target = $("#form_clients").attr("action");		
	$.app.sendAjaxRequest(target, params, function success(result)
	{
		$.app.toConsole({"fkt":"callback_ajax", "data":result});
		$.app.setFormValidationStates("form_clients", result.error, result.extra, null);
		
		if (result.error && result.error != ""){
			$.dialog.error($.lang.item('error'), result.error);
		}
		else{
			$.dialog.success($.lang.item('done'), $.lang.item('client_has_been_saved'), function callback()
			{
				$.app.redirect(baseUrl+"root/clients/");
			});
		}
	}, true, null, $.lang.item('client_save_progress'));
}

$.clients.remove = function(id)
{
	$.app.toConsole({"fkt":"$.clients.remove", "id":id});
	if (id == undefined){
		throw new Error('missing_parameter'); 
	}
	
	var params = [
  		{"name":"client_id", "value":id},
  		{"name":"confirmed", "value":1},
  		{"name":"rendermode", "value":"JSON"}
  	];
	
	$.dialog.confirm($.lang.item('msg_are_you_sure'), $.lang.item('client_sure_delete'), function callback_yes()
	{
		$.app.sendAjaxRequest(baseUrl+"root/clients/remove/", params, function success(result)
		{
			$.app.toConsole({"fkt":"callback_ajax", "result":result});
			if (result.error && result.error != ""){
				$.dialog.error($.lang.item('error'), result.error);
			}
			else{
				if (result.status == "SUCCESS"){
					$.dialog.success($.lang.item('done'), $.lang.item('client_deleted'), function callback_done(){
						$.clients.table.ajax.reload(); // reload the table 
					});
				}
			}
		}, true, null, $.lang.item('client_delete_progress'));
		
	}, null, $.lang.item('client_delete'), $.lang.item('cancel'))
}

/**
 * initialize clients form 
 **/
$.clients.init_form = function()
{
	if ($("#form_clients").length > 0)
	{
		$.app.toConsole("$.client.init_form");
		
		$.app.init_toggle();
		$.app.init_select2();
		
		if ($("#input_upload").length > 0)
		{
			var target = baseUrl+"root/clients/upload_logo/";
			var show_buttons = true;
			
			if ($("#i_client_id").val() != "")
			{
				$.app.init_fileinput("#input_upload", target,  ["jpg", "jpeg", "gif", "png"], false, null, null, 1, 1, 10000, {startUpload: 1, client_id:$("#i_client_id").val()}, $("#img_logo").get(0).outerHTML, true, true, show_buttons, show_buttons, show_buttons, false, false);
				
				$("#input_upload").on('filebatchuploadsuccess', function(event, files, extra) 
				{
					var response = files.response;
					$('#img_logo').attr("src", response.img_src);
					
					$("#input_upload").fileinput('refresh', {initialPreview: [ '<img src="'+response.img_src+'" class="file-preview-image">'], showUpload:false});
			    });
			};
		}
		
		$('#form_clients').submit(function(e) {
	        $.clients.save(e);
	        e.preventDefault();
		});
		/*
		$('#bt_submit').on("click", function(e) {
			$.clients.save(e);
		});
		*/
	}
		
}

/**
 * initialize clients table
 **/
$.clients.init_table = function()
{
	if ($("#tbl_clients").length > 0)
	{
		$.app.toConsole({"fkt":"$.clients.init_table"});
		
		var selected_rows = [];
		
		$.app.toConsole({"tbl_columns_clients":tbl_columns_clients});
	
		$.clients.table = $.app.datatable.initialize_ajax("tbl_clients", baseUrl+"root/clients/datatable", tbl_columns_clients, 
			$.app.datatable.callbacks.rowCallback, 
			$.app.datatable.callbacks.initComplete
		);
	}
};


$(document).ready(function()
{
	$.app.toConsole("clients.js ready", "log");
	
	$.clients.init_table();
	$.clients.init_form();
});