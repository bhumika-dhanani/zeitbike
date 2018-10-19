IWD.QuickSearch = {
		
	timeoutRequest : null,
	
	init : function() {
		this.search();
	},
	/*  start */
	search : function() {
		$ji('#quick-search').keyup(function() {
			if ($ji(this).val()==''){
				if (IWD.QuickSearch.timeoutRequest != null) {
					clearTimeout(IWD.QuickSearch.timeoutRequest);
					
				}
				if (IWD.App.xhr !=null){
					IWD.App.xhr.abort();
				}
				$ji('.quick-search-block').addClass('hidden');
			}else{
				IWD.QuickSearch.initRequest();
			}
		});
		$ji('.b2b-wrapper .quick-order-search').click(function(e) {
			e.preventDefault();
			if ($ji('#quick-search').val()==''){
				if (IWD.QuickSearch.timeoutRequest != null) {
					clearTimeout(IWD.QuickSearch.timeoutRequest);
					
				}
				if (IWD.App.xhr !=null){
					IWD.App.xhr.abort();
				}
				$ji('.quick-search-block').addClass('hidden');
			}else{
				IWD.QuickSearch.initRequest();
			}
		});
		
		$ji('#b2b-quick-search-form').submit(function(e) {
			e.preventDefault();
			if ($ji('#quick-search').val()==''){
				if (IWD.QuickSearch.timeoutRequest != null) {
					clearTimeout(IWD.QuickSearch.timeoutRequest);
					
				}
				if (IWD.App.xhr !=null){
					IWD.App.xhr.abort();
				}
				$ji('.quick-search-block').addClass('hidden');
			}else{
				IWD.QuickSearch.initRequest();
			}
		});
		
		/*  end */
		
		
		
		$ji(document).click( function(e){
			 var item = $ji(e.target);
			 var parent1 = item.closest('.quick-search-result');
			 var parent2 = item.closest('.quick-order-search');	
		     if(parent1.length == 0 && parent2.length == 0){		    	 
		    	 $ji('.quick-search-block').addClass('hidden');
		    	 IWD.Decorator.hideDropScroll();
		     }			
		});
		
		$ji(window).on("touchstart", function(ev) {
		    var e = ev.originalEvent;
		    var item = $ji(e.target);
			var parent = item.closest('.quick-search-result');
	
		    if(parent.length == 0){		    	 
		    	$ji('.quick-search-block').addClass('hidden');
		    	IWD.Decorator.hideDropScroll();
		    }		
		});
		
	},

	initRequest : function() {
		if (IWD.QuickSearch.timeoutRequest != null) {
			clearTimeout(IWD.QuickSearch.timeoutRequest);
			
		}
		if (IWD.App.xhr !=null){
			IWD.App.xhr.abort();
		}
				
		$ji('.quick-search-block').removeClass('hidden');
		$ji('.b2b-search-loader').show();		
		IWD.QuickSearch.timeoutRequest = setTimeout(function() {
			IWD.App.xhr = $ji.post(IWD.App.config.quickSearchUrl, {
				"search" : $ji('#quick-search').val()
			}, IWD.QuickSearch.response);
		}, 600);
	},

	response : function(response) {
		try{
			var responseJson = $ji.parseJSON(response);
			if (typeof(responseJson.location)!="undefined"){
				setLocation(responseJson.location);
				return;
			}
		}catch(e){}
		
		$ji('#quick-search-block').html(response);	
		IWD.Decorator.initDropScroll();		
	}
};