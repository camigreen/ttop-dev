<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of newPHPClass
 *
 * @author Shawn Gibbons
 */
class StoreUser {

	protected $_user;
	protected $_account;

	public $params;
	public $elements;
	public $app;

	public function __construct(JUser $user) {
		$this->_user = $user;
	}

	public function bind($data) {

        if(isset($data['user'])) {
            $this->_user->bind($data['user']);
        }

        if(isset($data['params'])) {
            $this->params = $this->app->parameter->create($data['params']);
        }

        if(isset($data['elements'])) {
            $this->elements = $this->app->parameter->create($data['elements']);
        }

        if(isset($data['account'])) {
            $this->setAccount($data['account']);
            $this->setParam('type', $this->app->account->get($this->_account)->getParam('user_type', 'default'));
        } else {
            $account = $this->app->storeuser->get()->getAccount();
            $this->setAccount($account->id);
            $this->setParam('type', $account->getParam('user_type', 'default'));
        }

	}

	public function save() {

        $profile = new JRegistry();
        foreach($this->params as $key => $value) {
            $profile->set('params.'.$key, $value);
        }
        foreach($this->elements as $key => $value) {
            $profile->set('elements.'.$key, $value);
        }
        var_dump($profile);
        $this->_user->profile = $profile;
        $this->_user->save();

		$this->mapAccount($this->_account);

        return true;
	}

    public function getAssetName() {
        $application = $this->app->zoo->getApplication();
        return 'com_zoo.application.'.$application->id.'.users';
    }

    public function getUser() {
        return $this->_user;
    }

	public function getAccount($asObject = true) {
        if(!$this->_account) {
            $query = 'SELECT parent FROM #__zoo_account_user_map WHERE child = '.$this->id;
            $this->_account = $this->app->database->queryResult($query);
        }

        $account = $this->_account;

        if($asObject) {
            $account = $this->app->account->get($this->_account);
        }

		return $account;
	}

    public function setAccount($aid) {
        $this->_account = $aid ? $aid : null;

        return $this;
    }

    public function getParam($name, $default = null) {
        return $this->params->get($name, $this->_user->getParam($name, $default));
    }

    public function setParam($name, $value) {
        return $this->params->set($name, $value);
    }

	protected function getUserVariable($name) {
		return $this->_user->get($name);
	}

    public function mapAccount() {
        // Remove all mappings where this user is linked to an account.
        $query = 'DELETE FROM #__zoo_account_user_map WHERE child = '.$this->id;
        $this->app->database->query($query);
        if($this->_account) {
            // Map this user to the account.
            $query = 'INSERT INTO #__zoo_account_user_map (parent, child) VALUES ('.$this->_account.','.$this->id.')';
            $this->app->database->query($query);
        }

    }

        
    

	public function isRegistered() {
        return !$this->_user->guest;
    }

    public function isReseller() {
        
        return $this->getAccount()->isReseller();
    }

    public function getAccountTerms() {
        return $this->_account->getParam('terms', 'DUR');
    }

    public function getDiscountRate() {
        return $this->app->number->toPercentage($this->_account->getParam('discount', 0),0);
    }

    public function isGuest() {
        return (bool) $this->_user->guest;
    }
    /**
     * Check if the user is a joomla super administrator
     *
     * @return boolean If the user is a super administrator
     *
     * @since 1.0.0
     */
    public function isJoomlaSuperAdmin() {
        return $this->authorise('core.admin', 'root.1');
    }
    /**
     * Check if a user is a joomla administrator
     *
     * @return boolean if the user is an administrator
     *
     * @since 1.0.0
     */
    public function isJoomlaAdmin() {
        return $this->authorise('core.login.admin', 'root.1');
    }
        /**
     * Check if a user can access a resource
     *
     * @param int $access The access level to check against
     *
     * @return boolean If the user have the rights to access that level
     *
     * @since 1.0.0
     */
    public function canAccess($access = 0) {

        return in_array($access, $this->_user->getAuthorisedViewLevels());

    }

        /**
     * Check if a user is a joomla administrator
     *
     * @return boolean if the user is an administrator
     *
     * @since 1.0.0
     */
    public function isAccountAdmin() {
        return $this->isStoreAdmin() || $this->authorise('account.admin', 'account');
    }

    /**
     * Check if a user is a joomla administrator
     *
     * @return boolean if the user is an administrator
     *
     * @since 1.0.0
     */
    public function isStoreAdmin() {
        return $this->authorise('store.admin', 'store');
    }

    public function isAppAdmin($admin) {
        return $this->authorise($admin.'.admin', $admin);
    }

