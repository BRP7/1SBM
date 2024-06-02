<?php
class Ccc_Outlook_Model_Observer
{
    // public function readMail()
    // {
    //     // $configurationCollection = Mage::getModel('ccc_outlook/configuration')->getCollection();
    //     // foreach ($configurationCollection as $collection) {
    //     //     Mage::getModel('ccc_outlook/outlook')->getAllEmails();
    //     // }
    //     $authorizationUrl=Mage::getModel('ccc_outlook/outlook')->getAuthorizationUrl();
    //     print_r($authorizationUrl);
    //     header('Location: '.$authorizationUrl);
    // }
    public function readMail()
    {
        try {
            $configurationCollection = Mage::getModel('ccc_outlook/configuration')
                ->getCollection()
                ->addFieldToFilter('is_active',1);
            $outlookModel = Mage::getModel('ccc_outlook/outlook');
            if ($configurationCollection) {
                foreach ($configurationCollection as $_configuration) {
                    $emails = $outlookModel->getEmails($_configuration);
                    $parseEmails = $outlookModel->parseMails($emails);
                    if ($parseEmails) {
                        foreach ($parseEmails as $parseEmail) {
                            $id = Mage::getModel('ccc_outlook/email')
                                ->storeMails($parseEmail, $_configuration);
                            if ($id) {
                                $_configuration->setData('last_readed_id', $id)
                                    ->save();
                            }
                        }
                    }
                }
            }

        } catch (Exception $e) {
            Mage::log('Error reading emails: ' . $e->getMessage(), null, 'outlook_emails.log');
        }
    }

}