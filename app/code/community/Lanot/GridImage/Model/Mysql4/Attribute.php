<?php
/**
 * Banner Category item resource model
 *
 * @author Lanot
 */
class Lanot_GridImage_Model_Mysql4_Attribute
//extends Mage_Core_Model_Resource_Db_Abstract
extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Initialize connection and define main table and primary key
     */
    protected function _construct()
    {
        $this->_init('catalog/product', 'entity_id');
    }

    public function getAttributeEntityValues(Mage_Catalog_Model_Resource_Eav_Attribute $attribute, $entityIds, $storeId = 0)
    {
        $attributeValues = array();
        if (!is_array($entityIds)) {
            $entityIds = array();
        }

        if (empty($entityIds)) {
            return $attributeValues;
        }

        $attributeId = (int) $attribute->getAttributeId();
        $storeId = (int) $storeId;


        $read = $this->getReadConnection();
        $select = $read->select()
            ->from(array('a' => $attribute->getBackendTable()), array('entity_id', 'value'))
            ->where('a.attribute_id = ' . $attributeId)
            ->where('a.`entity_id` IN (' . implode(',', $entityIds) .')')
            ->where('a.store_id IN (0, ?)', $storeId)
            ->where("a.value != 'no_selection'")
            ->order('a.store_id');

        //update data
        $rowSet = $read->fetchAll($select);
        foreach ($rowSet as $row) {
            $attributeValues[$row['entity_id']] = $row['value'];
        }

        return $attributeValues;
    }
}
