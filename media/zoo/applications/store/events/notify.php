<?php
/**
* @package   
* @author    Shawn Gibbons
* @copyright Copyright (C) Shawn Gibbons
* @license   
*/

class NotifyEvent {

	public static function payment() {
		$order = $event->getSubject();
        $app = APP::getInstance('zoo');

        echo 'Testing Email';
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
	 * Send the notifications.
	 *
	 * @param 	datatype		Description of the parameter.
	 *
	 * @return 	boolean 	Result of the send.
	 *
	 * @since 1.0
	 */
	public function send() {
		$this->_build();
		try {
			$this->_mail->send();
		} catch (Exception $e) {
			echo 'Caught exception: ',  $e->getMessage(), "\n";
			return false;
		}
		return true;
	}

	/**
	 * Add an attachment to the message
	 *
	 * @param 	string		Path to the file to be attached.
	 *
	 * @return 	object  	Return $this for chaining.
	 *
	 * @since 1.0
	 */
	public function addAttachment($path, $name) {
		$this->_attachments[] = $path;
		$this->_mail->addAttachment($path, $name);
		return $this;
		
	}

}