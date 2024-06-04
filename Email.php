<?php

class Ccc_Outlook_Model_Email extends Mage_Core_Model_Abstract
{
    protected $_configData;

    public function setConfigurationObject($configData)
    {
        $this->_configData = $configData;
        return $this;
    }
    public function getConfigData()
    {
        return $this->_configData;
    }
    protected function _construct()
    {
        $this->_init('ccc_outlook/email');
    }

    public function setRowData($emails)
    {
        $this->addData(
            [
                'message_id' => $emails['id'],
                'subject' => $emails['subject'],
                'from_email' => $emails['from'],
                'to_email' => $emails['to'],
                'body' => $emails['body'],
                'configuration_id' => $this->getConfigData()->getId(),
            ]
        );
        return $this;
    }

    // public function saveEmails($emails, $configuration)
    // {
    //     var_dump($emails);
    //     $id = $this->addData(
    //         [
    //             'subject' => $emails['subject'],
    //             'from_email' => $emails['from'],
    //             'to_email' => $emails['to'],
    //             'body' => $emails['body'],
    //             'configuration_id' => $configuration->getId(),
    //         ]
    //     )->save()->getId();
    //     if ($emails['has_attachments']) {
    //         $outlookModel = Mage::getModel('ccc_outlook/outlook');
    //         $accessToken = $outlookModel
    //             ->readTokenFromFile($configuration);
    //         $params = array(
    //             'accesstoken' => $accessToken,
    //             'message_id' => $emails['id'],
    //             'email_id' => $id
    //         );
    //         $attachments = $outlookModel
    //             ->downloadAttachments($configuration, $params);
    //         foreach ($attachments as $attachment) {
    //             Mage::getModel('ccc_outlook/attachment')
    //                 ->setAttachment($attachment, $id);
    //         }
    //     }

    //     // print_r($id);die;
    //     // if ($mail['attachment']=='yes') {
    //     //     Mage::getModel('ccc_outlook/attachment')
    //     //         ->setAttachment($mail['attachment'],$id);
    //     // }
    //     return $id;

    // }


    public function fetchAndSaveAttachment()
    {
        $allAttachments = Mage::getModel("ccc_outlook/outlook")->setEmailData($this)
                                                     ->fetchAllAttachments();
        foreach($allAttachments['value'] as $allAttachment){
            // if (isset($attachment['contentBytes'])) {
                $attachmentModel =Mage::getModel("ccc_outlook/attachment")->setEmails($this);
                // var_dump($this);
                // var_dump($attachmentModel->setEmails($this)->getEmails());
            $downloadAttachment = $attachmentModel->downloadMail($allAttachment);
            $attachmentModel->saveAttachmentData($downloadAttachment)
            ->save();
            return $this;
            // }
        }
    }


}
