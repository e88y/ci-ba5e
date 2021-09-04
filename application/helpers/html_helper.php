<?php
/**
 * This class helps us generating HTML-Markup
 *  
 * @category helper
 * @package application\helper\HTML_helper
 * @version 1.0
 */
class HTML
{
	const DEBUG_FILENAME 			= "HTML.log";

	protected $cls_draggable		= " draggable";
	protected $attributeBlacklist	= array("id", "name", "class", "style");
	protected $defaultStyles		= array();
	protected $defaultClasses		= array();
	
	protected $id 					= "";
	protected $name 				= "";
	protected $title 				= "";
	protected $visible 				= true;
	protected $enabled 				= true;
	protected $draggable			= false;

	public $styles					= array();
	public $classes					= array();
	public $attributes 				= array();

	// tmp
	protected $styleString			= "";
	protected $classString			= "";
	protected $attrString			= "";


	/**
	 * Base construct for all HTML-Elemets
	 *
	 * @param string $id			>> Id
	 * @param string $name			>> Name
	 * @param E_VISIBLE $visible	>>
	 * @param E_ENABLED $enabled	>>
	 * @param string $styles		>> array for custom style attributes.
	 * @param string $classes		>> array for custom class attributes.
	 * @param string $attributes	>> array for additional attributes.	@see <code>$this->$attributeBlacklist</code> for disallowed attributes
	 *
	 * @return void
	 */
	public function __construct($id, $name="", $visible=true, $enabled=true, $styles=array(), $classes=array(), $attributes=array() )
	{


		write2Debugfile(self::DEBUG_FILENAME, "\n", false);

		if (!E_VISIBLE::isValidValue($visible)){
			$visible = E_VISIBLE::YES;
		}
		if (!E_ENABLED::isValidValue($enabled)){
			$enabled = E_ENABLED::YES;
		}

		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		if ($visible === false){
			//$styles["display"] = "none"; // override if exists
			$classes[] = "hidden";
		}

		self::setID($id);
		self::setName($name);
		self::setVisible($visible);
		self::setEnabled($enabled);

		self::setStyles($styles);
		self::setClasses($classes);
		self::setAttributes($attributes);

	}

	/**
	 * Generates the complete string with custom attributes, build from attrubutes array <code>$this->attributes</code>
	 * @return string
	 * */
	public function getAttributesString($forWhat="")
	{
		//$explizitClassName = get_called_class();

		$this->attrString = "";
		if(count($this->attributes) > 0){
			foreach ($this->attributes as $key => $val)
			{
				if (in_array($key, $this->attributeBlacklist))
				{
					log_message(E_LOGLEVEL::ERROR, "getAttributesString() => attribute key '$key' ignored (called by ".get_called_class().")");
					continue;
				}

				if (is_array($val)){
					$val = base64_encode(json_encode($val));
				}

				if ($key != ""){
					$this->attrString .= ' '.$key.'="'.str_replace('"', "'", $val).'"';
				}
			}
		}
		return ($this->attrString != "" ? " ".$this->attrString:"");
	}

	/**
	 * Generates the complete class-attribute string, build from
	 * default array <code>$this->defaultClasses</code> and also class array <code>$this->classString </code> if desired
	 *
	 * @param string $arrayKey 		>> specific item
	 * @param bool $includeCustoms 	>> when false, <code>$this->classes </code> will not be used (only <code>$this->defaultClasses </code>).
	 *
	 * @return string $this->string >> complete class-attribute string
	 *
	 * */
	public function getClassString($arrayKey, $includeCustoms=false)
	{
		$explizitClassName = get_called_class();

		if($includeCustoms == true)
		{
			$this->defaultClasses[$arrayKey] = array_merge($this->defaultClasses[$arrayKey], $this->classes);
		}

		$this->classString 	= "";


		if ($arrayKey != "" && array_key_exists($arrayKey, $this->defaultClasses) === false){
			show_error("Key ($arrayKey) not found for $explizitClassName");
		}
		foreach ($this->defaultClasses[$arrayKey] as $val)
		{
			if($val != ""){
				$this->classString .= $val.' ';
			}
		}
		$this->classString = 'class="'.substr($this->classString, 0, -1).'"';

		return $this->classString;
	}

	/**
	 * Generates the complete styles-attribute string, build from default array <code>$this->defaultStyles</code> and custom styles array <code>$this->styles </code>

	 * @return string >> complete style string
	 */
	public function getStyleString()
	{
		if ($this->visible === false){
			$this->defaultStyles["display"] = "none";
		}


		$styleString = "";
		if (is_array($this->styles))
		{
			foreach ($this->defaultStyles as $key => $val)
			{
				if (array_key_exists($key, $this->styles))
				{
					unset($this->defaultStyles[$key]); // unset from default array
				}
			}
		}
		if (count($this->defaultStyles) > 0)
		{
			$this->styles = array_merge($this->defaultStyles, $this->styles);
		}

		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		foreach ($this->styles as $key => $val)
		{
			$styleString .= ' '.$key.':'.$val.';';
		}

		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		if ($styleString != "")
		{
			$styleString = 'style="'.substr($styleString, 1).'"';
		}

		$this->styleString = $styleString;

		return $styleString;
	}

	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// ..:: SOME GETTER
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	public function getIDString(){
		if ($this->id != ""){
			return 'id="'.$this->id.'"';
		}
		return "";
	}

	public function getNameString(){
		if ($this->name != ""){
			return 'name="'.$this->name.'"';
		}
		return "";
	}

	public function getTitleString(){
		if ($this->title != "")
		{
			$placement = (isset($this->attributes["data-placement"]) ? "_".$this->attributes["data-placement"]:"");
			return 'title'.$placement.'="'.$this->title.'"';
		}
		return "";
	}

	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// ..:: SETTER METHODS
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..

	/**
	 * Setter for custom attributes <code>$this->attributes </code>
	 *
	 * @param array $attributes
	 * @return $this
	 */
	public function setAttributes($attributes=array())
	{
		if ( is_array($attributes) && count($attributes) > 0 ){
			$this->attributes 	= $attributes;
		}
		return $this;
	}

	/**
	 * Simply add a class string to the classes array
	 * 
	 * @param string $class
	 * @return $this
	 */
	public function addAttribute($key, $value="")
	{
		$this->attributes[$key] = $value;

		return $this;
	}

	/**
	 * simply add a class string to the classes array
	 * @param string $class
	 * @return $this
	 */
	public function addClass($class)
	{
		if (! in_array($class, $this->classes)){
			$this->classes[] = $class;
		}
		return $this;
	}

	/**
	 * setter for custom classes <code>$this->classes </code>
	 *
	 * @param array $classes
	 * @return $this
	 */
	public function setClasses($classes=array())
	{
		if ( is_array($classes) && count($classes) > 0 ){
			$this->classes = array_unique($classes);
		}
		return $this;
	}

	/**
	 * setter for <code>$this->draggable </code>
	 *
	 * @param E_DRAGGABLE $draggable
	 * @return $this
	 */
	public function setDraggable($draggable=true)
	{
		if (! E_DRAGGABLE::isValidValue($draggable) ){
			$draggable	= E_DRAGGABLE::NO;
		}
	
		$this->draggable = $draggable;
		return $this;
	}
	
	/**
	 * setter for custom styles <code>$this->styles </code>
	 *
	 * @param array $styles
	 * @return $this
	 */
	public function setStyles($styles=array())
	{
		if ( is_array($styles) && count($styles) > 0 ){
			$this->styles 	= $styles;
		}
		return $this;
	}

	/**
	 * setter for the id value
	 * @param string $string
	 * @return $this
	 */
	public function setID($string){
		$this->id = $string;
		return $this;
	}

	/**
	 * setter for the name value
	 * @param string $string
	 * @return $this
	 */
	public function setName($string){
		$this->name = $string;
		return $this;
	}

	/**
	 * setter for the title
	 * @param string $string
	 * @return $this
	 */
	public function setTitle($string, $position="top"){
		$this->title = $string;
		if (! E_TOOLTIP_POSITION::isValidValue($position) ){
			$position	= E_TOOLTIP_POSITION::TOP;
		}
		$this->attributes["data-placement"] = $position;
		return $this;
	}

	/**
	 * setter for the enabled state
	 * @param E_ENABLED $bool
	 * @return $this
	 */
	public function setEnabled($bool){
		$this->enabled = boolval($bool);
		return $this;
	}

	/**
	 * setter for the visible state
	 * @param E_VISIBLE $bool
	 * @return $this
	 */
	public function setVisible($bool){
		$this->visible = boolval($bool);
		return $this;
	}
}

/**
 * HTML Alert 
 * 
 * 
 * @author _BA5E 
 * @category helper
 * @package application\helpers\htmlComponents_helper
 * @since 0.1
 * @version 1.2
 */
class HTML_Alert extends HTML
{
	/**
	 * @var E_DISMISSABLE
	 */
	public $dismissible = true;
	
	/**
	 * @var string
	 */
	public $titleText;
	
	/**
	 * @var string
	 */
	public $text;
	
	/**
	 * @var E_COLOR
	 */
	public $color;
	
	/**
	 * HTML_Alert
	 * 
	 * @param string $id					>> id
	 * @param string $titleText				>> alert title (it's NOT the title attribute )
	 * @param string $text					>> alert text
	 * @param E_COLOR $color				>> contextual color
	 * @param E_VISIBLE $visible			>> visible boolean
	 * @param E_DISMISSABLE $dismissible	>> dismissible boolean
	 * @param array $styles					>>
	 * @param array $classes				>>	
	 * @param array $attributes				>>
	 *
	 * @return void
	 */
	public function __construct($id, $titleText, $text, $color="info", $dismissible=false, $visible=true, $styles=array(), $classes=array(), $attributes=array())
	{
		parent::__construct($id, $id, $visible, E_ENABLED::YES, $styles, $classes, $attributes);

		self::setDismissible($dismissible);
		self::setColor($color);
		self::setText($text);
		self::setTitleText($titleText);
	}
	
	/**
	 * Generates the output of HTML_Alert
	 * @return $string
	 */
	public function generateHTML()
	{
		$this->defaultClasses["alert"] = array("alert", "alert-$this->color", ($this->dismissible ? 'alert-dismissible':''));
		
		$dismissButton = "";
		if ($this->dismissible === true)
		{
			$dismissButton = '
			<button type="button" class="close" data-dismiss="alert">
				<span aria-hidden="true">'.E_ICONS::CLOSE.'</span>
				<span class="sr-only">Close</span>
			</button>';
		}
		
		$title = "";
		if ($this->titleText != ""){
			$title = '<strong>'.$this->titleText.'</strong><br>';
		}

		$com = '
		<div id="'.$this->id.'" name="'.$this->name.'" role="alert" '.
			$this->getClassString('alert', true).
			$this->getAttributesString().
			$this->getStyleString().'
			>'.$dismissButton.$title.$this->text.'
		</div>';
		
		// write2Debugfile(self::DEBUG_FILENAME, "\nHTML_Alert generated\n".$com );
		return $com;
	}
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// Setter functions for HTML_Alert
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	/**
	 * setter for <code>$this->dismissible</code>
	 *
	 * @param E_DISMISSABLE $dismissable 
	 * @return $this
	 */
	public function setDismissible($dismissable)
	{
		if (!E_DISMISSABLE::isValidValue($dismissable)){
			$dismissable = E_DISMISSABLE::YES; 
		}
		
		$this->dismissible = $dismissable;
		return $this;
	}
	
	/**
	 * setter for <code>$this->color</code> 
	 * 
	 * @param E_COLOR $color
	 * @return $this
	 */
	public function setColor($color)
	{
		if (! E_COLOR::isValidValue($color) ){
			$color = E_COLOR::STANDARD;
		}
		$this->color = $color; 
		return $this;
	}
	
	/**
	 * setter for <code>$this->text</code>
	 *
	 * @param string $text
	 * @return $this
	 */
	public function setText($text)
	{
		$this->text = $text;
		return $this;
	}
	
	/**
	 * setter for <code>$this->titleText</code>
	 *
	 * @param string $text
	 * @return $this
	 */
	public function setTitleText($text)
	{
		$this->titleText = $text;
		return $this;
	}
	
} // END OF >> HTML_Alert

/**
 * HTML Anchor 
 * 
 * @author _BA5E 
 * @category helper
 * @package application\helpers\HTML_helper
 * @since 0.8
 * @version 1.0
 */
class HTML_Anchor extends HTML
{
	/**
	 * @var string
	 */
	public $text;

	/**
	 * @var string
	 */
	public $href;
	
	/**
	 * @var string
	 */
	public $onclick;
	
	/**
	 * simple anchor
	 *
	 * @param string $id			>> id
	 * @param string $text			>> label text
	 * @param string $href			>> link target
	 * @param string $onclick		>> onclick event
	 * @param bool $visible			>> valid <code>E_VISIBLE</code>
	 * @param array $styles			>> as usual
	 * @param array $classes		>> as usual
	 * @param array $attributes		>> as usual
	 *
	 * @return HTML_Anchor
	 */
	public function __construct($id, $text, $href, $onclick="", $visible=true, $styles=array(), $classes=array(), $attributes=array() )
	{
		parent::__construct($id, $id, $visible, E_ENABLED::YES, $styles, $classes, $attributes);
		
		self::setHref($href);
		self::setOnclick($onclick);
		self::setText($text);
	}

	/**
	 * Generates the output of HTML_Anchor
	 * @return $string
	 */
	public function generateHTML()
	{
		$this->defaultClasses["anchor"] = array();
		
		$com = '
		<a id="'.$this->id.'" name="'.$this->name.'" href="'.$this->href.'" '.$this->getTitleString().' '.$this->onclick.' '.
			$this->getAttributesString().' '.
			$this->getClassString('anchor', true).' '.
			$this->getStyleString().'
			>'.
			$this->text.'		
		</a>';
		
		// write2Debugfile(self::DEBUG_FILENAME, "\nHTML_Anchor generated\n".$com);
		return $com;
	}
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// Setter functions for HTML_Anchor
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	/**
	 * setter for <code>$this->href</code>
	 *
	 * @param string $href
	 * @return $this
	 */
	 public function setHref($href)
	 {
		$this->href = $href; 
		return $this;
	 }
	 
	 /**
	  * setter for <code>$this->onclick</code>
	  *
	  * @param string $onclick
	  * @return $this
	  */
	 public function setOnclick($onclick)
	 {
	 	$this->onclick = '';
	 	if ($onclick != ""){
	 		$this->onclick = 'href="'.$onclick.'"';
	 	}
	 	return $this;
	 }
	
	/**
	 * setter for <code>$this->text</code>
	 *
	 * @param string $text
	 * @return $this
	 */
	 public function setText($text){
		$this->text = $text; 
		return $this;
	}
} // END OF >> HTML_Anchor




// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
/**
 * HTML Badge. Creates a badge span with a surrounding header tag. 
 * 
 * @author _BA5E 
 * @category helper
 * @package application\helpers\HTML_helper
 * @since 0.2
 * @since 1.2 
 * 	the surrounding header tag has been removed so you can include a badge wherever you want (anchors, buttons, labels, heading etc.)
 * 	as a result, the size property and its setter has been removed
 * @version 1.2
 */
class HTML_Badge extends HTML
{
	/**
	 * @var string
	 */
	public $text;
	
	/**
	 * @var E_COLOR
	 */
	public $color;
	
	/**
	 * @deprecated
	 * @var E_SIZES
	 */
	public $size;
	
	/**
	 * creates a badge span 
	 * 
	 * @param string $id			>> badge identifier
	 * @param string $text			>> badge text
	 * @param E_COLOR $color		>> contextual color <code>E_COLOR</code>
	 * @param E_VISIBLE $visible	>> set to <code>false</code> to make the alert hidden
	 * @param array $styles			>> 
	 * @param array $classes		>>
	 * @param array $attributes		>>
	 * 
	 * @return HTML_Badge
	 */
	public function __construct($id, $text, $color="", $visible=true, $styles=array(), $classes=array(), $attributes=array())
	{
		parent::__construct($id, $id, $visible, E_ENABLED::YES, $styles, $classes, $attributes);
	
		self::setColor($color);
		self::setText($text);
	}
	
	/**
	 * Generates the output of HTML_Badge
	 * @return $string
	 */
	public function generateHTML()
	{
		$this->defaultClasses["badge"] = array("badge", "badge-$this->color");
		
		
		//<h'.$this->size.'></h'.$this->size.'>
		$com = '
		<span id="'.$this->id.'" '.
			$this->getClassString('badge').' '.
			$this->getAttributesString().' '.
			$this->getStyleString().'
			>'.
			$this->text.'
		</span>
		';
		
		// write2Debugfile(self::DEBUG_FILENAME, "\nHTML_Badge generated\n".$com);
		return $com;
	}
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// Setter functions for HTML_Badge
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	/**
	 * setter for <code>$this->text</code>
	 * 
	 * @param string $string
	 * @return $this
	 */
	public function setText($text){
		$this->text = $text; 
		return $this;
	}
	
	/**
	 * setter for <code>$this->color</code>
	 * 
	 * @param E_COLOR $color
	 * @return $this
	 */
	public function setColor($color){
		if (! E_COLOR::isValidValue($color) ){
			$color = E_COLOR::STANDARD;
		}
		$this->color = $color; 
		return $this;
	}
} // END OF >> HTML_Badge


/**
 * HTML Button. 
 * 
 * @author _BA5E 
 * @category helper
 * @package application\helpers\HTML_helper
 * @since 0.2
 * @version 1.2
 */
class HTML_Button extends HTML
{
	/**
	 * if set, a button-styled anchor will be generated instead of a button 
	 * @var string
	 */
	public $anchor = null;
	
	/**
	 * @var E_SIZES
	 */
	public $size;
	
	/**
	 * @var string
	 */
	protected $sizeString	= "";	
	
	/**
	 * @var E_BUTTON_TYPES
	 */
	protected $type 		= "button";
	
	/**
	 * @var E_HORIZONTAL_POSITION
	 */
	public $icon_placement 	= "left";
	
	/**
	 * @var string
	 */
	public $label;
	
	/**
	 * @var E_COLOR
	 */
	public $color;
	
	/**
	 * @var E_ICONS
	 */
	public $icon;
	
	/**
	 * @var string
	 */
	public $value = 1; 

	/**
	 * creates a HTML_Button >> button
	 *
	 * @param string $id			>> id
	 * @param string $name			>> name
	 * @param string $label			>> button label
	 * @param E_COLOR $color		>> 
	 * @param E_SIZES $size			>> 
	 * @param string $icon			>> icon for the button
	 * @param E_HORIZONTAL_POSITION $iconPlacement	>> icon placement
	 * //@param E_BUTTON_TYPES $type	>> button type 'button' or 'submit'  !!! removed from constructor since version 1.2. Default is E_BUTTON_TYPES::BUTTON
	 * @param E_VISIBLE $visible	>> set to <code>false</code> to make the alert hidden
	 * @param E_ENABLED $enabled	>> set to <code>false</code> to disable the alert
	 * 
	 * @param array $styles			>>
	 * @param array $classes		>>
	 * @param array $attributes		>>
	 *
	 * @return HTML_Button
	 */
	public function __construct($id, $name, $label, $color="primary", $size="", $icon="", $iconPlacement="left", $visible=true, $enabled=true, $styles=array(), $classes=array(), $attributes=array())
	{
		parent::__construct($id, $name, $visible, $enabled, $styles, $classes, $attributes);
		
		self::setColor($color);
		self::setIcon($icon);
		self::setIconPosition($iconPlacement);
		self::setLabel($label);
		self::setSize($size);
		self::setType(E_BUTTON_TYPES::BUTTON);	// removed from constructor since version 1.2
	}
	
	/**
	 * Generates the output of HTML_Button
	 * 
	 * @param bool $excludeDefaultBtnClasses	>> In some rare cases (for example the modal dismiss button) you dont want the button to have the classes ("btn", "btn-$this->color"). With this parameter you can exclude them
	 * @return $string
	 */
	public function generateHTML($excludeDefaultBtnClasses=false)
	{
		if ($excludeDefaultBtnClasses == false || $this->anchor != ""){
			$this->defaultClasses["button"] = array("btn", "btn-$this->color", $this->sizeString);
		}else{
			$this->defaultClasses["button"] = array($this->sizeString);
		}
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		if ($this->enabled === false){
			$this->defaultClasses["button"][] = "disabled"; 		// add disabled class
		}
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		$label_and_icon = $this->icon.'&nbsp;'.$this->label;		// icon left
		if ($this->icon_placement == E_HORIZONTAL_POSITION::RIGHT){
			$label_and_icon = $this->label.'&nbsp;'.$this->icon;	// icon right
		}
		
		if ($this->anchor != "")
		{
			$com = 
			'<a id="'.$this->id.'" name="'.$this->name.'" type="'.$this->type.'" value="'.$this->value.'" href="'.$this->anchor.'" '.
				$this->getClassString('button', true).
				$this->getAttributesString().
				$this->getStyleString().'>'.$label_and_icon.'
			</a>';
		}
		else 
		{
			$com = 
			'<button id="'.$this->id.'" name="'.$this->name.'" type="'.$this->type.'" value="'.$this->value.'" '.
			$this->getClassString('button', true).
			$this->getAttributesString().
			$this->getStyleString().'>'.$label_and_icon.'</button>';
		}
		
		// write2Debugfile(self::DEBUG_FILENAME, "\nHTML_Button generated\n".$com);
		return $com;
	}
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// Setter functions for HTML_Button
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	/**
	 * setter for <code>$this->color</code>
	 *
	 * @param E_COLOR $color
	 * @return $this
	 */
	public function setAnchor($target)
	{
		$this->anchor = $target;
		return $this;
	}
	
	/**
	 * setter for <code>$this->color</code>
	 *
	 * @param E_COLOR $color
	 * @return $this
	 */
	public function setColor($color)
	{
		if (! E_COLOR::isValidValue($color) ){
			$color	= E_COLOR::STANDARD;
		}
		$this->color = $color;
		return $this;
	}	
	
	/**
	 * setter for <code>$this->icon</code>
	 *
	 * @param string $icon
	 * @return $this
	 */
	public function setIcon($icon)
	{
		if (! E_ICONS::isValidValue($icon) ){
			$icon = E_ICONS::NO_ICON;
		}
		$this->icon = $icon;
		return $this;
	}	
	
	/**
	 * setter for <code>$this->icon_placement</code>
	 *
	 * @param E_HORIZONTAL_POSITION $position
	 * @return $this
	 */
	public function setIconPosition($position)
	{
		if (! E_HORIZONTAL_POSITION::isValidValue($position) ){
			$position = E_HORIZONTAL_POSITION::LEFT; 
		}
		$this->icon_placement = $position;
		
		return $this;
	}	
	
	/**
	 * setter for <code>$this->label</code>  
	 *
	 * @param string $label
	 * @return $this
	 */
	public function setLabel($label)
	{
		$this->label = $label;
		return $this;
	}
	
	/**
	 * setter for <code>$this->size</code>  
	 *
	 * @param E_SIZES $size
	 * @return $this
	 */
	public function setSize($size)
	{
		if (! E_SIZES::isValidValue($size) ){
			$size	= E_SIZES::STANDARD;
		}
		if ($size != ""){
			$this->sizeString = 'btn-'.$size;
		}
		
		$this->size = $size;
		return $this;
	}	
	
	/**
	 * setter for <code>$this->type</code>. Only 'button' and 'submit' are valid types here  
	 *
	 * @param E_BUTTON_TYPES $type
	 * @return $this
	 */
	public function setType($type)
	{
		if (! E_BUTTON_TYPES::isValidValue($type) ){
			$type = E_BUTTON_TYPES::BUTTON;
		}
		$this->type = $type;
		return $this;
	}
	
	/**
	 * setter for <code>$this->value</code>
	 *
	 * @param string $value
	 * @return $this
	 */
	public function setValue($value)
	{
		$this->value = $value;
		return $this;
	}
	
} // END OF >> HTML_Button

