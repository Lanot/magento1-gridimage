<?php

class Lanot_GridImage_Model_Handler_Sales_Order_Items
    extends Lanot_GridImage_Model_Handler_Abstract
{
    protected $_configNode = 'sales_order_items';
    protected $_collectionGetter = 'getItemsCollection';
    /**
     * @param Mage_Adminhtml_Block_Sales_Order_View_Items_Renderer_Default $grid
     * @return Lanot_GridImage_Model_Observer
     */
    protected function _addImageColumn($grid, $enabledData)
    {
        return $this;
    }
    /**
     * @param Mage_Sales_Model_Resource_Order_Item_Collection $collection
     * @return bool
     */
    protected function _addImagesToCollection($collection)
    {
        return $this->_addImagesToProducts($collection, $collection->getColumnValues('product_id'), 'product_id');
    }
}
