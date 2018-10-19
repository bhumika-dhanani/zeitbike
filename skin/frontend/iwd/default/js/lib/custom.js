$(function(){
	//jQuery('.catalog-category-view select, .catalogsearch-result-index select,.stock-notification-header select').customSelect();
});


jQuery(window).load(function() {

	if(jQuery('.skip-cart .count').css('display') != 'none') {
		var accwidth =  parseInt(jQuery('.skip-account').css('right').replace(/\D+/g,"")) + jQuery('.skip-cart .count').width();
		var contwidth = parseInt(jQuery('.skip-links .skip-contacts').css('right').replace(/\D+/g,"")) + jQuery('.skip-cart .count').width();

		jQuery('.skip-account').css({'right': accwidth});
		jQuery('.skip-links .skip-contacts:not(.skip-wholesale-log-in)').css({'right': contwidth});
		jQuery('.skip-links .skip-contacts.skip-wholesale-log-in').css({'right': parseInt(contwidth + 75)});
	}
	jQuery('.skip-cart').on('click',function () {
		if (jQuery(window).height() < jQuery('#header-cart').height()+300) {
			jQuery('#cart-sidebar').css({'max-height':'250px','overflow-y':'scroll'});
		}
	});

	var footer_bottom = jQuery('.b2b-sticky-footer').height()+10;
	jQuery('.footer-container').css({'margin-bottom': footer_bottom});


	/*------ fix for News Pop up -------------- */
	jQuery('.start_shopping.close span').on('click',function() {
		jQuery('#popup-newssubscribe').hide();
	});


	jQuery('.category-products select,.amshopby-ajax-select').selectpicker();
	/*Select with bootstrap*/
	jQuery('.bootstrap-select li').click( function () {
		var rel = jQuery(this).attr('rel');
		rel++;
		var val = jQuery(this).parent().parent().parent().parent().find('select option:nth-child(' + rel + ')').attr('value');
		setLocation(val);
	});




	jQuery('#narrow-by-list dt').on('click',function() {
		//jQuery('#narrow-by-list dt').addClass('amshopby-collapsed');

		jQuery('#narrow-by-list dd').removeClass('amshopby-collapsed');
		//jQuery(this).removeClass('amshopby-collapsed');
		//if (!jQuery(this).hasClass('amshopby-collapsed') && jQuery(this).next().hasClass('current')){jQuery(this).addClass('amshopby-collapsed');}

		if (jQuery(this).hasClass('amshopby-collapsed') && jQuery(this).hasClass('current')){jQuery(this).next().addClass('amshopby-collapsed');jQuery('#narrow-by-list dt').addClass('amshopby-collapsed');}
		//if (jQuery(this).hasClass('current') && jQuery(this).hasClass('current')){jQuery('#narrow-by-list dt').addClass('amshopby-collapsed');}
	});


	jQuery(window).resize(function() {
		if (jQuery(this).width() > 700) {
			jQuery('#narrow-by-list').addClass('active');
			jQuery('.block-subtitle--filter').addClass('active');
		}
		jQuery('#narrow-by-list.active').fadeIn();
		jQuery('#narrow-by-list:not(.active)').fadeOut();
		jQuery('.block-subtitle--filter.active').css({"content": ""});
		jQuery('.block-subtitle--filter:not(.active)').css({"content": ""});

	});

	jQuery('.block-subtitle--filter').addClass('active');
		jQuery('#narrow-by-list').addClass('active');
	jQuery('.block-subtitle--filter').on('click', function () {
			if (!jQuery('#narrow-by-list').hasClass('active')) {
				jQuery('#narrow-by-list').addClass('active');
				jQuery('.block-subtitle--filter').addClass('active');

			} else {
				jQuery('#narrow-by-list').removeClass('active');
				jQuery('.block-subtitle--filter').removeClass('active');

			}
			jQuery('#narrow-by-list.active').fadeIn();
			jQuery('#narrow-by-list:not(.active)').fadeOut();
			jQuery('.block-subtitle--filter.active').css({"content": ""});
			jQuery('.block-subtitle--filter:not(.active)').css({"content": ""});

		});
	//}



	jQuery('.stock-notification-header select').selectpicker();
	jQuery('.stock-notification-header select').hide();

	jQuery(document).scroll(function(){
		if (jQuery('.main-container').length>0){
			p = jQuery('.main-container').position();
			var pos = p.top - jQuery(window).scrollTop();
			if (pos <=0  && jQuery(window).width() > 770)
				jQuery('.page-header').addClass('fixed', 500);
			if(jQuery(window).scrollTop() == 0)
				jQuery('.page-header').removeClass('fixed', 500)
			
		}
	});
	/***/
	ChangeHeight();
	jQuery(window).resize(function() {
		ChangeHeight();
		getGridSize();
	});
	if(jQuery(".featured-products .slides").size()){
		jQuery(".featured-products .slides").owlCarousel({
			autoPlay: false,
			lazyLoad: true,
			items : 5,
			itemsDesktop : [1199,4],
			itemsDesktopSmall : [979,3],

			itemsTabletSmall:[769,2],
			itemsMobile:[479,1],

			// Navigation
			navigation : true,
			navigationText : ["<i class='fa fa-angle-left'></i>","<i class='fa fa-angle-right'></i>"],
			rewindNav : true,
			scrollPerPage : false,
		});
	}
	jQuery(".block-layered-nav .block-content > dl > dt").click(function(){
		display = jQuery(this).next().css("display");
		if(display == "none")
			jQuery(this).removeClass("current");
	});
	getGridSize();
	//if (IWD.App.config.currentStore == IWD.App.config.wholesaleStore) {
			/* trigger to login link*/
			var url = window.location.href;
			if (url.indexOf('?login')!=-1){
				IWD.Login.checkIsLogin();
			}
	//}
});
function ChangeHeight(){
	$w_w = jQuery(window).width();
	if( $w_w <= 770){
		$h = jQuery(".collection > img").height() - 12;
		jQuery(".collection .label .label-inner").height($h);
	}
	else
		jQuery(".collection .label .label-inner").height('auto');
}
function getGridSize(){
	jQuery(".product-view .block-related li.item, .product-view .box-up-sell li.item").each(function(){
		$height = jQuery(this).find(".hover-item").css("height");
		jQuery(this).height($height);
	});
}