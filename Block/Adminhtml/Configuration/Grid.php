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
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('configuration_id', array(
            'header' => Mage::helper('ccc_outlook')->__('Id'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'configuration_id',
        )
        );

        $this->addColumn('username', array(
            'header' => Mage::helper('ccc_outlook')->__('User Name'),
            'align' => 'left',
            'index' => 'username',
            'type' => 'text',
            'column_css_class' => 'row_name',
        )
        );
        $this->addColumn('tenant_id', array(
            'header' => Mage::helper('ccc_outlook')->__('Tenant Id'),
            'align' => 'left',
            'index' => 'tenant_id',
            'type' => 'text',
        )
        );
        $this->addColumn('client_secret', array(
            'header' => Mage::helper('ccc_outlook')->__('Client Secret'),
            'align' => 'left',
            'index' => 'client_secret',
            'type' => 'text',
            'column_css_class' => 'row_name',
        )
        );
        $this->addColumn('client_id', array(
            'header' => Mage::helper('ccc_outlook')->__('Client Id'),
            'align' => 'left',
            'index' => 'client_id',
            'type' => 'text',
            'column_css_class' => 'row_name',
        )
        );

        $this->addColumn('is_active', array(
            'header' => Mage::helper('ccc_outlook')->__('Is Active'),
            'align' => 'left',
            'index' => 'is_active',
            'type' => 'options',
            'options' => array(
                '1' => Mage::helper('ccc_outlook')->__('Yes'),
                '0' => Mage::helper('ccc_outlook')->__('No'),
            ),
            'column_css_class' => 'is_active',
        )
        );
      

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
    
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('id'); 

        $this->getMassactionBlock()->addItem(
            'delete',
            array(
                'label' => Mage::helper('ccc_outlook')->__('Delete'),
                'url' => $this->getUrl('*/*/massDelete'),
                'confirm' => Mage::helper('ccc_outlook')->__('Are you sure you want to delete selected configuration?')
            )
        );

        // $statuses = Mage::getSingleton('ccc_locationcheck/status')->getOptionArray();

        // array_unshift($statuses, array('label' => '', 'value' => ''));
        // $this->getMassactionBlock()->addItem(
        //     'is_active',
        //     array(
        //         'label' => Mage::helper('locationcheck')->__('Change Is Active'),
        //         'url' => $this->getUrl('*/*/massStatus', array('_current' => true)),
        //         'additional' => array(
        //             'visibility' => array(
        //                 'name' => 'is_active',
        //                 'type' => 'select',
        //                 'class' => 'required-entry',
        //                 'label' => Mage::helper('locationcheck')->__('Is Active'),
        //                 'values' => $statuses
        //             )
        //         )
        //     )
        // );

        Mage::dispatchEvent('outlook_adminhtml_configuration_grid_prepare_massaction', array('block' => $this));
        return $this;
    }



}
