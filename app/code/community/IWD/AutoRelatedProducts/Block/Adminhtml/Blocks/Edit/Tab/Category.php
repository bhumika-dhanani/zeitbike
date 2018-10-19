<?php
class IWD_AutoRelatedProducts_Block_Adminhtml_Blocks_Edit_Tab_Category extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('c_rule_');

        $fieldset = $form->addFieldset('categories_fieldset', array(
            'legend' => Mage::helper('iwd_autorelatedproducts')->__('Select categories'))
        );

        $fieldset->addField('category_id', 'multiselect', array(
            'label'     => 'Categories',
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'category_id',
            'values' => $this->getCategories(),
            'disabled' => false,
            'readonly' => false,
            'tabindex' => 1
        ));

        //$form->setUseContainer(true);

        try {
            if (Mage::registry('iwd_related_products_data')) {
                $data = Mage::registry('iwd_related_products_data')->getData();

                $data['category_id'] = unserialize($data['category_id_serialized']);

                $form->setValues($data);
            }
        } catch (Exception $e) {
            Mage::log(__CLASS__ . ": " . $e->getMessage(), null, 'iwd_related_products.log');
        }

        $this->setForm($form);

        return parent::_prepareForm();
    }


    protected function getCategories(){

        $category = Mage::getModel('catalog/category');
        $tree = $category->getTreeModel();
        $tree->load();
        $ids = $tree->getCollection()->getAllIds();
        $arr = array();
        if ($ids){
            foreach ($ids as $id){
                $cat = Mage::getModel('catalog/category');
                $cat->load($id);
                $arr[] = array(
                    'label' => $cat->getName(),
                    'value' => $id,
                );
            }
        }

        return $arr;

    }

    public function toOptionArray()
    {
        $collection = Mage::getResourceModel('catalog/category_collection');

        $collection->addAttributeToSelect('name')
            ->addFieldToFilter('path', array('neq' => '1'))
            ->load();

        $options = array();

        foreach ($collection as $category) {
            echo $category->getPath().'<br>';

            $depth = count(explode('/', $category->getPath())) - 2;
            $indent = str_repeat('-', max($depth * 2, 0));
            $options[] = array(
                'label' => $indent . $category->getName(),
                'value' => $category->getId()
            );
        }

        return $options;
    }
}
