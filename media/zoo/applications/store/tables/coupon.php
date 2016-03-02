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
class CouponTable extends AppTable {

	public function __construct($app) {
		parent::__construct($app, '#__zoo_coupon');
	}

}

/*
	Class: ItemTableException
*/
class CouponTableException extends AppException {}