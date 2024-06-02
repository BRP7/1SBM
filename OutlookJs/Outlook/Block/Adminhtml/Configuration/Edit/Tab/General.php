<?php
class Ccc_Outlook_Block_Adminhtml_Configuration_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $model = Mage::registry('configuration_model');

        $fieldset = $form->addFieldset('general_fieldset', array('legend' => Mage::helper('ccc_outlook')->__('Genneral Information')));
        if ($model->getId()) {
            $fieldset->addField(
                'configuration_id',
                'hidden',
                array(
                    'name' => 'configuration_id',
                )
            );
        }
        $fieldset->addField(
            'email_address',
            'text',
            array(
                'name' => 'email_address',
                'label' => Mage::helper('ccc_outlook')->__('Email'),
                'required'=>true,
                'title' => Mage::helper('ccc_outlook')->__('Email'),
            )
        );
        $fieldset->addField(
            'client_id',
            'text',
            array(
                'name' => 'client_id',
                'label' => Mage::helper('ccc_outlook')->__('Client Id'),
                'required'=>true,
                'title' => Mage::helper('ccc_outlook')->__('Client Id'),
            )
        );
        $fieldset->addField(
            'client_secret',
            'text',
            array(
                'name' => 'client_secret',
                'label' => Mage::helper('ccc_outlook')->__('Client Secret'),
                'required'=>true,
                'title' => Mage::helper('ccc_outlook')->__('Client Secret'),
            )
        );
        $fieldset->addField(
            'redirect_url',
            'text',
            array(
                'name' => 'redirect_url',
                'label' => Mage::helper('ccc_outlook')->__('Redirect Url'),
                'required'=>true,
                'title' => Mage::helper('ccc_outlook')->__('Redirect Url'),
            )
        );

        $fieldset->addField(
            'scope',
            'text',
            array(
                'name' => 'scope',
                'label' => Mage::helper('ccc_outlook')->__('Scope'),
                'required'=>true,
                'title' => Mage::helper('ccc_outlook')->__('Token'),
            )
        );
        $fieldset->addField(
            'is_active',
            'select',
            array(
                'label' => Mage::helper('ccc_outlook')->__('is_active'),
                'title' => Mage::helper('ccc_outlook')->__('is_active'),
                'name' => 'is_active',
                'required' => true,
                'options' => array(
                    '1' => Mage::helper('ccc_outlook')->__('Yes'),
                    '0' => Mage::helper('ccc_outlook')->__('No'),
                ),
            )
        );

        
        if (!($model->getId())) {
            $model->setData('is_active', '1');
        }
        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
    public function getTabLabel()
    {
        return Mage::helper('ccc_outlook')->__('General');
    }
    public function getTabTitle()
    {
        return Mage::helper('ccc_outlook')->__('General');
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
