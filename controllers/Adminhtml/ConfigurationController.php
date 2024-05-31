<?php

class Ccc_Outlook_Adminhtml_ConfigurationController extends Mage_Adminhtml_Controller_Action{
    
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('ccc_configuration/index');
        return $this;
    }


    public function indexAction()
    {
        $this->_title($this->__('Manage_Configuration'));
        $this->_initAction();
        $this->renderLayout();

    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {

        $this->_title($this->__('configuration'))->_title($this->__('configuration'));

        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('ccc_outlook/configuration');

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('configuration')->__('This locationcheck no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $this->_title($model->getId() ? $model->getTitle() : $this->__('New Configuration'));

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {

            $model->setData($data);
        }

        Mage::register('configuration_data', $model);

        $this->_initAction()
            ->_addBreadcrumb($id ? Mage::helper('ccc_outlook')->__('Edit Outlook Configuration') : Mage::helper('ccc_outlook')->__('New Outlook Configuration'), $id ? Mage::helper('ccc_outlook')->__('Edit Outlook Configuration') : Mage::helper('ccc_outlook')->__('New Outlook Configuration'));
        $this->renderLayout();
    }


    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            // var_dump($data);
            // die;
            $model = Mage::getModel('ccc_outlook/configuration');
            if ($id = $this->getRequest()->getParam('configuration_id')) {
                // die;
                $model->load($id);
                if (!$model->getId()) {
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('ccc_outlook')->__('This configuration no longer exists.'));
                    $this->_redirect('*/*/');
                    return;
                }
            }
            
            $model->setData($data);
            try {
                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('ccc_outlook')->__('The configuration has been saved.'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);
    
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId(), '_current' => true));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException($e, Mage::helper('ccc_outlook')->__('An error occurred while saving the configuration.'));
            }
            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            return;
        }
        $this->_redirect('*/*/');
    }
    
    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            $title = "";
            try {
                $model = Mage::getModel('ccc_outlook/configuration');
                $model->load($id);
                $title = $model->getTitle();
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('ccc_outlook')->__('The page has been deleted.')
                );
                Mage::dispatchEvent('adminhtml_cmspage_on_delete', array('title' => $title, 'status' => 'success'));
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::dispatchEvent('adminhtml_cmspage_on_delete', array('title' => $title, 'status' => 'fail'));
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $id));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('ccc_outlook')->__('Unable to find a page to delete.'));
        $this->_redirect('*/*/');
    }
    public function massDeleteAction()
    {
        $configurationIds = $this->getRequest()->getParam('id');
        if (!is_array($configurationIds)) {
            $this->_getSession()->addError($this->__('Please select configuration(s).'));
        } else {
            if (!empty($configurationIds)) {
                try {
                    foreach ($configurationIds as $configurationId) {
                        $config = Mage::getSingleton('ccc_outlook/configuration')->load($configurationId);
                        $config->delete();
                    }
                    $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) have been deleted.', count($configurationIds))
                    );
                } catch (Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }
            }
        }
        $this->_redirect('*/*/index');
    }


    public function saveConfigurationAction() {
        $tables = $this->getRequest()->getPost('tables', array());
        $tables = json_decode($tables, true);

        if ($tables) {
            foreach ($tables as $table) {
                foreach ($table as $row) {
                    // Save each row to the database
                    $model = Mage::getModel('ccc_outlook/dispatchevent');
                    $model->setData($row);
                    $model->save();
                }
            }
            $this->getResponse()->setBody('success');
        } else {
            $this->getResponse()->setBody('error');
        }
    }

    public function loginAction(){
        $authorizationUrl = Mage::getModel('ccc_outlook/outlook')->getAuthorizationUrl();
        $this->_redirectUrl($authorizationUrl);
    }

    
}