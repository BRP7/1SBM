<?php
class Ccc_Outlook_Model_Configuration extends Mage_Core_Model_Abstract
{
    protected $_eventPrefix = 'last_stored_email';
    protected $_eventObject = 'email';
    protected function _construct()
    {
        $this->_init('ccc_outlook/configuration');
    }

    public function formatDates($dateString)
    {
        $date = new DateTime($dateString, new DateTimeZone('UTC'));
        return $date->format('Y-m-d H:i:s');
    }

    public function fetchEmails()
    {
        $apiModel = Mage::getModel('ccc_outlook/outlook')->setConfigurationData($this);
        $emails = $apiModel->getEmails();
        var_dump($emails);
        foreach ($emails as $email) {
            $emailModel = Mage::getModel("ccc_outlook/email");
            $emailModel->setConfigurationObject($this)
                ->setRowData($email)
                ->save();
            $this->checkCondition($emailModel);
      }
        if ($email['has_attachments']) {
            $emailModel->fetchAndSaveAttachment();
        }
        $this->setLastReadedEmails($this->formatDates($email['createdDateTime']))->save();
    }

    public function checkCondition($emailModel)
    {
        $dispatchConfigurations = Mage::getModel('ccc_outlook/dispatchevent')
            ->getCollection()
            ->addFieldToFilter("configuration_id", $this->getId());
        $groupId = 0;
        $flag = true;
        foreach ($dispatchConfigurations->getData() as $tables) {
            if ($groupId == $tables['group_id']) {
                var_dump($tables['condition_name']);
                var_dump($tables);
                $flag=$this->matchCondition($emailModel, $tables, $flag);
               
            // }
                // echo "----------------------------------------------------------reurned-------------------------------------------------------------------------";
                // var_dump($flag);
                if ($flag == false) {
                    break;
                }
            } else {
                $flag = true; 
                $groupId++;
                var_dump($tables);  
                // die;
                $this->matchCondition($emailModel, $tables, $flag);
            }
            if ($flag) {
                echo "event called!";
                var_dump($tables['event_name']);
                Mage::dispatchEvent($tables['event']);
            }
            // die;
        }
    }


    public function matchCondition($emailModel, $tables, $flag)
    {
        var_dump($tables['group_id']);
        var_dump($tables['group_id']);
        switch ($tables['operator']) {
            case '=':
                // echo '=';
                $result = $emailModel[$tables['condition_name']] = $tables['value'];
                // var_dump($result);
                break;
            case '>=':
                // echo '>=';
                $result = strcmp($emailModel[$tables['condition_name']], $tables['value']) > 0
                    ? true : false;
                $flag = $result && $flag;
                // var_dump($result);
                break;
            case '<=':
                // echo '<=';
                $result = strcmp($emailModel[$tables['condition_name']], $tables['value']) < 0
                    ? true : false;
                $flag = $result && $flag;
                // var_dump($result);
                break;
            case '!=':
                // echo '!=';
                $result = $emailModel[$tables['condition_name']] != $tables['value'];
                $flag = $result && $flag;
                // var_dump($result);
                break;
            case 'Like':
                echo 'LIke';
                var_dump($emailModel[$tables['condition_name']]);
                var_dump($tables['value']);
                $result = strcmp($emailModel[$tables['condition_name']], $tables['value']) == 0 ? true : false;
                var_Dump($result);
                $flag = $result && $flag;
                break;
            case '%Like%':
                // echo '%Like%';
                // var_dump($tables['condition_name']);
                // var_dump($emailModel[$tables['condition_name']]);
                // var_dump($tables['value']);

                $result = strpos($emailModel[$tables['condition_name']], $tables['value']) !== false;
                // var_dump($result);
                $flag = $result && $flag;
                break;

            default: {
                var_dump('not valid operator');
            }
        }
        return $flag;
    }
}