/**
 * HTML Button Group
 *
 * @author _BA5E
 * @category helper
 * @package application\helpers\HTML_helper
 * @since 1.2
 * @version 1.0
 */
class HTML_ButtonGroup extends HTML
{
	public $buttons = array();
	
	/**
	 * creates a HTML_ButtonGroup 	
	 *
	 * @param string $id			>> id
	 *
	 * @param array $styles			>>
	 * @param array $classes		>>
	 * @param array $attributes		>>
	 *
	 * @return HTML_Button
	 */
	public function __construct($id, $buttons=array(), $enabled=true, $visible=true, $styles=array(), $classes=array(), $attributes=array())
	{
		parent::__construct($id, $id, $visible, $enabled, $styles, $classes, $attributes);
		
		self::setButtons($buttons);
	}
	
	/**
	 * Generates the output of HTML_ButtonGroup
	 *
	 * @return $string
	 */
	public function generateHTML($vertical=false)
	{
		$this->defaultClasses["button_group"] = array(($vertical ? 'btn-group-vertical':'btn-group'), ($this->enabled === E_ENABLED::NO ? 'disabled':'') );
		
		
		$buttons_string = "";
		foreach ($this->buttons as $button) 
		{
			$callable_name = null;
			if (is_object($button) && is_callable("generateHTML", true, $callable_name) ){
				// Element seems to be of type HTMLComponent.
				$buttons_string .= $button->generateHTML();
			}
			else {
				$buttons_string .= $button; // Accept any markup
			}
		}
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		$com = 
		'<div id="'.$this->id.'" '.
			$this->getClassString('button_group', true).' '.
			$this->getAttributesString().' '.
			$this->getStyleString().'>'.
			$buttons_string.' 
		</div>';
		
		return $com;
	}
	
	/**
	 * add a button to the buttons array
	 * 
	 * @param mixed $button
	 * @return HTML_ButtonGroup
	 */
	public function addButton($button)
	{
		$this->buttons[] = $button;
		return $this;
	}
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// Setter functions for HTML_ButtonGroup
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	/**
	 * setter for <code>$this->buttons</code>
	 *
	 * @param array $buttons
	 * @return $this
	 */
	public function setButtons($buttons)
	{
		if (is_array($buttons)){
			$this->buttons = $buttons;
		}
		return $this;
	}
} // END OF >> HTML_ButtonGroup

// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
/**
 * HTML Callout (like in the bootstrap docs).
 *
 * @author _BA5E
 * @category helper
 * @package application\helpers\HTML_helper
 * @since 1.2
 * @version 1.0
 */
class HTML_Callout extends HTML
{
	/** @var E_COLOR */
	public $color;
	
	/** @var string */
	public $heading;
	
	/** @var string */
	public $text;
	
	/**
	 *  HTML_Callout (Alert-like message) 
	 *  
	 * @param string $id
	 * @param string $heading
	 * @param string $text
	 * @param E_COLOR $color
	 * @param E_ENABLED $enabled
	 * @param E_VISIBLE $visible
	 * @param array $styles
	 * @param array $classes
	 * @param array $attributes
	 */
	public function __construct($id, $heading, $text, $color="info", $enabled=true, $visible=true, $styles=array(), $classes=array(), $attributes=array())
	{
		parent::__construct($id, $id, $visible, $enabled, $styles, $classes, $attributes);
	
		self::setColor($color);
		self::setHeading($heading);
		self::setText($text);
		
	}
	
	/**
	 * Generates the output of HTML_Callout
	 * @return $string
	 */
	public function generateHTML($noMargins=false)
	{
		$this->defaultClasses["callout"] 	= array("bs-callout", "bs-callout-".$this->color, ($this->enabled === E_ENABLED::NO ? 'disabled':'') );
	
		if ($noMargins){
			$this->defaultClasses["callout"][] = "no-margin";
		}
		
		$disabled = "";
		if ($this->enabled === E_ENABLED::NO){
			$disabled = " disabled";
		}
	
		$com = '
		<div id="'.$this->id.'" name="'.$this->name.'" '.$disabled.' '.
			$this->getClassString('callout', true).' '.
			$this->getAttributesString().' '.
			$this->getStyleString().'
			>
			<h4>'.$this->heading.'</h4>
			<p>'.$this->text.'</p>
		</div>';
	
	
		// write2Debugfile(self::DEBUG_FILENAME, "\nHTML_callout generated\n".$com);
		return $com;
	}
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// Setter functions for HTML_Callout
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	/**
	 * setter for <code>$this->color</code>
	 *
	 * @param E_COLOR $color >> a valid contextual color
	 * @return $this
	 */
	public function setColor($color){
		if (!E_COLOR::isValidValue($color) ){
			$color = E_COLOR::STANDARD;
		}
		$this->color = $color;
		return $this;
	}
	
	/**
	 * setter for <code>$this->heading</code>
	 *
	 * @param string $text
	 * @return $this
	 */
	public function setHeading($text)
	{
		$this->heading = $text;
		return $this;
	}

	/**
	 * setter for <code>$this->text</code>
	 *
	 * @param string $text
	 * @return $this
	 */
	public function setText($text)
	{
		$this->text = $text;
		return $this;
	}
} // END OF HTML_Callout

// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
/**
 * HTML Carousel 
 *
 * @author _BA5E
 * @category helper
 * @package application\helpers\HTML_helper
 * @since 1.2
 * @version 1.0
 */
class HTML_Carousel extends HTML
{
	const DEBUG_FILENAME		= "HTML_Carousel.log";
	
	/** @var string */
	private $default_caption 	= "";
	
	/** @var string */
	private $default_text 		= "";
	
	/** @var string */
	private $default_link 		= "";
	
	/** @var array */
	private $images 			= array();
	
	/** @var array */
	private $captions 			= array();
	
	/**
	 *  HTML_Carousel
	 *
	 * @param string $id
	 * @param array $images
	 * @param E_ENABLED $enabled
	 * @param E_VISIBLE $visible
	 * @param array $styles
	 * @param array $classes
	 * @param array $attributes
	 */
	public function __construct($id, $images=array(), $enabled=true, $visible=true, $styles=array(), $classes=array(), $attributes=array())
	{
		parent::__construct($id, $id, $visible, $enabled, $styles, $classes, $attributes);
		
		self::setImages($images);
		return $this;
	}
	
	/**
	 * Generates a carousel from the images in the given directory
	 *
	 * @param string $directory 	>> path to the images
	 * @param string $id 			>> optional id
	 * @return string
	 */
	static function fromDirectory($directory, $id=null)
	{
		$id 		= (!$id ? random_string():$id);
		$carousel 	= new HTML_Carousel($id, self::scanDirectory($directory));
		
		return $carousel->generateHTML();
	}
	
	/**
	 * Scan directory for images.
	 * ! Dont miss the concluding slash
	 *
	 * @param string $directory
	 * @return array
	 */
	public static function scanDirectory($directory)
	{
		$CI =& get_instance();
		$CI->load->library("BASE_Mime.php");
		
		$images = array();
		
		if ($handle = opendir($directory) )
		{
			$finfo 				= @finfo_open(FILEINFO_MIME_TYPE);
			if($finfo === false){
				log_message("could not open mimetype database", "error");
			}
			
			$allowed_mime_types = BASE_Mime::getImageTypes();
			write2Debugfile(HTML_Carousel::DEBUG_FILENAME, " allowed mime types-".print_r($allowed_mime_types, true));
			
			while (false !== ($entry = readdir($handle)))
			{
				if ($entry != "." && $entry != "..")
				{
					$file 			= $directory . $entry;
					$file_mimetype 	= @finfo_file(@finfo_open(FILEINFO_MIME_TYPE), $file);
					//$file_ext		= strtolower( @end( @explode('.', $file) ));
					write2Debugfile(HTML_Carousel::DEBUG_FILENAME, " file[$entry] type[$file_mimetype]");
					
					if($allowed_mime_types && !in_array(strtolower($file_mimetype), $allowed_mime_types))
					{
						write2Debugfile(HTML_Carousel::DEBUG_FILENAME, " - ignored");
						log_message("debug", "ignore file ($entry) on carousel creation. mime type ($file_mimetype) not allowed");
					}
					else{
						$images[] = $directory . $entry;
					}
				}
			}
		}
		
		write2Debugfile(HTML_Carousel::DEBUG_FILENAME, "\nHTML_Carousel >> directory scan ($directory) -".print_r($images, true));
		
		return $images;
	}
	
	/**
	 * Generates the output of HTML_Carousel
	 * @return $string
	 */
	public function generateHTML($img_max_width=null, $img_max_height=null)
	{
		$this->defaultClasses["carousel"] 	= array("carousel slide", ($this->enabled === E_ENABLED::NO ? 'disabled':'') );
		
		$c						= 0;
		$carousel_inner 		= "";
		$carousel_indicators	= "";
		
		$max_height = "";
		if ($img_max_height){
			$max_height = 'height:100%; max-height:'.$img_max_height.'px; object-fit: contain;';
		}
		
		$max_width = "";
		if ($img_max_width){
			// width and object-fit: contain are needed for the zoom-in to work
			$max_width = 'width:100%; max-width:'.$img_max_width.'px; object-fit: contain;';
		}
		
		
		foreach ($this->images as $image)
		{
			if (BASE_Mime::validate_mimetype($image))
			{
				$carousel_indicators.= '<li data-target="#'.$this->id.'" data-slide-to="'.$c.'" class="'.($c == 0 ? 'active':'').'"></li>';
				
				$link		= ((isset($image["link"]) && $image["link"] != "") ? $image["link"] : $this->default_link);
				
				if ($link == "")
				{
					$carousel_inner .= '
					<div class="item '.($c == 0 ? 'active':'').'">
						<img style="'.$max_width.';'.$max_height.'" alt="slide image" src="'.HTML_Image::generateDataURIFromImage($image["path"]).'" data-zoom-image="'.HTML_Image::generateDataURIFromImage($image["path"]).'" class="img-responsive" />'
					;
				}
				else
				{
					$carousel_inner .= '
					<div class="item '.($c == 0 ? 'active':'').'">
						<a href="'.$link.'">
							<img style="'.$max_width.';'.$max_height.'" alt="slide image" src="'.HTML_Image::generateDataURIFromImage($image["path"]).'" data-zoom-image="'.HTML_Image::generateDataURIFromImage($image["path"]).'" class="img-responsive" />
						</a>';
				}
				
				$caption 	= ((isset($image["caption"]) &&  $image["caption"] != "") ? $image["caption"] : $this->default_caption);
				$text		= ((isset($image["text"]) && $image["text"] != "") ? $image["text"] : $this->default_text);
				if ( $caption != "" )
				{
					$carousel_inner .= '
					<div class="carousel-caption">
						<h3>'.$caption.'</h3>';
					if ($text != ""){
						$carousel_inner .= '<p>'.$text.'</p>';
					}
					$carousel_inner .= '</div>';
				}
				
				$carousel_inner .= '</div>';
				$c++;
			}
		}
		
		
		$com = '
		<div id="'.$this->id.'" data-ride="carousel" class="carousel" count-images="'.count($this->images).'" '.
			$this->getClassString('carousel', true).' '.
			$this->getAttributesString().' '.
			$this->getStyleString().'>
			
			<ol class="carousel-indicators">
				'.$carousel_indicators.'
			</ol>
			<div class="carousel-inner '.E_IMAGE_STYLES::ROUNDED.'">'.
				$carousel_inner.'
			</div>
			
			<a class="left carousel-control" href="#'.$this->id.'" data-slide="prev"><span class="glyphicon glyphicon-chevron-left"></span></a>
			<a class="right carousel-control" href="#'.$this->id.'" data-slide="next"><span class="glyphicon glyphicon-chevron-right"></span></a>
		</div>';
		
		
		write2Debugfile(HTML_Carousel::DEBUG_FILENAME, "\nHTML_Carousel generated\nimages-".print_r($this->images, true)."\n".$com);
		return $com;
	}

	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// Setter functions for HTML_Carousel
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	/**
	 * setter for <code>$this->default_caption</code>
	 *
	 * @param array $caption >> string
	 * @return HTML_Carousel
	 */
	public function addImage($path, $caption, $text, $link="")
	{
		if (BASE_Mime::validate_mimetype($path) == true && in_array(BASE_Mime::getFileMimetype($path), BASE_Mime::getImageTypes()))
		{
			$this->images[] = array("path"=>$path, "caption"=>$caption, "text"=>$text, "link"=>$link);
		}
		else{
			throw new Exception("unknown mime type [".BASE_Mime::getMimetypeForFile($path)."] for file [$path]");
			// cannot use image. unknown or invalid mime type;
		}
		return $this;
	}
	
	/**
	 * setter for <code>$this->default_caption</code>
	 *
	 * @param array $caption >> string
	 * @return HTML_Carousel
	 */
	public function setDefaultCaption($caption){
		$this->default_caption = $caption;
		return $this;
	}
	
	/**
	 * setter for <code>$this->default_text</code>
	 *
	 * @param array $text >> string
	 * @return HTML_Carousel
	 */
	public function setDefaultText($text){
		$this->default_text = $text;
		return $this;
	}
	
	/**
	 * setter for <code>$this->color</code>
	 *
	 * @param array $images >> array with image paths
	 * @return HTML_Carousel
	 */
	public function setImages($images){
		$this->images = $images;
		return $this;
	}
} // END OF HTML_Carousel

// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
/**
 * HTML Checkbox. 
 * 
 * @author _BA5E 
 * @category helper
 * @package application\helpers\HTML_helper
 * @since 0.2
 * @version 1.2
 */
class HTML_Checkbox extends HTML
{
	/**
	 * @var string
	 */
	public $text;
	
	/**
	 * @var string
	 */
	public $value;
	
	/**
	 * @var E_CHECKED
	 */
	public $checked;
	
	/**
	 * @var E_INLINE
	 */
	public $inline;
	
	/**
	 *  HTML_Checkbox 
	 *  A input of type checkbox
	 *
	 * @param string $id			>> id
	 * @param string $name			>> name
	 * @param string $text			>> text
	 * @param E_CHECKED $checked	>> checked attribute
	 * @param string $value			>> value attribute
	 * @param E_ENABLED $enabled	>> enable or disable the checkbox
	 * @param E_INLINE $inline		>> align checkbox in one line
	 * @param E_VISIBLE $visible	>> set to <code>false</code> to make the checkbox hidden
	 * @param array $styles			>> 
	 * @param array $classes		>>
	 * @param array $attributes		>>
	 *
	 * @return HTML_Checkbox
	 */
	public function __construct($id, $name, $text, $checked, $value=0, $enabled=true, $inline=true, $visible=true, $styles=array(), $classes=array(), $attributes=array())
	{
		parent::__construct($id, $name, $visible, $enabled, $styles, $classes, $attributes);
	
		self::setChecked($checked);
		self::setInline($inline);
		self::setText($text);
		self::setValue($value);
	}
	
	/**
	 * Generates the output of HTML_Checkbox
	 * @return $string
	 */
	public function generateHTML()
	{
		$this->defaultClasses["checkbox"] 	= array( ($this->enabled === E_ENABLED::NO ? 'disabled':'') );
		
		
		$disabled = "";
		if ($this->enabled === E_ENABLED::NO){
			$disabled = " disabled";
		}
		
		$checked= "";
		if ($this->checked === E_SELECTED::YES){
			$checked = "checked";
		}
		
		$inline = "checkbox";
		if ($this->inline === E_INLINE::YES){
			$inline = 'checkbox-inline';
		}
		
		$com = '
		<div class="'.$inline.$disabled.'">
			<label id="'.$this->id.'_lbl" for="'.$this->id.'" class="'.$inline.$disabled.'">
				<input id="'.$this->id.'" name="'.$this->name.'" value="'.$this->value.'" '.$checked.'  '.$disabled.' type="checkbox" '.
					$this->getClassString('checkbox', true).' '.
					$this->getAttributesString().' '.
					$this->getStyleString().'
				/>
				<span>'.$this->text.'</span>
			</label>
		</div>';
		
		
		// write2Debugfile(self::DEBUG_FILENAME, "\nHTML_Checkbox generated\n".$com);
		return $com;
	}
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// Setter functions for HTML_Checkbox
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	/**
	 * setter for <code>$this->checked</code>
	 *
	 * @param E_SELECTED $checked
	 * @return $this
	 */
	public function setChecked($checked)
	{
		if (!E_SELECTED::isValidValue($checked)){
			$checked = E_SELECTED::NO;
		}
		$this->checked = $checked;
		return $this;
	}
	
	/**
	 * setter for <code>$this->inline</code>
	 *
	 * @param E_INLINE $inline
	 * @return $this
	 */
	public function setInline($inline)
	{
		if (! E_INLINE::isValidValue($inline)){
			$inline = E_INLINE::YES;
		}
		$this->inline = $inline;
		return $this;
	}
	
	/**
	 * setter for <code>$this->text</code>
	 *
	 * @param string $text
	 * @return $this
	 */
	public function setText($text)
	{
		$this->text = $text;
		return $this;
	}
	
	/**
	 * setter for <code>$this->value</code>
	 *
	 * @param string $value
	 * @return $this
	 */
	public function setValue($value)
	{
		$this->value = $value;
		return $this;
	}
	
} // END OF >> HTML_Checkbox

// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
/**
 * (Data)Table component
 * 
 * @link https://datatables.net/
 * 
 * @category Helper
 * @package application\helper\HTML_helper
 * @version 1.3
 */
class HTML_Datatable extends HTML
{
	/**
	 * @var string
	 */
	public $ajax_src	= null;
	
	/**
	 * @var string
	 */
	public $caption 	= "";
	
	/**
	 * @var array
	 */
	public $columns 	= array();
	
	/**
	 * @var array
	 */
	public $data		= array();
	
	/**
	 * HTML_Datatable
	 *
	 * @param string $id			>> id
	 * @param array $columns		>> columns array
	 * @param array $data			>> data provider, if you want to give data immediatly
	 * @param string $url			>> ajax source, if you want to use response from the Datatable-lib
	 * 
	 * @param E_VISIBLE $visible	>> set to <code>false</code> to hide the input
	 * @param E_ENABLED $enabled	>> set to <code>false</code> to disable the input
	 * @param array $styles			>> the usual tata
	 * @param array $classes		>> the usual tata
	 * @param array $attributes		>> the usual tata
	 * 
	 * @return HTML_Datatable
	 */
	public function __construct($id, $columns=array(), $data=array(),  $url=null, $visible=true, $enabled=true, $styles=array(), $classes=array(), $attributes=array() )
	{
		// write2Debugfile(self::DEBUG_FILENAME, "construct HTML_Datatable\nid[$id] url[$url] columns-".print_r($columns, true)."\ndata-".print_r($data, true));
		parent::__construct($id, $id, $visible, $enabled, $styles, $classes, $attributes);
		
		$this->setAjax_source($url);
		$this->setCaption("");
		$this->setColumns($columns);
		$this->setData($data);
		$this->addAttribute("table_id",$id);
		$this->styles["min-height"] = "150px";
		
		return $this;
	}
	
	public static function blankJSON(){
		return json_encode( self::blankArray()  );
	}
	
	public static function blankArray(){
		return array("draw"=>0, "recordsTotal"=>0, "recordFiltered"=>0, "data"=>array());
	}
	
	public function setDefaultClasses()
	{
		$this->defaultClasses["wrapper"]	= array("table-responsive", $this->id, "no-side-margin");
		$this->defaultClasses["table"] 		= array("table table-striped table-bordered table-advance table-hover table-full-width dataTable", $this->id);
	}
	
	/**
	 * Generates the output of HTML_Datatable
	 * @return $string
	 */
	public function generateHTML()
	{
		//$this->defaultClasses["wrapper"]	= array($this->id);
		//$this->defaultClasses["table"] 		= array("table table-striped table-bordered table-hover table-full-width dataTable nowrap", $this->id);
		$this->setDefaultClasses();
		
		write2Debugfile("html_table.log", "generateHTML\ncols:\n".print_r($this->columns, true)."\n\ndata:\n".print_r($this->data, true), false);
		
		$table_has_filter 	= false;
		$thead_with_labels	= "";
		$thead_with_filters	= "";
		
		if (is_array($this->columns) || is_object($this->columns))
		{
			foreach ($this->columns as $column)
			{
				$thead_content		= "";
				
				if ($column->bVisible == E_VISIBLE::NO){
					$column->defaultClasses["column"][] = "hidden";
				}
				
				if ($column->bSortable == E_SORTABLE::YES){
					$column->defaultClasses["column"][] = "sortable";
				}else{
					$column->defaultClasses["column"][] = "sortable_disabled";
				}
				
				if ($column->filter != null)
				{
					$table_has_filter = true;
					$column->defaultClasses["column"][] = "has-filter";
					
					$callable_name = null;
					if (is_object($column->filter) && is_callable("generateHTML", true, $callable_name) )
					{	
						/**
						 * !!! NEW MODE using 'HTML_DTColumn' !!! 
						 * The column contains already the complete component in the header. 
						 * @todo the corresponding javascript to perform a client side search is not yet implemented globally in app.js
						 */
						$column->filter->setStyles(array("width"=>"100%", "min-width"=>"40px"));
						
						$thead_content = $column->filter->generateHTML();
					}
					else {
						// !!! OLD MODE using 'T_DTColumn' !!! >>> Javascript "$.app.datatable.addColumnFilter" will add the inputs dynamically, if called
						$thead_content = "&nbsp;";	
					}
					 
				}
				
				
				$cls 				= "";
				$style 				= "";
				if (get_class($column) == "HTML_DTColumn")
				{
					$style 	= $column->getStyleString();
					$cls	= $column->getClassString("column");
				}
				
				$thead_with_filters	.= '<th '.$cls.' '.$style.'>'.$thead_content.'</th>';
				$thead_with_labels 	.= '<th '.$cls.' '.$style.'>'.$column->label.'</th>';
			}
		}
		else{
			$thead_with_labels = '<th class="">'.lang("undefined").'</th>';
			$thead_with_filters = '<th class="">'.lang("undefined").'</th>';
		}
		
		$rows = "";
		if (is_array($this->data) || is_object($this->data))
		{
			$row_column = "";
			foreach ($this->data as $row_object)
			{
				// write2Debugfile("html_table.log", "\n - create row[$row] - ".print_r($row_object, true));
			
				$row_array = (array)$row_object;
				$rows .= "<tr>";
				$row_column = "";
				foreach ($this->columns as $column)
				{
					// write2Debugfile("html_table.log", "\n - col[".$column->data."] =>".(isset($row_array[$column->data]) ? $row_array[$column->data] : $column->data." not found"));
					$cls 				= "";
					$style 				= "";
					if (get_class($column) == "HTML_DTColumn"){
						$style 	= $column->getStyleString();
						$cls	= $column->getClassString("column");
					}

					$row_column .= '<td '.$cls.' '.$style.'>'.(isset($row_array[$column->data]) ? ($row_array[$column->data]) :"").'</td>';
				}
				$rows .= $row_column;
				$rows .= "</tr>";
			
				// write2Debugfile("html_table.log", "\n".$rows);
			}
		}
		else{
			$rows = '<tr><td class="">'.lang("undefined").'</td></tr>';
		}
			
		//$this->styles["min-height"] = "150px";
		
		$com = '
		<div id="'.$this->id.'_wrapper" '.$this->getClassString("wrapper").' '.($this->getStyleString()).'> 
			<table id="'.$this->id.'" width="100%"'. 
				$this->getAttributesString().' '.
				$this->getClassString("table", true).' 
				>
				<thead>'.($table_has_filter ? '<tr role="row">'.$thead_with_filters.'</tr>':'').'
					<tr role="row">'.$thead_with_labels.'</tr>
				</thead> 
				<tbody>'.$rows.'</tbody>
			</table>
		</div>'
		;
		//($table_has_filter ? '<tr role="row">'.$thead.'</tr>':'')
		write2Debugfile("html_table.log", "\nHTML_Datatable generated\ndata->\n".$com);
		return $com;
	}

	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// Setter functions for HTML_Datatable
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	
	/**
	 * setter for <code>$this->ajax_src</code>
	 *
	 * @param string $value
	 * @return $this
	 */
	public function setAjax_source($value){
		$this->ajax_src = $value;
		return $this;
	}
	
