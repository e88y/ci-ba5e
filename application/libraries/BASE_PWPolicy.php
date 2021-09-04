<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');  
 
/**
 * Password policy class to enforce password formatting policy
 *
 * @author Marco Eberhardt (fork)
 * 
 * @category library
 * @package application\libraries\BASE_Password_policy
 * @version 1.0
 */
class BASE_PWPolicy
{
	const DEBUG_FILENAME = "BASE_PWPolicy.log";
	
	/**
	 * codeigniter instance
	 * @var object
	 */
	protected $ci;
	
	private $rules;
	private $errors;
	
	/**
	 * Constructor
     *
     * Allows an array of policy parameters to be passed on construction.
     * For any rules not listed in parameter array default values are set.
     *
     * @param  array $params optional array of policy configuration parameters
	 */
	public function __construct($params) 
    {
        $this->ci =& get_instance();
        /**
         * Basic rule definitions
	     *  - Key is rule identifier
	     *  - Value is rule parameter (false => disabled) 
	     *  - Type is type of parameter data (permitted values are 'integer' or 'boolean')
	     *  - Test is php code condition returning true if rule is passed (e.g. password string is $pass / rule value is $value)
	     *  - Error is rule string definition (use #VALUE# placeholder to insert value)
         */
        $this->rules[E_PW_POLICY_RULES::MIN_LENGTH] = array(
        	'value' => false,
        	'type'  => 'integer',
        	'test'  => 'return strlen($pass) >= $value;',
        	'error' => lang("pw_policy_min_length") 							// 'Password must be more than #VALUE# characters long'
        );
        
        $this->rules[E_PW_POLICY_RULES::MAX_LENGTH] = array(
        	'value' => false,
        	'type'  => 'integer',
        	'test'  => 'return (strlen($pass) <= $value);',
        	'error' => lang("pw_policy_max_length")								// 'Password must be less than #VALUE# characters long'
        );
        
        $this->rules[E_PW_POLICY_RULES::MIN_LOWERCASE_CHARS] = array(
        	'value' => false,
        	'type'  => 'integer',
        	'test'  => 'return preg_match_all("/[a-z]/",$pass,$x) >= $value;',
        	'error' => lang("pw_policy_min_lowercase_chars")					// 'Password must contain at least #VALUE# lowercase characters'
        );
        
        $this->rules[E_PW_POLICY_RULES::MAX_LOWERCASE_CHARS] = array(
        	'value' => false,
        	'type'  => 'integer',
        	'test'  => 'return preg_match_all("/[a-z]/",$pass,$x) <= $value;',
        	'error' => lang("pw_policy_max_lowercase_chars")					// 'Password must contain no more than #VALUE# lowercase characters'
        );
        
        $this->rules[E_PW_POLICY_RULES::MIN_UPPERCASE_CHARS] = array(
        	'value' => false,
        	'type'  => 'integer',
        	'test'  => 'return preg_match_all("/[A-Z]/",$pass,$x) >= $value;',
        	'error' => lang("pw_policy_min_uppercase_chars")					// 'Password must contain at least #VALUE# uppercase characters'
        );
        
        $this->rules[E_PW_POLICY_RULES::MAX_UPPERCASE_CHARS] = array(
        	'value' => false,
        	'type'  => 'integer',
        	'test'  => 'return preg_match_all("/[A-Z]/",$pass,$x) <= $value;',
        	'error' => lang("pw_policy_max_uppercase_chars")					// 'Password must contain no more than #VALUE# uppercase characters'
        );
        
        $this->rules[E_PW_POLICY_RULES::DISALLOW_NUMERIC_CHARS] = array(
        	'value' => false,
        	'type'  => 'boolean',
        	'test'  => 'return preg_match_all("/[0-9]/",$pass,$x) == 0;',
        	'error' => lang("pw_policy_disallow_numeric_chars")					// 'Password may not contain numbers'
        );
        
        $this->rules[E_PW_POLICY_RULES::DISALLOW_NUMERIC_FIRST] = array(
        	'value' => false,
        	'type'  => 'boolean',
        	'test'  => 'return preg_match_all("/^[0-9]/",$pass,$x) == 0;',
        	'error' => lang("pw_policy_disallow_numeric_first")					// 'First character cannot be numeric'
        );
        
        $this->rules[E_PW_POLICY_RULES::DISALLOW_NUMERIC_LAST] = array(
        	'value' => false,
        	'type'  => 'boolean',
        	'test'  => 'return preg_match_all("/[0-9]$/",$pass,$x) == 0;',
        	'error' => lang("pw_policy_disallow_numeric_last")					// 'Last character cannot be numeric'
        );
        
        $this->rules[E_PW_POLICY_RULES::MIN_NUMERIC_CHARS] = array(
        	'value' => false,
        	'type'  => 'integer',
        	'test'  => 'return preg_match_all("/[0-9]/",$pass,$x) >= $value;',
        	'error' => lang("pw_policy_min_numeric_chars")						// 'Password must contain at least #VALUE# numbers'
        );
        
        $this->rules[E_PW_POLICY_RULES::MAX_NUMERIC_CHARS] = array(
        	'value' => false,
        	'type'  => 'integer',
        	'test'  => 'return preg_match_all("/[0-9]/",$pass,$x) <= $value;',
        	'error' => lang("pw_policy_max_numeric_chars")						// 'Password must contain no more than #VALUE# numbers'
        );
        
        $this->rules[E_PW_POLICY_RULES::DISALLOW_NONALPHANUMERIC_CHARS] = array(
        	'value' => false,
        	'type'  => 'boolean',
        	'test'  => 'return preg_match_all("/[\W]/",$pass,$x) == 0;',
        	'error' => lang("pw_policy_disallow_nonalphanumeric_chars")			// 'Password may not contain non-alphanumeric characters'
        );
        
        $this->rules[E_PW_POLICY_RULES::DISALLOW_NONALPHANUMERIC_FIRST] = array(
        	'value' => false,
        	'type'  => 'boolean',
        	'test'  => 'return preg_match_all("/^[\W]/",$pass,$x) == 0;',
        	'error' => lang("pw_policy_disallow_nonalphanumeric_first") 		// 'First character cannot be non-alphanumeric'
        );
        
        $this->rules[E_PW_POLICY_RULES::DISALLOW_NONALPHANUMERIC_LAST] = array(
        	'value' => false,
        	'type'  => 'boolean',
        	'test'  => 'return preg_match_all("/[\W]$/",$pass,$x) == 0;',
        	'error' => lang("pw_policy_disallow_nonalphanumeric_last")			// 'Last character cannot be non-alphanumeric'
        );
        
        $this->rules[E_PW_POLICY_RULES::MIN_NONALPHANUMERIC_CHARS] = array(
        	'value' => false,
        	'type'  => 'integer',
        	'test'  => 'return preg_match_all("/[\W]/",$pass,$x) >= $value;',
        	'error' => lang("pw_policy_min_nonalphanumeric_chars")				// 'Password must contain at least #VALUE# non-aplhanumeric characters'
        );
        
        $this->rules[E_PW_POLICY_RULES::MAX_NONALPHANUMERIC_CHARS] = array(
        	'value' => false,
        	'type'  => 'integer',
        	'test'  => 'return preg_match_all("/[\W]/",$pass,$x) <= $value;',
        	'error' => lang("pw_policy_max_nonalphanumeric_chars")				// 'Password must contain no more than #VALUE# non-alphanumeric characters'
        );
        
        $this->rules[E_PW_POLICY_RULES::DISALLOW_PREVIOUS_PASSWORDS] = array(
        	'value' => false,
        	'type'  => 'array',
        	'test'  => 'return in_array($pass, $value);',
        	'error' => lang("pw_policy_disallow_previous_passwords")			// Previously used passwords are not allowed to reuse
        );
        
        $this->rules[E_PW_POLICY_RULES::DISALLOW_BLACKLISTED_PASSWORDS] = array(
        	'value' => false,
        	'type'  => 'array',
        	'test'  => 'return preg_match_all("/".implode("|", $value)."/i",$pass,$x) <= 0;', 
        	'error' => lang("pw_policy_disallow_blacklisted_passwords")			// Previously used passwords are not allowed to reuse
        );
        
        // Apply params from constructor array
        foreach($params as $rule=>$value) 
        { 
        	$this->$rule = $value; 
        }
        
        // Errors defaults empty
        $this->errors = array();
        
        return 1;
    }
    
