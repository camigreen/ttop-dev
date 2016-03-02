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
class CouponHelper extends AppHelper {
    
    protected $_coupons;

    public function __construct($app) {
        parent::__construct($app);
        $this->table = $this->app->table->coupon;
    }

    public function get($id) {
        $coupon = $this->table->get($id);
        
        // trigger init event
            $this->app->event->dispatcher->notify($this->app->event->create($coupon, 'coupon:init'));
        
        return $coupon;
    }

    public function getByCode($code) {

        $coupon = $this->table->first(array('conditions' => "code = '$code'"));
        
        // trigger init event
        $this->app->event->dispatcher->notify($this->app->event->create($coupon, 'coupon:init'));
        
        return $coupon;

    }

    public function create() {
        $this->app->loader->register('Coupon', 'classes:coupon.php');

        $coupon = $this->app->object->create('coupon');

        // trigger init event
        $this->app->event->dispatcher->notify($this->app->event->create($coupon, 'coupon:init'));

        return $coupon;
    }
}