	/**
	 * setter for <code>$this->caption</code>
	 *
	 * @param string $value
	 * @return $this
	 */
	public function setCaption($value){
		$this->caption = $value;
		return $this;
	}
	
	/**
	 * setter for <code>$this->columns</code>
	 *
	 * @param array $value
	 * @return $this
	 */
	public function setColumns($value){
		$this->columns = $value;
		return $this;
	}
	
	/**
	 * setter for <code>$this->data</code>
	 *
	 * @param array $value
	 * @return $this
	 */
	public function setData($value){
		$this->data= $value;
		return $this;
	}
	
} // END OF >> HTML_Datatable

/**
 * HTML Datatable column
 *
 * @category helper
 * @package application\helpers\HTML_helper
 * @version 1.0
 */
class HTML_DTColumn extends HTML
{
	public $table			= "";
	public $data			= "";
	public $label 			= "";
	public $bSortable		= true;
	public $sSortDataType	= "string"; // string | numeric | date
	public $bVisible 		= true;
	public $visible 		= true;
	public $mRender			= NULL;
	public $searchable		= true;
	public $name		= true;

	public $filter			= null;	//

	/**
	 * Table column
	 *
	 * @param string $data_field		>> will also be used as identifier
	 * @param string $label				>> column label / heading
	 * @param E_SORTABLE $sortable		>> sortable column (datatable specific)
	 * @param E_VISIBLE $visible		>> visible column
	 * @param string $jsRowCallback		>>
	 * @param E_SEARCHABLE $searchable	>> searchable column (datatable specific)
	 *
	 * @param array $styles				>> the usual tata
	 * @param array $classes			>> the usual tata
	 * @param array $attributes			>> the usual tata
	 *
	 *
	 * @return $this
	 */
	function __construct($data_field="", $label="", $sortable=true, $visible=true, $searchable = true, $filter=null, $styles=array(), $classes=array(), $attributes=array())
	{
		parent::__construct($data_field, $data_field, $visible, E_ENABLED::YES, $styles, $classes, $attributes);


		$segm = explode(".",$data_field);
		if (count($segm) == 2)
		{
			list($this->table, $this->data) = explode(".",$data_field);
		}
		else
		{
			$this->data	 		= $data_field;
		}
		$this->defaultClasses["column"] = $classes;

		$this->label		= $label;
		$this->bSortable 	= $sortable;
		$this->bVisible		= $visible;
		$this->visible		= $visible;
		$this->mRender 		= null;//$jsRowCallback;
		$this->searchable	= $searchable;
		$this->filter		= $filter;
		$this->name			= $this->data;


		write2Debugfile("html_table_column.log", "this->\n".print_r($this, true), true);


		return $this;
	}

	/**
	 * @param string $id >> prefix filter will be added autom.
	 */
	public static function buildInputFilter($id, $placeholder=""){
		return new HTML_Input("filter_".$id, "filter_".$id, E_INPUTTYPE::TEXT, $placeholder, "", "", "", E_ENABLED::YES, E_VISIBLE::YES, array(), array("table-filter"), array());
	}

	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// Setter functions for HTML_DTColumn
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..

	/**
	 * setter for <code>$this->label</code>
	 *
	 * @param string $value
	 * @return $this
	 */
	public function setLabel($value){
		$this->label = $value;
		return $this;
	}

	/**
	 * setter for <code>$this->bSortable</code>
	 *
	 * @param string $value
	 * @return $this
	 */
	public function setSortable($bool){
		$this->bSortable = $bool;
		return $this;
	}


	/**
	 * setter for <code>$this->filter</code>
	 *
	 * @param string $value
	 * @return $this
	 */
	public function setFilter($value){
		$this->label = $value;
		return $this;
	}
}
// END OF >> HTML_DTColumn

// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
/**
 * HTML Datepicker
 *
 * @author _BA5E
 * @category helper
 * @package application\helpers\HTML_helper
 * @version 1.5
 */
class HTML_Datepicker extends HTML_Datetimepicker
{
	/**
	 * Datepicker
	 *
	 * @param string $id			>> id
	 * @param string $name			>> name
	 * @param string $value			>> date value
	 * @param string $placeholder	>> placeholder text
	 * @param string $format		>> date-format string
	 * @param E_VISIBLE $visible	>> set to <code>false</code> to hide the input
	 * @param E_ENABLED $enabled	>> set to <code>false</code> to disable the input
	 * @param array $styles			>> the usual tata
	 * @param array $classes		>> the usual tata
	 * @param array $attributes		>> the usual tata
	 *
	 *
	 * @return $this
	 */
	public function __construct($id, $name, $value, $placeholder="", $format="dd.mm.yyyy", $visible=true, $enabled=true, $styles=array(), $classes=array(), $attributes=array() )
	{
		parent::__construct($id, $name, $value, $placeholder, $format, $visible, $enabled, $styles, $classes, $attributes);
		
		self::setFormat($format);
		self::setMinView(2); // To show only a datepicker without time we set the minView to "month". that's it.
		
	}
} // END OF >> HTML_Datepicker
// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
/**
 * HTML Datetimepicker 
 * 
 * bootstrap-datepicker (original plugin)
 * @link https://uxsolutions.github.io/
 * @link https://bootstrap-datepicker.readthedocs.io
 * 
 * bootstrap-datetimepicker is a fork of the bootstrap-datepicker plugin (see above). 
 * It uses the same attributes but includes also the time part :-) 
 * @link https://www.malot.fr/bootstrap-datetimepicker
 * 
 * All options that take a "Date" can handle a Date object; a String formatted according to the given format; 
 * or a timedelta relative to today, eg '-1d', '+6m +1y', etc, where valid units are 'd' (day), 'w' (week), 'm' (month), and 'y' (year).
 * You can also specify an ISO-8601 valid datetime, despite of the given format :
 * 
 * yyyy-mm-dd
 * yyyy-mm-dd hh:ii
 * yyyy-mm-ddThh:ii
 * yyyy-mm-dd hh:ii:ss
 * yyyy-mm-ddThh:ii:ssZ
 * 
 * @author _BA5E
 * @category helper
 * @package application\helpers\HTML_helper
 * @version 1.6
 */
class HTML_Datetimepicker extends HTML
{
	protected $views_available = array(0=>"hour", 1=>"day", 2=>"month", 3=>"year", 4=>"decade"); 
	
	
	/**
	 * If true, manually-entered dates with two-digit years, such as "5/1/15", will be parsed as "2015", not "15".
	 * If the year is less than 10 years in advance, the picker will use the current century, otherwise, it will use the previous one.
	 * For example "5/1/15" would parse to May 1st, 2015, but "5/1/97" would be May 1st, 1997.
	 * To configure the number of years in advance that the picker will still use the current century, use an Integer instead of the Boolean true. E.g. "assumeNearbyYear: 20"
	 *
	 * @var Bool or int
	 */
	public $assumeNearbyYear = true;
	
	/**
	 * @var string
	 */
	public $addon_right = "";
	
	/**
	 * Whether or not to close the datepicker immediately when a date is selected.
	 * @var bool
	 */
	public $autoclose = true;
	
	
	/**
	 * Whether or not to show week numbers to the left of week rows.
	 * @var bool
	 */
	public $calendarWeeks = false;
	
	/**
	 * If true, displays a "Clear" button at the bottom of the datepicker to clear the input value. If "autoclose" is also set to true, this button will also close the datepicker.
	 * @var bool
	 */
	public $clearBtn = false;
	
	/**
	 * Array of date strings or a single date string formatted in the given date format
	 * @var array
	 */
	public $datesDisabled = array();
	
	/**
	 * Days of the week that should be disabled. Values are 0 (Sunday) to 6 (Saturday). Multiple values should be comma-separated. 
	 * Example: disable weekends: '06' or '0,6' or [0,6].
	 * @var array
	 */
	public $daysOfWeekDisabled = array(0,6);
	
	/**
	 * The latest date that may be selected; all later dates will be disabled. 
	 * @var string
	 */
	public $endDate = "";
	
	/**
	 * Whether or not to force parsing of the input value when the picker is closed.
	 * That is, when an invalid date is left in the input field by the user, the picker will forcibly parse that value, and set the input's value to the new, valid date, conforming to the given format.
	 * @var bool
	 */
	public $forceParce	= true;
	
	/**
	 * The date format, combination of p, P, h, hh, i, ii, s, ss, d, dd, m, mm, M, MM, yy, yyyy.
	 * 
	 * p 	: meridian in lower case ('am' or 'pm') - according to locale file
	 * P 	: meridian in upper case ('AM' or 'PM') - according to locale file
	 * s 	: seconds without leading zeros
	 * ss 	: seconds, 2 digits with leading zeros
	 * i 	: minutes without leading zeros
	 * ii 	: minutes, 2 digits with leading zeros
	 * h 	: hour without leading zeros - 24-hour format
	 * hh 	: hour, 2 digits with leading zeros - 24-hour format
	 * H 	: hour without leading zeros - 12-hour format
	 * HH 	: hour, 2 digits with leading zeros - 12-hour format
	 * d 	: day of the month without leading zeros
	 * dd 	: day of the month, 2 digits with leading zeros
	 * m 	: numeric representation of month without leading zeros
	 * mm 	: numeric representation of the month, 2 digits with leading zeros
	 * M 	: short textual representation of a month, three letters
	 * MM 	: full textual representation of a month, such as January or March
	 * yy 	: two digit representation of a year
	 * yyyy : full numeric representation of a year, 4 digits
	 * 
	 * @var string
	 */
	public $format	= "dd.mm.yyyy hh:ii";
	
	/**
	 * If true, a calendar symbol will be added
	 * @var bool
	 */
	public $hasAddon = true;
	
	/**
	 * Whether or not to allow date navigation by arrow keys.
	 * @var bool
	 */
	public $keyboardNavigation = true;
	
	/**
	 * The two-letter code of the language to use for month and day names. 
	 * These will also be used as the input's value (and subsequently sent to the server in the case of form submissions). 
	 * Currently ships with English ('en'), German ('de'), Brazilian ('br'), and Spanish ('es') translations, but others can be added (see I18N below). If an unknown language code is given, English will be used.
	 * 
	 * @var string
	 */
	public $language = "de";
	
	/**
	 * The highest view that the datetimepicker should show.
	 * 
	 * 0 or 'hour' for the hour view
	 * 1 or 'day' for the day view
	 * 2 or 'month' for month view (the default)
	 * 3 or 'year' for the 12-month overview
	 * 4 or 'decade' for the 10-year overview. Useful for date-of-birth datetimepickers.
	 *
	 * @var number >> 0 to 4
	 */
	public $maxView = 4;
	
	/**
	 * The lowest view that the datetimepicker should show.
	 * 
	 * 0 or 'hour' for the hour view
	 * 1 or 'day' for the day view
	 * 2 or 'month' for month view (the default)
	 * 3 or 'year' for the 12-month overview
	 * 4 or 'decade' for the 10-year overview. Useful for date-of-birth datetimepickers.
	 * 
	 * @var number >> 0 to 4
	 */
	public $minView = 0;
	
	/**
	 * The increment used to build the hour view. A preset is created for each minuteStep minutes.
	 * @var int
	 */
	public $minuteStep = 5;
	
	/**
	 * This option is currently only available in the component implementation. 
	 * With it you can place the picker just under the input field.
	 * Supported values are 'bottom-right' or 'bottom-left'
	 * @var string
	 */
	public $pickerPosition = "bottom-right";
	
	/**
	 * @var string
	 */
	public $placeholder;
	
	/**
	 * This option will enable meridian views for day and hour views.
	 * @var bool
	 */
	public $showMeridian = false;
	
	/**
	 * If false, the datepicker will not append the names of the weekdays to its view.
	 * Default behavior is appending the weekdays.
	 *
	 * @var bool
	 */
	public $showWeekDays = true;
	
	/**
	 * The earliest date that may be selected; all earlier dates will be disabled.
	 * Date should be in local timezone. String must be parsable with $format.
	 * 
	 * @var string
	 */
	public $startDate = null;
	
	/**
	 * Number, String. Default: 2, 'month'
	 * The view that the datetimepicker should show when it is opened. Accepts values of :
	 * 
	 * 0 or 'hour' for the hour view
	 * 1 or 'day' for the day view
	 * 2 or 'month' for month view (the default)
	 * 3 or 'year' for the 12-month overview
	 * 4 or 'decade' for the 10-year overview. Useful for date-of-birth datetimepickers.
	 *
	 * @var string
	 */
	public $startView = 2;
	
	/**
	 * The string that will appear on top of the datepicker. If empty the title will be hidden.
	 * @var string
	 */
	public $theTitle = "";
	
	/**
	 * If true or "linked", displays a "Today" button at the bottom of the datepicker to select the current date. 
	 * If true, the "Today" button will only move the current date into view; if "linked", the current date will also be selected.
	 * @var bool or string
	 */
	public $todayBtn = false;
	
	/**
	 * If true, highlights the current date.
	 * @var bool
	 */
	public $todayHighlight	 = false;
	
	/**
	 * The date value. Should match the format
	 * @var string
	 */
	public $value;
	
	/**
	 * With this option you can select the view from which the date will be selected. 
	 * It's set to the first one, so at each click the date will be updated.
	 * @var string
	 */
	public $viewSelect = "decade";
	
	/**
	 * Day of the week start. 0 (Sunday) to 6 (Saturday)
	 * @var int
	 */
	public $weekStart = 1;
	
	/**
	 * Whether or not to allow wheel navigation
	 * @var bool
	 */
	public $wheelNavigation = true;
	
	
	/**
	 * Datepicker
	 *
	 * @param string $id			>> id
	 * @param string $name			>> name
	 * @param string $value			>> date value
	 * @param string $placeholder	>> placeholder text
	 * @param string $format		>> date-format string
	 * @param E_VISIBLE $visible	>> set to <code>false</code> to hide the input
	 * @param E_ENABLED $enabled	>> set to <code>false</code> to disable the input
	 * @param array $styles			>> the usual tata
	 * @param array $classes		>> the usual tata
	 * @param array $attributes		>> the usual tata
	 *
	 *
	 * @return $this
	 */
	public function __construct($id, $name, $value, $placeholder="", $format="dd.mm.yyyy", $visible=true, $enabled=true, $styles=array(), $classes=array(), $attributes=array() )
	{
		parent::__construct($id, $name, $visible, $enabled, $styles, $classes, $attributes);

		self::setLanguage(lang("app_locale_short"));
		self::setDateFormat($format);
		self::setValue($value);
		self::setPlaceholder($placeholder);

		return $this;
	}

	/**
	 * Generates the output of HTML_Dialog
	 * @return $string
	 */
	public function generateHTML()
	{
		$this->defaultClasses["picker"] = array("date");

		$attributes = array(
			"data-date"=>$this->value,
			"data-date-autoclose"=>($this->autoclose ? 'true':'false'),
			"data-date-clear-btn"=>($this->clearBtn ? 'true':'false'),
			"data-date-dates-disabled"=>implode(",", $this->datesDisabled),
			"data-date-days-of-week-disabled"=>"[".implode(",", $this->daysOfWeekDisabled)."]",
			"data-date-enddate"=>$this->endDate,
			"data-date-force-parse"=>$this->forceParce,
			"data-date-format"=>$this->format,
			"data-date-hour-disabled"=>"",
			"data-date-keyboard-navigation"=>($this->keyboardNavigation ? 'true':'false'),
			"data-date-language"=>strtolower($this->language),
			"data-date-minute-disabled"=>"",
			"data-date-startdate"=>$this->startDate,
			"data-date-today-btn"=>($this->todayBtn ? 'true':'false'),
			"data-date-today-highlight"=>($this->todayHighlight ? 'true':'false'),
			"data-date-weekstart"=>$this->weekStart,
			
			"data-max-view"=>$this->maxView,
			"data-min-view"=>$this->minView,
			"data-minute-step"=>$this->minuteStep,
			"data-picker-position"=>$this->pickerPosition,
			"data-show-meridian"=>($this->showMeridian ? 'true':'false'),
			"data-start-view"=>$this->startView,
			"data-view-mode-wheel-navigation"=>"true",
			"data-view-mode-wheel-navigation-inverse-dir"=>"false",
			"data-view-select"=>$this->viewSelect
		);
		
		
		$input 		= new HTML_Input($this->id, $this->name, E_INPUTTYPE::TEXT, $this->placeholder, $this->value, "", "", $this->enabled, $this->visible, array(), array("date", "datepicker"), $attributes );
		$inputGroup = new HTML_InputGroup($this->id, $this->name, E_INPUTTYPE::TEXT, "", "", "", "", "", E_SELECTED::NO, $this->visible, $this->enabled, array(), array(), array());
		$inputGroup->setInput($input);
		
		if ($this->hasAddon)
		{
			$disabled = ($this->enabled == false ? 'disabled readonly':'');
			$inputGroup->setAddonRight('<span class="glyphicon glyphicon-calendar '.$disabled.'"></span>');
		}

		$com = $inputGroup->generateHTML();

		// write2Debugfile(self::DEBUG_FILENAME, "\nHTML_Datetimepicker generated\n".$com);
		return $com;
	}

	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// Setter functions for HTML_Datetimepicker
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	/**
	 * setter for <code>$this->addon_right</code>
	 *
	 * @param string $var
	 * @return $this
	 */
	public function setAddonRight($var){
		$this->addon_right = $var;
		return $this;
	}
	
	/**
	 * Setter for <code>$this->autoclose</code>
	 *
	 * @param bool $bool
	 * @return $this
	 */
	public function setAutoclose($bool){
		$this->autoclose = boolval($bool);
		return $this;
	}
	
	/**
	 * Setter for <code>$this->calendarWeeks</code>
	 *
	 * @param bool $bool
	 * @return $this
	 */
	public function setCalendarWeeks($bool){
		$this->calendarWeeks = boolval($bool);
		return $this;
	}
	
	/**
	 * Setter for <code>$this->clearBtn</code>
	 *
	 * @param bool $bool
	 * @return $this
	 */
	public function setClearButton($bool){
		$this->clearBtn = boolval($bool);
		return $this;
	}
	
	/**
	 * setter for <code>$this->dateFormat</code>
	 *
	 * @param string $formatstring
	 * @return $this
	 */
	public function setDateFormat($formatstring){
		$this->format = $formatstring;
		return $this;
	}
	
	/**
	 * Setter for <code>$this->datesDisabled</code>
	 *
	 * @param array $arg
	 * @return $this
	 */
	public function setDatesDisabled($arg){
		if (is_array($arg)){
			$this->datesDisabled = $arg;
		}
		return $this;
	}
	
	/**
	 * Setter for <code>$this->daysOfWeekDisabled</code>
	 *
	 * @param array $arg
	 * @return $this
	 */
	public function setDaysOfWeekDisabled($arg){
		if (is_array($arg)){
			$this->daysOfWeekDisabled = $arg;
		}
		return $this;
	}
	
	/**
	 * Setter for <code>$this->endDate</code>
	 *
	 * @param string $arg
	 * @return $this
	 */
	public function setEndDate($arg){
		$this->endDate = $arg;
		return $this;
	}
	
	/**
	 * Setter for <code>$this->forceParce</code>
	 *
	 * @param bool $arg
	 * @return $this
	 */
	public function setForceParse($arg){
		$this->forceParce = boolval($arg);
		return $this;
	}
	
	/**
	 * Setter for <code>$this->format</code>
	 *
	 * @param string $arg
	 * @return $this
	 */
	public function setFormat($arg){
		$this->format = $arg;
		return $this;
	}
	
	/**
	 * setter for <code>$this->hasAddon</code>
	 *
	 * @param bool $bool
	 * @return $this
	 */
	public function setHasAddon($bool){
		$this->hasAddon = $bool;
		return $this;
	}
	
	/**
	 * 
	 * @param bool $arg
	 * @return $this
	 */
	public function setKeyboardNavigation($arg){
		$this->keyboardNavigation = boolval($arg);
		return $this;
	}
	
	/**
	 * Setter for <code>$this->language</code>
	 *
	 * @param string $arg
	 * @return $this
	 */
	public function setLanguage($arg){
		$this->language = $arg;
		return $this;
	}
	
	/**
	 * Setter for <code>$this->maxView</code>
	 *
	 * @param string $arg
	 * @return $this
	 */
	public function setMaxView($arg){
	if (in_array($arg, $this->views_available) || array_key_exists($arg, $this->views_available)){
			$this->maxView = $arg;
		}
		return $this;
	}
	
	/**
	 * Setter for <code>$this->minView</code>
	 *
	 * @param string $arg
	 * @return $this
	 */
	public function setMinView($arg){
		if (array_key_exists($arg, $this->views_available)){
			$this->minView = $arg;
		}
		return $this;
	}
	
	/**
	 * Setter for <code>$this->minuteStep</code>
	 *
	 * @param int $arg
	 * @return $this
	 */
	public function setMinuteStep($arg){
		$this->minuteStep = intval($arg);
		return $this;
	}
	
	/**
	 * setter for <code>$this->pickerPosition</code>
	 *
	 * @param string $text
	 * @return $this
	 */
	public function setPickerPosition($string){
		if ($string == "bottom-left" || $string == "bottom-right"){
			$this->pickerPosition = $string;
		}
		return $this;
	}
	
	/**
	 * setter for <code>$this->placeholder</code>
	 *
	 * @param string $text
	 * @return $this
	 */
	public function setPlaceholder($string){
		$this->placeholder = $string;
		return $this;
	}
	
	/**
	 * Setter for <code>$this->showMeridian</code>
	 *
	 * @param bool $arg
	 * @return $this
	 */
	public function setShowMeridian($arg){
		$this->showMeridian = boolval($arg);
		return $this;
	}
	
	/**
	 * Setter for <code>$this->showWeekDays</code>
	 *
	 * @param bool $arg
	 * @return $this
	 */
	public function setShowWeekDays($arg){
		$this->showWeekDays = boolval($arg);
		return $this;
	}
	
	
	/**
	 * Setter for <code>$this->startDate</code>
	 *
	 * @param string $arg
	 * @return $this
	 */
	public function setStartDate($arg){
		$this->startDate = $arg;
		return $this;
	}
	
	/**
	 * Setter for <code>$this->startView</code>
	 *
	 * @param number $arg >> 0 to 4 (or from hour to decade)
	 * @return $this
	 */
	public function setStartView($arg){
		
		if (in_array($arg, $this->views_available) || array_key_exists($arg, $this->views_available)){
			$this->startView = $arg;
		}
		return $this;
	}
	
	/**
	 * Setter for <code>$this->theTitle</code>
	 *
	 * @param string $arg
	 * @return $this
	 */
	public function setTheTitle($arg){
		$this->theTitle = $arg;
		return $this;
	}
	
	/**
	 * Setter for <code>$this->todayBtn</code>
	 *
	 * @param bool $arg
	 * @return $this
	 */
	public function setTodayBtn($arg){
		$this->todayBtn = boolval($arg);
		return $this;
	}
	
	/**
	 * Setter for <code>$this->todayHighlight</code>
	 *
	 * @param bool $arg
	 * @return $this
	 */
	public function setTodayHighlight($arg){
		$this->todayHighlight = boolval($arg);
		return $this;
	}
	
	/**
	 * setter for <code>$this->value</code>
	 *
	 * @param string $value
	 * @return $this
	 */
	public function setValue($value){
		$this->value = $value;
		return $this;
	}
	
