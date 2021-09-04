if (typeof jQuery === 'undefined') { 
	throw new Error('This JavaScript requires jQuery'); 
}

$.footer = {
		
};

$(document).ready(function()
{
	$.app.toConsole("footer.js ready", "log");
	
	$(".footer-toggle-log").on("click", function(){
		
		$(".side-body").toggleClass("footer-expanded");
		$(".app-footer").toggleClass("footer-expanded");
		$("#log-console").toggle();
		
		$("#log-console").niceScroll({
			cursorcolor:"#0090C8"
		});
	})
	
});