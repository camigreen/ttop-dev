<?php
/**
 * @package   com_zoo
 * @author    YOOtheme http://www.yootheme.com
 * @copyright Copyright (C) YOOtheme GmbH
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

// set attributes
$attributes = array('id' => $name, 'name' => "$name", 'value' => $label, 'class' => isset($class) ? $class : '', 'data-task', $task);

if($node->attributes()->disabled) {
	$attributes['disabled'] = true;
} 


printf('<button %s />', $this->app->field->attributes($attributes, array('label', 'description', 'default')));