    /**
     * Evaluates user permission
     *
     * @param int $asset_id
     * @param int $created_by
     *
     * @return boolean True if user has permission
     *
     * @since 3.2
     */
    public function canEdit($asset_id = 0, $created_by = 0) {
        // var_dump($created_by);
        // var_dump($this->_user->id);
        // var_dump($this->authorise('core.edit', $asset_id));
        // var_dump($asset_id);
        return $this->isAdmin($this->_user, $asset_id) || $this->authorise('core.edit', $asset_id) || ($created_by === $this->_user->id && $this->authorise('core.edit.own', $asset_id));
    }
    /**
     * Evaluates user permission
     *
     * @param int $asset_id
     * @param int $created_by
     *
     * @return boolean True if user has permission
     *
     * @since 3.2
     */
    public function canEditOwn($asset_id = 0, $created_by = 0) {
        if (is_null($this->_user)) {
            $this->_user = $this->get();
        }
        return $this->isAdmin($this->_user, $asset_id) || ($created_by === $this->_user->id && $this->authorise('core.edit.own', $asset_id));
    }

    public function checkPermissions($access= null) {
        if(!$access) {
            return true;
        }
        switch($access) {
            case 'user':
                
                break;
            case 'admin':
                break;
            case 'storeadmin':
                break;
            default:
                $result = false;
        }

        return $result;
    }

    /**
     * Evaluates user permission
     *
     * @param JUser $user User Object
     * @param int $asset_id
     *
     * @return boolean True if user has permission
     *
     * @since 3.2
     */
    public function canEditState($asset_id = 0) {
        return $this->isAdmin($this->_user, $asset_id) || $this->authorise('core.edit.state', $asset_id);
    }

    /**
     * Evaluates user permission
     *
     * @param JUser $user User Object
     * @param int $asset_id
     *
     * @return boolean True if user has permission
     *
     * @since 3.2
     */
    public function canCreate($asset_id = 0) {
        return $this->isAdmin($this->_user, $asset_id) || $this->authorise('core.create', $asset_id);
    }

    /**
     * Evaluates user permission
     *
     * @param JUser $user User Object
     * @param int $asset_id
     *
     * @return boolean True if user has permission
     *
     * @since 3.2
     */
    public function canDelete($asset_id = 0) {
        return $this->isAdmin($this->_user, $asset_id) || $this->authorise('core.delete', $asset_id);
    }

    /**
     * Evaluates user permission
     *
     * @param JUser $user User Object
     * @param int $asset_id
     *
     * @return boolean True if user has permission
     *
     * @since 3.2
     */
    public function canManage($asset_id = 0) {
        return $this->isAdmin($this->_user, $asset_id) || $this->authorise('core.manage', $asset_id);
    }

    /**
     * Evaluates user permission
     *
     * @param JUser $user User Object
     * @param int $asset_id
     *
     * @return boolean True if user has permission
     *
     * @since 3.2
     */
    public function isAdmin($asset_id = 0) {
        return $this->authorise($this->_user, 'core.admin', $asset_id);
    }

    /**
     * Evaluates user permission
     *
     * @param JUser $user User Object
     * @param int $asset_id
     *
     * @return boolean True if user has permission
     *
     * @since 3.2
     */
    public function canManageCategories($asset_id = 0) {
        return $this->isAdmin($this->_user, $asset_id) ||  $this->authorise('zoo.categories.manage', $asset_id);
    }

    /**
     * Evaluates user permission
     *
     * @param JUser $user User Object
     * @param int $asset_id
     *
     * @return boolean True if user has permission
     *
     * @since 3.2
     */
    public function canManageComments($asset_id = 0) {
        return $this->isAdmin($this->_user, $asset_id) ||  $this->authorise('zoo.comments.manage', $asset_id);
    }

    /**
     * Evaluates user permission
     *
     * @param JUser $user User Object
     * @param int $asset_id
     *
     * @return boolean True if user has permission
     *
     * @since 3.2
     */
    public function canManageFrontpage($asset_id = 0) {
        return $this->isAdmin($this->_user, $asset_id) ||  $this->authorise('zoo.frontpage.manage', $asset_id);
    }

    /**
     * Evaluates user permission
     *
     * @param JUser $user User Object
     * @param int $asset_id
     *
     * @return boolean True if user has permission
     *
     * @since 3.2
     */
    public function canManageTags($asset_id = 0) {
        return $this->isAdmin($this->_user, $asset_id) || $this->authorise('zoo.tags.manage', $asset_id);
    }
    /**
     * Evaluates user permission
     *
     * @param JUser $user User Object
     * @param string $action
     * @param int $asset_id
     *
     * @return boolean True if user has permission
     *
     * @since 3.2
     */
    protected function authorise($action, $asset_id) {
    	if(!$this->application) {
    		$this->application = $this->app->zoo->getApplication();
    	}
        if(!$asset_id) {
            $asset_id = 'com_zoo';
        } else {
        	$asset_id = $this->application->getAssetName().'.'.$asset_id;
        }

        return (bool) $this->_user->authorise($action, $asset_id);
    }

	public function __get($name) {
		return $this->getUserVariable($name);
	}



	/**
	 * Map all the methods to the JFactory class
	 *
	 * @param string $method The name of the method
	 * @param array $args The list of arguments to pass on to the method
	 *
	 * @return mixed The result of the call
	 *
	 * @see JFactory
	 *
	 * @since 1.0.0
	 */
    public function __call($method, $args) {
    	return call_user_func_array(array($this->_user, $method), $args);
    }

	
}