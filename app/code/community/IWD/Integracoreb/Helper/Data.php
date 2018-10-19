<?php
class IWD_Integracoreb_Helper_Data extends Mage_Core_Helper_Abstract {
    public $saveLogs = true;
    public function saveLogs($text,$type = 'after-get'){
        $resource = Mage::getSingleton('core/resource');
        $tableLogName = $resource->getTableName('iwdintegracoreb/table_integracoreb_api_log');
        $writeConnection = $resource->getConnection('core_write');

        $text = addslashes($text);

        $query = "Insert into " . $tableLogName . " ( text, type, created)
                    VALUES  ('" . $text . "', '" . $type . "', '" . date("Y-m-d H:i:s") . "') ";
        if($this->saveLogs) {
            $writeConnection->query($query);
        }
    }
    public function CheckForCompliance($OrderId,$OrdersDataArray,$getShipConfirm){
//        echo '$OrderId<pre>'.print_r($OrderId,true).'</pre>';
//        echo '$OrdersDataArray<pre>'.print_r($OrdersDataArray,true).'</pre>';
//        echo '$getShipConfirm<pre>'.print_r($getShipConfirm,true).'</pre>';
        $accepted = false;
        foreach($getShipConfirm as $val){
            $getShipConfirm = $val;
        }
        if($OrdersDataArray['OrderNumber']==$getShipConfirm->OrderNumber){
            $OrderDetails = $OrdersDataArray['OrderDetails'];
            $OrderDetailsWithKeyItemId = array();
            foreach($OrderDetails as $row){
                $OrderDetailsWithKeyItemId[$row['ItemID']] = $row;
            }
            $Items = $this->getArrayFromResponse($getShipConfirm->OrderNumber->Items->Item,'ItemID');
            $cnt = 0;
            foreach($Items as $oneItem){
                if(isset($OrderDetailsWithKeyItemId[$oneItem->ItemID])
                    && $OrderDetailsWithKeyItemId[$oneItem->ItemID]['Quantity']==$oneItem->Shipped){
                    $cnt++;
                }
            }
            if($cnt==count($Items)){
                $accepted = true;
                $this->sendNotification($OrdersDataArray['OrderNumber'],'Notice of confirmation of successful Compliance.');
            }
        }
        if(!$accepted){
            $this->sendNotification($OrdersDataArray['OrderNumber'],'Notice of confirmation of unsuccessful Compliance.');
        }
        return $accepted;
    }

    public function getHeadersForNotification(){
        $headers = "From: support@zeitbike.com"."\r\n";
        $headers  .= 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        return $headers;
    }

    public function sendNotification($OrderId,$subject=''){
        $headers = $this->getHeadersForNotification();
        //mail('bogdan.iglinsky89@gmail.com',$subject,'$OrderId is '.$OrderId,$headers);
    }

    public function addError($OrderId,$error){
        if(empty($OrderId)) return false;
        $resource = Mage::getSingleton('core/resource');
        $tableLogName = $resource->getTableName('iwdintegracoreb/table_integracoreb_log');
        $writeConnection = $resource->getConnection('core_write');

        $error = addslashes($error);

        $query = "Insert into " . $tableLogName . " (order_id, created, error)
                    VALUES  ('" . $OrderId . "', '" . date("Y-m-d H:i:s") . "', '" . $error . "') ";
        if($this->saveLogs) {
            $writeConnection->query($query);
        }
        echo '<br>order_id='.$OrderId;
        echo ' $error='.$error;
    }

    public function getArrayFromResponse($Obj,$needCheckFirstKey = ''){
//        echo '$needCheckFirstKey='.$needCheckFirstKey;
        $keys = array();
        foreach($Obj as $key=>$value){
            $keys[] = $key;
        }
//        echo '<pre>'.print_r($keys,true).'</pre>';
//        echo '<pre>'.print_r(($keys[0]==$needCheckFirstKey),true).'</pre>';
        if($keys[0]===$needCheckFirstKey){
            return  array($Obj);
        }else{
            return $Obj;
        }
    }
}
