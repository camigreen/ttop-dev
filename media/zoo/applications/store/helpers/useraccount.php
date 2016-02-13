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
class UserAccountHelper extends AppHelper {

	protected $_users = array();


	public function get($id = null) {
		if(!$id) {
			$user = $this->app->user->get();
			$user->app = $this->app;

			// trigger init event
			$this->app->event->dispatcher->notify($this->app->event->create($user, 'useraccount:init'));
			
			var_dump($user);
		}
	}



}