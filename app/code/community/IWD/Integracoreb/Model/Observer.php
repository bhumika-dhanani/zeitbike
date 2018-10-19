<?php
class IWD_Integracoreb_Model_Observer extends Mage_Core_Model_Abstract {

    public $xmlFields = array(
        'SiteID'=>1,
        'OrderNumber'=>'Testing',
        'ShipToCustID'=>'Testing',
        'BillToCustID'=>'Testing',
        'CreateDate'=>'',
        'RequiredShipDate'=>'',
        'Carrier'=>'UPS',
        'CarrierServiceCode'=>'UPS G',
        'ThirdPartyAccountNumber'=>'',
        'PrepaidOrCollect'=>'02',
        'ShipToContact'=>'',
        'ShipToName'=>'ShipToName',
        'ShipToAddr1'=>'ShipToAddr1',
        'ShipToCity'=>'ShipToCity',
        'ShipToState'=>'ShipToState',
        'ShipToZip'=>'ShipToZip',
        'ShipToCountry'=>'ShipToCountry',
        'DropShip'=>'T',
        'ResidentialFlag'=>'T',
        'PackSlipRequired'=>'F',
        'OrderDetails'=>array(
            array(
                'ItemID'=>'00714',
                'Quantity'=>1,
                'SalePrice'=> 1.4,
                'CustItemID'=> 'RED ITEM',
                'Custom1' => 'Red'
            )
        )
    );
    public $username = 'zeitbike';//Testing
    public $password = 'zeit001!';//Test
    public $test = false;
    public $orderPrefix = 'ZEIT';//zbt
    public $tmpOrderId = Null;
    public $statusCheck = array(
        Mage_Sales_Model_Order::STATE_PROCESSING,
    );
    public $cntOrdersSend = 1;
    public $statusModule = true;
    public $test_mode = true;

    public function initSetting(){
        $configValue = Mage::getStoreConfig('iwdintegracoreb/general/order_status');
        if(!empty($configValue)){
            $this->statusCheck = explode(',',$configValue);
        }

        $statusModule = Mage::getStoreConfig('iwdintegracoreb/general/status_module');
        if(empty($statusModule) || !$statusModule){
            $this->statusModule = false;
        }
        $test_mode = Mage::getStoreConfig('iwdintegracoreb/edit/test_mode');
        if(empty($test_mode) || !$test_mode){
            $this->test_mode = false;
        }
    }

