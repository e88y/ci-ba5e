<?php
/**
 * Download library
 * 
 * @author _BA5E
 * @category library
 * @package application\libraries\BASE_Downloader
 * @version 1.2
 * 
 *  now with mime type validation. mime types are from codeigniters "mime.php" which has been merged with the mime types from "apache.org"
 * 	@see http://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types
 */
require_once 'BASE_Mime.php';

class BASE_Downloader 
{
	const DEBUG_FILENAME = "BASE_Downloader.log";

	private $ci;
	
	public function __construct()
	{
		$this->ci &= get_instance();
	}
	
    /**
     * generate a new uuid for use as filename
     * @return string
     */
    function generate_uuid() {
    	return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
    		mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
			mt_rand( 0, 0xffff ),
			mt_rand( 0, 0x0fff ) | 0x4000,
			mt_rand( 0, 0x3fff ) | 0x8000,
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    	);
    }
    
    /**
     * @author Marco Eberhardt
     * @version 1.0
     * 
     * @param string $file				>> full path
     * @param string $filename			>> download filename 
     * @param string $client_id			>> client identifier
     * @param boolean $force_download	>> use content type "application/force-download"
     */
    public static function download($file, $filename, $client_id, $force_download=false)
    {
    	$messages = array();
    	
    	$filename = html_entity_decode($filename);	
    	
    	if (!file_exists( $file ))
    	{  
    		$messages[] = lang("error_download_requested_file_not_found");
    	}
    	else
    	{
    		//$file_info		= pathinfo($file);
    		$file_mimetype 	= @finfo_file(@finfo_open(FILEINFO_MIME_TYPE), $file);
    		
    		if(self::isDownloadPathAllowed($file, $client_id) == false)
    		{
    			$messages[] = lang("error_download_no_access_to_download_directory");
    		}
    		if (BASE_Mime::validate_mimetype($file) == false)
    		{
    			$messages[] = lang("error_download_file_extension_does_not_match_mimetype");
    		}
    		
    		if (pathinfo($file, PATHINFO_EXTENSION) != BASE_Mime::getExtensionForFile($file))
    		{
    			$messages[] = lang("error_download_file_extension_suspisious");
    		}
    		
    		
    		if (count($messages) == 0)
    		{
    			header("Pragma: public"); // required
    			header("Expires: 0");
    			header("Content-Transfer-Encoding: binary");
    			header('Cache-Control: private, no-transform, no-store, must-revalidate, post-check=0, pre-check=0');
    			header("Content-Disposition: attachment; filename=\"".$filename."\";" );
    			header("Content-Length: ".filesize($file));
    			
    			if ($force_download == true){
    				header("Content-Type: application/force-download");
    			}
    			else{
    				header("Content-Type: $file_mimetype");
    			}
    			
    			readfile($file);
    			
    			exit();
    		}
    		
	    		
    	}
    }
    
    /**
     * 
     * @author Marco Eberhardt
     * @version 1.0
     * 
     * @param string $file >> filepath
     * @return boolean
     */
    function isDownloadPathAllowed($file, $client_id=null)
    {
    	$realpath 	= realpath(dirname($file));
    	if($realpath == false) {
    		return false;
    	}
    	
    	$allowed_paths = array(
    		array("path" => realpath(root_path()."/application/uploads/article_icons/".$client_id), "only_subfolders" => true),
    		array("path" => realpath(root_path."/docs/".$client_id), "only_subfolders" => true)
    	);
    	
    	foreach($allowed_paths as $path)
    	{
    		$pattern = '/^'.str_replace('\\','\\\\',$path['path']).($path['only_subfolders']?'.':'').'/i';
    		
    		if(preg_match($pattern, $realpath)){
    			return true;
    		}
    	}
    	return false;
    }
    
    /**
     * send notification mail (if Email is set) after the download
     * 
     * @return bool >> true=Mail sent / false=Mail not sent
     */
    function SendMail($filename) 
    {
    	if ($this->Email == ""){
    		return true;
    	}
    	
    	return true;		// DISABLED
    	
    	$mails		= array(
    		"marco.eberhardt@codenground.com"
    	);	// Additional addresses
		
		$msg = "Sehr geehrte Administratoren, <br/><br/>";
		$msg .= "es wurde soeben die eine Datei (<b>".$filename."</b>) herunter geladen .";
    	$msg .= "<br/><br/><hr/>Dies ist eine automatisch generierte Mail. Bitte Anworten Sie nicht darauf!";
    	
        $mail = new BASE_Mailer();
        $send = $mail->send_email("File-Download", nl2br($msg), $mails);
        
        return $send;
    }
}


?>