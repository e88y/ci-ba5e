if (typeof jQuery === 'undefined') { 
	throw new Error('This JavaScript requires jQuery'); 
}
/** 
 * _BA5E
 *  	
 * @type Object
 * @description 
 * 	>> $.app is the main object for this app. It's used for implementing functions and options related to the application.
 *  >> Keeping everything wrapped in an object prevents conflict with other plugins and is a good way to organize our code.
 */
$.app = {
	/* Application options. Modify these options to suit your implementation */	
	baseUrl : baseUrl,
	
	options : {
		ajaxDataType: 'json',
		ajaxMethod:  'POST',
		ajaxHandleResultErrors: false
	},
	cookie : {
		
	},
	sessionStorage: undefined,
	localStorage: undefined,
	moment_defaults :
	{
		date: $.lang.item("date_format_long").toUpperCase(),
		date_and_time : "L - LT"
	},
	autonumeric_options_default :
	{
		aSep: $.lang.item("thousands_seperator"), 
		aDec: $.lang.item("decimal_seperator"),
	},
	autonumeric_options_currency :
	{
		aSep: $.lang.item("thousands_seperator"), 
		aDec: $.lang.item("decimal_seperator"),
		vMax: '99999999',	// <<-- will work with decimal 10,2
		pSign: 's', 
		aSign: " €"
	},
	tinyMCE : 
	{
		/** Options from the full featured example @see https://www.tinymce.com/docs/demo/full-featured/  
		 * 
		 * content_css: [
				'//localhost/cdn/tinymce/4.5.3/css/codepen.min.css'
			],
		 * */
		
		options_full : {
			height: 250,
			theme: 'modern',
			plugins: [
				'advlist autolink lists link image charmap print preview hr anchor pagebreak',
				'searchreplace wordcount visualblocks visualchars code fullscreen',
				'insertdatetime media nonbreaking save table contextmenu directionality',
				'emoticons template paste textcolor colorpicker textpattern imagetools codesample toc'
			],
			toolbar1: 'undo redo | insert | styleselect | emoticons | outdent indent | link image media | print preview codesample',
			toolbar2: 'bold italic | alignleft aligncenter alignright alignjustify | bullist numlist | forecolor backcolor',
			image_advtab: true,
			templates: [
				{ title: 'E-Mail', content: '<hr>Automatic mail<hr>' },
				{ title: 'var username', content: '{username}' }
			],
			
			insert_button_items: 'image link | inserttable',
			color_picker_callback: function(callback, value) {
			    callback('#FF00FF');
			  }
		}
	},
	colors : {
		standard: undefined,
		primary: undefined,
		info: undefined,
		warning: undefined,
		success: undefined,
		danger: undefined
	},
	screensize_class : undefined,
	callback_resize : undefined
};

/**
 * Get the ASCII number for a given char 
 */
$.app.char2acsii = function(char){
	return char.charCodeAt(0);
};

/**
 * calcluate a crosssumm 
 */
$.app.crosssum = function(value)
{
	sum = 0;
	while (value) 
	{
	    sum += value % 10;
	    value = Math.floor(value / 10);
	}
	return sum;
};

/**
 * basic html entity encode
 */
$.app.escapeHtml = function(text) {
	  var map = {
	    '&': '&amp;',
	    '<': '&lt;',
	    '>': '&gt;',
	    '"': '&quot;',
	    "'": '&#039;'
	  };

	  return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}

/**
 * update/manipulate the browser url 
 * 
 * @param new_url
 * @param title
 */
$.app.update_browser_url = function(new_url, title)
{
	if (typeof title === 'undefined') { 
		title = "";
	}

	if (typeof new_url === 'undefined') { 
		$.app.toConsole("no update url specified", "error");
	}
	else{
		window.history.pushState({}, title, new_url);
		//history.pushState({}, title, new_url);
	}
};

//..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
//..:: COOKIES ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
//..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
$.cookie = {
	sidebar_state : "sidebar-expanded",
	sidebar_selected_item : "sidebar-selected-item",
	no_js : false,
	
	
	setCookie : function(cname, cvalue, exdays) 
	{
	  var d = new Date();
	  	  d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
	  	
	  var expires = "expires="+d.toUTCString();
	  document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
	},
	getCookie : function(cname) 
	{
	  var name 	= cname + "=";
	  var ca 		= document.cookie.split(';');
	  
	  for(var i = 0; i < ca.length; i++) 
	  {
	      var c = ca[i];
	  
	      while (c.charAt(0) == ' ') 
	      {
	          c = c.substring(1);
	      }
	      
	      if (c.indexOf(name) == 0) 
	      {
	          return c.substring(name.length, c.length);
	      }
	  }
	  return "";
	},
	checkCookie : function() 
	{
		var user = $.app.getCookie("username");
		
		if (user != "") {
			alert("Welcome again " + user);
		}
		else
		{
			user = prompt("Please enter your name:", "");
			if (user != "" && user != null)
			{
				$.app.setCookie("username", user, 365);
			}
		}
	}
};

//..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
//..:: Generic functions
//..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
/**
 * console.log wrapper function
 * 
 * @description
 * Generic method write to console which should be used instead of <code>console.log</code> directly. 
 * When switching to a production environment, console-logs can be globaly dissabled here.   
 * 
 * @param {string} msg
 * @param {string} level optional
 * 
 */
$.app.toConsole = function(msg, level){
	if (msg != undefined && console_on == 1){
		console.log(msg, level);
	}
}

/**
 * @type function
 * @description encode uri. should work together with 'base_helper' 
 * @param string param 
 * @return string
 */
$.app.encode_uri = function(param) 
{
	var x = encodeURI($.app.base64.encode(param));
	$.app.toConsole("encode uri param ["+param+"]");
	return x;
};

/**
 * @type function
 * @description decode uri. should work together with 'base_helper' 
 * @param string param 
 * @return string
 */
$.app.decode_uri = function(param) 
{
	var x = $.app.base64.decode(decodeURI(param));
	$.app.toConsole("decode uri param ["+param+"]");
	return x;
};

/**
 * base64 object for encoding and decoding
 * 
 * @type function
 * @description encode and decode using base64
 * @link http://www.webtoolkit.info/javascript-base64.html
 */
$.app.base64 = 
{
	// private property
	_keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",	
	/**
	 * base64 encode
	 * 
	 * @type function
	 * @description encode base64
	 * @param string input
	 * @return string 
	 */
	encode : function (input) 
	{
        var output = "";
        var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
        var i = 0;
        
        input = $.app.base64._utf8_encode(input);

        while (i < input.length) 
        {
			chr1 = input.charCodeAt(i++);
			chr2 = input.charCodeAt(i++);
			chr3 = input.charCodeAt(i++);
 			
			enc1 = chr1 >> 2;
			enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
			enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
			enc4 = chr3 & 63;
			
			if (isNaN(chr2)) 
			{
				enc3 = enc4 = 64;
			} 
			else if (isNaN(chr3)) 
			{
				enc4 = 64;
			}
            output = output + this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) + this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);
        }
        return output;
    },
    /**
	 * public method for decoding base64 string
	 * 
	 * @type function
	 * @description decode base64
	 * @param string input
	 * @return string 
	 */
    decode : function (input) 
    {
		var output = "";
		var chr1, chr2, chr3;
		var enc1, enc2, enc3, enc4;
		var i = 0;
		
		input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

		while (i < input.length) 
		{
			enc1 = this._keyStr.indexOf(input.charAt(i++));
			enc2 = this._keyStr.indexOf(input.charAt(i++));
			enc3 = this._keyStr.indexOf(input.charAt(i++));
			enc4 = this._keyStr.indexOf(input.charAt(i++));
			chr1 = (enc1 << 2) | (enc2 >> 4);
			chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
			chr3 = ((enc3 & 3) << 6) | enc4;
			output = output + String.fromCharCode(chr1);
			
			if (enc3 != 64) 
			{
				output = output + String.fromCharCode(chr2);
			}

			if (enc4 != 64) 
			{
				output = output + String.fromCharCode(chr3);
			}
		}
		output = $.app.base64._utf8_decode(output);
		return output;
    },
    /**
	 * private method for UTF-8 encoding
	 * 
	 * @type function
	 * @description utf8 encode 
	 * @param string string
	 * @return string 
	 */
    _utf8_encode : function (string) 
    {
    	string = string.replace(/\r\n/g,"\n");
    	
    	var utftext = "";
    	for (var n = 0; n < string.length; n++) 
    	{
			var c = string.charCodeAt(n);
			if (c < 128) 
			{
				utftext += String.fromCharCode(c);
			}
			else if((c > 127) && (c < 2048)) 
			{
				utftext += String.fromCharCode((c >> 6) | 192);
				utftext += String.fromCharCode((c & 63) | 128);
			}
			else 
			{
				utftext += String.fromCharCode((c >> 12) | 224);
				utftext += String.fromCharCode(((c >> 6) & 63) | 128);
				utftext += String.fromCharCode((c & 63) | 128);
			}
		}
		return utftext;
	},
	/**
	 * private method for UTF-8 decoding
	 * 
	 * @type function
	 * @description utf8 decode 
	 * @param string utftext
	 * @return string 
	 */
	_utf8_decode : function (utftext) 
	{
		var string = "";
		var i = 0;
		var c = c1 = c2 = 0;
		
		while ( i < utftext.length ) 
		{
			c = utftext.charCodeAt(i);
			
			if (c < 128) 
			{
				string += String.fromCharCode(c);
				i++;
			}
			else if((c > 191) && (c < 224)) 
			{
				c2 = utftext.charCodeAt(i+1);
				string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
				i += 2;
			}
			else 
			{
				c2 = utftext.charCodeAt(i+1);
				c3 = utftext.charCodeAt(i+2);
				
				string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
				i += 3;
			}
		}
		return string;
	}
};

