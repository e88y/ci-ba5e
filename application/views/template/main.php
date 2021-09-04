<?php 
	/**
	 * This templates brings all parts together which will be placed within the "body-tag" by the skeleton template
	 * 
	 * - The template for the top navigation a.k.a the menu (template/menu)
	 * - The main container
	 *   - the page heading
	 *   - the breadcrump
	 *   - the content body which is filled with the view, we want to show
	 * - The footer template (templates/footer)
	 * 
	 * @author Marco Eberhardt
	 * @category View
	 * @package application\views\template\main
	 * @version 1.1
	 */
?>
<?php 
	$cls_hasNav 		= ($hasNav === true && $this->config->item('navbar_fixed') == 1 ? 'has-fixed-nav' : '');			// will be recognized by global.css (ad padding when fixed)
	$cls_hasFooter 		= ($hasFooter === true && $this->config->item('footer_fixed') == 1 ? 'has-footer' : '');	// will be recognized by global.css (ad padding when fixed)
	$cls_hasSidebar 	= ($hasSidebar === true ? 'has-sidebar' : '');	// will be recognized by global.css
	
	$cls_app_container 	= ($sidebar_expanded == 1 ? ' expanded': '' );
	$cls_app_container .= $bg_class;
	
	
	$cls_main_container = ($this->config->item('fluid_layout') ? "container-fluid" : "container");
	$cls_sidebody		= "side-body side-body-".$this->config->item('sidebar_location');
	
?>
<div id="app-container" class="app-container<?php echo $cls_app_container; ?>">
	<div class="row content-container">
		
		<?php if (isset($menu)){ echo $menu; }?>
		<?php if (isset($sidebar)){ echo $sidebar; }?>
		
		<!-- Main container -->
		<div class="<?php echo $cls_main_container; ?>">
			
			<div class="<?php echo $cls_sidebody; ?>">
				<?php 
					if (isset($pageHeading)){ 
						echo $pageHeading;
					}
					if (isset($breadcrump)){
						echo $breadcrump;
					}
				?>
				<div id="content-body" role="main">
					<?php 
						if (isset($content_body)){
							echo $content_body;
						}
					?>
					<?php 
						//echome("Expand [".$bg_class."]");
						//echo nl2br(print_r($_SESSION, true));
					?>			
				</div>
			</div>
		</div>
		<?php if (isset($footer)){echo $footer;} ?>
	</div>
	
	<div class="device-size device-xs visible-xs" size="xs"></div>
	<div class="device-size device-sm visible-sm" size="sm"></div>
	<div class="device-size device-md visible-md" size="md"></div>
	<div class="device-size device-lg visible-lg" size="lg"></div>
	
</div>