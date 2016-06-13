<?php
/**
* @package   
* @author    Shawn Gibbons
* @copyright Copyright (C) Shawn Gibbons
* @license   
*/

class Notification {

	protected $_object;

	protected $_attachment;

	protected $_method;

	protected $_metadata;

	public $app;

	public function __construct($app) {
		$this->app = $app;
		$this->_mail = JFactory::getMailer();
		$this->application = $this->app->zoo->getApplication();
		$this->_mail->SMTPDebug = 4;
	}

	/**
	 * Add the object of the notification
	 *
	 * @param 	object		Class object that contains the data reference for the notification.
	 *
	 * @return 	Notify Object	Return $this for chaining.
	 *
	 * @since 1.0
	 */
	public function addObject($object) {
		$this->_object = $object;
		return $this;
	}

	/**
	 * Set the method
	 *
	 * @param 	string		The method to call.
	 *
	 * @return 	Notification Object 	Returns $this to support chaining.
	 *
	 * @since 1.0
	 */
	public function setMethod($method) {
		$this->_method = '_'.$method;
	}

	/**
	 * Is the site in test mode
	 *
	 * @return 	boolean 	True or false if the site is in test mode.
	 *
	 * @since 1.0
	 */
	public function isTestMode() {
		return (bool) $this->app->store->get()->params->get('anet.test_mode');
	}

	/**
	 * Sets the body of the email
	 *
	 * When setting the body, checks if the string passed is an html page
	 * and set isHTML to true if so.
	 * 
	 * @param string $content The content of the body
	 * 
	 * @since 1.0.0
	 */
	public function setBody($content) {

		// auto-detect html
		if (stripos($content, '<html') !== false) {
			$this->_mail->IsHTML(true);
		}

		// set body
		$this->_mail->setBody($content);
	}

	/**
	 * Set the mail body using a template file
	 * 
	 * @param string $template The path to a template file. Can be use the registered paths
	 * @param array $args The list of arguments to pass on to the template
	 * 
	 * @since 1.0.0
	 */
	public function setBodyFromTemplate($template, $args = array()) {

		// init vars
		$__tmpl = $this->app->path->path($template);

		// does the template file exists ?
		if ($__tmpl == false) {
			throw new AppMailException("Mail Template $template not found");
		}

		// render the mail template
		extract($args);
		ob_start();
		include($__tmpl);
		$output = ob_get_contents();
		ob_end_clean();

		// set body
		$this->setBody($output);
	}

	/**
	 * Send the notifications.
	 *
	 * @param 	datatype		Description of the parameter.
	 *
	 * @return 	boolean 	Result of the send.
	 *
	 * @since 1.0
	 */
	public function send() {
		$method = $this->_method;
		$this->$method();
		try {	
			$this->_mail->send();
		} catch (phpmailerException $e) {
			echo $e->errorMessage(); //Pretty error messages from PHPMailer
		} catch (Exception $e) {
			echo $e->getMessage(); //Boring error messages from anything else!
		}
		if(is_array($this->_attachment)) {
			unlink($this->_attachment['path']);
		}
		
		return true;
	}

}