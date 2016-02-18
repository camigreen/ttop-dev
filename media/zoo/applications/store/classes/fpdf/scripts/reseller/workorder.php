<?php 

class WorkOrderFormPDF extends FormPDF {

	/**
	 * @var [string]
	 */
	public $resource = 'reseller';

	/**
	 * @var [string]
	 */
	public $type = 'workorder';
	
	

	public function __construct() {
		parent::__construct();
	}

	public function setData($order) {
		$form_data = $this->app->data->create();

		$billto = array(
            $order->elements->get('billing.name'),
            $order->elements->get('billing.street1'),
            ($order->elements->get('billing.street2') ? $order->elements->get('billing.street2') : null),
            $order->elements->get('billing.city').', '.$order->elements->get('billing.state').' '.$order->elements->get('billing.postalCode'),
            $order->elements->get('billing.phoneNumber').'  '.$order->elements->get('billing.altNumber'),
            $order->elements->get('email')
        );

		$form_data->set('billto', $billto);
		
		if($order->elements->get('shipping_method') != 'LP') {
        	$shipto = array(
	            $order->elements->get('shipping.name'),
	            $order->elements->get('shipping.street1'),
	            $order->elements->get('shipping.street2'),
	            $order->elements->get('shipping.city').', '.$order->elements->get('shipping.state').' '.$order->elements->get('shipping.postalCode'),
	            $order->elements->get('shipping.phoneNumber').' '.$order->elements->get('shipping.altNumber')
        	);
        	$form_data->set('shipto', $shipto);
    	}
    	$item_array = array();
	    foreach($order->elements->get('items.', array()) as $item) {
	    	$options = array();
	    	foreach($item->options as $option) {
	    		$options[] = $option['name'].': '.$option['text'];
	    	}
	    	$item_array[] = array(
	    		'item_description' => array(
	    			array('format' => 'item-name','text' => $item->name),
	    			array('format' => 'item-options','text' => implode("\n",$options))
	    		),
	    		'qty' => array('text' => $item->qty),
	    		'msrp' => array('text' => $item->getTotal('base')),
	    		'markup_price' => array('text' => $this->app->number->currency($item->getTotal('markup'), array('currency' => 'USD'))."\n".$item->getPrice()->getMarkupRate(true).' Markup'),
	    		'dealer_price' => array('text' => $this->app->number->currency($item->getTotal('reseller'), array('currency' => 'USD'))."\n".$item->getPrice()->getDiscountRate(true).' Discount'),
	    		'dealer_profit' => array('text' => $this->app->number->currency($item->getTotal('margin'), array('currency' => 'USD'))."\nTotal Discount ".$item->getPrice()->getProfitRate(true))
	    	);

	    }

		$form_data->set('items', $item_array);
	    $form_data->set('id', $order->id);
	    $form_data->set('created', $this->app->html->_('date', $order->created, JText::_('DATE_STORE_RECEIPT')));
	    $form_data->set('salesperson', $order->getCreator());
	    $form_data->set('delivery_method', JText::_(($ship = $order->elements->get('shipping_method')) ? 'SHIPPING_METHOD_'.$ship : ''));
	    $form_data->set('account_name', $order->params->get('payment.account_name'));
	    $form_data->set('account_number', $order->params->get('payment.account_number'));
	    $form_data->set('po_number', $order->params->get('payment.po_number'));
	    $form_data->set('customer', $order->params->get('payment.customer_name'));
	    $form_data->set('terms', JText::_(($terms = $order->params->get('terms')) ? 'ACCOUNT_TERMS_'.$terms : ''));
	    $form_data->set('subtotal', $order->getSubtotal());
	    $form_data->set('tax_total', $order->getTaxTotal());
	    $form_data->set('ship_total', $order->getShippingTotal());
	    $form_data->set('total', $order->getTotal());
	    $form_data->set('balance_due', $order->params->get('payment.status') == 3 ? 0 : $order->total);

		return parent::setData($form_data);
	}
	
}

?>