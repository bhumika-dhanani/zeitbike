IWD.B2BAjaxCart = {
		
		timeoutRequest: null,
		qty:null,
		product:null,
		quoteId:null,
		currentProduct:null,
		reload: false,
		init: function(){
			this.initClose();
			this.loadProductByType();
			this.initUpdateQty();
			this.initRemoveCart();
			this.initPreviewProduct();
			
			this.initAddProduct();
			this.initClearCart();
			
		}, 
		
		loadProductByType: function(){
			$ji(document).on('click', '.b2b-load-product', function(e){
				e.preventDefault();
				
				IWD.B2BAjaxCart.currentProduct = $ji(this).data('id');
				
				IWD.B2BAjaxCart.pullProduct(IWD.B2BAjaxCart.currentProduct);
			});
		},
		
		
		pullProduct: function(id){
			IWD.Decorator.showLoader();
			$ji.post(IWD.App.config.viewProduct, {"id":id,"b2b":true}, IWD.B2BAjaxCart.responseView, 'json');
		},
		
		responseView: function(response){		
			
			if (typeof(response.location)!="undefined"){
				setLocation(response.location);
				return;
			}
			
			IWD.Decorator.hideLoader();
			IWD.B2BAjaxCart.showProductDialog();
			if (typeof(response.content)!="undefined"){
				$ji('#b2b-product-add').empty().html(response.content);
			}
			
		},
		
		
		showProductDialog: function(){			
			if (!$ji('#b2b-ajaxcart-modal').is(':visible')){
				
				var options = {"backdrop":true};
				$ji('#b2b-ajaxcart-modal').modaliwd(options);
			}
		},
		
		initClose: function(){
			$ji('.b2b-dialog-modal .close, .b2b-dialog-overlay, .back-close').click(function(e){
				e.preventDefault();
				$ji('.b2b-product-dialog').removeClass('b2b-dialog-show');
			});
		},
		
		
		initUpdateQty: function(){
			
			
			/** ADD OR UPDATE QTY IN QUICK LIST BLOCK **/
			$ji(document).on('keyup', '.b2b-quick-qty', function(){
				
				IWD.B2BAjaxCart.product = $ji(this).data('id'); 
				IWD.B2BAjaxCart.qty = $ji(this).val();	
				IWD.B2BAjaxCart.quoteId = $ji(this).data('quote-id');
				IWD.B2BAjaxCart.status = $ji(this).data('status');
				IWD.B2BAjaxCart.item= $ji(this).data('item');
				var action = $ji(this).data('action');
				
				
				
				//add product to shopping cart
				if (action=='add'){
					
					if (IWD.B2BAjaxCart.timeoutRequest != null) {
						clearTimeout(IWD.B2BAjaxCart.timeoutRequest);						
					}
					
					IWD.B2BAjaxCart.timeoutRequest = setTimeout(function() {
						IWD.Decorator.lockBlock($ji('.b2b-selected-items'));
						$ji.post(IWD.App.config.addProductUrl, {
							"product" : IWD.B2BAjaxCart.product,
							"qty" : IWD.B2BAjaxCart.qty,
							"part" : pageType,
							"status":IWD.B2BAjaxCart.status,
							"item":IWD.B2BAjaxCart.item,
							"quote":IWD.B2BAjaxCart.quoteId
						}, IWD.B2BAjaxCart.responseUpdateQty, 'json');
						
					}, 600);
					
				}
				
				//update qty for this product
				if (action=='update'){
					if (IWD.B2BAjaxCart.timeoutRequest != null) {
						clearTimeout(IWD.B2BAjaxCart.timeoutRequest);						
					}
					
					IWD.B2BAjaxCart.timeoutRequest = setTimeout(function() {
						IWD.Decorator.lockBlock($ji('.b2b-selected-items'));
						$ji.post(IWD.App.config.updateQtyUrl, {
							"product" : IWD.B2BAjaxCart.product,
							"qty" : IWD.B2BAjaxCart.qty,
							"quote":IWD.B2BAjaxCart.quoteId,
							"part" : pageType
						}, IWD.B2BAjaxCart.responseUpdateQty, 'json');
						
					}, 600);
				}
				
			});
			
			
			/** UPDATE QTY IN SHOPPING CART BLOCK **/
			$ji(document).on('keyup', '.b2b-shopping-cart input', function(){
				
				IWD.B2BAjaxCart.qty = $ji(this).val();	
				IWD.B2BAjaxCart.quoteId = $ji(this).data('quote-id'); 
				
				if (IWD.B2BAjaxCart.timeoutRequest != null) {
					clearTimeout(IWD.B2BAjaxCart.timeoutRequest);						
				}
				
				IWD.B2BAjaxCart.timeoutRequest = setTimeout(function() {
					IWD.Decorator.lockBlock($ji('#b2b-shopping-cart'));
					$ji.post(IWD.App.config.updateQtyUrl, {
						"qty" : IWD.B2BAjaxCart.qty,
						"quote":IWD.B2BAjaxCart.quoteId,
						"part" : pageType
					}, IWD.B2BAjaxCart.responseUpdateQty, 'json');
					
				}, 600);
				
				
			});
		},
		
		responseUpdateQty: function(response){
	
			if (typeof(response.error)!="undefined"){
				if (response.error==true){
					alert(response.message);
				}
			}
			
			//shopping cart
			if (typeof(response.cart)!="undefined"){
				$ji('#b2b-shopping-cart').replaceWith(response.cart);
			}
			
			if (typeof(response.footer)!="undefined"){
				$ji('.b2b-sticky-footer').replaceWith(response.footer);
			}
			
			
			if (pageType=='quick'){
				//queue
				if (typeof(response.list)!="undefined"){
					$ji('.b2b-selected-items .table-body').html(response.list);
				}			
			}
			
			if (pageType=='products'){
				//queue
				if (typeof(response.list)!="undefined"){
					$ji('#b2b-all-product').empty();
					$ji(response.list).appendTo('#b2b-all-product');
				}			
			}
			
			
			if (pageType=='prev'){
				//queue
				if (typeof(response.list)!="undefined"){
					$ji('#b2b-reorder-table').empty();
					$ji('#b2b-reorder-table').html(response.list);					
				}			
			}
			
			IWD.Decorator.unLockBlock($ji('.b2b-selected-items'));
			IWD.Decorator.unLockBlock($ji('#b2b-shopping-cart'));
			IWD.Decorator.reInitTableScroll();
		},
		
		
		/** remove item from shopping cart **/
		initRemoveCart: function(){
			$ji(document).on('click', '#b2b-shopping-cart .btn-remove', function(e){
				e.preventDefault();				
				var url = $ji(this).prop('href');
				var id = IWD.B2BAjaxCart.getShoppingItemId(url);
				if (id==false){
					setLocation(url);
					return;
				}				
				IWD.Decorator.showLoader();	
				$ji.post(IWD.App.config.removeItemCartUrl,{"id":id, "part" : pageType}, IWD.B2BAjaxCart.parseResponseRemoveItem,'json');
				
			});
		},
		
		getShoppingItemId: function(url){
			var re1='.*?',re2='(cart)', re3='.*?',re4='(delete)', re5='.*?',re6='(id)',re7='.*?',re8='(\\d+)';
			var p = new RegExp(re1+re2+re3+re4+re5+re6+re7+re8,["i"]);
			var m = p.exec(url);
			if (m != null){		      
				var int1=m[4];
				return int1;
			}
			return false;
		},
		
		
		//TODO - DUPLICATE IWD.AJAXCART.responseUpdateQty - FIX IT AFTER COMPLETE DEVELOPERMENT
		parseResponseRemoveItem: function(response){

			//shopping cart
			if (typeof(response.cart)!="undefined"){
				$ji('#b2b-shopping-cart').replaceWith(response.cart);
			}
			
			if (typeof(response.footer)!="undefined"){
				$ji('.b2b-sticky-footer').replaceWith(response.footer);
			}
			
			//queue
			if (pageType=='quick'){
				//queue
				if (typeof(response.list)!="undefined"){
					$ji('.b2b-selected-items .table-body').html(response.list);
				}			
			}
			
			if (pageType=='products'){
				//queue
				if (typeof(response.list)!="undefined"){
					$ji('#b2b-all-product').empty();
					$ji(response.list).appendTo('#b2b-all-product');
				}			
			}
			
			if (pageType=='prev'){				
				if (typeof(response.list)!="undefined"){
					$ji('#b2b-reorder-table').empty();
					$ji(response.list).appendTo('#b2b-reorder-table');					
				}			
			}
			
			IWD.Decorator.reInitTableScroll();
			IWD.Decorator.hideLoader();
		},
		
		
		initAddProduct: function(){
			$(document).on('submit', '#product_addtocart_form_modal', function(e){
				e.preventDefault();
				var productForm = new VarienForm('product_addtocart_form_modal');
				
				if(productForm.validator && productForm.validator.validate()){			
					IWD.Decorator.showLoader();
					var formData = $ji('#product_addtocart_form_modal').serializeArray();
					formData.push({"name":"part", "value":pageType});
					IWD.B2BAjaxCart.reload = true;
					$ji.post(IWD.App.config.addProductUrl, formData, IWD.B2BAjaxCart.parseResponseAddProduct, 'json');					
			    }
				
			});
		},
		
		
		parseResponseAddProduct: function(response){
			//shopping cart
			if (typeof(response.cart)!="undefined"){
				$ji('#b2b-shopping-cart').replaceWith(response.cart);
			}
			
			if (typeof(response.footer)!="undefined"){
				$ji('.b2b-sticky-footer').replaceWith(response.footer);
			}
			
			//queue
			if (pageType=='quick'){				
				if (typeof(response.list)!="undefined"){
					$ji('.b2b-selected-items .table-body').html(response.list);
					IWD.Decorator.reInitTableScroll();
				}			
			}
			//all products
			if (pageType=='products'){
				
				if (typeof(response.list)!="undefined"){
					$ji('#b2b-all-product').empty();
					$ji(response.list).appendTo('#b2b-all-product');					
				}			
			}
			
			if (pageType=='prev'){				
				if (typeof(response.list)!="undefined"){
					$ji('#b2b-reorder-table').empty();
					$ji(response.list).appendTo('#b2b-reorder-table');					
				}			
			}
			
		
			IWD.Decorator.reInitTableScroll();
			
			
			if (IWD.B2BAjaxCart.reload == true){
			//reload product page
				IWD.B2BAjaxCart.pullProduct(IWD.B2BAjaxCart.currentProduct);
				return;
			}
			IWD.B2BAjaxCart.reload = false;
			IWD.Decorator.hideLoader();
			
		},
		
		
		
		initClearCart: function(){
			$ji(document).on('click', '#b2b-clear-cart', function(e){
				e.preventDefault();
				IWD.Decorator.showLoader();				
				$ji.post(IWD.App.config.clearCartUrl, {"part" : pageType}, IWD.B2BAjaxCart.parseResponseRemoveItem, 'json');
			})
								
		},
		
		initPreviewProduct: function(){
			$ji(document).on('click', '.table-body .name-product h3, .table-body .product-sku, .table-body .reorder-product h3, .table-body .reorder-sku', function(e){
				e.preventDefault();
				var id = $ji(this).data('id');
				
				if (id==undefined){
					return ;
				}
				IWD.Decorator.showLoader();
				
				$ji.post(IWD.App.config.previewProductUrl, {"product": id}, function(response){
					
					if (typeof(response.location)!="undefined"){
						setLocation(response.location);
						return;
					}
					
					IWD.Decorator.hideLoader();
					$ji('#b2b-product-view').html(response.content);
					var options = {"backdrop":true};
					$ji('#b2b-view-modal').modaliwd(options);
					
					//IWD.Decorator.initDescriptionScroll();
				}, 'json');
			});
		},
		
		
		
		
};