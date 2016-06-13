<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$elements = $order->elements;
$items = $order->elements->get('items.');
$query = $order->params->get('terms', 'DUR') == 'DUR' ? '&form=receipt' : '&form=invoice';
$query .= $order->getAccount()->isReseller() ? '&type=reseller' : '&type=default';
$page = $this->page;
$salesperson = $order->created_by == 0 ? 'Website' : $this->app->storeuser->get($order->created_by)->name;
?>
<div class='ttop-receipt'>
    <div class="uk-width-1-1 uk-container-center uk-text-right uk-margin-bottom">
        <a href="/store/checkout?task=getPDF<?php echo $query; ?>&id=<?php echo $order->id; ?>&format=raw" class="uk-button uk-button-primary" target="_blank"><i class="uk-icon-print"></i> Print <?php echo $order->params->get('payment.status') == 3 ? 'Receipt' : 'Invoice'; ?></a>
    </div>
    <div id="contentext" class="uk-width-1-1">
    </div>
    <div class="uk-width-1-1 uk-container-center">
        <table class="uk-table uk-table-condensed">
            <thead>
                <tr>
                    <th class="uk-width-3-10 uk-text-center">Salesperson</th>
                    <th class="uk-width-2-10 uk-text-center">Order Number</th>
                    <th class="uk-width-3-10">Order Date</th>
                    <th class="uk-width-2-10">Delivery Method</th>
                </tr>
            </thead>
            <tfoot>

            </tfoot>
            <tbody>
                <tr>
                    <td class="uk-text-center"><?php echo $salesperson ?></td>
                    <td class="uk-text-center"><?php echo $order->id; ?></td>
                    <td class="uk-text-center"><?php echo $order->getOrderDate(); ?></td>
                    <td class="uk-text-center"><?php echo JText::_(($ship = $order->elements->get('shipping_method')) ? 'SHIPPING_METHOD_'.$ship : ''); ?></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="uk-width-1-1 uk-container-center">
        <div class="uk-grid">
            <?php echo $this->partial('billing',compact('elements')); ?>
                <?php 
                    if(!$order->elements->get('localPickup')) {
                        echo $this->partial('shipping',compact('elements'));
                    }
                ?>
            <div class="uk-width-1-1 payment uk-margin-top">
                <div class="uk-grid" data-uk-margin>
                    <div class="uk-width-1-1">
                        <h4>Payment Details:</h4>
                    </div>
                    <div class="uk-width-1-1">
                        <?php if($this->app->storeuser->get()->isReseller()) : ?>
                        <div class="payment-data">
                            <div>Account Name:  <?php echo $order->params->get('payment.account_name'); ?></div>
                            <div>Account Number:  <?php echo $order->params->get('payment.account_number'); ?></div>
                            <div>P.O. Number:  <?php echo $order->params->get('payment.po_number'); ?></div>
                        </div>
                        <?php else : ?>
                        <div class="payment-data">
                            <div>Payment Method:  <?php echo $order->params->get('payment.creditcard.card_name'); ?></div>
                            <div>Card Number:  <?php echo $order->params->get('payment.creditcard.cardNumber'); ?></div>
                        </div>
                        <?php endif; ?>

                    </div>

                </div>
            </div>
            <div class='uk-width1-1 items-table'>
                            <?php if($this->app->storeuser->get()->isReseller()) : ?>
            <div class='uk-width1-1 items-table'>
                <?php echo $this->partial('item.table.reseller',compact('order', 'page')); ?>
            </div>
        <?php else : ?>
            <div class='uk-width1-1 items-table'>
                <?php echo $this->partial('item.table',compact('order', 'page')); ?>
            </div>
        <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<script>

jQuery(document).ready(function($) {
    $("#contentext").load("http://ttop?option=com_zoo&controller=checkout&task=orderNotification&oid=<?php echo $order->id; ?>&format=raw");
});

</script>