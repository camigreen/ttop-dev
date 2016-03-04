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
class Account {

    public $id;

    public $name;

    public $number;

    public $type = 'default';

    public $created;

    public $created_by;

    public $modified;

    public $modified_by;

    public $params;

    public $elements;

    public $access = 1;

    public $state = 1;

    public $app;

    protected $_mappedAccounts;

    protected $_mappedAccountsLoaded = false;

    public function __construct() {

    }

    /**
     * Bind data to this class
     *
     * @return object  Account Object  Returns $this for chaining.
     *
     * @since 1.0
     */
    public function bind($data = array()) {

        if(isset($data['core'])) {
            foreach($data['core'] as $key => $value) {
                if(property_exists($this, $key)) {
                    $this->$key = $value;
                }  
            }
        }

        if(isset($data['elements'])) {
            $elements = $this->app->parameter->create();
           foreach($data['elements'] as $key => $value) {
                if(is_array($value)) {
                    $this->elements->set($key.'.', $value);
                } else {
                    $this->elements->set($key, $value);
                }
            }
        }
        if(isset($data['params'])) {
            $params = $this->app->parameter->create();
           foreach($data['params'] as $key => $value) {
                if(is_array($value)) {
                    $this->params->set($key.'.', $value);
                } else {
                    $this->params->set($key, $value);
                }
                
            }
        }

        

        foreach($data as $key => $value) {
            if(property_exists($this, $key) && $key != 'elements' && $key != 'params') {
                $this->$key = $value;
            }
        }

        // Bind the related accounts.
        if($this->app->storeuser->get()->isAccountAdmin()) {
            $related = isset($data['related']) ? $data['related'] : array();
            $this->_bindMappedAccounts($related);
        }
        
        

        return $this;
    }

    public function save() {

        // Init vars
        $new = $this->id > 0;
        $now = $this->app->date->create();
        $user = $this->app->user->get()->id;
        $tzoffset = $this->app->date->getOffset();
        
        // Set Created Date
        if(!$this->created) {
            $this->created = $now->toSQL();
        }

        $this->created_by = $this->created_by ? $this->created_by : $user;

        // Set Modified Date
        $this->modified = $now->toSQL();
        $this->modified_by = $user;

        // Save the object to the database.
        $this->app->table->account->save($this);

        // trigger save event
        $this->app->event->dispatcher->notify($this->app->event->create($this, 'account:saved', compact('new')));

        $this->_mapRelatedAccounts();

        return $this;
    }

    public function delete() {
        $this->setState(3, true);
        //$this->_removeRelatedAccounts();

        return $this;
    }

    /**
     * Get the account type
     *
     * @return string       The account type.
     *
     * @since 1.0
     */
    public function getType() {
        return JText::_('ACCOUNT_TYPE_'.$this->type);
    }

    /**
     * Get the account type
     *
     * @return string       The account type.
     *
     * @since 1.0
     */
    public function getAssetName() {
        $application = $this->app->zoo->getApplication();
        return 'com_zoo.application.'.$application->id.'.account';
    }

    /**
     * Get the account type
     *
     * @return string       The account type.
     *
     * @since 1.0
     */
    public function getClassName() {
        list($class) = explode('.', $this->type, 2);

        return $class;
    }

    /**
     * Get the given parameter for the account object
     *
     * @param  string $name The parameter to retrieve
     *
     * @return mixed  The value of the parameter. Returns null if parameter does not exist
     *
     * @since 1.0
     */
    public function getParams() {
        $params = array();
        foreach($this->params as $k => $v) {
            $value = explode('.',$k, 5);
            if (!in_array($value[0], $params)) {
                $params[] = $value[0];
            }
        }
        return $params;
    }

    /**
     * Set the given parameter for the account object
     *
     * @param  string $name The parameter to set
     *
     * @param  mixed  $value The value of the parameter
     *
     * @return mixed  The value of the parameter.
     *
     * @since 1.0
     */
    public function setParam($name, $value) {
        return $this->params->set($name, $value);
    }

    /**
     * Set the given parameter for the account object
     *
     * @param  string $name The parameter to set
     *
     * @param  mixed  $value The value of the parameter
     *
     * @return mixed  The value of the parameter.
     *
     * @since 1.0
     */
    public function getParam($name, $default = null) {
        return $this->params->get($name, $default);
    }

    /**
     * Set the status for the account object
     *
     * @param  string $state The parameter to retrieve
     * 
     * @param  boolean $save Automatically save to the database? default = false
     *
     * @return Account $this for chaining support.
     *
     * @since 1.0
     */
    public function setState($state, $save = false) {
        if ($this->state != $state) {

            // set state
            $old_state   = $this->state;
            $this->state = $state;

            // autosave comment ?
            if ($save) {
                $this->app->table->account->save($this);
            }

            // fire event
            $this->app->event->dispatcher->notify($this->app->event->create($this, 'account:stateChanged', compact('old_state')));
        }

        return $this;
    }