/**
 * @type function
 * @description highlight some element with a effect
 *  
 * @param string element >>  id to highlight
 * @param string msg >> custom message text
 */
$.app.highlight = function(element, msg)
{
	setTimeout(function()
	{
		$('#'+element).animate({backgroundColor:'rgba(0, 150, 0, 0.3)'}, 
			function(){
				$('#glow').css({
					'-webkit-box-shadow': '0px 0px 5px 5px rgba(0, 150, 0, 0.3)',
					'box-shadow': '0px 0px 5px 5px rgba(0, 150, 0, 0.3)'
				});
			});
		}, 500);

		setTimeout(function()
		{
			$('#glow').animate({backgroundColor:'rgba(0, 150, 0, 0.0)'}, 
				function(){$('#glow').css({
					'-webkit-box-shadow': '0px 0px 5px 5px rgba(0, 150, 0, 0.0)',
					'box-shadow': '0px 0px 5px 5px rgba(0, 150, 0, 0.0)'
				});
			});
		},1200);
};

/**
 * @type function
 * @description Block UI-Element 
 * @param string element >>  id to block
 * @param string msg >> custom message text
 */
$.app.blockUI = function(element, msg)
{
	var msg_txt = $.lang.item('msg_one_moment_please')+'...<br>'+(msg != null ? msg : '');
	
	msg = 
	'<table style="width:100%">'+
		'<tbody>'+
			'<tr>'+
				'<td style="width:50px;min-width:50px" >'+
					'<svg width="42px"  height="42px"  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" class="lds-dual-ring " style="background: none;">'+
						'<circle cx="50" cy="50" fill="none" stroke-linecap="round" r="40" stroke-width="14" '+
							'stroke-dasharray="62.83185307179586 62.83185307179586"'+
							'stroke="'+$.app.colors.primary+'" transform="rotate(269.501 50 50)">'+
							'<animateTransform attributeName="transform" type="rotate" calcMode="linear" values="0 50 50;360 50 50" keyTimes="0;1" dur="1s" begin="0s" repeatCount="indefinite"></animateTransform>'+
						'</circle>'+
					'</svg>'+
				'</td>'+
				'<td width="100%" style="width:100%;min-width:150px">'+msg_txt+
				'</td>'+
			'</tr>'+
		'</tbody>'+
	'</table>';
	
	var css = {
		minwidth: '260px',
		cursorReset: 'default',
		border: '1px solid '+$.app.colors.primary,
		draggable: true,
		width: '260px', 
    	top: '80px', 
    	left: '', 
    	right: '20px',
    	textAlign: 'left',
    	padding: '10px',
        baseZ: 99999999,	// z-index for the blocking overlay
        growlCSS: { 	// styles applied when using $.growlUI
            width:    '350px',
            top:      '10px',
            left:     '',
            right:    '10px',
            border:   'none',
            padding:  '5px',
            opacity:   0.6,
            cursor:    null,
            color:    $.app.colors.primary,
            backgroundColor: '#000',
            '-webkit-border-radius': '10px',
            '-moz-border-radius':    '10px'
        },
        iframeSrc: /^https/i.test(window.location.href || '') ? 'javascript:false' : 'about:blank',  // IE issues: 'about:blank' fails on HTTPS and javascript:false is s-l-o-w (hat tip to Jorge H. N. de Vasconcelos)
        forceIframe: false, 			// force usage of iframe in non-IE browsers (handy for blocking applets)
		overlayCSS: {					// styles for the overlay 
			backgroundColor: '#00f', 
			opacity: .1, 
			cursor: 'wait' 
		},		 
        
        // set these to true to have the message automatically centered
	    centerX: true, 				// <-- only effects element blocking (page block controlled via css above)
	    centerY: true,
	    allowBodyStretch: true,		// allow body element to be stetched in ie6; this makes blocking look better on "short" pages.  disable if you wish to prevent changes to the body height
	    bindEvents: true,			// enable if you want key and mouse events to be disabled for content that is blocked	
	    constrainTabKey: true, 		// be default blockUI will supress tab navigation from leaving blocking content (if bindEvents is true)
	    fadeIn:  200,				// fadeIn time in millis; set to 0 to disable fadeIn on block
	    fadeOut:  400,				// fadeOut time in millis; set to 0 to disable fadeOut on unblock
	    timeout: 30000,				// time in millis to wait before auto-unblocking; set to 0 to disable auto-unblock
	    showOverlay: true,			// disable if you don't want to show the overlay
	    focusInput: true,			// if true, focus will be placed in the first available input field when page blocking
	    onBlock: function(){		// callback method invoked when fadeIn has completed and blocking message is visible
	    	$.app.toConsole("blockUI has blocked the content", "info");
	    },
	    onUnblock: function(element, options){	// callback method invoked when unblocking has completed; the callback is passed the element that has been unblocked (which is the window object for page blocks) and the options that were passed to the unblock call: onUnblock(element, options)
	    	$.app.toConsole("blockUI has UN-blocked the content", "info");
	    },
	    quirksmodeOffsetHack: 4,	// don't ask; if you really must know: http://groups.google.com/group/jquery-en/browse_thread/thread/36640a8730503595/2f6a79a77a78e493#2f6a79a77a78e493
	    blockMsgClass: 'warning',	// class name of the message block
	    ignoreIfBlocked: false		// if it is already blocked, then ignore it (don't unblock and reblock)
	};
	
	if (element != null)
	{
		$(element).block({ message: msg, css: css });
	}
	else
	{
		// block whole page
		$.blockUI({ message: msg, css: css });
	}
};

/**
 * @type function
 * @description Unblock UI-Element  
 * @param string element >>  id to unblock
 */
$.app.unblockUI = function (element)
{
	if (element != null){
		$(element).unblock();
	}
	else{
		$.unblockUI();
	}
};

/**
 * @type function
 * @description returns a UUID 
 */
$.app.generateUUID = function(){
    
	var d = new Date().getTime();
    
    var uuid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) 
    {
        var r = (d + Math.random() * 16) %16 | 0;
        d = Math.floor(d/16);
        
        return (c == 'x' ? r : (r&0x3|0x8)).toString(16);
    });
    return uuid;
};

/**
 * @type function
 * @description global redirect method
 * @param string url
 */
$.app.redirect = function (url) 
{
    var ua        = navigator.userAgent.toLowerCase(),
        isIE      = ua.indexOf('msie') !== -1,
        version   = parseInt(ua.substr(4, 2), 10);

    // Internet Explorer 8 and lower
    if (isIE && version < 9) 
    {
        var link = document.createElement('a');
        link.href = url;
        document.body.appendChild(link);
        link.click();
    }

    // All other/normal browsers
    else { 
    	window.location.href = url; 
    }
};

