<?php
/**
* @package   com_zoo
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
    Class: DefaultController
        Site controller class
*/
class CouponController extends AppController {

    
    public function __construct($default = array()) {
        parent::__construct($default);

        // get application
        $this->application = $this->app->zoo->getApplication();

        // get Joomla application
        $this->joomla = $this->app->system->application;

        // get params
        $this->params = $this->joomla->getParams();

        // get pathway
        $this->pathway = $this->joomla->getPathway();

        // set base url
        $this->baseurl = $this->app->link(array('controller' => $this->controller), false);

        // registers tasks
        //$this->registerTask('command', 'function');
        $this->registerTask('apply', 'save');
    }
    
    /*
            Function: display
                    View method for MVC based architecture

            Returns:
                    Void
    */
    public function display($cachable = false, $urlparams = false) {

    }

    public function edit () {
        if (!$this->template = $this->application->getTemplate()) {
            return $this->app->error->raiseError(500, JText::_('No template selected'));
        }
        $this->coupon = $this->app->coupon->getByCode('TEST');
        $type = 'default';
        $layout = 'edit';
        $this->form = $this->app->form->create(array($this->template->getPath().'/coupon/config.xml', compact('type')));

        $this->getView()->addTemplatePath($this->template->getPath().'/coupon');
        $this->getView()->addTemplatePath($this->template->getPath())->setLayout($layout)->display();
    }

    public function save() {

        // check for request forgeries
        $this->app->session->checkToken() or jexit('Invalid Token');

        // init vars
        
        $cid = $this->app->request->get('cid', 'int');
        $post = $this->app->request->get('post:', 'array', array());
        echo 'Post</br>';
        var_dump($post);
        return;

        if($cid) {
            $coupon = $this->app->coupon->get($cid);
        } else {
            $coupon = $this->app->coupon->create();
        }

        $account->bind($post);
        echo 'Bind</br>';
        var_dump($account);
        //return;

        $account->save();
        echo 'Save</br>';
        var_dump($account);
        //return;

        $msg = 'The account was saved successfully.';
        $link = $this->baseurl;
        switch ($this->getTask()) {
            case 'apply' :
                $link .= '&task=edit&aid='.$account->id.'&type='.$account->type;
                break;
            case 'save2new':
                $link .= '&task=add';
                break;
            default:
                if($this->cUser->isAccountAdmin()) {
                    $link .= '&task=search';
                } else {
                    $link = '/';
                }
                
        }

        $this->setRedirect($link, $msg);
    }


}