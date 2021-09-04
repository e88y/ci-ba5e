<?php 
$max_tiles_per_row 	= 4;
$col_size 			= intval(12 / $max_tiles_per_row);

$output = '<div class="row">';

foreach ($data["tile_data"] as $index => $tiledata)
{
	$tile = new HTML_TileBox("tile_$index", $tiledata["count"], $tiledata["text"], $tiledata["bgColor"], $tiledata["icon"]);
	$tile->setFooter( $tile->generateFooterLink("tile_bt_$index", lang("show_more"), $tiledata["url"], E_ICONS::ARROW_CIRCLE_RIGHT_BLACK ) );
	$tile->setLink($tiledata["url"]);
	
	// 8 % 4 == 0
	if ( ($index) % $max_tiles_per_row == 0 ) 
	{
		$output .= '</div><div class="row">';
	}
	$output .= '<div class="col-sm-'.$col_size.'">'.$tile->generateHTML().'</div>';
}
$output .= '</div>';

?>
<div class="row">
	<div class="col-xs-12">
		<?php echo $output; ?>
	</div>
</div>