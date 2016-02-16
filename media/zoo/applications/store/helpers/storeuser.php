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
class StoreUserHelper extends AppHelper {

	protected $_users = array();

	public function get($id = null) {
		$this->app->loader->register('StoreUser','classes:storeuser.php');
		$user = $this->app->user->get($id);
		if($user) {
			$storeuser = new StoreUser($user);
			$storeuser->app = $this->app;
			// trigger init event
			$this->app->event->dispatcher->notify($this->app->event->create($storeuser, 'storeuser:init'));
		} else {
			return false;
		}
	
		return $storeuser;

	}

	public function create() {
		$this->app->loader->register('StoreUser','classes:storeuser.php');
		$user = new JUser;
		$storeuser = new StoreUser($user);
		$storeuser->app = $this->app;
		$this->app->event->dispatcher->notify($this->app->event->create($storeuser, 'storeuser:init'));
		return $storeuser;
	}

	public function all($options = array()) {
		if(isset($options['conditions'])) {
			var_dump($options['conditions']);
		}
		$query = 'SELECT id FROM #__users';

        $rows = $this->app->database->queryObjectList($query);
        $users = array();
        foreach($rows as $row) {
        	$users[$row->id] = $this->get($row->id);

        	// Trigger init event
        	$this->app->event->dispatcher->notify($this->app->event->create($users[$row->id], 'storeuser:init'));

        }

        return $users;
	}


}