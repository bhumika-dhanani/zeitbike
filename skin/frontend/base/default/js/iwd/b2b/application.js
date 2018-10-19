$ji = jQueryIWD;
var IWD = IWD||{};


/* MAIN APPLICATION - BASE FUNCTION IT'S START ADITIONAL MODULES */

IWD.App = {
		xhr : null,
		config: null,
		
		init: function(){
			if (IWD.App.config.extensionActive==1){
				if (typeof(IWD.Login)!="undefined"){
					IWD.Login.init();	
					IWD.Register.init();	
				}
				if (typeof(IWD.QuickSearch)!="undefined"){
					IWD.QuickSearch.init();
					IWD.B2BAjaxCart.init();
					IWD.List.init();
					IWD.Logout.init();
					IWD.Decorator.init();
					IWD.Filter.init();
					IWD.Download.init();
					IWD.Stock.init();
				}
			}
		}
}

$ji(document).ready(function(){
	if (typeof(IWDB2BConfig) !="undefined"){
		IWD.App.config = $ji.parseJSON(IWDB2BConfig);
	}
	IWD.App.init();
});
