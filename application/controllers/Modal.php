<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * AJAX-Controller for the dialogs.js
 * 
 * @author Marco Eberhardt
 * @category Controller
 * @package application\controllers
 * @version 1.0
 */	
class Modal extends BASE_Controller
{
	const DEBUG_FILENAME = "modal.log";
	
	/**
	 * Constructor for the modal controller
	 */
	function __construct()
	{
		parent::__construct(false);
		
		$this->setData( new BASE_Result());
		write2Debugfile(self::DEBUG_FILENAME, "modal-controller", false);
	}

	/**
	 * Ajax method for the dialogs.js to build a Modal dialog via the HTML_Helper 
	 * and send it back as json_encoded <code>BASE_Result</code>
	 */
	function generic()
	{
		$post = $this->input->post();
		
		if (isset($post) && is_array($post))
		{
			write2Debugfile(self::DEBUG_FILENAME, "create a simple modal params-".print_r($post, true));
			
			$modal = new HTML_Dialog(
				$this->input->post('id_modal'), 
				$this->input->post('id_modal'), 
				$this->input->post('title'), 
				$this->input->post('content'), 
				$this->input->post('footer'), 
				$this->input->post('color')
			);
			
			
			$modal->setDataBackdrop($this->input->post('backdrop'));
			
			if ($this->input->post('txt_no') != ""){
				$modal->addFooterButtonCancel($this->input->post('txt_no'));
			}
			if ($this->input->post('txt_yes') != ""){
				$modal->addFooterButtonOK($this->input->post('txt_yes'));
			}
			
			write2Debugfile(self::DEBUG_FILENAME, "HTML\n".$modal->generateHTML());
			
			echo json_encode(new BASE_Result($modal->generateHTML(), null, null, E_STATUS_CODE::SUCCESS));
		}
		else 
		{
			echo json_encode(new BASE_Result(null, "Missing parameter", null, E_STATUS_CODE::ERROR));
		}
	}
}
?>