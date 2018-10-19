<?php

class IWD_NewsPopup_Helper_Data extends IWD_NewsPopup_Helper_Mobiledetects {
	
	const XML_PATH_ICONSTYLE =     'iwdpopup/socialicons/newsiconstyle';
	const XML_PATH_IS_ENABLED =    'iwdpopup/general/newsenabled';
	const XML_PATH_LINK_CLASS =    'iwdpopup/general/newslinkclass';
	const XML_PATH_POPUP_WIDTH =   'iwdpopup/general/newswidth';
	const XML_PATH_BUTTON_BG =     'iwdpopup/general/newsbuttonbg';
	const XML_PATH_BUTTON_HOVER =  'iwdpopup/general/newsbuttonhover';
	const XML_PATH_BUTTON_TEXT =   'iwdpopup/general/newsbuttontext';
	const XML_PATH_ICON_BG = 	   'iwdpopup/general/newsiconbg';
	const XML_PATH_ICON_HOVER_BG = 'iwdpopup/general/newsiconhover';
	const XML_PATH_ICON_TEXT = 	   'iwdpopup/general/newsicontext';
	const XML_PATH_TOP_MARGIN =    'iwdpopup/general/newstopspace';
	const XML_PATH_LOAD_DELAY =    'iwdpopup/general/newspopupdelay';
	const XML_PATH_MULTIPLE_QUEST_SUBSCRIPTION =    'iwdpopup/general/multiplesubscriptionguest';
	
	const XML_PATH_FACEBOOK_ENABLED =  'iwdpopup/socialicons/facebookenabled';
	const XML_PATH_FACEBOOK_LINK  =    'iwdpopup/socialicons/facebooklink';
	const XML_PATH_FACEBOOK_BG = 	   'iwdpopup/socialicons/facebookbg';
	const XML_PATH_FACEBOOK_ICON  =    'iwdpopup/socialicons/facebookicon';
	const XML_PATH_FACEBOOK_BG_HOVER = 'iwdpopup/socialicons/facebookbghover';
	const XML_PATH_FACEBOOK_ICON_HOVER='iwdpopup/socialicons/facebookiconhover';
	
	const XML_PATH_TWITTER_ENABLED =   'iwdpopup/socialicons/twitterenabled';
	const XML_PATH_TWITTER_LINK  =     'iwdpopup/socialicons/twitterlink';
	const XML_PATH_TWITTER_BG = 	   'iwdpopup/socialicons/twitterbg';
	const XML_PATH_TWITTER_ICON  =     'iwdpopup/socialicons/twittericon';
	const XML_PATH_TWITTER_BG_HOVER =  'iwdpopup/socialicons/twitterbghover';
	const XML_PATH_TWITTER_ICON_HOVER= 'iwdpopup/socialicons/twittericonhover';
	
	const XML_PATH_LINKEDIN_ENABLED =  'iwdpopup/socialicons/linkedinenabled';
	const XML_PATH_LINKEDIN_LINK  =    'iwdpopup/socialicons/linkedinlink';
	const XML_PATH_LINKEDIN_BG = 	   'iwdpopup/socialicons/linkedinbg';
	const XML_PATH_LINKEDIN_ICON  =    'iwdpopup/socialicons/linkedinicon';
	const XML_PATH_LINKEDIN_BG_HOVER = 'iwdpopup/socialicons/linkedinbghover';
	const XML_PATH_LINKEDIN_ICON_HOVER='iwdpopup/socialicons/linkediniconhover';
	
	const XML_PATH_GOOGLE_ENABLED =	   'iwdpopup/socialicons/googleenabled';
	const XML_PATH_GOOGLE_LINK  =      'iwdpopup/socialicons/googlelink';
	const XML_PATH_GOOGLE_BG = 	       'iwdpopup/socialicons/googlebg';
	const XML_PATH_GOOGLE_ICON  =      'iwdpopup/socialicons/googleicon';
	const XML_PATH_GOOGLE_BG_HOVER =   'iwdpopup/socialicons/googlebghover';
	const XML_PATH_GOOGLE_ICON_HOVER=  'iwdpopup/socialicons/googleiconhover';
	
