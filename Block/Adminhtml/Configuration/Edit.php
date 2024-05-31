<?php
class Ccc_Outlook_Block_Adminhtml_Configuration_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_configuration';
        $this->_blockGroup = 'ccc_outlook';
        parent::__construct();

        $this->_updateButton('save', 'label', Mage::helper('ccc_outlook')->__('Save Configuration'));
        $this->_updateButton('delete', 'label', Mage::helper('ccc_outlook')->__('Delete Configuration'));
        $this->_updateButton('login', 'label', Mage::helper('ccc_outlook')->__('Login'));

        $this->_addButton('saveandcontinue', array(
            'label' => Mage::helper('ccc_outlook')->__('Save and Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
        ), -100);
        $this->_addButton('login', array(
            'label'     => Mage::helper('adminhtml')->__('Login'),
            'onclick'   => 'setLocation(\'' . $this->getLoginUrl() . '\')',
            'class'     => 'login',
        ), -1);

        $this->_formScripts[] = "
        function toggleEditor() {
            if (tinyMCE.getInstanceById('block_content') == null) {
                tinyMCE.execCommand('mceAddControl', false, 'block_content');
            } else {
                tinyMCE.execCommand('mceRemoveControl', false, 'block_content');
            }
        }

        function saveAndContinueEdit(){
            editForm.submit($('edit_form').action+'back/edit/');
        }
    ";
    }
    
    public function getHeaderText()
    {
        if (Mage::registry('configuration_data')->getId()) {
            return Mage::helper('ccc_outlook')->__("Edit Outlook '%s'", $this->escapeHtml(Mage::registry('configuration_data')->getTitle()));
        } else {
            return Mage::helper('ccc_outlook')->__('New Configuration');
        }
    }

    public function getLoginUrl()
    {
        return $this->getUrl('*/*/login', array(
            Mage_Core_Model_Url::FORM_KEY => $this->getFormKey()
        ));
    }
}
?>