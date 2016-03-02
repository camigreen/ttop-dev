<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

class Coupon {

	public $id;
	public $code;
	public $created;
	public $created_by;
	public $modified;
	public $modified_by;
	public $params;
	public $app;


	public function getParam($name, $default = null) {
		return $this->params->get($name, $default);
	}

	public function setParam($name, $value) {
		$this->params->set($name, $value);
		return $this;
	}

	public function getExpirationDate() {
		$tzoffset = $this->app->date->getOffset();
		return $this->app->date->create($this->exp_date);
	}

	public function isExpired() {
		$tzoffset = $this->app->date->getOffset();
		$now = $this->app->date->create();
		$exp = $this->app->date->create($this->exp_date);

		return $now > $exp;
	}

	public function bind($data = array()) {
		$exclude = array('params', 'dates');
		foreach($data as $key => $value) {
			if(property_exists($this, $key) && !in_array($key, $exclude)) {
				$this->$key = $value;
			}
		}

		if(isset($data['dates'])) {

		}

		if(isset($data['params'])) {
			foreach($data['params'] as $key => $value) {
				if(is_array($value)) {
					$this->params->set($key.'.', $value);
				} else {
					$this->params->set($key, $value);
				}
				
			}
		}
	}

}