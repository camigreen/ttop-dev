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
class StoreUserTable extends AppTable {

	public function __construct($app) {
		parent::__construct($app, '#__users');
		
	}


	/**
	 * Get all the results of the query built using the options passed
	 *
	 * @see _select()
	 *
	 * @param array $options The list of conditions for the query
	 *
	 * @return array The list of objects representing the table record
	 */
	public function all($options = null) {
		$profiles = parent::all($options);

		foreach($profiles as $profile) {
			$new = !$profile->id  ? true : false;

			// trigger save event
			$this->app->event->dispatcher->notify($this->app->event->create($profile, 'userprofile:init', compact('new')));

		}
		return $profiles;
	}

}

/*
	Class: ItemTableException
*/
class UserProfileTableException extends AppException {}