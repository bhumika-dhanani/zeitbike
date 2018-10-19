var $q = jQuery.noConflict();
var footer_bp = {
	    xsmall: 480,
	    small: 500,
	    xmedium: 600,
	    medium: 770,
	    large: 979,
	    xlarge: 1199
}

//==============================================
//IWD: tools
//==============================================
/*clicktoggle instead of deprecated Toggle function*/
$q.fn.toggleClick = function(){

var functions = arguments ;

return this.click(function(){
       var iteration = $q(this).data('iteration') || 0;
       functions[iteration].apply(this, arguments);
       iteration = (iteration + 1) % functions.length ;
       $q(this).data('iteration', iteration);
});
};

//==============================================
// IWD Fotter D3: equal height footer columns, contact || newsletter block 
// ==============================================
var window_width = $q(window).width();
var footer_columns_selector = ".footer3  .footer-child";
var footer_contact_social_selector = ".footer3 .block-subscribe, .footer3 .footer-contacts";


equalheight = function(container){
	var currentTallest = 0;
	 $q(container).each(function() {
		 $q(this).height('auto');
	     currentTallest = (currentTallest < $q(this).height()) ? ($q(this).height()) : (currentTallest);
	  });
	 if(currentTallest)
		 $q(container).each(function() {
			 $q(this).height(currentTallest);
		 });
}  


$q(window).on('load', function () {
	var window_width = $q(window).width();
	if(window_width < footer_bp.xlarge && window_width > footer_bp.xmedium )
	{
		equalheight(footer_contact_social_selector);
	}
	if(window_width > footer_bp.xmedium )	
	{
		equalheight(footer_columns_selector);
	}
	
	
});
$q(window).resize(function(){
	var window_width = $q(window).width();
	if(window_width < footer_bp.xlarge && window_width > footer_bp.xmedium )
		equalheight(footer_contact_social_selector);
	else
	{
		$q(footer_contact_social_selector).each(function(){$q(this).height('auto')});
	}
	if(window_width > footer_bp.xmedium )
		equalheight(footer_columns_selector);
	else
	{
		$q(footer_contact_social_selector).each(function(){$q(this).height('auto')});
	}
});

//==============================================
//IWD Fotter D3: mobile toggle columns 
//==============================================
$q(document).ready(function () {	
	$q(".footer .footer-child .block-title").toggleClick(
	    	 function() {
	    		 if($q(window).width()<= footer_bp.xmedium){
		    		 $q(this).closest('.footer-child').addClass('opened'); 
	    		 }
	    	 },
	    	 function() {
	    		 if($q(window).width()<= footer_bp.xmedium){
		    		 $q(this).closest('.footer-child').removeClass('opened');
	    		 }
	    	 }
   	 );
});





