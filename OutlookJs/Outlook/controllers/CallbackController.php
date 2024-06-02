<?php 
class Ccc_Outlook_CallbackController extends Mage_Core_Controller_Front_Action{
    public function tokenAction(){
        $code = $this->getRequest()->getParam('code');
        $id=$this->getRequest()->getParam('id');
        $outlookModel=Mage::getModel('ccc_outlook/outlook');
        $configurationModel=Mage::getModel('ccc_outlook/configuration')
        ->load($id);
        $token=$outlookModel->getAccessToken($configurationModel,$code);
        $outlookModel->saveTokenToFile($token,$configurationModel);
    }
    public function mailAction(){
        Mage::getModel('ccc_outlook/observer')->readMail();
    }
}
