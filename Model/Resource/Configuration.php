
<?php
class Ccc_Outlook_Model_Resource_Configuration extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('ccc_outlook/configuration', 'configuration_id');
    }
    
}
