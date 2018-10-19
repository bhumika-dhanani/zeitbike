/*
 * Dependency:
 *   - /lid/enquire.js
 *   - /lid/bootstrap-select.min.js 
 */

var $q = jQuery.noConflict();
var bp = {
	    xsmall: 480,
	    small: 500,
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
$q(document).ready(function () {

    // ==============================================
    // IWD: Header1 Account Menu Switcher
    // ==============================================
    enquire.register('(max-width: ' + bp.xsmall + 'px)', {
        match: function () {
            $q('.header1 .account-menu-container').prepend($q('#header-account'));
            
        },
        unmatch: function () {
            $q('.header1 .account-link-wrapper').prepend($q('#header-account'));
           
        }
    });
    // ==============================================
    // IWD: Header1  Shopping Cart Menu Switcher
    // ==============================================
    enquire.register('(max-width: ' + bp.xsmall + 'px)', {
        match: function () {
            $q('#header .cart-container').prepend($q('#header-cart'));
        },
        unmatch: function () {
            $q('#header .skip-links > .header-minicart').prepend($q('#header-cart'));
        }
    });
    
    // ==============================================
    // IWD: Header1  Logo Position Switcher
    // ==============================================
    enquire.register('(max-width: ' + bp.xsmall + 'px)', {
        match: function () {
            $q('.header1 .top-page-header-container').prepend($q('a.logo')); 
        },
        unmatch: function () {
            $q('.header1 .table-row-container').prepend($q('a.logo'));
        }
    });
 // ==============================================
    // IWD: Header1  Account Link,Cart Link, Search Link Position Switcher
    // ==============================================
    enquire.register('(max-width: ' + bp.xsmall + 'px)', {
        match: function () {
        	 
        	 $q('.header1 .skip-links.all-links').append(jQuery('.skip-links .header-minicart'));
        	 $q('.header1 .skip-links.all-links').append(jQuery('.account-link-wrapper'));
        	 $q('.header1 .table-row-container').append(jQuery('#header-search'));
        },
        unmatch: function () {
        	$q('.header1 .right-half-column .skip-links').append(jQuery('#header-search'));
        	$q('.header1 .right-half-column .skip-links').prepend(jQuery('.account-link-wrapper'));
        	$q('.header1 .right-half-column .skip-links').prepend(jQuery('.skip-links .header-minicart'));
        }
    });
    
 
    // ==============================================
    // IWD: Header1  Search Box Animation
    // ==============================================
    	$q(".header-search").toggleClick(
	    	 function() {
	    		 $q('#search_mini_form .input-box').show();
	    		 $q('#search_mini_form .input-box').animate({ width: '220px', left: '-220px'}, 1000, function(){
	    			 $q('#header-search').addClass('opened');
	    		 });
	    	 },
	    	 function() {
	    		 $q('#search_mini_form .input-box').animate({ width: '0', left: '0'}, 1000, function(){
	    			 $q('#search_mini_form .input-box').hide();
	    			 $q('#header-search').removeClass('opened');
	    			});
	    	 }
    	 );
    
});

// custom header select 
$q(window).on('load', function () {
	$q('#select-store').selectpicker();
	$q('#select-language').selectpicker();
	$q('#select-currency').selectpicker();
});
