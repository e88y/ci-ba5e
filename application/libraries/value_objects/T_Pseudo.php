<?php 
/**
 * value object pseudo class
 * All other value objects extend from this class
 * 
 * 
 * @author Marco Eberhardt
 * @category value object
 * @package application\libraries\value_objects\T_Pseudo
 * @version 1.2
 */
class T_Pseudo
{
	const DEBUG_FILENAME = "T_Base.log";
	
	function __construct($data=array())
	{
		$this->init($data);
	}
	
	function init($data)
	{
		$local_vars = get_class_vars(get_class($this));
		
		if ($data && ( is_object($data) || is_array($data) ) )
		{
			foreach ($data as $key => $value)
			{
				if (array_key_exists($key, $local_vars)){
					self::setValue($key, $value);
				}
			}
		}
		//write2Debugfile(self::DEBUG_FILENAME, " - child-class[".get_class($this)."] local-vars[".print_r($local_vars, true)."]\n".print_r($this, true), true);
	}
	
	/**
	 * set value
	 *  
	 * @param string $name
	 * @param mixed $value
	 */
	private function setValue($name, $value)
	{
		//write2Debugfile(self::DEBUG_FILENAME, " - name[$name] value-".print_r($value, true), true);
		if (is_array($value) || is_object($value) || is_array(json_decode($value)))	// check for array
		{
			if (is_array($value) || is_object($value)){
				$this->$name	= $this->list_escape($value);
			}
			else if (is_array( json_decode($value)))
			{
				$this->$name 	= json_encode( $this->list_escape( json_decode($value) ) );
			}
			//write2Debugfile(self::DEBUG_FILENAME, " - ARRAY|OBJECT - name[$name]", true);
		}
		else if (is_scalar($value) || is_null($value))
		{
			$this->$name = html_escape($value);
			//write2Debugfile(self::DEBUG_FILENAME, " - SCALAR - name[$name] value[".$this->$name."]", true);
		}
	}
	
	/**
	 * get value by name
	 * @param mixed $name
	 */
	public function getValue($name)
	{
		if (isset($this->$name)){
			return $this->$name;
		}
	}
	
	/**
	 * Recursive function to escape array-values or object values
	 */
	public function list_escape($array_or_object)
	{
		if (is_array($array_or_object) || is_object($array_or_object) )
		{
			//write2Debugfile(self::DEBUG_FILENAME, " -> ESCAPE LIST", true);
			foreach ($array_or_object as $key => $value)
			{
				if (is_array($value) || is_object($value) || is_array(json_decode($value)) )
				{
					if (is_array( json_decode($value))){
						$value	= json_encode( $this->list_escape( json_decode($value) ) );
					}
					else{
						$value	= $this->list_escape($value);
					}
				}
				else{
					$value = html_escape($value);
				}
	
	
				if (is_array($array_or_object)){
					$array_or_object[$key] = $value;
				}
				else if (is_object($array_or_object)){
					$array_or_object->$key = $value;
				}
	
				//write2Debugfile(self::DEBUG_FILENAME, " -> key[$key] value [".print_r($value, true)."]", true);
			}
			return $array_or_object;
		}
	}
}	
?>