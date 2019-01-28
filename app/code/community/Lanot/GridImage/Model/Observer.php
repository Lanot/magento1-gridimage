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

class Lanot_GridImage_Model_Observer
{
    /**
     * @var null|array
     */
    protected $_rulesEnabled = null;

    /**
     * @var null|array
     */
    protected $_rulesDisabled= null;

    /**
     * @param Varien_Object $observer
     */
    public function adminhtmlBlockHtmlBefore($observer)
    {
        /** @var $block Mage_Adminhtml_Block_Widget_Grid */
        $block = $observer->getEvent()->getBlock();
        if (!$block) {
            return $this;
        }

        if (null === $this->_rulesEnabled) {
            $this->_initRules();
        }

        $enabledData = $this->_isEnabled($block);
        if ((false === $enabledData) || $this->_isDisabled($block) || empty($enabledData['handler'])) {
            return $this;
        } else {
            Mage::getModel($enabledData['handler'])->prepare($block, $enabledData);
        }
        return $this;
    }

    /**
     * Init rules from configuration
     *
     * @return Lanot_GridImage_Model_Observer
     */
    protected function _initRules()
    {
        $this->_rulesEnabled = array();

        /** @var $ruleNode Mage_Core_Model_Config_Element */
        if (Mage::getConfig()->getNode('default/lanot_gridimage/rules/enabled')) {
            foreach(Mage::getConfig()->getNode('default/lanot_gridimage/rules/enabled')->children() as $ruleClass => $ruleNode) {
                $ruleClass = trim($ruleClass);
                $data = (array) $ruleNode;
                $this->_rulesEnabled[$ruleClass] = !empty($data['@attributes']) ? $data['@attributes'] : array();
            }
        } else {
            $this->_rulesEnabled = array();
        }

        if (Mage::getConfig()->getNode('default/lanot_gridimage/rules/disabled')) {
            foreach(Mage::getConfig()->getNode('default/lanot_gridimage/rules/disabled')->children() as $ruleClass => $ruleNode) {
                $ruleClass = trim($ruleClass);
                $this->_rulesDisabled[$ruleClass] = $ruleClass;
            }
        } else {
            $this->_rulesDisabled = array();
        }

        return $this;
    }

    /**
     * Check if is block mathed by rules
     *
     * @param Mage_Adminhtml_Block_Template $block
     * @return bool|array
     */
    protected function _isEnabled($block)
    {
        foreach($this->_rulesEnabled as $ruleClass => $ruleData) {
            if ($block instanceof $ruleClass) {
                return $ruleData;
            }
        }
        return false;
    }

    /**
     * Check if is block matched by rules
     *
     * @param Mage_Adminhtml_Block_Template $block
     * @return bool
     */
    protected function _isDisabled($block)
    {
        foreach($this->_rulesDisabled as $ruleClass) {
            if ($block instanceof $ruleClass) {
                return true;
            }
        }
        return false;
    }
}
