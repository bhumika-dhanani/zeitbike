<?php
class Rishabhsoft_AdvanceImport_Model_Convert_Adapter_Configurableimport
    extends Rishabhsoft_AdvanceImport_Model_Convert_Adapter_Abstract
	{
		public function import($importData,$product)
		{
			if ($importData['type'] == 'configurable') 
			{
				$product->setCanSaveConfigurableAttributes(true);
				if(isset($importData['config_attributes']) && !empty($importData['config_attributes']))
				{
				$configAttributeCodes = $this->userCSVDataAsArray($importData['config_attributes']);
				$usingAttributeIds = array();

				//Check the product's super attributes (see catalog_product_super_attribute table), and make a determination that way.
				
				$cspa  = $product->getTypeInstance()->getConfigurableAttributesAsArray($product);
				$attr_codes = array();
				if(isset($cspa) && !empty($cspa)){ //found attributes
					foreach($cspa as $cs_attr){
					//$attr_codes[$cs_attr['attribute_id']] = $cs_attr['attribute_code'];
						$attr_codes[] = $cs_attr['attribute_id'];
					}
				}
				foreach($configAttributeCodes as $attributeCode) 
				{
					$attribute = $product->getResource()->getAttribute($attributeCode);
					if ($product->getTypeInstance()->canUseAttribute($attribute)) {
						if (!in_array($attributeCode,$attr_codes)) { // fix for duplicating attributes error
							$usingAttributeIds[] = $attribute->getAttributeId();
						}
					}
				} 
				
				if (!empty($usingAttributeIds)) 
				{
					$product->getTypeInstance()->setUsedProductAttributeIds($usingAttributeIds);
					//$product->setConfigurableAttributesData($product->getTypeInstance()->getConfigurableAttributesAsArray());
					
					//The label MUST be set or we'll get a MySQL error
					$configurableAttributesArray = $product->getTypeInstance()->getConfigurableAttributesAsArray();
					
					$read = Mage::getSingleton('core/resource')->getConnection('core_read'); 
					
					
					foreach($configurableAttributesArray as &$configurableAttributeArray)
					{
						//Set a number of default values
						$configurableAttributeArray['use_default']     = 1;
						$configurableAttributeArray['position']        = 0;
						
						/// code for value array
						$attr_code = $configurableAttributeArray['attribute_code'];	
						
						$value_index = $this->userCSVDataAsArray($importData['config_code-is_per-'.$attr_code]);
						$j = 0;
						for($i=0;$i<count($value_index);$i++)
						{					
							$mix = explode(':',$value_index[$i]);
							if(isset($mix) && !empty($mix))
							{	
								$select = $read->select()
									->distinct()
									->from('eav_attribute_option_value',array('option_id','value'))
									->where('value =?',$mix[0]);
								$optionArr = $read->fetchRow($select);
								if(isset($optionArr) && !empty($optionArr))
								{
									//$data['values'][$i]['label'] =  ;
									$data[$i]['attribute_id'] =  (int)$configurableAttributeArray["attribute_id"];
									$data[$i]['value_index'] =  $optionArr['option_id'];
									$data[$i]['is_percent'] = $mix[1];
									$data[$i]['pricing_value'] = $mix[2];							
								}
							}
						}
						$dataArr[$j] = $data;
						$configurableAttributeArray['values'] = $dataArr[$j];	
						$j++;					
						// End code for values Array Creation
						
						//Use the frontend_label as label, if available        
						if(isset($configurableAttributeArray['frontend_label'])){
							$configurableAttributeArray['label'] = $configurableAttributeArray['frontend_label'];
							continue;
						}
						//Use the attribute_code as a label
						$configurableAttributeArray['label'] = $configurableAttributeArray['attribute_code'];					
					}
					
					$product->setConfigurableAttributesData($configurableAttributesArray);
					$product->setCanSaveConfigurableAttributes(true);
					$product->setCanSaveCustomOptions(true);
				}
				if(isset($importData['associated'])){
					$product->setConfigurableProductsData($this->skusToIds($importData['associated'], $product));
				}
				}
			}
		}
	}
?>