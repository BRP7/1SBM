<?php
class Ccc_Outlook_Block_Adminhtml_Configuration_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct($attributes = array())
    {
        parent::__construct($attributes);

    }
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('ccc_outlook/configuration')->getCollection();
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'email_address',
            array(
                'header' => Mage::helper('ccc_outlook')->__('Email'),
                'id'=> 'email_address',
                'align' => 'center',
                'width' => '200px',
                'index' => 'email_address',
            )
        );
        $this->addColumn(
            'client_id',
            array(
                'header' => Mage::helper('ccc_outlook')->__('Client Id'),
                'id'=> 'client_id',

                'align' => 'center',
                'width' => '200px',
                'index' => 'client_id',
            )
        );
        $this->addColumn(
            'client_secret',
            array(
                'header' => Mage::helper('ccc_outlook')->__('Client Secret'),
                'align' => 'center',
                'width' => '100px',
                'index' => 'client_secret',
            )
        );
        $this->addColumn(
            'redirect_url',
            array(
                'header' => Mage::helper('ccc_outlook')->__('Redirect Url'),
                'align' => 'center',
                'width' => '100px',
                'index' => 'redirect_url',
            )
        );
        
        $this->addColumn(
            'scope',
            array(
                'header' => Mage::helper('ccc_outlook')->__('Scope'),
                'align' => 'center',
                'width' => '100px',
                'index' => 'scope',
            )
        );
        $this->addColumn(
            'is_active',
            array(
                'header' => Mage::helper('ccc_outlook')->__('is_active'),
                'align' => 'center',
                'index' => 'is_active',
                'width' => '100px',
                'type' => 'options',
                'options' => array(
                    1 => Mage::helper('ccc_outlook')->__('Yes'),
                    0 => Mage::helper('ccc_outlook')->__('No')
                ),
            )
        );
        return parent::_prepareColumns();
    }
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('configuration_id' => $row->getId()));
    }
}
