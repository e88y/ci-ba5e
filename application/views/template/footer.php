<?php
	$lbl_client_id 	= new HTML_Label("lbl_client_id", "ID: ".$data["client_id"], E_COLOR::INFO, E_SIZES::XS);
	$lbl_js 		= new HTML_Label("lbl_js", "JS: ".strval($js_enabled), E_COLOR::INFO, E_SIZES::XS);
	$lbl_lang 		= new HTML_Label("lbl_lng", "LNG: ".strval($loaded_language), E_COLOR::INFO, E_SIZES::XS);

	$goto_top		= '<a href="#" class="go-top btn btn-xs btn-default">'.E_ICONS::CHEVRON_UP.'</a>';
	$cls_container	= ($this->config->item('fluid_footer') == 1 ? 'container-fluid' : 'container' );
	
	$navbarClass	= "navbar".($hasSidebar === true ? " has-sidebar" : "");
	$navbarClass	.=  ($this->config->item('footer_inverse') == 1 ? " navbar-inverse" : " navbar-default");
	$navbarClass	.=  ($this->config->item('footer_fixed') == 1 ? " navbar-fixed-".$this->config->item('footer_location') : " ");
	
	/**
	 * 
	 * 
	 */
?>
	
<div class="app-footer <?php echo $navbarClass;?>" name="footer" role="banner">
	
	<?php if ($this->config->item('footer_console') == 1) :?>
	
	<div class="footer-toggle-log"></div>
	<p id="log-console" class="collapse log-console" style="display: none;" >
		<?php echo str_repeat(E_LOREM::IPSUM_STRING, 12); ?>
	</p>
	
	<?php endif;?>
	
	
	<div class="wrapper footer-wrapper <?php echo $cls_container?>">
		<span class="navbar-text pull-right">
			<?php echo $goto_top; ?>
		</span>
		
		<span class="navbar-text pull-right">
			<?php 
				if (ENVIRONMENT !== E_ENVIRONMENT::PRODUCTION)
				{
					echo $lbl_js->generateHTML()."&nbsp;".$lbl_lang->generateHTML()."&nbsp;".$lbl_client_id->generateHTML(); 
				}
			?>
		</span>		
		
		<p class="navbar-text text-muted"><?php echo $this->config->item('site_name')."&nbsp;". $this->config->item('site_copyright'); ?>
		
		<?php 
			if (ENVIRONMENT !== E_ENVIRONMENT::PRODUCTION)
			{
				$more  = '<br><strong class="'.E_BG_COLOR::RED.'">'. strtoupper(ENVIRONMENT) . '</strong> | ';
				$more .= 'CI-Version <strong>'. CI_VERSION . '</strong> | ';
				$more .= 'PHP <strong>'.phpversion().'</strong> | ';
				$more .= 'Elapsed time <strong>{elapsed_time}</strong> | ';
				$more .= $_SERVER['SERVER_SOFTWARE'];
				echo $more;
			}
		?>
		</p>
	</div>
</div>