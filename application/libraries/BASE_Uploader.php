<?php
/**
 * Upload library
 * dont forget to chmod(644) for the upload folder (No execution)  
 * 
 * @author _BA5E
 * @category library
 * @package application\libraries\Upload
 * @version 1.2
 * 
 *  now with mime type validation. mime types are from codeigniters "mime.php" which has been merged with the mime types from "apache.org"
 * 	@see http://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types
 */
require_once 'BASE_Mime.php';
class BASE_Uploader 
{
	const DEBUG_FILENAME = "BASE_Uploader.log";
	
    /**
     * filename of the file you want to upload to
     * @var string
     */
    var $FileName;
    
    /**
     * temporary filename of the file you want to upload
     * @var string
     */
    var $TempFileName;
    
    /**
     * the upload directory
     * @var string 
     */
    var $UploadDirectory;
    
    /**
     * array containing allowed file extensions
     * @var array 
     */
    var $ValidExtensions;
    
    /**
     * message store
     * @var string
     */
    var $Message;
    
    /**
     * maximum allowed filesize in bytes 
     * @var integer
     */
    var $MaximumFileSize;
    
    /**
     * if true, the file will be validated to be an image type
     * @var bool
     */
    var $IsImage;
    
    /**
     * recipient adress for mail notifications
     * @var string
     */
    var $Email;
    
    /**
     * maximum allowed with, if file is an image
     * @var integer
     */
    var $MaximumWidth;
    
    /**
     * maximum allowed height, if file is an image
     * @var integer
     */
    var $MaximumHeight;

