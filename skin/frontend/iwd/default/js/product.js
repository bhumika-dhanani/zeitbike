/* jCarousel - http://sorgalla.com/jcarousel/*/
var $j = jQuery.noConflict();

/*
*  Main Product Slider Options
*
*/
jc_options = {
	carousel:'.jcarousel',
	control:'.jcarusel-control',
	prevButton:'.jcarousel-prev',
	nextButton:'.jcarousel-next',
	neededContainer:'.product-img-box .product-image',
	itemsCount:6,
	threshold:2,
	itemDefaultBottomMargin:26,
	itemDefaultRightMargin:25,
	tablet:770,
	mobile:480
};
$j(document).ready(function () {
	var carousel  = jQuery(jc_options.carousel).jcarousel();
	
	jQuery(jc_options.carousel).after('<div class="'+jc_options.control.replace(/[.#]/, "")+'"> <a class="'+jc_options.prevButton.replace(/[.#]/, "")+'" href="#"><i class="fa"></i></a> <a class="'+jc_options.nextButton.replace(/[.#]/, "")+'" href="#"><i class="fa"></i></a></div>');
	jQuery(jc_options.carousel).next(jc_options.control).hide();
	
	
	jQuery(jc_options.nextButton).jcarouselControl({
	       target: '+=1',
	       carousel: carousel
	});
	jQuery(jc_options.prevButton).jcarouselControl({
	       target: '-=1',
	       carousel: carousel
	});

	carousel.on('jcarousel:animateend', function(event, carousel) {
	     var currentFirstItem = jQuery(this).jcarousel('first');
	     var currentLastItem  = jQuery(this).jcarousel('last');
	     var firstItem = jQuery(this).find('ul li').first();
	     var lastItem = jQuery(this).find('ul li').last();
	     if(currentFirstItem.is(firstItem))
	     {
	       	jQuery(this).next(jc_options.control).find(jc_options.prevButton).addClass('disabled');
	     }
	     else
	     {
	       	jQuery(this).next(jc_options.control).find(jc_options.prevButton).removeClass('disabled');
	     }
	     if(currentLastItem.is(lastItem))
	     {
	       	jQuery(this).next(jc_options.control).find(jc_options.nextButton).addClass('disabled');
	     }
	     else
	     {
	       	jQuery(this).next(jc_options.control).find(jc_options.nextButton).removeClass('disabled');
	     }
	 });
	
	/*
	*  Vertical Product Slider
	*
	*
	*/
	enquire.register('screen and (min-width: ' + parseInt(jc_options.mobile+1) + 'px)', {
        match: function (){
        	
        	carousel.on('jcarousel:reload', function() {
        		
        		var container_height = jQuery(jc_options.neededContainer).height();
        		var item_bottom_margin = jQuery(this).find('li').outerHeight(true)-jQuery(this).find('li').outerHeight();
        		var li_height = jQuery(this).find('li').outerHeight(true) * jc_options.itemsCount - item_bottom_margin;
        		var item_height = jQuery(this).find(' li').outerHeight(true);
        		var new_items_count = Math.floor((container_height)/item_height);
        		
        		
        		
        		jQuery(this).css('width', '100%');
        		jQuery(this).addClass('left');
        		jQuery(jc_options.control).addClass('left');
        		jQuery(this).removeClass('bottom');
        		jQuery(jc_options.control).removeClass('bottom');
        		
        		jQuery(this).jcarousel('items').css('margin', '0 0 '+jc_options.itemDefaultBottomMargin+'px 0');	  
        		jQuery(this).css('height', container_height + 'px');
        		
        		
        		if( (container_height + jc_options.threshold) < li_height && (new_items_count < jQuery(this).jcarousel('items').length))
        		 {
        			 
        			 var height = new_items_count * item_height - item_bottom_margin;
        			 var margin = Math.round(container_height-height)/2;
        			 jQuery(this).css('margin', margin + 'px 0');
        			 jQuery(this).css('height', height + 'px')
        			 jQuery(this).next(jc_options.control).show();
        			 
        			 
        			 var clean_item_height = jQuery(this).find(' li').outerHeight();
        			 
         			var item_margin  = Math.round((height - clean_item_height *new_items_count)/(new_items_count - 1));
         		    jQuery(this).jcarousel('items').css('margin-bottom', item_margin+'px');
        		 }
        		 else
        		{   jQuery(this).css('height', container_height+'px')
        			jQuery(this).css('margin', '0');
        			
        			var clean_item_height = jQuery(this).find(' li').outerHeight();
        		    var margin  = Math.round((container_height - clean_item_height *jc_options.itemsCount )/(jc_options.itemsCount - 1));
        		    
        		    
        		    jQuery(this).jcarousel('items').css('margin-bottom', margin+'px').css('height', 'auto');
        		    jQuery(this).next(jc_options.control).hide();
        		}
        			
        	}).jcarousel({vertical: true});
        	carousel.jcarousel('reload');
        }
    });
	/*
	*  Horizontal Product Slider
	*
	*
	*/
	enquire.register('screen and (max-width: ' + jc_options.mobile + 'px)', {
		match : function() {
			
			
			carousel.on('jcarousel:reload', function(){

				jQuery(this).css('height', 'auto');
				jQuery(this).addClass('bottom');
		        jQuery(this).removeClass('left');
		        jQuery(jc_options.control).addClass('bottom');
		        jQuery(jc_options.control).removeClass('left');
		        
		        
		        jQuery(this).jcarousel('items').css('margin', '0 '+jc_options.itemDefaultRightMargin+'px 0 0 ');
		        
		        
		        var container_width = jQuery(jc_options.neededContainer).width();
		        var item_right_margin = jQuery(this).find('li').outerWidth(true)-jQuery(this).find('li').outerWidth();
		        var li_width = jQuery(this).find('li').outerWidth(true) * jc_options.itemsCount - item_right_margin;
        		var item_width = jQuery(this).find(' li').outerWidth(true);
        		var new_items_count = Math.floor((container_width)/item_width);
		        
        		jQuery(this).css('width', container_width + 'px');
        		if( (container_width + jc_options.threshold) < li_width && (new_items_count < jQuery(this).jcarousel('items').length))
        		 {
        			 
        			 var width = new_items_count * item_width - item_right_margin;
        			 var margin = Math.round(container_width-width)/2;
        			 console.log(margin);
        			 jQuery(this).css('margin', '25px '+ margin + 'px');
        			 jQuery(this).css('width', width + 'px')
        			 jQuery(this).next(jc_options.control).show();
        		 }
        		 else
        		{   jQuery(this).css('width', container_width+'px')
        			jQuery(this).css('margin', '25px 0');
        			
        			var clean_item_width = jQuery(this).find(' li').outerHeight();
        			var margin  = Math.round((container_width - clean_item_width *jc_options.itemsCount )/(jc_options.itemsCount - 1));
        		
        		    jQuery(this).jcarousel('items').css('margin-right', margin+'px').css('width', 'auto');
        		    jQuery(this).next(jc_options.control).hide();
        		}
        		
		        
		     }).jcarousel({vertical: false});
			carousel.jcarousel('reload');
		},
	});
	
	enquire.register('screen and (max-width: ' + jc_options.tablet + 'px)', {
	    match: function () {
	       	jQuery(jc_options.carousel).jcarousel('reload');
	   },
	   unmatch: function () {
	       	jQuery(jc_options.carousel).jcarousel('reload');
	   }
	});
	
	
	
	/*
	 *  Related Product Slider
	 *
	 */
    $j(window).load(function() {   
    	$j(".block-related .slides").owlCarousel({
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
	
	/*
	 *  Upsell Product Slider
	 *
	 */
    $j(window).load(function() {   
    	$j(".box-up-sell .slides").owlCarousel({
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
	

    /*
     *  Product Bootstrap Select Init
     *
     */
    	jQuery('.pager-no-toolbar select').selectpicker();
    	jQuery('.product-view select').not(('[multiple=multiple]')).selectpicker();
    	//jQuery('.product-view select').not(('[multiple=multiple], .redemption_selector')).selectpicker();

    	/* set display block for validation */
    	jQuery('.product-shop select').not(('[multiple=multiple]')).parent().addClass('custom-select-holder');
    	jQuery('.product-shop select').not(('[multiple=multiple]')).show();
    	
    	jQuery('.product-view select').not(('[multiple=multiple]')).change(function(e){
    	    selectId = e.target.id;
    	    try{
    		    spConfig.configureElement(document.getElementById(selectId));
    		    spConfig.reloadPrice();
    		    jQuery('.product-view.configurable').find(".btn-group").removeClass("super-attribute-select");
    	    }catch(e){
    	    	console.debug(e);
    	    }
    	});
    	jQuery('.bootstrap-select').click(function(e){
    	    jQuery('.product-view select').not(('[multiple=multiple]')).selectpicker('refresh');
    	});

	
});