<?php $this->form->setValues($params); ?>
<?php if($this->form->checkGroup('coupon_code') && $this->app->store->get()->getParam('allow_coupons', false)) : ?>
    <div class="uk-form-row">
        <fieldset id="coupon_code">
            <?php echo $this->form->render('coupon_code')?>
        </fieldset>
    </div>
<?php endif; ?>