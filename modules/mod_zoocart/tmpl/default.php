<?php // no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
$user = $zoo->customer->getUser();
$testMode = $zoo->merchant->testMode();
?>

<ul id="cart-module" class="uk-list uk-hidden-small uk-navbar-flip">
    <li class="uk-parent" data-uk-dropdown>
        <a href="#">
            <div class="uk-grid"> 
                <div class="uk-width-1-1">
                    <div id="cart-module" data-cart="open">
                        <div class="">
                            <span class="icon"></span>
                            <span class="currency">$</span>
                            <span data-cart="total">0.00</span>
                            <span class="items">(<span data-cart="quantity">0</span> Items)</span>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </li>
</ul>
<div id="cart-modal" class="uk-modal">
    <div class="uk-modal-dialog uk-modal-dialog-large">
        <div class="uk-panel uk-panel-box">
            <h3 class="uk-panel-title">Shopping Cart</h3>
                <table class="uk-table uk-table-hover uk-table-striped uk-table-condensed">
                    <thead>
                        <tr>
                            <th class="uk-width-2-4">Item</th>
                            <th class="uk-width-1-4">Quantity</th>
                            <th class="uk-width-1-4">Price</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="uk-text-right" colspan="2">Total</td>
                            <td class="item-total uk-text-bold uk-text-large"></td>
                        </tr>
                    </tfoot>
                </table>
            <div class="uk-align-right">
                <button class="uk-button uk-button-primary continue"><?php echo JText::_('CART_CONTINUE_SHOPPING_BUTTON'); ?></button>
                <button class="uk-button uk-button-primary checkout"><?php echo JText::_('CART_CHECKOUT_BUTTON'); ?></button>
                <button class="uk-button uk-button-primary clear"><?php echo JText::_('CART_EMPTY_CART_BUTTON'); ?></button>
            </div>
        </div>
    </div>
</div>






