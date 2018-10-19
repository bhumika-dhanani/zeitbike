;
$q = jQuery;
if (!window.hasOwnProperty('IWD')) {
		IWD = {};
};
IWD.Newspopup = {
	popupSelector: "#popup-newssubscribe",
	popupLinkClass: ".newsletter_popup",
	signinConfig : null,
	config : null,
	popupValidation: function(form){

		if(typeof iwdpopup != 'undefined') {
			iwdpopup.submit = function (form) {
				if (this.validator.validate()) {
					try {

						IWD.Newspopup.sendForm();

					} catch (e) {
					}
				}
			}.bind(iwdpopup);
		}
	},
					
	loadPopup: function(){
        IWD.Newspopup.hideLoaderBg();
		$q('.news_popup').height($q(window).height()-100);
		if ($q('.news_contaiter').width() < $q('.news_popup').width() )
			$q('.news_popup').css({'margin-right':'10px'});
        $q(IWD.Newspopup.popupSelector).show();
        $ji(IWD.Newspopup.popupSelector).modaliwd('show');

	},

	readCookie: function(name){
		var nameEQ = escape(name) + "=";
		var ca = document.cookie.split(';');
		for(var i=0;i < ca.length;i++) {
			var c = ca[i];
			while (c.charAt(0)==' ') c = c.substring(1,c.length);
			if (c.indexOf(nameEQ) == 0) return unescape(c.substring(nameEQ.length,c.length));
		}
	return null;
	},

	createCookie: function(name,value,days){
		if (days) {
			var date = new Date();
			date.setTime(date.getTime()+(days*24*60*60*1000));
			var expires = "; expires="+date.toGMTString();
		}
		else var expires = "";
		document.cookie = escape(name)+"="+escape(value)+expires+"; path=/";
	},
			
	popupInit: function(){
		if (typeof(iwdNewspopupConfig)!="undefined"){
			IWD.Newspopup.config = $q.parseJSON(iwdNewspopupConfig);
		}else{
			return;
		};
		
		$q(IWD.Newspopup.popupLinkClass).click(function(event){
			event.preventDefault();
			IWD.Newspopup.loadPopup();
		});
			
		if(typeof(popupNewsLinkClass)!='undefined' && popupNewsLinkClass != undefined)
		{
			if($q(popupNewsLinkClass).length)
			{
				$q(popupNewsLinkClass).click(function(event){
					event.preventDefault();
					IWD.Newspopup.loadPopup();
				});
			}
		}
        //button close
        $q(IWD.Newspopup.popupSelector).find('button.close').click(function(){
            $j(IWD.Newspopup.popupSelector).modal('hide');
            IWD.Newspopup.hideLoaderBg();
		});

        $q(IWD.Newspopup.popupSelector).find('div.clear_popup').click(function(){
        	 $j(IWD.Newspopup.popupSelector).modal('hide');
			IWD.Newspopup.hideLoaderBg();
		});

        //button start_shopping
		$q(IWD.Newspopup.popupSelector).find('button.start_shopping').click(function(){
			//$q(IWD.Newspopup.popupSelector).modal('hide');
			 $j(IWD.Newspopup.popupSelector).modal('hide');
			$j('body').css({'overflow':'auto','padding':'0px !important'});
			IWD.Newspopup.hideLoaderBg();
		});
        //button go_back
        $q(IWD.Newspopup.popupSelector).find('button.go_back').click(function(){
            IWD.Newspopup.clearPopup();
            IWD.Newspopup.hideLoaderBg();
        });

	},
	
	clearPopup: function(){
		$q(IWD.Newspopup.popupSelector).find('.news_popup').show();
		$q(IWD.Newspopup.popupSelector).find('.message_content').html("");
		$q(IWD.Newspopup.popupSelector).find('#popup_message').hide();
		$q(IWD.Newspopup.popupSelector).find('.message_title').hide();
		
	},
    showLoaderBg: function(){
    	$j('.bg_fonts').show();    
    },
    
    hideLoaderBg: function(){
        $j('.bg_fonts').hide();
    },

	sendForm: function(){
		IWD.Newspopup.showLoaderBg();
		var email = jQuery(IWD.Newspopup.popupSelector).find('#newsletter_input').val();
		$q.post(IWD.Newspopup.config.url + 'iwdpopup/index/subscribe',{"email":email}, IWD.Newspopup.parseResponseLoadPopup, 'json');
	},
	
	parseResponseLoadPopup: function(response){
		if (typeof(response.message!="undefined")){
			$q(IWD.Newspopup.popupSelector).find('.news_popup').hide();
			$q(IWD.Newspopup.popupSelector).find('#popup_message').show();
            IWD.Newspopup.hideLoaderBg();
			if(response.error)
				$q(IWD.Newspopup.popupSelector).find('.error_title').show().css('display','block');
			else
				$q(IWD.Newspopup.popupSelector).find('.success_title').show().css('display','block');

			$q(IWD.Newspopup.popupSelector).find('.message_content').append(response.message);

		}
	},
	/*COMPATIBILITY WITH IWD SIGN-IN EXTENSION */
	prepareLoginForm: function(){
		IWD.Signin.showLoader();
		$q.post(IWD.Newspopup.signinConfig.url + 'signin/json/load',{"block":"login"}, IWD.Newspopup.parseResponseLoad, 'json');
	}, 
				
	parseResponseLoad: function(response){
		IWD.Signin.hideLoader();
		if (typeof(response.id !="undefined")){
			var block = response.id; 
			var signin_html = $q("<div>" + response[block] + "</div>");
			signin_html.find('#login-form').remove();
			signin_html.find('h4').remove();
			signin_html.find('script').remove();
			signin_html.prepend('<h3 class="promo_text">Or Sign up with your...</h3>');
			$j('#newspopup-ajax-load').html(signin_html.html());
		}
	}
	/* END */
				
	};
				
	$q(document).ready(function(){


		IWD.Newspopup.popupInit();
		IWD.Newspopup.popupValidation();
		/*COMPATIBILITY WITH IWD SIGN-IN EXTENSION */
		if(typeof(IWD.Signin) != 'undefined' && IWD.Signin != undefined ){
			if (typeof(SigninConfig)!="undefined"){
				IWD.Newspopup.signinConfig = SigninConfig;
			}else{
				return;
			};
			IWD.Newspopup.prepareLoginForm();
		}
		/* END */
		
		if(!IWD.Newspopup.readCookie('Subscribe')){
			if(typeof(IWD.Newspopup.config.popupLoadDelay)=='undefined' || IWD.Newspopup.config.popupLoadDelay == undefined)
				IWD.Newspopup.config.popupLoadDelay = 3;
			setTimeout(function() {
				IWD.Newspopup.loadPopup();
			}, IWD.Newspopup.config.popupLoadDelay*1000);
			IWD.Newspopup.createCookie('Subscribe', 1);
		}
	});	