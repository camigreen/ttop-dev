<?php
	$multiple = $node->attributes()->multiple == 1 ? 'multiple' : null;
	$name = $control_name == 'null' ? $name : $control_name."[$name][]";
	$html[] = '<select name="'.$name.'" class="'.$class.'" '.$multiple.'>';
	if(!$multiple) {
		$html[] = '<option value="0">- Select -</option>';
	}
	$accounts = $this->app->table->account->all();
	foreach($accounts as $key => $account) {
		$html[] = '<option value="'.$key.'" '.($key == $value ? "selected" : "").' >'.$account->name.'</option>';
	}
	$html[] = '</select>';

	echo implode("\n", $html);

?>