    public function getOrdersData(){
//        var_dump($this->statusCheck);
//        echo '<pre>'.print_r($this->statusCheck,true).'</pre>';
        $processingOrders = Mage::getModel('sales/order')->getCollection()
            ->addFieldToFilter('status', array('in'=>$this->statusCheck))
        ;
        $orderData = array();
//        $cntItem = 0;
        $cntSendOrdersReal = 0;
//        echo 'count='.count($processingOrders);die();
        foreach ($processingOrders as $itemId => $item)
        {
            if($this->test_mode)
                if($cntSendOrdersReal==$this->cntOrdersSend) break;

//            echo '<br>$item->canShip()='.($item->canShip());
//            echo '<br>$item->getForcedDoShipmentWithInvoice()='.(!$item->getForcedDoShipmentWithInvoice());
            if($item->canShip() && !$item->getForcedDoShipmentWithInvoice()) {
                $arrNeed = array();
                $Increment_id = $item->getIncrement_id();
                $OrderId = $item->getId();
//                echo 'orderId=';
                $arrNeed['OrderId'] = $OrderId;
                $arrNeed['OrderNumber'] = $this->orderPrefix.$Increment_id;
                $arrNeed['ShipToCustID'] = $this->username;
                $arrNeed['BillToCustID'] = $this->username;
                $arrNeed['CreateDate'] = str_replace(' ', 'T', $item->getCreated_at());
                $arrNeed['RequiredShipDate'] = $arrNeed['CreateDate'];
                $arrNeed['ShipToName'] = $item->getCustomer_firstname() . ' ' . $item->getCustomer_lastname();
                $ShippingAddress = $item->getShippingAddress();
//              var_dump($ShippingAddress->getData());
//              var_dump($ShippingAddress->getStreet());
                $Streets = $ShippingAddress->getStreet();
                $cnt = 1;
                foreach ($Streets as $row) {
                    $arrNeed['ShipToAddr' . $cnt] = $row;
                    $cnt++;
                }
                $arrNeed['ShipToCity'] = $ShippingAddress->getCity();
                $arrNeed['ShipToState'] = $ShippingAddress->getRegion();
                $arrNeed['ShipToZip'] = $ShippingAddress->getPostcode();
//              var_dump($ShippingAddress->getCountry_id());
                $country_name = Mage::app()->getLocale()->getCountryTranslation($ShippingAddress->getCountry_id());
                $arrNeed['ShipToCountry'] = $country_name;
//              var_dump($item->getShippingAddress()->getData());
//              var_dump($item->getBillingAddress()->getData());
                $props = $item->getAllItems();
                $arrNeed['OrderDetails'] = array();
                foreach ($props as $propId => $prop) {
//                    if($OrderId==73)
                    /*$getData = $prop->getData();
//                    if($item->getId()==47)
                    foreach($getData as $keyGetData=>$valueGetData){
                        if(strstr($keyGetData,'qty')){
                            echo '<br><br>$keyGetData='.$keyGetData;
                            echo '<br>$valueGetData='.$valueGetData;
                        }
                    }*/
                    $Qty_ordered = $prop->getQty_ordered();
                    $Qty_shipped = $prop->getQty_shipped();
                    $Qty_shipped_need = $Qty_ordered - $Qty_shipped;
                    $itemsPropId = $prop->getId();
                    $parent_item_id = $prop->getParent_item_id();
//                  var_dump($itemsPropId);
                    if (!empty($Qty_shipped_need) && $Qty_shipped_need > 0 && empty($parent_item_id)) {
                        $arrNeedOneProp = array();
                        $arrNeedOneProp['ItemID'] = $prop->getSku();
//                        $arrNeedOneProp['ItemID'] = '00714';
//                        $arrNeedOneProp['ItemID'] = $arrSku[$cntItem];
                        $arrNeedOneProp['itemsProp'] = $itemsPropId;
                        $arrNeedOneProp['Quantity'] = $Qty_shipped_need;
                        $arrNeedOneProp['SalePrice'] = $prop->getPrice();
                        $arrNeedOneProp['CustItemID'] = $prop->getProduct_id();
                        $arrNeedOneProp['Custom1'] = $prop->getName();
                        $arrNeed['OrderDetails'][] = $arrNeedOneProp;
                        /*if(count($arrSku)>=$cntItem) {
                            $cntItem++;
                        }else{
                            $cntItem = 0;
                        }*/
                    }
                }
//                if($item->getId()==47)var_dump($arrNeed);
                $orderData[$OrderId] = $arrNeed;
            }
            $cntSendOrdersReal ++;
        }
//        echo '<pre>'.print_r($orderData,true).'</pre>';
//        die();
        return $orderData;
    }