	const XML_PATH_YOUTUBE_ENABLED =   'iwdpopup/socialicons/youtubeenabled';
	const XML_PATH_YOUTUBE_LINK  =     'iwdpopup/socialicons/youtubelink';
	const XML_PATH_YOUTUBE_BG = 	   'iwdpopup/socialicons/youtubebg';
	const XML_PATH_YOUTUBE_ICON  =     'iwdpopup/socialicons/youtubeicon';
	const XML_PATH_YOUTUBE_BG_HOVER =  'iwdpopup/socialicons/youtubebghover';
	const XML_PATH_YOUTUBE_ICON_HOVER= 'iwdpopup/socialicons/youtubeiconhover';
	
	const XML_PATH_FLICKR_ENABLED =    'iwdpopup/socialicons/flickrenabled';
	const XML_PATH_FLICKR_LINK  =      'iwdpopup/socialicons/flickrlink';
	const XML_PATH_FLICKR_BG = 	       'iwdpopup/socialicons/flickrbg';
	const XML_PATH_FLICKR_ICON  =      'iwdpopup/socialicons/flickricon';
	const XML_PATH_FLICKR_BG_HOVER =   'iwdpopup/socialicons/flickrbghover';
	const XML_PATH_FLICKR_ICON_HOVER=  'iwdpopup/socialicons/flickriconhover';
	
	const XML_PATH_VIMEO_ENABLED = 	   'iwdpopup/socialicons/vimeoenabled';
	const XML_PATH_VIMEO_LINK  =       'iwdpopup/socialicons/vimeolink';
	const XML_PATH_VIMEO_BG = 	       'iwdpopup/socialicons/vimeobg';
	const XML_PATH_VIMEO_ICON  =       'iwdpopup/socialicons/vimeoicon';
	const XML_PATH_VIMEO_BG_HOVER =    'iwdpopup/socialicons/vimeobghover';
	const XML_PATH_VIMEO_ICON_HOVER=   'iwdpopup/socialicons/vimeoiconhover';
	
	const XML_PATH_PINTEREST_ENABLED=   'iwdpopup/socialicons/pinterestenabled';
	const XML_PATH_PINTEREST_LINK  =    'iwdpopup/socialicons/pinterestlink';
	const XML_PATH_PINTEREST_BG = 	    'iwdpopup/socialicons/pinterestbg';
	const XML_PATH_PINTEREST_ICON  =    'iwdpopup/socialicons/pinteresticon';
	const XML_PATH_PINTEREST_BG_HOVER = 'iwdpopup/socialicons/pinterestbghover';
	const XML_PATH_PINTEREST_ICON_HOVER='iwdpopup/socialicons/pinteresticonhover';
	
	const XML_PATH_INSTAGRAM_ENABLED=   'iwdpopup/socialicons/instagramenabled';
	const XML_PATH_INSTAGRAM_LINK  =    'iwdpopup/socialicons/instagramlink';
	const XML_PATH_INSTAGRAM_BG = 	    'iwdpopup/socialicons/instagrambg';
	const XML_PATH_INSTAGRAM_ICON  =    'iwdpopup/socialicons/instagramicon';
	const XML_PATH_INSTAGRAM_BG_HOVER = 'iwdpopup/socialicons/instagrambghover';
	const XML_PATH_INSTAGRAM_ICON_HOVER='iwdpopup/socialicons/instagramiconhover';
	
	const XML_PATH_FORSQUARE_ENABLED=   'iwdpopup/socialicons/foursquareenabled';
	const XML_PATH_FORSQUARE_LINK  =    'iwdpopup/socialicons/foursquarelink';
	const XML_PATH_FORSQUARE_BG = 	    'iwdpopup/socialicons/foursquarebg';
	const XML_PATH_FORSQUARE_ICON  =    'iwdpopup/socialicons/foursquareicon';
	const XML_PATH_FORSQUARE_BG_HOVER = 'iwdpopup/socialicons/foursquarebghover';
	const XML_PATH_FORSQUARE_ICON_HOVER='iwdpopup/socialicons/foursquareiconhover';
	
