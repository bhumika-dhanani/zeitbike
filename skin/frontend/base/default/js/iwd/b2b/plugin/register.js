IWD.Register = {
		init: function(){
			this.initSameAsShipping()
		}, 
		
		initSameAsShipping: function(){
			$ji('#same_as_shipping').click(function(){
				if ($ji(this).prop('checked')==true){
					$ji('#b2b-billing-form').fadeOut('fast');
					$ji('#billing_same_as_shipping').val('1');
					$ji(this).find('input').each(function(){
						$ji(this).removeClass('required-entry')
					});
				}else{
					$ji('#b2b-billing-form').fadeIn();
					$ji('#billing_same_as_shipping').val('fast');
					$ji(this).find('input').each(function(){
						$ji(this).addClass('required-entry')
					});
				}
			})
		}
		
		
};