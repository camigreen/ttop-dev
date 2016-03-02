<?php
/**
 * @package   com_zoo
 * @author    YOOtheme http://www.yootheme.com
 * @copyright Copyright (C) YOOtheme GmbH
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
$this->app->document->addScript('assets:jquery-ui-1.11.4.custom/jquery-ui.min.js');
//$this->app->document->addScript('assets:jquery-ui-1.11.4.custom/jquery-ui.theme.css');
$this->app->document->addStyleSheet('assets:jquery-ui-1.11.4.custom/jquery-ui.css');
// set attributes
$control_name = $control_name == 'null' ? $name : $control_name."[$name]";
$attributes = array('id' => $name,'type' => 'text', 'name' => "$control_name", 'value' => $value);
if($disabled) {
	$attributes['disabled'] = true;
}
$min = $node->attributes()->min ? (int) $node->attributes()->min : 0;
$max = $node->attributes()->max ? (int) $node->attributes()->max : 100;


?>
<div class="uk-grid">
	<div class="uk-width-1-3">
		<?php printf('<input %s />', $this->app->field->attributes($attributes, array('label', 'description', 'default'))); ?>
	</div>
	<div class='uk-width-1-1 uk-margin slider-container'></div>
</div>

<script>
	jQuery(function($) {
	  	$(document).ready(function() {
		  	var textbox = $( "#<?php echo $name; ?>" );
		    var slider = $( "<div id='slider'></div>" ).appendTo( '.slider-container' ).slider({
		      min: <?php echo $min; ?>,
		      max: <?php echo $max; ?>,
		      range: "min",
		      value: textbox.val(),
		      slide: function( event, ui ) {
		        textbox.val(ui.value);
		      }
		    });
		    $( "#<?php echo $name; ?>" ).change(function() {
		    	console.log('changed');
		      slider.slider( "value", $(this).val() );
		    });
	  	})
  	});
</script>