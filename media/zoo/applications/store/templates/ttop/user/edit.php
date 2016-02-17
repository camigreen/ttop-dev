<?php
	
	$user = $this->storeuser->getUser();
	$params = $this->storeuser->params;
	$elements = $this->storeuser->elements;
	$account = $this->storeuser->getAccount(true);
?>

<div class="ttop ttop-account-edit uk-grid">
	
		<div class="uk-width-1-1">
			<div class="uk-article-title uk-text-center">
				<?php echo $this->title; ?>
				<?php if($user->id) : ?>
					<div class="uk-article-lead"><?php echo $user->name.' | ' . $params->get('type', 'default') . ' | ID: '.$user->id; ?></div>
				<?php endif; ?>
			</div>
		</div>

			<div class="uk-width-1-1 uk-margin">
				<div class="uk-grid menu-buttons">
					<div class="uk-width-1-6">
			    		<button class="uk-button uk-button-success uk-width-1-1 uk-margin-small-bottom" data-task="apply">Save</button>
			    	</div>
			    	<div class="uk-width-1-6">
			    		<button class="uk-width-1-1 uk-button uk-button-primary uk-margin-small-bottom" data-task="save">Save and Close</button>
			    	</div>
			    	<div class="uk-width-1-6">
			    		<button class="uk-width-1-1 uk-button uk-button-primary uk-margin-small-bottom" data-task="save2new">Save and New</button>
			    	</div>
			    	<div class="uk-width-1-6">
			    		<button class="uk-width-1-1 uk-button uk-button-default uk-margin-small-bottom" data-task="cancel">Cancel</button>
			    	</div>
		    	</div>
			</div>
			<div class="uk-width-1-1">
				<div class="uk-width-1-1">
					<form id="account_admin_form" class="uk-form" method="post" action="<?php echo $this->baseurl; ?>" enctype="multipart/form-data">
							<?php $this->form->setValues($user); ?>
							<?php $this->form->setValue('status',$params->get('status', 1)); ?>
							<?php if($this->form->checkGroup('details')) : ?>
								<div class="uk-form-row">
									<?php echo $this->form->render('details')?>
								</div>
							<?php endif; ?>
							<?php if($this->form->checkGroup('password')) : ?>
								<div class="uk-form-row">
									<fieldset id="password">
										<legend>Password</legend>
										<?php 
											if($this->app->user->get()->id == $user->id || !$user->id || true) {
												echo $this->form->render('password');
											} else {
												echo '<button id="resetPWD" class="uk-width-1-3 uk-button uk-button-primary uk-margin" data-task="resetPassword">Reset Password</button>';
											}
										?>
									</fieldset>
								</div>
							<?php endif; ?>
							<?php $this->form->setValues($elements); ?>
							<?php if($this->form->checkGroup('contact')) : ?>
								<div class="uk-form-row">
									<?php echo $this->form->render('contact')?>
								</div>
							<?php endif; ?>
							<?php $this->form->setValue('account', ($account ? $account->id : 0)); ?>
							<?php if($this->form->checkGroup('elements')) : ?>
								<div class="uk-form-row">
									<?php echo $this->form->render('elements')?>
								</div>
							<?php endif; ?>
							<?php if($this->form->checkGroup('params')) : ?>
								<div class="uk-form-row">
									<?php echo $this->form->render('params')?>
								</div>
							<?php endif; ?>
						<input type="hidden" name="task" value="apply" />
						<input type="hidden" name="uid" value="<?php echo $user->id; ?>" />
						<input type="hidden" name="params[type]" value="<?php echo $params->get('type', 'default'); ?>" />
						<?php echo $this->app->html->_('form.token'); ?>
					</form>
					<script>
						jQuery(function($) {

							$(document).ready(function(){
								$('button').on('click', function(e) {
									e.preventDefault();
									var task = $(e.target).data('task');
									var form = document.getElementById('account_admin_form');
									form.task.value = task;
									var button = document.createElement('input');
									button.style.display = 'none';
									button.type = 'submit';

									form.appendChild(button).click();

									//form.removeChild(button);
								})
							})
						})
					</script>
				</div>
			</div>
</div>