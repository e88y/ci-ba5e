<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * This helper contains basic/common functions.
 * 
 * @author Marco Eberhardt, Stefan Geschwentner
 * @category helper
 * @package application\helpers\base_helper
 * @version 1.0
 */

// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
// ..:: SHORTCUTS TO GET PATHS FROM CONFIG
// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
if ( ! function_exists('root_path'))
{
	/**
	 * shortcut to <code>$this->config->item("root_path")</code>
	 * 
	 * @author Marco Eberhardt
	 * @category helper
	 * @package application\helpers\base_helper
	 * @version 1.0
	 */
	function root_path(){
		$ci =& get_instance();
		return $ci->config->item("root_path");
	}
}

if ( ! function_exists('upload_path'))
{
	/**
	 * shortcut to <code>$this->config->item("upload_folder")</code>
	 * 
	 * @author Marco Eberhardt
	 * @category helper
	 * @package application\helpers\base_helper
	 * @version 1.0
	 * 
	 * @return string containing the common upload path
	 */
	function upload_path(){
		$ci =& get_instance();
		return $ci->config->item("upload_folder");
	}
}

// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
// ..:: URL 
// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
if ( ! function_exists('url_exists'))
{
	/**
	 * check a url using file_get_contents (we read only the first byte since we just want to no if there is content)
	 *
	 * @author Marco Eberhardt
	 * @category helper
	 * @package application\helpers\base_helper
	 * @version 1.0
	 *
	 * @param string $url >> full url to you want to check
	 * @return bool
	 */
	function url_exists($url){
		if (@file_get_contents($url, false, NULL, 0, 1)){
			return true;
		}
		return false;
	}
}

