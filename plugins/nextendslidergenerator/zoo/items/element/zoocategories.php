<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php

nextendimport('nextend.form.element.list');

class NextendElementZooCategories extends NextendElementList {

    var $_menutype = 'mainmenu';

    function fetchElement() {
        global $nextendzooapp;
        //$menu = explode('|*|', $this->parent->_value);
        
        $this->_categories = $nextendzooapp->getCategories(true, null, true);
        

        $this->_xml->addChild('option', 'Root')->addAttribute('value', 0);
        $this->cats = array();
        if (count($this->_categories)) {
            foreach ($this->_categories AS $category) {
                if(!isset($this->cats[$category->parent])) $this->cats[$category->parent] = array();
                $this->cats[$category->parent][] = $category;
            }
            $this->renderCategory(0, ' - ');
        }
        
        $this->_value = $this->_form->get($this->_name, $this->_default);
        $html = parent::fetchElement();
        return $html;
    }

    
    function renderCategory($parent, $pre){
      if(isset($this->cats[$parent])){
          foreach($this->cats[$parent] AS $category){
              $this->_xml->addChild('option', htmlspecialchars($pre.$category->name))->addAttribute('value', $category->id);
              $this->renderCategory($category->id, $pre.' - ');
          }
      }
    }
}
