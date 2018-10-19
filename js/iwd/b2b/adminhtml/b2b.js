$j = jQueryIWD;
var IWD = IWD||{};
IWD.App = {
		init: function(){
			IWD.System.init();
			IWD.Category.init();
			IWD.Product.init();
		}
}

IWD.System = {
		init: function(){
			if ($j('#row_b2b_default_info').length){
				//todo start logic
				this.updateType();
				this.initUpdateType();
			}
		},
		
		initUpdateType: function(){
			$j('#b2b_store_type').change(function(){
				IWD.System.updateType();
			})
		},
		
		updateType: function(){
			var val = $j('#b2b_store_type').val();
			
			if (val==0){
				
				$j('#row_b2b_store_store').hide();
				$j('#row_b2b_store_name').hide();
				$j('#row_b2b_store_code').hide();
				$j('#row_b2b_store_cagegories').hide();
			}
			
			if (val==1){
				
				$j('#row_b2b_store_store').show();
				$j('#row_b2b_store_name').hide();
				$j('#row_b2b_store_code').hide();
				$j('#row_b2b_store_cagegories').hide();
			}
			if (val==2){
				
				$j('#row_b2b_store_store').hide();
				$j('#row_b2b_store_name').show();
				$j('#row_b2b_store_code').show();
				$j('#row_b2b_store_cagegories').show();
			}
			$j('#row_b2b_store_type').hide();
		}
}

IWD.Category = {
		init: function(){
			if (typeof(collection)!="undefined"){
				collection = $j.parseJSON(collection); 
				if (collection.totalRecords!=0){
					$j.each(collection.items, function(index, value){
						
						var $object = $j('.option-row:last').clone();
						$object.insertAfter('.option-row:last').show();;
						$j('.option-row:last input').val(value.name).attr('name','name[exist][' + value.entity_id + ']');
					})
				}
			}
			$j('#add_new_option_button').click(function(e){
				e.preventDefault();
				var $object = $j('.option-row:last').clone();
				$object.insertAfter('.option-row:last').show();
				$j('.option-row:last input').val('').attr('name','name[new][]');;
			});
			
			$j(document).on('click','.delete-option',function(e){
				e.preventDefault();
				$tr = $j(this).closest('tr').remove();
			});
		}
}


IWD.Product = {
		init: function(){
			$j(document).on('change', '#related_files_grid_table tr .checkbox', function(e){
				e.preventDefault();				
				var $prev = $j(this).parent();
				if (!$prev.hasClass('fa-spinner')){
					$j(this).wrap('<i class="fa fa-spinner fa-spin"></i>');					
					IWD.Product.save( $j(this).val(),  $j(this).prop('checked'));	
				}
			});
		}, 
		
		save: function(value, status){
			$j.post(b2bSaveUrl, {"form_key":$j('input[name="form_key"]').val(),"file":value, "status":status}, function(response){
				if (response.file!="undefined"){
					$j('#related_files_grid_table tr .checkbox').each(function(){
						if ($j(this).val()==response.file){
							var $prev = $j(this).parent();
							if ($prev.hasClass('fa-spinner')){
								$j(this).unwrap();
							}
						}
					});
				}
			}, 'json');
		}
		
}

$j(document).ready(function(){
	IWD.App.init();
});