	/**
	 * Setter for <code>$this->weekStart</code>
	 *
	 * @param int $arg
	 * @return $this
	 */
	public function setWeekStart($arg){
		$this->weekStart = intval($arg);
		return $this;
	}

} // END OF >> HTML_Datetimepicker
// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..

/**
 * Modal dialogs
 *
 * @author _BA5E
 * @category helper
 * @package application\helpers\HTML_helper
 * @since 1.0
 * @version 1.2
 */
class HTML_Dialog extends HTML
{
	/**
	 * @var array
	 */
	protected $title_icons 		= array();		// default icons in correlation with the contextual color (primary, success etc.)
	
	/**
	 * @var string
	 */
	protected $dismissButton 	= '';
	
	/**
	 * @var E_DISMISSABLE
	 */
	protected $dismissable	 	= false;
	
	/**
	 * @var E_DATA_BACKDROPS
	 */
	protected $dataBackdrop		= 'static';
	
	/**
	 * @var string
	 */
	public $dialogHeader;						
	
	/**
	 * @var E_ICONS
	 */
	public $titleIcon;

	/**
	 * @var string
	 */
	public $content;
	
	/**
	 * @var string
	 */
	public $footer;
	
	/**
	 * @var E_COLOR
	 */
	public $color;
	
	/**
	 * @var array
	 */
	public $footerButtons	= array();

	/**
	 * @var bool
	 */
	public $showTitleIcon	= true;
	
	/**
	 * @var bool
	 */
	public $showCloseIcon 	= true;
	
	/**
	 * Modal dialog
	 * 
	 * Note (Keep in mind): 
	 * If you dont pass a color in the constructor, the default (<code>E_COLOR::STANDARD</code>) will be used which results also in a default title icon (<code>E_ICONS::BULLHORN</code>). If you use 
	 * the setter for the color after instantiation of the modal, the title icon will not be updated (so you need to use also the setter for the title icon if you want to change it). 
	 * 
	 *
	 * @param string $id			>> id
	 * @param string $name			>> name
	 * @param string $header		>> header text/content
	 * @param string $content		>> the content 
	 * @param string $footer		>> footer text/content
	 * @param E_COLOR $color		>> default, primary etc. <code>E_COLOR</code>
	 * @param E_VISIBLE $visible	>> pretty useless because its a dialog will be opened through data-toggle or the JS modal-method
	 * @param array $styles			>> array for additional styles
	 * @param array $classes		>> array for additional classes
	 * @param array $attributes		>> array for additional attributes
	 *
	 * @return HTML_Dialog
	 */
	public function __construct($id, $name, $header, $content, $footer="", $color="default", $visible=true, $styles=array(), $classes=array(), $attributes=array() )
	{
		parent::__construct($id, $name, $visible, E_ENABLED::YES, $styles, $classes, $attributes);

		$this->title_icons = array(
			"success" 			=> E_ICONS::CHECK,
			"info" 				=> E_ICONS::INFO_CIRCLE,
			"warning" 			=> E_ICONS::WARNING,
			"danger" 			=> E_ICONS::FIRE,
			"confirm" 			=> E_ICONS::QUESTION,
			"confirm_delete" 	=> E_ICONS::QUESTION,
			"default" 			=> E_ICONS::BULLHORN
		);
		
		$titleIcon = E_ICONS::BULLHORN;
		if (array_key_exists($color, $this->title_icons)){
			$titleIcon = $this->title_icons[$color];
		}
		
		// handle special "color"-cases, because we support a custom title icon for "confirm" and "confirm_delete" dialogs, but they are not valid contextual bootstrap colors anyway,
		// so we need to overwrite it before the setColor-Method will take over and overwrite it to the default
		if ($color == "confirm"){
			$color = E_COLOR::WARNING;
		}
		if ($color == "confirm_delete"){
			$color = E_COLOR::DANGER;
		}
		
		
		
		
		self::setHeader($header);
		self::setTitleIcon($titleIcon);
		self::setDataBackdrop(E_DATA_BACKDROPS::BACKDROP_ENABLED);
		self::setContent($content);
		self::setColor($color, false);
		self::setFooter($footer);
		self::setDismissable(E_DISMISSABLE::YES);

		return $this;
	}

	/**
	 * Generates the output of HTML_Dialog
	 * @return $string
	 */
	public function generateHTML($incl_defaultClasses=true)
	{
		$this->defaultClasses["dialog"] = array();
		if ($incl_defaultClasses){
			$this->defaultClasses["dialog"] = array("modal", "modal-message", "modal-".$this->color, "fade");
		}
		
		$this->defaultClasses["body"]	= array("modal-body");
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		$this->dismissButton = "";
		if ($this->dismissable === true)
		{
			$dBtn = new HTML_Button($this->id."-close", $this->id."-close", '<span class="text-'.$this->color.'">'.E_ICONS::CLOSE.'</span>', $this->color, E_SIZES::STANDARD);
			$dBtn
			->setAttributes( array("data-dismiss"=>"modal", "data-target"=>"#".$this->id, "title"=>lang("close") ))
			->setClasses( array("close", "modal-control") );
			
			$this->dismissButton = $dBtn->generateHTML(true);	// generate the button without the default btn-classes
		}
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		$header = "";
		if ($this->dialogHeader != "" || $this->dismissButton != ""){

			$header = '
			<div class="modal-header">
				<div class="modal-title">
					<h5 class="text-'.$this->color.'">'.$this->dismissButton.'
						<strong>'.
							$this->titleIcon.
							$this->dialogHeader.'
						</strong>
					</h5>
				</div>
			</div>';
		}

		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		$footer = "";
		if ($this->footer != "" || count($this->footerButtons) > 0)
		{
			$footer = '<div class="modal-footer">'.$this->footer.'&nbsp;';
			
			foreach ($this->footerButtons as $button) 
			{
				$callable_name = null;
				// fix to accept any markup (see ticket #9)
				if (is_object($button) && is_callable("generateHTML", true, $callable_name) ){
					// Element seems to be of type HTMLComponent.
					$footer .= $button->generateHTML();
				}
				else {
					$footer .= $button; // Accept any markup
				}
			}
			$footer .= '</div>';
		}
		
		$com = '
		<div id="'.$this->id.'" aria-hidden="'.($this->visible ? 'false':'true').'" data-backdrop="'.$this->dataBackdrop.'" data-keyboard="false" role="dialog" '.
			$this->getAttributesString().' '.
			$this->getClassString("dialog", true).' '.
			$this->getStyleString().'  
			>
			<div class="modal-dialog">
				<div class="modal-content">'.
					$header.'
					<div class="modal-body" id="conent_'.$this->id.'">'.$this->content.'</div>'.
					$footer.'
				</div>
			</div>
		</div>
		';		

		// write2Debugfile("dialog.log", "\nHTML_Dialog generated\n".$com);
		return $com;
	}

	/**
	 * Add as many HTML_Buttons as you want
	 * 
	 * @param HTML_Button $btn
	 * @return $this 
	 */
	public function addFooterButton($btn)
	{
		$this->footerButtons[] = $btn;
		return $this;
	}
	
	/**
	 * This is a shortcut function which creates a <code>HTML_Button</code> 
	 * It will also add the data-dismiss and data-target attributes (if $triggersDismiss is set true).
	 * 
	 * you should know, The button-ID is always " <modalID>-btn-cancel "
	 * 
	 * @param string $label			>> the button text
	 * @param E_ICONS $icon			>> default is E_ICONS::CANCEL but you can define any valid E_ICON (E_ICON::NO_ICON is also a valid one ;-) )
	 * @param E_SIZES $size			>> pass any value from E_SIZE or leave it empty to use the default
	 * @param bool $triggersDismiss	>> if you dont want the modal to close after clicking the button (maybe you want to validate something and close with js), then set this to false
	 * @param E_COLOR $customColor	>> per default, E_COLOR::STANDARD will be used, but you can overwrite it here
	 * 
	 * @return $this
	 */
	public function addFooterButtonCancel($label="", $icon='<i class="fa fa-times"></i>', $size="md", $triggersDismiss=true, $customColor=null)
	{
		if ($label == ""){
			$label = lang("cancel");
		}
		
		if (!E_ICONS::isValidValue($icon) ){
			$icon = E_ICONS::CANCEL;
		}
		if (!E_SIZES::isValidValue($size) ){
			$size = E_SIZES::STANDARD;
		}
		$color = E_COLOR::STANDARD; 	// per default we use the standard color (default)
		if ($customColor && E_COLOR::isValidValue($customColor) ){
			$color = $customColor;
		}
		
		$btn = new HTML_Button($this->id."-btn-cancel", $this->id."-btn-cancel", $label, $color, $size, $icon);
		
		if ($triggersDismiss === true){
			$btn->setAttributes( array("data-dismiss"=>"modal", "data-target"=>"#".$this->id) );
		}
	
		$this->footerButtons[] = $btn;
		
		// write2Debugfile("dialog.log", "\nFooter-Button-Cancel added\n".$btn->generateHTML());
		
		return $this;
	}
	
	
	/**
	 * This is a shortcut function which creates a <code>HTML_Button</code>
	 * It will also add the data-dismiss and data-target attributes (if $triggersDismiss is set true).
	 * 
	 * The button-ID is always " <modalID>-btn-ok "
	 * 
	 * @param string $label			>> the button text
	 * @param E_ICONS $icon			>> default is E_ICONS::CHECK but you can define any valid E_ICON (E_ICON::NO_ICON is also a valid one ;-) )
	 * @param E_SIZES $size			>> pass any value from E_SIZE or leave it empty to use the default
	 * @param bool $triggersDismiss	>> if you dont want the modal to close after clicking the button (maybe you want to validate something and close with js), then set this to false
	 * @param E_COLOR $customColor	>> per default, the color of the modal will be used for the OK-Button. but you can change it 
	 * 
	 * @return $this
	 */
	public function addFooterButtonOK($label="", $icon='<i class="fa fa-check"></i>', $size="md", $triggersDismiss=true, $customColor=null)
	{
		if ($label == ""){
			$label = lang("ok");
		}
		
		if (!E_ICONS::isValidValue($icon) ){
			$icon = E_ICONS::CHECK;
		}
		if (!E_SIZES::isValidValue($size) ){
			$size = E_SIZES::STANDARD;
		}
		
		$color = $this->color; 	// per default we use the modals color for the OK-Button
		if ($customColor && E_COLOR::isValidValue($customColor) ){
			$color = $customColor;
		}
		
		$btn = new HTML_Button($this->id."-btn-ok", $this->id."_btn_ok", $label, $color, $size, $icon);
		if ($triggersDismiss === true){
			$btn->setAttributes( array("data-dismiss"=>"modal", "data-target"=>"#".$this->id) );
		}
		$btn->addAttribute("autofocus", "");
		
		$this->footerButtons[] = $btn;
		return $this;
	}
	
	
	
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// Setter functions for HTML_Dialog
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	/**
	 * setter for <code>$this->content</code>
	 *
	 * @param string $text
	 * @return $this
	 */
	public function setHeader($header){
		$this->dialogHeader = $header;
		return $this;
	}
	
	/**
	 * setter for <code>$this->content</code>
	 *
	 * @param string $text
	 * @return $this
	 */
	public function setContent($content){
		$this->content = $content;
		return $this;
	}
	
	/**
	 * setter for <code>$this->color</code>
	 *
	 * @param E_COLOR $color 		>> a valid contextual color
	 * @param bool $setTitleIcon 	>> setting the color per default updates also the default tile icon 
	 * 
	 * @return $this
	 */
	public function setColor($color, $setTitleIcon=true){
		if (!E_COLOR::isValidValue($color) ){
			$color = E_COLOR::STANDARD;
		}
		$this->color = $color;
		
		if ($setTitleIcon === true){
			self::setTitleIcon($this->title_icons[$color]);
		}
		return $this;
	}
	
	/**
	 * setter for <code>$this->titleIcon</code>
	 *
	 * @param E_ICONS $icon >> pass empty string to disable the icon
	 * @return $this
	 */
	public function setTitleIcon($icon=""){
		if ($icon != "" && !E_ICONS::isValidValue($icon) ){
			$icon = E_ICONS::BULLHORN;
		}
		$this->titleIcon = $icon.($icon != "" ? "&nbsp;":""); // add non breakable space when icon is defined
		return $this;
	}
	
	/**
	 * setter for <code>$this->dataBackdrop</code>
	 *
	 * @param string $backdrop
	 * @return $this
	 */
	public function setDataBackdrop($backdrop)
	{
		if (!E_DATA_BACKDROPS::isValidValue($backdrop) ){
			$backdrop = E_DATA_BACKDROPS::BACKDROP_STATIC;
		}
		$this->dataBackdrop = $backdrop;
		return $this;
	}
	
	
	/**
	 * sets the dismissable string for the modal dialog
	 *
	 * @param string $dismissable 	>> if <code>true</code>, an dismiss button will be added to the panel header
	 * @return HTML_Dialog
	 */
	public function setDismissable($dismissable=true)
	{
		if (!E_DISMISSABLE::isValidValue($dismissable) ){
			$dismissable = E_DISMISSABLE::YES;
		}
		$this->dismissable = $dismissable;
		
		return $this;
	}

	/**
	 * setter for <code>$this->footer</code>
	 *
	 * @param string $footer
	 * @return $this
	 */
	public function setFooter($footer){
		$this->footer = $footer;
		return $this;
	}
	
} // END OF >> HTML_Dialog


// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
/**
 * HTML Formular
 *
 * @author _BA5E
 * @category helper
 * @package application\helpers\HTML_helper
 * @since 0.2
 * @version 1.2
 * 
 * @todo finish vertical and inline support. the markup for a vertical form differs. thats why the vertical mode dont looks nice
 */
class HTML_Form extends HTML
{
	/**
	 * @var array
	 */
	protected $formElements = array();		// holds all form elements

	/**
	 * @var E_FORMLAYOUT
	 */
	private $direction = 'form-horizontal';	// form layout -> use setter method for this
	
	/**
	 * @var string
	 */
	public $formTitle;

	/**
	 * @var E_FORMMETHOD
	 */
	public $method;
	
	/**
	 * @var string
	 */
	public $action;

	/**
	 *  HTML_Form 
	 *  A Formular 
	 *
	 * @param string $id			>> id
	 * @param string $name			>> name
	 * @param string $action>		> form action
	 * @param string $formTitle		>> if set, a legend element will be added at the top of the form 
	 * @param E_FORMMETHOD $method	>> form method (POST / GET)
	 * @param E_VISIBLE $visible	>> set to <code>false</code> to make the alert hidden
	 * @param E_ENABLED $enabled	>> set to <code>false</code> to disable the alert
	 * @param array $styles			>>
	 * @param array $classes		>>
	 * @param array $attributes		>>
	 *
	 * @return HTML_Button
	 */
	public function __construct($id, $name, $action, $formTitle="", $method="POST", $visible=true, $enabled=true, $horizontal=true, $styles=array(), $classes=array(), $attributes=array())
	{
		//write2Debugfile(self::DEBUG_FILENAME, "construct HTML_Form\nid[$id] name[$name] action[$action] formTitle[$formTitle] method[$method] visible[$visible] enabled[$enabled] horizontal[$horizontal]");
		
		parent::__construct($id, $name, $visible, $enabled, $styles, $classes, $attributes);

		self::setAction($action);
		self::setDirection($horizontal);
		self::setFormTitle($formTitle);
		self::setMethod($method);
	}
	
	/**
	 * Adds FormItems to <code>$this->formElements</code>
	 * 
	 * @param HTML_FormItem $item
	 * @return HTML_Form
	 */
	public function addFormItem($item)
	{
		$this->formElements[] = $item;
		return $this;
	}
	
	/**
	 * generates the output for each form element
	 * @return string
	 */
	private function generateFormElementsHTML()
	{
		$return = "";
		if (is_array($this->formElements) && count($this->formElements) > 0 )
		{
			foreach ($this->formElements as $value) 
			{
				$callable_name = null;
				if (is_object($value) && is_callable("generateHTML", true, $callable_name) )
				{
					if (get_class($value) == "HTML_FormItem"){
						$value->direction = $this->direction;
					}
					
					// Element maybe of type HTMLComponent.
					$return .= $value->generateHTML();
				}
				else {
					$return .= $value;
				}
			}
		}
		
		//write2Debugfile(self::DEBUG_FILENAME, "\nHTML_Form -> elements generated\n".$return);
		return $return;
	}

	/**
	 * Generates the output of HTML_Form
	 * 
	 * @param bool $asDiv >> generates a div instead of a form if true.
	 * @return $string
	 */
	public function generateHTML($asDiv=false)
	{
		$this->defaultClasses["form"] = array("form", $this->direction);
		
		$tag = ($asDiv ? 'div':'form');
		
		$enabled = "";
		if ($this->enabled === false){
			$enabled = ' disabled=""';
		}
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		$title = "";
		if ($this->formTitle != ""){
			$title = self::createLegend( $this->formTitle );
		}
		
		$com = '
		<'.$tag.' id="'.$this->id.'" name="'.$this->name.'" '.($asDiv ? '' : 'action="'.$this->action.'" method="'.$this->method.'"').
			$this->getAttributesString().' '.
			$this->getClassString('form', true).' '.
			$this->getStyleString().'
			>
			<fieldset'.$enabled.'>
				'.$title.' '.
				self::generateFormElementsHTML().
			'</fieldset>
		</'.$tag.'>';
		
		//write2Debugfile(self::DEBUG_FILENAME, "\nHTML_Form generated\n".$com);
		return $com;
	}

	/**
	 * @param string $title
	 * @return string
	 */
	public static function createLegend($title)
	{
		$return = '<legend>'.$title.'</legend>';
		return $return;
	}
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// Setter functions for HTML_Form
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	/**
	 * setter for <code>$this->action</code>
	 *
	 * @param string $action
	 * @return $this
	 */
	public function setAction($action=true)
	{
		$this->action = $action;
		return $this;
	}
	
	/**
	 * setter for <code>$this->direction</code>
	 *
	 * @param E_FORMLAYOUT $horizontal
	 * @return $this
	 */
	public function setDirection($formlayout)
	{
		if (! E_FORMLAYOUT::isValidValue($formlayout) ){
			$formlayout = E_FORMLAYOUT::HORIZONTAL;
		}
		
		$this->direction = $formlayout;
		return $this;
	}
	
	/**
	 * setter for <code>$this->formTitle</code>
	 *
	 * @param string $title
	 * @return $this
	 */
	public function setFormTitle($title="")
	{
		$this->formTitle = $title;
		return $this;
	}
	
	/**
	 * setter for <code>$this->method</code>
	 * 
	 * @param E_FORMMETHOD $method
	 * @return HTML_Form
	 */
	public function setMethod($method=true)
	{
		if (! E_FORMMETHOD::isValidValue($method) ){
			$method	= E_FORMMETHOD::POST;
		}
		$this->method = $method;
		return $this;
	}
	
} // END OF >> HTML_Form

/**
 * HTML Formular element
 * @todo FIX LAYOUT OPTIONS
 * 
 * @author _BA5E
 * @category helper
 * @package application\helpers\HTML_helper
 * @since 0.2
 * @version 1.2
 */
class HTML_FormItem extends HTML
{
	public $direction 		= "form-horizontal";
	/**
	 * @var array
	 */
	public $colRatio		= array(4, 8); // 3 cols for the label & 9 columns for the components 
	
	/**
	 * @var array
	 */
	public $components		= array();
	
	/**
	 * @var string
	 */
	public $label			= "";
	
	/**
	 * @var string
	 */
	public $labelColor		= "";
	
	/**
	 * @var string
	 */
	public $labelForWhat	= "";
	
	/**
	 * @var E_REQUIRED
	 */
	public $required 		= false;
	
	/**
	 * @var E_VALIDATION_STATES
	 */
	public $validationState	= "";
	
	/**
	 *  HTML_FormItem
	 *  A Formular item with 2 columns (label & components) for use in a HTML_Form 
	 *
	 * @param string $label							>> labeltext for the 1st column
	 * @param string $id							>> id
	 * @param string $forWhat						>> for="" -attribute for the label (usually the id of the input)
	 * @param array $com							>> you can pass all elements for the 2nd column here
	 * @param E_REQUIRED $required					>> if set to true, the label will be extended with a required icon  
	 * @param array $colRatio						>> defines the column-ratio between the 1st and the 2nd column (12 in summ is required)
	 * @param E_VALIDATION_STATES $validationState	>> validation state for the resulting 'form-group'
	 * @param E_VISIBLE	$visible					>> 
	 * @param E_ENABLED $enabled					>> 
	 * @param array $styles							>> custom styles
	 * @param array $classes						>> custom classes
	 * @param array $attributes						>> custom attributes
	 *
	 * @return HTML_FormItem
	 */
	public function __construct($label, $id, $forWhat, $com=array(), $required=false, $colRatio=array(4, 8), $validationState="", $visible=true, $enabled=true, $styles=array(), $classes=array(), $attributes=array() )
	{
		parent::__construct($id, $id, $visible, $enabled, $styles, $classes, $attributes);
	
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		
		self::addComponent($com);
		self::setColumnRatio($colRatio);
		self::setLabel($label);
		self::setValidationState($validationState);
		self::setRequired($required);

		$this->labelColor 		= "black";
		$this->labelForWhat		= $forWhat;
		
		return $this;
	}
	
	/**
	 * generates the output string for all components (2nd column)
	 */
	private function generateFormItemsHTML()
	{
		$return = "";
		if (is_array($this->components) && count($this->components) > 0)
		{
			//echo nl2br(print_r($this->components, true));
			foreach ($this->components as $value)
			{
				$callable_name = null;
				if (is_object($value) && is_callable("generateHTML", true, $callable_name) )
				{	
					if ($this->required == E_REQUIRED::YES){
						//$value->addAttribute("required");
					}
					
					// Element is of type HTMLComponent.
					if ($this->direction == E_FORMLAYOUT::VERTICAL){
						$value->addClass("form-control");
					}

					$return .= $value->generateHTML();
				}
				else {
					$return .= $value;
				}
			}
		}
		
		// write2Debugfile(self::DEBUG_FILENAME, "\nHTML_FormItem components generated\n".$return);
		return $return;
	}
	
	/** 
	 * 
	 * Generates the output of HTML_FormItem
	 * 
	 * @param bool $noMargins >> no-margin class will be added if true
	 * @return $string
	 */
	public function generateHTML($noMargins=false)
	{
		$lbl_cls = "";
		if ($this->direction == E_FORMLAYOUT::HORIZONTAL){
			$lbl_cls = "control-label";
		}
		
		
		$this->defaultClasses = array(
			"label" => array($lbl_cls,  "col-sm-".$this->colRatio[0] , "label-$this->labelColor", ($this->required ? 'is_required': '')),
			"item" => array("form-group", $this->validationState, ($this->required ? 'required': '') ),
			"component" => array("col-sm-".$this->colRatio[1])
		);
		
		if ($noMargins){
			$this->defaultClasses["item"][] = "no-margin";
		}
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		/*
		$required = "&nbsp;";
		if ($this->required === true){
			$required = E_ICONS::FORMITEM_REQUIRED;
		}
		*/
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		if ($this->label != ""){
			//$this->label .= "&nbsp;".$required;
		}
		//$this->label .= "&nbsp;".$required;
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		
		if ($this->direction == E_FORMLAYOUT::HORIZONTAL)
		{
			$formItem = '
			<div id="'.$this->id.'" '.
				$this->getAttributesString().' '.
				$this->getClassString('item', true).' '.
				$this->getStyleString('item').'>
			
				<label id="lbl_'.$this->id.'" for="'.$this->labelForWhat.'" '.
					$this->getClassString('label').' >'.
					$this->label.'
				</label>
					
				<div '.$this->getClassString('component').' >
					'.self::generateFormItemsHTML().'
				</div>
			</div>
			';
		}
		elseif ($this->direction == E_FORMLAYOUT::INLINE)
		{
			$formItem = '
			<div id="'.$this->id.'" '.
						$this->getAttributesString().' '.
						$this->getClassString('item', true).' '.
						$this->getStyleString('item').'>
		
				<label id="lbl_'.$this->id.'" for="'.$this->labelForWhat.'" '.
							$this->getClassString('label').' >'.
							$this->label.'
				</label>
			
				<div '.$this->getClassString('component').' >
					'.self::generateFormItemsHTML().'
				</div>
			</div>
			';
		}
		else 
		{
			$formItem = '
			<div id="'.$this->id.'" '.
				$this->getAttributesString().' '.
				$this->getClassString('item', true).' '.
				$this->getStyleString('item').'>
		
				<label id="lbl_'.$this->id.'" for="'.$this->labelForWhat.'" '.$this->getClassString('label').' >'.$this->label.'</label>
				'.self::generateFormItemsHTML().'
			</div>
			';
		}
		
		
			
		
		// write2Debugfile(self::DEBUG_FILENAME, "\nHTML_FormItem generated\n".$formItem);
		return $formItem;
	}
	
