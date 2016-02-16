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
class StoreHelper extends AppHelper {
    

    public function get() {
        return $this->app->account->get(7);
    }

    public function getUnassignedUsers() {

        $unassigned = array();

        $query = 'SELECT child FROM #__zoo_account_user_map';

        $rows = $this->app->database->queryResultArray($query);

        $assigned = $rows;

        $query = 'SELECT id FROM #__users';

        $rows = $this->app->database->queryResultArray($query);

        $users = $rows;

        foreach($users as $user) {
            if(!in_array($user, $assigned)) {
                $usr = $this->app->user->get($user);
                if(!$usr->superadmin || true){
                    $unassigned[$usr->id] = $usr;
                }
            }

        }
        return $unassigned;
        
    }

    public function merchantTestMode() {
        return (bool) $this->get()->getParam('anet.test_mode', false) || $this->app->storeuser->get()->getParam('test_mode', false);
    }
    
}