if ( ! function_exists('protocoll'))
{
	/**
	 * Just returns the protocol. Should also work with proxy but untested
	 * 
	 * @author Marco Eberhardt
	 * @category helper
	 * @package application\helpers\base_helper
	 * @version 1.0
	 * 
	 * @return string >> [http:// | https://]
	 */
	function protocoll()
	{
		$protocol = "http://";
		if (isset($_SERVER['HTTPS']) &&
				($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
		{
			$protocol = 'https://';
		}
		return $protocol;
	}
}

if ( ! function_exists('ssl_enabled'))
{
	/**
	 * check if SSL is enabled
	 * 
	 * @author Marco Eberhardt
	 * @category helper
	 * @package application\helpers\base_helper
	 * @version 1.0
	 * 
	 * @return boolean
	 */
	function ssl_enabled(){
		return (protocoll() === 'https://' ? true:false);
	}	
}

if ( ! function_exists('strong_random_bytes'))
{
	/**
	 * 
	 * @link https://stackoverflow.com/questions/6101956/generating-a-random-password-in-php
	 * @author jeteon
	 * 
	 * @param number $nbBytes
	 * @throws \Exception
	 * @return string
	 */
	function strong_random_bytes($length=32)
	{
		$strong = false; // Flag for whether a strong algorithm was used
		$bytes = openssl_random_pseudo_bytes($length, $strong);
		
		if ( ! $strong)
		{
			// System did not use a cryptographically strong algorithm
			throw new Exception('Strong algorithm not available for PRNG.');
		}
		return $bytes;
	}
}

if ( ! function_exists('generatePassword'))
{
	/**
	 * The 3/4 factor is due to the fact that base64 encoding results in a string that has a length at least a third bigger than the byte string. 
	 * The result will be exact for $length being a multiple of 4 and up to 3 characters longer otherwise. 
	 * Since the extra characters are predominantly the padding character =, if we for some reason had a constraint that the password be an exact length, then we can truncate it to the length we want. 
	 * This is especially because for a given $length, all passwords would end with the same number of these, so that an attacker who had access to a result password, would have up to 2 less characters to guess.
	 * 
	 * @link https://stackoverflow.com/questions/6101956/generating-a-random-password-in-php
	 * @author jeteon
	 * 
	 * @param int $length
	 * @return string
	 */
	function generatePassword($length){
		return base64_encode(strong_random_bytes( intval(ceil($length * 3 / 4) )));
	}
}

// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
// ..:: HASHING AND FORMATTING
// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
if ( ! function_exists('hash_password'))
{
	/**
	 * creates a hashed password 
	 * 
	 * @author Marco Eberhardt
	 * @category helper
	 * @package application\helpers\base_helper
	 * @version 1.0
	 * 
	 * @param string $salt
	 * @param string $password_plain
	 * @return string
	 */
	function hash_password($salt, $password_plain)
	{
		return hash("sha256", $salt . APP_SALT_SEPERATOR . $password_plain);
	}	
}

if ( ! function_exists('format_number'))
{
	/**
	 * Formats a number to the 'correct' format, which depends on the loaded localization.
	 * 
	 * @author Marco Eberhardt
	 * @category helper
	 * @package application\helpers\base_helper
	 * @version 1.0
	 * 
	 * @param float $number
	 * @param string $prefix
	 * @param string $suffix
	 * 
	 * @return string
	 */
	function format_number($number, $prefix="", $suffix="")
	{
	    if(is_string($number))
	    {
	        $number = floatVal(str_replace(",",".",$number));
	    }
		return $prefix.number_format($number, lang("num_decimals"), lang("decimal_seperator"), lang("thousand_seperator")).$suffix;
	}
}

if ( ! function_exists('format_timestamp2date'))
{
	/**
	 * Formats a unix timestamp to the date format, which depends on the loaded localization.
	 * 
	 * @author Marco Eberhardt
	 * @category helper
	 * @package application\helpers\base_helper
	 * @version 1.1
	 * 
	 * @param int $time			>> timestamp to convert
	 * @param string $prefix	>> optional prefix
	 * @param string $suffix	>> optional suffix
	 * 
	 * @return string
	 */
	function format_timestamp2date($time, $prefix="", $suffix="")
	{
		if ($time > 0){
			return $prefix.date(lang("date_format"), $time).$suffix;
		}
		return " - ";
	}
}

if ( ! function_exists('format_timestamp2datetime'))
{
	/**
	 * Formats a unix timestamp to a date with time, which format depends on the loaded localization.
	 * Note: This is not for the mysql-datetime 
	 * 
	 * @author Marco Eberhardt
	 * @category helper
	 * @package application\helpers\base_helper
	 * @version 1.1
	 * 
	 * @param int $time				>> timestamp to convert
	 * @param string $prefix		>> optional prefix
	 * @param string $suffix		>> optional suffix
	 * 
	 * @return string
	 */
	function format_timestamp2datetime($time, $prefix="", $suffix="")
	{
		if ($time > 0){
			return $prefix.date(lang("date_format_with_time"), $time).$suffix;
		}
		return " - ";
	}
}

/**
 * Convert a ms sql datetime sting to a given format
 * 
 * @author Marco Eberhardt
 * @category helper
 * @package application\helpers\base_helper
 * @version 1.0
 * 
 * @param string $mssqldate
 * @param string $format
 * 
 * @return string
 */
if(!function_exists("formatMSSQLDate"))
{
    function formatMSSQLDate($mssqldate,$format="")
    {
        if($format == "")
        {
            $format = lang("date_format");
        }
        if($mssqldate != "")
        {
            $tmp = explode(" ",$mssqldate);
            if(count($tmp)==2)
            {
                list($year,$month,$day) = explode("-",$tmp[0]);
                list($hour,$min,$sec) = explode(":",$tmp[1]);
            }
            else
            {
                list($year,$month,$day) = explode("-",$tmp[0]);
                list($hour,$min,$sec) = array(0,0,0);
            }
            return date($format, mktime(intval($hour),intval($min),intval($sec),intval($month),intval($day),intval($year)));
        }
        else
        {   
            return "";
        }
    }
}

// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
// ..:: ENCODE / DECODE
// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
if ( ! function_exists('encode_string'))
{
	/**
	 * Encodes a string using safe_utf8_encode and urlencode methods.
	 * !!! This does not ENCRYPT the string !!!
	 * 
	 * @author Marco Eberhardt
	 * @category helper
	 * @package application\helpers\base_helper
	 * @version 1.0
	 *
	 * @param string $plain_text 	>> must be encoded with encode_string-method
	 * 
	 * @return string 				>> utf8 and urlencoded text
	 */
	function encode_string($plain_text)
	{
		return urlencode(safe_utf8_encode($plain_text));
	}
}

if ( ! function_exists('decode_string'))
{
	/**
	 * Decodes a encoded string (created with encode_string) and returns the plain text.
	 * ! This does not DECRYPT the string !
	 * 
	 * @author Marco Eberhardt
	 * @category helper
	 * @package application\helpers\base_helper
	 * @version 1.0
	 *
	 * @param string $encoded_text >> must be encoded with encode_string-method
	 * 
	 * @return string >> plain text
	 */
	function decode_string($encoded_text)
	{
		$ci =& get_instance();
		$ci->load->library('encryption');
		$ci->encryption->initialize( $ci->config->item('encryption_settings') );

		return urlencode(safe_utf8_decode($encoded_text));
	}
}

if ( ! function_exists('safe_b64encode'))
{
	/**
	 * replaces '+/=' on a the base64 encoded $string
	 *
	 * @author Stefan Geschwentner
	 * @category helper
	 * @package application\helpers\base_helper
	 * @version 1.0
	 *
	 * @param string $string
	 * 
	 * @return mixed
	 */
	function safe_b64encode($string)
	{
		$data = base64_encode($string);
		$data = str_replace(array('+', '/', '='), array('-', '_', ''), $data);
		return $data;
	}
}

if ( ! function_exists('safe_b64decode'))
{
	/**
	 * oposite method for safe_b64encode replaces '-_' on $base64_string
	 *
	 * @author Stefan Geschwentner
	 * @category helper
	 * @package application\helpers\base_helper
	 * @version 1.0
	 *
	 * @param string $base64_string
	 * 
	 * @return string
	 */
	function safe_b64decode($base64_string)
	{
		$data = str_replace(array('-', '_'), array('+', '/'), $base64_string);
		$mod4 = strlen($data) % 4;
		if ($mod4) {
			$data .= substr('====', $mod4);
		}
		return base64_decode($data);
	}
}

if ( ! function_exists('safe_utf8_encode'))
{
	/**
	 * utf-8 encode, if the detected encoding dont match utf8
	 *
	 * @author Stefan Geschwentner
	 * @category helper
	 * @package application\helpers\base_helper
	 * @version 1.0
	 *
	 * @param string $value
	 * 
	 * @return string
	 */
	function safe_utf8_encode($value)
	{
		mb_detect_order("UTF-8,ISO-8859-2,ISO-8859-1,ASCII");	// encoding detection order. Used by safe_utf8_encode and file_encoding
		if(mb_detect_encoding($value) != "UTF-8"){
			$value = utf8_encode($value);
		}

		$value = str_replace(array('+', '/', '='), array('-', '_', ''), $value);
		return $value;
	}
}

if ( ! function_exists('safe_utf8_decode'))
{
	/**
	 * utf-8 decode, if the detected encoding matches utf8. changes the mb_detect_order taht UTF-8 will be checked first
	 * 
	 * @author Stefan Geschwentner
	 * @category helper
	 * @package application\helpers\base_helper
	 * @version 1.0
	 *
	 * @param string $value
	 * 
	 * @return string
	 */
	function safe_utf8_decode($value)
	{
		$value = str_replace(array('-', '_'), array('+', '/'), $value);

		mb_detect_order("UTF-8,ISO-8859-2,ISO-8859-1,ASCII");	// encoding detection order. Used by safe_utf8_encode and file_encoding
		if(mb_detect_encoding($value) == "UTF-8")
		{
			$value = utf8_decode($value);
		}
		return $value;
	}
}

// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
// ..:: ARRAY MANIPULATION
// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
if ( ! function_exists('array_from_get'))
{
	/**
	 * Takes a GET-Parameter string and creates a key-value array.
	 * If you have encrypted params use use <code>decrypt_get()</code> instead.
	 * 
	 * @author Marco Eberhardt
	 * @category helper
	 * @package application\helpers\base_helper
	 * @version 1.0
	 *
	 * @param array $parameters >> GET-Array (key-value array ( array("edit_id"=>123, "test"=>44) ) ...
	 * @return
	 */
	function array_from_get($parameters)
	{
		$result = array();
		foreach ($parameters as $key => $value)
		{
			$result["$key"] = ($value);
		}
		return $params;
	}
}

if ( ! function_exists('array_remap'))
{
	/**
	 * Rewrite array index from array of objects or two-dimensional arrays
	 *
	 * @author Marco Eberhardt
	 * @category helper
	 * @package application\helpers\base_helper
	 * @version 1.0
	 *
	 * @param array $array		>> input array
	 * @param mixed $keyField 	>> The column to use as the index/keys for the returned array. This value may be the integer key of the column, or it may be the string key name.
	 * @param mixed $value		>> The column of values to return. This value may be an integer key of the column you wish to retrieve, or it may be a string key name for an associative array or property name. It may also be NULL to return complete arrays or objects
	 * @param bool $kSort		>> if set to true the return array will be sorted by its keys
	 *
	 * @return array
	 */
	function array_remap($array, $keyField, $value=null, $kSort=false)
	{
		if (version_compare(PHP_VERSION, '7.0.0') >= 0) {
			// At least PHP version 7.0.0 >>  The function array_columns now supports an array of objects as well as two-dimensional arrays.
			$result = array_column($array, $value, $keyField);
			
			if ($kSort === true){
				ksort($result, SORT_REGULAR);
			}
			
			return $result;
		}
		else
		{
			// Pre-PHP7
			$result = array();
			foreach ($array as $subArray)
			{
				if (is_null($keyField) && array_key_exists($value, $subArray)) {
					$result[] = is_object($subArray) ? $subArray->$value : $subArray[$value];
				}
				elseif (array_key_exists($keyField, $subArray))
				{
					if (is_null($value))
					{
						$index 				= is_object($subArray) ? $subArray->$keyField : $subArray[$keyField];
						$result[$index] 	= $subArray;
					}
					elseif (array_key_exists($value, $subArray))
					{
						$index 				= is_object($subArray) ? $subArray->$keyField : $subArray[$keyField];
						$result[$index] 	= is_object($subArray) ? $subArray->$value : $subArray[$value];
					}
				}
			}
			
			if ($kSort === true){
				ksort($result, SORT_REGULAR);
			}
			
			return $result;
		}
	}
}

if ( ! function_exists('array_to_get'))
{
	/**
	 * Takes a key-value array and creates an GET-Parameter string.
	 * If you want your stuff encrypted, use <code>encrypt_get()</code> 
	 * 
	 * @author Marco Eberhardt
	 * @category helper
	 * @package application\helpers\base_helper
	 * @version 1.0
	 * 
	 * @param string $parameters >> key-value array ( array("edit_id"=>123, "test"=>44) ) ...
	 * @return
	 */
	function array_to_get($parameters)
	{
		$params = "";
		foreach ($parameters as $key => $value){
			$params .= urldecode($key) . '=' . urlencode($value) . '&';
		}
		if($params != ""){
			$params = substr($params, 0, -1);
		}
		
		return $params;
	}
}

if (! function_exists('array_to_ul'))
{
	/**
	 * takes an array an coverts it to unordered list <ul>
	 * 
	 * @author Marco Eberhardt
	 * @category helper
	 * @package application\helpers\base_helper
	 * @version 1.0
	 *
	 * @param array $array
	 * 
	 * @return string unordered list <ul>
	 */
	function array_to_ul($array){
		return "<ul><li>" . implode("</li><li>", $array) . "</li></ul>";
	}
}

if ( ! function_exists('checkIBAN'))
{
	/**
	 * @link https://en.wikipedia.org/wiki/International_Bank_Account_Number#Validating_the_IBAN
	 * @link https://stackoverflow.com/questions/20983339/validate-iban-php
	 *
	 * @version 1.0
	 *
	 * @param string $iban
	 * @return boolean
	 */
	function checkIBAN($iban)
	{
		$iban 		= strtolower(str_replace(' ','',$iban));
		$Countries 	= array('al'=>28,'ad'=>24,'at'=>20,'az'=>28,'bh'=>22,'be'=>16,'ba'=>20,'br'=>29,'bg'=>22,'cr'=>21,'hr'=>21,'cy'=>28,'cz'=>24,'dk'=>18,'do'=>28,'ee'=>20,'fo'=>18,'fi'=>18,'fr'=>27,'ge'=>22,'de'=>22,'gi'=>23,'gr'=>27,'gl'=>18,'gt'=>28,'hu'=>28,'is'=>26,'ie'=>22,'il'=>23,'it'=>27,'jo'=>30,'kz'=>20,'kw'=>30,'lv'=>21,'lb'=>28,'li'=>21,'lt'=>20,'lu'=>20,'mk'=>19,'mt'=>31,'mr'=>27,'mu'=>30,'mc'=>27,'md'=>24,'me'=>22,'nl'=>18,'no'=>15,'pk'=>24,'ps'=>29,'pl'=>28,'pt'=>25,'qa'=>29,'ro'=>24,'sm'=>27,'sa'=>24,'rs'=>22,'sk'=>24,'si'=>19,'es'=>24,'se'=>24,'ch'=>21,'tn'=>24,'tr'=>26,'ae'=>23,'gb'=>22,'vg'=>24);
		$Chars 		= array('a'=>10,'b'=>11,'c'=>12,'d'=>13,'e'=>14,'f'=>15,'g'=>16,'h'=>17,'i'=>18,'j'=>19,'k'=>20,'l'=>21,'m'=>22,'n'=>23,'o'=>24,'p'=>25,'q'=>26,'r'=>27,'s'=>28,'t'=>29,'u'=>30,'v'=>31,'w'=>32,'x'=>33,'y'=>34,'z'=>35);
		
		if(strlen($iban) == $Countries[substr($iban,0,2)]){
			
			$MovedChar = substr($iban, 4).substr($iban,0,4);
			$MovedCharArray = str_split($MovedChar);
			$NewString = "";
			
			foreach($MovedCharArray AS $key => $value)
			{
				if(!is_numeric($MovedCharArray[$key]))
				{
					$MovedCharArray[$key] = $Chars[$MovedCharArray[$key]];
				}
				$NewString .= $MovedCharArray[$key];
			}
			
			if(bcmod($NewString, '97') == 1)
			{
				return true;
			}
		}
		return false;
	}
}


// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
// ..:: ENCRYPT & DECRYPT
// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
if ( ! function_exists('encrypt_string'))
{
	/**
	 * Encrypt a string using codeigniter encryption library. 
	 * Settings are stored in the config
	 * 
	 * @author Marco Eberhardt
	 * @category helper
	 * @package application\helpers\base_helper
	 * @version 1.0
	 * 
	 * @param string $plain_text 	>> The input string
	 * @param bool $base64			>> run safe_b64encode on the encrypted text 
	 * @param bool $url_encoded		>> run urlencode on the encrypted (and maybe base64 encoded) cyphertext
	 * 
	 * @return string 				>> ciphertext
	 */
	function encrypt_string($plain_text, $base64=true, $url_encoded=true)
	{
		$ci =& get_instance();
		$ci->load->library('encryption');
		$ci->encryption->initialize( $ci->config->item('encryption_settings') ); 
		
		$ciphertext 	= $ci->encryption->encrypt($plain_text);
		$return 		= $ciphertext;
		
		if ($base64 === true){
			$return = safe_b64encode($ciphertext);
		}
		
		if ($url_encoded === true){
			$return = urlencode($return);
		}
		
		return $return;
	}
}

if ( ! function_exists('decrypt_string'))
{
	/**
	 * Decrypt a cyphertext and returns the plain text using codeigniter encryption library. It's the opposite method to encrypt string
	 * Encryption settings are taken from the config file
	 * 
	 * @author Marco Eberhardt
	 * @category helper
	 * @package application\helpers\base_helper
	 * @version 1.0
	 *
	 * @param string $ciphertext	>> the input string
	 * @param bool $base64			>> run also safe_b64decode
	 * @param bool $url_encoded		>> run urldecode on the decrypted text
	 *
	 * @return string 				>> plain text
	 */
	function decrypt_string($ciphertext, $base64=true, $url_encoded=true)
	{
		$ci =& get_instance();
		$ci->load->library('encryption');
		$ci->encryption->initialize( $ci->config->item('encryption_settings') );

		$return = $ciphertext;

		if ($url_encoded === true){
			$return = urldecode($ciphertext);
		}

		if ($base64 === true){
			$return = safe_b64decode($return);
		}

		$return = $ci->encryption->decrypt($return);

		return $return;
	}
}

if ( ! function_exists('encrypt_get'))
{
	/**
	 * Takes a key-value array and creates an encrypted GET-Parameter string
	 * 
	 * @author Marco Eberhardt
	 * @category helper
	 * @package application\helpers\base_helper
	 * @version 1.0
	 *
	 * @param string $parameters >> key-value array ( array("edit_id"=>123, "test"=>44) ) ...
	 * @param bool $alsoEncryptKeys >> if set to true, the keys will also encrypted
	 * 
	 * @return string
	 */
	function encrypt_get($parameters, $alsoEncryptKeys=true)
	{
		if (is_scalar($parameters)){
			throw new Exception("Scalar values not supported");
		}

		$params = "";
		foreach ($parameters as $key => $value)
		{
			if ($alsoEncryptKeys)
			{
				$params .= urldecode(encrypt_string($key)) . '=' . urlencode(encrypt_string($value)) . '&';
			}
			else{
				$params .= $key.'='.encrypt_string($value).'&';
			}
		}
			
		if($params != ""){
			$params = substr($params, 0, -1);
		}

		return $params;
	}
}

if ( ! function_exists('decrypt_get'))
{
	/**
	 * Takes the encrypted GET (from $this->input->get() ) and return a plain text array
	 * Oposite method to encrypt_get
	 * 
	 * @author Marco Eberhardt
	 * @category helper
	 * @package application\helpers\base_helper
	 * @version 1.0
	 *
	 * @param string $params 		>> encrypted GET-String
	 * @param bool $alsoDecryptKeys >> if set to true, the keys are expected to be encrypted too
	 * @throws Exception			>> when input string is scalar value
	 * 
	 * @return array
	 */
	function decrypt_get($params, $alsoDecryptKeys=true)
	{
		if (is_scalar($parameters)){
			throw new Exception("Scalar values not supported");
		}

		$result = array();
		foreach ($params as $key => $value)
		{
			if ($alsoDecryptKeys)
			{
				$DecKey 			= decrypt_string($key);
				$result["$DecKey"] 	= decrypt_string(urldecode($value));
			}
			else {
				$result["$key"] = decrypt_string(($value));
			}
		}
		return $result;
	}
}

// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
// ..:: STRING MANIPULATION
// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
if ( ! function_exists('append_to_filename'))
{
	/**
	 * this function appends some text after the filename (before the extension)
	 *
	 * @author Marco Eberhardt
	 * @category helper
	 * @package application\helpers\base_helper
	 * @version 1.0
	 *
	 * @param string $file
	 * @param string $string
	 *
	 * @return string
	 */
	function append_to_filename($file, $string)
	{
		$extension_pos 	= strrpos($file, '.'); 		// find position of the last dot, so where the extension starts
		$return 		= substr($file, 0, $extension_pos) . $string . substr($file, $extension_pos);
		return $return;
	}
}

if ( ! function_exists('escapestring'))
{
	/**
	 * classic escape method using stripslashes, mysql_real_escape_string and finaly trim on the input string 
	 * 
	 * @author Marco Eberhardt
	 * @category helper
	 * @package application\helpers\base_helper
	 * @version 1.0
	 * 
	 * @param string $string
	 * @return string
	 */
	function escapestring($string){
		return trim(mysql_real_escape_string(stripslashes($string)));
	}
}

if ( ! function_exists('removetags'))
{
	/**
	 * classic removetags function converts angle brackets, single and double apostrophe with its html entity
	 *
	 * @author Markus Günther
	 * @category helper
	 * @package application\helpers\base_helper
	 * @version 1.0
	 *
	 * @param string $string
	 * 
	 * @return string
	 */
	function removetags($string)
	{
		$string = str_replace("<","&lt;",$string);
		$string = str_replace(">","&gt;",$string);
		$string = str_replace("'","&apos;",$string);
		$string = str_replace('"',"&quot;",$string);
		return $string;
	}
}

// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
// ..:: IMAGE MANIPULATION
// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
if ( ! function_exists('image2base64'))
{
	/**
	 * Convert image file to a base64 encoded string
	 * 
	 * @author Marco Eberhardt
	 * @category helper
	 * @package application\helpers\base_helper
	 * @version 1.0
	 * @throws Exception 			>> when we cant get the file contents 
	 * 
	 * @param string $img_path 		>> absolute, local path 
	 * @param bool $as_img_source 	>> not implemented yet
	 * 
	 * @return string 				>> base64 string
	 */
	function image2base64($img_path, $as_img_source=false)
	{
		$data = @file_get_contents($img_path);
		if ($data !== false)
		{
			$type 	= substr( strrchr($img_path, "."), 1);
			$base64 = 'data:image/'. $type.';base64,'.base64_encode($data);
			return $base64;
		}else{
			throw new Exception("could not get file contents");
		}
	}
}

?>