	const XML_PATH_TUMBLR_ENABLED =     'iwdpopup/socialicons/tumblrenabled';
	const XML_PATH_TUMBLR_LINK  =       'iwdpopup/socialicons/tumblrlink';
	const XML_PATH_TUMBLR_BG = 	        'iwdpopup/socialicons/tumblrbg';
	const XML_PATH_TUMBLR_ICON  =       'iwdpopup/socialicons/tumblricon';
	const XML_PATH_TUMBLR_BG_HOVER =    'iwdpopup/socialicons/tumblrbghover';
	const XML_PATH_TUMBLR_ICON_HOVER=   'iwdpopup/socialicons/tumblriconhover';
	
	const XML_PATH_RSS_ENABLED = 	  	'iwdpopup/socialicons/rssenabled';
	const XML_PATH_RSS_LINK   =   	  	'iwdpopup/socialicons/rsslink';
	const XML_PATH_RSS_BG = 	    	'iwdpopup/socialicons/rssbg';
	const XML_PATH_RSS_ICON  =     		'iwdpopup/socialicons/rssicon';
	const XML_PATH_RSS_BG_HOVER =  		'iwdpopup/socialicons/rssbghover';
	const XML_PATH_RSS_ICON_HOVER= 		'iwdpopup/socialicons/rssiconhover';
	
	private $_version = 'CE';
	
	function hex2rgb($hex) {
		$hex = str_replace("#", "", $hex);
	
		if(strlen($hex) == 3) {
			$r = hexdec(substr($hex,0,1).substr($hex,0,1));
			$g = hexdec(substr($hex,1,1).substr($hex,1,1));
			$b = hexdec(substr($hex,2,1).substr($hex,2,1));
		} else {
			$r = hexdec(substr($hex,0,2));
			$g = hexdec(substr($hex,2,2));
			$b = hexdec(substr($hex,4,2));
		}
		$rgb = array($r, $g, $b);
		return implode(',', $rgb); 
	}
	

	public function getLoadDelay()
	{
		return trim(Mage::getStoreConfig(self::XML_PATH_LOAD_DELAY));
	}
	
	public function getNewsLinkClass(){
		return Mage::getStoreConfig(self::XML_PATH_LINK_CLASS);
	}
	
	public function getJsonConfig(){
		$config = array();
		$config['newsLinkClass'] = Mage::getStoreConfig(self::XML_PATH_LINK_CLASS);
		$config['loadDelay'] = trim(Mage::getStoreConfig(self::XML_PATH_LOAD_DELAY));
		$config['url'] = Mage::getBaseUrl();
		return Mage::helper('core')->jsonEncode($config);
	}
	
	public function isEnabled()
	{
		 if(Mage::getStoreConfig(self::XML_PATH_IS_ENABLED) && Mage::helper('iwdpopup')->isAvailableVersion()) 
		 	return true;
		 return false;
	}
	
	public function isMobileStyle()
	{
		$width = trim(Mage::getStoreConfig(self::XML_PATH_POPUP_WIDTH));
		if($width<=380)
			return true;
		return false;
	}
	