/**
 * replace content of any id with animation. 
 * 
 * @param content		>> new content
 * @param callback		>> callback function
 * @param id			>> id without the # where the content goes to (default: content-body)
 * @param newURI		>> automatically call 'update_browser_url'
 * @param newURITitle	>> only if newURI is specified
 */
$.app.replaceContent = function(content, callback, id, newURI, newURITitle, tag)
{
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	if (content == undefined || content == ""){
		return;
	}
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	if (id == undefined){
		id = "content-body";
	}
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// ..:: Update the URL
	if (newURI != undefined)
	{
		$.app.update_browser_url(newURI, (newURITitle != undefined ? newURITitle : '') );
	}
	
	if (tag == undefined)
	{
		tag = "div";
	}
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// $.app.toConsole({"fkt":"$.app.replaceContent", "id":id, "content":content, "callback":callback});
	$('#'+id).fadeOut("fast", function()
	{
		// EDITED 21.01.2018:meb >> keep original classes
		var cls 	= $("#"+id).attr("class");
		var div 	= $('<'+tag+' id="'+id+'" class="'+cls+'">'+content+'</'+tag+'>');

		$(this).replaceWith(div);
		
		$('#'+id).fadeIn("fast", function()
		{
			//$.app.toConsole("... new content faded in by $.app.replaceContent");
			
			if(callback != undefined && typeof callback == 'function'){
				//$.app.toConsole("... calling calback");
				callback(); //setTimeout(callback, 10000); //delays next call
			}
		});
	});
};

/**
 * @description generic method for processing an AJAX-Request with POST-Data and JSON resonse (as defined in $.app.options.ajaxMethod and $.app.options.ajaxDataType)
 * Since the BASE_result is our generic Object for data exchange, we should retrieve a JSON encoded BASE-Result from the target controller.
 * By default, this method will NOT populate an error message in the success handler, if the recived BASE_Result contains errors in 'data.errors', so we can handle them in our own success handler.
 * You can activate this behaviour by setting <code>handleResultErrors</code> to <code>true</code>.
 * Only a real thrown AJAX-Error will be populated using $.app.dialog.error. 
 * 
 * Note for requests containing fileuploads:
 * 1. provide new FormData(form) as params as serialize will not work
 * 2. set 'processData' and 'contentType' to false 
 * 3. Make sure, your form send with "enctype"=>"multipart/form-data"  
 * 
 * 
 * @param string target				>> target controller method
 * @param object params				>> Parameters you want to send
 * @param function successHandler	>> callback function after successfull request
 * @param string blockContainer		>> Class or ID of the element you want to block while request
 * @param string blockMessage		>> Message you want to show while blocking
 * @param bool handleResultErrors	>> default=false
 * @param bool processData			>> default=false 
 * @param mixed contentType			>> default='application/x-www-form-urlencoded; charset=UTF-8' 
 */
$.app.sendAjaxRequest = function (target, params, successHandler, blockUI, blockContainer, blockMessage, handleResultErrors, processData, contentType)
{
	if (target == undefined){
		throw "No target specified";
	}

	if (handleResultErrors == undefined){
		handleResultErrors  = $.app.options.ajaxHandleResultErrors
	}
	if (blockUI === true){
		$.app.blockUI(blockContainer, blockMessage);
	}
	
	
	var ajax_object = {
		type: $.app.options.ajaxMethod,
		dataType : $.app.options.ajaxDataType,
		url: target,
		data: params,
		contentType: false,
	    processData: false,
	    cache:false,
		
		beforeSend:function()
		{	
			//$.app.toConsole({"fkt":"AJAX prepare", "params":params}, "log");
		},
		success:function(result)
		{	
			//$.app.toConsole({"fkt":"AJAX success", "data":result.data, "errors":result.error, "fullResult":result}, "log");
			
			if (result == undefined)
			{
				$.dialog.error("Die Anfrage lieferte ein leeres Ergebniss");
			}
			else
			{
				//$.app.validateAjaxResult(result, successHandler, handleResultErrors);
				
				if (result.error != null && result.error != "" && result.redirect != null && result.redirect != "")
				{	
					// Fehler mit anschließendem redirect
					$.dialog.error("Es ist ein Fehler aufgetreten", result.error+"<br>", function(){
						$.app.redirect(result.redirect);
					});
				}
				else if (result.error != null && result.error != "" && handleResultErrors === true)
				{
					// Fehler ohne redirect (handleResultErrors ist aber per default deaktiviert) --> Fehler werden üblicherweise vom aufrufenden Script behandelt
					$.dialog.error("Es ist ein Fehler aufgetreten", result.error+"<br>error dialog is auto-generated @see app.js");
				}
				else if (result.warning != null && result.warning != "")
				{
					$.dialog.warning($.lang.item("warning"), result.warning);
				}
				else if (result.info != null && result.info != "")
				{
					$.dialog.info($.lang.item("info"), result.info);
				}
				else if(successHandler != undefined && successHandler != null && typeof(successHandler) === "function")
				{
					successHandler(result);
				}
			}
			
			if (blockUI === true){
				$.app.unblockUI(blockContainer);
			}
		},
		error:function(xhr, ajaxOptions, thrownError)
		{
			$.app.toConsole({"fkt":"AJAX error", "target":target, "status":xhr.status, "error":thrownError, "options":ajaxOptions, "xhr":xhr}, "error");
			
			/*
			var response = String(xhr.responseText.replace(/[\r\n\t]/g, ' ').match(/<body.*>(.*)<\/body>/));
				response = response.replace("<body>", "");
				response = response.replace("</body>,", "");
			*/
			
			$.dialog.error($.lang.item('request_error'), $.lang.item('error_code')+":"+xhr.status+"  "+$.lang.item('error')+": "+thrownError);
			
			if (blockUI === true){
				$.app.unblockUI(blockContainer);
			}
		}
	};
	
	ajax_object.contentType = 'application/x-www-form-urlencoded; charset=UTF-8';
	if (contentType != undefined){
		ajax_object.contentType = contentType; 
	}
	
	ajax_object.processData = true;
	if (processData != undefined){
		ajax_object.processData = processData;
	}
	
	$.ajax( ajax_object );
};

/**
 * Sets form validation states on form items
 * 
 * @param formID		>> ID of the form (without #)
 * @param errorString	>> complete error string
 * @param errorArray	>> all errors as array. Basically it codeigniters form_validation->error_array() (BASE_Result->extra)
 * @param errorDiv		>> container to show the error string
 */
$.app.setFormValidationStates = function(formID, errorString, errorArray, errorDiv)
{
	$.app.toConsole( {"fkt":"setFormValidationStates", "form":formID, "errorArray":errorArray, "errorString":errorString, "count form groups":$("#"+formID+" .form-group").length } );
	
	if (! $("#"+formID).length > 0 ){
		throw new Error('element '+formID+' not found. make sure you have removed the # ?'); 
	}
	
	$.app.toConsole( " - remove classes from .form-groups ["+$("#"+formID+" .form-group").length+"]");
	$("#"+formID+" .form-group").each( function(index)
	{
		$(this).removeClass('has-error');
		$(this).removeClass('has-warning');
		$(this).removeClass('has-success');
	});

	if ($("#"+errorDiv).length > 0)
	{
		$.app.toConsole( " - set errorString to container ["+errorDiv+"]");
		
		$("#"+errorDiv).html("");
		$("#"+errorDiv).css("display", "none");
		
		if (errorString != "")
		{
			$("#"+errorDiv).html(errorString);
			$("#"+errorDiv).css("display", "block");
		}
	}	
	
	$.each(errorArray, function(key, val)
	{ 
		$.app.toConsole( " - set error > element ["+key+"] error["+val+"] found["+$("#"+formID+ " [name='"+key+"']").closest(".form-group").length+"]");
		
		if ($("#"+formID+" [name=\""+key+"\"]").length > 0 && $("#"+formID+" [name=\""+key+"\"]").closest(".form-group").length > 0)
		{
			$("#"+formID+" [name=\""+key+"\"]").closest("#"+formID+ " .form-group").addClass('has-error');
		}
		else{
			$.app.toConsole( " - formgroup ["+key+"] not found ");
		}
		
	});
};

