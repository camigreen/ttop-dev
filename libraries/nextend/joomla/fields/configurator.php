<?php
/*
# author Roland Soos
# copyright Copyright (C) Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
*/
defined('_JEXEC') or die('Restricted access'); ?><?php

class JFormFieldConfigurator extends JFormField {
    
    protected $type = 'Configurator';
    
    protected function getInput() {
        if($this->id == 'jform_params_config_orig') return '';
        if(is_object($this->value)) $this->value = (array)$this->value;
        $html = '';
        jimport('nextend.library');
        NextendText::l('common');
        
        nextendimport('nextend.css.css');
        nextendimport('nextend.javascript.javascript');
        
        $css = NextendCss::getInstance();
        $js = NextendJavascript::getInstance();
        
        $css->addCssLibraryFile('common.css');
        $css->addCssLibraryFile('window.css');
        $css->addCssLibraryFile('configurator.css');
        
        $attribs = $this->element->attributes();
        
        $configurationXmlFile = JPATH_SITE.(string)$attribs->xml;
        
        if(NextendFilesystem::fileexists($configurationXmlFile)){
            
            $js->loadLibrary('dojo');
            
            $js->addLibraryJsLibraryFile('dojo', 'dojo/window.js');
            $js->addLibraryJsAssetsFile('dojo', 'window.js');
            $js->addLibraryJs('dojo', '
                new NextendWindow({
                  button: dojo.byId("nextend-configurator-button"),
                  node: dojo.byId("nextend-configurator-lightbox"),
                  save: dojo.byId("nextend-configurator-save"),
                  message: dojo.byId("nextend-configurator-message"),
                  onHide: function(){
                    this.message.innerHTML = "'.NextendText::_('Now_you_should_save_the_module_settings_to_apply_changes').'";
                  }
                });
            ');
            $html.= '<div id="nextend-configurator-lightbox" class="gk_hack nextend-window '.(string)$attribs->identifier.'">';
            $html.= '<div class="gk_hack nextend-window-container">';
            $html.= '<div class="gk_hack nextend-topbar"><div class="gk_hack nextend-topbar-logo"></div>';
            
            $manual = (string)$attribs->manual;
            if($manual != ""){
              $html.= '<a href="'.$manual.'" target="_blank" class="gk_hack nextend-topbar-button nextend-topbar-manual">'.NextendText::_('Manual').'</a>';
            }
            
            $support = (string)$attribs->support;
            if($support != ""){
              $html.= '<a href="'.$support.'" target="_blank" class="gk_hack nextend-topbar-button nextend-topbar-support">'.NextendText::_('Support').'</a>';
            }
            $html.= '<div id="nextend-configurator-save" class="nextend-window-save"><div class="NextendWindowSave">'.NextendText::_('APPLY').'</div></div>';
            $html.= '</div>';
            
            
            $html.= '<div class="gk_hack nextend-window-container-inner">';
            
            $html.= '<fieldset id="nextend-configurator-panels" class="gk_hack panelform">';
            $html.= '<div id="menu-pane" class="gk_hack pane-sliders">';
            
            nextendimport('nextend.form.form');
            $form = new NextendForm();
            $form->loadArray($this->value);
            
            $form->set('manual', $manual);
            $form->set('support', $support);
            
            $form->loadXMLFile($configurationXmlFile);
            
            ob_start();
            $form->render($this->name);
            
            $html.= ob_get_clean();
            
            $html.= '</div>';
            $html.= '</fieldset>';
            $html.= '</div>';
            
            $html.= '</div>';
            $html.= '</div>';
            
            $html.= '<a id="nextend-configurator-button" class="nextend-configurator-button" href="#">'.NextendText::_('Configure').'<span></span></a>
                      <span id="nextend-configurator-message">&nbsp;</span>';

            $js->addLibraryJsAssetsFile('dojo', 'form.js');
            $js->addLibraryJs('dojo', '
                new NextendForm({
                  container: "nextend-configurator-lightbox",
                  data: '.json_encode($form->_data).',
                  xml: "'.NextendFilesystem::toLinux(NextendFilesystem::pathToRelativePath($configurationXmlFile)).'",
                  control_name: "'.$this->name.'",
                  url: "'.JUri::current().'",
                  loadedJSS: '.json_encode($js->generateArrayJs()).',
                  loadedCSS: '.json_encode($css->generateArrayCSS()).'
                });
            ', true);
            
            $doc = JFactory::getDocument();
            $doc->_script = str_replace("jQuery('select').chosen({", "jQuery('select:not(.nextend-select)').chosen({", $doc->_script);
            return $html;
        }else{
            return NextendText::_("Not_found_xml_configuration").$configurationXmlFile;
        }
    }
}
