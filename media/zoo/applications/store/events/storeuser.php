<?php
/**
 * @package   com_zoo
 * @author    YOOtheme http://www.yootheme.com
 * @copyright Copyright (C) YOOtheme GmbH
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

/**
 * Deals with application events.
 *
 * @package Component.Events
 */
class StoreUserEvent {

	/**
	 * When an application is loaded on the frontend,
	 * load the language files from the app folder too
	 *
	 * @param  AppEvent 	$event The event triggered
	 */
	public static function init($event) {

		$storeuser = $event->getSubject();
        $app = $storeuser->app;
        $storeuser->set('password', null);
        $profile = JUserHelper::getProfile($storeuser->id);
        $profile = $app->parameter->create($profile->get('profile'));
        $storeuser->params = $app->parameter->create($profile->get('params'));
        $storeuser->elements = $app->parameter->create($profile->get('elements'));

		

	}

	/**
	 * Placeholder for the saved event
	 *
	 * @param  AppEvent $event The event triggered
	 */
	public static function saved($event) {

		$user = $event->getSubject();
		$new = (bool)$event['new'];
		$app = $user->app;
		
		if($new) {
			$user->elements->set('account', 0);
		}



	}

	/**
	 * Placeholder for the deleted event
	 *
	 * @param  AppEvent $event The event triggered
	 */
	public static function deleted($event) {

		$account = $event->getSubject();

	}

}