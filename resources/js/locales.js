if (typeof jQuery === 'undefined') { 
	throw new Error('This JavaScript requires jQuery'); 
}

$.locales = {
	/* client options. Modify these options to suit your implementation */	
	table : null,
	table_importfiles:null
};

$.locales.edit = function(locale_code, id)
{
	$.app.toConsole({"fkt":"$.locales.edit", "locale_code":locale_code, "id":id});
	
	var params = [
		{"name":"locale_code", "value":locale_code}, 
		{"name":"locale_token", "value":id},
		{"name":"rendermode", "value":"ajax"}
	];
	var params_get 	= $.param(params);
	var target 		= baseUrl+'root/locales/edit/'+locale_code+"/"+id;
	$.app.sendAjaxRequest(target, params, function success(result)
	{
		if (result.error && result.error != ""){
			$.dialog.error($.lang.item('error'), result.error);
		}
		else{
			
			$.app.replaceContent(result.data, $.locales.init_form, undefined, target);
			$.app.replaceContent(result.extra.breadcrumb, undefined, "breadcrumb");
		}
	}, true, null, $.lang.item('msg_wait') );
}

/**
 * save locale
 */
$.locales.save = function(e)
{
	$.app.toConsole("locale.save", "log");
	
	var params	= $('#form_locale').serializeArray();
		params.push({"name":"rendermode", "value":"json"})
		
	//$.app.sendAjaxRequest(baseUrl+"root/locales/ajax_save", params, function success(result)
	$.app.sendAjaxRequest(e.delegateTarget.action, params, function success(result)
	{
		$.app.toConsole({"fkt":"callback_ajax", "data":result});
		$.app.setFormValidationStates("form_locale", result.error, result.extra, null);
		
		if (result.error && result.error != ""){
			$.dialog.error($.lang.item('error'), result.error);
		}
		else{
			$('#i_locale_id').val(result.data.locale.locale_id);	// go into edit mode after save
			
			var callback_yes = function go_on(){
				
			}
			var callback_no = function go_back(){
				$.app.redirect(baseUrl+"root/locales/"); 
			}
			
			$.dialog.show($.lang.item('done'), $.lang.item('locale_has_been_saved'), callback_yes, callback_no, "success", $.lang.item('ok'), $.lang.item('back_to_overview'), true)
		}
	}, true, null, $.lang.item('locale_save_progress'));
}

/**
 * remove/delete locale entry
 * @param locale_code
 * @param id
 */
$.locales.remove = function(locale_code, id)
{
	$.app.toConsole({"fkt":"$.locales.remove", "locale_code":locale_code, "id":id});
	if (id == undefined){
		throw new Error($.lang.item('msg_missing_parameter')); 
	}
	
	var params = [
		{"name":"locale_code", "value":locale_code},
		{"name":"locale_id", "value":id},
		{"name":"confirmed", "value":1},
		{"name":"rendermode", "value":"JSON"}
	];
	
	$.dialog.confirm($.lang.item('msg_are_you_sure'), $.lang.item('locale_sure_delete'), function callback_yes()
	{
		$.app.sendAjaxRequest(baseUrl+"root/locales/remove/", params, function success(result)
		{
			$.app.toConsole({"fkt":"callback_ajax", "result":result});
			if (result.error && result.error != ""){
				$.dialog.error($.lang.item('error'), result.error);
			}
			else{
				if (result.status == "SUCCESS")
				{
					$.dialog.success($.lang.item('done'), $.lang.item('locale_deleted'), function callback_done(){
						$.locales.table.ajax.reload(); // reload the table 
					});
				}
			}
		}, true, null, $.lang.item('locale_delete_progress'));
		
	}, null, $.lang.item('locale_delete'), $.lang.item('cancel'))
}

$.locales.generate = function(e)
{
	$.app.toConsole("locale.generate", "log");
	
	var params	= $('#form_locale_actions').serializeArray();
		params.push({"name":"rendermode", "value":"json"})
	
	$.app.sendAjaxRequest(baseUrl+"root/locales/generate/", params, function success(result)
	{
		$.app.toConsole({"fkt":"callback_ajax", "result":result});
		if (result.error && result.error != ""){
			$.dialog.error($.lang.item('error'), result.error);
		}
		else{
			if (result.status == "SUCCESS"){
				$.dialog.success($.lang.item('done'), result.success);
			}
		}
	}, true, null, $.lang.item('locale_generate_progress'));
}

/**
 * initialize localization form 
 **/
$.locales.init_form = function()
{
	$.app.toConsole("$.locales.init_form");
	
	//$.app.init_toggle();
	//$.app.init_chosen();
	$('#form_locale').submit(function(e) {
        $.locales.save(e);
        e.preventDefault();
	});
	
	$('#form_locale_actions').submit(function(e) {
        $.locales.generate(e);
        e.preventDefault();
	});
	
	$('a[name=locale]').each(function() {
		$(this).removeAttr("href");
		
		$(this).on("click", function(e)
		{
			$('a[name=locale]').removeClass("active");
			$(this).addClass("active");
			
			var url = baseUrl+'root/locales/datatable/'+$(this).attr("locale_code");			

			// reload datatable with new url
			$.locales.table.ajax.url(url).load(); 	

			e.preventDefault();
		});
	});
	
						//selector, upload_url,  allowedExt, multiple, onSuccess, onError, minFiles, maxFiles, maxSize, upload_extra, initialPreview, showCaption, showPreview, showRemove, showUpload, showCancel, showClose, showUploadedThumbs
	$.app.init_fileinput("#input_upload", baseUrl+"root/locales/import_ajax",  ["csv"], false, null, null, 1, 1, 10000, {"submit_file": 1, "locale_code":$('#i_locale_code').val()}, null, true, true, true, true, true, false, false);
	$("#input_upload").on('filebatchuploadsuccess', function(event, files, extra) 
	{
		var response = files.response;
		
		$.locales.table_importfiles.ajax.reload();
		
		$.app.toConsole("file uploaded - reloading table");
    });
}

/**
 * initialize localization table
 **/
$.locales.init_table = function()
{
	$.app.toConsole({"fkt":"$.locales.init_table"});
	
	if ($("#tbl_locales").length > 0)
	{
		var selected_rows 	= [];
		
		$.locales.table = $.app.datatable.initialize_ajax("tbl_locales", baseUrl+"root/locales/datatable/"+selected_locale, tbl_columns_locales, 
			$.app.datatable.callbacks.rowCallback, 
			$.app.datatable.callbacks.initComplete
		);
	}
}


$(document).ready(function()
{
	$.app.toConsole("locale.js ready", "log");
	
	$.locales.init_table();
	$.locales.init_form();
});







