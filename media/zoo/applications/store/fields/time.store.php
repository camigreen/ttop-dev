<?php
/**
 * @package   com_zoo
 * @author    YOOtheme http://www.yootheme.com
 * @copyright Copyright (C) YOOtheme GmbH
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

$control_name = $control_name == 'null' ? $name : $control_name."[$name]";
$attributes = array('id' => $name, 'name' => $control_name, 'type' => 'text', 'value' => $value);
if($node->attributes()->description) {
	$attributes['data-uk-tooltip'] = '{delay: \'1000\'}';
	$attributes['title'] = (string) $node->attributes()->description;
}
?>

<?php printf('<input %s data-uk-timepicker="{format:\'12h\'}">', $this->app->field->attributes($attributes)); ?>