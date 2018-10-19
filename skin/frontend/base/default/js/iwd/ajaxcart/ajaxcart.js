$j = jQuery;
var IWD=IWD||{};
IWD.AjaxCart = {
		config:null
};
IWD.AjaxCart.Processor = {
		
		init: function(){
		
			this.initShowDialog();
			this.initSubmitModalForm();
			this.initSubmitFormProductPage();
			this.initRemoveItemShoppingCart();
			
			this.initAdditionalItems();
		},
		
		initShowDialog: function(){
			if (typeof(IWD.ES)!="undefined"){
				IWD.ES.Plugin.event('viewCheckout', IWD.AjaxCart.Processor.openCheckout);
			}
			//standart buttons
			var clickable = $j('.btn-cart, .see-options-button, .buy-now').not('.my-wishlist .btn-cart, #product_addtocart_form .btn-cart, .block-reorder .btn-cart');
			
			$j(clickable).on('click', function(e){
				
				var link = $j(this).data('link');
				if (link==''){
					return;
				}
				e.preventDefault();
				IWD.AjaxCart.Decorator.showModal();
				IWD.AjaxCart.Processor.request(link);
			});
			
			//check all links where edit product url 
			
			//$j(document).on('click', 'a', function(e){
            //
			//	var link = $j(this).attr('href');
			//	if (link=='' || link=='javascript:void(0);' || link=='#' || typeof(link)=='undefined'){
			//		return;
			//	}
			//if ($j('#product_addtocart_form').length>0){
             //   setLocation($j(this).attr('url')); return;
            //}
            //
			//	if (link.indexOf('checkout/cart/configure') !=-1){
			//		e.preventDefault();
			//		IWD.AjaxCart.Decorator.showModal();
			//		IWD.AjaxCart.Processor.request(link, true);
			//	}
            //
			//});

		},
		
		
		initAdditionalItems: function(){
			if (IWD.AjaxCart.config.items!=''){
				alert(IWD.AjaxCart.config.items);
				$j(document).on('click', IWD.AjaxCart.config.items, function(e){
					e.preventDefault();
					var link = $j(this).attr('href');
					if (link==''){
						return;
					}
					
					IWD.AjaxCart.Decorator.showModal();
					IWD.AjaxCart.Processor.request(link);
				});
			}
		},
		
		
		/** request add to cart or view product **/		
		request: function(link, edit){
			if (typeof(edit)=="undefined"){
				edit = false;
			}
			var cart = IWD.AjaxCart.Processor.isShoppingCart();
			IWD.AjaxCart.config.reloadTimeoutId = setTimeout(IWD.AjaxCart.Processor.reloadPage, 15000);
			$j.post(link, {"aac":true,"cart":cart, "edit":edit}, IWD.AjaxCart.Processor.parseResponse, 'json');
		},
		
		reloadPage: function () {
			location.reload()
		},
		
		/**
		 * INIT SUBMIT FORM EVENT (MODAL FORM)
		 */
		initSubmitModalForm: function(){
			
			$j(document).on('submit', '#product_addtocart_form_modal', function(event){
				
				var modalProductForm = new VarienForm('product_addtocart_form_modal');
				if(modalProductForm.validator && modalProductForm.validator.validate()){
					
					if (!$j('#product_addtocart_form_modal input:file').length){
						event.preventDefault();
						IWD.AjaxCart.Processor.sendRequestFormAddToCart($j(this).prop('action'), $j('#product_addtocart_form_modal'));
					}
					
					
			    }else{
			    	event.preventDefault();
			    }
				
			});
		},
		
		
		/** REWRITE DEFAULT METHOD productAddToCartForm;**/
		initSubmitFormProductPage: function(){
			if (window.hasOwnProperty('productAddToCartForm')) {
				productAddToCartForm = {
						submit: function(button){
							var productForm = new VarienForm('product_addtocart_form');
							if(productForm.validator && productForm.validator.validate()){
								if (IWD.AjaxCart.Processor.isPaypalExpress(button.href)){
									productForm.submit();
									return;
								};								
								IWD.AjaxCart.Decorator.showModal();
								if (!$j('#product_addtocart_form input:file').length){
									IWD.AjaxCart.Processor.sendRequestFormAddToCart($j('#product_addtocart_form').prop('action'), $j('#product_addtocart_form'));
								}else{
									productForm.submit();
									return;
								}
						    }
						},
						
						submitLight: function(button, url){
							var productForm = new VarienForm('product_addtocart_form');
							 if(productForm.validator) {
								 var nv = Validation.methods;
								 delete Validation.methods['required-entry'];
								 delete Validation.methods['validate-one-required'];
								 delete Validation.methods['validate-one-required-by-name']; 
								
								 if (productForm.validator.validate()) {
									 if (IWD.AjaxCart.config.isLoggedIn==1){
										 IWD.AjaxCart.Decorator.showModal();
										 $j.post(IWD.AjaxCart.config.addToWishlistUrl, $j('#product_addtocart_form').serializeArray(), IWD.AjaxCart.Processor.parseResponse,'json');
									 }else{
										setLocation(url);
									 }
								 }
							 }
						}
				};
			};
			
		},
		
		/** CHECK IS PAYPAL BUTTON **/
		isPaypalExpress: function(url){
			var re1='((?:[a-z][a-z]+))', re2='.*?', re3='(express)';
			var p = new RegExp(re1+re2+re3,["i"]);
			var m = p.exec(url);
			if (m != null){
		          return true;
			}
			return false;
		},
		
		/** check if current page is shopping cart - for ajax reload page after add to cart */
		isShoppingCart: function(){
			var href = parent.location.href;
			if (href.indexOf('checkout/cart')!=-1){
				return true;
			}
			return false;
		},
		
		/**
		 * SEND AJAX REQUEST - SUBMITED FORM
		 */
		sendRequestFormAddToCart: function(url, $form){
			$j('.ajaxcart-ajax-loader').show();
			$j('#ajax-cart-modal').addClass('ajaxcart-show');
			var formData = $form.serializeArray();
			
			
			var cart = IWD.AjaxCart.Processor.isShoppingCart();
			
			formData.push({name: "aac", value: true,});
			formData.push({name: "cart", value: cart});
			$j.post(url, formData, IWD.AjaxCart.Processor.parseResponse, 'json');			
		},
		
		/** METHOD FOR REMOVE ITEM FROM SHOPPING CART **/
		initRemoveItemShoppingCart: function(){
			$j(document).on('click','.cart-table a.btn-remove', function(event){
				event.preventDefault();
				var url = $j(this).prop('href');
				var id = IWD.AjaxCart.Processor.getShoppingItemId(url);
				if (id==false){
					setLocation(url);
					return;
				}
				IWD.AjaxCart.Decorator.showModal();
				$j.post(IWD.AjaxCart.config.removeShoppingCartUrl,{id:id}, IWD.AjaxCart.Processor.parseResponse,'json');
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
		
		/** PARSE AJAX RESPONSE **/
		parseResponse:function(response){
			clearTimeout(IWD.AjaxCart.config.reloadTimeoutId);
			try {
				$j('.ajaxcart-ajax-loader').hide();
				$j('#ajaxcart-wrapper').removeAttr('style');
				/** check confirmation status **/
				if (typeof(response.confirmation) != "undefined" && response.confirmation !== false) {
					$j('#ajaxcart-wrapper').html(response.confirmation);
				}

				if (typeof(response.confirmation) != "undefined" && response.confirmation == false) {
					IWD.AjaxCart.Decorator.hideModal();
				}

				/** product view **/
				if (typeof(response.content) != "undefined") {
					$j('#ajaxcart-wrapper').html(response.content);
					IWD.AjaxCart.Decorator.fixHeight();
				}

				/** shopping cart **/
				if (typeof(response.shopping_cart) != "undefined") {
					if (typeof(response.confirmation) != "undefined" && response.confirmation !== false) {
						$j('#ajaxcart-wrapper').html(response.message);
					} else {
						IWD.AjaxCart.Decorator.hideModal();
					}

					$j('.cart').parent().html(response.shopping_cart);
					IWD.AjaxCart.Decorator.updateButton();
				}

				/** header - full update for correct display shopping cart **/
				if (typeof(response.header) != "undefined") {
					$j('.header .links').replaceWith(response.header);

				}
				$container = $j('#ajaxcart-wrapper');
				$container.animate({scrollTop: 0}, "fast");

				/** update top dropdown **/
				if (typeof(response.dropdown) != "undefined") {

					if (IWD.AjaxCart.config.useDefaultDropDown == true) {

						$j('.header-minicart').html(response.dropdown);

						var skipContents = $j('.skip-content');
						var skipLinks = $j('.skip-link');
						skipLinks.off('click');
						skipLinks.on('click', function (e) {
							e.preventDefault();

							var self = $j(this);
							var target = self.attr('href');

							// Get target element
							var elem = $j(target);

							// Check if stub is open
							var isSkipContentOpen = elem.hasClass('skip-active') ? 1 : 0;

							// Hide all stubs
							skipLinks.removeClass('skip-active');
							skipContents.removeClass('skip-active');

							// Toggle stubs
							if (isSkipContentOpen) {
								self.removeClass('skip-active');
							} else {
								self.addClass('skip-active');
								elem.addClass('skip-active');
							}
						});

						$j('#header-cart').on('click', '.skip-link-close', function (e) {
							var parent = $j(this).parents('.skip-content');
							var link = parent.siblings('.skip-link');

							parent.removeClass('skip-active');
							link.removeClass('skip-active');

							e.preventDefault();
						});

						setTimeout(function () {
							if (IWD.AjaxCart.config.showDropdown == true) {
								$j('.skip-cart').click();

							}
						}, 200);
						return;
					}

					$j('.es-top-cart').remove();
					$j(response.dropdown).insertAfter('#ajax-cart-modal');

					IWD.AjaxCart.Decorator.moveDropdown();

					setTimeout(function () {
						if (IWD.AjaxCart.config.showDropdown == 'true') {
							$j('.wrrapper-ajaxcart-dropdown').addClass('opened');
							$j('.skip-cart').click();

						}
					}, 200);

				}
			} catch (e) {
				location.reload();
			}
		},
		
};

IWD.AjaxCart.Decorator = {
	timer:null,
	init: function(){	
		this.updateButton();
		this.initActionDialog();
		this.initQtyChange();
		if (IWD.AjaxCart.config.useDefaultDropDown==false){
			this.moveDropdown();
		}
		this.clearPrice();
		this.removeOpen();	
		
		setTimeout(function(){
			IWD.AjaxCart.Decorator.fixHeight();
		}, 400);		
	},
	
	
	
	/** remove onclick from all buttons with class "btn-cart" **/
	updateButton: function(){
		$j('.btn-cart').not('.my-wishlist .btn-cart, #product_addtocart_form .btn-cart').each(function(){
			var value = $j(this).attr('onclick');
			if(value!=undefined) {
				if (value.indexOf('setLocation') != -1){
					value = value.replace("setLocation('", "");
					value = value.replace("')", "");
				
					$j(this).removeAttr('onclick').data('link', value);
				}
			}
		});
	},
	
	initActionDialog: function(){
		$j(document).on('click','.close-ajax-dialog, .ajaxcart-close-dialog, .ajaxcart-overlay', function(e){
			e.preventDefault();
			IWD.AjaxCart.Decorator.hideModal()
		});
		
	},
	
	/** show dialog **/
	showModal: function(){
		$j('#ajaxcart-wrapper').removeAttr('style');
		$j('#ajax-cart-modal').addClass('ajaxcart-show');
		$j('#ajaxcart-wrapper').empty();
		$j('.ajaxcart-ajax-loader').show();
	},
	
	/** hide dialog **/
	hideModal: function(){
		$j('#ajaxcart-wrapper').removeAttr('style');
		$j('#ajax-cart-modal').removeClass('ajaxcart-show');
	},
	
	/** decorate qty box **/
	decorateQty: function(){
        var width = $j(window).width();
		$j('.ajaxcart-modal input.qty').each(function(){
			$j(this).wrap('<div class="qty-block"></div>');

            $j(this).addClass('ajax-qty-input').addClass('left');

              /*if (width>480){
				$j(this).attr('readonly', 'readonly');
              }*/

            if (width<480) {
                $j(this).attr('readonly', 'readonly');
            }

            if(!$j(this).is(".qty-disabled")){
                $j('<div class="right qty-slider"><a class="inc"></a><a class="dec"></a></div>').insertAfter(this);
            }else{
                $j(this).closest('.qty-block').addClass('ajax-qty-width-small');
            }
		});
	},
	
	
	initQtyChange: function(){
		$j(document).on('click','.ajaxcart-modal .inc', function(){
			var parent  = $j(this).closest('.qty-block');
			var input = parent.find('.ajax-qty-input');
			var val = input.val();
			val = 	parseFloat(val);
			val = val+1;
			input.val(val);
		});
		
		$j(document).on('click','.ajaxcart-modal .dec', function(){
			var parent  = $j(this).closest('.qty-block');
			var input = parent.find('.ajax-qty-input');
			var val = input.val();
			val = 	parseFloat(val);
			val = val-1;
			if (val<1){
				return;
			}
			input.val(val);
		});
	},
	
	moveDropdown: function(){
		
		var $html = $j('#wrrapper-ajaxcart-dropdown').html();
		$j('#wrrapper-ajaxcart-dropdown').remove();
		var parent = $j('.top-link-cart').parent();
		parent.addClass('wrrapper-ajaxcart-dropdown')
		$j($html).insertAfter('.top-link-cart');
		
	},
	
	fixHeight: function(){
		var heightDialog = $j('#ajaxcart-wrapper').height(),
		heightViewport = $j(window).height();
		
		if (heightDialog>heightViewport){
			var height = heightViewport-150;
			$j('#ajaxcart-wrapper').attr('style','overflow:hidden;overflow-y:auto; height:'+height+'px');
		}
	},
	
	clearPrice: function(){
		
			$j('.price-box span').not('#product_addtocart_form .price-box span').removeAttr('id');
		
	},
	
	removeOpen: function(){
		$j(document).on('mousemove', '.es-top-cart',function(){
			$j('.wrrapper-ajaxcart-dropdown').removeClass('opened');
		})
	}
	
}

$j(document).ready(function(){
	if (typeof(AjaxCartConfig)!='undefined'){
		IWD.AjaxCart.config = $j.parseJSON(AjaxCartConfig);
		if (IWD.AjaxCart.config.enabled==true){
			IWD.AjaxCart.Decorator.init();
			IWD.AjaxCart.Processor.init();
		}
	}
    /************Max******************/
    $j(document).on('click','#ajaxcart-wrapper #recurring_start_date_trig', function(){
       console.log('click');
        $j('#ajaxcart-wrapper .calendar').remove();
        $j('.calendar').appendTo($j('#ajaxcart-wrapper'));

    });
});