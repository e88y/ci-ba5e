<?php 
	/**
	 * skeleton template view
	 *
	 * @author Marco Eberhardt
	 * @category View
	 * @package application\views\template\menu
	 * @version 1.0
	 * 
	 * This VIEW is the skeleton to generate the full page for a "FULLPAGE"-rendered VIEW 
	 * All single template snipplets come together here 
	 * 
	 * @see BASE_Controller  
	 */
	$stamp 			= (ENVIRONMENT == E_ENVIRONMENT::PRODUCTION ? "?".time() : "");	
	$min			= (ENVIRONMENT == E_ENVIRONMENT::PRODUCTION ? ".min" : ""); 	// Use minified css and js files on production environment
	$cdn			= $this->config->item('cdn');
	$lang			= (isset($_SESSION[E_SESSION_ITEM::USER_LANGUAGE]) ? strtolower($_SESSION[E_SESSION_ITEM::USER_LANGUAGE]) : "de");
	$cache_control	= E_CACHE_CONTROL::NO_CACHE.",".E_CACHE_CONTROL::NO_STORE.",".E_CACHE_CONTROL::MUST_REVALIDATE.",".E_CACHE_CONTROL::MAX_AGE(3600);
	
	//$subdomain		= $this->config->item('subdomain');
	//$subdomain_s	= ($subdomain != "" ? $subdomain."/" : "");
	$theme 			= (! isset($theme) ? E_THEMES::STANDARD : $theme);
	
	$cls_body		= ($_SESSION[E_SESSION_ITEM::JS_ENABLED] == false ? 'no-js ':'');
	$cls_body		.= ($has_bg == 1 ? 'has-bg':'');
	
	/**
	 * @todo 
	 * check inclusion orders and dependencies 
	 * Some scripts need to follow a specific order.
	 * For example Bootstrap Tooltip don't like jQuery UI (there are a few names conflict and 'tooltip' is one of them).
	 * So jquery-ui first, bootstrap after
	 * 
	 */
	//$plugin_js		= correctScripIncludeOrder($plugin_js);
?>
<!doctype html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> 
<html class="<?php echo $cls_body;?>" lang="<?php echo $lang;?>"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta http-equiv="expires" content="0">
	<meta http-equiv="Cache-Control" content="<?php echo $cache_control;?>">
	<meta http-equiv="pragma" content="<?php echo $cache_control;?>">
	<meta http-equiv="content-language" content="<?php echo $loaded_language;?>" />
	
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
	<meta name="description" content="<?php echo $description ?>" />
	<meta name="keywords" content="<?php echo $keywords ?>" />
	<meta name="robots" content="noindex,nofollow">
	<meta name="author" content="<?php echo $author ?>" />
	<meta name="date" content="<?php echo date("c");?>"/>
	<?php 
	/**
		<meta name="keywords" lang="de" content="Ferien, Griechenland, Sonnenschein"> 
		<meta name="keywords" lang="en-us" content="vacation, Greece, sunshine"> 
		<meta name="keywords" lang="en" content="holiday, Greece, sunshine"> 
		<meta name="keywords" lang="fr" content="vacances, Grèce, soleil">
	 */
	?>
	<title><?php echo $title ?></title>
	
	<link rel="shortcut icon" href="<?php echo base_url(PATH_IMAGES.'app_icons/'.$subdomain_s.'favicon.ico');?>">
	<link rel="apple-touch-icon" href="<?php echo base_url(PATH_IMAGES.'app_icons/'.$subdomain_s.'apple-touch-icon-57x57.png');?>">
	<link rel="apple-touch-icon" sizes="57x57" href="<?php echo base_url(PATH_IMAGES.'app_icons/'.$subdomain_s.'apple-touch-icon-57x57.png');?>">	
	<link rel="apple-touch-icon" sizes="72x72" href="<?php echo base_url(PATH_IMAGES.'app_icons/'.$subdomain_s.'apple-touch-icon-72x72.png');?>">
	<link rel="apple-touch-icon" sizes="114x114" href="<?php echo base_url(PATH_IMAGES.'app_icons/'.$subdomain_s.'apple-touch-icon-114x114.png');?>">

	<link rel="stylesheet" href="<?php echo $cdn."bootstrap/3.3.7/css/".$theme.$stamp;?>">
	<link rel="stylesheet" href="<?php echo base_url(PATH_CSS."bootstrap-customs.css").$stamp;?>">
	<link rel="stylesheet" href="<?php echo base_url(PATH_CSS."customs.css").$stamp;?>">
	<link rel="stylesheet" href="<?php echo base_url(PATH_CSS."global.css").$stamp;?>">
	<?php if ($hasNav === true): ?>
	<link href="<?php echo base_url(PATH_CSS."navbar.css").$stamp;?>" rel="stylesheet">	
	<?php endif; ?>
	
	<?php if ($hasSidebar === true): ?>
	<link href="<?php echo base_url(PATH_CSS."sidebar.css").$stamp;?>" rel="stylesheet">	
	<?php endif; ?>
	
	<?php // styles for the plugins located at the content delivery network  ?>
	<?php foreach($plugins_css as $style):?>
	<link rel="stylesheet" href="<?php echo $cdn.str_replace("{min}", $min, $style).$stamp; ?>">
	<?php endforeach;?>
	
	<?php // additional custom css and font files defined by a controller - usually they are empty as we use our own cdn ?>
	<?php foreach($css as $c):?>
	<link rel="stylesheet" href="<?php echo base_url().PATH_CSS.$c ?>">
	<?php endforeach;?>

	<?php // additional font files defined by the controller - usually they are empty ?>	
	<?php foreach($fonts as $f):?>
	<link href="<?php echo base_url().PATH_FONTS.$f; ?>>" rel="stylesheet" type="text/css">
	<?php endforeach;?>
