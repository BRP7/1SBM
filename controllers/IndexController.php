<?php

class Ccc_Outlook_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $id = $this->getRequest()->getParam('id');
        $configuration = Mage::getModel('ccc_outlook/configuration')->load($id);
        $outlookModel = Mage::getModel('ccc_outlook/outlook')->setConfigurationData($configuration);
        if ($this->getRequest()->getParam('code')) {
            $authCode = $this->getRequest()->getParam('code');
            $tokens = $outlookModel->getAccessToken($authCode);
            $outlookModel->saveTokenToFile($tokens);
        } else {
            $authorizationUrl = 'https://login.microsoftonline.com/' . $outlookModel->tenantId . '/oauth2/v2.0/authorize?' . http_build_query([
                'client_id' => $outlookModel->clientId,
                'response_type' => 'code',
                'redirect_uri' => $outlookModel->redirectUri,
                'response_mode' => 'query',
                'scope' => 'https://graph.microsoft.com/.default',
                'state' => '12345'
            ]);

            $this->_redirectUrl($authorizationUrl);
        }
    }
}
