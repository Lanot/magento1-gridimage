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

class Lanot_GridImage_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * @var bool
     */
    protected $_canClick = false;
    /**
     * @var bool
     */
    protected $_canShowLightbox = false;
    /**
     * @var string
     */
    protected $_imageClass = '';
    /**
     * @var null
     */
    protected $_imageSizeWidth = null;
    /**
     * @var null
     */
    protected $_imageSizeHeight = null;
    /**
     * @var null
     */
    protected $_imageSizeBigWidth = null;
    /**
     * @var null
     */
    protected $_imageSizeBigHeight = null;

    protected $_configNode = 'catalog_product';

    /**
     *
     */
    public function _construct()
    {
        parent::_construct();

        /** @var $helper Lanot_GridImage_Helper_Data */
        $helper = Mage::helper('lanot_gridimage');

        //define configuration
        $this->_canClick = $helper->canClick($this->_configNode);
        $this->_canShowLightbox = $helper->canShowLightbox($this->_configNode);
        $this->_imageClass = $helper->getImageClass($this->_configNode);
        $size = $helper->getImageSize($this->_configNode);
        $sizeBig = $helper->getImageBigSize($this->_configNode);

        //thumbnail size in grid
        if (isset($size[0]) && !empty($size[0])) {
            $imageSizeWidth = $size[0];
        }
        if (isset($size[1]) && !empty($size[1])) {
            $imageSizeHeight = $size[1];
        }

        //size by image click
        if (isset($sizeBig[0]) && !empty($sizeBig[0])) {
            $this->_imageSizeBigWidth = $sizeBig[0];
        }
        if (isset($sizeBig[1]) && !empty($sizeBig[1])) {
            $this->_imageSizeBigHeight = $sizeBig[1];
        }
    }

    /**
     * @param $node
     * @return bool
     */
    public function canClick($node)
    {
        return (bool) Mage::getStoreConfig(sprintf('lanot_gridimage/%s/image_click', $node), 0);
    }

    /**
     * @param $node
     * @return bool
     */
    public function getImageClass($node)
    {
        return (string) Mage::getStoreConfig(sprintf('lanot_gridimage/%s/image_class', $node), '');
    }

    /**
     * @param $node
     * @return array
     */
    public function getImageSize($node)
    {
        $size = Mage::getStoreConfig(sprintf('lanot_gridimage/%s/image_size', $node), 0);
        return @explode('x', strtolower($size));
    }

    /**
     * @param $node
     * @return array
     */
    public function getImageBigSize($node)
    {
        $size = Mage::getStoreConfig(sprintf('lanot_gridimage/%s/image_big_size', $node), 0);
        return @explode('x', strtolower($size));
    }

    /**
     * @param $node
     * @return bool
     */
    public function canShowLightbox($node)
    {
        if (!$this->canClick($node)) {
            return false;
        }
        return (bool) Mage::getStoreConfig(sprintf('lanot_gridimage/%s/lightbox_enabled', $node), 0);
    }

    public function prepareOrderItemImage($item)
    {
        if (!$value = $item->getImage()) {
            return '';
        }

        $imageClass = $this->getImageClass('sales_order_items');
        $size = $this->getImageSize('sales_order_items');

        $imageSizeWidth = !empty($size[0]) ? $size[0] : null;
        $imageSizeHeight = !empty($size[1]) ? $size[1] : null;

        //stub model
        $product = Mage::getModel('catalog/product')->setData('lanot_gridimage', $value);

        /** @var $imageHelper Mage_Catalog_Helper_Image */
        $imageHelper = Mage::helper('catalog/image')->init($product, 'lanot_gridimage', $value);

        //prepare width and height
        $widthAndHeightTags = '';
        if($imageSizeWidth && $imageSizeHeight) {
            $imageHelper->resize($imageSizeWidth, $imageSizeHeight);
            $widthAndHeightTags .= ' width="' . $imageSizeWidth . '"';
            $widthAndHeightTags .= ' heigth="' . $imageSizeHeight . '"';
        } else if ($imageSizeWidth) {
            $imageHelper->resize($imageSizeWidth);
            $widthAndHeightTags .= ' width="' . $imageSizeWidth . '"';
        } elseif ($imageSizeHeight) {
            $imageHelper->resize(null, $imageSizeHeight);
            $widthAndHeightTags .= ' heigth="' . $imageSizeHeight . '"';
        }

        return sprintf('<img src="%s" class="%s" border="0" alt="" %s/>',
            $imageHelper,
            $imageClass,
            $widthAndHeightTags
        );
    }
}
