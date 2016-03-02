<?php
/**
 * @package   com_zoo
 * @author    YOOtheme http://www.yootheme.com
 * @copyright Copyright (C) YOOtheme GmbH
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

// set attributes
$task = (string) $node->attributes()->task;
$next = (string) $node->attributes()->next;
$class = isset($class) ? 'uk-button uk-button-primary ttop-checkout-button '.$class : 'uk-button uk-button-primary ttop-checkout-button';
$attributes = array('id' => $name, 'name' => "$name",'class' => $class, 'data-task' => $task, 'data-next' => $next);

if($node->attributes()->disabled) {
	$attributes['disabled'] = true;
} 


printf('<button %s>%s</button>', $this->app->field->attributes($attributes, array('label', 'description', 'default')), $value);