<?php 
$tzoffset = $this->app->date->getOffset();
if($this->coupon->getParam('expiration.')) {
	$expiration = array();
	foreach($this->coupon->getParam('expiration.') as $key => $value) {
		$datetime = $this->app->date->create($value);
		$datetime->setTimezone(new DateTimeZone($tzoffset));
		list($date, $time) = explode(' ', $datetime->format(JText::_('DATE_STORE_RECEIPT'), true),2);
		$expiration[$key.'.date'] = $date;
		$expiration[$key.'.time'] = $time;
	}
	$this->form->setValues($expiration);
}


$this->form->setValues($this->coupon);

$this->form->setValues($this->coupon->params);
?>

<p>Edit Coupon</p>
<div class="uk-grid">
	<div class="uk-width-1-1 uk-margin">
		<div class="uk-grid menu-buttons">
			<div class="uk-width-1-6">
	    		<button class="uk-button uk-button-success uk-width-1-1 uk-margin-small-bottom task-button" data-task="apply">Save</button>
	    	</div>
	    	<div class="uk-width-1-6">
	    		<button class="uk-width-1-1 uk-button uk-button-primary uk-margin-small-bottom task-button" data-task="save">Save and Close</button>
	    	</div>
	    	<?php if($this->app->storeuser->get()->canCreate()) : ?>
		    	<div class="uk-width-1-6">
		    		<button class="uk-width-1-1 uk-button uk-button-primary uk-margin-small-bottom task-button" data-task="save2new">Save and New</button>
		    	</div>
	    	<?php endif; ?>
	    	<div class="uk-width-1-6">
	    		<button class="uk-width-1-1 uk-button uk-button-default uk-margin-small-bottom task-button" data-task="cancel">Cancel</button>
	    	</div>
    	</div>
	</div>
	<div class="uk-width-2-10 side-bar">
		<div class="uk-grid" uk-grid-margin>
			<div class="uk-grid-margin-1-1 uk-text-small">
				<div>Created:</div>
				<div class="uk-text-muted"><?php echo $this->coupon->created == null ? JText::_('Not created') : $this->app->html->_('date', $this->coupon->created, JText::_('DATE_FORMAT_LC2'), $this->app->date->getOffset()); ?></div>
				<div>Created By:</div>
				<div class="uk-text-muted"><?php echo $this->coupon->created_by ? $this->app->user->get($this->coupon->created_by)->name : null; ?></div>
				<div>Modified:</div>
				<div class="uk-text-muted"><?php echo $this->coupon->modified == null ? JText::_('Not modified') : $this->app->html->_('date', $this->coupon->modified, JText::_('DATE_FORMAT_LC2'), $this->app->date->getOffset()); ?></div>
				<div>Modified By:</div>
				<div class="uk-text-muted"><?php echo $this->coupon->modified_by ? $this->app->user->get($this->coupon->modified_by)->name: null; ?></div>

			</div>
		</div>
	</div>
	<div class="uk-width-8-10">
		<form id="coupon_form" class="uk-form" method="post" action="<?php echo $this->baseurl; ?>">
			<?php if($this->form->checkGroup('test')) : ?>
			    <div class="uk-form-row">
			           <?php echo $this->form->render('test')?>
			    </div>
			<?php endif; ?>
			<input type="hidden" name="task" />
			<input type="hidden" name="cid" value="<?php echo $this->coupon->id; ?>" />
			<?php echo $this->app->html->_('form.token'); ?>
		</form>
		<script>
			jQuery(function($) {
				$(document).ready(function(){
					$('button.task-button').on('click', function(e) {
						console.log('menu-button clicked');
						e.preventDefault();
						var task = $(e.target).data('task');
						console.log(task);
						$('[name="task"]').val(task);
						$('#coupon_form').trigger('submit');
					})
				})
			})
		</script>
	</div>
</div>