    public function saveOrderToDB($OrdersDataArray = array()){
        if(empty($OrdersDataArray)) return false;
        $OrdersIds = array_keys($OrdersDataArray);
        $OrdersIdsInsert = $OrdersIds;
//        var_dump($OrdersIds);
        $resource = Mage::getSingleton('core/resource');

        $tableName = $resource->getTableName('iwdintegracoreb/table_integracoreb');

        $read = $resource->getConnection('core_read');
        $select = $read->select()
            ->from($tableName)
            ->where('order_id in (?)', $OrdersIds);
        $result = $read->fetchAll($select);
//        var_dump($result);
//        var_dump($OrdersIds);
        if(!empty($result)){
            foreach($result as $rowData){
                if(array_search($rowData['order_id'],$OrdersIds)!==false){
                    $keysDelete = array_search($rowData['order_id'],$OrdersIds);
                    unset($OrdersIdsInsert[$keysDelete]);
                    if($rowData['accepted']){
                        unset($OrdersIds[$keysDelete]);
                    }
                }
            }
        }
//        var_dump($OrdersIds);
        $writeConnection = $resource->getConnection('core_write');
        foreach($OrdersIdsInsert as $order_id) {
            $OrderNumber = $OrdersDataArray[$order_id]['OrderNumber'];
            $query = "Insert into " . $tableName . "
            (`order_id`, `accepted`, `is_shipped`, `created`, `update`, `order_number`)
            VALUES  ('".$order_id."','0','0','".date("Y-m-d H:i:s")."','".date("Y-m-d H:i:s")."','".$OrderNumber."') ";
            $writeConnection->query($query);
        }
        return $OrdersIds;
    }

    public function convertOrderDataToXmlFormat($OrdersDataArray, $idOrdersPutToXml = array()){
        if (empty($idOrdersPutToXml)) {
            return false;
        }
        $xmlFieldPut = array();
        foreach($idOrdersPutToXml as $idOrder) {
            $xmlOneFieldPut = array();
            foreach ($this->xmlFields as $key => $defValue) {
                if(!is_array($defValue)) {
                    if (isset($OrdersDataArray[$idOrder][$key])) {
                        $xmlOneFieldPut[$key] = $OrdersDataArray[$idOrder][$key];
                    } else {
                        $xmlOneFieldPut[$key] = $defValue;
                    }
                }else{
                    $xmlOneFieldPropsPut = array();
                    if(isset($OrdersDataArray[$idOrder][$key]) && is_array($OrdersDataArray[$idOrder][$key])) {
                        foreach($OrdersDataArray[$idOrder][$key] as $cnt=>$orderDataOneProduct) {
                            $xmlOneFieldOnePropPut = array();
                            foreach ($defValue[0] as $OrderDetailsKey => $OrderDetailsDefValue) {
//                                echo '<br>$OrderDetailsKey='.$OrderDetailsKey;
                                if (isset($orderDataOneProduct[$OrderDetailsKey])) {
                                    $xmlOneFieldOnePropPut[$OrderDetailsKey] = $orderDataOneProduct[$OrderDetailsKey];
                                } else {
                                    $xmlOneFieldOnePropPut[$OrderDetailsKey] = $OrderDetailsDefValue;
                                }
                            }
                            $xmlOneFieldPropsPut[] = $xmlOneFieldOnePropPut;
                        }
                    }
                    if(!empty($xmlOneFieldPropsPut)) {
                        $xmlOneFieldPut[$key] = $xmlOneFieldPropsPut;
                    }
                }
            }
            $xmlFieldPut[] = $xmlOneFieldPut;
        }
        Mage::helper('iwdintegracoreb')->saveLogs(json_encode($xmlFieldPut),'data-put');
//        var_dump($xmlFieldPut);
        return $xmlFieldPut;
    }