//..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
//..:: DATATABLES :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
//..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
$.app.datatable = {};

/** 
 * datatable default options. 
 * modify these options to suit your implementation before initializing the table 
 */	
$.app.datatable.options = 
{
	deferRender: true,
	info: true,
	lengthChange:true,
	ordering: true,
	paging: true,
	processing: true,
	searching: true,
	responsive: true,
	select: false,
	lengthMenu: [ 5, 15, 25, 50, 100],
	pageLength: 15,
	pagingType: "full_numbers",	// numbers|simple|simple_numbers|full|full_numbers|first_last_numbers
	renderer: "bootstrap",
	language: JSON.parse($.lang.item("datatable")),
	
	/*
	dom: 'Bfrtip',
	buttons: [
	    'colvis',
	  	'copy',
	  	'excelHtml5',
	  	'csvHtml5',
	  	'pdfHtml5'
	],
	*/
	stateSave: false,
	stateSaveCallback: function (settings, data)
	{
		//$.app.toConsole({"fkt":"stateSaveCallback", "settings":settings, "data":data});
		localStorage.setItem( 'DataTables_' + settings.sInstance, JSON.stringify(data) );
		sessionStorage.setItem( 'DataTables_' + settings.sInstance, JSON.stringify(data) );
	},
	stateLoadCallback: function (settings) 
	{
		//$.app.toConsole({"fkt":"stateLoadCallback", "settings":settings, "storageItem":JSON.parse(localStorage.getItem( 'DataTables_' + settings.sInstance ))});
		return JSON.parse( localStorage.getItem( 'DataTables_' + settings.sInstance ) );
	}
};

$.app.datatable.user_settings =
{
	
};
/*
dom: 'Bfrtip',
buttons: [
	'copyHtml5',
	'excelHtml5',
	'csvHtml5',
	'pdfHtml5'
],
*/

/**
 * @description realign/adjust the table columns
 * @param table_obj >> datatable object
 */
$.app.datatable.adjust_columns = function(table_obj)
{
	if (table_obj != undefined){
		table_obj.columns.adjust().draw();
	}
};

/**
 * reusable default callback methods 
 */
$.app.datatable.callbacks = 
{
	initComplete : function(settings, json)
	{
		//$.app.toConsole("default dt-callback. initComplete");
	},
	
	/**
	 * Remove the href-Attributes from the edit and the delete button (only if there is a onclick attr)
	 * since we use onclick if js is enabled.
	 */
	rowCallback	: function(row, data, index){
		
		//$.app.toConsole({"fkt":"default rowCallback", "row":row, "data":data});
		$(row).find(".dtbt_edit").each(function(){
			if ($(this).attr("onclick")){
				$(this).removeAttr("href");
			}
			
		});
		$(row).find(".dtbt_remove").each(function(){
			if ($(this).attr("onclick")){
				$(this).removeAttr("href");
			}
		});
	}
};

/**
 * Handle and store user defined configuration
 * 
 * @param Event event
 * @param Object settings
 */
$.app.datatable.user_settings_changed = function (table_id, table_obj, additional)
{
	$.app.toConsole({"fkt":"user_settings_changed", "table-id":table_id, "table-obj":table_obj, "additional":additional});
	
	var columns_config 	= {};
	var settings 		= table_obj.settings();
	if (settings != undefined && settings.aoColumns != undefined)
	{
		$.each(settings.aoColumns, function (index, value) {
			columns_config[index] = {idx:value.idx, sWidth:value.sWidth, label:value.label,mData:value.mData};
		});
	}

	var state = {
			time:    +new Date(),
			start:   settings._iDisplayStart,
			length:  settings._iDisplayLength,
			order:   $.extend( true, [], settings.aaSorting ),
			search:  settings.oPreviousSearch,
			columns: $.map( settings.aoColumns, function ( col, i ) {
				return {
					data: 		col.data,
					label:		col.label,
					sortable: 	col.bSortable,
					visible:	col.bVisible,
					mData: 		col.mData,
					searchable: col.bSearchable ,
					filter:		col.filter,
					bSortable: 	col.bSortable,
					bVisible: 	col.bVisible,
					orderable: 	col.bOrderable,
					search: 	settings.aoPreSearchCols[i],
					width: 		col.sWidth 
				};
			} )
		};
	
	
	
	
	if (columns_config[0] != undefined )
	{
		var json_config = JSON.stringify(state);
	
		$.app.sendAjaxRequest(baseUrl+"home/saveUserTableConfiguration",
		{
			table_id: table_id,
			json_config: json_config,
			rendermode: "json"
		},
		function(result){
		
		});	
	}	
};

/**
 * Apply column filtering on table columns
 * 
 * @param obj table_obj				>> datatable instance
 * @param string table_id 			>> table identifier (without #)
 * @param bool search_immediatly 	>> start search on keydown or just on enter
 * @param function callback
 */
$.app.datatable.addColumnFilter = function(table_obj, table_id, search_immediatly, callback)
{
	if (search_immediatly == undefined){
		search_immediatly = true;
	}
	
	var search_always_on_focus_out = false;
	
	$.app.toConsole({"fkt":"addColumnFilter", "table_id":table_id, "table_obj":table_obj});
	
	//..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// NOTE: 
	// The column resize and ordering plugin generates a copy of the table. The 1st table contains only the Header and the 2nd table contains the data.
	// It also generates wrapper containers for this two tables. Thats why we have to check for this container first and change the selector for identifing the desired table.
	var tbl_selector 	= "#"+table_id
	if ($("#"+table_id).parent().hasClass("dataTables_scrollBody"))
	{
		tbl_selector	=  "#"+table_id+"_wrapper > .dataTables_scroll > .dataTables_scrollHead > .dataTables_scrollHeadInner > table"; 
	}

	//..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// Loop trough each table header
	$(tbl_selector+" thead tr:eq(0) th").each(function() 
	{
		//var title = $(this).text();
		var title = $("#"+table_id+' thead tr:eq(0) th').eq( $(this).index() ).text();
		if ($(this).hasClass("has-filter") && title != "")
		{
			//..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
			// Here we create the input for column filtering and prevent the click event to be triggered. Otherwise the will send a sort-request
			var filter_input = $('<input type="text" style="z-index:1000; width:100%" placeholder="" class="table-filter" />');
			
			$(filter_input).on("click", function(e)
			{
				e.stopPropagation();
			});
			
			//..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
			// apply the input to the table header 
			$(this).html( filter_input );
		}
	});

	//..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
    // Now we apply the search functionality
	table_obj.columns().eq( 0 ).each( function ( colIdx ) 
    {
		// key-up
		$(tbl_selector+' thead tr:eq(0) th:eq(' + colIdx + ') input').on('keyup', function(event)
		{
			var keycode = (event.keyCode ? event.keyCode : event.which);
			
			$.app.toConsole({"fkt":"column filter key-up", "table_obj":table_obj, "column-index":colIdx, "key-code":keycode, "search-value":this.value});
			
			if(keycode == '13' || search_immediatly === true)
			{
				table_obj
				    .column( colIdx )
				    .search( this.value )
				    .draw();
			}
	    });
		
		// focus-out / blur 
		if (search_immediatly !== true || search_always_on_focus_out === true)
		{
			$(tbl_selector+' thead tr:eq(0) th:eq(' + colIdx + ') input').on('focusout', function(event)
			{
				$.app.toConsole({"fkt":"column filter focus-out", "table_obj":table_obj, "column-index":colIdx, "search-value":this.value});
				
				if(this.value != "")
				{
					table_obj
					    .column( colIdx )
					    .search( this.value )
					    .draw();
				}
			});
		}
    } );
	
	if(callback != undefined && typeof callback == "function")
	{
		callback();
	}
};

/**
 * Reset the global search and column filters and redraw table 
 * 
 * @param tbl_object >> datatable object
 */
