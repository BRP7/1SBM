<?php 
class Ccc_Outlook_Block_Adminhtml_Configuration_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array('id' => 'edit_form', 'action' => $this->getUrl('*/*/save'), 'method' => 'post'));
        $form->setUseContainer(true); // put this only this file when use tabs
        $this->setForm($form);
        return parent::_prepareForm();
    }

}