    public function putOrdersToIncomingOrders($ordersDataXmlFormat,$OrdersDataArray){
        if(empty($ordersDataXmlFormat)) return false;
        $OrdersOrderIdOrderNumberKey = array();
        foreach($OrdersDataArray as $OrderId=>$rowData){
            $OrdersOrderIdOrderNumberKey[$rowData['OrderNumber']] = $OrderId;
        }
        $strOfOrderNumber = implode(',',array_keys($OrdersOrderIdOrderNumberKey));
        Mage::helper('iwdintegracoreb')->sendNotification($strOfOrderNumber, 'New order was submitted to API');
        $client = new SoapClient(   "http://www.integracoreb2b.com/IntCore/IncomingOrders.asmx?WSDL");
        $client = $this->addHeadersForClient($client);
        $response = $client->OrderImport(array('Orders'=>$ordersDataXmlFormat));
        Mage::helper('iwdintegracoreb')->saveLogs(json_encode($response),'after-put');
//        var_dump($response);
        if(!empty($response)){
//            var_dump($response->OrderImportResult->OrderMessage);
            $resource = Mage::getSingleton('core/resource');

            $tableName = $resource->getTableName('iwdintegracoreb/table_integracoreb');
            $writeConnection = $resource->getConnection('core_write');
            $read = $resource->getConnection('core_read');
//            echo '<pre>'.print_r($response->OrderImportResult->OrderMessage->OrderResult,true).'</pre>';
            $OrderResult = Mage::helper('iwdintegracoreb')->getArrayFromResponse($response->OrderImportResult->OrderMessage->OrderResult,'orderNumber');
            foreach($OrderResult as $OrderResultOneItem){
//            echo '<pre>'.print_r($OrderResultOneItem,true).'</pre>';
                if(isset($OrdersOrderIdOrderNumberKey[$OrderResultOneItem->orderNumber])) {
                    $OrderId = $OrdersOrderIdOrderNumberKey[$OrderResultOneItem->orderNumber];
                    $query = "Update " . $tableName . "
                    set  `update` = '" . date("Y-m-d H:i:s") . "'
                    where `order_id`= '" . $OrderId . "' ";
                    $writeConnection->query($query);
                    $select = $read->select()
                        ->from($tableName)
                        ->where(" `order_id`= '" . $OrderId . "' ");
                    $result = $read->fetchAll($select);
                    if(!empty($result)){
                        foreach($result as $rowData){
                            if($rowData['order_number']!=$OrderResultOneItem->orderNumber){
                                $query = "Update " . $tableName . "
                                set  `order_number` = '" . $OrderResultOneItem->orderNumber . "'
                                where `order_id`= '" . $OrderId . "' ";
                                $writeConnection->query($query);
                            }
                        }
                    }
                    $accepted = false;
                    if ($OrderResultOneItem->accepted) {
                        $accepted = true;
                    } else {
                        Mage::helper('iwdintegracoreb')->addError($OrderId,$OrderResultOneItem->error);
                        if('OrderNumber is already in use; OrderNumber is already submitted;'==$OrderResultOneItem->error) {
                            $accepted = Mage::helper('iwdintegracoreb')
                                ->CheckForCompliance($OrderId, $OrdersDataArray[$OrderId], $this->getShipConfirm(array($OrderResultOneItem->orderNumber => 1)));
                        }
                    }
                    if($accepted){
                        $query = "Update " . $tableName . "
                        set  `accepted` = '1'
                        where `order_id`= '" . $OrderId . "' ";
                        $writeConnection->query($query);
                    }
                }
            }
        }
    }

    public function addHeadersForClient($client){
        $ns = 'http://www.integracoreb2b.com/'; //Namespace of the WS.

        //Body of the Soap Header.
        $headerbody = array('Username' => $this->username,
            'Password' => $this->password,
            'Test'=>$this->test);
        //Create Soap Header.
        $header = new SOAPHeader($ns, 'AuthHeader', $headerbody);

        //set the Headers of Soap Client.
        $client->__setSoapHeaders($header);
        return $client;
    }

    public function getAcceptedOrders(){
        $resource = Mage::getSingleton('core/resource');
        $tableName = $resource->getTableName('iwdintegracoreb/table_integracoreb');
        $read = $resource->getConnection('core_read');
        $select = $read->select()
            ->from($tableName)
            ->where('`accepted` = "1" and `is_shipped` = "0" ');
        $result = $read->fetchAll($select);
        $OrdersData = array();
        if(!empty($result)){
            foreach($result as $rowData){
                $OrdersData[$rowData['order_number']] = $rowData;
            }
        }
        return $OrdersData;
    }

