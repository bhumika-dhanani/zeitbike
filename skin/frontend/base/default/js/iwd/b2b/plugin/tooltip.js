IWD.Tooltip = {
		block:null,	
		init: function(){
			this.addBlock();
			this.createEvent();
		},
		addBlock: function(){
			this.block = $ji('<div />').addClass('b2b-tooltip');
			this.block.appendTo('body');
			this.block.hide();
		},
		createEvent: function(){
			$ji(document).on('mousemove','[data-toggle="tooltip"]',function(e){
				
				if ($ji(this).data('title')==null){
					if ($ji(this).attr('title')==""){
						return;
					}else{
						$ji(this).data('title', $ji(this).attr('title'));
						$ji(this).removeAttr('title');
					}	
				}
				
				var xOffset = e.pageX +10;
				var yOffset = e.pageY +10;
				
				IWD.Tooltip.block.show().html($ji(this).data('title'));
				IWD.Tooltip.block.css('left', xOffset)
									.css('top', yOffset);
			});
			
			$ji(document).on('mouseleave','[data-toggle="tooltip"]',function(e){
				
				IWD.Tooltip.block.hide().empty();
				
			});
			
		}
		
}
$ji(document).ready(function(){
	
	
});