    /**
     * it, recommended to always create your own filenames (set it to true)
     * @var bool
     */
    var $renameFiles;
    
    
    /**
     * constructor for the BASE_Uploader library
     */
    public function __construct()
    {
    	$this->renameFiles = true;
    	
    	write2Debugfile(self::DEBUG_FILENAME, "Uploader initialized..", false);
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
     * check if the has a allowed extension
     * 
     * @return bool >> true=Datei erlaubt / false=Datei nicht erlaubt
     */
    function ValidateExtension()
    {
        $FileName 			= trim($this->FileName);
        $FileParts 			= pathinfo($FileName);
        $Extension 			= strtolower($FileParts['extension']);
        $ValidExtensions 	= $this->ValidExtensions;

        write2Debugfile(self::DEBUG_FILENAME, " - ValidateExtension => filename: ".$FileName." => Extension:".$Extension."\n - Valid Extensions:\n".print_r($ValidExtensions, true));
        
        if (!$FileName) {
            $this->SetMessage(lang("error").": ".lang("filename_not_set"));
            return false;
        }

        if (!$ValidExtensions) {
            $this->SetMessage(lang("warning").": ".lang("all_filetypes_allowed"));
            return false;
        }

        if (in_array($Extension, $ValidExtensions) === true) {
            $this->SetMessage($Extension.": ".lang("extension_is_valid"));
            write2Debugfile(self::DEBUG_FILENAME, " - Extension '$Extension' allowed for upload");
            return true;
        } 
        else {
            $this->SetMessage($Extension.": ".lang("extension_is_invalid"));
            return false;  
        }
    }

    /**
     * returns whether the file size is acceptable.

     * @return bool
     */
    function ValidateSize()
    {
        $MaximumFileSize 	= $this->MaximumFileSize;
        $TempFileName 		= $this->GetTempName();
        $TempFileSize 		= filesize($TempFileName);

        if($MaximumFileSize == "") {
            $this->SetMessage(lang("warning").": ".lang("no_file_size_limit_set"));
            return true;
        }

        if ($MaximumFileSize <= $TempFileSize) {
            $this->SetMessage(lang("error").": ".lang("file_size_limit_exceeded")."\n".lang("max_file_size").":".$MaximumFileSize." Bytes\n".lang("file_size").": ".$TempFileSize." Bytes");
            return false;
        }

        $this->SetMessage(lang("message").": ".lang("file_size_valid"));
        return true;
    }

    /**
     * check if the file already exists. change filename till its unique in the upload folder.
     * 
     * @return bool
     */
    function ValidateExistance()
    {
        $FileName 			= $this->FileName;
        $UploadDirectory 	= $this->UploadDirectory;
        $File 				= $UploadDirectory . $FileName;

        if (file_exists($File)) 
        {
            $this->SetMessage(lang("message").": ".sprintf(lang("file_x_already_exists"), $FileName));

            $UniqueName = rand()."_".$FileName;
            $this->SetFileName($UniqueName);
            $this->ValidateExistance();
        } 
        else {
            $this->SetMessage(lang("message").": ".sprintf(lang("file_x_does_not_exist"), $FileName));
            return true;
        }
    }

    /**
     * 
     * check if the upload directory exists and is writeable and has a trailing slash 
     * 
     * @return bool
     */
    function ValidateDirectory()
    {
        $UploadDirectory = $this->UploadDirectory;

        if (!$UploadDirectory) {
            $this->SetMessage(lang("error").": ".lang("no_upload_directory_set"));
            return false;
        }

        if (!is_dir($UploadDirectory)) {
            $this->SetMessage(lang("error").": $UploadDirectory ".lang("directory_does_not_exist"));
            return false;
        }

        if (!is_writable($UploadDirectory)) {
            $this->SetMessage(lang("error").": ".lang("directory_not_writable"));
            return false;
        }

        if (substr($UploadDirectory, -1) != "/") {
            $this->SetMessage(lang("error").": ".lang("trailing_slash_appended"));
            $NewDirectory = $UploadDirectory . "/";
            $this->SetUploadDirectory($NewDirectory);
            $this->ValidateDirectory();
        } 
        else {
            $this->SetMessage(lang("message").": ".lang("trailing_slash_found"));
            return true;
        }
    }

    /**
     * check image dimensions
     */
    function ValidateImage() 
    {
        $MaximumWidth 	= $this->MaximumWidth;
        $MaximumHeight 	= $this->MaximumHeight;
        $TempFileName 	= $this->TempFileName;

	    if($Size = @getimagesize($TempFileName)) {
	        $Width 	= $Size[0];	
	        $Height = $Size[1]; 
	    }

        if (!is_null($MaximumWidth) && $Width > $MaximumWidth) 
        {
            $this->SetMessage(lang("error").": ".sprintf(lang("maximum_image_width_exceeded"), $Width, $MaximumWidth) );
            return false;
        }

        if (!is_null($MaximumHeight) && $Height > $MaximumHeight) {
            $this->SetMessage(lang("error").": ".sprintf(lang("maximum_image_height_exceeded"), $Height, $MaximumHeight));
            return false;
        }

        $this->SetMessage(lang("message").": ".lang("image_dimensions_within_alowed_range"));     
        return true;
    }
    
    /**
     * validate the mimetype of the 
     * requires finfo_open 
     * 
     * @return boolean
     */
    function ValidateMimitype()
    {
    	$finfo = @finfo_open(FILEINFO_MIME_TYPE);
    	if($finfo === false){
    		$this->SetMessage(lang("error").": ".lang("mime_type_database_not_found"));
    		return false;
    	}
    	
    	$x = explode('.', $this->FileName);
    	if (count($x) === 1){
    		$this->SetMessage(lang("error").": ".lang("could_not_detect_file_extension"));
    		return false;
    	}
    	
    	$FileName 			= trim($this->TempFileName);
    	//$FileParts 			= pathinfo($FileName);
    	$Extension 			= ($this->FileName) ? strtolower(end($x)) : end($x);
    	$fileinfo 			= @finfo_file($finfo, $FileName);
    	$allowedMimtypes 	= BASE_Mime::getMimetypeByExtension($Extension);
    	
    	write2Debugfile(self::DEBUG_FILENAME, " - ValidateMimitype => filename: ".$FileName." => Extension:".$Extension." => mimetype:".$fileinfo."\n - Allowed Types:\n".print_r($allowedMimtypes, true));
    	 
    	if($fileinfo === false)
    	{
    		$this->SetMessage(lang("error").": ".lang("could_not_detect_file_type"));
    		return false;
    	}
    	
    	if($allowedMimtypes && !in_array(strtolower($fileinfo), $allowedMimtypes))
    	{
    		$these = implode(', ', $allowedMimtypes);
    		$this->SetMessage(lang("error").": ".sprintf(lang("for_the_extension_x_only_the_following_filetypes_are_allowed_x"), $Extension, $these));
    		return false;
    	}
    	
    	write2Debugfile(self::DEBUG_FILENAME, " - detected mimetype '$fileinfo' is valid for type '$Extension'");
    	$this->SetMessage(lang("message").": ".lang("file_type_is_valid"));
    	return true;
    }
    


    /**
     * upload the file after all validations have been passed successfully. 
     * return array with possible errors and the filename
     * 
     * @return array >> array("file", "filepath", "error", "mail_sent");
     */
    function UploadFile()
    {
    	if ($this->renameFiles === true)
    	{
    		$x 			= explode('.', $this->FileName);
    		$Extension 	= ($this->FileName) ? strtolower(end($x)) : end($x);
    		$FileName 	= $this->generate_uuid().".".$Extension;
    		
    		$this->FileName = $FileName;
    	}
    	
    	$status = E_STATUS_CODE::ERROR;
    	$return = array("error"=>"", "file"=>"");
    	
    	if (!$this->ValidateExtension()) {
        	$return["error"] = ($this->GetMessage());
        }
        else if (!$this->ValidateMimitype()) {
        	$return["error"] = ($this->GetMessage());
        } 
        else if (!$this->ValidateSize()) {
            $return["error"] = ($this->GetMessage());
        }
        else if (!$this->ValidateExistance()) {
            $return["error"] = ($this->GetMessage());
        }
        else if (!$this->ValidateDirectory()) {
            $return["error"] = ($this->GetMessage());
        }
        else if ($this->IsImage && !$this->ValidateImage()) {
            $return["error"] = ($this->GetMessage());
        }
        else 
        {
            $FileName 			= $this->FileName;
            $x 					= explode('.', $this->FileName);
    		$Extension 			= ($this->FileName) ? strtolower(end($x)) : end($x);
    		$TempFileName 		= $this->TempFileName;
            $UploadDirectory 	= $this->UploadDirectory;

            write2Debugfile(self::DEBUG_FILENAME, "\nvalidation passed upld-dir[".$this->UploadDirectory."] filename[".$this->FileName."]");


            if (is_uploaded_file($TempFileName)) 
            { 
            	move_uploaded_file($TempFileName, $UploadDirectory . $FileName);
            	
            	$mail_sent = $this->SendMail($FileName);
                
            	$return["error"] 		= "";
            	$return["file"] 		= $FileName;
            	$return["filepath"]		= realpath($UploadDirectory . $FileName);
            	$return["mail_sent"]	= $mail_sent;
            	
            	$status = E_STATUS_CODE::SUCCESS;
            } 
            else 
            {
            	$return["error"] 		= lang("upload_error");
            	$return["file"] 		= "";
            	$return["filepath"]		= "";
            	$return["mail_sent"]	= 0;
            }
        }
        $result = new BASE_Result($return, $return["error"], array(), $status);
        
        write2Debugfile(self::DEBUG_FILENAME, "\nUpload [".realpath($UploadDirectory . $FileName)."]-".print_r($result, true));
        
        return $result;
    }

    /**
     * send notification mail (if Email is set) after the upload
     * 
     * @return bool >> true=Mail sent / false=Mail not sent
     */
    function SendMail($filename) 
    {
    	if ($this->Email == "")
    	{
    		return true;
    	}
    	
    	return true;
    	
    	/*

    	$mails		= array(
    		"marco.eberhardt@codenground.com"
    	);	// Additional addresses
		
		$msg = "Sehr geehrte Administratoren, <br/><br/>";
		$msg .= "es wurde eine neue Datei (<b>".$filename."</b>) hochgeladen.";
    	$msg .= "<br/><br/><hr/>Dies ist eine automatisch generierte Mail. Bitte Anworten Sie nicht darauf!";
    	
    	
    	require_once 'PHPMailer/class.phpmailer.php';
        $mail = new PHPMailer;
	  	$mail->From = 'base@codenground.de';
	  	$mail->FromName = 'BA5E | Upload notifier';
		
	  	$mail->AddAddress($this->Email,"codenground.de");
	  	foreach($mails as $email)
	  	{
	  		$mail->AddAddress($email,"codenground.de");
	  	} 
	  	
		$mail->Subject = "Datei-Upload durchgeführt";
		$mail->IsHTML(true);
		$mail->Body = nl2br($msg);
	    $send = $mail->Send();
        
        return $send;
        */
    }
    
    
    // ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
    // ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
    // ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
    /**
     * Setter for the filename
     * @param string $argv
     */
    function SetFileName($argv)
    {
        $this->FileName = $argv;
        
        write2Debugfile(self::DEBUG_FILENAME, "- Filename: ".$this->FileName, true);
    }

    /**
     * setter for the upload directory
     * @param string $argv
     */
    function SetUploadDirectory($argv)
    {
        $this->UploadDirectory = $argv;
    }

    /**
     * setter for the temp filename
     * @param string $argv
     */
    function SetTempName($argv)
    {
        $this->TempFileName = $argv;
    }

    /**
     * setter for the allowed extensions 
     * @param string $argv
     */
    function SetValidExtensions($argv)
    {
        $this->ValidExtensions = $argv;
    }

    /**
     * setter for the messages 
     * @param string $argv
     */
    function SetMessage($argv)
    {
        $this->Message = $argv;
    }

    /**
     * setter for the maximum filesize
     * @param string $argv
     */
    function SetMaximumFileSize($argv)
    {
        $this->MaximumFileSize = $argv;
    }

    /**
     * setter for the email recipient
     * @param string $argv
     */
    function SetEmail($argv)
    {
        $this->Email = $argv;
    }
   
    /**
     * setter for isImage
     * @param string $argv
     */
    function SetIsImage($argv)
    {
        $this->IsImage = $argv;
    }

    /**
     * setter for the maximum width
     * @param string $argv
     */
    function SetMaximumWidth($argv)
    {
        $this->MaximumWidth = $argv;
    }

    /**
     * setter for the maximum height
     * @param string $argv
     */
    function SetMaximumHeight($argv)
    {
        $this->MaximumHeight = $argv;
    }   
    
    /**
     * getter for the filename
     * @return string
     */
    function GetFileName()
    {
        return $this->FileName;
    }

    /**
     * getter for the upload directory
     * @return string
     */
    function GetUploadDirectory()
    {
        return $this->UploadDirectory;
    }

    /**
     * getter for the temp-filename
     * @return string
     */
    function GetTempName()
    {
        return $this->TempFileName;
    }

    /**
     * getter for the allowed extensions
     * @return string
     */
    function GetValidExtensions()
    {
        return $this->ValidExtensions;
    }

    /**
     * getter for the message
     * @return string
     */
    function GetMessage()
    {
        if (!isset($this->Message)) {
            $this->SetMessage(lang("no_messages"));
        }

        return $this->Message;
    }
    
    /**
     * getter for the max filesize
     * @return string
     */
    function GetMaximumFileSize()
    {
        return $this->MaximumFileSize;
    }

    /**
     * getter for the Email
     * @return string
     */
    function GetEmail()
    {
        return $this->Email;
    }

    /**
     * getter for isImage
     * @return string
     */
    function GetIsImage()
    {
        return $this->IsImage;
    }

    /**
     * getter for the max width
     * @return string
     */
    function GetMaximumWidth()
    {
        return $this->MaximumWidth;
    }

    /**
     * getter for the max height
     * @return string
     */
    function GetMaximumHeight()
    {
        return $this->MaximumHeight;
    }
}


?>