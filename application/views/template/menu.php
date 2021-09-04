<?php 
	/**
	 * menu template view
	 *
	 * @author Marco Eberhardt
	 * @category View
	 * @package application\views\template\menu
	 * @version 1.0
	 */
?>
<?php
	$containerClass = ($this->config->item('fluid_header') == 1 ? "container-fluid" : "container");
	
	$navbarClass	 = "navbar ". ($hasSidebar === true ? " has-sidebar " : "");
	$navbarClass	.= "navbar-".$this->config->item('navbar_location');
	$navbarClass	.=  ($this->config->item('navbar_inverse') == 1 ? " navbar-inverse" : " navbar-default");
	$navbarClass	.=  ($this->config->item('navbar_fixed') == 1 ? " navbar-fixed-".$this->config->item('navbar_location') : " navbar-static-".$this->config->item('navbar_location') );

?>
<nav id="topnav" class="<?php echo $navbarClass;?>">
	<div class="<?php echo $containerClass;?>">
	
		<div class="navbar-header">
			<?php if ($hasSidebar === true): ?>
			<button type="button" class="navbar-expand-toggle btn btn-md">
				<i class="fa fa-ellipsis-v icon"></i>
			</button>
			<?php endif; ?>
			
			<?php if ($hasSidebar === false): ?>
			<a class="navbar-brand hidden-xs" href="<?php echo base_url("home");?>">
				<span>
					<img alt="site_logo" style="height:60px" src="<?php echo HTML_Image::generateDataURIFromImage($this->config->item('site_logo'))?>">
				</span> 
			</a>
			<?php endif; ?>
			
			<button type="button" class="navbar-right-expand-toggle pull-right visible-xs">
				<i class="fa fa-ellipsis-v icon"></i>
			</button>
			
			<ul class="nav navbar-nav">
				<?php echo buildMenuItems($menu_default);?>
			</ul>
		</div>
		
		<ul class="nav navbar-nav navbar-default navbar-left expanded">

		</ul>
		
		<ul class="nav navbar-nav navbar-default navbar-right expanded">

			<button type="button" class="navbar-right-expand-toggle pull-right visible-xs fa-rotate-90">
				<?php echo E_ICONS::TIMES;?>
			</button>
			
			<?php 
				if ($this->session->userdata(E_SESSION_ITEM::LOGGED_IN) === true && strtolower($caller_class) != "logout")
				{
					//echo buildLocalizationsMenu($menu_localizations);
					
					echo buildUserMenu($this->session->userdata);
					echo buildLogoutButton();
					//echo buildSettingsDropdown($settings);
				}
				else 
				{
					//echo buildLocalizationsMenu($menu_localizations);
					echo buildNavbarLoginForm();
				}
			?>
		</ul>
	</div>
</nav>