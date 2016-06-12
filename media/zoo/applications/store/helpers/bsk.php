<?php defined('_JEXEC') or die('Restricted access');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of newPHPClass
 *
 * @author Shawn
 */
class BSKHelper extends AppHelper {

	protected $xml;

	public function __construct($app) {
		parent::__construct($app);

        // var_dump($this->xml);
        $this->xml = simplexml_load_file($this->app->zoo->getApplication()->getTemplate()->getPath().'/renderer/item/measurements.xml');
	}

	public function getMakes($type) {
		
		$makes = $this->xml->$type;
		$result = array();
		foreach($makes->make as $make) {
			$models = $this->getModel($type, (string) $make->attributes()->value);
			if($make->model && !empty($models)) {
				$result[(string)$make->attributes()->value] = (string) $make->attributes()->name;
			}
			
		}
		return $result;
	}

	public function getModel($type, $boatMake) {
		$bsk = $this->xml->$type;
		$result = array();
		foreach($bsk->make as $make) {
			if($make->attributes()->value == $boatMake) {
				foreach($make->model as $model) {
					if(!(bool) $model->attributes()->disabled) {
						$result[(string)$model->attributes()->value] = (string)$model;
					}
				}
			}
		}

		return $result;

	}
    
}