<?php
	$editPermission = (string) $node->attributes()->edit;
	$store = $this->app->store->get();
	$optionset = $node->attributes()->optionset;
	$options = $store->params->get('options.'.$optionset.'.', array());
	$class = $parent->getValue('class');
	$isParent = (bool) $parent->getValue('parent', false);


	$canEdit = $parent->getValue('canEdit');
	$viewOnly = (bool) $node->attributes()->viewOnly;
	$disabled = (int) $node->attributes()->disabled == 0 ? false : true;



	if($this->app->storeuser->get()->isStoreAdmin() || ($canEdit && !$viewOnly)) {
		$name = "{$control_name}[$name]";
		$attributes = array('name' => "$name",'class' => $class);
		$opts = array();
		foreach($options as $key => $text) {
			$selected = $key == $value ? "selected" : "";
			$opts[] = '<option value="'.$key.'" '.$selected.'>'.$text.'</option>';
		}
		printf('<select %s>%s</select>',$this->app->field->attributes($attributes, array('label', 'description', 'default')), implode("\n", $opts));
	} else {
		printf('<div>%s</div>', $options[$value]);
	}
?>