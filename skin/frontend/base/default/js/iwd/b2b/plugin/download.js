IWD.Download = {
		
		checked:0,
		product: null,
		field: 'title',
		sort: 'asc',
		
		currentProductPage:0,
		maxProductPage:0,
		
		init: function(){
			this.triggerLoad();
			this.triggerCheck();
			this.triggerCheckAll();
			this.submitDownload();
			
			this.triggerTab();
			
			this.paginationProducts();
			this.triggerCheckProduct();
			this.triggerCheckAllProduct();
			
			
			this.paginationMarketing();
			this.triggerCheckMarketing();
			this.triggerCheckAllMarketing();
			
			this.submitDownloadTab();
			
			
			
			//change tab
			
			var hash = window.location.hash;
			
			if (hash=='#marketing'){
					$ji('.b2b-download-tabs ul li:last').click();
			}
		},
		
		triggerLoad: function(){
			$ji(document).on('click', '.download-icon-link', function(e){
				e.preventDefault();
				IWD.Decorator.showLoader();
				IWD.Download.product = $ji(this).data('id')
				$ji.post(IWD.App.config.downloadInfoUrl,{"product": IWD.Download.product }, IWD.Download.afterLoadInfo,'json');
			});
		},
		
		afterLoadInfo: function(response){
			IWD.Decorator.hideLoader();
			$ji('#b2b-download-modal').modaliwd();
			$ji('#b2b-dialog-download').html(response.content);
		},
		
		triggerCheck: function(){
			$ji(document).on('change', '#b2b-download-modal .table-body input:checkbox', function(){				
				if ($ji(this).prop('checked')){
					IWD.Download.checked += 1;
				}else{
					IWD.Download.checked -= 1;
				}
				IWD.Download.updateText();
			});
		},
		
		triggerCheckAll:function(){
			$ji(document).on('change', '#b2b-download-modal .table-header input:checkbox', function(){				
				if ($ji(this).prop('checked')){
					$ji('#b2b-download-modal .table-body input:checkbox').each(function(){
						if (!$ji(this).prop('checked')){
							$ji(this).next().click();
						}						
					});
				}else{
					$ji('#b2b-download-modal .table-body input:checkbox').each(function(){
						if ($ji(this).prop('checked')){
							$ji(this).next().click();
						}						
					});
				}
				
				IWD.Download.updateText();
			});
		},
		
		updateText: function(){
			$ji('#b2b-download-modal strong').text(IWD.Download.checked);
		},
		
		submitDownload: function(){
			$ji('.b2b-modal-footer .btn-download').click(function(e){
				e.preventDefault();
				var formData = $ji('#b2b-form-download').serializeArray();
				$ji.post(IWD.App.config.downloadUrl, formData, function(response){
					if (typeof(response.url)!="undefined"){
						if (response.url!=false){
							setLocation(response.url);
						}
					}
				}, 'json');
			})
			
		},
		
		
		parseResponseSortProductPopp: function(response){
			IWD.Decorator.hideLoader();
			if (typeof(response.content)!="undefined"){
				$ji('#b2b-dialog-download').empty().html(response.content);
			}
		},
		
		
		parseResponseSortProductPage: function(response){
			IWD.Decorator.hideLoader();
			if (typeof(response.content) !="undefined"){
				$ji('#b2b-download-products-tab').empty().html(response.content);
				
			}
		},
		
		parseResponseSortMarketingPage: function(response){
			IWD.Decorator.hideLoader();
			if (typeof(response.content) !="undefined"){
				$ji('#b2b-download-marketing-tab').empty().html(response.content);
				
			}
		},
		
		
		
		paginationProducts: function(){
			$ji(document).on('click','#b2b-download-products-tab .b2b-prev', function(e){				
				e.preventDefault();
				if ($ji(this).hasClass('disabled')){
					return;
				}
				IWD.Download.loadPageProducts(IWD.Download.currentProductPage-1);
			});
			$ji(document).on('click','#b2b-download-products-tab .b2b-next', function(e){
				e.preventDefault();
				if ($ji(this).hasClass('disabled')){
					return;
				}
				IWD.Download.loadPageProducts(IWD.Download.currentProductPage+1);
			});
		},
		
		paginationMarketing: function(){
			$ji(document).on('click','#b2b-download-marketing-tab .b2b-prev', function(e){				
				e.preventDefault();
				if ($ji(this).hasClass('disabled')){
					return;
				}
				IWD.Download.loadPageMarketing(IWD.Download.currentProductPage-1);
			});
			$ji(document).on('click','#b2b-download-marketing-tab .b2b-next', function(e){
				e.preventDefault();
				if ($ji(this).hasClass('disabled')){
					return;
				}
				IWD.Download.loadPageMarketing(IWD.Download.currentProductPage+1);
			});
		},
		
		
		loadPageProducts: function(loadPage){
			
				var form = $ji('#b2b-filter-download-products').serializeArray();		
				form.push({name: "attribute", value: IWD.Download.field});
				form.push({name: "sort", value: IWD.Download.sort});
				form.push({name: "page", value: loadPage});
				form.push({name: "limit", value: $ji('#b2b-page-limit').val()});
				IWD.Decorator.showLoader();
				$ji.post(IWD.App.config.reloadDownloadProducts, form, IWD.Download.parseResponseSortProductPage,'json')
		},
		
		
		loadPageMarketing: function(loadPage){
			
			var form = $ji('#b2b-filter-download-marketing').serializeArray();		
			form.push({name: "attribute", value: IWD.Download.field});
			form.push({name: "sort", value: IWD.Download.sort});
			form.push({name: "page", value: loadPage});
			form.push({name: "limit", value: $ji('#b2b-page-limit').val()});
			IWD.Decorator.showLoader();
			$ji.post(IWD.App.config.reloadDownloadMarketing, form, IWD.Download.parseResponseSortMarketingPage,'json')
		},
		
		prepareProductPagination: function(){
			if (IWD.Download.currentProductPage<=1){
				$ji('.b2b-prev').addClass('disabled');
			}else{
				$ji('.b2b-prev').removeClass('disabled');
			}
			
			if (IWD.Download.currentProductPage>=IWD.Download.maxProductPage){
				$ji('.b2b-next').addClass('disabled');
			}else{
				$ji('.b2b-next').removeClass('disabled');
			}
		},
		
		
		triggerCheckProduct: function(){
			$ji(document).on('change', '#b2b-product-downloads-list input:checkbox', function(){				
				if ($ji(this).prop('checked')){
					IWD.Download.checked += 1;
				}else{
					IWD.Download.checked -= 1;
				}
				
				IWD.Download.updateTextProducts();
			});
		},
		
		triggerCheckMarketing: function(){
			$ji(document).on('change', '#b2b-marketing-downloads-list input:checkbox', function(){				
				if ($ji(this).prop('checked')){
					IWD.Download.checked += 1;
				}else{
					IWD.Download.checked -= 1;
				}
				IWD.Download.updateTextProducts();
			});
		},
		
		
		
		updateTextProducts: function(){
			
			IWD.Download.checked = 0;
			
			if($ji('#b2b-download-products-tab').is(':visible')){
				
				$ji('#b2b-product-downloads-list input:checkbox').each(function(){
					if ($ji(this).prop('checked')){
						IWD.Download.checked +=1;
					}
				});
			}
			
			if($ji('#b2b-download-marketing-tab').is(':visible')){
			
				$ji('#b2b-marketing-downloads-list input:checkbox').each(function(){
					if ($ji(this).prop('checked')){
						IWD.Download.checked +=1;
					}
				});
			}
			$ji('.b2b-download-product-footer strong').text(IWD.Download.checked);
		},
		
		triggerCheckAllProduct:function(){
			$ji(document).on('change', '#b2b-filter-download-products input:checkbox', function(){				
				if ($ji(this).prop('checked')){
					$ji('#b2b-product-downloads-list input:checkbox').each(function(){
						if (!$ji(this).prop('checked')){
							$ji(this).next().click();
						}						
					});
				}else{
					$ji('#b2b-product-downloads-list input:checkbox').each(function(){
						if ($ji(this).prop('checked')){
							$ji(this).next().click();
						}						
					});
				}
				
				IWD.Download.updateTextProducts();
			});
		},
		
		triggerCheckAllMarketing:function(){
			$ji(document).on('change', '#b2b-filter-download-marketing input:checkbox', function(){				
				if ($ji(this).prop('checked')){
					$ji('#b2b-marketing-downloads-list input:checkbox').each(function(){
						if (!$ji(this).prop('checked')){
							$ji(this).next().click();
						}						
					});
				}else{
					$ji('#b2b-marketing-downloads-list input:checkbox').each(function(){
						if ($ji(this).prop('checked')){
							$ji(this).next().click();
						}						
					});
				}
				
				IWD.Download.updateTextProducts();
			});
		},
		
		submitDownloadTab: function(){
			$ji('.b2b-download-product-footer .btn-download').click(function(e){
				e.preventDefault();
				if($ji('#b2b-download-products-tab').is(':visible')){
					var formData = $ji('#b2b-product-downloads-list').serializeArray();
					$ji.post(IWD.App.config.downloadUrl, formData, function(response){
						if (typeof(response.url)!="undefined"){
							if (response.url!=false){
								setLocation(response.url);
							}
						}
					}, 'json');
				}
				
				
				if($ji('#b2b-download-marketing-tab').is(':visible')){
					var formData = $ji('#b2b-marketing-downloads-list').serializeArray();
					$ji.post(IWD.App.config.downloadUrl, formData, function(response){
						if (typeof(response.url)!="undefined"){
							if (response.url!=false){
								setLocation(response.url);
							}
						}
					}, 'json');
				}
			})
		},
		
		
		triggerTab: function(){
			$ji('.b2b-download-tabs li').click(function(){
				$ji('.b2b-download-tabs  li').removeClass('active');
				var id = $ji(this).data('id');
				$ji(this).addClass('active');
				$ji('#b2b-download-products-tab').hide();
				$ji('#b2b-download-marketing-tab').hide();
				$ji('#'+id).show();
				IWD.Download.updateTextProducts();
				$ji(".b2b-table-scroll").getNiceScroll().show();
				$ji(".b2b-table-scroll").getNiceScroll().resize();
			});
		}
				
};