<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed');  
 
//require_once APPPATH."/third_party/PHPMailer-5.2.4/class.phpmailer.php";
/**
 * Mailing class
 *
 * @author _BA5E
 * @category library
 * @package application\libraries\BASE_Mailer
 * @since 1.2
 * @version 1.0
 */
class BASE_Mailer  
{
	const DEBUG_FILENAME = "BASE_Mailer.log";
	
	/**
	 * codeigniter instance
	 * @var object
	 */
	protected $ci;
	
	/**
	 * the template
	 * @var string
	 */
	protected $template;

    /**
     * user identifier
     * @var string
     */
    protected $user_id;

    /**
     * @var string
     */
    private $language;
	
	private $config ;
	/**
	 * constructor for the BASE_Mailer library
	 */
    public function __construct() 
    {
        $this->ci =& get_instance();
        $this->ci->load->library('parser');
        $this->ci->load->library('email');
        
        $this->language	= "DE";	// default
        
        $this->config['protocol'] = 'sendmail';
        $this->config['mailpath'] = '/usr/sbin/sendmail';
        $this->config['charset'] = 'utf-8';
        $this->config['wordwrap'] = TRUE;
        $this->config['mailtype'] = 'html';
    }

    public function setLanguage($lng){
        $this->language = $lng;
    }
    
    /**
     * validate variable using filter_var-method
     * @param string $email
     * @return mixed
     */
    public static function isEmail($email){
		return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
    
    /**
     * load and parse email template
     * 
     * @throws Exception when no template is specified or the provided template could not be found

     * @param string $template			>> template to load
     * @param string $recipient			>> 
     * @param array $replacements		>> key-value array("firstname"=>"mike").
     * @param array $attachments		>> array containing file attachments
     * @param string $message_override	>> if not null this string will be used for the message body instead of the one from the template
     */
    public function send_emailFromTemplate($template, $recipient="", $replacements=array(), $attachments=array(), $message_override=null)
    {
    	if ($template != "")
    	{
			$this->template = $template;    		
    		
    		// 1. load template and replace placeholders
    		$template_result = $this->ci->app_model->do_select(TBL_TEMPLATES_EMAIL, "*", array("template_name"=>$template, "locale_code"=>$this->language));
    		
    		if ($template_result->extra["num_rows"] > 0)
    		{
    			$template_content 	= $template_result->data[0];
    			
    			if ($message_override != null && $message_override != ""){
    				$parsed_txt 		= $this->ci->parser->parse_string($message_override, $replacements);
    			}else{
    				$parsed_txt 		= $this->ci->parser->parse_string($template_content->message, $replacements);
    			}
    			
    			$parsed_subject		= $this->ci->parser->parse_string($template_content->subject, $replacements);
    			
    			write2Debugfile(self::DEBUG_FILENAME, "\n\n\ntemplate_content:\n".print_r($template_content, true)."\n\nreplacements:\n".print_r($replacements, true), true);
    			
    			// 2. BASE_Mailer::send_email
    			$sent = self::send_email($parsed_subject, $parsed_txt, $recipient, $template_content->static_cc, $template_content->static_bcc, $attachments, $template);
    			return  $sent;
    		}
    		else{
    			throw new Exception("template $template not found<br>".$this->ci->app_model->lastQuery()."<br>".print_r($template_result));
    		}
    	}
		else{
			throw new Exception("no template specified");
		}
    }
    
    /**
     * Static function to send a E-Mail
     * will write a log entry to the database
     * 
     * @param string $subject	>> E-Mail subject
     * @param string $message	>> E-Mail message
     * @param mixed $to			>> email or array of emails 
     * @param mixed $cc			>> email or array of emails
     * @param mixed $bcc		>> email or array of emails
     * @param array $attachments>> files to attach
     * @param string $template	>> template for creating a log entry
     * 
     * 
     * @return bool
     */
    public static function send_email($subject, $message, $to, $cc=array(), $bcc=array(), $attachments=array(), $template=null )
    {
    	$ci =& get_instance();
    	$ci->config->load("email");
    	
    	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
    	// ..:: message
    	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
    	$ci->email->from($ci->config->item("email_from"));
    	//$ci->email->from_name($ci->config->item("email_from_name"));
    	$ci->email->reply_to($ci->config->item("email_from"));
    	$ci->email->subject($subject);
    	$ci->email->message($message);
    	
    	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
    	// ..:: recipient(s)
    	// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
   		$ci->email->to($to);
   		$ci->email->cc(explode(",", $cc));
   		$ci->email->bcc(explode(",", $bcc));
    	
   		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
   		// ..:: attachments(s)
   		// ..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::..
    	if (count($attachments) > 0){
    		foreach ($attachments as $file){
    			if (file_exists($file) === true){
    				$ci->email->attach($file);
    			}
    		}
    	}
    	 
    	$is_sent	= $ci->email->send(FALSE);
    	$mail_log 	= $ci->email->print_debugger();

    	// log email to database
    	$ci->db->insert(TBL_LOG_EMAIL, array(
    		"sent_at" => time(),
    		"recipient" => $to,
    		"template_name" => $template,
    		"initialized_by" => "",
    		"success" => $is_sent,
    		"debug" => $mail_log	
    			
    	));
    	
    	$ci->email->clear(true);
    	return $is_sent;
    }
}
