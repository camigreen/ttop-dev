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
class UserController extends AppController {

    
    public function __construct($default = array()) {
        parent::__construct($default);

        // set table
        $this->table = $this->app->table->account;

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

        $this->cUser = $this->app->storeuser->get();


        // registers tasks
        $this->registerTask('apply', 'save');
        // $this->registerTask('edit', 'edit');
        // $this->registerTask('save2new', 'save');
        $this->registerTask('cancel', 'display');
        // $this->registerTask('upload', 'upload');
    }
    
    /*
            Function: display
                    View method for MVC based architecture

            Returns:
                    Void
    */
    public function display($cachable = false, $urlparams = false) {
        $link = $this->baseurl;
        if($this->cUser->isAccountAdmin() || $this->cUser->isStoreAdmin()) {
            $link .= '&task=search&s=account.users';
        } else {
            $link = '/';
        }
        switch($this->getTask()) {
            case 'cancel':
                $msg = 'Changes to the user account were cancelled.';
                break;
            default:
                $msg = null;
        }
        $this->setRedirect($link, $msg);

        
    }

    public function getProfile() {
        $this->edit(true);
    }

    public function search() {
        if (!$this->template = $this->application->getTemplate()) {
            return $this->app->error->raiseError(500, JText::_('No template selected'));
        }

        $options = array();
        $search = $this->app->request->get('s', 'string', $this->app->session->get('return'));
        $this->app->session->set('return', $search);

        switch($search) {
            case 'store.users':
                $account = $this->app->store->get();
                $this->users = $account->getUsers(true);
                $title = 'All '.$account->name.' Users';
                break;
            case 'account.users':
                $account = $this->cUser->getAccount();
                $this->users = $account->getUsers(true);
                $title = 'All '.$account->name.' Users';
                break;
            default:
                $title = 'All Users';
                $this->users = $this->app->storeuser->all($options);
        }

        
        
        $this->title = $title;

        $layout = 'search';
        $this->getView()->addTemplatePath($this->template->getPath().'/user');

        $this->getView()->addTemplatePath($this->template->getPath())->setLayout($layout)->display();
    }

    public function add() {
        $this->edit();
    }

    public function edit($current = false) {

        if (!$this->template = $this->application->getTemplate()) {
            return $this->app->error->raiseError(500, JText::_('No template selected'));
        }
        if($current) {
            $this->storeuser = $this->app->storeuser->get();
        } else {
            $uid = $this->app->request->get('uid', 'int', 0);
            $this->storeuser = $this->app->storeuser->get($uid);
        }
        if(!$this->storeuser) {
            $this->app->error->raiseError('USER.001', JText::_('ERROR.USER.001'));
            return;
        }
        $edit = $this->storeuser->id > 0;
        if($edit) {
            $this->title = 'Edit User';
        } else {
            $this->storeuser->setParam('type', $this->app->request->get('type', 'string', $this->cUser->getAccount(true)->getParam('user_type', 'default')));
            $this->title = 'New '.$this->cUser->getAccount(true)->name.' User';
        }
        $type = $this->storeuser->getParam('type', 'default');
        $this->type = $type;
        $this->form = $this->app->form->create(array($this->template->getPath().'/user/config.xml', compact('type')));
        $this->form->setAssetName($this->storeuser->getAssetName())->setBelongsTo($this->storeuser->id);
        var_dump($this->form);
        $layout = 'edit';
        $this->getView()->addTemplatePath($this->template->getPath().'/user/');
        $this->getView()->addTemplatePath($this->template->getPath())->setLayout($layout)->display();

    }

    public function save() {

        // check for request forgeries
        $this->app->session->checkToken() or jexit('Invalid Token');

        // init vars
        
        $uid = $this->app->request->get('uid', 'int');
        $post = $this->app->request->get('post:', 'array', array());
        $type = $this->app->request->get('type', 'string', 'default');
        echo 'Post</br>';
        var_dump($post);
        //return;

        if($uid) {
            $storeuser = $this->app->storeuser->get($uid);
        } else {
            $storeuser = $this->app->storeuser->create();
        }

        $storeuser->bind($post);
        echo 'Bind</br>';
        var_dump($storeuser);
        //return;

        if(!$storeuser->save()) {
            var_dump($storeuser->getErrors());
            $msg = implode("\n", $storeuser->getErrors());
            $this->setRedirect($this->baseurl.'&task=edit&uid='.$uid, $msg);
            return;
        }
        echo 'Save</br>';
        var_dump($storeuser);
        //return;

        $msg = 'The user was saved successfully.';
        $link = $this->baseurl;
        switch ($this->getTask()) {
            case 'apply' :
                $link .= '&task=edit&uid='.$uid;
                break;
            case 'save2new':
                $link .= '&task=add';
                break;
            case 'save':
                if($this->cUser->isAccountAdmin() || $this->cUser->isStoreAdmin()) {
                    $link .= '&task=search&s=account.users';
                } else {
                    $link = '/';
                }
                break;
            default:
                $link = '/';
                
        }

        $this->setRedirect($link, $msg);
    }


}