	public static function buildLegendItem($content)
	{
		return '<legend>'.$content.'</legend>';
	}
	
	public static function buildFormControl($content)
	{
		return '<p class="form-control-static">'.$content.'</p>';
	}
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// Setter functions for HTML_FormItem
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	/**
	 * validate and set <code>$this->colRatio</code>
	 *
	 * @param array $columnRatio >> array(3, 9)
	 * @return $this
	 */
	public function setColumnRatio($columnRatio){
		
		if (!is_array($columnRatio) or count($columnRatio) != 2){
			$columnRatio = array(3, 9);
		}
		
		$this->colRatio = $columnRatio;
		return $this;
	}
	
	/**
	 * setter for <code>$this->label</code>
	 *
	 * @param string $label
	 * @return $this
	 */
	public function setLabel($label)
	{
		$this->label = $label;
		return $this;
	}
	
	/**
	 * setter for <code>$this->required</code>
	 *
	 * @param bool $required
	 * @return $this
	 */
	public function setRequired($required)
	{
		if (!E_REQUIRED::isValidValue($required) ){
			$required = E_REQUIRED::YES;
		}
		$this->required = $required;
		return $this;
	}
	
	/**
	 * sets the validationState 
	 *
	 * @param E_VALIDATION_STATES $state
	 */
	public function setValidationState($state)
	{
		if (!E_VALIDATION_STATES::isValidValue($state) ){
			$state = E_VALIDATION_STATES::NONE;
		}
		$this->validationState = $state;
		
		return $this;
	}
	
	/**
	 * simply adds your component of type whatever to the components array for the 2nd column.
	 * you can pass a object with callable <code>generateHTML -Method</code> or plain HTML 
	 */
	public function addComponent($component)
	{
		if (is_array($component)){
			foreach ($component as $component) {
				$this->components[] = $component;
			}
		}else{
			$this->components[] = $component;
		}
			
		return $this;
	}
	
} // END OF >> HTML_FormItem

// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
/**
 * HTML Image
 *
 * @author _BA5E
 * @category helper
 * @package application\helpers\HTML_helper
 * @since 0.4
 * @version 1.0
 */
class HTML_Image extends HTML
{
	/**
	 * @var string
	 */
	public $text;
	
	/**
	 * @var string
	 */
	public $src;
	
	/**
	 * @var string
	 */
	public $width;
	
	/**
	 * @var string
	 */
	public $height;
	
	/**
	 * @var E_IMAGE_STYLES
	 */
	public $decoration;

	/**
	 * creates an image element
	 *
	 * @param string $id					>> id
	 * @param string $name					>> name
	 * @param string $src					>> image source
	 * @param string $text					>> text
	 * @param string $width					>> image width
	 * @param string $height				>> image height
	 * @param E_IMAGE_STYLES $decoration	>> ROUNDED, CIRCLE, THUMBNAIL
	 * 
	 * @param E_VISIBLE $visible	>>
	 * @param E_ENABLED $enabled	>> 
	 * @param array $styles			>>
	 * @param array $classes		>>
	 * @param array $attributes		>>
	 *
	 * @return HTML_Image
	 */
	public function __construct($id, $name, $src, $text, $width, $height, $decoration="", $visible=true, $enabled=true, $styles=array(), $classes=array(), $attributes=array())
	{
		parent::__construct($id, $name, $visible, $enabled, $styles, $classes, $attributes);
		
		self::setText($text);
		self::setSource($src);
		self::setWidth($width);
		self::setHeight($height);
		self::setDecoration($decoration);
	}

	/**
	 * Generates the output of HTML_Image
	 * @return $string
	 */
	public function generateHTML()
	{
		$this->defaultClasses["image"] 	= array($this->decoration);
		
		$com = '<img src="'.$this->src.'" id="'.$this->id.'" alt="'.$this->text.'" width="'.$this->width.'" height="'.$this->height.'" '.
			$this->getAttributesString().' '.
			$this->getClassString('image', true).'  '.
			$this->getStyleString().'>';

		// write2Debugfile(self::DEBUG_FILENAME, "\nHTML_Image generated\n".$com);
		return $com;
	}
	
	static function generate($src_path, $width=80, $height=60)
	{
		$img = new HTML_Image(time(), time(), HTML_Image::generateDataURIFromImage($src_path), "", $width, $height);
		return $img->generateHTML();
	}
	
	static function generatePlaceholderSVG($width, $height, $text="", $fill="#CFCFCF", $font_size=1.6)
	{
		$color = new Color($fill);
		
		$svg = 'data:image/svg+xml;base64,'.base64_encode('
		<svg xmlns="http://www.w3.org/2000/svg"	width="'.$width.'" height="'.$height.'" >
			<rect width="'.$width.'" height="'.$height.'" fill="'.$fill.'"/>
			<text text-anchor="middle" x="'.($width/2).'" y="'.($height/2).'"  fill="'.$color->get_complementary($fill).'"
				style="font-weight:bold; font-size:'.$font_size.'px; font-family:Arial,Helvetica,sans-serif; dominant-baseline:central">'.$text.'
			</text>
		</svg>');
		
		return $svg;
	}
	
	/**
	 * Scan $directory for images and return them as array
	 * 
	 * @param string $directory
	 * @return array
	 */
	static function scanDirectory($directory){
		return HTML_Carousel::scanDirectory($directory);
	}
	
	/**
	 * Resize image to match max height and or max width and keeping the originale scale
	 * It makes use of the imagick library if available. Otherwise fallback to GD2
	 *
	 * @author Marco Eberhardt
	 * @version 1.0
	 *
	 * @param string $src
	 * @param string $target
	 * @param int $width
	 * @param int $height
	 */
	public static function resize_image($src, $target, $max_width, $max_height)
	{
		list($old_width, $old_height) = getimagesize($src);
		
		// original scale
		$xscale	= $old_width / $max_width;
		$yscale	= $old_height / $max_height;
		
		//new dimensions with same scale
		if ($yscale > $xscale)
		{
			$new_width 	= round($old_width * (1/$yscale));
			$new_height = round($old_height * (1/$yscale));
		}
		else
		{
			$new_width 	= round($old_width * (1/$xscale));
			$new_height = round($old_height * (1/$xscale));
		}
		
		
		if (extension_loaded('imagssick'))
		{
			$imagick = new Imagick($src);
			
			/*
			$profiles 			= $imagick->getImageProfiles('*', false); // get profiles
			$has_icc_profile 	= (array_search('icc', $profiles) !== false); // we're interested if ICC profile(s) exist
			if ($has_icc_profile === false)
			{	// image does not have CMYK ICC profile, we add one
				$icc_cmyk = file_get_contents(FCPATH.'/resources/icc_profiles/CMYK/USWebUncoated.icc');
				$imagick->profileImage('icc', $icc_cmyk);
			}
			// Then we need to add RGB profile
			$icc_rgb = file_get_contents(FCPATH.'/resources/icc_profiles/RGB/USWebUncoated.icc');
			$imagick->profileImage('icc', $icc_rgb);
			
			$imagick->setimagecolorspace(Imagick::COLORSPACE_RGB);
			*/
			$imagick->resizeImage($new_width, $new_height, imagick::FILTER_LANCZOS, 0.9, true);
			
			$resize = $imagick->writeimage($target);
		}
		else
		{
			$config = array();
			$config['image_library'] 	= 'GD2';
			$config['source_image'] 	= $src;
			$config['new_image'] 		= $target;
			$config['maintain_ratio'] 	= TRUE;
			$config['width']         	= $new_width;
			$config['height']       	= $new_height;
			
			$ci	=& get_instance();
			if (! class_exists('image_lib')){
				$ci->load->library('image_lib', $config);
			}
			
			$ci->image_lib->initialize($config);
			
			$resize = $ci->image_lib->resize();
			
			if ( ! $resize){
				throw new Exception($ci->image_lib->display_errors());
			}
			
			$ci->image_lib->clear();
		}
		return $resize;
	}
	
	/**
	 * @deprecated Use HTML_Image::resize_image instead
	 * 
	 * Static method to resize an image using GD2-Library
	 * 
	 * @param string $source_file	>> absolute path to the source image
	 * @param int $width			>> the desired image height
	 * @param int $height			>> the desired image width
	 * @param string $target_path	>> relative path to a target directoy (by default its the same directory the source file is located)
	 * @param string $suffix		>> filename suffix
	 * 
	 * @return bool
	 */
	static function resize($source_file, $width, $height, $target_path="/", $suffix="_thumb")
	{
		if (!is_file($source_file)){
			return false;
		}
		
		if (!is_dir($target_path)){
			mkdir($target_path);
		}
		
		$config = array();
		$config['image_library'] 	= 'gd2';
		$config['source_image'] 	= $source_file;
		$config['thumb_marker']		= $suffix;
		$config['create_thumb'] 	= TRUE;
		$config['maintain_ratio'] 	= TRUE;
		$config['width']         	= $width;
		$config['height']       	= $height;
		
		$ci =& get_instance();
		if (! class_exists('image_lib')){
			$ci->load->library('image_lib', $config);
		}
		
        $ci->image_lib->initialize($config);
        $resize = $ci->image_lib->resize();

		write2Debugfile("image-resize.log", "\nresize image [$source_file]");
		
		if ($resize)
		{
			if ($target_path != "/" && is_dir($target_path))
			{
				$src_filename 	= pathinfo($source_file, PATHINFO_FILENAME);
				$src_filepath 	= pathinfo($source_file, PATHINFO_DIRNAME);
				$src_fileext 	= pathinfo($source_file, PATHINFO_EXTENSION);
				
				$thumb_path		= str_replace(".".$src_fileext, $suffix.".".$src_fileext, $source_file);
				$thumb_filename	= pathinfo($thumb_path, PATHINFO_FILENAME).".".pathinfo($thumb_path, PATHINFO_EXTENSION);
				
				$target_path	= $target_path."/".$thumb_filename;

				write2Debugfile("image-resize.log", "move file:\nsrc-filepath[$src_filepath] \nsrc-filename[$src_filename] \nsrc-ext[$src_fileext]\nthumb-path[$thumb_path] \nto target path [$target_path] ");
				
				if (is_file($thumb_path)){
					rename($thumb_path, $target_path);
				}
			}
			
			
		}

        $ci->image_lib->clear();
		return $resize;
	}
	
	
	/**
	 * generates a data uri string from an image 
	 * 	data:[<mime type>][;charset=<charset>][;base64],<encoded data>
	 * 
	 * @param string $path full path to the image
	 * @return string >> base64 data-uri
	 */
	static function generateDataURIFromImage($path, $txt_placeholder="404")
	{
		$data = @file_get_contents($path);
		if ($data !== false)
		{
			//$type 	= pathinfo($path, PATHINFO_EXTENSION);
			$type 	= substr( strrchr($path, "."), 1);
			//$data 	= file_get_contents($path);
			$base64 = 'data:image/'. $type.';base64,'.base64_encode($data);
			return $base64;
		}
		return self::generatePlaceholderSVG(120, 120, $txt_placeholder);
	}
	
	private function scaleImage($image, $maxWidth=128, $maxHeight=96)
	{
		//return $ret_arr;
	}
	

	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// Setter functions for HTML_Image
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	/**
	 * setter for <code>$this->decoration</code>
	 * 
	 * @param E_IMAGE_STYLES $decoration
	 * @return $this
	 */
	public function setDecoration($decoration){
		if (! E_IMAGE_STYLES::isValidValue($decoration)){
			$decoration = E_IMAGE_STYLES::THUMBNAIL;
		}
		$this->decoration = $decoration; 
		return $this;
	}
	
	/**
	 * setter for <code>$this->text</code>
	 *
	 * @param string $text
	 * @return $this
	 */
	public function setText($text){
		$this->text = $text;
		return $this;
	}
	
	/**
	 * setter for <code>$this->src</code>
	 *
	 * @param string $src
	 * @return $this
	 */
	public function setSource($src){
		$this->src = $src;
		return $this;
	}
	
	/**
	 * setter for <code>$this->width</code>
	 *
	 * @param string $width
	 * @return $this
	 */
	public function setWidth($width){
		$this->width = $width;
		return $this;
	}
	
	/**
	 * setter for <code>$this->height</code>
	 *
	 * @param string $height
	 * @return $this
	 */
	public function setHeight($height){
		$this->height = $height;
		return $this;
	}
	

} // END OF >> HTML_Image

// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
/**
 * HTML Input
 * 
 * @todo this component needs a redesign.
 * 	its simply too much to allow all E_INPUTTYPE's. they differ to much to keep this class simple stupid
 *  This component should only create textual input elements (text, password and textarea)
 * 
 * @author _BA5E
 * @category helper
 * @package application\helpers\HTML_helper
 * @since 0.4
 * @version 1.2
 */
class HTML_Input extends HTML
{
	/**
	 * @var array
	 */
	protected $supprtedInputTypes = array();
	
	/**
	 * @var string
	 */
	protected $disabled 			= "";
	
	/**
	 * @var string
	 */
	protected $selectedString		= "";	// for checkboxes and radios
	
	/**
	 * @var E_INPUTTYPE
	 */
	public $type;
	
	/**
	 * @var string
	 */
	public $value;
	
	/**
	 * @var E_SELECTED
	 */
	public $selected;	// for checkboxes and radios
	
	/**
	 * @var string
	 */
	public $labelBefore;
	
	/**
	 * @var string
	 */
	public $labelAfter;
	
	/**
	 * @var string
	 */
	public $placeholder;
	
	/**
	 * HTML_Input
	 *
	 * @param string $id			>> id
	 * @param string $name			>> name
	 * @param string $type			>> label text
	 * @param string $value			>> input value
	 * @param string $placeholder	>> placeholder
	 * @param string $labelBefore	>> 
	 * @param string $labelAfter	>> 
	 * @param E_SELECTED $selected	>> checked attribute (only relevant for inputs of type <code>E_INPUTTYPE::RADIO</code> or <code>E_INPUTTYPE::CHECKBOX</code> )
	 * @param E_VISIBLE	$visible	>>
	 * @param E_ENABLED $enabled	>>	
	 * @param array $styles			>> as usual
	 * @param array $classes		>> as usual
	 * @param array $attributes		>> as usual
	 * 
	 * @return HTML_Input
	 */
	public function __construct($id, $name, $type, $placeholder="", $value="", $labelBefore="", $labelAfter="", $enabled=true, $visible=true, $styles=array(), $classes=array(), $attributes=array() )
	{
		parent::__construct($id, $name, $visible, $enabled, $styles, $classes, $attributes);
		
		$this->supprtedInputTypes = array(E_INPUTTYPE::TEXT, E_INPUTTYPE::PASSWORD, E_INPUTTYPE::CHECKBOX, E_INPUTTYPE::RADIO, E_INPUTTYPE::EMAIL, E_INPUTTYPE::HIDDEN, E_INPUTTYPE::URL, E_INPUTTYPE::SUBMIT, E_INPUTTYPE::SEARCH, E_INPUTTYPE::NUMBER);
		if (! in_array($type, $this->supprtedInputTypes)){
			throw new Exception("the specified input type [$type] is not supported by HTML_Input");
		}
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		self::setEnabled($enabled);
		self::setLabelAfter($labelAfter);
		self::setLabelBefore($labelBefore);
		self::setPlaceholder($placeholder);
		self::setType($type);
		self::setValue($value);
		
		return $this;
	}
	
	/**
	 * Generates the output of HTML_Input
	 * @return $string
	 */
	public function generateHTML()
	{
		$this->defaultClasses["input"] = array("form-control");
		
		if ($this->type == E_INPUTTYPE::PASSWORD){
			$this->addAttribute("autocomplete", "off");
		}
		
		$input = '
		<input id="'.$this->id.'" name="'.$this->name.'" type="'.$this->type.'" value="'.$this->value.'" '.$this->selectedString.'  '.$this->disabled.' '.
			$this->getAttributesString().' '.
			$this->getClassString('input', true).' '.
			$this->getStyleString().' 
			placeholder="'.$this->placeholder.'"
		>';
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		if ($this->labelBefore != "" || $this->labelAfter != "" )
		{
			$input = $this->labelBefore.' '.$input.' '.$this->labelAfter;
			//$input = '<label>'.$this->labelBefore.$input.$this->labelAfter.'</label>';
			//$input = '<span>'.$this->labelBefore.'</span>'.$input.'<span>'.$this->labelAfter.'</span>';
		}
		
		// write2Debugfile(self::DEBUG_FILENAME, "\nHTML_Input generated\n".$input);
		return $input;
	}
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// Setter functions for HTML_Input
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	public function setEnabled($enabled)
	{
		if (! E_ENABLED::isValidValue($enabled) ){
			$enabled = E_ENABLED::YES;
		}
		
		parent::setEnabled($enabled);	// the parent setter stores just the bool state
		
		if ($enabled === true){
			$this->disabled = "";
		}else{
			$this->disabled = 'disabled=""';
		}
	}

	public function setLabelAfter($string){
		$this->labelAfter = $string; return $this;
	}
	public function setLabelBefore($string){
		$this->labelBefore = $string; return $this;
	}
	
	public function setPlaceholder($string){
		$this->placeholder = $string; return $this;
	}
	
	public function setType($type)
	{
		if (! E_INPUTTYPE::isValidValue($type) ){
			$type = E_INPUTTYPE::TEXT;
		}
		$this->type = $type;
	}
	public function setValue($value)
	{
		$this->value = $value;
	}
	
} // END OF >> HTML_Input

// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
/**
 * @todo - remove input type
 * 	create add component-method for n HTML 
 * 
 * @author _BA5E
 * @category helper
 * @package application\helpers\HTML_helper
 * @since 0.4
 * @version 1.1
 */
class HTML_InputGroup extends HTML
{
	/**
	 * @var E_INPUTTYPE
	 */
	public $inputType;

	/**
	 * @var E_SELECTED
	 */
	public $selected 		= false;
	
	/**
	 * @var string
	 */
	public $placeholder;
	
	/**
	 * @var string
	 */
	public $inputLabelBefore = "";
	
	/**
	 * @var mixed
	 */
	public $input;
	
	/**
	 * @var string
	 */
	public $inputLabelAfter = "";
	
	/**
	 * @var string
	 */
	public $addonLeft;
	
	/**
	 * @var string
	 */
	public $addonRight;
	
	/** @var string */
	public $buttonLeft = "";
	
	/** @var string */
	public $buttonRight = "";
	
	
	/**
	 * HTML_InputGroup
	 * 
	 * Your component will be wrapped into an input-group
	 * 
	 * @param string $id
	 * @param string $name
	 * @param E_INPUTTYPE $inputType
	 * @param string $placeholder
	 * @param string $inputLabelBefore
	 * @param string $inputLabelAfter
	 * @param string $leftAddon
	 * @param string $rightAddon
	 * @param E_SELECTED $selected
	 * @param E_VISIBLE $visible
	 * @param E_ENABLED $enabled
	 * 
	 * @param array $styles
	 * @param array $classes
	 * @param array $attributes
	 * 
	 * @return HTML_InputGroup
	 */
	public function __construct($id, $name, $inputType, $placeholder="", $leftAddon="", $rightAddon="", $inputLabelBefore="", $inputLabelAfter="", $selected=true, $visible=true, $enabled=true, $styles=array(), $classes=array(), $attributes=array() )
	{
		parent::__construct($id, $name, $visible, $enabled, $styles, $classes, $attributes);

		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		self::setAddonLeft($leftAddon);
		self::setAddonRight($rightAddon);
		self::setInputLabelAfter($inputLabelAfter);
		self::setInputLabelBefore($inputLabelBefore);
		self::setPlaceholder($placeholder);
		self::setSelected($selected);
		self::setType($inputType);
		
		return $this;
	}
	
	/**
	 * Generates the output of HTML_InputGroup
	 * @return $string
	 */
	public function generateHTML()
	{
		$this->defaultClasses["group"] 		= array("input-group pull-right");

		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		// ..:: Addons
		$addonL = "";
		if ($this->addonLeft != ""){
			$addonL = '<span class="input-group-addon">'.$this->addonLeft.'</span>';
		}
		$addonR = "";
		if ($this->addonRight != ""){
			$addonR = '<span class="input-group-addon">'.$this->addonRight.'</span>';
		}
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		// ..:: Buttons
		$buttonL = "";
		if ($this->buttonLeft != ""){
			$buttonL = '<span class="input-group-btn">'.$this->buttonLeft.'</span>';
		}
		$buttonR = "";
		if ($this->buttonRight != ""){
			$buttonR = '<span class="input-group-btn">'.$this->buttonRight.'</span>';
		}
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		if ($this->input == null)
		{
			if ($this->inputType == E_INPUTTYPE::BUTTON){
				$this->input = new HTML_Button($this->id, $this->name, $this->placeholder);
			}
			elseif ($this->inputType == E_INPUTTYPE::SELECT){
				$options = array();
				$this->input = new HTML_Select($this->id, $this->name, $options);
			}
			elseif ($this->inputType == E_INPUTTYPE::TEXT_AREA){
				$this->input = new HTML_TextArea($this->id, $this->name, "", $this->placeholder, $this->visible, $this->enabled, $this->styles, $this->classes, $this->attributes);
			}
			else {
				// default input
				$this->input = new HTML_Input($this->id, $this->name, $this->inputType, $this->placeholder, "", $this->inputLabelBefore, $this->inputLabelAfter, $this->selected, $this->visible, $this->enabled);
			}
		}
	
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		if ($addonL == "" && $addonR == "" && $buttonL == "" && $buttonR == ""){
			$com = $this->input->generateHTML();
		}
		else 
		{
			$com = '
			<div id="'.$this->id.'-group" name="'.$this->name.'-group" '.
				$this->getAttributesString().' '.
				$this->getClassString('group', true).' '.
				$this->getStyleString().'
				>'.
				$addonL.$buttonL.
				$this->input->generateHTML().
				$buttonR.$addonR.'
			</div>
			';
		}
		
		// write2Debugfile(self::DEBUG_FILENAME, "\nHTML_InputGroup generated\n".$com);
		return $com;
	}
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// Setter functions for HTML_InputGroup
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	/**
	 * @param string $string
	 * @return HTML_InputGroup
	 */
	public function setAddonLeft($string){
		$this->addonLeft = $string; 
		return $this;
	}
	
	/**
	 * @param string $string
	 * @return HTML_InputGroup
	 */
	public function setAddonRight($string){
		$this->addonRight = $string; 
		return $this;
	}
	
	/**
	 * @param string $string
	 * @return HTML_InputGroup
	 */
	public function setButtonLeft($string){
		$this->buttonLeft = $string;
		return $this;
	}
	/**
	 * @param string $string
	 * @return HTML_InputGroup
	 */
	public function setButtonRight($string){
		$this->buttonRight = $string;
		return $this;
	}
	
