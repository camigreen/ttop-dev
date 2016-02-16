<?php
/**
 * @package   com_zoo
 * @author    YOOtheme http://www.yootheme.com
 * @copyright Copyright (C) YOOtheme GmbH
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
$this->app->document->addScript('assets:jquery-toggles-master/toggles.js');
$this->app->document->addStyleSheet('assets:jquery-toggles-master/css/toggles.css');
$this->app->document->addStyleSheet('assets:jquery-toggles-master/css/themes/toggles-light.css');
$control_name = $control_name == 'null' ? $name : $control_name."[$name]";
$options = array();
$width = $node->attributes()->toggle_width ? (int) $node->attributes()->toggle_width : 60;
$height = $node->attributes()->height ? (int) $node->attributes()->height : 25;
foreach ($node->children() as $option) {
	$options[(string) $option->attributes()->value] = JText::_((string) $option);
}
	$toggles = array(
		'drag' => false,
		'on' => (bool) $value,
		'textbox' => "[name=\"$control_name\"]",
		'text' => $options,
		'width' => $width,
		'height' => $height
	);
	$settings = json_encode($toggles);
?>
<div class="uk-margin uk-width-1-1">
<div id="<?php echo $name; ?>" class="toggle toggle-light"></div>
<input type="hidden" name="<?php echo $control_name; ?>" value="<?php echo $value; ?>" />
</div>
<script>
jQuery(function($){
	$(document).ready(function(){
		$('#<?php echo $name; ?>.toggle').toggles(<?php echo $settings; ?>);
	})
})
</script>