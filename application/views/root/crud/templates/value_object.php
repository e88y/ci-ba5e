<?php 
	
	$variables = '';
	foreach ($cdata as $columnnname => $value){
		$variables .= "\n\t".'public $'.$columnnname.'	= NULL;';
	}


	$str = '<?php 
/**
 * '.ucfirst($classname).' - Value Object
 *
 * @author Marco Eberhardt
 * @category value object
 * @package application\libraries\value_objects\T_'.ucfirst($classname).'
 * @version 1.0
 */
class T_'.ucfirst($classname).' extends T_Pseudo
{
	const DEBUG_FILENAME = "T_'.ucfirst($classname).'.log";
	'.$variables.'

	function __construct($data=array())
	{
		parent::__construct($data);
		
		write2Debugfile(self::DEBUG_FILENAME, "T_'.ucfirst($classname).' src-".print_r($data, true)."\nresult-".print_r($this, true), false);
	}
}
	
?>';
	echo $str;
?>