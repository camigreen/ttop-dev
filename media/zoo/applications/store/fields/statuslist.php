<?php
	
	$attributes['name'] = $control_name.'['.$name.']';	
	$attributes['class'] = $class;
	$attributes['disabled'] = $disabled;
	var_dump($disabled);
	$html[] = sprintf('<select %s >', $this->app->field->attributes($attributes, array('label', 'description', 'default')));
	$html[] = '<option value="0">- Select -</option>';

	$states = $this->app->status->getList($node->attributes()->value);
	foreach($states as $key => $state) {
		$html[] = '<option value="'.$key.'" '.($value == $key ? "selected" : "").' >'.JText::_($state).'</option>';
	}
	$html[] = '</select>';

	echo implode("\n", $html);

?>
	

