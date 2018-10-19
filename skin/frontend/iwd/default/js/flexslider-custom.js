(function() {
  // store the slider in a local variable
  var $window = jQuery(window),
      flexslider;

  // tiny helper function to add breakpoints
  function getGridSize() {
    return (window.innerWidth < 480) ? 1 :
    	   (window.innerWidth < 770) ? 2 :
    	   (window.innerWidth < 979) ? 3 :
           (window.innerWidth < 1200) ? 4 : 5;
  }

  $window.load(function() {
    jQuery('.featured-products').flexslider({
    	namespace: "",
		controlsContainer: jQuery(".featured-products"),
		animation: "slide",
		slideshow: false,
		animationLoop: false,
		animationSpeed: 400,
		pauseOnHover: true,
		controlNav: false,
		itemWidth: 240,
		minItems: getGridSize(), // use function to pull in initial value
	    maxItems: getGridSize(), // use function to pull in initial value
	    start: function(slider){
	    	flexslider = slider;
      }
    });
    $window.resize(function() {
        var gridSize = getGridSize();

        flexslider.vars.minItems = gridSize;
        flexslider.vars.maxItems = gridSize;
      });
  });

  // check grid size on resize event
  
}());