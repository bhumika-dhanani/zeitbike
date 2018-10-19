<?php
$installer = $this;
$installer->startSetup();
$model = Mage::getModel('cms/block');
$block = $model->load('iwd_newsletter_bottom');
$stores = array(0);

# BACKUP STATIC BLOCKS AND WRITE NEW CONTENT 

//1. Top popup block
if($block)
{
	$dataBlock = array(
			'title' => 'BACKUP: IWD Newsletter Popup Bottom',
			'identifier' => 'iwd_newsletter_bottom_bk',
			'stores' => $stores,
			'is_active' => 0,
			'content'	=> $block->getContent()
			
	);
	try{
	$model->setData($dataBlock)->save();
	$block = $model->load('iwd_newsletter_bottom');
	$block->setContent('<p class="subtitle">Visit Us At:</p>
<p>Indulge in all we have to offer by visiting us at our other social media below!</p>')->save();
	}
	catch(Exception $e)
	{
		Mage::logException($e);
	}
	$block = $model->load('iwd_newsletter_top');
	if($block)
	{
		$dataBlock = array(
				'title' => 'BACKUP: IWD Newsletter Popup Top',
				'identifier' => 'iwd_newsletter_top_bk',
				'stores' => $stores,
				'is_active' => 0,
				'content'	=> $block->getContent()
					
		);
		try{
			$model->setData($dataBlock)->save();
			$block = $model->load('iwd_newsletter_top');
			$block->setContent('<h1 class="title">REGISTER TO GET <span class="discount_amount">20% OFF</span> YOUR FIRST ORDER!</h1>
<p>Be the first to hear about new products, special offers, latest news and sweepstakes by subscribing to the our newsletter.</p>')->save();
		}
		catch(Exception $e)
		{
			Mage::logException($e);
		}	
	}
	
	
}


$installer->endSetup();
?>