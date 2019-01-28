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

class Lanot_GridImage_Block_Adminhtml_Catalog_Product_Grid_Renderer_Image
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
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
            $this->_imageSizeWidth = $size[0];
        }
        if (isset($size[1]) && !empty($size[1])) {
            $this->_imageSizeHeight = $size[1];
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
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $value =  $row->getData($this->getColumn()->getIndex());
        if (empty($value)) {
            return '';
        }

        /** @var $imageHelper Mage_Catalog_Helper_Image */
        $imageHelper = Mage::helper('catalog/image')->init($row, 'lanot_gridimage', $value);

        //prepare width and height
        $widthAndHeightTags = '';
        if($this->_imageSizeWidth && $this->_imageSizeHeight) {
            $imageHelper->resize($this->_imageSizeWidth, $this->_imageSizeHeight);
            $widthAndHeightTags .= ' width="' . $this->_imageSizeWidth . '"';
            $widthAndHeightTags .= ' heigth="' . $this->_imageSizeHeight . '"';
        } else if ($this->_imageSizeWidth) {
            $imageHelper->resize($this->_imageSizeWidth);
            $widthAndHeightTags .= ' width="' . $this->_imageSizeWidth . '"';
        } elseif ($this->_imageSizeHeight) {
            $imageHelper->resize(null, $this->_imageSizeHeight);
            $widthAndHeightTags .= ' heigth="' . $this->_imageSizeHeight . '"';
        }

        //prepare lightbox rel attribute
        $lightboxAttr = '';
        if ($this->_canShowLightbox) {
            $lightboxAttr = 'rel="lightbox"';
        }

        //prepare title attribute
        $titleAttr = sprintf('title="%s"', $this->escapeHtml($row->getName()));

        if ($this->_canClick) {
            $href = $this->_getImageUrl($row);
            return sprintf('<a href="%s" class="%s" target="_blank" %s %s><img src="%s" border="0" alt="" %s/>',
                $href,
                $this->_imageClass,
                $lightboxAttr,
                $titleAttr,
                $imageHelper,
                $widthAndHeightTags
            );
        } else {
            return sprintf('<img src="%s" class="%s" border="0" alt="" %s/>',
                $imageHelper,
                $this->_imageClass,
                $widthAndHeightTags
            );
        }
    }

    /**
     * @param Varien_Object $row
     * @return string
     */
    protected function _getImageUrl($row)
    {
        $value =  $row->getData($this->getColumn()->getIndex());

        /** @var $imageHelper Mage_Catalog_Helper_Image */
        $imageHelper = Mage::helper('catalog/image')->init($row, 'lanot_gridimage_big', $value);
        if($this->_imageSizeBigWidth && $this->_imageSizeBigHeight) {
            $imageHelper->resize($this->_imageSizeBigWidth, $this->_imageSizeBigHeight);
        } else if ($this->_imageSizeBigWidth) {
            $imageHelper->resize($this->_imageSizeBigWidth);
        } elseif ($this->_imageSizeBigHeight) {
            $imageHelper->resize(null, $this->_imageSizeBigHeight);
        } else {
            return Mage::getSingleton('catalog/product_media_config')->getMediaUrl($value);
        }
        return $imageHelper->__toString();
    }
}