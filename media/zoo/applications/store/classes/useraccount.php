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
class UserAccount {

	protected $_user;
	protected $_related;
	protected $_errors = array();
	public $profile;

	public function __construct($app, $user) {
		$this->app = $app;
		$this->_user = $user;
		$this->application = $this->app->zoo->getApplication();
		$this->_related = $this->app->parameter->create();
	}

	public function getUser() {
		return $this->_user;
	}

	public function getAccount () {
		return $this->_related->get('parent.account');
	}

	public function setAccount($account) {
		$this->_related->set('parent.account', $account);

		return $this;

	}

	public function getUserProfile() {
		return $this->profile;
	}

	public function setErrors($errors) {
		$errors = (array) $errors;
		foreach($errors as $error) {
			$this->_errors[] = $error;
		}
		return $this->_errors;
	}

	public function getErrors () {
		return $this->_errors;
	}

	public function clearErrors() {
		$this->_errors = array();

	}

	public function bind ($data = array()) {
		if(isset($data['user'])) {
			$this->_user->bind($data['user']);
		}
		if(isset($data['profile'])) {
			$this->profile = $this->app->parameter->create();
			foreach($data['profile'] as $key => $value) {
				$this->profile->set($key, $value);
			}
		}
		if(isset($data['related']) && isset($data['related']['parent.account'])) {
			$this->_related->remove('parent.account');
			foreach($data['related']['parent.account'] as $parent) {
				if($parent != 0) {
					$this->setAccount($this->app->account->get($parent));
				}
			}
		}
	}

	public function save() {
		$this->_user->profile = (array) $this->profile;
		$this->_user->save();

		if($this->_user->getErrors()) {
			$this->setErrors($this->_user->getError());
			return false;
		}
		
		if($this->getAccount()) {
			$this->getAccount()->setChild('user',$this->_user->id)->save();
		}
		return $this;
	}

	public function isRegistered() {
        return !$this->_user->guest;
    }

    public function isReseller() {
        
        return $this->_account->isReseller();
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

        if (is_null($this->_user)) {
            $this->_user = $this->userhelper->get();
        }

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
        if(!$asset_id) {
            $asset_id = 'com_zoo';
        } else {
        	$asset_id = $this->application->getAssetName().'.'.$asset_id;
        }

        return (bool) $this->_user->authorise($action, $asset_id);
    }

}