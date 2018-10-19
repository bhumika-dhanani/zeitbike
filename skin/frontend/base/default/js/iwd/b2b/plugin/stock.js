IWD.Stock = {

	config : null,

	init : function() {
		this.changeSuperAttribute();
		this.bundleProduct();
	},

	applyMethod : function() {
		if (typeof(spConfig)=="undefined"){
			return;
		}	
		spConfig.reloadStock = function() {
			for ( var i = this.settings.length - 1; i >= 0; i--) {
				var selected = this.settings[i].options[this.settings[i].selectedIndex];
				if (selected.config) {
					var allowedProducts = selected.config.allowedProducts;

					if (allowedProducts.length == 1 && selected.config.stock) {
						var productStock = parseInt(selected.config.stock[allowedProducts[0]]['qty']);
						var productMessage = selected.config.stock[allowedProducts[0]]['message'];
						if (productMessage!=''){							
							$ji(productMessage).insertBefore('.b2b-modal-action');
						}else{
							$ji('.b2b-product-message').remove();
						}
						if (productStock == 0) {
							$ji('.b2b-option-available span').removeClass('hidden');
							$ji('#b2b-current-stock').html('out of stock');
						} else if (productStock >= 1) {
							$ji('.b2b-option-available span').removeClass(
									'hidden');
							$ji('#b2b-current-stock').html(productStock);
						} else {
							$ji('.b2b-option-available span').addClass('hidden');
							$ji('#b2b-current-stock').empty();
						}
					}
				}else{
					$ji('.b2b-option-available span').addClass('hidden');
					$ji('#b2b-current-stock').empty();
				}
			}
		};
	},

	changeSuperAttribute : function() {
		$ji(document).on('change', '.super-attribute-select',function(){
			spConfig.reloadStock();
		});
	},
	
	bundleProduct: function(){
		
		
		//selectbox
		$ji(document).on('change', 'form.bundle select', function(e){
			e.preventDefault();
			var id = $ji(this).data('id');
			if ($ji(this).val()!=""){
				var qty = $ji(this).find('option:selected').data('qty');
				$ji('#bdb-av-'+id).show();
				$ji('#bdb-av-'+id + ' strong').html(qty);
				
				var message = $ji(this).find('option:selected').data('message');
				
				if (message!=''){
					$ji('#bdb-av-m-'+id).show();
					$ji('#bdb-av-m-'+id).html(message);
				}else{
					$ji('#bdb-av-m-'+id).hide();
				}
			}else{
				$ji('#bdb-av-'+id).hide();
				$ji('#bdb-av-m-'+id).hide();
			}
		});
		
		//radio buttons
		$ji(document).on('change', 'form.bundle .radio', function(e){
			e.preventDefault();			
			var id = $ji(this).data('id');			
			if ($ji(this).val()!=""){
				var parent = $ji(this).closest('.options-list');
				parent.find('.b2b-product-available').hide();
				var qty = $ji(this).data('qty');
				$ji('#bdb-av-'+id).show();
				$ji('#bdb-av-'+id + ' strong').html(qty);
				
				var message = $ji(this).data('message');
				
				if (message!=''){
					$ji('#bdb-av-m-'+id).show();
					$ji('#bdb-av-m-'+id).html(message);
				}else{
					$ji(this).closest('.options-list').find('.b2b-product-available-message').hide();
				}
				
			}else{
				$ji(this).closest('.options-list').find('.b2b-product-available').hide();	
				$ji(this).closest('.options-list').find('.b2b-product-available-message').hide();	
			}
		});
		
		//checkbox buttons
		$ji(document).on('change', 'form.bundle .checkbox', function(e){
			e.preventDefault();			
			var id = $ji(this).data('id');
			if ($ji(this).prop('checked')){				
				var qty = $ji(this).data('qty');
				$ji('#bdb-av-'+id).show();
				$ji('#bdb-av-'+id + ' strong').html(qty);
				
				var message = $ji(this).data('message');
				
				if (message!=''){
					$ji('#bdb-av-m-'+id).show();
					$ji('#bdb-av-m-'+id).html(message);
				}else{
					$ji('#bdb-av-m-'+id).hide();
				}
				
			}else{
				$ji('#bdb-av-'+id).hide();
				$ji('#bdb-av-m-'+id).hide();
			}
		});
		
	}
};