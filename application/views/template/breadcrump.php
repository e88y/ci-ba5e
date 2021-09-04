<?php
	/**
	 * Breadcrump template view 
	 * @see page_helper
	 *
	 * @author Marco Eberhardt
	 * @category View
	 * @package application\views\template\main
	 * @version 1.1
	 */
	$last = "";
	if (isset($last_item) && $last_item != ""){
		$last = $last_item;
	}else if (isset($data["last_item"]) && $data["last_item"] != ""){
		$last = $data["last_item"];
	}
?>
<div class="row breadcrump-row">
	<div class="col-xs-12">
		<?php echo buildBreadcrumb($last);?>
	</div>
</div>