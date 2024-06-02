<?php
class Ccc_Outlook_Model_Email extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('ccc_outlook/email');
    }
    public function storeMails($mail, Ccc_Outlook_Model_Configuration $configuration)
    {
        $id=$this->addData(
            [
                'subject' => $mail['subject'],
                'from' => $mail['from'],
                'to' => $mail['to'],
                'body' => $mail['body'],
                'configuration_id' => $configuration->getId(),
            ]
        )->save()->getId();
        // print_r($id);die;
        // if ($mail['attachment']=='yes') {
        //     Mage::getModel('ccc_outlook/attachment')
        //         ->setAttachment($mail['attachment'],$id);
        // }
        return $id;
    }
}