    /**
     * Get a rule configuration parameter
     *
     * @param  string $rule 	>> Identifier for a rule
     * @return mixed        	>> Rule configuration parameter
     */
    public function __get($rule)
    {
    	if( isset($this->rules[$rule]) ){
    		return $this->rules[$rule]['value'];
    	}
    	return false;
    }
    
    /**
     * Set a rule configuration parameter
     *
     * @param  string $rule  	>> Identifier for a rule
     * @param  string $value 	>> Parameter for rule
     * @return boolean       	>> 1 on success | 0 otherwise
     */
    public function __set($rule, $value)
    {
    	if(isset($this->rules[$rule]))
    	{
    		if( 'integer' == $this->rules[$rule]['type'] && is_int($value) ){
    			return $this->rules[$rule]['value'] = $value;
    		}
    			
    		if( 'boolean' == $this->rules[$rule]['type'] && is_bool($value) ){
    			return $this->rules[$rule]['value'] = $value;
    		}
    		
    		if( 'array' == $this->rules[$rule]['type'] && is_array($value) ){
    			return $this->rules[$rule]['value'] = $value;
    		}
    	}
    	return false;
    }
    
    /**
     * Get the errors showing which rules were not matched on the last validation
     *
     * Returns array of strings where each element has a key that is the failed
     * rule identifier and a string value that is a human readable description
     * of the rule
     *
     * @return array	>> Array of descriptive strings
     */
    public function get_errors()
    {
    	return $this->errors;
    }
    
