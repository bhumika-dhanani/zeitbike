IWD.Decorator = {
		widthWindow:null,
		maxColumHeight: null,
		init: function(){
			
			IWD.Decorator.widthWindow = $ji(window).width();
			
			this.backToTopQueue();
			this.initDataPicker();
			this.initSelect();
			this.resizeTitleBlock();
			this.initScrollUpdates()
			
			this.initToolTips();
			
			
			
			this.initTableScroll();
			
			this.initChangeImage();
			
			this.block();
			
		},
		
		initChangeImage: function(){
			$ji(document).on('click','.more-views li a', function(e){
				e.preventDefault();
				var url = $ji(this).data('image');
				$ji('#b2b-image').attr('src', url);
			});
		},
		
		initTableScroll: function(){
			
			 $ji(".b2b-table-scroll").niceScroll({/*touchbehavior:true,*/railpadding:{top:-28,right:0,left:0,bottom:0}, cursorborder:"",cursorcolor:"#ccc", cursoropacitymin:0.35, cursoropacitymax:0.35,cursorwidth:9,boxzoom:false, railoffset:{top:11}});
		},
		
		reInitTableScroll: function(){
			$ji('.nicescroll-rails-hr').remove();			
			this.initTableScroll();
		},
		
		/** tooltips **/
		initToolTips: function(){						
			IWD.Tooltip.init();
		},
		
		/** DROPDOWN SCROLL **/
		initDropScroll: function(){
		    $ji(".quick-search-result").niceScroll({cursorborder:"",cursorcolor:"#ccc", cursoropacitymax:0.35,cursorwidth:9,boxzoom:false, railoffset:{left:-11}});
		},
		
		
		hideDropScroll: function(){
			$ji(".quick-search-result").getNiceScroll().hide();
		},
		
		updateDropScroll: function(){
			$ji(".quick-search-result").getNiceScroll().show();
			$ji(".quick-search-result").getNiceScroll().resize();
		},

		/** DESCRIPTION SCROLL **/
		initDescriptionScroll: function(){
		    $ji(".b2b-product-description .std").niceScroll({cursorborder:"",cursorcolor:"#ccc", cursoropacitymax:1,cursorwidth:9,boxzoom:false, railoffset:{left:11}});
		},
		
		
		hideDescriptionScroll: function(){
			$ji(".b2b-product-description .std").getNiceScroll().hide();
		},
		
		updateDescriptionScroll: function(){
			$ji(".b2b-product-description .std").getNiceScroll().show();
			$ji(".b2b-product-description .std").getNiceScroll().resize();
		},
		
		initScrollUpdates:function(){
			$ji(window).scroll(function(){
				if ($ji('.quick-search-result').is(':visible')){
					IWD.Decorator.updateDropScroll();
				}
			});
			
		},
		
		showLoader: function(){
			$ji('.b2b-ajax-loader').show();
		},
		
		hideLoader: function(){
			$ji('.b2b-ajax-loader').hide();
		},
		
		
		lockBlock: function(wrapElement){
			wrapElement.wrap('<div class="locker-block"></div>');
			$ji('<div>').addClass('table-locker-block').insertAfter(wrapElement);
		},
		
		unLockBlock: function(wrapElement){
			var $locker = wrapElement.closest('.locker-block');
			if($locker.length !=0 ){
				$locker.find('.table-locker-block').remove();
				wrapElement.unwrap()	
			}
		},
		
		backToTopQueue: function(){
			$ji(document).on('click','#b2b-back-queue',function(e){
				e.preventDefault();
				$ji("html, body").animate({ scrollTop: 0 }, "slow");					
			});
			
			$ji(window).resize(function(){
				IWD.Decorator.widthWindow = $ji(window).width();
				
				if (IWD.Decorator.widthWindow<1024){
					$ji('.b2b-selected-items .table-header ').removeClass('fixed-header');
					return;
				}
			});
			
			$ji(window).scroll(function(){
				
				if (IWD.Decorator.widthWindow<=1024){
					return;
				}
				position = $ji(window).scrollTop();
				if (position >= 468) {
					var height = $ji('.b2b-selected-items .div-table').height();

					if (position>=468+height-50){
						$ji('.b2b-selected-items .table-header ').removeClass('fixed-header');
					}else{
						if(!$ji('.b2b-selected-items .table-header').hasClass('fixed-header')){
							
							$ji('.b2b-selected-items .table-header').stop();
							$ji('.b2b-selected-items .table-header').addClass('fixed-header').css('top','-75px');
							
							$ji('.b2b-selected-items .table-header').animate({				       
							        top: "+=75"       
						    }, 200, function() {
							        // Animation complete.
						    });
						}
					}
					
					

				}else{
					$ji('.b2b-selected-items .table-header ').removeClass('fixed-header');
				}
			});
			
		},
		
		
		tierPrice: function(){
			$ji('.product-price i, .reorder-price i').each(function(){				
				$container = $ji(this).prev();
				
				$box = $container.find('.price-box:first .regular-price');
				$ji(this).appendTo($box)
				
			});
			
			
		
		},
		
		initDataPicker: function(){
			if ($ji('.input-daterange').length>0){
				$ji('.input-daterange').datepicker({autoclose: true});
			}
		},
		
		
		initSelect: function(){
			$ji(".b2b-chosen-select").choseniwd({disable_search_threshold: 10});
		},
		
		resizeTitleBlock: function(){
			$ji(document).on('click', '.b2b-collapse-block span', function(e){
				e.preventDefault();
				var $parent = $ji(this).parent();
				$parent.addClass('hidden');
				$parent.next().removeClass('hidden');
			});
			
			$ji(document).on('click', '.b2b-full-block span', function(e){
				e.preventDefault();
				var $parent = $ji(this).parent();
				$parent.addClass('hidden');
				$parent.prev().removeClass('hidden');
			});
		},
		
		initThumbnailSlider: function(){
			$ji('.b2b-product-slider').jcarousel({
		        // Configuration goes here
		    });
			
			$ji('.b2b-jcarousel-prev').click(function(e) {
				e.preventDefault();
				$ji('.b2b-product-slider').jcarousel('scroll', '-=1');
			});

			$ji('.b2b-jcarousel-next').click(function(e) {
				e.preventDefault();
				$ji('.b2b-product-slider').jcarousel('scroll', '+=1');
			});
			
		},
		
		block: function(){
			$ji(document).on('click','.b2b-wrapper .minimal-price-link',function(e){
				e.preventDefault();
			});
		},
		
		decorateTable: function(){
			$ji('.table-row').each(function(){
				$fields = $ji(this).find('div.left:not(.sub .div.left)');
				$fields.each(function(){
					if ($ji(this).outerHeight() > IWD.Decorator.maxColumHeight){
						IWD.Decorator.maxColumHeight = $ji(this).outerHeight(); 
					}
				});
				$fields.each(function(){
					$ji(this).css('height', IWD.Decorator.maxColumHeight);
				});
			});
		}
		
	
		
		
};