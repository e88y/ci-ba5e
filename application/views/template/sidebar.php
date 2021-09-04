<?php
/**
 * sidebar template view
 * 
 * @author Marco Eberhardt
 * @category View
 * @package application\views\template\sidebar
 * @version 1.2
 */

$sidebar_expanded 		= $sidebar_expanded;
$sidebar_selected_item 	= $sidebar_selected_item;

$sidebarClass	= "side-menu side-menu-".$this->config->item('sidebar_location');
$sidebarClass	.=  ($this->config->item('sidebar_inverse') == 1 ? " sidebar-inverse navbar-inverse" : " sidebar-default navbar-default");
$sidebarClass	.=  ($this->config->item('sidebar_fixed') == 1 ? " sidebar-fixed " : " sidebar-static ");

//$navbarClass	= "side-menu";
//$navbarClass	.=  ($this->config->item('sidebar_inverse') == 1 ? " navbar-inverse" : " navbar-default");

write2Debugfile("sidebar-view.log", "build sidemenu expanded[$sidebar_expanded] selected-item[$sidebar_selected_item] => loop [".count($menu_data)."] URI[".$this->uri->uri_string."]", false);
//write2Debugfile("sidebar-view.log", "\n - menu-data ".print_r($menu_data, true), false);
// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
// ..:: build menu
// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
$menu_list = '<ul class="nav navbar-nav">';

if (is_array($menu_data) && count($menu_data) > 0)
{
	foreach ($menu_data as $root_node)
	{
		write2Debugfile("sidebar-view.log", "\nroot-node [".$root_node["menu_id"]."]  with [".count($root_node["items"])."] items \n".print_r($root_node, true));
	
		$items 		= $root_node["items"];
	
		if ("#".$root_node["menu_id"] == $sidebar_selected_item){
			$root_node["active"] = "active";
			
			write2Debugfile("sidebar-view.log", " - expand node");
		}
	
	
		if (count($root_node["items"]) > 0)
		{
			//write2Debugfile("sidebar-view.log", " - items-".print_r($root_node["items"], true));
			// '.$root_node["active"].'
			$menu_list .= '
			<li class="panel panel-default dropdown">	
			<a href="'.base_url($root_node["menu_ref"]).'" class="sidebar-root-node '.($root_node["active"] == "" ? 'collapsed':' ').'" data-toggle="collapse" data-target="#'.$root_node["menu_id"].'">
				<span class="'.$root_node["menu_icon"].' icon"></span><span class="title">'.lang($root_node["menu_label"]).'</span>
			</a>';
				
			// <!-- Dropdown level 1 -->	
			$collapse = ($data["js_enabled"] && $root_node["active"] != "" ? 'collapse in':'collapse'); 
			$menu_list .= '
			<div id="'.$root_node["menu_id"].'" class="panel-collapse '.$collapse.'">
				<div class="panel-body">
					<ul class="nav navbar-nav">';
				
			foreach ($root_node["items"] as $key => $item) 
			{
				write2Debugfile("sidebar-view.log", " - processing item [$key] menu-ref[".$item->menu_ref."] uri[".$this->uri->uri_string."]");
				
				$item->active = "";
				if (trim($this->uri->uri_string)."/" == trim($item->menu_ref)){
					//$item->active = "active";
				}
	
				$menu_list .= '<li class="'.$item->active.'"><a href="'.base_url().$item->menu_ref.'" class="sidebar-child-node"><i class="'.$item->menu_icon.' icon"></i>'.lang($item->menu_label).'</a></li>';
			}
				
			$menu_list .= '</ul></div></div></li>';
		}
		else
		{
			write2Debugfile("sidebar-view.log", " - solo-node ");
			$menu_list .= '<li class="'.$root_node["active"].'"><a href="'.base_url().$root_node["menu_ref"].'" class="sidebar-solo-node"><span class="'.$root_node["menu_icon"].' icon"></span><span class="title">'.lang($root_node["menu_label"]).'</span></a></li>';
		}
	}
}

/*

$menu_list .= '<li><span class="divider"></span</li>
<li>
	<a href="'.base_url().'home/contact">
	<span class="icon fa fa-thumbs-o-up"></span><span class="title">'.lang("#menu_contact").'</span></a> 
</li>';

$menu_list .= '
<li>
	<a href="'.base_url("support").'">
	<span class="icon fa fa-support"></span><span class="title">'.lang("#menu_support").'</span></a>
</li>';
 */
$menu_list .= '</ul>';

write2Debugfile("sidebar-view.log", "");
//<div class="side-menu sidebar-default">
?>
<div class="<?php echo $sidebarClass; ?>">
	<div class="navbar navbar-default" name="sidebar" role="contentinfo">
		<div class="side-menu-container">
			<div class="sidebar-header navbar-header">
				<a class="sidebar-brand navbar-brand" href="<?php echo base_url("home");?>">
					<img alt="logo" width="auto" height="28px" src="<?php echo HTML_Image::generateDataURIFromImage($this->config->item('site_logo'));?>">
				</a>
				
			</div>
			
			<?php  echo $menu_list;?>
		</div><!-- /.navbar-collapse -->
	</div>
</div>