    public function getShipConfirm($arrAcceptedOrders){
//        var_dump($arrAcceptedOrders);die();
        $arrShipConfirmTrue = array();
        $client = new SoapClient(   "http://www.integracoreb2b.com/API/ShipConfirm.asmx?WSDL");
//        var_dump($client->__getFunctions());
        $client = $this->addHeadersForClientShipConfirm($client);
//        var_dump(array_keys($arrAcceptedOrders));die();
        $client->getShipConfirms();
        $response = $client->getMultipleOrderStatus (array('OrderNumbers'=>array_keys($arrAcceptedOrders)));
//        echo '<pre>'.print_r($arrAcceptedOrders,true).'</pre>';
//        echo '<pre>'.print_r($response,true).'</pre>';
//        die();
        if(!empty($response->getMultipleOrderStatusResult)){
//            echo '<pre>'.print_r($response->getMultipleOrderStatusResult,true).'</pre>';
            $getMultipleOrderStatusResult = Mage::helper('iwdintegracoreb')->getArrayFromResponse($response->getMultipleOrderStatusResult->Order,'OrderNumber');
//            echo '<pre>'.print_r($getMultipleOrderStatusResult,true).'</pre>';
//            die('app/code/community/IWD/Integracoreb/Model/Observer.php:346');
            foreach($getMultipleOrderStatusResult as $cnt=>$Order){
//                echo '<pre>'.print_r($Order,true).'</pre>';
                if(isset($Order->OrderNumber)) {
                    $orderNumber = $Order->OrderNumber;
//                    echo '$orderNumber=';
//                    var_dump($orderNumber);
                    if(isset($arrAcceptedOrders[$orderNumber])) {
                        $order_id = $arrAcceptedOrders[$orderNumber]['order_id'];
                        $arrShipConfirmTrue[$order_id] = $Order;
                    }
                }
            }
        }
//        echo '<pre>'.print_r($arrShipConfirmTrue,true).'</pre>';
//        die();
        return $arrShipConfirmTrue;
    }

    public function addHeadersForClientShipConfirm($client){
        $ns = 'http://www.integracoreb2b.com/API/'; //Namespace of the WS.

        //Body of the Soap Header.
        $headerbody = array('UserName' => $this->username,
            'Password' => $this->password,
            'Test'=>$this->test);

        //Create Soap Header.
        $header = new SOAPHeader($ns, 'Authenticate', $headerbody);
        //var_dump($header);
        //set the Headers of Soap Client.
        $client->__setSoapHeaders($header);
        return $client;
    }

    public function saveShipment($Order,$orderRealData = array()){
//        echo '<pre>'.print_r($Order,true).'</pre>';
//        echo '<pre>'.print_r($orderRealData,true).'</pre>';
//        die();
        $data = array();
        $data['comment_text'] = 'StatusDescription: '.$Order->StatusDescription.'
        ShipDate:'.$Order->ShipDate.'
        ShipCost:'.$Order->ShipCost;
        $data['send_email'] = 1;
        $orderId = $orderRealData['OrderId'];
//        var_dump($orderId);
        if(empty($orderId)) return false;
        $this->tmpOrderId = $orderId;
        try {
            $shipment = $this->_initShipment($Order,$orderRealData);
            if (!$shipment) {
                Mage::helper('iwdintegracoreb')->addError($orderId,Mage::helper('iwdintegracoreb')->__('noRoute'));
                return false;
            }
            $shipment->register();
            $comment = '';
            if (!empty($data['comment_text'])) {
                $shipment->addComment(
                    $data['comment_text'],
                    isset($data['comment_customer_notify']),
                    isset($data['is_visible_on_front'])
                );
                if (isset($data['comment_customer_notify'])) {
                    $comment = $data['comment_text'];
                }
            }

            if (!empty($data['send_email'])) {
                $shipment->setEmailSent(true);
            }

            $shipment->getOrder()->setCustomerNoteNotify(!empty($data['send_email']));

            $this->_saveShipment($shipment);

            $shipment->sendEmail(!empty($data['send_email']), $comment);

//            $order = $shipment->getOrder();
//            $order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
            $order = Mage::getModel('sales/order')->load($orderId);
//            echo '<br>canShip=<pre>'.print_r(($order->canShip()),true).'</pre>';
//            echo '<br>getForcedDoShipmentWithInvoice=<pre>'.print_r((!$order->getForcedDoShipmentWithInvoice()),true).'</pre>';
//            die();

            if(!$order->canShip() || $order->getForcedDoShipmentWithInvoice()) {
                $resource = Mage::getSingleton('core/resource');
                $tableName = $resource->getTableName('iwdintegracoreb/table_integracoreb');
                $writeConnection = $resource->getConnection('core_write');
                $query = "Update " . $tableName . "
                        set  `update` = '" . date("Y-m-d H:i:s") . "',
                        `is_shipped` = '1'
                        where `order_id`= '" . $orderId . "' ";
                $writeConnection->query($query);
            }

        } catch (Exception $e) {
            Mage::helper('iwdintegracoreb')->addError($orderId,$e->getMessage());
            Mage::helper('iwdintegracoreb')->addError($orderId,Mage::helper('iwdintegracoreb')->__('Cannot save shipment.'));
        }
        return true;
    }

