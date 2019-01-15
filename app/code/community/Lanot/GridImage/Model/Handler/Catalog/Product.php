<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category    Lanot
 * @package     Lanot_GridImage
 * @copyright   Copyright (c) 2012 Lanot
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Lanot_GridImage_Model_Handler_Catalog_Product
    extends Lanot_GridImage_Model_Handler_Abstract
{
    protected $_configNode = 'catalog_product';

    /**
     * @param Mage_Adminhtml_Block_Catalog_Product_Grid $block
     * @param array $extraColumnsData
     * @return bool
     */
    public function prepare($block, $extraColumnsData = array())
    {
        //fix for export - do not export images
        if (!$block->getCollection()->isLoaded())  {
            return false;
        }

        if ($result = parent::prepare($block, $extraColumnsData)) {
            $block->sortColumnsByOrder();
        }
        return $result;
    }

    /**
     * @param Mage_Adminhtml_Block_Catalog_Product_Grid $grid
     * @return Lanot_GridImage_Model_Observer
     */
    protected function _addImageColumn($grid, $enabledData)
    {
        $size = Mage::helper('lanot_gridimage')->getImageSize($this->_configNode);
        $width = !empty($size[0]) ? ((int) $size[0] + 10) . 'px' : null;

        $grid->addColumn('image', array(
            'header'    => Mage::helper('lanot_gridimage')->__('Image'),
            'width'     => $width,
            'index'     => 'image',
            'sortable'  => false,
            'filter'    => false,
            'renderer' => 'lanot_gridimage/adminhtml_catalog_product_grid_renderer_image'
        ));

        if (isset($enabledData['after'])) {
            $grid->addColumnsOrder('image', $enabledData['after']);
        }

        return $this;
    }

    /**
     * @param Mage_Catalog_Model_Resource_Product_Collection $collection
     * @return bool
     */
    protected function _addImagesToCollection($collection)
    {
        return $this->_addImagesToProducts($collection, $collection->getColumnValues('entity_id'));
    }
}
