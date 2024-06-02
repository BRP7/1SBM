<?php 
class Ccc_Outlook_Block_Adminhtml_Configuration_Edit_Tab_Advance extends Mage_Adminhtml_Block_Widget_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('outlook/configuration.phtml');
      
    }
       public function getTabLabel()
    {
        return Mage::helper('practice_test')->__('Advance');
    }
    public function getTabTitle()
    {
        return Mage::helper('practice_test')->__('Advance');
    }
    public function canShowTab()
    {
        return true;
    }
    public function isHidden()
    {
        return false;
    }
}