    protected function _initShipment($Order,$orderRealData = array())
    {
//        echo '<pre>'.print_r($Order,true).'</pre>';
//        echo '<pre>'.print_r($orderRealData,true).'</pre>';
        $orderRealDataKeyIsSku = array();
        foreach($orderRealData['OrderDetails'] as $idProd=>$rowTxt){
            $orderRealDataKeyIsSku[$rowTxt['ItemID']] = $rowTxt;
        }
//        die();
        $shipment = false;
        $orderId = $orderRealData['OrderId'];
//        echo '$orderId='.$orderId;
//        die();
        if ($orderId) {
            $order      = Mage::getModel('sales/order')->load($orderId);

            /**
             * Check order existing
             */
            if (!$order->getId()) {
                Mage::helper('iwdintegracoreb')->addError($orderId,Mage::helper('iwdintegracoreb')->__('The order no longer exists.'));
                return false;
            }
            /**
             * Check shipment is available to create separate from invoice
             */
            if ($order->getForcedDoShipmentWithInvoice()) {
                Mage::helper('iwdintegracoreb')->addError($orderId,Mage::helper('iwdintegracoreb')->__('Cannot do shipment for the order separately from invoice.'));
                return false;
            }
            /**
             * Check shipment create availability
             */
            if (!$order->canShip()) {
                Mage::helper('iwdintegracoreb')->addError($orderId,Mage::helper('iwdintegracoreb')->__('Cannot do shipment for the order.'));
                return false;
            }
            $savedQtys = array();
            if(isset($orderRealData['OrderDetails']) && isset($Order->Items->Item)){
//                var_dump($orderRealData['OrderDetails']);
//                var_dump($Order->Items->Item);
                $convertItemRealData = array();
//                echo '<pre>'.print_r($Order->Items->Item->ItemID,true).'</pre>';
                $OrderItem = Mage::helper('iwdintegracoreb')->getArrayFromResponse($Order->Items->Item,'ItemID');
//                echo '<pre>'.print_r($OrderItem,true).'</pre>';
//                die('app/code/community/IWD/Integracoreb/Model/Observer.php:496');
                foreach ($OrderItem as $rowData) {
//                        var_dump($rowData);
                    $convertItemRealData[$rowData->ItemID] = $rowData;
                }
//                echo '<pre>'.print_r($orderRealData,true).'</pre>';
//                die('app/code/community/IWD/Integracoreb/Model/Observer.php:500');
                foreach($orderRealData['OrderDetails'] as $OrderDetailsOneItem){
//                    echo '<pre>'.print_r($convertItemRealData,true).'</pre>';
                    $needQuantity = $OrderDetailsOneItem['Quantity'];
                    if(isset($convertItemRealData[$OrderDetailsOneItem['ItemID']])) {
                        $realQuantity = $convertItemRealData[$OrderDetailsOneItem['ItemID']]->Shipped;
                    }else{
                        $realQuantity = 0;
                    }
//                    echo '$realQuantity='.$realQuantity;
                    if($realQuantity>$needQuantity){
                        $realQuantity = $needQuantity;
                    }
                    $itemsProp = $OrderDetailsOneItem['itemsProp'];
                    $savedQtys[$itemsProp] = $realQuantity;
                }
            }
//            echo '<pre>'.print_r($savedQtys,true).'</pre>';
//            die();
            $emptyShipment = true;
            foreach($savedQtys as $qtyOneItem){
                if(!empty($qtyOneItem)){
                    $emptyShipment = false;
                }
            }
            if($emptyShipment){
                Mage::helper('iwdintegracoreb')->addError($orderId,Mage::helper('iwdintegracoreb')->__('No consignment.'));
                return false;
            }
            $shipment = Mage::getModel('sales/service_order', $order)->prepareShipment($savedQtys);
            $tracks = array();
            $Shipper = $Order->TrackingInformation->Tracking->Shipper;
            $tracks['carrier_code'] = substr(strtolower($Shipper),0,strpos($Shipper,' '));
            $tracks['title'] = 'United Parcel Service';
            $tracks['number'] = '';
            foreach($Order->TrackingNumbers as $rowTxt) {
                if(!empty($tracks['number'])) $tracks['number'] .= ' ';
                $tracks['number'] .= $rowTxt;
            }
            $tracks = array($tracks);
//            var_dump($tracks);
//            die('tracks');
            if ($tracks) {
                foreach ($tracks as $data) {
                    if (empty($data['number'])) {
                        Mage::helper('iwdintegracoreb')->addError($orderId,Mage::helper('iwdintegracoreb')->__('Tracking number cannot be empty.'));
                    }
                    $track = Mage::getModel('sales/order_shipment_track')
                        ->addData($data);
                    $shipment->addTrack($track);
                }
            }
        }
        return $shipment;
    }