     /**
     * Get the state account object
     *
     * @return string  The human readable value of the account state.
     *
     * @since 1.0
     */
    public function getState() {
        return JText::_($this->app->status->get('account', $this->state));
    }

    /**
     * Get a sub-account for the account
     *
     * @param  int $id The id of the subaccount to retrieve.
     *
     * @return mixed  Returns an Account Object if it exists.  If not, returns null
     *
     * @since 1.0
     */
    public function getChild($type, $id) {
        
        $child = $this->_mappedAccounts->get('children.'.$type.'.'.$id);
        if(!is_object($child)) {
            $child = $this->app->{$type}->get($child);
        }
        return $child;

    }

    /**
     * Set a child account.
     *
     * @param  int $id The id of the child to set.
     *
     * @return object  Return $this for chaining.
     *
     * @since 1.0
     */
    public function setChild($type, $child_id) {
        $this->_mappedAccounts->set('children.'.$type.'.'.$child_id, $child_id);
        return $this;

    }

    /**
     * Get all users account.
     *
     * @param  int $id The id of the child to set.
     *
     * @return object  Return $this for chaining.
     *
     * @since 1.0
     */
    public function getUsers($asObjects = false) {
        $users = $this->_mappedAccounts->get('users.', array());
        if($asObjects) {
            $userArray = array();
            foreach($users as $id => $user) {
                $userArray[$id] = $this->app->storeuser->get($user);
            }
            $users = $userArray;
        }
        return $users;
    }

    public function addUser($user_id, $autosave = false) {
        $old_value = $this->_mappedAccounts->get('users.'.$user_id);
        $new_value = $user_id;
        $this->_mappedAccounts->set('users.'.$user_id, $user_id);
        if($autosave && $old_value !== $new_value) {
            $this->save();
        }
        return $this; 
    }

    /**
     * Get all users account.
     *
     * @param  int $id The id of the child to set.
     *
     * @return object  Return $this for chaining.
     *
     * @since 1.0
     */
    public function getParentAccounts($asObjects = false) {
        $accounts = $this->_mappedAccounts->get('accounts.parents.');
        if($asObjects) {
            $userArray = array();
            foreach($accounts as $id => $user) {
                $userArray[$id] = $this->app->account->get($user);
            }
            $accounts = $userArray;
        }
        return $accounts;
    }
     /**
     * Get all users account.
     *
     * @param  int $id The id of the child to set.
     *
     * @return object  Return $this for chaining.
     *
     * @since 1.0
     */
    public function getChildAccounts($asObjects = false) {
        $accounts = $this->_mappedAccounts->get('accounts.children.');
        if($asObjects) {
            $userArray = array();
            foreach($accounts as $id => $user) {
                $userArray[$id] = $this->app->account->get($user);
            }
            $accounts = $userArray;
        }
        return $accounts;
    }

    /**
     * Get all child accounts
     *
     * @return array  Returns an array of Account objects.
     *
     * @since 1.0
     */
    public function getChildren($type = 'account') {

        $children = $this->_mappedAccounts->get('children.'.$type.'.', array());
        $result = array();
        switch ($type) {
            case 'account':
                $getter = $this->app->account;
                break;
            case 'user':
                $getter = $this->app->user;
                break;
            default:
                $getter = $this->app->account;
        }

        foreach($children as $child) {
            if(!is_object($child)) {
                $object = $getter->get($child);
                $result[$object->id] = $object; 
            } else {
                $result[$child->id] = $child;
            }
            
        }
        return $result;

    }

    /**
     * Get all child accounts by type
     *
     * @return array  Returns an array of Account objects.
     *
     * @since 1.0
     */
    public function getChildrenByType($type) {

        $accounts = $this->_mappedAccounts->get('children.', array());
        $result = array();

        foreach($accounts as $account) {
            if($account->type == $type) {
                $result[$account->id] = $account;
            }
        }

        return $result;
    }

    /**
     * Get a parent for the account
     *
     * @param  int $id The id of the parent account to retrieve.
     *
     * @return mixed  Returns an Account Object if it exists.  If not, returns null
     *
     * @since 1.0
     */
    public function getParent($id = null) {
        
        return $this->_mappedAccounts->get('parents.'.$id);

    }

    /**
     * Set a parent account.
     *
     * @param  int $id The id of the parent to set.
     *
     * @return object  Return $this for chaining.
     *
     * @since 1.0
     */
    public function setParent($id) {

        $this->_mappedAccounts->set('parents.'.$id, $this->app->account->get($id));

        return $this;

    }

    /**
     * Get all parent accounts
     *
     * @return array  Returns an array of Account objects.
     *
     * @since 1.0
     */
    public function getParents() {

        return $this->_mappedAccounts->get('parents.', array());

    }

    public function getRelated() {
        return $this->_mappedAccounts;
    }