$.app.datatable.reset_column_filter = function(tbl_object)
{
	var tbl_id			= tbl_object.table().node().id;
	var tbl_selector 	= "#"+tbl_id
	if ($("#"+tbl_id).parent().hasClass("dataTables_scrollBody"))
	{
		tbl_selector	=  "#"+tbl_id+"_wrapper > .dataTables_scroll > .dataTables_scrollHead > .dataTables_scrollHeadInner > table"; 
	}
	
	$.app.toConsole({"fkt":"reset_column_filter", "table-id":tbl_id});
	
	tbl_object.columns().eq( 0 ).each( function ( colIdx ) 
	{
		var filter	= $(tbl_selector+' thead tr:eq(0) th:eq(' + colIdx + ') .table-filter');
		
		$.app.toConsole({"current-val":$(filter).val()});
		
		if ($(filter).is("select")){
			$(filter).val("-9999").trigger('change');
		}
		else if($(filter).is("input")){
			$(filter).val("");
		}
		
		//tbl_object.column( colIdx ).search( '', true, false ).draw();
	});
	
	// reset global search and all column filterings at once
	tbl_object.search('', true, false ).columns().search('').draw();
};

/**
 * re-init table, clear and add new data
 * 
 * @param string tableID
 * @param object data
 */
$.app.datatable.reload = function(tableID, data)
{
};

/**
 * reload server side datatable with new url
 * 
 * @param string table		>> the table object (NOT THE ID)
 * @param string url		>> the new ajax url for the table
 * @param function callback >> after data has been loaded
 */
$.app.datatable.reload_ajax = function(tableObj, url, callback)
{
	// setthe new url
	tableObj.ajax.url(url).load(function(){ }, true);
	
	// reload the table and call the callback function
	tableObj.ajax.reload(function(json){		
		if (callback != undefined && typeof callback == 'function'){
			callback(json);
			
			if ($.app.datatable.bFancy === true){
				$(tableObj).parent().addClass("animated fadeInRight");
			}
    	}
	}, true); 
};

/**
 * Initialize server side datatable
 * 
 * @param string tableID 		>> table id without #
 * @param string url			>> source url
 * @param object columns		>> "columns": [ {"data":"username", bSortable:false, mRender: function (data, type, full) {},  { "data": "firstname" }, { "data": "lastname" } ]
 * @param function rowCallback 	>> function( row, data )
 * @param function initComplete	>> function( settings, json )
 * @param object options		>> custom options to set missing options or override the default ones
 * @param object append_data_fkt>> function to append data to the post (custom filtering) >>> function ( data ) { data.custom_field = 'tada';}
 */
$.app.datatable.initialize_ajax = function(tableID, url, columns, rowCallback, initComplete, options, append_data_fkt)
{
	$.app.toConsole( {"fkt":"$.app.datatable.initialize_ajax", "tableID":tableID, "url":url, "columns":columns, "rowCallback":rowCallback, "initComplete":initComplete});
	if (tableID == undefined || url == undefined){
		throw new Error('table identifier and datasource url required'); 
	}
	
	var dt_options = {
        "serverSide": true,
		"ajax":{
            url :url, 		// json datasource
            type: "post",  	// method  , by default get
            error: function(xhr, error, thrown)
            {  
            	$.app.toConsole({"fkt":"datatable ajax error", "xhr":xhr, "error":error, "thrown":thrown});
				$.dialog.error( "Beim Senden der Anfrage ist ein Fehler aufgetreten", "Status-Code ["+xhr.status+"] Error ["+thrown+"]<br><br>"+xhr.responseText);
				$.app.unblockUI();
            	
            },
            data: function ( data ) 
            { 
            	data.table_id 	= tableID;
            	data.rendermode = "JSON_DATA";
            	
            	if (append_data_fkt != undefined && typeof append_data_fkt == 'function'){
            		append_data_fkt(data);		// append custom post data
            	}
            }
        },
        "rowCallback": function( row, data, index ) 
		{
        	//$.app.toConsole({"fkt":"rowCallback", "row":row, "data":data, "table":tableID});
			
        	// call custom row callback if defined
        	if (rowCallback != undefined && typeof rowCallback == 'function'){
				rowCallback(row, data, index);
        	}
		},
        "initComplete": function(settings, json)
        {
        	//$.app.toConsole({"fkt":"initComplete", "settings":settings, "json":json, "table":tableID});

        	// call custom init complete handler if defined
        	if (initComplete != undefined && typeof initComplete == 'function'){
        		initComplete(settings, json);
			}
        	
        	if ($.app.datatable.bFancy === true){
        		$('#'+tableID).parent().addClass("animated fadeInRight");
        	}
        },
        stateSave: 		$.app.datatable.options.stateSave,
        "stateSaveCallback": $.app.datatable.options.stateSaveCallback,
        "stateLoadCallback": $.app.datatable.options.stateLoadCallback,

        "columns": columns,
        "autoWidth":		false,		
        "deferRender": 		$.app.datatable.options.deferRender,
    	"info": 			$.app.datatable.options.info,
    	"lengthChange": 	$.app.datatable.options.lengthChange,
    	"ordering": 		$.app.datatable.options.ordering,
    	"paging": 			$.app.datatable.options.paging,
    	"processing": 		$.app.datatable.options.processing,
    	"searching": 		$.app.datatable.options.searching,
    	"responsive": 		$.app.datatable.options.responsive,
    	"stateSave": 		$.app.datatable.options.stateSave,
    	"lengthMenu": 		$.app.datatable.options.lengthMenu,
    	"pageLength": 		$.app.datatable.options.pageLength,
    	"pagingType": 		$.app.datatable.options.pagingType,
    	"renderer": 		$.app.datatable.options.renderer,
    	"language": 		$.app.datatable.options.language,
    	"order": 			$.app.datatable.options.order,
    }
	
	//..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// ..:: overwirte or append custom options
	if (options != undefined){
		$.each(options, function (index, value) {
			dt_options[index] = value;
		});
	}
	
	//..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// ..:: set custom listener on column resize and reorder events 
	if (dt_options.dom != undefined && dt_options.dom == "Rlfrtip")
	{
		//..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		$('#'+tableID).on('column-resize.dt.mouseup', function(event, oSettings) 
		{
			$.app.toConsole({"fkt":"column-resize", "event":event, "settings":oSettings});
		    //$.app.datatable.user_settings_changed(event, oSettings);
		});
		
		//..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		$('#'+tableID).on('column-reorder.dt.mouseup', function(event, oSettings) 
		{
			if (event.namespace == "dt.mouseup")
			{
				$.app.toConsole({"fkt":"column-reorder", "event":event, "settings":oSettings});
				//$.app.datatable.user_settings_changed(event, oSettings);
			}
		});
	}
	
	var table = $('#'+tableID).DataTable( dt_options );
	
    return table;
};


//..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
//..:: PLUGIN INITIALIZATION ::::::::::::::::::::::::::::::::::::::::::::::::..
//..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
$.app.initialize = function()
{
	$.app.toConsole("initialize");
	$.app.toConsole( {"lng":$.lang.language, "datatables":JSON.parse($.lang.item('datatable'))});
	
	//..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// set default datatable options
	if ($.fn.dataTable != undefined){
		$.extend( $.fn.dataTable.defaults, $.app.datatable.options);
		
		$.fn.dataTable.ColReorder();
	}
};

/**
 * initialize the autonumeric plugin
 * @param string selector >> optional selector
 */
$.app.init_autonumeric = function(selector, options)
{
	if (selector == undefined){
		selector = ".autonumeric";
	}
	
	if (options == undefined)
	{
		var options = {};
	}
	
	if (options.aSep == undefined)
	{
		options.aSep = ".";
		if ($.lang.item('thousands_seperator') != undefined)
		{
			options.aSep = $.lang.item('thousands_seperator');
		}
	}
	if (options.aDec == undefined)
	{
		options.aDec = ",";
		if ($.lang.item('decimal_seperator') != undefined)
		{
			options.aDec = $.lang.item('decimal_seperator');
		}
	}	
	
	if(jQuery().autoNumeric) 
	{
		if ($(selector).length > 0)
		{
			$(selector).autoNumeric('init', options);
			
			$.app.toConsole(" - autonumeric initialized");
		}
	}
	else{
		$.app.toConsole(" - autonumeric plugin NOT FOUND");
	}
};


