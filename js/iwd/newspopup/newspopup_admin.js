;
$j = jQuery;

function initClick(element){
	$j(element).click(function(){
		current_row = $j(element).closest('tr');
		if($j(current_row).next('tr').is(':hidden'))
	    {
			$j(current_row).nextAll().slice(0,5).show();
			$j(current_row).removeClass('closed');
			$j(current_row).addClass('opened');
	    }else{
	    	$j(current_row).nextAll().slice(0,5).hide();
	    	$j(current_row).removeClass('opened');
			$j(current_row).addClass('closed');
	    }	
	});
	
}

function checkIconStyle(){
	var selector = ["#row_iwdpopup_socialicons_facebookenabled", "#row_iwdpopup_socialicons_linkedinenabled", "#row_iwdpopup_socialicons_twitterenabled", "#row_iwdpopup_socialicons_googleenabled", "#row_iwdpopup_socialicons_youtubeenabled", "#row_iwdpopup_socialicons_flickrenabled", "#row_iwdpopup_socialicons_vimeoenabled", "#row_iwdpopup_socialicons_pinterestenabled", "#row_iwdpopup_socialicons_instagramenabled", "#row_iwdpopup_socialicons_foursquareenabled", "#row_iwdpopup_socialicons_tumblrenabled", "#row_iwdpopup_socialicons_rssenabled"];
	var select = $j('#row_iwdpopup_socialicons_newsiconstyle').find('select');
	var flag = $j(select).val();
	if(flag)
	{
		for(i = 0;i < selector.length; i++)
		{
			$j(selector[i]).nextAll().slice(1,5).find('input').prop('disabled', true);
		}
	}
	
	$j(select).change(function(){
		if($j(this).val()==1)
		{
			for(i = 0;i < selector.length; i++)
			{
				$j(selector[i]).nextAll().slice(1,5).find('input').prop('disabled', true);
			}
		}
		else
		{
			for(i = 0;i < selector.length; i++)
			{
				$j(selector[i]).nextAll().slice(1,5).find('input').prop('disabled', false);
			}
		}
		
	});
	
	
}
function decorateOptions(){
	
	var selector = ["#row_iwdpopup_socialicons_facebookenabled", "#row_iwdpopup_socialicons_linkedinenabled", "#row_iwdpopup_socialicons_twitterenabled", "#row_iwdpopup_socialicons_googleenabled", "#row_iwdpopup_socialicons_youtubeenabled", "#row_iwdpopup_socialicons_flickrenabled", "#row_iwdpopup_socialicons_vimeoenabled", "#row_iwdpopup_socialicons_pinterestenabled", "#row_iwdpopup_socialicons_instagramenabled", "#row_iwdpopup_socialicons_foursquareenabled", "#row_iwdpopup_socialicons_tumblrenabled", "#row_iwdpopup_socialicons_rssenabled"];
	for(i = 0;i < selector.length; i++)
	{
		if($j(selector[i]).find('select').val()==1)
			$j(selector[i]).addClass('closed');
		$j(selector[i]).nextAll().slice(0,5).hide();
		$j(selector[i]).css('font-weight', '600');
		
		$j(selector[i]).find('select').change(function(){
			current_row = $j(this).closest('tr');
			if($j(this).val()==1)
			{
				$j(current_row).nextAll().slice(0,5).show();
				$j(current_row).addClass('opened');
				$j(current_row).removeClass('closed');
				initClick($j(current_row).find('td label'));
			}
			else
			{
				$j(current_row).nextAll().slice(0,5).hide();
				$j(current_row).removeClass('opened');
				$j(current_row).removeClass('closed');
				$j(current_row).find('td label').unbind();
			}
		});
		
		initClick($j(selector[i]+'.closed').find('td label'));
		
	}
}
$j(document).ready(function() {
	decorateOptions();
	checkIconStyle();
});