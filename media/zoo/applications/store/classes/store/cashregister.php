<?php

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
class CashRegister {
    
    public $order;
    
    public $payment;

    public $merchant;
    
    public $items;
    
    protected $taxRate = 0.07;
    
    protected $transactionID;
    
    protected $order_number;
    
    protected $order_date;
    
    protected $taxableStates = array('SC');
    
    public $total = 0;
    
    public $subtotal = 0;
    
    public $taxTotal = 0;
    
    Public $shipping = 0;
    
    public $taxExempt = false;
    
    public $app;
    
    protected $shipper;
    

    public function __construct($app) {
        $app->loader->register('PageStore','classes:store/page.php');
        $this->app = $app;
        $this->merchant = $this->app->merchant->anet;
        $this->order = $this->app->orderdev->create();
        $this->application = $this->app->zoo->getApplication();

        $this->account = $this->app->storeuser->get()->getAccount();
    }
    
    protected function updateItemQty() {
        $update = $this->app->request->get('update','array');
        if($this->app->request->get('updated','string') == 'true') {
            foreach($update as $key => $item) {
                $this->items[$key]->qty = (int)$item['qty'];
            }
            $this->set('payment:creditCard:auth_code','');
        }
    }
    
    public function scanItems () {
        $cart = $this->app->cart;
        $this->order->items = $cart->getAllItems();       
    }

    public function processOrder() {
        $this->order->orderDate = $this->app->date->create()->toSql();
        $this->order->save(true);

        return $this;
    }

    public function clearOrder() {
        $this->app->session->clear('order','checkout');
        $this->app->session->clear('cart','checkout');
    }

    public function processPayment($method) {

        switch($method) {
            case 'NET45':
            case 'NET30':
                return $this->_purchaseOrder();
                break;
            case 'DUR':
                return $this->_creditCard();
                break;
            case 'bypass':
                $this->order->transaction_id = "Not Processed";
                $this->order->save();
                $result = array(
                    'approved' => true,
                    'orderID' => $this->order->id
                );
                $this->order->result = $result;
                $this->clearOrder();
                
                return $this->order;
                break;
            default:

        }
    }

    protected function _purchaseOrder () {

        $items = $this->app->cart;
        $this->order->params->set('payment.transaction_id', "Purchase Order");
        $this->order->elements->set('items.', $items->getAllItems());
        // Update Payment Status
        $this->order->params->set('payment.status', 2);
        $this->order->params->set('payment.type', 'PO');
        $this->order->setStatus(2);
        //this->order->calculateCommissions();
        $this->order->params->set('payment.approved', true);
        $this->order->save(true);

        $this->clearOrder();
        
        return $this->order;
    }

    protected function _creditCard() {
        $order = $this->order;
        $billing = $order->elements->get('billing.');
        $shipping = $order->elements->get('shipping.');
        $items = $this->app->cart->getAllItems();
        $creditCard = $order->params->get('payment.creditcard.');
        $sale = $this->merchant;
        
        $sale->card_num = $creditCard['cardNumber'];
        $sale->exp_date = $creditCard['expMonth'].'/'.$creditCard['expYear'];
        $sale->card_code = $creditCard['card_code'];
        $sale->amount = $order->total;
        // $sale->card_num = '6011000000000012';
        // $sale->exp_date = '03/2017';
        // $sale->card_code = '555';
        // $sale->amount = '10.00';
        list($first, $last) = explode(' ', $shipping['name']);
        $sale->first_name = $first;
        $sale->last_name = $last;
        $sale->address = $billing['street1'].($billing['street2'] ? ', '.$billing['street2'] : '');
        $sale->city = $billing['city'];
        $sale->state = $billing['state'];
        $sale->zip = $billing['postalCode'];
//        $sale->country = $country = "US";
        $sale->phone = $billing['phoneNumber'];
        $sale->setCustomField("CustomerAltNumber", $billing['altNumber']);
        $sale->email = $order->elements->get('email');
        $sale->customer_ip = $order->elements->get('ip');
        $sale->invoice_num = $order->id;
        list($first, $last) = explode(' ', $shipping['name']);
        $sale->ship_to_first_name = $first;
        $sale->ship_to_last_name = $last;
        $sale->ship_to_address = $shipping['street1'] . ($shipping['street2'] ? ', ' . $shipping['street2'] : '');
        $sale->ship_to_city = $shipping['city'];
        $sale->ship_to_state = $shipping['state'];
        $sale->ship_to_zip = $shipping['postalCode'];
//        $sale->ship_to_country = $ship_to_country = "US";
        $sale->tax = $this->taxTotal;
        $sale->freight = $this->shipping;
//        $sale->duty = $duty = "Duty1<|>export<|>15.00";
//        $sale->po_num = $po_num = "12";
        $priceDisplay = ($this->app->storeuser->get()->isReseller() ? 'reseller' : 'retail');
        foreach($items as $item) {
            $sale->addLineItem(
                $item->id,
                str_replace('-',' ',substr($item->name,0,25)),// Item Name
                '',
//                str_replace('-',' ',substr($item->get('description'),0,31)),// Item Description
                $item->qty,// Item Quantity
                number_format($item->getPrice()->get($priceDisplay),2,'.',''), // Item Unit Price
                ($item->taxable ? 'Y' : 'N')// Item taxable
            );
        }

        $response = $sale->authorizeAndCapture();

        
        if($response->approved) {

            $order->params->set('payment.creditcard.cardNumber', $response->account_number);
            $order->params->set('payment.creditcard.card_type', str_replace(' ','_',strtolower($response->card_type)));
            $order->params->set('payment.creditcard.card_name', $response->card_type);
            $order->params->set('payment.creditcard.auth_code', $response->authorization_code);
            $order->params->set('payment.approved', $response->approved);
            $order->params->set('payment.response_text', $response->response_reason_text);
            $order->params->set('payment.status', 3);
            $order->params->set('payment.type', 'CC');
            $order->elements->set('items.', $items);
            $order->setStatus(2);
            if($this->app->store->merchantTestMode()) {
                $order->params->set('payment.transaction_id','Test Mode');
                $order->save(true);
            } else {
                $order->params->set('payment.transaction_id',$response->transaction_id); ;
                $order->save(true);
            }

            $this->clearOrder();

        } else {
            // trigger payment failure event
            $this->app->event->dispatcher->notify($this->app->event->create($this->order, 'order:paymentFailed', array('response' => $response)));
            $order->params->set('payment.approved', $response->approved);
            $order->params->set('payment.response_text', $response->response_reason_text);
        }

        return $order;
    }
}

class CashRegisterException extends AppException {}
