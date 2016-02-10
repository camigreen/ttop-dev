<?php
/**
 * @package   com_zoo
 * @author    YOOtheme http://www.yootheme.com
 * @copyright Copyright (C) YOOtheme GmbH
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

/*
	Class: ItemTable
		The table class for items.
*/
class OrderDevTable extends AppTable {

	public function __construct($app) {
		parent::__construct($app, '#__zoo_orderdev');
	}


	/*
		Function: save
			Override. Save object to database table.

		Returns:
			Boolean.
	*/
	public function save($object) {
		$result = parent::save($object);
		return $result;
	}

	protected function _initObject($object) {

		parent::_initObject($object);
	
		// add to cache
		$key_name = $this->key;

		if ($object->$key_name && !key_exists($object->$key_name, $this->_objects)) {
			$this->_objects[$object->$key_name] = $object;
		}

		// trigger init event
		$this->app->event->dispatcher->notify($this->app->event->create($object, 'order:init'));

		return $object;
	}
}

/*
	Class: ItemTableException
*/
class OrderDevTableException extends AppException {}