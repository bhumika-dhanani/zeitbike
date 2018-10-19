IWD.Login = {
		
		init: function(){
			if (IWD.App.config.currentStore==IWD.App.config.wholesaleStore){
				this.initTrigger(); 
				this.triggerLogin();
				this.triggerRegister();
				this.triggerRegisterSubmit();
				if (IWD.App.config.login==1){
					var options = {"backdrop":"static"};
					$ji('#b2b-signin-modal').modaliwd(options);
				}
			}
			
		},
		
		
		initTrigger: function(){
            $ji('.b2b-signin-modal').off('click');
			$ji('.b2b-signin-modal').click(function(e){
				e.preventDefault();
				IWD.Login.checkIsLogin();	
				
			});
		},
		
		checkIsLogin: function(){
			
			$ji.post(IWD.App.config.isLoginUrl,{},function(response){
				
				if (response.isLogin!='false'){
					setLocation(response.isLogin);					
				}else{
					var options = {"backdrop":"static"};
					$ji('#b2b-signin-modal').modaliwd(options);
				}
			}, 'json');
			
			
		}, 
		
		triggerLogin: function(){
			$ji(document).off('click','.signin #signin-login');
			$ji(document).on('click','.signin #signin-login',function(e){
				e.preventDefault();
				$ji('.signin #login-form').submit();
			});
			$ji(document).on('submit', '.signin #login-form', function(event){
				IWD.Decorator.showLoader();
				event.preventDefault();
				var form = $ji('.signin #login-form').serializeArray();
				$ji.post(IWD.App.config.signInUrl, form, IWD.Login.parseLoginResponse, 'json');
			});
		},
		
		parseLoginResponse: function(response){
			IWD.Decorator.hideLoader();
			if (response==null){return;}
			
			if (typeof(response.error) !="undefined" && response.error==1){
				//if error show error message				
				$ji('#signin-error').remove();
				$ji('<div />').attr('id','signin-error').addClass('signin-error').html(response.message).insertBefore('.signin #login-form'); 
			}

			if (typeof(response.linkAfterLogin)!="undefined"){
				
				if (typeof(response.message)!="undefined"){
					//show message and redirect to url after 2.5s;
					setTimeout(function(){
						setLocation(response.linkAfterLogin);
					}, 2500);
				}else{					
					//just redirect to url
					setTimeout(function(){
						setLocation(response.linkAfterLogin);
					}, 500);					
				}
				
			}
		},
				
		triggerRegister: function(){
			$ji('#b2b-forgot-password').click(function(e){
				e.preventDefault();
				$ji('#b2b-forgot-form').removeClass('hidden');
				$ji('#b2b-login-form').addClass('hidden');
			});
			
			$ji('#back-link-login').click(function(e){
				e.preventDefault();
				$ji('#b2b-forgot-form').addClass('hidden');
				$ji('#b2b-login-form').removeClass('hidden');
				$ji('#signin-error').remove();
			});
		},
				
		triggerRegisterSubmit: function(){
			$ji('#form-validate-forgot').submit(function(e){
				e.preventDefault();
				var dataForm = new VarienForm('form-validate-forgot', true);
				if (dataForm.validator.validate()){
					IWD.Decorator.showLoader();
					var form = $ji('#form-validate-forgot').serializeArray();
					$ji.post(IWD.App.config.forgotPasswordUrl, form, IWD.Login.parseForgotPasswordResponse, 'json');
				}
				
			});
		},
		
		
		parseForgotPasswordResponse: function (response){
			IWD.Decorator.hideLoader();
			$ji('#signin-error').remove();
			if (typeof(response.error)!="undefined"){
				$ji('<div />').attr('id','signin-error').addClass('signin-error').html(response.message).insertBefore('#form-validate-forgot'); 
			}
				
			if (typeof(response.link)!="undefined"){
				$ji('<div />').attr('id','signin-error').addClass('signin-error').html(response.link).insertBefore('#form-validate-forgot'); 
			}
			
			
		},
};

IWD.Logout = {
		
		init: function(){
			this.initTrigger(); 		
		},
		
		initTrigger: function(){
			$ji('.b2b-signout-modal').click(function(e){
				e.preventDefault();
				var options = {"backdrop":"static"};
				$ji('#b2b-signin-modal').modaliwd(options);
			});
		}
		
};
