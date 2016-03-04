<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  User.joomla
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\Registry\Registry;

/**
 * Joomla User plugin
 *
 * @since  1.5
 */
class PlgUserZooStore extends JPlugin
{
	/**
	 * Application object
	 *
	 * @var    JApplicationCms
	 * @since  3.2
	 */
	protected $app;

	/**
	 * Database object
	 *
	 * @var    JDatabaseDriver
	 * @since  3.2
	 */
	protected $db;

	public function onUserLoginFailure($response) {

		var_dump($response);
		if($response['status'] === 4 && $response['error_message'] == "suspended") {
			$this->app->redirect(JRoute::_('account-suspended',false));
		}
		//die();
	}
}
