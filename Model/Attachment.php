<?php

class Ccc_Outlook_Model_Attachment extends Mage_Core_Model_Abstract
{
    protected $_email;
    protected function _construct()
    {
        $this->_init('ccc_outlook/attachment');
    }

    public function saveAttachmentData($attachments)
    {
        foreach ($attachments as $attachment) {
            $this->addData(
                [
                    'filename' => $attachment['filename'],
                    'email_id' => $attachment['id'],
                ]
            );
        }
        return $this;
    }

    public function downloadMail($attachment)
    {
        // var_dump($attachment);
        $fileName = $attachment['name'];
        $this->saveAttachment($attachment);
        $downloadedAttachment[] = [
            'id' => $this->getEmails()->getId(),
            'filename' => $fileName,
        ];
        return $downloadedAttachment;
    }
    public function setEmails($email)
    {
        $this->_email = $email;
        return $this;
    }

    public function getEmails()
    {
        return $this->_email;
    }


    public function saveAttachment($attachment)
    {
        // var_dump($this->getEmails());
        $filePath = Mage::getBaseDir('var') . DS . 'attachment' . DS . $this->getEmails()->getConfigData()->getId() . DS . $this->getEmails()->getId() . DS . $attachment['name'];
        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }
        if (!file_put_contents($filePath, base64_decode($attachment['contentBytes']))) {
            throw new Exception('Failed to save attachment: ' . $filePath);
        }
        // file_put_contents($filePath, base64_decode($attachment['contentBytes']));
    }
}
