<?php 

$bsk = $this->app->bsk;
$kind = (string) $node->attributes()->kind;
$makes = $bsk->getMakes($kind);

?>
<div class="uk-grid" data-uk-grid-margin>
    <div class="uk-width-1-1">
        <div class="uk-article-title uk-text-center uk-text-danger">Choose your Boat</div>
       	<p class="uk-text-center ttop-modal-subtitle" >For your convenience, we have collected measurements for the following boats.  If you do not see your boat listed, don't worry you can enter your own measurements on the order page.</p>
    </div>
    <div class="uk-width-1-1">
    	<div class="uk-grid">
			<div class="uk-width-1-2">
				<label>Boat Make</label>
				<select name="boatmake" class="uk-width-1-1">
					<option value="0">- Select -</option>
					<?php foreach($makes as $value => $make) : ?>
						<option value="<?php echo $value; ?>"><?php echo $make; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="uk-width-1-2">
				<label>Boat Model</label>
				<select name="boatmodel" class="uk-width-1-1" disabled>
					<option value="0">- Select -</option>
				</select>
			</div>
		</div>
    </div>
    <div class="uk-width-1-1">
        <div class="uk-grid">
            <div class="uk-width-1-2">
                <button id="btn_enter_my_own" class="uk-width-1-1 uk-button uk-button-danger cancel" data-mode="EMM">Enter My Own</button>
            </div>
            <div class="uk-width-1-2">
                <button id="btn_continue" class="uk-width-1-1 uk-button uk-button-danger confirm" disabled>Continue</button>
            </div>
        </div>
    </div>
</div>

<script>

	jQuery(function($) {
	  	$(document).ready(function() {
	  		$('[name="boatmake"]').on('change', function() {
	  			var elem = $('[name="boatmodel"]');
	  			$.ajax({
	                type: 'POST',
	                url: "?option=com_zoo&controller=test&task=testBoatModel&format=json",
	                data: {kind: '<?php echo $kind; ?>', make: $('[name="boatmake"]').val()},
	                success: function(data){
	                	// console.log(data);
	                	elem.children('option').remove('option.mod_dynamic');
	                    $.each(data, function(k,v) {
	                    	elem.append('<option value="'+k+'" class="mod_dynamic">'+v+'</option>')
	                    })
	                    elem.trigger('change');
	                },
	                error: function(data, status, error) {
	                	console.log('error');
	                },
	                dataType: 'json'
            	});
	  		})

	  		$('#startPage select').on('change', function() {
	  			if($('[name="boatmake"]').val() == 0 || $('[name="boatmodel"]').val() == 0) {
                    $('#startPage .uk-button.confirm').prop('disabled', true);
                } else {
                    $('#startPage .uk-button.confirm').prop('disabled', false);
                }
            	if($('[name="boatmake"]').val() == 0) {
                    $('[name="boatmodel"]').prop('disabled',true);
                } else {
                	$('[name="boatmodel"]').prop('disabled', false);
                }
	  		})

	  		
	  		
	  	})
	 })

</script>


