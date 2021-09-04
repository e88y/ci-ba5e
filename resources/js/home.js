if (typeof jQuery === 'undefined') { 
	throw new Error('This JavaScript requires jQuery'); 
}

$.home = {
};

$(document).ready(function()
{
	$.app.toConsole("home.js ready location["+window.location.href+"] baseURL["+baseUrl+"]", "log");

	var location = String(window.location.href).toLowerCase();
	
	var apply_backstretch_on = [
		String(baseUrl+"home/").toLowerCase(), 
		String(baseUrl+"home").toLowerCase(),
		String(baseUrl).toLowerCase()
	];
	
	if (jQuery.inArray(location, apply_backstretch_on) !== -1)
	{
		var images = [
			$.app.baseUrl+"/resources/img/backgrounds/home/00.jpg",
  			$.app.baseUrl+"/resources/img/backgrounds/home/01.jpg",
  			$.app.baseUrl+"/resources/img/backgrounds/home/02.jpg",
  			$.app.baseUrl+"/resources/img/backgrounds/home/03.jpg"
  		];
  		$.backstretch(images, {duration: 5000, fade: 1500});
	}
	
	
});