<?php defined('_JEXEC') or die('Restricted access');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of newPHPClass
 *
 * @author Shawn
 */
class AccountHelper extends AppHelper {

	protected $_accounts = array();


	public function __construct($app) {
		parent::__construct($app);

		$this->app->loader->register('Account', 'classes:account.php');
		$this->table = $this->app->table->account;

        
	}

	public function get($id = null) {

		// if id is null then get the logged in account.
		if(!$id) {
			$account = $this->create();
			return $account;
		}

		if(!is_null($id) && !isset($this->_accounts[$id])) {
			$account = $this->table->get($id);
			$this->_accounts[$id] = $account;
			$new = false;
			// trigger init event
			$this->app->event->dispatcher->notify($this->app->event->create($this->_accounts[$id], 'account:init', compact('new')));
		}

		return $this->_accounts[$id];

	}

	public function create($type = 'default', $args = array()) {

		if($type == 'default') {
			$class = 'Account';
			$classname = 'default';
		} else {
			list($classname, $type) = explode('.', $type.'.',3);
			$class = $classname.'Account';
			$this->app->loader->register($class, 'classes:accounts/'.strtolower(basename($class,'Account')).'.php');
		}

		$account = new $class();
		$account->type = $classname.($type ? '.'.$type : '');
		$account->app = $this->app;

		$new = true;
		
		// trigger init event
		$this->app->event->dispatcher->notify($this->app->event->create($account, 'account:init', compact('new')));
		
		if(!empty($args)) {
			$account->bind($args);
		}
		return $account;

	}

	public function all($options = array()) {
		$accounts = $this->table->all($options);
		foreach($accounts as $account) {
			$new = false;
			// trigger init event
			$this->app->event->dispatcher->notify($this->app->event->create($account, 'account:init', compact('new')));
		}
		return $accounts;
	}

	public function getByUser($user = null) {

		$db = $this->app->database;
		$id = $db->queryResult('SELECT parent FROM #__zoo_account_user_map WHERE child = '.$user->id);
		if(is_null($id)) {
			return $this->create();
		} 
		$account = $this->get($id);

		return $account;
	}

	public function getByUserId($uid = null) {
		if(!$uid || $uid == 0) { return false; }
		$db = $this->app->database;
		$id = $db->queryResult('SELECT parent FROM #__zoo_account_user_map WHERE child = '.$uid);

		$account = $this->get($id);

		return $account;
	}
    
}