	public function generateCss()
	{
		$file = Mage::getBaseDir('skin').'/frontend/base/default/css/iwd/newspopup/newspopup_generated.css';
		$handle = fopen($file, 'w');
	
		$button_bg    = trim(trim(Mage::getStoreConfig(self::XML_PATH_BUTTON_BG)), '#');
		$button_hover = trim(trim(Mage::getStoreConfig(self::XML_PATH_BUTTON_HOVER)), '#');
		$button_text  = trim(trim(Mage::getStoreConfig(self::XML_PATH_BUTTON_TEXT)), '#');
		$button_bg_rgba = Mage::helper('iwdpopup')->hex2rgb($button_bg);
	
		$icon_bg 	= trim(trim(Mage::getStoreConfig(self::XML_PATH_ICON_BG)), '#');
		$icon_hover = trim(trim(Mage::getStoreConfig(self::XML_PATH_ICON_HOVER_BG)), '#');
		$icon_text  = trim(trim(Mage::getStoreConfig(self::XML_PATH_ICON_TEXT)), '#');
		$icon_style = Mage::getStoreConfig(self::XML_PATH_ICONSTYLE);
	
		$popup_width  = trim(Mage::getStoreConfig(self::XML_PATH_POPUP_WIDTH));
		$popup_top_margin = trim(Mage::getStoreConfig(self::XML_PATH_TOP_MARGIN));
	
	
		$data = "/* This css file was generate automatically by IWD Quick View extension, please dont change it.File regenerated each time admin QuickView settings changed*/";
	
		$data.="#popup-newssubscribe #newsletter_submit{background:#".$button_bg." !important}";
		$data.='#popup-newssubscribe .modal-dialog{max-width:'.$popup_width.'px;margin-top:'.$popup_top_margin.'px}';
		$data.="#popup-newssubscribe #newsletter_submit:hover{background:#".$button_hover." !important}";
		$data.="#popup-newssubscribe #newsletter_submit span{color:#".$button_text." !important}";

        /************max***********/
        $data.="#popup-newssubscribe #popup_message .buttons_block .start_shopping{background:#".$button_bg." !important}";
        $data.="#popup-newssubscribe #popup_message .buttons_block .start_shopping:hover{background:#".$button_hover." !important}";
        $data.="#popup-newssubscribe #popup_message .buttons_block .start_shopping span{color:#".$button_text." !important}";

        /**********end max**********/


		if(!$icon_style)
		{
			$data.="#popup-newssubscribe .news_popup .social_links  a.facebook span.fa-square{color:#".trim(trim(Mage::getStoreConfig(self::XML_PATH_FACEBOOK_BG)), '#')."}";
			$data.="#popup-newssubscribe .news_popup .social_links  a.facebook span:hover .fa-square{color:#".trim(trim(Mage::getStoreConfig(self::XML_PATH_FACEBOOK_BG_HOVER)), '#')."}";
			$data.="#popup-newssubscribe .news_popup .social_links  a.facebook span.fa-stack-20{color:#".trim(trim(Mage::getStoreConfig(self::XML_PATH_FACEBOOK_ICON)), '#')."}";
			$data.="#popup-newssubscribe .news_popup .social_links  a.facebook span:hover .fa-stack-20{color:#".trim(trim(Mage::getStoreConfig(self::XML_PATH_FACEBOOK_ICON_HOVER)), '#')."}";
			
			$data.="#popup-newssubscribe .news_popup .social_links  a.twitter span.fa-square{color:#".trim(trim(Mage::getStoreConfig(self::XML_PATH_TWITTER_BG)), '#')."}";
			$data.="#popup-newssubscribe .news_popup .social_links  a.twitter span:hover .fa-square{color:#".trim(trim(Mage::getStoreConfig(self::XML_PATH_TWITTER_BG_HOVER)), '#')."}";
			$data.="#popup-newssubscribe .news_popup .social_links  a.twitter span.fa-stack-20{color:#".trim(trim(Mage::getStoreConfig(self::XML_PATH_TWITTER_ICON)), '#')."}";
			$data.="#popup-newssubscribe .news_popup .social_links  a.twitter span:hover .fa-stack-20{color:#".trim(trim(Mage::getStoreConfig(self::XML_PATH_TWITTER_ICON_HOVER)), '#')."}";
				
			$data.="#popup-newssubscribe .news_popup .social_links  a.linkedin span.fa-square{color:#".trim(trim(Mage::getStoreConfig(self::XML_PATH_LINKEDIN_BG)), '#')."}";
			$data.="#popup-newssubscribe .news_popup .social_links  a.linkedin span:hover .fa-square{color:#".trim(trim(Mage::getStoreConfig(self::XML_PATH_LINKEDIN_BG_HOVER)), '#')."}";
			$data.="#popup-newssubscribe .news_popup .social_links  a.linkedin span.fa-stack-20{color:#".trim(trim(Mage::getStoreConfig(self::XML_PATH_LINKEDIN_ICON)), '#')."}";
			$data.="#popup-newssubscribe .news_popup .social_links  a.linkedin span:hover .fa-stack-20{color:#".trim(trim(Mage::getStoreConfig(self::XML_PATH_LINKEDIN_ICON_HOVER)), '#')."}";
				
			$data.="#popup-newssubscribe .news_popup .social_links  a.google span.fa-square{color:#".trim(trim(Mage::getStoreConfig(self::XML_PATH_GOOGLE_BG)), '#')."}";
			$data.="#popup-newssubscribe .news_popup .social_links  a.google span:hover .fa-square{color:#".trim(trim(Mage::getStoreConfig(self::XML_PATH_GOOGLE_BG_HOVER)), '#')."}";
			$data.="#popup-newssubscribe .news_popup .social_links  a.google span.fa-stack-20{color:#".trim(trim(Mage::getStoreConfig(self::XML_PATH_FACEBOOK_ICON)), '#')."}";
			$data.="#popup-newssubscribe .news_popup .social_links  a.google span:hover .fa-stack-20{color:#".trim(trim(Mage::getStoreConfig(self::XML_PATH_GOOGLE_ICON_HOVER)), '#')."}";
				
			$data.="#popup-newssubscribe .news_popup .social_links  a.youtube span.fa-square{color:#".trim(trim(Mage::getStoreConfig(self::XML_PATH_YOUTUBE_BG)), '#')."}";
			$data.="#popup-newssubscribe .news_popup .social_links  a.youtube span:hover .fa-square{color:#".trim(trim(Mage::getStoreConfig(self::XML_PATH_YOUTUBE_BG_HOVER)), '#')."}";
			$data.="#popup-newssubscribe .news_popup .social_links  a.youtube span.fa-stack-20{color:#".trim(trim(Mage::getStoreConfig(self::XML_PATH_YOUTUBE_ICON)), '#')."}";
			$data.="#popup-newssubscribe .news_popup .social_links  a.youtube span:hover .fa-stack-20{color:#".trim(trim(Mage::getStoreConfig(self::XML_PATH_YOUTUBE_ICON_HOVER)), '#')."}";
				
			$data.="#popup-newssubscribe .news_popup .social_links  a.flickr span.fa-square{color:#".trim(trim(Mage::getStoreConfig(self::XML_PATH_FLICKR_BG)), '#')."}";
			$data.="#popup-newssubscribe .news_popup .social_links  a.flickr span:hover .fa-square{color:#".trim(trim(Mage::getStoreConfig(self::XML_PATH_FLICKR_BG_HOVER)), '#')."}";
			$data.="#popup-newssubscribe .news_popup .social_links  a.flickr span.fa-stack-20{color:#".trim(trim(Mage::getStoreConfig(self::XML_PATH_FLICKR_ICON)), '#')."}";
			$data.="#popup-newssubscribe .news_popup .social_links  a.flickr span:hover .fa-stack-20{color:#".trim(trim(Mage::getStoreConfig(self::XML_PATH_FLICKR_ICON_HOVER)), '#')."}";
				
			$data.="#popup-newssubscribe .news_popup .social_links  a.vimeo span.fa-square{color:#".trim(trim(Mage::getStoreConfig(self::XML_PATH_VIMEO_BG)), '#')."}";
			$data.="#popup-newssubscribe .news_popup .social_links  a.vimeo span:hover .fa-square{color:#".trim(trim(Mage::getStoreConfig(self::XML_PATH_VIMEO_BG_HOVER)), '#')."}";
			$data.="#popup-newssubscribe .news_popup .social_links  a.vimeo span.fa-stack-20{color:#".trim(trim(Mage::getStoreConfig(self::XML_PATH_VIMEO_ICON)), '#')."}";
			$data.="#popup-newssubscribe .news_popup .social_links  a.vimeo span:hover .fa-stack-20{color:#".trim(trim(Mage::getStoreConfig(self::XML_PATH_VIMEO_ICON_HOVER)), '#')."}";
				
			$data.="#popup-newssubscribe .news_popup .social_links  a.pinterest span.fa-square{color:#".trim(trim(Mage::getStoreConfig(self::XML_PATH_PINTEREST_BG)), '#')."}";
			$data.="#popup-newssubscribe .news_popup .social_links  a.pinterest span:hover .fa-square{color:#".trim(trim(Mage::getStoreConfig(self::XML_PATH_PINTEREST_BG_HOVER)), '#')."}";
			$data.="#popup-newssubscribe .news_popup .social_links  a.pinterest span.fa-stack-20{color:#".trim(trim(Mage::getStoreConfig(self::XML_PATH_PINTEREST_ICON)), '#')."}";
			$data.="#popup-newssubscribe .news_popup .social_links  a.pinterest span:hover .fa-stack-20{color:#".trim(trim(Mage::getStoreConfig(self::XML_PATH_PINTEREST_ICON_HOVER)), '#')."}";
				
			$data.="#popup-newssubscribe .news_popup .social_links  a.instagram span.fa-square{color:#".trim(trim(Mage::getStoreConfig(self::XML_PATH_INSTAGRAM_BG)), '#')."}";
			$data.="#popup-newssubscribe .news_popup .social_links  a.instagram span:hover .fa-square{color:#".trim(trim(Mage::getStoreConfig(self::XML_PATH_INSTAGRAM_BG_HOVER)), '#')."}";
			$data.="#popup-newssubscribe .news_popup .social_links  a.instagram span.fa-stack-20{color:#".trim(trim(Mage::getStoreConfig(self::XML_PATH_INSTAGRAM_ICON)), '#')."}";
			$data.="#popup-newssubscribe .news_popup .social_links  a.instagram span:hover .fa-stack-20{color:#".trim(trim(Mage::getStoreConfig(self::XML_PATH_INSTAGRAM_ICON_HOVER)), '#')."}";
				
			$data.="#popup-newssubscribe .news_popup .social_links  a.forsqr span.fa-square{color:#".trim(trim(Mage::getStoreConfig(self::XML_PATH_FORSQUARE_BG)), '#')."}";
			$data.="#popup-newssubscribe .news_popup .social_links  a.forsqr span:hover .fa-square{color:#".trim(trim(Mage::getStoreConfig(self::XML_PATH_FORSQUARE_BG_HOVER)), '#')."}";
			$data.="#popup-newssubscribe .news_popup .social_links  a.forsqr span.fa-stack-20{color:#".trim(trim(Mage::getStoreConfig(self::XML_PATH_FORSQUARE_ICON)), '#')."}";
			$data.="#popup-newssubscribe .news_popup .social_links  a.forsqr span:hover .fa-stack-20{color:#".trim(trim(Mage::getStoreConfig(self::XML_PATH_FORSQUARE_ICON_HOVER)), '#')."}";
				
			$data.="#popup-newssubscribe .news_popup .social_links  a.tumblr span.fa-square{color:#".trim(trim(Mage::getStoreConfig(self::XML_PATH_TUMBLR_BG)), '#')."}";
			$data.="#popup-newssubscribe .news_popup .social_links  a.tumblr span:hover .fa-square{color:#".trim(trim(Mage::getStoreConfig(self::XML_PATH_TUMBLR_BG_HOVER)), '#')."}";
			$data.="#popup-newssubscribe .news_popup .social_links  a.tumblr span.fa-stack-20{color:#".trim(trim(Mage::getStoreConfig(self::XML_PATH_TUMBLR_ICON)), '#')."}";
			$data.="#popup-newssubscribe .news_popup .social_links  a.tumblr span:hover .fa-stack-20{color:#".trim(trim(Mage::getStoreConfig(self::XML_PATH_TUMBLR_ICON_HOVER)), '#')."}";
				
			$data.="#popup-newssubscribe .news_popup .social_links  a.rss span.fa-square{color:#".trim(trim(Mage::getStoreConfig(self::XML_PATH_RSS_BG)), '#')."}";
			$data.="#popup-newssubscribe .news_popup .social_links  a.rss span:hover .fa-square{color:#".trim(trim(Mage::getStoreConfig(self::XML_PATH_RSS_BG_HOVER)), '#')."}";
			$data.="#popup-newssubscribe .news_popup .social_links  a.rss span.fa-stack-20{color:#".trim(trim(Mage::getStoreConfig(self::XML_PATH_RSS_ICON)), '#')."}";
			$data.="#popup-newssubscribe .news_popup .social_links  a.rss span:hover .fa-stack-20{color:#".trim(trim(Mage::getStoreConfig(self::XML_PATH_RSS_ICON_HOVER)), '#')."}";
				
		//	$data.="#popup-newssubscribe .news_popup .social_links a span.fa{color:#".$icon_text." !important}";
		//	$data.="#popup-newssubscribe .news_popup .social_links a span.fa-square, #popup-newssubscribe .news_popup .social_links a span.fa-circle{color:#".$icon_bg." !important}";
		//	$data.="#popup-newssubscribe .news_popup .social_links a span:hover .fa-square, #popup-newssubscribe .news_popup .social_links a:hover .fa-square{color:#".$icon_hover." !important}";
		}
		$data.="#popup-newssubscribe .modal-content{border:4px solid rgba(".$button_bg_rgba.", 0.5) !important}";
		$data.="#popup-newssubscribe .modal-content .discount_amount{color:#".$button_bg." !important}";



		fwrite($handle, $data);
		fclose($handle);
	}
	
	
	
