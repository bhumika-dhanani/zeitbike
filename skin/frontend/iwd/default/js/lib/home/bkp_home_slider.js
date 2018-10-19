/**==========================** HomePage FullWidth Slider*/
var sliderOptions = {
    slider_id: '#homepage_slider',
    slide_class: '.inner',
    slide_inner_text: '#homepage_slider .inner-wrapper',
}
var $s = jQuery.noConflict();
function initSliderText(){
	var resizeTimer;
	if(!$s(sliderOptions.slider_id).length)
		return;
	$s(sliderOptions.slider_id).owlCarousel({
			navigation : true, 
			slideSpeed : 300,
			paginationSpeed : 400,
			singleItem:true,
			navigationText:''
		});
	resizeSliderText();
	$s(window).resize(function (e) {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {
            resizeSliderText();
        }, 250);
    });
		
}
function resizeSliderText(){
	$s(sliderOptions.slide_inner_text).fitText(5.2, { minFontSize: '10px', maxFontSize: '18px' });
	$s(sliderOptions.slide_inner_text).each(function(){
		var top_margin =  $s(this).height()/-2;
		$s(this).css('margin-top', top_margin);
	});
}

jQuery(document).ready(function() {
	initSliderText();
});


/**==========================** HomePage Text Block*/

var textOptions = {
	resizable_text_class: '.text-block',
}
jQuery(document).ready(function() {
	$(textOptions.resizable_text_class).fitText(5.2, { minFontSize: '10px', maxFontSize: '18px' });
});

/**==========================** HomePage Text Block With Banner Background*/
var textOptions = {
	resizable_text_class: '.text-block-with-background',
}
jQuery(document).ready(function() {
    $(textOptions.resizable_text_class).fitText(5.2, { minFontSize: '10px', maxFontSize: '18px' });
});
/**==========================** HomePage D3: 3 Category Banners */

jQuery(document).ready(function () {
	var cms_banner_block = '.three-banners-block .banner-child';
    $(cms_banner_block).each(function(){
		var background_image = $(this).find(' > img ');
		if(typeof background_image !=='undefined')
		{	
			var src = background_image.attr('src');
            $(this).css("background", "transparent url('"+src+"') no-repeat center center");
			background_image.css('visibility', 'hidden');
		}
	});
});

/**==========================** HomePage D3: New Arrivals*/
jQuery(window).load(function() {
    jQuery(".featured-brands-d3 ul").owlCarousel({
		items : 5,
		itemsCustom : false,
		itemsDesktop : [1199,4],
		itemsDesktopSmall : [770,3],
		itemsTablet: [480,2],
		itemsTabletSmall: false,
		itemsMobile : [320,1],
   		navigation : true,
		navigationText : ["<i class='fa fa-angle-left'></i>","<i class='fa fa-angle-right'></i>"],
		rewindNav : false
    });
});