	/**
	 * @param HTML_Input $input
	 * @return HTML_InputGroup
	 */
	public function setInput($input){
		$this->input = $input; 
		return $this;
	}
	
	/**
	 * 
	 * @param string $string
	 * @return HTML_InputGroup
	 */
	public function setInputLabelAfter($string){
		$this->inputLabelAfter = $string; 
		return $this;
	}
	
	/**
	 * @param string $string
	 * @return HTML_InputGroup
	 */
	public function setInputLabelBefore($string){
		$this->inputLabelBefore = $string; 
		return $this;
	}
	
	/**
	 * @param string $string
	 * @return HTML_InputGroup
	 */
	public function setPlaceholder($string){
		$this->placeholder = $string; return $this;
	}
	
	/**
	 * @param E_SELECTED $bool
	 * @return HTML_InputGroup
	 */
	public function setSelected($bool){
		if (! E_SELECTED::isValidValue($bool) ){
			$bool	= E_SELECTED::NO;
		}
		$this->selected = $bool; 
		return $this;
	}
	
	/**
	 * @param E_INPUTTYPE $type
	 * @return HTML_InputGroup
	 */
	public function setType($type)
	{
		if (! E_INPUTTYPE::isValidValue($type) ){
			$type = E_INPUTTYPE::TEXT;
		}
		$this->inputType = $type;
		return $this;
	}
	
} // END OF >> HTML_InputGroup

// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
/**
 * @author _BA5E
 * @category helper
 * @package application\helpers\HTML_helper
 * @since 0.2
 * @version 1.1
 */
class HTML_Label extends HTML
{
	/**
	 * @var string
	 */
	public $text;
	
	/**
	 * @var E_COLOR
	 */
	public $color;
	
	/**
	 * @var E_SIZES
	 */
	public $size;

	/**
	 * simple label
	 *
	 * @param string $id		>> id
	 * @param string $text		>> label text
	 * @param string $color		>> use <code>E_COLOR</code>
	 * @param int $size			>> heading size (1-6)
	 * @param array $attributes	>> array for additional attributes
	 *
	 * @return HTML_Label
	 */
	public function __construct($id, $text, $color="default", $size="md", $visible=true, $styles=array(), $classes=array(), $attributes=array() )
	{
		parent::__construct($id, $id, $visible, E_ENABLED::YES, $styles, $classes, $attributes);

		self::setText($text);
		self::setColor($color);
		self::setSize($size);
		
		return $this;
	}

	/**
	 * Generates the output of HTML_Label
	 * @return $string
	 */
	public function generateHTML()
	{
		$this->defaultClasses["label"] = array("label", "label-$this->color", "label-".$this->size);

		$com = '<span id="'.$this->id.'" '.$this->getAttributesString().' '.$this->getClassString('label').' '.$this->getStyleString().'>'.$this->text.'</span>';
		
		// write2Debugfile(self::DEBUG_FILENAME, "\nHTML_Label generated\n".$com);
		return $com;
	}
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// Setter functions for HTML_Label
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	/**
	 * setter for <code>$this->color</code>
	 *
	 * @param E_COLOR $color
	 * @return $this
	 */
	 public function setColor($color){
		if (! E_COLOR::isValidValue($color) ){
			$color = E_COLOR::STANDARD;
		}
		$this->color = $color; 
		return $this;
	}
	
	/**
	 * setter for <code>$this->size</code>
	 *
	 * @param E_SIZES $size
	 * @return $this
	 */
	 public function setSize($size){
		if (! E_SIZES::isValidValue($size) ){
			$size = E_SIZES::STANDARD;
		}
		$this->size = $size; 
		return $this;
	}
	
	/**
	 * setter for <code>$this->text</code>
	 *
	 * @param string $text
	 * @return $this
	 */
	 public function setText($text){
		$this->text = $text; 
		return $this;
	}
	
	
} // END OF >> HTML_Label

/**
 * HTML_Media Default media
 * The default media displays a media object (images, video, audio) to the left or right of a content block.

 * @author _BA5E
 * @category helper
 * @package application\helpers\HTML_helper
 * @since 1.2
 * @version 1.0
 */
class HTML_Media extends HTML
{
	/** @var array */
	public $nested_list = array();
	
	/** @var E_COLOR */
	public $color;
	
	/** @var string */
	public $content_left 	= "";
	
	/** @var string */
	public $content_right	= "";

	/**
	 * HTML_Media Default media
	 * The default media displays a media object (images, video, audio) to the left or right of a content block.
	 * 
	 * @param string $id
	 * @param string $left_content
	 * @param string $right_content 
	 * @param string $color
	 * @param string $enabled
	 * @param string $visible
	 * @param array $styles
	 * @param array $classes
	 * @param array $attributes
	 * @return HTML_Media
	 */
	public function __construct($id, $left_content, $right_content, $color="default", $visible=true, $styles=array(), $classes=array(), $attributes=array() )
	{
		parent::__construct($id, $id, $visible, E_ENABLED::YES, $styles, $classes, $attributes);
	
		self::setColor($color);
		self::setLeftContent($left_content);
		self::setRightContent($right_content);
		
		
		return $this;
	}
	
	
	/**
	 * Generates the output of HTML_Label
	 * @return $string
	 */
	public function generateHTML($revertDirections=false)
	{
		$this->defaultClasses["media"] = array("media", "media-$this->color");
	
		if($this->content_left == ""){
			$this->content_left = '<a href="#"><img class="media-object" src="'.HTML_Image::generatePlaceholderSVG(80, 80).'" alt="placeholder image"></a>';
		}
		if ($this->content_right == ""){
			$this->content_right = '<h4 class="media-heading">Media heading</h4>'.E_LOREM::IPSUM_STRING;
		}
		
		$com = '<div id="'.$this->id.'" '.$this->getAttributesString().' '.$this->getClassString('media').' '.$this->getStyleString().'>';
		if ($revertDirections == false){
			$com .= '<div class="media-left">'.$this->content_left.'</div><div class="media-body">'.$this->content_right.'</div>';
		}else{
			$com .= '<div class="media-body">'.$this->content_right.'</div><div class="media-right">'.$this->content_left.'</div>';
		}
		$com .= '</div>';
		
		
		// write2Debugfile(self::DEBUG_FILENAME, "\nHTML_Label generated\n".$com);
		return $com;
	}
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// Setter functions for HTML_Label
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	/**
	 * setter for <code>$this->color</code>
	 *
	 * @param E_COLOR $color
	 * @return $this
	 */
	public function setColor($color){
		if (! E_COLOR::isValidValue($color) ){
			$color = E_COLOR::STANDARD;
		}
		$this->color = $color;
		return $this;
	}
	
	/**
	 * setter for <code>$this->content_left</code>
	 *
	 * @param string $content
	 * @return $this
	 */
	public function setLeftContent($content){
		$this->content_left = $content;
		return $this;
	}
	
	/**
	 * setter for <code>$this->content_right</code>
	 *
	 * @param string $content
	 * @return $this
	 */
	public function setRightContent($content){
		$this->content_right = $content;
		return $this;
	}

}// END OF HTML_Media

/**
 * This is just an Alias for HTML_Dialog
 *
 * @author _BA5E
 * @category helper
 * @package application\helpers\HTML_helper
 * @since 1.0
 * @version 1.0
 */
class HTML_Modal extends HTML_Dialog 
{
	
}

// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
/**
 * @author _BA5E
 * @category helper
 * @package application\helpers\HTML_helper
 * @since 0.6
 * @version 1.6
 */
class HTML_Panel extends HTML
{
	/**
	 * @var string
	 */
	protected $dismissButton 	= '';
	
	/**
	 * @var string
	 */
	protected $collapseButton 	= '';
	
	/**
	 * @deprecated
	 * @var E_PANEL_LAYOUT
	 */
	protected $layout			= ""; 
	
	/**
	 * @var string
	 */
	public $panelTitle 			= "";
	
	/**
	 * @var array
	 */
	public $titleControls = array();
	
	/**
	 * @var string
	 */
	public $content;
	
	/**
	 * @var string
	 */
	public $footer;
	
	/**
	 * @var E_COLOR
	 */
	public $titleColor;
	
	/**
	 * @var E_COLOR
	 */
	public $footerColor;
	
	/**
	 * @var bool
	 */
	public $headerWithoutH3 = false;
	
	/**
	 * Panel component (supports draggable)
	 * 
	 * @todo in addition to setCollapseable add "setIsCollapsed" 
	 * 
	 *
	 * @param string $id			>> id
	 * @param string $name			>> name
	 * @param string $title			>> alert title
	 * @param string $content		>> alert text
	 * @param string $footer		>> if not empty, a footer will be generated
	 * @param bool $dismissable		>> set to <code>true</code> to make the panel dismissable
	 * @param E_VISIBLE $visible	>> use <code>E_VISIBLE</code>
	 * @param E_COLOR $titleColor	>> use <code>E_COLOR</code>
	 * @param E_COLOR $footerColor	>> use <code>E_COLOR</code>
	 * @param array	$styles			>> array for additional styles 
	 * @param array $classes		>> array for additional classes 
	 * @param array $attributes		>> array for additional attributes
	 *
	 * @return HTML_Panel
	 */
	public function __construct($id, $title, $content="", $footer="", $dismissable=false, $visible=true, $titleColor="default", $footerColor="default", $styles=array(), $classes=array(), $attributes=array())
	{
		parent::__construct($id, $id, $visible, E_ENABLED::YES, $styles, $classes, $attributes);

		self::setPanelTitle($title);
		self::setTitleColor($titleColor);
		self::setContent($content);
		self::setFooter($footer);
		self::setFooterColor($footerColor);
		self::setDismissable($dismissable);
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		// note: the following items are not part of the constructor. use the setter-methods if you want to use them
		self::setCollapseable(E_COLLAPSEABLE::NO);
		self::setDraggable(E_DRAGGABLE::NO);
		
		return $this;
	}
	
	/**
	 * Generates the output of HTML_Panel
	 * @return $string
	 */
	public function generateHTML()
	{
		$this->defaultClasses['panel'] = array("panel", "panel-$this->titleColor");
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		$this->titleControls["dismiss"] 	= $this->dismissButton;
		$this->titleControls["collapse"] 	= $this->collapseButton;
		
		$titleControls = "";
		if (is_array($this->titleControls))
		{
			$titleControls = '<span id="'.$this->id.'-titleControl" class="panel-titel-controls btn-group">';
			foreach ($this->titleControls as $control)
			{
				$titleControls .= "&nbsp;".$control;
			}
			$titleControls .= '</span>';
			
			if ($titleControls != ""){
				$titleControls = $titleControls.'&nbsp;';
			}
		}
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		$draggable = ($this->draggable === true ? $this->cls_draggable : '') ;
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		$panelHeading_cls 	= "panel-heading";
		$panelTitle_cls 	= "panel-title";
		$panelBody_cls		= "panel-body";
				
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		$title 	= '<div class="'.$panelHeading_cls.$draggable.'"><label class="'.$panelTitle_cls.'">'. $this->collapseButton . $this->panelTitle . $this->dismissButton.'</label>'. $titleControls .'</div>';
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		$footer	= '';
		if ($this->footer != ""){
			$footer = '<div class="panel-footer panel-'.$this->footerColor.'">'.$this->footer.'</div>';
		}
		
		$com = '
		<div id="'.$this->id.'" name="'.$this->name.'" title="'.$this->title.'" '.
			$this->getClassString('panel', true).' '.
			$this->getAttributesString().' '.
			$this->getStyleString().
			'>'.
			$title.'
			<div id="'.$this->id.'-body" class="'.$panelBody_cls.' collapse in ">'.
				$this->content.'
			</div>
			<div class="clearfix"></div>'.
			$footer.'
		</div>';
		
		// write2Debugfile(self::DEBUG_FILENAME, "\nHTML_Panel generated\n".$com);
		return $com;
	}
	
	/**
	 * Button fAYr Header hinzufAYgen
	 *
	 * @param string $id
	 * @param string $name
	 * @param string $icon
	 * @param string $tooltip
	 * @param string $onclick
	 */
	public function addTitleControl($id, $name, $icon, $tooltip="", $onclick="")
	{
		$styles		= array();
		$classes 	= array("pull-right", "panel-control");
		$attributes = array();
	
		if ($onclick != ""){
			$onclick = 'onclick="'.$onclick.'"';
		}
	
		$button = '<button id="'.$id.'" title="'.$tooltip.'" type="button" class="panel-control" '.$onclick.'>'.$icon.'</button>';
	
		$this->titleControls[] = $button;
	}
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// Setter functions for HTML_Panel
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	/**
	 * @param string $string
	 * @return HTML_Panel
	 */
	public function setContent($string){
		$this->content = $string; 
		return $this;
	}
	
	/**
	 * @param string $string
	 * @return HTML_Panel
	 */
	public function setTitleControls($controls, $side="right"){
		$this->titleControls = $controls; 
		return $this;
	}
	
	/**
	 * @param string $string
	 * @return HTML_Panel
	 */
	public function setPanelTitle($string){
		$this->panelTitle = $string; return $this;
	}
	
	/**
	 * @param E_COLOR $titleColor
	 * @return HTML_Panel
	 */
	public function setTitleColor($titleColor)
	{
		if (! E_COLOR::isValidValue($titleColor) ){
			$titleColor	= E_COLOR::STANDARD;
		}
		$this->titleColor = $titleColor; 
		return $this;
	}
	
	/**
	 * @param string $string
	 * @return HTML_Panel
	 */
	public function setFooter($string){
		$this->footer = $string; return $this;
	}
	
	/**
	 * @param E_COLOR $footerColor
	 * @return HTML_Panel
	 */
	public function setFooterColor($footerColor)
	{
		if (! E_COLOR::isValidValue($footerColor) ){
			$footerColor	= E_COLOR::STANDARD;
		}
		$this->footerColor = $footerColor; return $this;
	}
	
	/**
	 * sets the dismissable string for the panel
	 *
	 * @param string $dismissable 	>> if <code>true</code>, an dismiss button will be added to the panel header
	 * @param string $closeIcon		>> specifiy the desired span for the close icon
	 * 
	 * @return HTML_Panel
	 */
	public function setDismissable($dismissable=true)
	{
		if ($dismissable === false){
			$this->dismissButton = "";
		}else{
			$this->dismissButton = '<button id="'.$this->id.'-close" title="Fenster schlieA>en" type="button" class="panel-control" data-dismiss="alert" data-target="#'.$this->id.'" >'.E_ICONS::PANEL_DISMISS.'</button>';
		}
		
		return $this;
	}
	
	/**
	 * sets the collapseable 
	 *
	 * @param string $collapseable 	>> if <code>true</code>, an button will be added to the panel header to collapse the panel content
	 * 
	 * @return HTML_Panel
	 */
	public function setCollapseable($collapseable=true)
	{
		if ($collapseable === false){
			$this->collapseButton = "";
		}else{
			$this->collapseButton = '
			<button id="'.$this->id.'-collapse" title="Ein/Ausklappen" type="button" aria-expanded="true" class="accordion-toggle panel-control" data-toggle="collapse" data-parent="'.$this->id.'" href="#'.$this->id.'-body" >
				<span class="sr-only">Ein/Ausklappen</span>
			</button>';
		}
		return $this;
	}
	
	/**
	 * Sets the collapse state
	 *
	 * @param bool $collapsed
	 * @return HTML_Panel
	 */
	public function setCollapsed($collapsed=true)
	{
		$this->collapsed = (boolval($collapsed));
		return $this;
	}	
	
} // END OF >> HTML_Panel

// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
/**
 * @author _BA5E
 * @category helper
 * @package application\helpers\HTML_helper
 * @since 0.6
 * @version 1.0
 * 
 * @todo create setter methods
 */
class HTML_ProgressBar extends HTML
{
	/**
	 * @var string
	 */
	protected $stripped = 'progress-bar-striped';
	
	/**
	 * @var string
	 */
	public $value;
	
	/**
	 * @var string
	 */
	public $text;
	
	/**
	 * @var E_COLOR
	 */
	public $color;
	
	/**
	 * @var int
	 */
	public $minVal;
	
	/**
	 * @var int
	 */
	public $maxVal;
	
	public function __construct($id, $name, $value, $text, $visible=true, $color="", $minVal=0, $maxVal=100, $stripped=false, $styles=array(), $classes=array(), $attributes=array())
	{
		parent::__construct($id, $name, $visible, E_ENABLED::YES, $styles, $classes, $attributes);
	
		if (! E_COLOR::isValidValue($color) ){
			$color	= E_COLOR::INFO;
		}
	
		$this->value		= $value;
		$this->text			= $text;
		$this->color		= $color;
		$this->minVal		= $minVal;
		$this->maxVal		= $maxVal;
	
		$this->setStripped($stripped);
		
		return $this;
	}
	
	/**
	 * Generates the output of HTML_ProgressBar
	 * @return $string
	 */
	public function generateHTML()
	{
		$this->defaultClasses["progressbar"] = array("progress-bar", "progress-bar-$this->color", $this->stripped);
		
		$percent = ($this->maxVal / 100) * $this->value;
		
		$com = '
		<div id="'.$this->id.'-container" name="'.$this->name.'-container" class="progress" '.
			$this->getAttributesString().' '.
			$this->getStyleString().'
			>
			<div id="'.$this->id.'" name="'.$this->name.'" role="progressbar" '. 
				$this->getClassString('progressbar').' 
				style="width:'.$percent.'%;" 
				aria-valuenow="'.$this->value.'" 
				aria-valuemin="'.$this->minVal.'" 
				aria-valuemax="'.$this->maxVal.'"
			>'.
			$this->text.'
			<span class="sr-only">'.$this->value.'</span></div>
		</div>';
		
		// write2Debugfile(self::DEBUG_FILENAME, "\nHTML_ProgressBar generated\n".$com);
		return $com;
	}
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// Setter functions for HTML_ProgressBar
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	/**
	 * sets the stripped string for the progress bar
	 *
	 * @param string $stripped 	>> if <code>true</code>, the striped-attribute will be added
	 * @return HTML_ProgressBar
	 */
	public function setStripped($stripped=true)
	{
		if ($stripped === false){
			$this->stripped = "";
		}else{
			$this->stripped = 'progress-bar-striped';
		}
		return $this;
	}
	
} // END OF HTML_ProgressBar 

// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
/**
 * HTML Radio buttons
 * 
 * @author _BA5E
 * @category helper
 * @package application\helpers\HTML_helper
 * @since 0.8
 * @version 1.2
 */
class HTML_Radio extends HTML
{
	/**
	 * @var string
	 */
	public $text;
	
	/**
	 * @var string
	 */
	public $value;
	
	/**
	 * @var E_CHECKED
	 */
	public $checked;
	
	/**
	 * @var E_INLINE
	 */
	public $inline;
	/**
	 *  HTML_Radio 
	 *  A input of type radio 
	 *
	 * @param string $id			>> id
	 * @param string $name			>> name
	 * @param string $text			>> text
	 * @param E_CHECKED $checked	>> checked attribute
	 * @param string $value			>> value attribute
	 * @param E_ENABLED $enabled	>> set to <code>false</code> to disable the radio
	 * @param E_INLINE $inline		>> align radios in one line
	 * @param E_VISIBLE $visible	>> hide or show the radio
	 * @param array $styles			>>
	 * @param array $classes		>>
	 * @param array $attributes		>>
	 *
	 * @return HTML_Radio
	 */
	public function __construct($id, $name, $text, $checked, $value=0, $enabled=true, $inline=true, $visible=true, $styles=array(), $classes=array(), $attributes=array())
	{
		parent::__construct($id, $name, $visible, $enabled, $styles, $classes, $attributes);
	
		self::setChecked($checked);
		self::setInline($inline);
		self::setText($text);
		self::setValue($value);
		
		
	}
	
	/**
	 * Generates the output of HTML_Radio
	 * @return $string
	 */
	public function generateHTML()
	{
		$this->defaultClasses["radio"] = array();
		
		$disabled = "";
		if (!$this->enabled){
			$disabled = " disabled";
		}
		
		$checked= "";
		if ($this->checked == E_SELECTED::YES){
			$checked = "checked";
		}
		
		$inline = "";
		if ($this->inline === true){
			$inline = ' radio-inline';
		}
		
		$com = '
		<div class="radio'.$inline.$disabled.'">
			<label id="'.$this->id.'_lbl" for="'.$this->id.'" class="'.$inline.$disabled.'">
				<input id="'.$this->id.'" name="'.$this->name.'" value="'.$this->value.'" '.$checked.'  '.$disabled.' type="radio" '.
					$this->getClassString('radio', true).' '.
					$this->getAttributesString().' '.
					$this->getStyleString().'
				/>
				<span>'.$this->text.'</span>
			</label>
		</div>
		';
		// write2Debugfile(self::DEBUG_FILENAME, "\nHTML_Radio generated\n".$com);
		return $com;
	}
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// Setter functions for HTML_Radio
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	/**
	 * setter for <code>$this->checked</code>
	 *
	 * @param E_SELECTED $checked
	 * @return $this
	 */
	public function setChecked($checked)
	{
		if (! E_SELECTED::isValidValue($checked)){
			$checked = E_SELECTED::NO;
			throw new Exception("invalid");
		}
		$this->checked = $checked;
		return $this;
	}
	
	/**
	 * setter for <code>$this->inline</code>
	 *
	 * @param E_INLINE $inline
	 * @return $this
	 */
	public function setInline($inline)
	{
		if (! E_INLINE::isValidValue($inline)){
			$inline = E_INLINE::YES;
		}
		$this->inline = $inline;
		return $this;
	}
	
	/**
	 * setter for <code>$this->text</code>
	 *
	 * @param string $text
	 * @return $this
	 */
	public function setText($text)
	{
		$this->text = $text;
		return $this;
	}
	
	/**
	 * setter for <code>$this->value</code> 
	 *
	 * @param string $value
	 * @return $this
	 */
	public function setValue($value)
	{
		$this->value = $value;
		return $this;
	}
	
} // END OF >> HTML_Radio

// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
/**
 * HTML Select 
 * 
 * @author _BA5E
 * @category helper
 * @package application\helpers\HTML_helper
 * @since 0.4
 * @version 1.0
 */
class HTML_Select extends HTML
{
	/**
	 * @var string
	 */
	protected $disabled = "";
	
	/**
	 * @var string
	 */
	public $selectedValue;
	
	/**
	 * options HTML | @see buildOptions() -HelperMethod 
	 * @var string
	 */
	public $options;
	
	/**
	 * @var bool
	 */
	public $multiple;
	
	/**
	 * @var string
	 */
	public $placeholder;
	
	/**
	 * @var bool
	 */
	public $allow_clear = true;

	/**
	 * Select/Dropdown component
	 *
	 * @param string $id			>> id
	 * @param string $name			>> name
	 * @param string $options		>> options HTML | @see buildOptions() -HelperMethod 
	 * @param E_VISIBLE $visible	>>
	 * @param E_ENABLED $enabled	>>
	 * @param array	$styles			>> array for additional styles
	 * @param array $classes		>> array for additional classes
	 * @param array $attributes		>> array for additional attributes
	 *
	 * @return HTML_Select
	 */
	public function __construct($id, $name, $options, $multiple=false, $placeholder="", $visible=true, $enabled=true, $styles=array(), $classes=array(), $attributes=array())
	{
		parent::__construct($id, $name, $visible, $enabled, $styles, $classes, $attributes);

		self::setOptions($options);
		self::setMultiple($multiple);
		self::setPlaceholder($placeholder);

		return $this;
	}
	