    /**
     * Load sub-accounts into the cache.
     *
     * @param  boolean $reload Automatically reload all subaccounts from the database. Default is NULL
     *
     * @return array  Returns an array of Account Objects.
     *
     * @since 1.0
     */
    public function loadMappedAccounts($reload = false) {
        if(!$this->_mappedAccounts) {
            $this->_mappedAccounts = $this->app->parameter->create();
        }
        // if the subaccounts array is empty load all subaccounts from the database and hold them in cache
        if($this->id) {
            $query = 'SELECT * FROM #__zoo_account_map WHERE parent = '.$this->id.' OR child = '.$this->id;

            $rows = $this->app->database->queryObjectList($query);

            foreach($rows as $row) {
                if($row->parent == $this->id) {
                    $this->_mappedAccounts->set('accounts.children.'.$row->child, $row->child);
                }
                if($row->child == $this->id) {
                    $this->_mappedAccounts->set('accounts.parents.'.$row->parent, $row->parent);
                }
            }
            $query = 'SELECT * FROM #__zoo_account_user_map WHERE parent = '.$this->id;

            $rows = $this->app->database->queryObjectList($query);

            foreach($rows as $row) {

                $this->_mappedAccounts->set('users.'.$row->child, $row->child);
            }
        }
        //var_dump($this->_mappedAccounts);
        $this->_mappedAccountsLoaded = true;
        return $this;

    }

    /**
     * Bind sub-accounts.
     *
     * @param  boolean $reload Automatically reload all subaccounts from the database. Default is NULL
     *
     * @return object  $this  Returns $this for chaining.
     *
     * @since 1.0
     */
    protected function _bindMappedAccounts($data = array()) {
        $accounts = $this->app->parameter->create();
        // Deal with the child accounts.
        if(isset($data['accounts.children'])) {
            $accounts->set('accounts.children.', $data['accounts.children']);
        }
        // Deal with the user accounts.
        if(isset($data['users'])) {
            $accounts->set('accounts.users.', $data['users']);
        }
        // Deal with the parent accounts.
        if(isset($data['accounts.parents'])) {
            $accounts->set('accounts.parents.', $data['accounts.parents']);
        }
        $this->_mappedAccounts = $accounts;
        return $this;
    }

    /**
     * Map all related accounts to the database
     *
     * @return object $this Account object for chaining.
     *
     * @since 1.0
     */
    protected function _removeRelatedAccounts() {

        // Remove all mappings where this account is the child from the database.
        $query = 'DELETE FROM #__zoo_account_map WHERE child = '.$this->id;
        $this->app->database->query($query);

        // Remove all mappings where this account is the parent from the database.
        $query = 'DELETE FROM #__zoo_account_map WHERE parent = '.$this->id;
        $this->app->database->query($query);

        // Remove all mappings where this account is the parent from the database.
        $query = 'DELETE FROM #__zoo_account_user_map WHERE parent = '.$this->id;
        $this->app->database->query($query);

        return $this;
    }

    /**
     * Map all related accounts to the database
     *
     * @return object $this Account object for chaining.
     *
     * @since 1.0
     */
    protected function _mapRelatedAccounts() {

        // Remove all mappings from the database.
        $this->_removeRelatedAccounts();

        // Map all of the parent accounts to the database.
        foreach($this->_mappedAccounts->get('accounts.parents.', array()) as $parent) {
            $query = 'INSERT INTO #__zoo_account_map (parent, child) VALUES ('.$parent.','.$this->id.')';
            $this->app->database->query($query);
        }

        // Map all of the parent accounts to the database.
        foreach($this->_mappedAccounts->get('accounts.users.', array()) as $child) {
            $query = 'INSERT INTO #__zoo_account_user_map (parent, child) VALUES ('.$this->id.','.$child.')';
            $this->app->database->query($query);
        }

        // Map all of the child accounts to the database.
        foreach($this->_mappedAccounts->get('accounts.children.', array()) as $child) {
            $query = 'INSERT INTO #__zoo_account_map (parent, child) VALUES ('.$this->id.','.$child.')';
            $this->app->database->query($query);
        }

        return $this;
    }

    public function isTaxExempt() {
        return (bool) $this->params->get('tax_exempt', false);
    }

    public function isReseller() {
        $resellers = array('dealership');
        return in_array($this->type, $resellers);
    }

    public function getConfigForm() {
        $template = $this->app->zoo->getApplication()->getTemplate();
        $type = $this->type;
        $form = $this->app->form->create(array($template->getPath().'/accounts/config.xml', compact('type')));

        return $form;
    }

    public function getNotificationEmails() {
        $email = $this->elements->get('poc.order_notification') ? array($this->elements->get('poc.email')) : array();
        return $email;
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
    public function canEdit() {

        return $this->app->storeuser->get()->canEdit($this->getAssetName(), $this->created_by);
    }

}