	public function getSocialIcons(){
		
		if(Mage::getStoreConfig(self::XML_PATH_ICONSTYLE))
			$class ='social_links standard';
		else
			$class ='social_links';
		$html='<div class="'.$class.'">';
		
		if(Mage::getStoreConfig(self::XML_PATH_FACEBOOK_ENABLED))
		{
			$link = Mage::getStoreConfig(self::XML_PATH_FACEBOOK_LINK);
			if($link){
				$facebook = '<a href="'.$link.'" class="facebook" target="_blank"><span class="fa-stack fa-lg"> <span class="fa fa-square fa-stack-40">&nbsp;</span> <span class="fa fa-facebook fa-stack-20">&nbsp;</span> </span></a>';
				$html.=$facebook;
			}
		}
		if(Mage::getStoreConfig(self::XML_PATH_TWITTER_ENABLED))
		{
			$link = Mage::getStoreConfig(self::XML_PATH_TWITTER_LINK);
			if($link){
				$twitter = 	'<a href="'.$link.'" class="twitter" target="_blank"><span class="fa-stack fa-lg"> <span class="fa fa-square fa-stack-40">&nbsp;</span> <span class="fa fa-twitter fa-stack-20">&nbsp;</span> </span></a>';
				$html.=$twitter;
			}
		}	
		if(Mage::getStoreConfig(self::XML_PATH_LINKEDIN_ENABLED))
		{
			$link = Mage::getStoreConfig(self::XML_PATH_LINKEDIN_LINK);
			if($link){
				$linkedin = '<a href="'.$link.'" class="linkedin" target="_blank"><span class="fa-stack fa-lg"> <span class="fa fa-square fa-stack-40">&nbsp;</span> <span class="fa fa-linkedin fa-stack-20">&nbsp;</span> </span></a>';
				$html.= $linkedin;
			}
		}
		if(Mage::getStoreConfig(self::XML_PATH_GOOGLE_ENABLED))
		{
			$link = Mage::getStoreConfig(self::XML_PATH_GOOGLE_LINK);
			if($link){
				$google = '<a href="'.$link.'" class="google" target="_blank"><span class="fa-stack fa-lg"> <span class="fa fa-square fa-stack-40">&nbsp;</span> <span class="fa fa-google-plus fa-stack-20">&nbsp;</span> </span></a>';
				$html.= $google;
			}
		}
		if(Mage::getStoreConfig(self::XML_PATH_YOUTUBE_ENABLED))
		{
			$link = Mage::getStoreConfig(self::XML_PATH_YOUTUBE_LINK);
			if($link){
				$youtube = '<a href="'.$link.'" class="youtube" target="_blank"><span class="fa-stack fa-lg"> <span class="fa fa-square fa-stack-40">&nbsp;</span> <span class="fa fa-youtube fa-stack-20">&nbsp;</span> </span></a>';
				$html.= $youtube;
			}
		}
		if(Mage::getStoreConfig(self::XML_PATH_FLICKR_ENABLED))
		{
			$link = Mage::getStoreConfig(self::XML_PATH_FLICKR_LINK);
			if($link){
				$flikr = '<a href="'.$link.'" class="flickr" target="_blank"><span class="fa-stack fa-lg"> <span class="fa fa-square fa-stack-40">&nbsp;</span> <span class="fa fa-flickr fa-stack-20">&nbsp;</span> </span></a>';
				$html.= $flikr;
			}
		}
		if(Mage::getStoreConfig(self::XML_PATH_VIMEO_ENABLED))
		{
			$link = Mage::getStoreConfig(self::XML_PATH_VIMEO_LINK);
			if($link){
				$vimeo = '<a href="'.$link.'" class="vimeo" target="_blank"><span class="fa-stack fa-lg"> <span class="fa fa-square fa-stack-40">&nbsp;</span> <span class="fa fa-vimeo-square fa-stack-20">&nbsp;</span> </span></a>';
				$html.= $vimeo;
			}
		}
		if(Mage::getStoreConfig(self::XML_PATH_PINTEREST_ENABLED))
		{
			$link = Mage::getStoreConfig(self::XML_PATH_PINTEREST_LINK);
			if($link){
				$pinterest = '<a href="'.$link.'" class="pinterest" target="_blank"><span class="fa-stack fa-lg"> <span class="fa fa-square fa-stack-40">&nbsp;</span> <span class="fa fa-pinterest fa-stack-20">&nbsp;</span> </span></a>';
				$html.= $pinterest;
			}
		}
		if(Mage::getStoreConfig(self::XML_PATH_INSTAGRAM_ENABLED))
		{
			$link = Mage::getStoreConfig(self::XML_PATH_INSTAGRAM_LINK);
			if($link){
				$instagram ='<a href="'.$link.'" class="instagram" target="_blank"><span class="fa-stack fa-lg"> <span class="fa fa-square fa-stack-40">&nbsp;</span> <span class="fa fa-instagram fa-stack-20">&nbsp;</span> </span></a>';
				$html.= $instagram;
			}
		}
		if(Mage::getStoreConfig(self::XML_PATH_FORSQUARE_ENABLED))
		{
			$link = Mage::getStoreConfig(self::XML_PATH_FORSQUARE_LINK);
			if($link){
				$forsqr = '<a href="'.$link.'" class="forsqr" target="_blank"><span class="fa-stack fa-lg"> <span class="fa fa-square fa-stack-40">&nbsp;</span> <span class="fa fa-foursquare fa-stack-20">&nbsp;</span> </span></a>';
				$html.= $forsqr;
			}
		}
		if(Mage::getStoreConfig(self::XML_PATH_TUMBLR_ENABLED))
		{
			$link = Mage::getStoreConfig(self::XML_PATH_TUMBLR_LINK);
			if($link){
				$tumblr = '<a href="'.$link.'" class="tumblr" target="_blank"><span class="fa-stack fa-lg"> <span class="fa fa-square fa-stack-40">&nbsp;</span> <span class="fa fa-tumblr fa-stack-20">&nbsp;</span> </span></a>';
				$html.= $tumblr;
			}
		}
		if(Mage::getStoreConfig(self::XML_PATH_RSS_ENABLED))
		{
			$link = Mage::getStoreConfig(self::XML_PATH_RSS_LINK);
			if($link){
				$rss = '<a href="'.$link.'" class="rss" target="_blank"><span class="fa-stack fa-lg"> <span class="fa fa-square fa-stack-40">&nbsp;</span> <span class="fa fa-rss fa-stack-20">&nbsp;</span> </span></a>';
				$html.= $rss;
			}
		}
		$html.='</div>';
		return $html;
	}
	
	public function isAvailableVersion(){
	
		$mage  = new Mage();
		if (!is_callable(array($mage, 'getEdition'))){
			$edition = 'Community';
		}else{
			$edition = Mage::getEdition();
		}
		unset($mage);
			
		if ($edition=='Enterprise' && $this->_version=='CE'){
			return false;
		}
		return true;
	
	}
}

