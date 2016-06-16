<?php
	$multiple = $node->attributes()->multiple ? (bool) $node->attributes()->multiple : false;
	$attributes = array();
	if($disabled) {
		$attributes['disabled'] = true;
	}
	if($multiple) {
		$attributes['multiple'] = true;
	}
	if($control_name == 'null') {
		$attributes['name'] = $name;
	} else {
		$control_name."[$name][]";
	}
	$html[] = sprintf('<select %s >', $this->app->field->attributes($attributes, array('label', 'description', 'default')));
	if(!$multiple) {
		$html[] = '<option value="0">- Select -</option>';
	}
	$accounts = $this->app->table->account->all(array('conditions' => array('state != 3')));
	foreach($accounts as $key => $account) {
		$html[] = '<option value="'.$key.'" '.($key == $value ? "selected" : "").' >'.$account->name.'</option>';
	}
	$html[] = '</select>';

	echo implode("\n", $html);

?>