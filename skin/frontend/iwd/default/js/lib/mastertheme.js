jQuery.fn.extend({
	    listToColumns: function(numCols) {
	    	jQuery(this).find(' li.li-column > li').unwrap();
	    	jQuery(this).find(' li.li-column > ul').unwrap();
	    	jQuery(this).find(' ul.column > li').unwrap();
	    	
	    	jQuery(this).find('  li.li-column:empty').remove();
	    	jQuery(this).find('  ul.column:empty').remove();
	    	//prepare li
	    	jQuery(this).find(' > li.level1').each(function(){
				if(jQuery(this).find(' > ul').size)
				{
					var links = jQuery(this).find(' > ul').find('> li');
					jQuery(this).addClass('notLast');
					jQuery(this).after(links);
				}
			});
	    	
	        var listItems = jQuery(this).find('> li').not('.nav-cms-block'); /* get the list data */
	        var listHeader = jQuery(this);
	        var numListItems = listItems.length;
	        
	        var notFirstItems = jQuery(this).find('> li.not-first').length;
	        var numItemsPerCol = Math.ceil((numListItems - notFirstItems) / numCols); /* divide by the number of columns requires */
	        var currentColNum = 1, currentItemNumber = 1, returnHtml = '', i = 0;
	        /* append the columns */
	        for (i=1;i<=numCols;i++) {
	            jQuery(this).append('<li class="li-column column-'+ numCols+'"><ul class="column level1 list-column-' + i + ' "></ul></li>');
	        }
	        /* append the items to the columns */
	        jQuery.each(listItems, function (i, v)
	        {    
	            if (currentItemNumber <= numItemsPerCol){
	                	currentItemNumber ++;
	            }
	            else {
	            	if(!jQuery(v).hasClass('not-first'))
	            	{
	            		currentItemNumber = 2;
	            		currentColNum ++;
	            	}
	            }
	            jQuery(listHeader).find('.list-column-'+currentColNum).append(v);
	        });
	        jQuery(this).find('  > li.li-column:empty').remove();
	    },
	    columnsToList: function()
	    {
	    	
	    	jQuery(this).find(' li.li-column > li').unwrap();
	    	jQuery(this).find(' li.li-column > ul').unwrap();
	    	jQuery(this).find(' ul.column > li').unwrap();
	    	
	    	jQuery(this).find(' li.li-column:empty').remove();
	    	jQuery(this).find(' ul.column:empty').remove();
			
	    	var listItems = jQuery(this).find('> li');
			jQuery(listItems).each(function(){
				var children = jQuery(this).nextUntil( '.level1');
				if(children.length)
				{
					jQuery(this).find('> ul.level1').append(children);
				}
				
			});
	    }
	});
	
	
jQuery(document).ready(function(){
	//breakpoints 
	var desktop = 1200;
	var desktop_small = 1000;
	var tablet = 770;//due to D1 header design, enable standard Magento mobile navigation, 
	var mobile = 480;//enable standard Magento mobile navigation
	
	function getColumnsCount(container_width) {
		//due to D1 header design (container_width < tablet) ? 3 :
		return (container_width < desktop_small) ? 4 :
	    	   (container_width < desktop) ? 5: 5;
	}
	
	if(jQuery('.navigation-d1 .sub-wrapper').length)
	{
		var navigations = jQuery('.navigation-d1 .sub-wrapper');

		var container_width = jQuery('body').width();
		jQuery(navigations).each(function(){
			if(container_width > tablet)
			{
				jQuery(this).listToColumns(getColumnsCount(container_width)); 
			}
			
		});
		
		jQuery(window).resize(function() {
			var container_width = jQuery('body').width();
			var navigations = jQuery('.navigation-d1 .sub-wrapper');
			jQuery(navigations).each(function(){
				console.log(container_width+' - '+tablet);
				if(container_width <= tablet)
				{
					jQuery(this).columnsToList();
					return;
				}
				if(getColumnsCount(container_width)!= jQuery(this).find('.li-column').length)
				{
					jQuery(this).listToColumns(getColumnsCount(container_width)); 
				}
			});
		});
	}
	
	
});