/**
 * initialize list items with checkboxes
 * Default selector [.list-group.checked-list-box .list-group-item] will be used if no custom selector is provided.
 * 
 * @link (modified) http://bootsnipp.com/snippets/featured/checked-list-group
 * @param string selector >> optional jquery selector
 */
$.app.init_checked_list_box = function(selector)
{
	if (selector == undefined){
		selector = ".list-group.checked-list-box .list-group-item";
	}
	
	if ($(selector).length > 0)
	{
		$(selector).each(function () 
		{
			var cb 		= $(this).find('input:checkbox');
			var lbl		= $(this).find("label").find("span");
			
			$(this).find("label").addClass("no-padding");
			$(this).find('input:checkbox').hide();
			
			var checked = (cb.hasClass("active") ? "checked":"");
			// Settings
	        var $widget = $(this),
	            $checkbox = $(cb),
	            color = ($widget.data('color') ? $widget.data('color') : "primary"),
	            style = ($widget.data('style') == "button" ? "btn-" : "list-group-item-"),
	            settings = {
	                on: {
	                    icon: 'glyphicon glyphicon-check'
	                },
	                off: {
	                    icon: 'glyphicon glyphicon-unchecked'
	                }
	            };
	            
	        $widget.css('cursor', 'pointer')
	        $widget.append($checkbox);

	        // Event Handlers
	        $widget.on('click', function () {
	        	if($checkbox.hasClass("disabled") == false)
	        	{
	        		$checkbox.prop('checked', !$checkbox.is(':checked'));
		            $checkbox.triggerHandler('change');
		            //updateDisplay();
	        	}
	        });
	        $checkbox.on('change', function () {
	            updateDisplay();
	        });
	          
	        // Actions
	        function updateDisplay() {
	            var isChecked = $checkbox.is(':checked');

	            // Set the button's state
	            $widget.data('state', (isChecked) ? "on" : "off");

	            // Set the button's icon
	            $widget.find('.state-icon')
	                .removeClass()
	                .addClass('state-icon ' + settings[$widget.data('state')].icon);

	            // Update the button's color
	            if (isChecked) {
	                $widget.addClass(style + color + ' active');
	            } else {
	                $widget.removeClass(style + color + ' active');
	            }
				//$.app.toConsole({"fkt":"updateDisplay", "checked":isChecked}, "log");
	        }

	        // Initialization
	        function init() {
	            
	            if ($widget.data('checked') == true) {
	                $checkbox.prop('checked', !$checkbox.is(':checked'));
	            }
	            
	            updateDisplay();

	            // Inject the icon if applicable
	            if ($widget.find('.state-icon').length == 0) {
	                //$widget.prepend('<span class="state-icon ' + settings[$widget.data('state')].icon + '"></span>');
	            	$widget.find("label").prepend('<span class="state-icon ' + settings[$widget.data('state')].icon + '"></span>');
	            }
	        }
	        
	        init();
	    });
		$.app.toConsole(" - checked list box initialized");
	}
	/*
    $('#get-checked-data').on('click', function(event) {
        event.preventDefault(); 
        var checkedItems = {}, counter = 0;
        $("#checked-list-box li.active").each(function(idx, li) {
			checkedItems[counter] = $(li).text();
            counter++;
			
			//$.app.toConsole({"fkt":"each", "idx":idx, "li":li}, "log");
        });
		//$.app.toConsole({"fkt":"get-checked-data", "checkedItems":checkedItems}, "log");
		
        $('#display-json').html(JSON.stringify(checkedItems, null, '\t'));
    });
	*/
};

/**
 * Initialize datepicker-plugin.
 * Default selector [.datepicker] will be used if no custom selector is provided.
 *  
 * @param string selector
 */
$.app.init_datepicker = function(selector)
{
	if(jQuery().datepicker) 
	{
		if (selector == undefined)
		{
			selector = ".datepicker";
		}
		
		if ($(selector).length > 0)
		{
            $(selector).datetimepicker("destroy");

			$(selector).each(function()
			{
				$(this).datetimepicker({
					//language: $.lang.item('locale'),
					//format: $.lang.item('date_format')
				});
                                
			});
			//$.app.toConsole(" - dateppicker initialized", "log");	
		}
	}
};


/**
 * Initialize bootstrap fileinput-plugin.
 * @see http://plugins.krajee.com/file-input
 * 
 * @param string $selector	>> Default selector [ input[name=upload] ] will be used if no custom selector is provided.
 * @param string $upload_url
 * @param string $multiple
 * @param string $minFiles
 * @param string $maxFiles
 * @param string $maxSize
 * @param string $upload_extra
 * @param string $allowedExt
 * @param string $showCaption
 * @param string $showPreview
 * @param string $showRemove
 * @param string $showUpload
 * @param string $showCancel
 * @param string $showClose
 * @param string $showUploadedThumbs
 */