	/**
	 * Generates the output of HTML_Select
	 * @return $string
	 */
	public function generateHTML()
	{
		$this->defaultClasses["select"] = array("form-control");
		
		
		$multiple = "";
		if ($this->multiple === true){
			$multiple = "multiple";
			$this->allow_clear = false;		// not allowed
		}
		
		
 
		$com = '
		<select id="'.$this->id.'" name="'.$this->name.'" role="select"  '.$multiple.' data-placeholder="'.$this->placeholder.'" data-allow-clear="'.$this->allow_clear.'" '.$this->disabled.
			$this->getAttributesString().' '.
			$this->getStyleString().' '.
			$this->getClassString("select", true).' 
			>
			'.$this->options.'
		</select>';

		// write2Debugfile(self::DEBUG_FILENAME, "\nHTML_ProgressBar generated\n".$com);
		return $com;
	}
	
	/**
     * create select options by an array
     * 
     * @param array $data					>> all data
     * @param string $keyField				>> key
     * @param string $labelField			>> the label field
     * @param string $addAllEntry			>> add extra option
     * @param string $allText				>> text for the extra option
     * @param string $returnOptionString	>> return as string
     * 
     * @return mixed >> array|string
     */
    static function buildOptions($data, $keyField, $labelField, $selectedItem, $allText="Alle", $addAllEntry=true, $returnOptionString=true,$translateString=false)
	{
		$options 		= array();
		$optionString	= "";
		
		if($addAllEntry == true)
		{
			$options["ALL"] = array("label"=>$allText);
			$optionString 	.= '<option id="ALL" value="" title="'.$allText.'">'.$allText.'</option>'; 
		}
		
		if (is_array($data))
		{
			foreach ($data as $entry)
			{

				$opt_data = (array)$entry;
				
				if (! array_key_exists($keyField, $entry)){
					throw new Exception(nl2br("Der Index [$keyField] für den Schlüsselwert existiert nicht im Datenbestand!\n".print_r($entry, true)));
				}
				if (! array_key_exists($labelField, $entry)){
					throw new Exception(nl2br("Der Index [$labelField] für die Beschriftung existiert nicht im Datenbestand!\n".print_r($entry, true)));
				}
				if (is_array($entry) ){
					$key				= $entry[$keyField];
					$opt_data["label"]	= $entry[$labelField];
					$text 				= $entry[$labelField];
				}
				else{
					$key 				= $entry->$keyField;
					$opt_data["label"]	= $entry->$labelField;
					$text 				= $entry->$labelField;
				}
				
				$options[$key]		= $opt_data;

				$selected = "";
				if(is_array($selectedItem))
				{
				    foreach($selectedItem as $selectedItemKey=>$selectedItemValue)
				    {
				         if ($selectedItemValue == $key)
				        {
					$selected = "selected";
				        }
				    }
				}
				else
				{
				    if ($selectedItem == $key)
				    {
					$selected = "selected";
				    }
				}
				
                
                if($translateString == true)
                {
                    if(lang($text) != '')
                    {
                        $text = lang($text);
                    }
                }

                $optionString .= '<option id="'.$key.'" value="'.$key.'" '.$selected.' title="'.strip_tags($text).'">'.$text.'</option>';

			}
		}
		
		if ($returnOptionString){
			return $optionString;
		}
		return $options;
	}

	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// Setter functions for HTML_Select
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	/**
	 * Setter for the 'allow_clear' property 
	 * @param bool $bool
	 * @return HTML_Select
	 */
	public function setAllowClear($bool)
	{
		$this->allow_clear = (bool)$bool;
		return $this;
	}
	
	/**
	 * Setter for the enabled state because of additional customizations
	 *
	 * @param bool $enabled
	 * @return HTML_Select
	 */
	public function setEnabled($enabled)
	{
		if (! E_ENABLED::isValidValue($enabled) ){
			$enabled = E_ENABLED::YES;
		}
	
		parent::setEnabled($enabled);	// the parent setter stores just the bool state
	
		if ($enabled === true){
			$this->disabled = "";
		}else{
			$this->disabled = 'disabled="true"';
			$this->addClass("select2-disabled");
		}
	}
	
	/**
	 * setter for the dropdown options <code>$this->options</code>
	 *
	 * @param string $options 
	 * @return HTML_Select
	 */
	public function setOptions($options)
	{
		$this->options = $options;
		return $this;
	}
	
	/**
	 * setter for <code>$this->placeholder</code>
	 *
	 * @param string $placeholder 
	 * @return HTML_Select
	 */
	public function setPlaceholder($placeholder)
	{
		$this->placeholder = $placeholder;
		return $this;
	}
	
	/**
	 * setter for <code>$this->multiple</code>
	 *
	 * @param bool $multiple
	 * @return HTML_Select
	 */
	public function setMultiple($multiple)
	{
		$this->multiple = $multiple;
		return $this;
	}
	
} // END OF HTML_Select

/**
 * Alias class for HTML_Datatable
 *
 * @author _BA5E
 * @category helper
 * @package application\helpers\HTML_helper
 * @since 1.2
 * @version 1.2
 */
class HTML_Table extends HTML_Datatable{
	
}

// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
/**
 * HTML Tab-Item 
 * 
 * @author _BA5E
 * @category helper
 * @package application\helpers\HTML_helper
 * @since 1.1
 * @version 0.2
 */
class HTML_TabItem extends HTML
{
	/**
	 * @var string
	 */
	public $text;

	/**
	 * TabItem
	 *
	 * @param string $id			>> id
	 * @param string $name			>> name
	 * @param string $text			>> text
	 * @param E_VISIBLE $visible	>>
	 * @param E_ENABLED $enabled	>>
	 *
	 * @param array	$styles			>> array for additional styles
	 * @param array $classes		>> array for additional classes
	 * @param array $attributes		>> array for additional attributes
	 *
	 * @return HTML_TabItem
	 */
	public function __construct($id, $name, $text, $visible=true, $enabled=true, $styles=array(), $classes=array(), $attributes=array())
	{
		parent::__construct($id, $name, $visible, $enabled, $styles, $classes, $attributes);

		throw new Exception("sorry but this component is not ready yet");
		
		self::setText($text);

		return $this;
	}

	/**
	 * Generates the output of HTML_TabItem
	 * @return $string
	 */
	public function generateHTML()
	{
		$this->defaultClasses["tabItem"] = array("form-control");

		$com = '
		<ul class="nav nav-tabs" id="'.$this->id.'" name="'.$this->name.'" '.
			$this->getAttributesString().' '.
			$this->getStyleString().' '.
			$this->getClassString("tabItem").'
			>
			<li class="active"><a href="#profile" data-toggle="tab">Profile</a></li>
			<li><a href="#messages" data-toggle="tab">Messages</a></li>
			<li><a href="#settings" data-toggle="tab">Settings</a></li>
		</ul>
		'.$this->text.'';

		// write2Debugfile(self::DEBUG_FILENAME, "\nHTML_TextArea generated\n".$com);
		return $com;
	}


	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// Setter functions for HTML_TabItem
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	/**
	 * setter for <code>$this->text</code>
	 *
	 * @param string $text
	 * @return HTML_TextArea
	 */
	public function setText($text)
	{
		$this->text = $text;
		return $this;
	}
} // END OF HTML_TabItem



// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
/**
 * HTML Text-Area
 * 
 * @author _BA5E
 * @category helper
 * @package application\helpers\HTML_helper
 * @since 0.4
 * @version 1.1
 */
class HTML_TextArea extends HTML
{
	/**
	 * @var string
	 */
	public $text;

	/**
	 * @var string
	 */
	public $placeholder;

	/**
	 * Textarea component
	 *
	 * @param string $id			>> id
	 * @param string $name			>> name
	 * @param string $text			>> text 
	 * @param string $placeholder	>> 
	 * @param E_VISIBLE $visible	>>
	 * @param E_ENABLED $enabled	>>
	 * 
	 * @param array	$styles			>> array for additional styles
	 * @param array $classes		>> array for additional classes
	 * @param array $attributes		>> array for additional attributes
	 *
	 * @return HTML_TextArea
	 */
	public function __construct($id, $name, $text, $placeholder="", $visible=true, $enabled=true, $styles=array(), $classes=array(), $attributes=array())
	{
		parent::__construct($id, $name, $visible, $enabled, $styles, $classes, $attributes);
		
		self::setPlaceholder($placeholder);
		$this->text = $text;
		return $this;
	}
	
	/**
	 * Generates the output of HTML_TextArea
	 * @return $string
	 */
	public function generateHTML()
	{
		$this->defaultClasses["textarea"] = array("form-control", ($this->enabled ? '':'disabled'));
		
		$disabled = "";
		if ($this->enabled === E_ENABLED::NO){
			$disabled = " disabled";
		}
		
		$com = '
		<textarea id="'.$this->id.'" name="'.$this->name.'" placeholder="'.$this->placeholder.'" '.$disabled.' '.
			$this->getAttributesString().' '.
			$this->getStyleString().' '.
			$this->getClassString("textarea", true).' 
			>'.$this->text.'</textarea>';

		// write2Debugfile(self::DEBUG_FILENAME, "\nHTML_TextArea generated\n".$com);
		return $com;
	}
	
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// Setter functions for HTML_TextArea
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	/**
	 * setter for <code>$this->placeholder</code>
	 *
	 * @param string $placeholder 
	 * @return HTML_TextArea
	 */
	public function setText($text)
	{
		$this->text = $text;
		return $this;
	}
	
	/**
	 * setter for <code>$this->placeholder</code>
	 *
	 * @param string $placeholder 
	 * @return HTML_TextArea
	 */
	public function setPlaceholder($placeholder)
	{
		$this->placeholder = $placeholder;
		return $this;
	}
	
} // END OF HTML_TextArea

// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
/**
 * Bootstrap Thumbnail
 * @see http://getbootstrap.com/components/#thumbnails
 * @author _BA5E
 * @category helper
 * @package application\helpers\HTML_helper
 * @since 1.0
 * @version 1.2
 */
class HTML_Thumbnail extends HTML
{
	/**
	 * @var string
	 */
	public $image;

	/**
	 * @var string
	 */
	public $caption = "";

	/**
	 * @var string
	 */
	public $caption_right = "";

	/**
	 * @var string
	 */
	public $content = "";

	/**
	 * Thumbnail
	 *
	 * @param string $id			>> id
	 * @param string $name			>> name
	 * @param string $img			>> the image
	 * @param string $caption		>> Beschriftung (Titel)
	 * @param E_VISIBLE $visible	>>
	 * @param E_ENABLED $enabled	>>
	 * @param array	$styles			>> array for additional styles
	 * @param array $classes		>> array for additional classes
	 * @param array $attributes		>> array for additional attributes
	 *
	 * @return HTML_Thumbnail
	 */
	public function __construct($id, $img, $caption="", $text="", $visible=true, $enabled=true, $styles=array(), $classes=array(), $attributes=array())
	{
		parent::__construct($id, $id, $visible, $enabled, $styles, $classes, $attributes);

		self::setCaption($caption);
		self::setCaptionRight("");
		self::setContent($text);
		self::setImage($img);

		return $this;
	}
	
	/**
	 * directly returns a thumnail component with base64 image source 
	 * 
	 * @param string $image_path
	 * @param string $caption
	 * @param string $text
	 * @param string $id	>> optional identifier 
	 */
	public static function fromImageFile($image_path, $caption="", $text="", $id=null)
	{
		$id 	= (!$id ? random_string():$id);
		$img 	= HTML_Image::generateDataURIFromImage($image_path);
		
		$thumb 	= new HTML_Thumbnail($id, $img, $caption, $text);
		
		return $thumb->generateHTML();
	}

	/**
	 * Generates the output of HTML_Thumbnail
	 * @return $string
	 */
	public function generateHTML()
	{
		$this->defaultClasses["thumbnail"] = array("thumbnail");

		$caption_right = "";
		if ($this->caption_right != ""){
			$caption_right = '<h3 class="pull-right text-line-1">'.$this->caption_right.'</h3>';
		}

		$com = '
		<div  id="'.$this->id.'" name="'.$this->name.'" title="'.$this->title.'" '.
		$this->getAttributesString().' '.
		$this->getStyleString().' '.
		$this->getClassString("thumbnail", true).'><br>'.
			
		$this->image.'
			<div id="'.$this->id.'-caption" class="caption">'.
			$caption_right.'
				<h3 class="text-line-1">'.$this->caption.'</h3>'.
				$this->content.'
			</div>
		</div>
		';

		// write2Debugfile(self::DEBUG_FILENAME, "\nHTML_TextArea generated\n".$com);
		return $com;
	}


	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// Setter functions for HTML_Thumbnail
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	/**
	 * setter for <code>$this->caption</code>
	 *
	 * @param string $text
	 * @return HTML_Thumbnail
	 */
	public function setCaption($text)
	{
		$this->caption = $text;
		return $this;
	}

	/**
	 * setter for <code>$this->caption_right</code>
	 *
	 * @param string $string
	 * @return $this
	 */
	public function setCaptionRight($string){
		$this->caption_right = $string;
		return $this;
	}

	/**
	 * setter for <code>$this->content</code>
	 *
	 * @param string $content
	 * @return HTML_Thumbnail
	 */
	public function setContent($content)
	{
		$this->content = $content;
		return $this;
	}

	/**
	 * setter for <code>$this->image</code>
	 *
	 * @param string $image
	 * @return HTML_Thumbnail
	 */
	public function setImage($image)
	{
		$this->image = $image;
		return $this;
	}
} // END OF HTML_Thumbnail
// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
/**
 * create a tree from array or object
 * requires E_PLUGINS_CSS::BS_TREE to be loaded
 * 
 * @author _BA5E
 * @category helper
 * @package application\helpers\HTML_helper
 * @since 1.2
 * @version 1.0
 */
class HTML_Tree extends HTML
{
	/**
	 * @var string
	 */
	public $indention_str = "&nbsp;&nbsp;&nbsp;&nbsp;";
	
	/**
	 * @var array|object
	 */
	public $data = array();
	
	/**
	 * HTML_Tree constructor
	 * 
	 * @param string $id
	 * @param array|object $data
	 * @param string $indent
	 * @param string $enabled
	 * @param string $visible
	 * @param array $styles
	 * @param array $classes
	 * @param array $attributes
	 */
	public function __construct($id, $data, $indent="", $visible=true, $enabled=true, $styles=array(), $classes=array(), $attributes=array())
	{
		parent::__construct($id, $id, $visible, $enabled, $styles, $classes, $attributes);

		if ($indent != ""){
			self::setIndent($indent);
		}
		self::setData($data);
	}
	
	public function generate_html()
	{
		$this->defaultClasses["tree"] = array("tree", "well");
		
		$com = '<div id="'.$this->id.'" '.$this->getClassString('tree', true).' '.$this->getAttributesString().' '.$this->getStyleString().'>';
		if (is_array($this->data) || is_object($this->data))
		{
			$com .= '<ul>';
				$com .= self::print_composite($this->data, 1, false);
			$com .= "</ul>";
		}
		$com .= "</div>";
		
		return $com;
	}
	
	public static function quick_table($data)
	{
		$tree = new HTML_Tree("tree".time(), $data);
		return $tree->generate_html();
	}
	
	private function print_composite($composite, $nestlevel=1, $as_text=false)
	{
		if (is_array($composite) || is_object($composite))
		{
			$out 	= "";
			$txt	= "";
			$indent = str_repeat($this->indention_str, (int)$nestlevel);
			if ($nestlevel === 1){
				$txt .= HTML_FormItem::buildLegendItem("<h4>::parse_array => array_keys[".count(array_keys((array)$composite))."]</h4>"); //.nl2br(print_r($composite, true))
			}else{
				$txt .= " >> recursion[<b>$nestlevel</b>]";
			}
	
			$current_count = 0;
			foreach ($composite as $array_key => $value)
			{
				if (is_array($value))
				{
					$txt .= "\n".$indent."[$array_key][array].[length(".count($value).")]";
					$out .= '<li><span><i class="icon-folder-open"></i><b>[array]['.$array_key.']</b></span> '.count($value).' '.lang("elements").'';
					$out .= "<ul>".self::print_composite($value, (1+$nestlevel), $as_text)."</ul>";
	
				}
				else if (is_object($value))
				{
					$txt .= "\n".$indent."[$array_key].[object].[length(".count($value).")]";
					$out .= '<li><span><i class="icon-folder-open"></i><b>[object]['.$array_key.']</b></span> '.count($value).' '.lang("elements").'';
					$out .= "<ul>".self::print_composite($value, $out, (1+$nestlevel), $as_text)."</ul>";
				}
				else
				{
					$txt .= "\n".$indent."[scalar].[$array_key] => ".$value."";
					$out .= ' <li><span><i class="icon-leaf"></i><b>'.$array_key.'</b></span> '.$value.'</li>';
				}
				$current_count++;
			}
			
			if ($as_text){
				return $txt;
			}
			return $out;
		}
	}
	
	
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// Setter functions for HTML_Tree
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	/**
	 * setter for <code>$this->data</code>
	 *
	 * @param array|object $data
	 * @return HTML_Tree
	 */
	public function setData($data)
	{
		$this->data = $data;
		return $this;
	}
	
	/**
	 * setter for <code>$this->indention_str</code>
	 *
	 * @param string $indent
	 * @return HTML_Tree
	 */
	public function setIndent($indent)
	{
		$this->indention_str = (string)$indent;
		return $this;
	}
} // END of HTML_Tree


/**
 * !!! Attention: This component is not a part of bootstrap.css and requires its custom CSS to work !!!
 * In this project the stylesheet for this component is located in the customs.css
 * @see resources/css/generic/customs.css
 *
 * @todo think about seperating NON-Bootstrap components
 *
 * @author _BA5E
 * @category helper
 * @package application\helpers\HTML_helper
 * @since 0.4
 * @version 1.2
 */
class HTML_TileBox extends HTML
{
	/**
	 * @var E_BG_COLOR
	 */
	public $bgColor;
	
	/**
	 * @var string
	 */
	public $footer;
	
	/**
	 * @var string
	 */
	public $heading;
	
	/**
	 * @var string
	 */
	public $heading_right;
	
	/**
	 * @var E_ICONS
	 */
	public $icon;
	
	/**
	 * To make the hole thing clickable
	 * @var string
	 */
	public $link;
	
	/**
	 * @var string
	 */
	public $text;
	

	/**
	 * creates a Tile Box
	 *
	 * @param string $id			>> id
	 * @param string $heading		>> the big fat heading
	 * @param string $text			>> alert text
	 * @param E_COLOR $bgcolor		>> valid <code>E_COLOR</code>
	 * @param E_VISIBLE $visible	>> set to <code>false</code> to make the alert hidden
	 * @param array $styles			>>
	 * @param array $classes		>>
	 * @param array $attributes		>>
	 *
	 * @return HTML_TileBox
	 */
	public function __construct($id, $heading, $text, $bgColor, $icon="", $footer='', $visible=true, $styles=array(), $classes=array(), $attributes=array())
	{
		parent::__construct($id, $id, $visible, E_ENABLED::YES, $styles, $classes, $attributes);

		self::setText($text);
		self::setHeading($heading);
		self::setBackgoundColor($bgColor);
		self::setIcon($icon);
		self::setFooter($footer);
	}

	/**
	 * Generates the output of HTML_TileBox
	 * @return $string
	 */
	public function generateHTML()
	{
		$this->defaultClasses["tilebox"] = array("tile-box", "$this->bgColor");

		$heading = "<h3>&nbsp;</h3>";
		if ($this->heading != ""){
			$heading = '<h3>'.$this->heading.'</h3>';
		}
	
		
		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		$text = "&nbsp;";
		if ($this->text != ""){
			$text = $this->text;
		}

		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		$icon = "";
		if ($this->icon != ""){
			$icon = '<div class="icon">'.$this->icon.'</div>';
		}

		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
		$inner = '<div class="inner" >'.$heading.'<p>'.$text.'</p>'.$icon.'</div>'.$this->footer;
		
		$com = '
		<div id="'.$this->id.'" '.
			$this->getClassString('tilebox').' '.
			$this->getAttributesString().' '.
			$this->getStyleString().'
			>';
		
		if ($this->link != ""){
			$com .= '<a href="'.$this->link.'" >'.$inner.'</a>';
		}
		else{
			$com .= $inner;
		}
		
		$com .= '</div>';
		
			


		// write2Debugfile(self::DEBUG_FILENAME, "\nHTML_TileBox generated\n".$com);
		return $com;
	}

	/**
	 * generates an array for use as tile data
	 * 
	 * @version 1.0
	 * 
	 * @param string $permission
	 * @param string $counter
	 * @param string $text
	 * @param string $url
	 * @param string $icon
	 * @param string $bgColor
	 * 
	 * @return array
	 */
	static function tileObject($permission=null, $counter="", $text="", $url="#", $icon="", $bgColor="")
	{
		$tile_data = array(
			"count"=>(string)$counter, 
			"text"=>$text, 
			"url"=>$url,
			"icon"=>$icon, 
			"bgColor"=>$bgColor, 
			"permission"=>$permission
		);
		return $tile_data;
	}
	
	/**
	 * check if the user has the required permission for each tile.
	 * 
	 * @version 1.0
	 * 
	 * @param string $user_permissions	>> array with the users permissions (usually from the session)
	 * @param array $tiles 				>> array of tileObjects
	 * 
	 * @return array
	 */
	static function cleanupTilesArray($user_permissions=null, $tiles=array())
	{
		$return = array();

		if (is_array((array)$user_permissions) && count($user_permissions) > 0)
		{
			foreach ($tiles as $tile){
				if ($tile["permission"] == "" || array_key_exists($tile["permission"], $user_permissions)){
					$return[] = $tile;
				}else{
					write2Debugfile(self::DEBUG_FILENAME, " - show -> no permission [".$tile["permission"]."]");
				}
			}
		}
		return $return;
	}
	
	/**
	 * create footer for the tilebox (anchor with class="tile-box-footer")
	 * if you want to create your own, remember to add >> class="tile-box-footer" for proper results
	 * 
	 * @version 1.2
	 * 
	 * @param string $id
	 * @param string $text
	 * @param string $url
	 * @param string $icon
	 * 
	 * @return $string
	 */
	static function generateFooterLink($id, $text, $url="", $icon="")
	{
		if (! E_ICONS::isValidValue($icon)){
			$icon = "";
		}
		$a		= new HTML_Anchor($id."-footerlink", $text.'&nbsp;'.$icon, $url, "", E_VISIBLE::YES, array(), array("tile-box-footer"));
		$com 	= $a->generateHTML();

		// write2Debugfile(self::DEBUG_FILENAME, "\nHTML_TileBox footer generated\n".$com);
		return $com;
	}

	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// Setter functions for HTML_TileBox
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	/**
	 * setter for <code>$this->footer</code>
	 *
	 * !! your element should contain the class="tile-box-footer" !!
	 * you can use (static) generateFooterLink to generate one
	 *
	 * @param string $string
	 * @return $this
	 */
	public function setFooter($string){
		$this->footer = $string;
		return $this;
	}

	/**
	 * setter for <code>$this->text</code>
	 *
	 * @param string $string
	 * @return $this
	 */
	public function setText($string){
		$this->text = $string;
		return $this;
	}

	/**
	 * setter for <code>$this->heading</code>
	 *
	 * @param string $string
	 * @return $this
	 */
	public function setHeading($heading){
		$this->heading = $heading;
		return $this;
	}

	/**
	 * setter for <code>$this->bgColor</code>
	 *
	 * @param E_COLOR $color
	 * @return $this
	 */
	public function setBackgoundColor($bgColor){
		/*
		if (! E_BG_COLOR::isValidValue($bgColor) ){
			$bgColor = E_BG_COLOR::GREY;
		}
		*/
		$this->bgColor = $bgColor;
		return $this;
	}

	/**
	 * setter for <code>$this->icon</code>
	 *
	 * @param E_ICONS $icon
	 * @return $this
	 */
	public function setIcon($icon){
		if (! E_ICONS::isValidValue($icon) ){
			$icon = "";
		}
		$this->icon = $icon;
		return $this;
	}
	
