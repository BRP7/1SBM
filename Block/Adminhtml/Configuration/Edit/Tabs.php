<?php
class Ccc_Outlook_Block_Adminhtml_Configuration_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('outlook_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('ccc_outlook')->__('Configuration Information'));
    }
    protected function _beforeToHtml()
    {
        $this->addTab('general', array(
            'label'     => Mage::helper('ccc_outlook')->__('General'),
            'title'     => Mage::helper('ccc_outlook')->__('General'),
            'content'   => $this->getLayout()->createBlock('ccc_outlook/adminhtml_configuration_edit_tab_general')->toHtml(),
        ));

        $this->addTab('advance', array(
            'label'     => Mage::helper('ccc_outlook')->__('Advance'),
            'title'     => Mage::helper('ccc_outlook')->__('Advance'),
            'content'   => $this->getLayout()->createBlock('ccc_outlook/adminhtml_configuration_edit_tab_advance')->toHtml(),
        ));

        return parent::_beforeToHtml();
    }
}
