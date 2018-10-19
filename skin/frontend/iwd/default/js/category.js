var bp_category = {
    xsmall: 479,
    small: 500,
    medium: 770,
    large: 979,
    xlarge: 1199
}
var $j = jQuery.noConflict();

$j(document).ready(function () {
	//custom toolbar select 
	//$j('.toolbar-top .toolbar .sort-by select').selectpicker();
	//$j('.toolbar-bottom .toolbar .limiter select').selectpicker();
	
	// ==============================================
	// Layered Navigation Block
	// ==============================================
	
	// On product list pages, we want to show the layered nav/category menu immediately above the product list.
	// While it would make more sense to just move the .block-layered-nav block rather than .col-left-first
	// (since other blocks can be inserted into left_first), it creates simpler code to move the entire
	// .col-left-first block, so that is the approach we're taking
	if ($j('.col-left-first > .block').length && $j('.category-products').length) {
	    enquire.register('screen and (max-width: ' + bp_category.medium + 'px)', {
	        match: function () {
	            $j('.col-left-first').insertBefore($j('.category-products'))
	        },
	        unmatch: function () {
	            // Move layered nav back to left column
	            $j('.col-left-first').insertBefore($j('.col-main'))
	        }
	    });
	}
});