</head>
<body class="document <?php echo $cls_body;?>" has-js="<?php echo ($_SESSION[E_SESSION_ITEM::JS_ENABLED] == false ? '0':'1');?>">
	<?php echo $body; ?> 
	
	<?php foreach($plugins_js as $plugin): ?>
	<script src="<?php echo $cdn.str_replace("{min}", $min, $plugin); ?>"></script>
	<?php endforeach;?>
	<?php //echo get_base_js();?>
	
	<script>
		
		var baseUrl 	= "<?php echo base_url(); ?>";
		var env			= "<?php echo ENVIRONMENT; ?>";
		var console_on	= "<?php echo $this->config->item('enable_console_log'); ?>";
		
		$.lang = {
			locale		: "<?php echo $this->config->item('language'); ?>",
			language 	: <?php echo json_encode($this->lang->language);?>,
			item : function(languageItem)
			{
				var i = $.lang.language[languageItem];
				if (i == undefined || i == ""){
					i = languageItem;
				}
				return i;
			} 
		}
	</script>
	<script src="<?php echo base_url(PATH_JS."generic/app.js");?>"></script>
	<script src="<?php echo base_url(PATH_JS."generic/dialogs.js");?>"></script>
	
	<?php if ($hasNav === true): ?>
	<script src="<?php echo base_url(PATH_JS."generic/menu.js");?>"></script>
	<?php endif; ?>
	
	<?php if ($hasSidebar === true): ?>
	<script src="<?php echo base_url(PATH_JS."generic/sidebar.js");?>"></script>	
	<?php endif; ?>
	
	<?php if ($hasFooter === true): ?>
	<script src="<?php echo base_url(PATH_JS."generic/footer.js");?>"></script>
	<?php endif; ?>
	
	<?php foreach($javascript as $js):?>
	<script src="<?php echo base_url().PATH_JS.$js?>"></script>
	<?php endforeach;?>
	
	<?php if ($this->config->item('enable_tracker_code') === 1): ?>
		<!-- INISERT TRACKER XCODE HERE -->
		
	<?php endif; ?>
	
	<noscript>
		<?php 
			$js_warning = new HTML_Alert("hint_nojs", lang("missing_javascript"), lang("hint_missing_javascript"), E_COLOR::DANGER, E_DISMISSABLE::NO);
			echo $js_warning->generateHTML();
		?>
	</noscript>
</body>
</html>