    protected function _saveShipment($shipment)
    {
        try{
            $shipment->getOrder()->setIsInProcess(true);
            Mage::getModel('core/resource_transaction')
                ->addObject($shipment)
                ->addObject($shipment->getOrder())
                ->save();
        } catch (Exception $e) {
            Mage::helper('iwdintegracoreb')->addError($this->tmpOrderId,$e->getMessage());
        }

        return $this;
    }

    public function showOrdersInXml($ordersDataXmlFormat,$OrdersDataArray){
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?>'
            .'<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">'
            .'</soap:Envelope>');
        $header = $xml->addChild('soap:Header');
        $AuthHeader = $header->addChild('AuthHeader',null,'http://www.integracoreb2b.com/');
        $AuthHeader->addChild('Username',$this->username);
        $AuthHeader->addChild('Password',$this->password);
        $AuthHeader->addChild('Test',$this->test);
        $Body = $xml->addChild('soap:Body');
        $OrderImport = $Body->addChild('OrderImport',null,'http://www.integracoreb2b.com/');
        $OrdersImport = $OrderImport->addChild('Orders');
        $arrFieldsInside = array(
            'SiteID',
            'CreateDate',
            'RequiredShipDate',
            'OrderNumber',
            'ShipToCustID',
            'BillToCustID',
        );
//        var_dump($ordersDataXmlFormat);die();
        foreach($ordersDataXmlFormat as $oneItem) {
            $OrderImport = $OrdersImport->addChild('Order');
            foreach ($this->xmlFields as $name => $row) {
                if (!is_array($row)) {
                    if (!isset($oneItem[$name])) {
                        $value = $row;
                    } else {
                        $value = $oneItem[$name];
                    }
                    $track = $OrderImport->addChild($name, $value);
                } else {
                    $trackItems = $OrderImport->addChild($name);
                    foreach ($row as $rowRow) {
//                    var_dump($oneItem[$name]);die();
                        foreach ($oneItem[$name] as $oneItemProp) {
                            $OrderDetail = $trackItems->addChild('OrderDetail');
                            foreach ($rowRow as $nameRow => $valueRow) {
                                if (!isset($oneItemProp[$nameRow])) {
                                    $value = $valueRow;
                                } else {
                                    $value = $oneItemProp[$nameRow];
                                }
                                $OrderDetail->addChild($nameRow, $value);
                            }
                        }
                    }
                }
            }
        }

