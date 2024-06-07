<?php

class Ccc_Outlook_Model_Observer
{

    public function readMail()
    {
        try {
            $configurationCollection = Mage::getModel('ccc_outlook/configuration');
            if ($configurationCollection) {
                foreach ($configurationCollection->getCollection() as $_configuration) {
                    $_configuration->fetchEmails();

                }
            }
        } catch (Exception $e) {
            Mage::log('Error reading emails: ' . $e->getMessage(), null, 'outlook_emails.log');
        }
    }

    public function checkConditionAfterEmailSave(Varien_Event_Observer $observer){
        echo "event called!";
        print_r($observer);
    }

}
