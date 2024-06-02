<?php
class Ccc_Outlook_Model_Resource_Attachment_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('ccc_outlook/attachment');
    }
}