        Header('Content-type: text/xml');
        print($xml->asXML());
    }

    public function showOrdersInXmlShipConfirm($arrAcceptedOrders){
//        var_dump($arrAcceptedOrders);
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?>'
            .'<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">'
            .'</soap:Envelope>');
        $header = $xml->addChild('soap:Header');
        $AuthHeader = $header->addChild('Authenticate',null,'http://www.integracoreb2b.com/API/');
        $AuthHeader->addChild('UserName',$this->username);
        $AuthHeader->addChild('Password',$this->password);
        $AuthHeader->addChild('Test',$this->test);
        $Body = $xml->addChild('soap:Body');
        $OrderImport = $Body->addChild('getMultipleOrderStatus',null,'http://www.integracoreb2b.com/API/');
        $OrderNumber = $OrderImport->addChild('OrderNumber');
        foreach($arrAcceptedOrders as $OrderNumberId=>$rowData)
        $OrderNumber->addChild('string',$OrderNumberId);


        Header('Content-type: text/xml');
        print($xml->asXML());
    }

    public function runShipping(){
        $this->initSetting();
        if($this->statusModule) {
            $OrdersDataArray = $this->getOrdersData();
//            echo '<pre>'.print_r($OrdersDataArray,true).'</pre>';
//            die();
            if (!empty($OrdersDataArray)) {
                $idOrdersPutToXml = $this->saveOrderToDB($OrdersDataArray);
                $ordersDataXmlFormat = $this->convertOrderDataToXmlFormat($OrdersDataArray, $idOrdersPutToXml);
//            $this->showOrdersInXml($ordersDataXmlFormat,$OrdersDataArray);
//            die();
                $this->putOrdersToIncomingOrders($ordersDataXmlFormat, $OrdersDataArray);
            }
            $arrAcceptedOrders = $this->getAcceptedOrders($OrdersDataArray);
//            echo '<pre>'.print_r($arrAcceptedOrders,true).'</pre>';
//        die();
            if(!empty($arrAcceptedOrders)) {
//        $this->showOrdersInXmlShipConfirm($arrAcceptedOrders);
//            die();
                Mage::helper('iwdintegracoreb')->saveLogs(json_encode($arrAcceptedOrders), 'data-get');
                $arrShipConfirm = $this->getShipConfirm($arrAcceptedOrders);
                Mage::helper('iwdintegracoreb')->saveLogs(json_encode($arrShipConfirm));
//            echo '<pre>'.print_r($arrShipConfirm,true).'</pre>';
//        die();
                if (!empty($arrShipConfirm)) {
                    $strOfOrderNumber = '';
                    foreach ($arrShipConfirm as $key => $Order) {
//                    echo '<pre>'.print_r($Order,true).'</pre>';
                        if (isset($Order->Results->Result->Success) && !$Order->Results->Result->Success) {
                            Mage::helper('iwdintegracoreb')->addError($key, $Order->Results->Result->Message);
                        } else {
                            $Status = $Order->Status;
                            Mage::helper('iwdintegracoreb')->addError($key, $Order->StatusDescription);
                            if ($Status == 'C') {
                                $this->saveShipment($Order, $OrdersDataArray[$key]);
                                if(!empty($strOfOrderNumber))$strOfOrderNumber .= ',';
                                $strOfOrderNumber .= $Order->OrderNumber;
                            }
                        }
                    }
                    if(!empty($strOfOrderNumber)) {
                        Mage::helper('iwdintegracoreb')->sendNotification($strOfOrderNumber, 'New order was submitted to API');
                    }
                }
            }
        }
        return true;
    }
}