<?php
/**
 * Created by PhpStorm.
 * User: Bogdan
 * Date: 11/11/2016
 * Time: 22:01
 */
require_once 'abstract.php';

/**
 * Magento Compiler Shell Script
 *
 * @category    Mage
 * @package     Mage_Shell
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Shell_HandleOrderCreate extends Mage_Shell_Abstract
{

    public function run(){
//        $this->createOrderWithNumber(500000162,1694,5);
//        $this->createOrderWithNumber(500000160,1012,5);
//        $this->createOrderWithNumber(500000173,1710,5);
//        $this->createOrderWithNumber(500000183,1818,5);
    }

    public function createOrderWithNumber($number_id,$quote_id,$store_id){
        $store = Mage::getSingleton('core/store')->load($store_id);
        $quote = Mage::getModel('sales/quote')->setStore($store)->load($quote_id);

        // Set Sales Order Payment
        $quote->getPayment()->importData(array('method' => 'checkmo'));
        $quote->setReservedOrderId($number_id);
//        $needProducts = array(
//            1910,
//            1911,
//            1912,
//            2168,
//            2306,
//            2307,
//            2308,
//            2309,
//            2310,
//            2317,
//            2316,
//            2318,
//            2319,
//            2320,
//            2312,
//            2313,
//            2314,
//            2315,
//            2311
//        );
//        echo "Before ";
//        echo count($quote->getAllVisibleItems());
//        echo "\n\r";
//        foreach ($quote->getAllVisibleItems() as $row=>$item){
//            $itemId = $item->getProductId();
//            echo "ID=".$itemId;
//            if(!in_array($itemId,$needProducts)) {
//                echo " delete";
//                $quote->deleteItem($item);
//            }
//            echo "\n\r";
//        }
//        echo "After ";
//        echo count($quote->getAllVisibleItems());
//        echo "\n\r";
//        die('shell/handleOrderCreate.php:34');
        // Collect Totals & Save Quote
        $quote->save();

        try {
            // Create Order From Quote
            $service = Mage::getModel('sales/service_quote', $quote);
            $service->submitAll();
            $increment_id = $service->getOrder()->getRealOrderId();
        }
        catch (Exception $ex) {
            echo $ex->getMessage();
        }
        catch (Mage_Core_Exception $e) {
            echo $e->getMessage();
        }

        // Finished
        echo $increment_id;
    }
}

$shell = new Mage_Shell_HandleOrderCreate();
$shell->run();
