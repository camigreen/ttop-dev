<?php
/**
* @package   
* @author    Shawn Gibbons
* @copyright Copyright (C) Shawn Gibbons
* @license   
*/

class OrderNotification extends Notification {


	protected function _receipt() {
		$order = $this->_object;
		$account = $this->app->account->get($order->account);
		if($account->isReseller()) {
			$formType = 'reseller';
			foreach($account->getNotificationEmails() as $email) {
				$recipients[] = $email;
			}
		} else {
			$recipients[] = $order->elements->get('email');
			$formType = 'default';
		}

		// Set the Subject
		$subject = 'Thank you for your order'.($this->isTestMode() ? ' - Test Order# '. $order->id : '');
		
		// Set Recipients
		foreach($recipients as $recipient) {
			$this->_mail->addRecipient($recipient);

		}
		// Set Attachment
		$pdf = $this->app->pdf->create('receipt', $formType);
        $filename = $pdf->setData($order)->generate()->toFile();
        $this->_attachment['path'] = $this->app->path->path('assets:pdfs/'.$filename);
        $this->_attachment['name'] = 'Order-'.$order->id.'.pdf';

        // Set Body
        

		// Send variables to JMail object
		try {
			$this->_mail->setSubject($subject);
			$this->_mail->addAttachment($this->_attachment['path'],$this->_attachment['name']);
			$this->setBodyFromTemplate($this->application->getTemplate()->resource.'mail.checkout.receipt.php');
		} catch (phpmailerException $e) {
		  echo $e->errorMessage(); //Pretty error messages from PHPMailer
		} catch (Exception $e) {
		  echo $e->getMessage(); //Boring error messages from anything else!
		}

	}

	protected function _payment() {
		$order = $this->_object;
		$account = $this->app->account->get($order->account);
		$recipients = explode("\n", $this->app->store->get()->params->get('notify_emails'));
		$formType = $account->isReseller() ? 'reseller' : 'default';

		// Set the Subject
		$subject = 'Online Order Notification - Order# '.$order->id;
		if($this->isTestMode()) {
			$subject = 'Test - '.$subject;
		}
		$subject = 'Online Order Notification'.($this->isTestMode() ? ' - Test - Order# '. $order->id : ' - Order# '.$order->id);
		
		// Set Recipients
		foreach($recipients as $recipient) {
			$this->_mail->addRecipient($recipient);

		}
		// Set Attachment
		$pdf = $this->app->pdf->create('workorder', $formType);
        $filename = $pdf->setData($order)->generate()->toFile();
        $this->_attachment['path'] = $this->app->path->path('assets:pdfs/'.$filename);
        $this->_attachment['name'] = 'Order-'.$order->id.'.pdf';

        // Set Body
        

		// Send variables to JMail object
		try {
			$this->_mail->setSubject($subject);
			$this->_mail->addAttachment($this->_attachment['path'],$this->_attachment['name']);
			$this->setBodyFromTemplate($this->application->getTemplate()->resource.'mail.checkout.order.php');
		} catch (phpmailerException $e) {
		  echo $e->errorMessage(); //Pretty error messages from PHPMailer
		} catch (Exception $e) {
		  echo $e->getMessage(); //Boring error messages from anything else!
		}
		
	}

	protected function _printer() {

		$order = $this->_object;
		$account = $this->app->account->get($order->account);
		$recipients = explode("\n", $this->app->store->get()->params->get('notify_printer'));
		$formType = $account->isReseller() ? 'reseller' : 'default';
		
		// Set the Subject
		$subject = 'Send Order to Printer';
		
		// Set Recipients
		foreach($recipients as $recipient) {
			$this->_mail->addRecipient($recipient);

		}
		// Set Attachment
		$pdf = $this->app->pdf->create('workorder', $formType);
        $filename = $pdf->setData($order)->generate()->toFile();
        $attachment['path'] = $this->app->path->path('assets:pdfs/'.$filename);
        $attachment['name'] = 'Order-'.$order->id.'.pdf';

        // Set Body
        

		// Send variables to JMail object
		try {	
			$this->_mail->AllowEmpty = true;
			//$this->_mail->setBody('Test');
			$this->_mail->addAttachment($attachment['path'],$attachment['name']);
		} catch (phpmailerException $e) {
		  echo $e->errorMessage(); //Pretty error messages from PHPMailer
		} catch (Exception $e) {
		  echo $e->getMessage(); //Boring error messages from anything else!
		}
		
	}

}