<?php
$installer = $this;
$installer->startSetup();
$block = Mage::getModel('cms/block');
$page = Mage::getModel('cms/page');
$stores = array(0);

# INSERT STATIC BLOCKS 

//1. Top popup block
$dataBlock = array(
	'title' => 'IWD Newsletter Popup Top',
	'identifier' => 'iwd_newsletter_top',
	'stores' => $stores,
	'is_active' => 1,
	'content'	=> <<<EOB
		<h1 class="title">REGISTER TO GET <span class="discount_amount">20% OFF</span> YOUR FIRST ORDER!</h1>
<p>Be the first to hear about new products, special offers, latest news and sweepstakes by subscribing to the our newsletter.</p>
EOB
);
$block->setData($dataBlock)->save();

//2.Bottom popup block
$dataBlock = array(
	'title' => 'IWD Newsletter Popup Bottom',
	'identifier' => 'iwd_newsletter_bottom',
	'stores' => $stores,
	'is_active' => 1,
	'content'	=> <<<EOB
		<p class="subtitle">Visit Us At:</p>
<p>Indulge in all we have to offer by visiting us at our other social media below!</p>
EOB
);
$block->setData($dataBlock)->save();

$installer->endSetup();
?>