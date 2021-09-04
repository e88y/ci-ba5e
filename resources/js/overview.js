if (typeof jQuery === 'undefined') { 
	throw new Error('This JavaScript requires jQuery'); 
}

$.overview = {
	options : {
		opt:false
	},
	table : null
};


$(document).ready(function()
{
	$.app.toConsole("overview.js ready", "log");
});