$.app.init_fileinput = function(selector, upload_url,  allowedExt, multiple, onSuccess, onError, minFiles, maxFiles, maxSize, upload_extra, initialPreview, showCaption, showPreview, showRemove, showUpload, showCancel, showClose, showUploadedThumbs, other_options)
{
	if(jQuery().fileinput) 
	{
		if (selector == undefined){
			selector = "input[name=upload]";
		}
		if (upload_url == undefined){
			$.app.toConsole("no upload url specified for fileinput. Degrading to normal"); 
		}
		
		if ($(selector).length > 0)
		{
			if (allowedExt == undefined){
				allowedExt = false;  
			}
			if (multiple == undefined){
				multiple = false;  
			}
			if (minFiles == undefined){
				minFiles = 1;
			}
			if (maxFiles == undefined){
				maxFiles = 1;
			}
			if (maxSize == undefined){
				maxSize = 0;
			}
			if (upload_extra == undefined){
				upload_extra = {
					startUpload: 1
				};
			}
			
			if (showCancel == undefined){
				showCancel = true;
			}
			if (showCaption == undefined){
				showCaption = true;
			}
			if (showClose == undefined){
				showClose = false;
			}
			if (showPreview == undefined){
				showPreview = true;
			}
			
			var options = {
				language: $.lang.item('app_locale_short').toLowerCase(),
			    uploadUrl: upload_url,
			    error: "error",
			    uploadAsync: false,
			    deleteUrl: "",
				showCaption: showCaption, showPreview: showPreview, showRemove: showRemove, showUpload: showUpload,
			    showCancel: showCancel, showClose: showClose, showUploadedThumbs: showUploadedThumbs,
			    browseOnZoneClick: true, dropZoneClickTitle: "",
			    multiple: multiple,
			    autoReplace: true,
			    minFileCount: minFiles, 
			    maxFileCount: maxFiles,
			    maxFileSize: maxSize,
			    allowedFileExtensions: allowedExt,
			    overwriteInitial:true,
				previewSettings : {
			        image: {width: "auto", height: "auto"}
			    },
			    previewClass: "hidden-xs",
			    uploadExtraData: function() {
			    	if (upload_extra != undefined && typeof upload_extra == 'function'){
			    		return upload_extra();
			    	}else{
			    		return upload_extra;
			    	}
			    },
			    layoutTemplates: {
			        main1: 
					    '{preview}\n' +
					    '<div class="kv-upload-progress hide"></div>\n' +
					    '<div class="row">'+
					    '	<div class="col-xs-12">'+
					    '		<div class="input-group {class} ">\n' +
					    '   		{caption}\n' +
					    '			<div id="grp-browse" class="input-group-btn">\n' +
					    '       		{browse}\n' +
					    '   		</div>\n' +
					    '		</div>'+	
					    '	</div>'+
					    '</div><br>'+
					    '<div class="row">'+
					    '	<div class="col-xs-12">'+
					    '      		{upload}\n' +
					    '    		{remove}\n' +
					    '			{cancel}\n' +
					    '	</div>'+
					    '</div>',
					actions:
						'<div class="file-actions">\n' +
				        '    <div class="file-footer-buttons">\n' +
				        '        {delete} {other}' +
				        '    </div>\n' +
				        '    {drag}\n' +
				        '    <div class="file-upload-indicator" title="{indicatorTitle}">{indicator}</div>\n' +
				        '    <div class="clearfix"></div>\n' +
				        '</div>'
			    },
			    
			    buttonLabelClass: "btn-sm",
			    browseClass: "btn btn-default btn-sm",
			    removeClass: "btn btn-danger btn-sm btn-block",
			    cancelClass: "btn btn-danger btn-sm btn-block",
			    cancelLabel: "Vorgang abbrechen",
			    uploadClass: "btn btn-success btn-sm btn-block"
			};
			
			if (initialPreview !== undefined){
				options.initialPreview = [initialPreview];  
			}
			
			//..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
			// ..:: overwirte or append custom options
			if (other_options != undefined){
				$.each(other_options, function (index, value) {
					options[index] = value;
				});
			}
			
			
			$(selector).fileinput('destroy');
			$(selector).fileinput(options);
			
			// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
			// Register event-handlers 
			
			//IE clear file input
			var ua        = navigator.userAgent.toLowerCase(),
	        isIE      = ua.indexOf('msie') !== -1,
	        version   = parseInt(ua.substr(4, 2), 10);

		    // Internet Explorer 8 and lower
		    if (isIE && version < 9) 
		    {
				$("#clear").on("click", function () {
				    $fileInput.replaceWith( $fileInput = $fileInput.clone( true ) );
				});
			}

			$(selector).off('filebatchpreupload').on('filebatchpreupload', function(event, data, previewId, index) {
				$.app.toConsole({"fkt":"filebatchpreupload", "evt":event, "data":data, "previewId":previewId, "index":index});
				return true;// validateFormData();
		    });
			
			$(selector).off('filepreupload').on('filepreupload', function(event, data, previewId, index) {
				$.app.toConsole({"fkt":"filepreupload", "evt":event, "data":data, "previewId":previewId, "index":index});
				return true;// validateFormData();
		    });
			
			$(selector).off('filecustomerror').on('filecustomerror', function(event, params) 
			{
		       // params.abortData will contain the additional abort data passed
		       // params.abortMessage will contain the aborted error message passed
				$.app.toConsole( {"fkt":"filecustomerror", "event":event, "abortData":params.abortData, "abortMessage":params.abortMessage} );
		    });
			
			$(selector).off('filebatchuploadcomplete').on('filebatchuploadcomplete', function(event, files, extra) 
			{
				//$(selector).fileinput('reset');
				//$(".progress-bar").hide();
				
				$(".fileinput-upload-button").hide() 
				
				$.app.toConsole({"fkt":"filebatchuploadcomplete", "evt":event, "files":files, "countFiles":files.length, "extra":extra});
		    });
			
			// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
			$(selector).off('filebatchuploadsuccess').on('filebatchuploadsuccess', function(event, data, previewId, index) 
			{
				$.app.toConsole({"fkt":"filebatchuploadsuccess", "data":data, "evt":event, "previewID":previewId, "index":index});
				
				//$(selector).fileinput('reset');
				//$(selector).fileinput('clear');
				
				if (onSuccess != undefined && typeof onSuccess == 'function'){
					onSuccess(event, data, previewId, index);
				}
				else
				{
					$.dialog.success($.lang.item("upload_success"), $.lang.item("msg_upload_success"), function(){
						
					});
				}
			});
			
			$(selector).off('filebatchuploaderror').on('filebatchuploaderror', function(event, data, previewId, index) 
			{
				$.app.toConsole({"fkt":"filebatchuploaderror", "data":data, "evt":event, "previewID":previewId, "index":index});
				
				//$(selector).fileinput('reset');
				//$(selector).fileinput('clear');
				
				if (onError != undefined && typeof onError == 'function'){
					onError(event, data, previewId, index);
				}
				else{
					$.dialog.error($.lang.item("upload_error"), $.lang.item("msg_upload_error")+":<br>"+previewId, function(){
						//$(selector).fileinput('clear');
					});
				}
			});
			
			// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
			$(selector).off('fileuploaded').on('fileuploaded', function(event, data, previewId, index) {
		        $.app.toConsole({"fkt":"fileuploaded", "data":data, "evt":event, "previewID":previewId, "index":index});
		        
		        $(selector).fileinput('reset');
		        
		        if (onSuccess != undefined && typeof onSuccess == 'function'){
					onSuccess(event, data, previewId, index);
				}
				else{
					$.dialog.success($.lang.item("upload_success"), $.lang.item("msg_upload_success"), function(){
						//$(selector).fileinput('clear');
					});
				}
		    });

			$.app.toConsole( "upload successfull initialized" );
			$.app.toConsole( {"options":options} );
			
			// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
			//..:: Additional possible events for the fileinput plugin  
			//..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
			/*  
			$(selector).off('filepreajax').on('filepreajax', function(event, previewId, index) {
		        $.app.toConsole({"fkt":'filepreajax', "event":event});
		    });
			
			$(selector).off('fileclear').on('fileclear', function(event) {
				$.app.toConsole( {"fkt":"fileclear", "event":event} );
		    });
			
			$(selector).off('filecleared').on('filecleared', function(event) {
		        $.app.toConsole( {"fkt":"filecleared", "event":event} );
		    });
			
			$(selector).off('fileloaded').on('fileloaded', function(event, file, previewId, index, reader) {
		       $.app.toConsole( {"fkt":"fileloaded", "event":event, "file":file} );
		    });
			
			$(selector).off('filereset').on('filereset', function(event) {
		        $.app.toConsole( {"fkt":"filereset", "event":event} );
		    });
			
			$(selector).off('fileimageloaded').on('fileimageloaded', function(event, previewId) {
		        $.app.toConsole( {"fkt":"fileimageloaded", "event":event} );
		    });
			
			$(selector).off('fileimagesloaded').on('fileimagesloaded', function(event) {
		        $.app.toConsole( {"fkt":"fileimagesloaded", "event":event} );
		    });
			
			$(selector).off('fileimagesresized').on('fileimagesresized', function(event) {
		        $.app.toConsole( {"fkt":"fileimagesresized", "event":event} );
		    });
			
			$(selector).off('filebrowse').on('filebrowse', function(event) {
		        $.app.toConsole( {"fkt":"filebrowse", "event":event} );
		    });
			
			$(selector).off('filebatchselected').on('filebatchselected', function(event, files) {
		        $.app.toConsole( {"fkt":"filebatchselected", "event":event, "files":files} );
		    });
			
			$(selector).off('fileselectnone').on('fileselectnone', function(event) {
		        $.app.toConsole( {"fkt":"fileselectnone", "event":event, "msg":"Huh! No files were selected."} );
		    });
			
			$(selector).off('filelock').on('filelock', function(event, filestack, extraData) {
		        var fstack = filestack.filter(function(n){ return n != undefined });
		        $.app.toConsole( {"fkt":"filelock", "event":event, "filestack":fstack, "extraData":extraData} );
		    });
			
			$(selector).off('fileunlock').on('fileunlock', function(event, filestack, extraData) {
		        var fstack = filestack.filter(function(n){ return n != undefined });
		        $.app.toConsole( {"fkt":"fileunlock", "event":event, "filestack":fstack, "extraData":extraData} );
		    });
			
			$(selector).off('filepredelete').on('filepredelete', function(event, key) {
		        $.app.toConsole( {"fkt":"filepredelete", "event":event, "key":key} );
		    });
			
			$(selector).off('filedeleted').on('filedeleted', function(event, key) {
		       $.app.toConsole( {"fkt":"filedeleted", "event":event, "key":key} );
		    });
			*/
			$.app.toConsole(" - fileinput plugin initialized");
		}
	}
};

/**
 * initialize jQuery tooltips.
 * Default selector [*[title]] will be used if no custom selector is provided.
 * 
 * @link 
 * @param string selector
 */
$.app.init_toolTips = function ()
{
	$("*[title]").tooltip({placement:"top", tooltipClass:"ui-tooltip1"});
	
	$("*[title_right]").each(function(){
		$(this).tooltip({placement:"right", title:$(this).attr("title_right"), tooltipClass:"ui-tooltip1"});
	});
	
	$("*[title_left]").each(function(){
		$(this).tooltip({placement:"left", title:$(this).attr("title_left"), tooltipClass:"ui-tooltip1"});
	});
	
	$("*[title_bottom]").each(function(){
		$(this).tooltip({placement:"bottom", title:$(this).attr("title_bottom"), tooltipClass:"ui-tooltip1"});
	});
	$.app.toConsole(" - tooltips initialized", "log");
};