	/**
	 * setter for <code>$this->link</code>
	 *
	 * @param string $link
	 * @return $this
	 */
	public function setLink($link){
		$this->link = $link;
		return $this;
	}

} // END OF >> HTML_TileBox

// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
/**
 * !!! Attention: This component is not a part of bootstrap.
 * It initial markup for this plugin is a bootstrap checkbox which we already have.
 * So we just need to utilize the HTML_Checkbox and enhance its markup with the custom "data"-attributes for the bootstrap-toggle plugin !!!
 * @see http://www.bootstraptoggle.com/
 *
 * @author _BA5E
 * @category helper
 * @package application\helpers\HTML_helper
 * @since 1.2
 * @version 1.0
 */
class HTML_Toggle extends HTML_Checkbox
{
	/**
	 * @var array
	 */
	protected $customStyles 		= array("ios", "android");

	/**
	 * @var array
	*/
	protected $animationSpeeds 		= array("slow", "quick", "fast");

	/**
	 * maps E_SIZE with the required data-attributes
	 * @var array
	*/
	protected $sizes = array(
			'lg' => 'large',
			'md' => 'normal',
			'sm' => 'small',
			'xs' => 'mini'
	);

	/**
	 * animation speed..	data-class="fast" data-style="slow" data-style="quick"
	 * @var string
	*/
	public $animationSpeed	= "";

	/**
	 * custom width in pixel
	 * @var string
	 */
	public $width 			= null;

	/**
	 * custom height in pixel
	 * @var string
	 */
	public $height 			= null;

	/**
	 * E_SIZE will be mapped using $this->$sizes
	 * @var E_SIZES
	 */
	public $size			= "normal";

	/**
	 * <ios|android> ios  style is rounded and android style is square
	 * @var string
	 */
	public $style			= "";

	/**
	 * @var E_COLOR
	 */
	public $onColor			= "primary";

	/**
	 * @var E_COLOR
	 */
	public $offColor		= "default";

	/**
	 * accepts regular markup Icons/Html
	 * @var string
	 */
	public $onText			= "";

	/**
	 * accepts regular markup Icons/Html
	 * @var string
	 */
	public $offText			= "";


	/**
	 *  HTML_Toggle
	 *  A input of type checkbox with custom attributes
	 *
	 * @param string $id			>> id
	 * @param string $name			>> name
	 * @param E_CHECKED $checked	>> checked attribute
	 * @param string $value			>> value attribute
	 * @param E_SIZES $size			>> toggle size
	 * @param string $onTxt			>> test for the on-state
	 * @param string $offTxt		>> test for the off-state
	 * @param E_COLOR $onColor		>> color for the on-state
	 * @param E_COLOR $offColor		>> color for the off-state
	 * @param E_ENABLED $enabled	>> enable or disable the checkbox
	 * @param E_INLINE $inline		>> align checkbox in one line
	 * @param E_VISIBLE $visible	>> set to <code>false</code> to make the checkbox hidden
	 * @param array $styles			>>
	 * @param array $classes		>>
	 * @param array $attributes		>>
	 *
	 * @return HTML_Toggle
	 */
	public function __construct($id, $name, $checked, $text="", $value=0, $size="sm", $onTxt='<i class="fa fa-check"></i>', $offTxt='<i class="fa fa-circle-o"></i>', $onColor="primary", $offColor="default", $enabled=true, $inline=true, $visible=true, $styles=array(), $classes=array(), $attributes=array())
	{
		parent:: __construct($id, $name, $text, $checked, $value, $enabled, $inline, $visible, $styles, $classes, $attributes);

		self::setAnimationSpeed("quick");
		self::setCustomStyle("");
		self::setSize($size);
		self::setOnColor($onColor);
		self::setOnText($onTxt);
		self::setOffColor($offColor);
		self::setOffText($offTxt);
		self::setHeight(null);	// only via setter
		self::setWidth(null);	// only via setter
	}

	/**
	 * Generates the output of HTML_Toggle
	 * @return $string
	 */
	public function generateHTML()
	{
		$attributes = array(
			"data-toggle"=>"toggle",
			"data-size"=>$this->size,
			"data-onstyle"=>$this->onColor,
			"data-offstyle"=>$this->offColor,
			"data-on"=>$this->onText,
			"data-off"=>$this->offText,
			"data-class"=>$this->animationSpeed
		);

		if ($this->style != ""){
			$attributes["data-style"] = $this->style;
		}

		if (is_int($this->width)){
			$attributes["data-width"] = $this->width;
		}

		if (is_int($this->height)){
			$attributes["data-height"] = $this->height;
		}

		$this->setAttributes($attributes);
		$this->setClasses(array("toggle"));
		$com 	= parent::generateHTML();

		// write2Debugfile(self::DEBUG_FILENAME, "\nHTML_Toggle generated\n".$com);
		return $com;
	}

	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	// Setter functions for HTML_Toggle
	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
	/**
	 * setter for <code>$this->animationSpeed</code>
	 *
	 * @param string $speed >> <slow|quick|fast>
	 * @return $this
	 */
	public function setAnimationSpeed($speed)
	{
		if (! in_array($speed, $this->animationSpeeds) ){
			$speed	= "quick";
		}
		$this->animationSpeed = $speed;
		return $this;
	}

	/**
	 * setter for <code>$this->style</code>. Custom mobile appearance
	 *
	 * @param string $style >> <ios|android>
	 * @return $this
	 */
	public function setCustomStyle($style)
	{
		if (in_array($style, $this->customStyles) ){
			$this->style = $this->style[$style];
		}
		return $this;
	}

	/**
	 * setter for <code>$this->size</code>
	 *
	 * @param E_SIZES $size
	 * @return $this
	 */
	public function setSize($size)
	{
		if (! E_SIZES::isValidValue($size) || $size == E_SIZES::STANDARD ){
			$size	= E_SIZES::MD;
		}
		$this->size = $this->sizes[$size];
		return $this;
	}

	/**
	 * setter for <code>$this->onColor</code>
	 *
	 * @param string $color
	 * @return $this
	 */
	public function setOnColor($color)
	{
		if (!E_COLOR::isValidValue($color)){
			$color = E_COLOR::PRIMARY;
		}
		$this->onColor = $color;
		return $this;
	}

	/**
	 * setter for <code>$this->onText</code>
	 *
	 * @param string $text
	 * @return $this
	 */
	public function setOnText($text)
	{
		$this->onText = $text;
		return $this;
	}

	/**
	 * setter for <code>$this->offColor</code>
	 *
	 * @param string $color
	 * @return $this
	 */
	public function setOffColor($color)
	{
		if (!E_COLOR::isValidValue($color)){
			$color = E_COLOR::PRIMARY;
		}
		$this->offColor = $color;
		return $this;
	}

	/**
	 * setter for <code>$this->offText</code>
	 *
	 * @param string $text
	 * @return $this
	 */
	public function setOffText($text)
	{
		$this->offText = $text;
		return $this;
	}

	/**
	 * setter for <code>$this->width</code>
	 *
	 * @param int $width
	 * @return $this
	 */
	public function setWidth($width)
	{
		if (is_int($width)){
			$this->width = $width;
		}
		return $this;
	}

	/**
	 * setter for <code>$this->height</code>
	 *
	 * @param int $height
	 * @return $this
	 */
	public function setHeight($height)
	{
		if (is_int($height)){
			$this->height = $height;
		}
		return $this;
	}
} // END OF >> HTML_Toggle
// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..

// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
// END OF HTML_COMPONENTS
// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..

/**
 * A color utility that helps manipulate HEX colors
 *
 * @author Arlo Carreon <http://arlocarreon.com>
 * @link http://mexitek.github.io/phpColors/
 * @license http://arlo.mit-license.org/
 * @since 1.2
 * @package application\helpers\HTML_helper
 * 
 */
class Color {

	private $_hex;
	private $_hsl;
	private $_rgb;

	/**
	 * Auto darkens/lightens by 10% for sexily-subtle gradients.
	 * Set this to FALSE to adjust automatic shade to be between given color
	 * and black (for darken) or white (for lighten)
	 */
	const DEFAULT_ADJUST = 10;

	/**
	 * Instantiates the class with a HEX value
	 * @param string $hex
	 * @throws Exception "Bad color format"
	 */
	function __construct( $hex ) {
		// Strip # sign is present
		$color = str_replace("#", "", $hex);

		// Make sure it's 6 digits
		if( strlen($color) === 3 ) {
			$color = $color[0].$color[0].$color[1].$color[1].$color[2].$color[2];
		} else if( strlen($color) != 6 ) {
			throw new Exception("HEX color needs to be 6 or 3 digits long");
		}

		$this->_hsl = self::hexToHsl( $color );
		$this->_hex = $color;
		$this->_rgb = self::hexToRgb( $color );
	}

	// ====================
	// = Public Interface =
	// ====================

	/**
	 * Given a HEX string returns a HSL array equivalent.
	 * @param string $color
	 * @return array HSL associative array
	 */
	public static function hexToHsl( $color ){

		// Sanity check
		$color = self::_checkHex($color);

		// Convert HEX to DEC
		$R = hexdec($color[0].$color[1]);
		$G = hexdec($color[2].$color[3]);
		$B = hexdec($color[4].$color[5]);

		$HSL = array();

		$var_R = ($R / 255);
		$var_G = ($G / 255);
		$var_B = ($B / 255);

		$var_Min = min($var_R, $var_G, $var_B);
		$var_Max = max($var_R, $var_G, $var_B);
		$del_Max = $var_Max - $var_Min;

		$L = ($var_Max + $var_Min)/2;

		if ($del_Max == 0)
		{
			$H = 0;
			$S = 0;
		}
		else
		{
			if ( $L < 0.5 ) $S = $del_Max / ( $var_Max + $var_Min );
			else            $S = $del_Max / ( 2 - $var_Max - $var_Min );

			$del_R = ( ( ( $var_Max - $var_R ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;
			$del_G = ( ( ( $var_Max - $var_G ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;
			$del_B = ( ( ( $var_Max - $var_B ) / 6 ) + ( $del_Max / 2 ) ) / $del_Max;

			if      ($var_R == $var_Max) $H = $del_B - $del_G;
			else if ($var_G == $var_Max) $H = ( 1 / 3 ) + $del_R - $del_B;
			else if ($var_B == $var_Max) $H = ( 2 / 3 ) + $del_G - $del_R;

			if ($H<0) $H++;
			if ($H>1) $H--;
		}

		$HSL['H'] = ($H*360);
		$HSL['S'] = $S;
		$HSL['L'] = $L;

		return $HSL;
	}

	/**
	 *  Given a HSL associative array returns the equivalent HEX string
	 * @param array $hsl
	 * @return string HEX string
	 * @throws Exception "Bad HSL Array"
	 */
	public static function hslToHex( $hsl = array() ){
		// Make sure it's HSL
		if(empty($hsl) || !isset($hsl["H"]) || !isset($hsl["S"]) || !isset($hsl["L"]) ) {
			throw new Exception("Param was not an HSL array");
		}

		list($H,$S,$L) = array( $hsl['H']/360,$hsl['S'],$hsl['L'] );

		if( $S == 0 ) {
			$r = $L * 255;
			$g = $L * 255;
			$b = $L * 255;
		} else {

			if($L<0.5) {
				$var_2 = $L*(1+$S);
			} else {
				$var_2 = ($L+$S) - ($S*$L);
			}

			$var_1 = 2 * $L - $var_2;

			$r = round(255 * self::_huetorgb( $var_1, $var_2, $H + (1/3) ));
			$g = round(255 * self::_huetorgb( $var_1, $var_2, $H ));
			$b = round(255 * self::_huetorgb( $var_1, $var_2, $H - (1/3) ));

		}

		// Convert to hex
		$r = dechex($r);
		$g = dechex($g);
		$b = dechex($b);

		// Make sure we get 2 digits for decimals
		$r = (strlen("".$r)===1) ? "0".$r:$r;
		$g = (strlen("".$g)===1) ? "0".$g:$g;
		$b = (strlen("".$b)===1) ? "0".$b:$b;

		return $r.$g.$b;
	}


	/**
	 * Given a HEX string returns a RGB array equivalent.
	 * @param string $color
	 * @return array RGB associative array
	 */
	public static function hexToRgb( $color ){

		// Sanity check
		$color = self::_checkHex($color);

		// Convert HEX to DEC
		$R = hexdec($color[0].$color[1]);
		$G = hexdec($color[2].$color[3]);
		$B = hexdec($color[4].$color[5]);

		$RGB['R'] = $R;
		$RGB['G'] = $G;
		$RGB['B'] = $B;

		return $RGB;
	}


	/**
	 * Given an RGB associative array returns the equivalent HEX string
	 * 
	 * @param array $rgb
	 * @return string RGB string
	 * @throws Exception "Bad RGB Array"
	 */
	public static function rgbToHex( $rgb = array() ){
		// Make sure it's RGB
		if(empty($rgb) || !isset($rgb["R"]) || !isset($rgb["G"]) || !isset($rgb["B"]) ) {
			throw new Exception("Param was not an RGB array");
		}

		// https://github.com/mexitek/phpColors/issues/25#issuecomment-88354815
		// Convert RGB to HEX
		$hex[0] = str_pad(dechex($rgb['R']), 2, '0', STR_PAD_LEFT);
		$hex[1] = str_pad(dechex($rgb['G']), 2, '0', STR_PAD_LEFT);
		$hex[2] = str_pad(dechex($rgb['B']), 2, '0', STR_PAD_LEFT);

		return implode( '', $hex );
	}

	/**
	 * Given a HEX value, returns a darker color. If no desired amount provided, then the color halfway between
	 * given HEX and black will be returned.
	 * @param int $amount
	 * @return string Darker HEX value
	 */
	public function darken( $amount = self::DEFAULT_ADJUST ){
		// Darken
		$darkerHSL = $this->_darken($this->_hsl, $amount);
		// Return as HEX
		return self::hslToHex($darkerHSL);
	}

	/**
	 * Given a HEX value, returns a lighter color. If no desired amount provided, then the color halfway between
	 * given HEX and white will be returned.
	 * @param int $amount
	 * @return string Lighter HEX value
	 */
	public function lighten( $amount = self::DEFAULT_ADJUST ){
		// Lighten
		$lighterHSL = $this->_lighten($this->_hsl, $amount);
		// Return as HEX
		return self::hslToHex($lighterHSL);
	}

	/**
	 * Given a HEX value, returns a mixed color. If no desired amount provided, then the color mixed by this ratio
	 * @param string $hex2 Secondary HEX value to mix with
	 * @param int $amount = -100..0..+100
	 * @return string mixed HEX value
	 */
	public function mix($hex2, $amount = 0){
		$rgb2 = self::hexToRgb($hex2);
		$mixed = $this->_mix($this->_rgb, $rgb2, $amount);
		// Return as HEX
		return self::rgbToHex($mixed);
	}

	/**
	 * Creates an array with two shades that can be used to make a gradient
	 * @param int $amount Optional percentage amount you want your contrast color
	 * @return array An array with a 'light' and 'dark' index
	 */
	public function makeGradient( $amount = self::DEFAULT_ADJUST ) {
		// Decide which color needs to be made
		if( $this->isLight() ) {
			$lightColor = $this->_hex;
			$darkColor = $this->darken($amount);
		} else {
			$lightColor = $this->lighten($amount);
			$darkColor = $this->_hex;
		}

		// Return our gradient array
		return array( "light" => $lightColor, "dark" => $darkColor );
	}


	/**
	 * Returns whether or not given color is considered "light"
	 * @param string|Boolean $color
	 * @param int $lighterThan
	 * @return boolean
	 */
	public function isLight( $color = FALSE, $lighterThan = 130 ){
		// Get our color
		$color = ($color) ? $color : $this->_hex;

		// Calculate straight from rbg
		$r = hexdec($color[0].$color[1]);
		$g = hexdec($color[2].$color[3]);
		$b = hexdec($color[4].$color[5]);

		return (( $r*299 + $g*587 + $b*114 )/1000 > $lighterThan);
	}

	/**
	 * Returns whether or not a given color is considered "dark"
	 * @param string|Boolean $color
	 * @param int $darkerThan
	 * @return boolean
	 */
	public function isDark( $color = FALSE, $darkerThan = 130 ){
		// Get our color
		$color = ($color) ? $color:$this->_hex;

		// Calculate straight from rbg
		$r = hexdec($color[0].$color[1]);
		$g = hexdec($color[2].$color[3]);
		$b = hexdec($color[4].$color[5]);

		return (( $r*299 + $g*587 + $b*114 )/1000 <= $darkerThan);
	}

	/**
	 * Returns the complimentary color
	 * @return string Complementary hex color
	 *
	 */
	public function complementary() {
		// Get our HSL
		$hsl = $this->_hsl;

		// Adjust Hue 180 degrees
		$hsl['H'] += ($hsl['H'] > 180) ? -180 : 180;

		
		// Return the new value in HEX
		return self::hslToHex($hsl);
	}

	/**
	 * Returns your color's HSL array
	 */
	public function getHsl() {
		return $this->_hsl;
	}
	/**
	 * Returns your original color
	 */
	public function getHex() {
		return $this->_hex;
	}
	/**
	 * Returns your color's RGB array
	 */
	public function getRgb() {
		return $this->_rgb;
	}

	/**
	 * Returns the cross browser CSS3 gradient
	 * @param int $amount Optional: percentage amount to light/darken the gradient
	 * @param boolean $vintageBrowsers Optional: include vendor prefixes for browsers that almost died out already
	 * @param string $prefix Optional: prefix for every lines
	 * @param string $suffix Optional: suffix for every lines
	 * @link  http://caniuse.com/css-gradients Resource for the browser support
	 * @return string CSS3 gradient for chrome, safari, firefox, opera and IE10
	 */
	public function getCssGradient( $amount = self::DEFAULT_ADJUST, $vintageBrowsers = FALSE, $suffix = "" , $prefix = "" ) {

		// Get the recommended gradient
		$g = $this->makeGradient($amount);

		$css = "";
		/* fallback/image non-cover color */
		$css .= "{$prefix}background-color: #".$this->_hex.";{$suffix}";

		/* IE Browsers */
		$css .= "{$prefix}filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#".$g['light']."', endColorstr='#".$g['dark']."');{$suffix}";

		/* Safari 4+, Chrome 1-9 */
		if ( $vintageBrowsers ) {
			$css .= "{$prefix}background-image: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#".$g['light']."), to(#".$g['dark']."));{$suffix}";
		}

		/* Safari 5.1+, Mobile Safari, Chrome 10+ */
		$css .= "{$prefix}background-image: -webkit-linear-gradient(top, #".$g['light'].", #".$g['dark'].");{$suffix}";

		/* Firefox 3.6+ */
		if ( $vintageBrowsers ) {
			$css .= "{$prefix}background-image: -moz-linear-gradient(top, #".$g['light'].", #".$g['dark'].");{$suffix}";
		}

		/* Opera 11.10+ */
		if ( $vintageBrowsers ) {
			$css .= "{$prefix}background-image: -o-linear-gradient(top, #".$g['light'].", #".$g['dark'].");{$suffix}";
		}

		/* Unprefixed version (standards): FF 16+, IE10+, Chrome 26+, Safari 7+, Opera 12.1+ */
		$css .= "{$prefix}background-image: linear-gradient(to bottom, #".$g['light'].", #".$g['dark'].");{$suffix}";

		// Return our CSS
		return $css;
	}

	// ===========================
	// = Private Functions Below =
	// ===========================


	/**
	 * Darkens a given HSL array
	 * @param array $hsl
	 * @param int $amount
	 * @return array $hsl
	 */
	private function _darken( $hsl, $amount = self::DEFAULT_ADJUST){
		// Check if we were provided a number
		if( $amount ) {
			$hsl['L'] = ($hsl['L'] * 100) - $amount;
			$hsl['L'] = ($hsl['L'] < 0) ? 0:$hsl['L']/100;
		} else {
			// We need to find out how much to darken
			$hsl['L'] = $hsl['L']/2 ;
		}

		return $hsl;
	}

	/**
	 * Lightens a given HSL array
	 * @param array $hsl
	 * @param int $amount
	 * @return array $hsl
	 */
	private function _lighten( $hsl, $amount = self::DEFAULT_ADJUST){
		// Check if we were provided a number
		if( $amount ) {
			$hsl['L'] = ($hsl['L'] * 100) + $amount;
			$hsl['L'] = ($hsl['L'] > 100) ? 1:$hsl['L']/100;
		} else {
			// We need to find out how much to lighten
			$hsl['L'] += (1-$hsl['L'])/2;
		}

		return $hsl;
	}

	/**
	 * Mix 2 rgb colors and return an rgb color
	 * @param array $rgb1
	 * @param array $rgb2
	 * @param int $amount ranged -100..0..+100
	 * @return array $rgb
	 *
	 * 	ported from http://phpxref.pagelines.com/nav.html?includes/class.colors.php.source.html
	 */
	private function _mix($rgb1, $rgb2, $amount = 0) {

		$r1 = ($amount + 100) / 100;
		$r2 = 2 - $r1;

		$rmix = (($rgb1['R'] * $r1) + ($rgb2['R'] * $r2)) / 2;
		$gmix = (($rgb1['G'] * $r1) + ($rgb2['G'] * $r2)) / 2;
		$bmix = (($rgb1['B'] * $r1) + ($rgb2['B'] * $r2)) / 2;

		return array('R' => $rmix, 'G' => $gmix, 'B' => $bmix);
	}

	/**
	 * Given a Hue, returns corresponding RGB value
	 * @param int $v1
	 * @param int $v2
	 * @param int $vH
	 * @return int
	 */
	private static function _huetorgb( $v1,$v2,$vH ) {
		if( $vH < 0 ) {
			$vH += 1;
		}

		if( $vH > 1 ) {
			$vH -= 1;
		}

		if( (6*$vH) < 1 ) {
			return ($v1 + ($v2 - $v1) * 6 * $vH);
		}

		if( (2*$vH) < 1 ) {
			return $v2;
		}

		if( (3*$vH) < 2 ) {
			return ($v1 + ($v2-$v1) * ( (2/3)-$vH ) * 6);
		}

		return $v1;

	}

	/**
	 * You need to check if you were given a good hex string
	 * @param string $hex
	 * @return string Color
	 * @throws Exception "Bad color format"
	 */
	private static function _checkHex( $hex ) {
		// Strip # sign is present
		$color = str_replace("#", "", $hex);

		// Make sure it's 6 digits
		if( strlen($color) == 3 ) {
			$color = $color[0].$color[0].$color[1].$color[1].$color[2].$color[2];
		} else if( strlen($color) != 6 ) {
			throw new Exception("HEX color needs to be 6 or 3 digits long");
		}

		return $color;
	}
	
	/**
	 * Generates the complementary color for a given color
	 * @link https://rene-vorndran.de/2015/10/php-funktion-zur-berechnung-einer-komplementaerfarbe/
	 * @param $color
	 * @return string
	 */
	public static function get_complementary($color)
	{
		$leadingHash = false;
	
		//clear whitespaces just to be shure
		$color = trim($color);
	
		//cut leading #
		if (strpos($color, "#") !== false) {
			$color = substr($color, 1);
			$leadingHash = true;
		}
	
		//check if valid color string
		if  (preg_match('/^[A-Fa-f0-9]+$/', $color)== 'false') {
			return $leadingHash ? '#' . $color : $color;
		}
	
		$r1 = dechex((15 - (hexdec($color[0]))));
		$r2 = dechex((15 - (hexdec($color[1]))));
		$g1 = dechex((15 - (hexdec($color[2]))));
		$g2 = dechex((15 - (hexdec($color[3]))));
		$b1 = dechex((15 - (hexdec($color[4]))));
		$b2 = dechex((15 - (hexdec($color[5]))));
	
		$complementary = $r1 . $r2 . $g1 . $g2 . $b1 . $b2;
	
		return $leadingHash ? '#' . $complementary : $complementary;
	}

}