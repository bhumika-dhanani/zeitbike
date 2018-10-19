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
    // IWD: D3 Header Left Cms Text Block 
    // ==============================================
    enquire.register('(max-width: ' + bp.medium + 'px)', {
        match: function () {
        	$q('.header3 .header-search-container').prepend($q('.left-half-column .top-header-cms-block'));
        },
        unmatch: function () {
            $q('.header3 .left-half-column').prepend($q('.header-search-container .top-header-cms-block'));
        }
    });
    
    enquire.register('(max-width: ' + bp.xsmall + 'px)', {
        match: function () {
            $q('.header3 .header-logo-container').append($q('.header-search-container .top-header-cms-block'));
        },
        unmatch: function () {
            $q('.header3 .header-search-container').prepend($q('.header-logo-container .top-header-cms-block'));
        }
    });
    // ==============================================
    // IWD: D3 Header  Shopping Cart Menu Switcher
    // ==============================================
    enquire.register('(max-width: ' + bp.medium + 'px)', {
        match: function () {
        	$q('.top-page-header .cart-container').prepend($q('.table-row-container .header-minicart'));
        },
        unmatch: function () {
            $q('.skip-links.cart-only').prepend($q('.cart-container .header-minicart'));
        }
    });
});

// custom header select 
$q(window).on('load', function () {
	$q('#select-store').selectpicker();
	$q('#select-language').selectpicker();
	$q('#select-currency').selectpicker();
});
