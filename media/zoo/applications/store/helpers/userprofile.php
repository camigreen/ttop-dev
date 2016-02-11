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
class UserProfileHelper extends AppHelper {

	/**
	 * Map all the methods to the JFactory class
	 *
	 * @param string $method The name of the method
	 * @param array $args The list of arguments to pass on to the method
	 *
	 * @return mixed The result of the call
	 *
	 * @see JFactory
	 *
	 * @since 1.0.0
	 */
    public function __call($method, $args) {
		return $this->_call(array('UserAppHelper', $method), $args);
    }


}