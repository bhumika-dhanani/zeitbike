;(function(e){e.fn.visible=function(t,n,r){var i=e(this).eq(0),s=i.get(0),o=e(window),u=o.scrollTop(),a=u+o.height(),f=o.scrollLeft(),l=f+o.width(),c=i.offset().top,h=c+i.height(),p=i.offset().left,d=p+i.width(),v=t===true?h:c,m=t===true?c:h,g=t===true?d:p,y=t===true?p:d,b=n===true?s.offsetWidth*s.offsetHeight:true,r=r?r:"both";if(r==="both")return!!b&&m<=a&&v>=u&&y<=l&&g>=f;else if(r==="vertical")return!!b&&m<=a&&v>=u;else if(r==="horizontal")return!!b&&y<=l&&g>=f}})(jQueryIWD);
IWD.List = {
	timeoutRequest : null,
	stopCheckScroll : false,
	product : null,
	nextPage: 2,
	stopLoad: 0,
	
	init : function() {
		this.addToPreparedList();
		this.scrollAllProducts();
	},

	addToPreparedList: function(){
		$ji(document).on('click','.item-result', function(e){
			e.preventDefault();
			IWD.List.product = $ji(this).data('product');
			IWD.List.addToList();
		});
	},
	
	addToList: function(){
		$ji.post(IWD.App.config.addProductToListUrl, {"product":IWD.List.product}, IWD.List.response,'json')
	},
	
	response: function(response){
		if (typeof(response.error)=="undefined"){
			$ji('.b2b-selected-items').removeClass('hidden');
			$ji('.b2b-selected-items .table-body').html(response.content);
			
			IWD.Decorator.reInitTableScroll();
		}
	},
	
	
	/** LOGIC TO LOAD NEW PRODUCT FOR ALL PRODUCTS **/ 
	scrollAllProducts: function(){
		$ji(window).scroll(function(){
			if (IWD.List.stopLoad==1){
				return;
			}
			if ($ji('.b2b-reorder-order').length==0){
				return;
			}
			var backLink = $ji('.b2b-reorder-order #b2b-back-queue');

			if (backLink.visible() && IWD.List.stopCheckScroll==false){
				IWD.Decorator.lockBlock($ji('.b2b-selected-items'));
				IWD.List.stopCheckScroll=true;
				IWD.List.loadProducts();	
			}
		});
	},
	
	
	loadProducts: function (){		
		var form = $ji('#b2b-filter-all-products').serializeArray();
		form.push({name: "page", value: IWD.List.nextPage});
		$ji.post(IWD.App.config.loadProductPageUrl, form, IWD.List.parsePageResponse, 'json')
	},
	
	parsePageResponse: function(response){
		IWD.List.stopCheckScroll = false;
		IWD.Decorator.unLockBlock($ji('.b2b-selected-items'));
		if (typeof(response.content) !="undefined"){
			$ji(response.content).appendTo('#b2b-all-product');
			$ji(".b2b-table-scroll").getNiceScroll().hide();
			$ji(".b2b-table-scroll").getNiceScroll().show();
			$ji(".b2b-table-scroll").getNiceScroll().resize();
		}
	}, 
	
	parseResponseSortProduct: function(response){
		IWD.Decorator.unLockBlock($ji('.b2b-selected-items'));
		$ji('#quick-list-products').empty();
		$ji(response.content).appendTo('#quick-list-products');
		
	},
	
	parseResponseFilterProduct: function(response){
		IWD.Decorator.unLockBlock($ji('.b2b-selected-items'));
		if (typeof(response.content) !="undefined" ){
			$ji('#b2b-all-product').html(response.content);
			
		}
	},
	
	
	
	parseResponseSortQuick: function(response){
		IWD.Decorator.hideLoader();
		if (typeof(response.list) !="undefined" ){
			$ji('#quick-list-products').empty();
			$ji('#quick-list-products').html(response.list);
		}
		
	},
	
	parseResponseFilterQuick: function(response){
		IWD.Decorator.unLockBlock($ji('.b2b-selected-items'));
		if (typeof(response.list) !="undefined" ){
			$ji('#quick-list-products .table-body').empty();
			$ji('#quick-list-products .table-body').html(response.list);
		}
	},
	
	
	/** REORDER PAGE **/
	parseResponseSortReorder: function(response){
		IWD.Decorator.unLockBlock($ji('.b2b-selected-items'));
		$ji('#quick-list-products').empty();
		$ji(response.content).appendTo('#quick-list-products');
		
	},
	
	parseResponseFilterReorder: function(response){
		IWD.Decorator.unLockBlock($ji('.b2b-selected-items'));
		if (typeof(response.content) !="undefined" ){
			$ji('#b2b-reorder-table').html(response.content);
			
		}
	},
	
	
	
	
	parseResponseQuickListFilterSort: function(response){
		
		//queue
		if (pageType=='quick'){
			//queue
			if (typeof(response.list)!="undefined"){
				$ji('.b2b-selected-items .table-body').html(response.list);
				IWD.Decorator.reInitTableScroll();
			}			
		}
		
		if (pageType=='products'){
			//queue
			if (typeof(response.list)!="undefined"){
				$ji('#b2b-all-product').empty();
				$ji(response.list).appendTo('#b2b-all-product');					
			}			
		}
		
		
		IWD.Decorator.reInitTableScroll();
		IWD.Decorator.hideLoader();
		
	}
	
	
};