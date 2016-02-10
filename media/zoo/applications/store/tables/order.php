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
class OrderTable extends AppTable {

	public function __construct($app) {
		parent::__construct($app, '#__zoo_order');
	}


	/*
		Function: save
			Override. Save object to database table.

		Returns:
			Boolean.
	*/
	public function save($object) {

		$object->orderDate = $this->app->date->create($object->orderDate)->toSQL();
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
		$this->app->event->dispatcher->notify($this->app->event->create($object, 'orderdev:init'));

		return $object;
	}
}

/*
	Class: ItemTableException
*/
class OrderTableException extends AppException {}