    /**
     * Get the error description for a rule
     *
     * @param  string $rule	>> Identifier for the rule to be applied
     * @return string		>> Error string for rule if it exists | false otherwise
     */
    private function get_rule_error($rule)
    {
    	return ( isset($this->rules[$rule]) && $this->rules[$rule]['value'] ) ? sprintf($this->rules[$rule]['error'], $this->rules[$rule]['value']) : false;
    }
    
    /**
     * Get human readable representation of policy rules
     *
     * Returns array of strings where each element is a string description of
     * the active rules in the policy
     *
     * @return array	>> Array of descriptive strings
     */
    public function policy()
    {
    	$return = array();
    	
    	// Itterate over policy rules
    	foreach( $this->rules as $k => $v )
    	{
    		// If rule is enabled, add string to array
    		$string = $this->get_rule_error($k);
    		if( $string ) {
    			$return[$k] = $string;
    		}
    	}
    	return $return;
    }
    
    /**
     * Validate a password against the policy
     *
     * @param  string $password 	>> The password string to validate
     * @return boolean				>> 1 if password conforms to policy | 0 otherwise
     */
    public function validate($password)
    {
    	// Aliases for password and rule value
    	$pass = $password;
    	
    	foreach($this->rules as $k => $rule)
    	{
    		$value 	= $rule['value'];
    		
    		if ($rule['value'] !== false)
    		{
    			// Apply each configured rule in turn
    			// If eval() is the answer, you're almost certainly asking the wrong question. -- Rasmus Lerdorf, BDFL of PHP
    			
    			// Note:
    			// The eval code here is save because the the command-string ($rule['test']) is constant and not dynamically generated by users input. 
    			// $pass is always a string when eval gets executed.  
    			
    			if( $rule['value'] && !eval($rule['test']) )
    			{
    				$this->errors[$k] = $this->get_rule_error($k);
    			}
    		}
    	}
    	return sizeof($this->errors) == 0;
    }
}
?>