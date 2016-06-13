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
class NotifyHelper extends AppHelper {

	public function __construct($app) {
		parent::__construct($app); 
		$this->app->loader->register('Notification', 'classes:notification.php');
	}

	public function create($type, $object) {
		list($file, $method) = explode(':', $type);
		$class = $file.'Notification';
		$this->app->loader->register($class, 'notifications:'.$file.'.php');
		$notify = new $class($this->app);
		$notify->addObject($object)->setMethod($method);
		return $notify;
	}

}