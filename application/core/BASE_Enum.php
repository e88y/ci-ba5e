<?php
/**
 * Ennumeration class 
 * 
 * @see aplication/helper/enum_helper for implementation
 * 
 * @author Markus Günther
 * @category helper
 * @package application\core\BASE_Enum
 * @version 1.0
 */
abstract class BASE_Enum
{
	private static $constCache = NULL;

	/**
	 * Get all constants from caller class or by providing an class name as parameter
	 * 
	 * @param string $explizitClassName
	 * @return multitype:
	 */
	public static function getConstants($explizitClassName="")
	{
		if($explizitClassName == ""){
			$explizitClassName = get_called_class();
		}

		$reflect = new ReflectionClass($explizitClassName);
		self::$constCache = $reflect->getConstants();

		return self::$constCache;
	}

	/**
	 * Check, if the given $name is valid for the caller class
	 * 
	 * @param string $name
	 * @param bool $strict
	 * @return boolean
	 */
	public static function isValidName($name, $strict = false)
	{
		$constants = self::getConstants();

		if ($strict) {
			return array_key_exists($name, $constants);
		}
		$keys = array_map('strtolower', array_keys($constants));

		return in_array(strtolower($name), $keys);
	}

	/**
	 * Check, if the provided $value is valid for the class
	 *
	 * @param string $value
	 * @param string $explizitClassName
	 * @return boolean
	 */
	public static function isValidValue($value, $explizitClassName="")
	{
		$values = array_values(self::getConstants($explizitClassName));
		$return = in_array($value, $values, $strict = true);
		if ($return == false){
			//show_error("no validValue[$value]");
		}
		return $return ;
	}
}
?>