<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * BASE_Updater - Provides the update functionality 
 * 
 * @author Marco Eberhardt
 * @category Library
 * @package application\libraries\BASE_Updater
 * @version 1.0
 */
class BASE_Updater
{
	/**
	 * contains the codeigniter instance
	 * @var string
	 */
	private $ci;
	
	/**
	 * Constructor for the BASE_Updater
	 *
	 * @return BASE_Updater
	 */
	public function __construct()
	{
		$this->ci &= get_instance();
		
		return $this;
	}
	
	
	/**
	 * Get the current version from the master branch (or any other)
	 *
	 * @param $branch
	 * @return bool|mixed|string
	 */
	static function getCurrentVersion($branch="latest_stable")
	{
		$url = "svn://localhost/ci_base_core/branches/".$branch."/.version";
		//file_put_contents("../logs/index.getCurrentVersion.branch_url.txt", print_r($url, true));
		$out = false;
	
		if (function_exists('curl_init'))
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_USERAGENT, 'BA5E');
			$out = curl_exec($ch);
	
			if (false === $out)
			{
				trigger_error('Not sure what it is, but there\'s a problem with contacting the update server. Maybe this will help: "' . curl_error($ch) . '"');
			}
			echo curl_error($ch);
			curl_close($ch);
		}else{
			throw new Exception("curl is required for this action");
		}
	
		
		return ($out !== false) ? $out : VERSION;
	}

}

?>