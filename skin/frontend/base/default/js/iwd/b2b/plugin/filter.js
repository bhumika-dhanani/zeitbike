IWD.Filter = {
		timeoutRequest:null, 
		init: function(){
			
			this.sortQuickList();
			this.filterQuickList();
			
			this.sortAllProducts()
			this.filterAllProducts();
			
			/* download popup*/
			this.sortDownloadPopup();
			this.filterDownloadPopup();
			
			/* products - download page */
			this.sortDownloadProduct();
			this.filterDownloadProduct();
			
			
			/* marketing - download page */
			this.sortDownloadMarketing();
			this.filterDownloadMarketing();
			
			
			/** reorder **/
			this.sortReorder()
			this.filterReorder();
			
			this.changeLimit();
		}, 
		
		
		/** SORT TABLE - QUICK LIST **/
		sortQuickList:function(){
			$ji(document).on('click','#b2b-filter a', function(e){
				e.preventDefault();
				var sort = $ji(this).data('sort'),
				attribute = $ji(this).data('attribute'),
				status = $ji(this).data('sort');
				if (status=='no-sortable'){
					return;
				}
				
				IWD.Decorator.showLoader();
				$ji.post(IWD.App.config.formatListUrl,{"attribute":attribute,"sort":sort}, IWD.List.parseResponseSortQuick,'json')
			});
		},
		
	
		/** FILTER TABLE - QUICK LIST **/
		filterQuickList: function(){
			$ji(document).on('keyup','#b2b-filter input', function(e){
				var form = $ji('#b2b-filter').serializeArray();				
				
				if (IWD.Filter.timeoutRequest != null) {
					clearTimeout(IWD.Filter.timeoutRequest);
				}
				IWD.Filter.timeoutRequest = setTimeout(function() {
					$ji.post(IWD.App.config.formatListUrl,form, IWD.List.parseResponseFilterQuick,'json')
				},600);
			});
		},
		//parseResponseQuickListFilterSort
		
		
		
		sortAllProducts:function(){
			$ji(document).on('click','#b2b-filter-all-products a', function(e){
				e.preventDefault();
				var sort = $ji(this).data('sort'),
				attribute = $ji(this).data('attribute'),
				status = $ji(this).data('sort');
				if (status=='no-sortable'){
					return;
				}
				
				var form = $ji('#b2b-filter-all-products').serializeArray();		
				form.push({name: "attribute", value: attribute});
				form.push({name: "sort", value: sort});
				form.push({name: "includePages", value: IWD.List.nextPage-1});
				//IWD.Decorator.lockBlock($ji('.b2b-selected-items')); //todo place loader to correct position 
				$ji.post(IWD.App.config.sortAllProduct, form, IWD.List.parseResponseSortProduct,'json')
			});
		},
		
		filterAllProducts: function(){
			$ji(document).on('keyup','#b2b-filter-all-products input', function(e){
				var form = $ji('#b2b-filter-all-products').serializeArray();				
				
				if (IWD.Filter.timeoutRequest != null) {
					clearTimeout(IWD.Filter.timeoutRequest);
				}
			
				IWD.Filter.timeoutRequest = setTimeout(function() {
					//IWD.Decorator.lockBlock($ji('.b2b-selected-items')); //todo place loader to correct position 
					$ji.post(IWD.App.config.loadProductPageUrl,form, IWD.List.parseResponseFilterProduct,'json')
				},600);
			});
		},
		
		
		
		
		/** SORT TABLE IN DOWNLOAD DROPDOWN **/
		sortDownloadPopup:function(){
			$ji(document).on('click','#b2b-filter-download-modal a:not(.prettycheckbox a)', function(e){
				e.preventDefault();
				IWD.Download.sort = $ji(this).data('sort'),
				IWD.Download.field = $ji(this).data('attribute');
				if (IWD.Download.sort=='no-sortable'){
					return;
				}
				
				var form = $ji('#b2b-filter-download-modal').serializeArray();		
				form.push({name: "attribute", value: IWD.Download.field});
				form.push({name: "sort", value: IWD.Download.sort});		
				form.push({name:"product", value: IWD.Download.product});
				IWD.Decorator.showLoader()
				$ji.post(IWD.App.config.downloadInfoUrl, form, IWD.Download.parseResponseSortProductPopp,'json')
			});
		},
		
		/** FILTER TABLE IN DOWNLOAD DROPDOWN **/
		filterDownloadPopup: function(){
			$ji(document).on('keyup','#b2b-filter-download-modal input:not(:checkbox)', function(e){
				var form = $ji('#b2b-filter-download-modal').serializeArray();				
				form.push({name: "attribute", value: IWD.Download.field});
				form.push({name: "sort", value: IWD.Download.sort});		
				form.push({name:"product", value: IWD.Download.product});
				
				if (IWD.Filter.timeoutRequest != null) {
					clearTimeout(IWD.Filter.timeoutRequest);
				}
			
				IWD.Filter.timeoutRequest = setTimeout(function() {
					IWD.Decorator.showLoader()
					$ji.post(IWD.App.config.downloadInfoUrl, form, IWD.Download.parseResponseSortProductPopp,'json')
				},600);
			});
			
			$ji(document).on('change','#b2b-filter-download-modal select', function(e){
				var form = $ji('#b2b-filter-download-modal').serializeArray();				
				form.push({name: "attribute", value: IWD.Download.field});
				form.push({name: "sort", value: IWD.Download.sort});		
				form.push({name:"product", value: IWD.Download.product});
				
				if (IWD.Filter.timeoutRequest != null) {
					clearTimeout(IWD.Filter.timeoutRequest);
				}
			
				IWD.Filter.timeoutRequest = setTimeout(function() {
					IWD.Decorator.showLoader()
					$ji.post(IWD.App.config.downloadInfoUrl, form, IWD.Download.parseResponseSortProductPopp,'json')
				},200);
			});
		},
		
		
		
		/** SORT PRODUCT TABLE ON DOWNLOAD PAGE **/
		sortDownloadProduct:function(){
			$ji(document).on('click','#b2b-filter-download-products a:not(.prettycheckbox a)', function(e){
				e.preventDefault();
				IWD.Download.sort = $ji(this).data('sort'),
				IWD.Download.field = $ji(this).data('attribute');
				if (IWD.Download.sort=='no-sortable'){
					return;
				}
				
				var form = $ji('#b2b-filter-download-products').serializeArray();		
				form.push({name: "attribute", value: IWD.Download.field});
				form.push({name: "sort", value: IWD.Download.sort});
				form.push({name: "page", value: IWD.Download.currentProductPage});
				form.push({name: "limit", value: $ji('#b2b-page-limit').val()});
				
				IWD.Decorator.showLoader();
				$ji.post(IWD.App.config.reloadDownloadProducts, form, IWD.Download.parseResponseSortProductPage,'json')
			});
		},
		
		
		/** FILTER TABLE IN DOWNLOAD DROPDOWN **/
		filterDownloadProduct: function(){
			$ji(document).on('keyup','#b2b-filter-download-products input:not(:checkbox)', function(e){
				var form = $ji('#b2b-filter-download-products').serializeArray();				
				form.push({name: "attribute", value: IWD.Download.field});
				form.push({name: "sort", value: IWD.Download.sort});						
				form.push({name: "page", value: 1});
				form.push({name: "limit", value: $ji('#b2b-page-limit').val()});
				if (IWD.Filter.timeoutRequest != null) {
					clearTimeout(IWD.Filter.timeoutRequest);
				}
			
				IWD.Filter.timeoutRequest = setTimeout(function() {
					IWD.Decorator.showLoader()
					$ji.post(IWD.App.config.reloadDownloadProducts, form, IWD.Download.parseResponseSortProductPage,'json')
				},600);
			});
			
			$ji(document).on('change','#b2b-filter-download-products select', function(e){
				var form = $ji('#b2b-filter-download-products').serializeArray();				
				form.push({name: "attribute", value: IWD.Download.field});
				form.push({name: "sort", value: IWD.Download.sort});						
				form.push({name: "page", value: 1});
				form.push({name: "limit", value: $ji('#b2b-page-limit').val()});
				if (IWD.Filter.timeoutRequest != null) {
					clearTimeout(IWD.Filter.timeoutRequest);
				}
			
				IWD.Filter.timeoutRequest = setTimeout(function() {
					IWD.Decorator.showLoader()
					$ji.post(IWD.App.config.reloadDownloadProducts, form, IWD.Download.parseResponseSortProductPage,'json')
				},200);
			});
		},
		
		/** SORT MARKETING TABLE ON DOWNLOAD PAGE **/
		sortDownloadMarketing:function(){
			$ji(document).on('click','#b2b-filter-download-marketing a:not(.prettycheckbox a)', function(e){
				e.preventDefault();
				IWD.Download.sort = $ji(this).data('sort'),
				IWD.Download.field = $ji(this).data('attribute');
				if (IWD.Download.sort=='no-sortable'){
					return;
				}
				
				var form = $ji('#b2b-filter-download-marketing').serializeArray();		
				form.push({name: "attribute", value: IWD.Download.field});
				form.push({name: "sort", value: IWD.Download.sort});
				form.push({name: "page", value: IWD.Download.currentProductPage});
				form.push({name: "limit", value: $ji('#b2b-page-limit').val()});
				
				IWD.Decorator.showLoader();
				$ji.post(IWD.App.config.reloadDownloadMarketing, form, IWD.Download.parseResponseSortMarketingPage,'json')
			});
		},
		
		
		/** FILTER TABLE ON MARKETING TAB **/
		filterDownloadMarketing: function(){
			$ji(document).on('keyup','#b2b-filter-download-marketing input:not(:checkbox)', function(e){
				var form = $ji('#b2b-filter-download-marketing').serializeArray();				
				form.push({name: "attribute", value: IWD.Download.field});
				form.push({name: "sort", value: IWD.Download.sort});						
				form.push({name: "page", value: 1});
				form.push({name: "limit", value: $ji('#b2b-page-limit').val()});
				if (IWD.Filter.timeoutRequest != null) {
					clearTimeout(IWD.Filter.timeoutRequest);
				}
			
				IWD.Filter.timeoutRequest = setTimeout(function() {
					IWD.Decorator.showLoader()
					$ji.post(IWD.App.config.reloadDownloadMarketing, form, IWD.Download.parseResponseSortMarketingPage,'json')
				},600);
			});
			
			$ji(document).on('change','#b2b-filter-download-marketing select', function(e){
				var form = $ji('#b2b-filter-download-marketing').serializeArray();				
				form.push({name: "attribute", value: IWD.Download.field});
				form.push({name: "sort", value: IWD.Download.sort});						
				form.push({name: "page", value: 1});
				form.push({name: "limit", value: $ji('#b2b-page-limit').val()});
				if (IWD.Filter.timeoutRequest != null) {
					clearTimeout(IWD.Filter.timeoutRequest);
				}
			
				IWD.Filter.timeoutRequest = setTimeout(function() {
					IWD.Decorator.showLoader()
					$ji.post(IWD.App.config.reloadDownloadMarketing, form, IWD.Download.parseResponseSortMarketingPage,'json')
				},200);
			});
		},
		
		/** CHANGE LIMIT **/
		changeLimit: function(){
			$ji('#b2b-page-limit').change(function(){
				
				if($ji('#b2b-download-products-tab').is(':visible')){
					
					$ji('#b2b-filter-download-products input:first').keyup();
				}
				
				if($ji('#b2b-download-marketing-tab').is(':visible')){
					
					$ji('#b2b-filter-download-marketing input:first').keyup();
				}
			});
		},
		
		
		/** REORDER */
		sortReorder:function(){
			$ji(document).on('click','#b2b-filter-reorder a', function(e){
				e.preventDefault();
				var sort = $ji(this).data('sort'),
				attribute = $ji(this).data('attribute'),
				status = $ji(this).data('sort');
				if (status=='no-sortable'){
					return;
				}
				
				var form = $ji('#b2b-filter-reorder').serializeArray();		
				form.push({name: "attribute", value: attribute});
				form.push({name: "sort", value: sort});
				form.push({name: "includePages", value: IWD.List.nextPage-1});
				
				form.push({name: "from", value: $ji('#b2b-from').val()});
				form.push({name: "to", value: $ji('#b2b-to').val()});
				
				IWD.Decorator.lockBlock($ji('.b2b-selected-items'));
				$ji.post(IWD.App.config.sortReorder, form, IWD.List.parseResponseSortReorder,'json')				
			});
		},
		
		filterReorder: function(){
			
			$ji(document).on('change','#datepicker input', function(e){
				var form = $ji('#b2b-filter-reorder').serializeArray();	
				
				form.push({name: "from", value: $ji('#b2b-from').val()});
				form.push({name: "to", value: $ji('#b2b-to').val()});


				
				if (IWD.Filter.timeoutRequest != null) {
					clearTimeout(IWD.Filter.timeoutRequest);
				}
			
				IWD.Filter.timeoutRequest = setTimeout(function() {
					IWD.Decorator.lockBlock($ji('.b2b-selected-items'));
					$ji.post(IWD.App.config.loadReorder,form, IWD.List.parseResponseFilterReorder,'json')
				},0);
			});
			
			
			$ji(document).on('keyup','#b2b-filter-reorder input', function(e){
				var form = $ji('#b2b-filter-reorder').serializeArray();
				
				form.push({name: "from", value: $ji('#b2b-from').val()});
				form.push({name: "to", value: $ji('#b2b-to').val()});
				
				if (IWD.Filter.timeoutRequest != null) {
					clearTimeout(IWD.Filter.timeoutRequest);
				}
			
				IWD.Filter.timeoutRequest = setTimeout(function() {
					IWD.Decorator.lockBlock($ji('.b2b-selected-items'));
					$ji.post(IWD.App.config.loadReorder,form, IWD.List.parseResponseFilterReorder,'json')
				},600);
			});
		},
		
		
};