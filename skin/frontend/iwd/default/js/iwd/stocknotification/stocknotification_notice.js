;
if (!window.hasOwnProperty('IWD')) {
	IWD = {};
};

IWD.StockNotification = {
		
		requestUrl: null,
		currentBlock : null,
		container:null,
		blockSending:false,
		init: function(){
			jQuery('.request-notice').click(function(e){
				
				e.preventDefault();		
				IWD.StockNotification.sendRequestNotify(jQuery(this));
			});
			
			jQuery('.btn-notify').click(function(e){
				e.preventDefault();		
				jQuery('.modal-notify').hide();//hide all
				IWD.StockNotification.currentBlock = jQuery(this).data('id')
				jQuery('#modal-notify-'+IWD.StockNotification.currentBlock).fadeIn();
			});
			
			jQuery('a.close-notification').click(function(e){
				e.preventDefault();
				jQuery(this).closest('.modal-notify').hide();
			});
		},
		
		sendRequestNotify: function(button){
			IWD.StockNotification.container  = button.closest('.stock-notification');
			var email = IWD.StockNotification.container.find('input[name="email_notification"]').val();
			var product = IWD.StockNotification.container.find('[name="product_id"]').val();
			var parent = IWD.StockNotification.container.find('input[name="parent_id"]').val();
			
			if (!IWD.StockNotification.validateEmail(email)){
				
				IWD.StockNotification.container.find('.stock-notification-message').html('Please enter a valid email address.').addClass('error');
				return;
			}
			
			if(!IWD.StockNotification.blockSending) {
				IWD.StockNotification.blockSending = true;
				jQuery.post(IWD.StockNotification.requestUrl, {
					"email": email,
					"id": product,
					'parent': parent
				}, function (response) {
					IWD.StockNotification.applyError(response);
					IWD.StockNotification.applyResponse(response);
					IWD.StockNotification.blockSending = false;
				}, 'json');
			}
		}, 
		
		applyError: function(response){
			if (typeof(response.error)!='undefined' && response.error==true){
				IWD.StockNotification.container.find('.stock-notification-message').html(response.message).removeClass('success').addClass('error');
			}
			
		},
		
		applyResponse: function(response){
			if (typeof(response.error)!='undefined' && response.error==false){
				IWD.StockNotification.container.find('.stock-notification-header').hide();
				
				IWD.StockNotification.container.find('.stock-notification-message').html(response.message).removeClass('error').addClass('success');
				
				IWD.StockNotification.closeExistModal();
			}
			
		},
		
		validateEmail: function(email){  
			   var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;   
			   return emailPattern.test(email);  
		},
		
		closeExistModal:function(){
			if (jQuery('#modal-notify-'+IWD.StockNotification.currentBlock).length){
				setTimeout(function(){
					jQuery('#modal-notify-'+IWD.StockNotification.currentBlock).fadeOut();
				}, 2000);
			}
		}
		
		
		
};

jQuery(document).ready(function(){
		IWD.StockNotification.init();
});