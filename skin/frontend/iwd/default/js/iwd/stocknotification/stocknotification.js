;
console.log()
if (!window.hasOwnProperty('IWD') || typeof(IWD)=='undefined') {
	IWD = {};
};

IWD.StockNotificationOriginal = {
		
		requestUrl: null,
		currentBlock : null,
		container:null,
		init: function(){
			
			jQuery('.btn-notify').click(function(e){				
				e.preventDefault();		
				IWD.StockNotificationOriginal.sendRequestNotify(jQuery(this));
			});
			
			jQuery('.btn-notify').click(function(e){
				
				e.preventDefault();		
				jQuery('.modal-notify').hide();//hide all
				IWD.StockNotificationOriginal.currentBlock = jQuery(this).data('id')
				jQuery('#modal-notify-'+IWD.StockNotificationOriginal.currentBlock).fadeIn();
			});
			
			jQuery('a.close-notification').click(function(e){
				e.preventDefault();
				jQuery(this).closest('.modal-notify').hide();
			});
		},
		
		sendRequestNotify: function(button){
			IWD.StockNotificationOriginal.container  = button.closest('.stock-notification');
			var email = IWD.StockNotificationOriginal.container.find('input[name="email_notification"]').val();
			var product = IWD.StockNotificationOriginal.container.find('[name="product_id"]').val();
			var parent = IWD.StockNotificationOriginal.container.find('input[name="parent_id"]').val();
			
			if (!IWD.StockNotificationOriginal.validateEmail(email)){
				
				IWD.StockNotificationOriginal.container.find('.stock-notification-message').html('Please enter a valid email address.').addClass('error');
				return;
			}
			
			
			jQuery.post(IWD.StockNotificationOriginal.requestUrl,{"email":email, "id":product,'parent':parent}, function(response){
				IWD.StockNotificationOriginal.applyError(response);
				IWD.StockNotificationOriginal.applyResponse(response);
			},'json');

		}, 
		
		applyError: function(response){
			if (typeof(response.error)!='undefined' && response.error==true){
				IWD.StockNotificationOriginal.container.find('.stock-notification-message').html(response.message).removeClass('success').addClass('error');
			}
			
		},
		
		applyResponse: function(response){
			if (typeof(response.error)!='undefined' && response.error==false){
				IWD.StockNotificationOriginal.container.find('.stock-notification-header').hide();
				
				IWD.StockNotificationOriginal.container.find('.stock-notification-message').html(response.message).removeClass('error').addClass('success');
				
				IWD.StockNotificationOriginal.closeExistModal();
			}
			
		},
		
		validateEmail: function(email){  
			   var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;   
			   return emailPattern.test(email);  
		},
		
		closeExistModal:function(){
			if (jQuery('#modal-notify-'+IWD.StockNotificationOriginal.currentBlock).length){
				setTimeout(function(){
					jQuery('#modal-notify-'+IWD.StockNotificationOriginal.currentBlock).fadeOut();
				}, 2000);
			}
		}
		
		
		
};

jQuery(document).ready(function(){
	
		IWD.StockNotificationOriginal.init();
});