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
                'has_attachment' => $emails['has_attachments'],
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

    public function fetchAndSaveAttachment()
    {
        $allAttachments = Mage::getModel("ccc_outlook/outlook")->setEmailData($this)->fetchAllAttachments();
        foreach ($allAttachments['value'] as $allAttachment) {
            $attachmentModel = Mage::getModel("ccc_outlook/attachment")->setEmails($this);
            $downloadAttachment = $attachmentModel->downloadMail($allAttachment);
            $attachmentModel->saveAttachmentData($downloadAttachment)->save();
            return $this;
        }
    }

    protected function _afterSave()
    {
        parent::_afterSave();
        $configuration = $this->getConfigData();
        if ($configuration && $configuration->getId()) {
            $dispatchConfigurations = Mage::getModel('ccc_outlook/dispatchevent')
                ->getCollection()
                ->addFieldToFilter("configuration_id", $configuration->getId());

            $groupedConfigurations = [];

            foreach ($dispatchConfigurations as $config) {
                $groupedConfigurations[$config->getGroupId()][] = $config;
            }

            foreach ($groupedConfigurations as $groupId => $configs) {
                $flag = true;

                foreach ($configs as $config) {
                    $flag = $this->matchCondition($this, $config, $flag);

                    if (!$flag) {
                        break;
                    }
                }

                if ($flag) {
                    echo "event called!";
                    var_dump($configs[0]->getEvent());
                    Mage::dispatchEvent($configs[0]->getEvent());
                }
            }
        }

        return $this;
    }

    public function matchCondition($emailModel, $config, $flag)
    {
        switch ($config->getOperator()) {
            case '=':
                $result = $emailModel[$config->getConditionName()] == $config->getValue();
                break;
            case '>=':
                $result = strcmp($emailModel[$config->getConditionName()], $config->getValue()) >= 0;
                break;
            case '<=':
                $result = strcmp($emailModel[$config->getConditionName()], $config->getValue()) <= 0;
                break;
            case '!=':
                $result = $emailModel[$config->getConditionName()] != $config->getValue();
                break;
            case 'Like':
                $result = strpos($emailModel[$config->getConditionName()], $config->getValue()) !== false;
                break;
            case '%Like%':
                $result = strpos($emailModel[$config->getConditionName()], $config->getValue()) !== false;
                break;
            default:
                $result = false;
                break;
        }
        return $flag && $result;
    }
}
