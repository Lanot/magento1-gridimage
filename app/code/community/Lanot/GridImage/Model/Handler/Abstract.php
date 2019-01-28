<?php
/**
 * Private Entrepreneur Anatolii Lehkyi (aka Lanot)
 *
 * @category    Lanot
 * @package     Lanot_GridImage
 * @copyright   Copyright (c) 2010 Anatolii Lehkyi
 * @license     http://opensource.org/licenses/osl-3.0.php
 * @link        http://www.lanot.biz/
 */

abstract class Lanot_GridImage_Model_Handler_Abstract
{
    const ATTRIBUTE_CODE = 'image';
    protected $_configNode = null;
    protected $_collectionGetter = 'getCollection';
    protected $_imageRenderer = null;

    /**
     * @param Mage_Adminhtml_Block_Template $block
     * @param array $extraColumnsData
     * @return bool
     */
    public function prepare($block, $extraColumnsData = array())
    {
        if ($this->_isViewEnabled()) {
            $this->_addImagesToCollection($block->{$this->_collectionGetter}());
            $this->_addImageColumn($block, $extraColumnsData);
            return true;
        }
        return false;
    }

    /**
     * @param Mage_Catalog_Model_Resource_Product_Collection $collection
     * @return bool
     */
    protected function _addImagesToProducts($collection, $productIds = array(), $field = 'entity_id')
    {
        if (empty($productIds)) {
            return false;
        }

        /** $var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
        $attributeId = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product', self::ATTRIBUTE_CODE);
        $attribute = Mage::getModel('catalog/resource_eav_attribute')->load($attributeId);
        if (!$attribute) {
            return false;
        }

        /** @var $resourceModel Lanot_GridImage_Model_Mysql4_Attribute */
        $resourceModel = Mage::getModel('lanot_gridimage/mysql4_attribute');
        $productsImages = $resourceModel->getAttributeEntityValues($attribute, $productIds, $this->_getStoreId());
        foreach ($collection as $product) {
            if (isset($productsImages[$product->getData($field)])) {
                $product->setData(self::ATTRIBUTE_CODE, $productsImages[$product->getData($field)]);
            }
        }

        return true;
    }

    /**
     * @return bool
     */
    protected function _isViewEnabled()
    {
        return (bool) Mage::getStoreConfig(sprintf('lanot_gridimage/%s/enabled', $this->_configNode), 0);
    }

    /**
     * @return mixed
     */
    protected function _getStoreId()
    {
        return Mage::app()->getRequest()->getParam('store', 0);
    }

    /**
     * @param Mage_Adminhtml_Block_Template $grid
     * @return Lanot_GridImage_Model_Observer
     */
    abstract protected function _addImageColumn($grid, $enabledData);

    /**
     * @param Varien_Data_Collection_Db $collection
     * @return bool
     */
    abstract protected function _addImagesToCollection($collection);
}
