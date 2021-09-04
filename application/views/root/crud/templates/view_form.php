<?php 

	$form_str 		= "".'$form = new HTML_Form("form_'.$classname.'", "'.$classname.'", "#", lang("data_common"));'."\n\t";
	$form_items_str = "";
	
	foreach ($cdata as $columnnname => $value) 
	{
		if ($columnnname == "client_id" || $columnnname == $classname."_id"){
			continue;
		}
		
		$type 		= strtolower($value["input_type"]);
		$required 	= $value["required"];
		$enabled	= $value["enabled"];
		
		$fiid 		= "fi_".$columnnname;
		$comid		= "i_".$columnnname;
		
		if ($value["hidden"] == 1){
			continue;
		}
		
		
		$form_items_str .= "// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..\n\t";
		
		switch ($type) {
			case "select":
				$input_options = '$input_options = array(';
				if (is_array($value["input_options"]) && count($value["input_options"]) > 0)
				{
					foreach ($value["input_options"] as $option) {
						$input_options .= "\n\t\t".'"'.$option.'",';
					}
				}
					
				$input_options = substr($input_options, 0, -1);
				$input_options .= "\n\t);";
				
				
				$form_items_str .= $input_options."\n\n\t".'$opt = array();'."\n \t".
				'foreach ($input_options as $key => $value) {'."\n\t\t".
					'$opt[] = array('."\n \t\t\t".
						'"label"=>$value,'."\n\t\t\t".
						'"key"=>$value'."\n\t\t".
					');'."\n\t".
				'}'."\n\n\t";
				
			  	$form_items_str .= '$'.$comid.' = new HTML_Select("i_'.$columnnname.'", "'.$columnnname.'", HTML_Select::buildOptions($opt, "key", "label", $'.$classname.'->'.$columnnname.', "all", false), false, "", E_VISIBLE::YES );'."\n\t";
				break;
			
			case "textarea":
				$form_items_str .= '$'.$comid.' = new HTML_TextArea("i_'.$columnnname.'", "'.$columnnname.'", $'.$classname.'->'.$columnnname.');'."\n\t";
				break;
				
			case "text":
				$form_items_str .= '$'.$comid.' = new HTML_Input("i_'.$columnnname.'", "'.$columnnname.'", E_INPUTTYPE::TEXT, "", $'.$classname.'->'.$columnnname.');'."\n\t";
				break;
		}
		if ($enabled == false){
			$form_items_str .= '$'.$comid.'->setEnabled(E_ENABLED::NO);'."\n\t";
		}
		
		$form_items_str .= '$'.$fiid.' = new HTML_FormItem(lang("'.$columnnname.'"), "'.$fiid.'", "");'."\n\t";
		$form_items_str .= '$'.$fiid.'->setValidationState( form_error("'.$columnnname.'") != "" ? E_VALIDATION_STATES::HAS_ERROR : E_VALIDATION_STATES::NONE);'."\n\t";
		$form_items_str .= '$'.$fiid.'->addComponent( $'.$comid.' );'."\n\n\t";
		
		$form_str .= '$form->addFormItem($'.$fiid.');'."\n\t";
	}
	$form_str .= '$form->addFormItem($hidden_'.$classname.'_id);'."\n\t";
	$form_str .= '$form->addFormItem($hidden_'.$classname.'_name);'."\n\t";
	$form_str .= '$form->addFormItem($hidden_save);'."\n\t";
	
	

	$str = '<?php 
	
	$page_alerts = buildPageAlerts($error, $success, $warning, $info);

	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// ..:: create value object	
	$'.$classname.' = new T_'.ucfirst($classname).'($data["'.$classname.'"]);

	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// ..:: build the '.$classname.' form
	'.$form_items_str.' 
			
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	$btn_submit = new HTML_Button("bt_submit", "save", lang("save"), E_COLOR::PRIMARY, E_SIZES::STANDARD, E_ICONS::SAVE, "left", E_VISIBLE::YES, E_ENABLED::YES, array(), array(), array());
	$btn_submit->setAttributes(array("form"=>"form_'.$classname.'")); 
	$btn_submit->setType(E_BUTTON_TYPES::SUBMIT);
	
	$btn_reset = new HTML_Button("bt_reset", "reset", lang("undo"), E_COLOR::INFO, E_SIZES::STANDARD, E_ICONS::UNDO, "left", E_VISIBLE::YES, E_ENABLED::YES, array(), array(), array());
	$btn_reset->setAttributes(array("form"=>"form_'.$classname.'"));
	$btn_reset->setType(E_BUTTON_TYPES::RESET);
	
	$fi_submit 	= new HTML_FormItem("", "fi_submit", "submit");
	$fi_submit->addComponent( $btn_submit );
	
	$hidden_'.$classname.'_id 		= new HTML_Input("i_'.$classname.'_id", "'.$classname.'_id", E_INPUTTYPE::HIDDEN, lang("'.$classname.'_id"), $'.$classname.'->'.$classname.'_id) ;
	$hidden_'.$classname.'_name 	= new HTML_Input("i_'.$classname.'_name", "'.$classname.'_name_orig", E_INPUTTYPE::HIDDEN, lang("'.$classname.'_name"), $'.$classname.'->'.$classname.'_name) ;
	$hidden_save 					= new HTML_Input("i_save", "save", E_INPUTTYPE::HIDDEN, lang("save"), 1) ;
			
	'.$form_str.'
			
			
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// ..:: put all forms together
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	/*
	$full_form = new HTML_Form("form_'.$classname.'", "form_'.$classname.'", base_url("admin/'.$classname.'/").($'.$classname.'->'.$classname.'_id == "" ? "create":"edit"), "", E_FORMMETHOD::POST, E_VISIBLE::YES, E_ENABLED::YES, E_FORMLAYOUT::HORIZONTAL, array(), array(), array());
	//$full_form->setAttributes(array("enctype"=>"multipart/form-data"));
	$full_form->addFormItem(
		$page_alerts.\' 
		<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">\'.
			$form->generateHTML(true).\'
		</div>
		<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">\'.
			
		</div>\'
	);
	*/
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// ..:: output panel
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	$panel 	= new HTML_Panel("pnl_'.$classname.'", ($'.$classname.'->'.$classname.'_id != "" ? lang("'.$classname.'_edit"):lang("'.$classname.'_create")) , "", date("d.m.Y"), E_DISMISSABLE::NO, E_VISIBLE::YES, E_COLOR::STANDARD, E_COLOR::STANDARD);
	$panel->setContent($page_alerts.$form->generateHTML());
	$panel->setFooter($btn_submit->generateHTML()."&nbsp;".$btn_reset->generateHTML());
	
?>
<div class="row">
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-10">
		<?php echo $panel->generateHTML(); ?>
	</div>
</div>
<div class="row">
	<?php // echo nl2br(print_r($data, true));?>
</div>
';
echo $str;

?>

