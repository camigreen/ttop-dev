<?php
/**
 * @package   com_zoo
 * @author    YOOtheme http://www.yootheme.com
 * @copyright Copyright (C) YOOtheme GmbH
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

// set attributes
$control_name = $control_name == 'null' ? $name : $control_name."[$name]";
$attributes = array('id' => $name,'type' => 'text', 'name' => "$control_name", 'value' => $value, 'class' => isset($class) ? $class : '');
if($node->attributes()->disabled) {
	$attributes['disabled'] = true;
}

printf('<input %s />', $this->app->field->attributes($attributes, array('label', 'description', 'default')));