<?php
/**
 * @package   Package Name
 * @author    Shawn Gibbons http://www.palmettoimages.com
 * @copyright Copyright (C) Shawn Gibbons
 * @license   
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Class Description
 *
 * @package Class Package
 */
class TestController extends AppController {

	/**
	 * Class constructor
	 *
	 * @param datatype	$value	Parameter Description
	 */
	public function __construct($default = array()) {

		parent::__construct($default);

        // set table
        $this->table = $this->app->table->account;

        $this->application = $this->app->zoo->getApplication();

        // registers tasks
        //$this->registerTask('receipt', 'display');



	}

	/**
	 * Describe the Function
	 *
	 * @param 	datatype		Description of the parameter.
	 *
	 * @return 	datatype	Description of the value returned.
	 *
	 * @since 1.0
	 */
	public function sessionIn() {

		$user = $this->app->account->get(10);

		var_dump($user);

		$this->app->session->set('account', $user, 'test');
		
	}

	public function clearOrder() {
		$this->app->session->clear('order', 'checkout');
	}

	public function sessionOut() {
		$user = $this->app->session->get('account', array(), 'test');

		var_dump($user);
	}

	public function testList() {
		$test = 'user.dealership';
		list($class, $type) = explode('.',$test.'.',3);
		echo 'Class:</br>';
		var_dump($class);
		echo 'Type:<?br>';
		var_dump($type);
	}

	public function itemTest() {
		$item = $this->app->table->item->get(5);
		$item = $this->app->item->create($item, 'ttopboatcover');
		var_dump($item->getPrice());
		var_dump($item->getPrice()->get('discount'));

	}

	public function testUser() {
		$storeuser = $this->app->storeuser->get();
		if(!$storeuser) {
			$this->app->error->raiseError('USER.001', JText::_('ERROR.USER.001'));
			return;
		}
		// $profile = $storeuser->getUserProfile();
		// $user->profile = $profile;
		// //$storeuser->save();
		echo 'User:</br>';
		var_dump($storeuser);
		echo 'Is Account Admin:</br>';
		var_dump($storeuser->isAccountAdmin());
		echo 'Is Store Admin:</br>';
		var_dump($storeuser->isStoreAdmin());
		echo 'User Can Edit Accounts:</br>';
		var_dump($storeuser->CanEdit('account'));
		echo 'User Can Edit Orders:</br>';
		var_dump($storeuser->CanEdit('order'));
		echo 'User Can Edit Own:</br>';
		var_dump($storeuser->CanEditOwn(0,$storeuser->id));
		echo 'User Can Edit State(Orders):</br>';
		var_dump($storeuser->CanEditState('order'));
		echo 'User Can Edit State(Accounts):</br>';
		var_dump($storeuser->CanEditState('account'));
	}
	public function testAccount() {
		$aid = $this->app->request->get('aid', 'int', false);
		if($aid) {
			$account = $this->app->account->get($aid);
		} else {
			$storeuser = $this->app->storeuser->get();
			$account = $storeuser->getAccount(true);
		}
		$account->addUser('779', true);
		echo 'Account:</br>';
		var_dump($account);
		echo 'Account User IDs:</br>';
		var_dump($account->getUsers());
	}


	public function testCoupon() {

		$coupon = $this->app->coupon->get('TEST');
		$coupon->setParam('discount', (float)'.30');
		$this->app->table->coupon->save($coupon);
		var_dump($coupon);
		var_dump($coupon->getParam('discount'));
		$coupon->isExpired();

		echo $this->app->html->_('calendar', $coupon->getExpirationDate(), 'testdate', 'testdate');
		echo 'Coupon Expired: '.($coupon->isExpired() ? 'Yes' : 'No');
	}

	public function testEmail() {
		$all = $this->app->request->get('all','bool', false);
		$oid = $this->app->request->get('oid', 'int', 6699);


		$order = $this->app->orderdev->get($oid);
		$email = $this->app->mail->create();
		$filename = $this->app->pdf->create('receipt', 'default')->setData($order)->generate()->toFile();
        $path = $this->app->path->path('assets:pdfs/'.$filename);
        $email->SMTPDebug = 2;
        $email->setSubject("Thank you for your order.");
        $email->setBodyFromTemplate($this->application->getTemplate()->resource.'mail.checkout.receipt.php');
        $email->addAttachment($path,'Receipt-'.$this->order->id.'.pdf');
        $email->addRecipient('shawn@ttopcovers.com');
        $email->Send();

        unlink($path);

        if(!$all) {return;}

        $email = $this->app->mail->create();
        $filename = $this->app->pdf->create('workorder', 'default')->setData($order)->generate()->toFile();
        $path = $this->app->path->path('assets:pdfs/'.$filename);
        $email->SMTPDebug = 2;
        $email->setSubject("Printing Workorder");
        $email->addAttachment($path,'Receipt-'.$this->order->id.'.pdf');
        $email->addRecipient('atkub24opir26@hpeprint.com');
        $email->Send();

        unlink($path);

	}



}
?>