/**
 * initialize select2-plugin.
 * Default selector [select] will be used if no custom selector is provided.
 * 
 * @link https://select2.github.io/
 * @param string selector
 */
$.app.init_select2 = function(selector)
{
	if(jQuery().select2) 
	{
		if (selector == undefined){
			selector = "select";
		}
		
		if ($(selector).length > 0)
		{
			$(selector).select2({
				placeholder: $.lang.please_select,
				width: "100%",
				allowClear: false,
				language: $.lang.locale,
				//theme: "classic"
				theme: "bootstrap",
				dropdownAutoWidth : true
			});
			$.app.toConsole(" - select2 plugin initialized");
		}
	}else{
		$.app.toConsole(" - select2 plugin not loaded !!!");
	}
};

/**
 * initialize tabs bootstraptoggle
 * Default selector [.toggle] will be used if no custom selector is provided.
 * 
 * @link http://www.bootstraptoggle.com/
 * @param string selector
 */
$.app.init_toggle = function(selector)
{
	if(jQuery().bootstrapToggle) 
	{
		if (selector == undefined){
			selector = ".toggle";
		}
		
		if ($(selector).length > 0)
		{
			$(selector).bootstrapToggle('destroy'); 
			$(selector).bootstrapToggle();
			$.app.toConsole(" - bootstraptoggle initialized");
		}
	}else{
		$.app.toConsole(" - toggle plugin not loaded !!!");
	}
};

//..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
//..:: DOCUMENT-READY :::::::::::::::::::::::::::::::::::::::::::::::::::::::..
//..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
$(document).ready(function()
{
	$.fx.off = true;

	$.app.toConsole("app.js ready - "+$.app.generateUUID(), "log");
	
	$.app.unblockUI("body");
	
	$('.modal').remove();
	
	
	//..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// Storage
	try{
		//$.app.sessionStorage 		= sessionStorage;
		//$.app.localStorage 		= localStorage;
		var key
		$.app.localStorage = {};
		for(var i =0; i < localStorage.length; i++){
			
			key = localStorage.key(i)
			$.app.localStorage[key] = localStorage.getItem(key) ;
		}
		
		
		$.app.toConsole({"sessionStorage":$.app.sessionStorage});
		$.app.toConsole({"localStorage":$.app.localStorage});
		
	}catch(e){
		$.app.toConsole({"app.js >> catch":e});
	}
	
	//..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// set js support to enabled
	$.post(baseUrl+"home/js", {"storage":$.app.localStorage}, function( data ) 
	{
		$.app.toConsole(" - js support written to session", "log");
		
		// remove attributes and classes which indicate that js is not present
		$("body").attr("has-js", "1");
		$("html, body").removeClass("no-js");
		$("#hint_nojs").remove();
	});
	
	$(window).bind("popstate", function (event) {
		$.app.toConsole("popstate event catched. location.href["+location.href+"]", "log");
		$.app.redirect(location.href)
	});
	
	//..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// Pull contxtual colors out of the buttons css and store it in $.app.colors
	var tmp_btn 		= $('<input id="tmp_btn" type="button" />');
	$("body").append( tmp_btn );
	
	$.each( $.app.colors, function( key, value ) {
		if (key == "standard"){
			key == "default";
		}
		$(tmp_btn).addClass("btn-"+key);
		$.app.colors[key] = $('#tmp_btn').css("background-color");
		$(tmp_btn).removeClass("btn-"+key);
	});
	$("#tmp_btn").remove();
	
	//..::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// Append style to the head after we know our contextual colors
	$('head').append('<style>.footer-toggle-log::before{background:'+$.app.colors["primary"]+' !important;}</style>');
	

	//..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// determinate the bootstrap screensize (xs, sm, md, lg) and write it to '$.app.screensize_class'
	$.app.screensize_class = $(".device-size:visible").attr("size");
	
	// register a resize event globally
	$(window).resize(function () 
	{
		// update the screensize_class
		$.app.screensize_class = $(".device-size:visible").attr("size");		
		
		$.app.toConsole({"fkt":"resize", "class":$.app.screensize_class}, "log");
		
		// check if a resize callback has been injected and call it
		if($.app.callback_resize != undefined && typeof $.app.callback_resize == 'function'){
			$.app.callback_resize();
		}
	});
	
	//..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// Datatable global defaults and extensions
	if ($.fn.DataTable !== undefined)
	{
		// default datatable options
		$.extend( $.fn.dataTable.defaults, $.app.datatable.options);
		
		// set the right l18n as default
		$.extend( true, $.fn.dataTable.defaults, {
			"language": JSON.parse($.lang.item("datatable"))
		}); 
		
		// limit the amount of shown numbers in the pager
		$.fn.DataTable.ext.pager.numbers_length = 5; 
		
		// custom ordering for input columns in dataTable
		$.fn.dataTable.ext.order['dom-input'] = function (settings, col) {
	        return this.api().column(col, { order: 'index' }).nodes().map(function (td, i) {
	            return $('input', td).val();
	        });
	    };
	    
	    // custom ordering for select columns in dataTable
	    $.fn.dataTable.ext.order['dom-select'] = function (settings, col) {
	        return this.api().column(col, { order: 'index' }).nodes().map(function (td, i) {
	            return $('select option:selected', td).val();
	        });
	    };
		
		// monkey patch dataTables buttons, so we can specifiy the target container for the button
		var org_buildButton = $.fn.DataTable.Buttons.prototype._buildButton;
		
		$.fn.DataTable.Buttons.prototype._buildButton = function(config, collectionButton) 
		{
			var button = org_buildButton.apply(this, arguments);
			
			$(document).one('init.dt', function(e, settings, json) 
			{
				if (config.container && $(config.container).length) {
					$(button.inserter[0]).detach().appendTo(config.container);
				}
		    });    
		    return button;
		 }
	}
	
	//..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// Autonumeric defaults
	if ($.fn.autoNumeric !== undefined)
	{
		$.extend($.fn.autoNumeric.defaults, {              
			aSep 	: $.lang.item('thousands_seperator'),
			aDec 	: $.lang.item('decimal_seperator'),
			dGroup 	: '3',									//controls the digital grouping - the placement of the thousand separator
			aSign 	: '', 									// symbol
			pSign	: 'p'									// symbol position (p | s)     
		});
	}
	
	//..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// Proxy function to extend jQuery's serializeArray method with an option to include disabled inputs
	(function($){
	    var proxy = $.fn.serializeArray;
	    $.fn.serializeArray = function(incl_disabled){
	        
	    	if (incl_disabled === true){
	    		var inputs = this.find(':disabled');
	    		inputs.prop('disabled', false);
	    		var serialized = proxy.apply( this, arguments );
	    		inputs.prop('disabled', true);
	    	}
	    	else{
	    		return proxy.apply( this, arguments );
	    	}
	        return serialized;
	    };
	})(jQuery);
	
	//..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// Moment defaults
	moment.locale( $.lang.item("app_locale_short").toLowerCase() );
	moment.defaultFormat = $.lang.item("date_format_long").toUpperCase();
	
	//..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// blockUI defaults
	$.blockUI.defaults = {
		message:  '<h1>'+$.lang.item('msg_one_moment_please')+'</h1>',
		title: $.lang.item('please_wait'),
		draggable: true,
		theme: false,
		cursorReset: 'default',
		iframeSrc: /^https/i.test(window.location.href || '') ? 'javascript:false' : 'about:blank',
		forceIframe: false,
		baseZ: 10000,
		centerX: true,
		centerY: true,
		allowBodyStretch: true,
		bindEvents: true,
		constrainTabKey: true,
		fadeIn:  200,
		fadeOut:  400,
		timeout: 30000,
		showOverlay: true,
		focusInput: true,
        focusableElements: ':input:enabled:visible',
		onBlock: null,
		onUnblock: null,
		onOverlayClick: null,
		quirksmodeOffsetHack: 4,
		blockMsgClass: 'blockMsg',
		ignoreIfBlocked: false
	};
	$.blockUI.defaults.css = {}
	
	
	//..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// Plugin initialization
	$.app.init_toolTips();
	
	//$.app.initialize();
	
});