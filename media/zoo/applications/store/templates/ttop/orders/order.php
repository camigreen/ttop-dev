<?php 
	$order = $this->order;
	$elements = $this->order->elements;
	$salesperson = $this->order->created_by == 0 ? JText::_('WEBSITE_SALESPERSON') : $this->app->account->get($order->created_by)->name; 
	$type = '&type=default';
	if($this->app->account->get($order->account)->isReseller()) {
		$type='&type=reseller';
	}
$tzoffset = $this->app->date->getOffset();
$now = $this->app->date->create($order->created, $tzoffset);
	var_dump($now->toSQL());
	
	echo $tzoffset;
	var_dump($this->app->date->create($now, $tzoffset)->toSQL());
?>
<div class="uk-width-1-1 uk-margin-bottom">
	<a href="/orders/all-orders" class="uk-button uk-button-primary">Back to All Orders</a>
	<a href="/store/checkout?task=getPDF&form=workorder<?php echo $type; ?>&id=<?php echo $this->order->id; ?>&format=raw" target="_blank" class="uk-button uk-button-primary">Print Work Order</a>
	<a href="/store/checkout?task=getPDF&form=receipt<?php echo $type; ?>&id=<?php echo $this->order->id; ?>&format=raw" target="_blank" class="uk-button uk-button-primary">Download Receipt</a>
</div>
<div class="uk-width-1-3">
	<div>Order ID: <?php echo JText::_('ACCOUNT_TERMS_'.$this->order->params->get('terms')); ?></div>
	<div>Order Date: <?php echo $this->order->getOrderDate(); ?></div>
	<div>Status: <?php echo $this->app->orderdev->getStatus($order); ?></div>
</div>
<div class="uk-width-1-1">
	<div class="uk-grid">
		<?php echo $this->partial('billing', compact('elements')); ?>
		<?php echo $this->partial('shipping', compact('elements')); ?>
	</div>
</div>
<div class="uk-width-1-1 uk-margin-top">
    <div>E-mail: <?php echo $elements->get('email'); ?></div>
    <div>Delivery Preference: <?php echo $elements->get('shipping_method') == 'LP' ? 'Local Pickup' : 'UPS Ground'; ?></div>
</div>
<div class="uk-width-1-1 uk-margin-top">
    <div>Sales Rep: <?php echo $salesperson; ?></div>
</div>
<div class="uk-width-1-1">
	<?php echo $this->partial('item.table', compact('order')); ?>
</div>
<div class="uk-width-1-1">
	<div class="uk-panel uk-panel-box" style="word-wrap:break-word;">
		<div class="uk-grid">
			<div class="uk-width-1-3">
				<legend class="uk-h3">Payment</legend>
				<div>Terms: <?php echo JText::_('ACCOUNT_TERMS_'.$this->order->params->get('terms')); ?></div>
				<div>Status: <?php echo $this->app->orderdev->getPaymentStatus($order); ?></div>
				<div>Payment Approved: <?php echo $this->order->params->get('payment.approved') ? JText::_('JYES') : JText::_('JNO'); ?></div>
			</div>
			<div class="uk-width-1-3">
				<legend class="uk-h3">Credit Card Info</legend>
				<div>Card Type: <?php echo $this->order->params->get('payment.creditcard.card_name'); ?></div>
				<div>Card Number: <?php echo $this->order->params->get('payment.creditcard.cardNumber'); ?></div>
			</div>
			<div class="uk-width-1-3">
				<legend class="uk-h3">Account Info</legend>
				<div>Account Name: <?php echo $this->order->params->get('payment.account_name'); ?></div>
				<div>Account Number:<?php echo $this->order->params->get('payment.account_number'); ?></div>
				<div>Customer Name: <?php echo $this->order->params->get('payment.customer_name'); ?></div>
				<div>PO Number:<?php echo $this->order->params->get('payment.po_number'); ?></div>
			</div>
		</div>
		
	</div>
	
</div>