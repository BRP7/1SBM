<?php
class Ccc_Outlook_Model_Outlook
{
    protected $_configurationData;
    protected $_emailData;
    private $tenantId = 'common';
    public function setConfigurationData($configurationData){
        $this->_configurationData = $configurationData;
        return $this;
    }

    public function getConfigurationData(){
        return $this->_configurationData;
    }
 
    public function setEmailData($emailObj){
        $this->_emailData=$emailObj;
        return $this;
    }

    public function getEmailData(){
        return  $this->_emailData;
    }


    public function getAuthorizationUrl()
    {

        $authorizationEndpoint = sprintf(
            "https://login.microsoftonline.com/%s/oauth2/v2.0/authorize",
            $this->tenantId
        );
        $authUrl = sprintf(
            "%s?client_id=%s&response_type=code&redirect_uri=%s&scope=%s",
            $authorizationEndpoint,
            $this->getConfigurationData()->getClientId(),
            urlencode($this->getConfigurationData()->getRedirectUrl() .  $this->getConfigurationData()->getId()),
            urlencode($this->getConfigurationData()->getScope())
        );
        return $authUrl;
    }


    public function getAccessToken($authorizationCode)
    {
        $tokenEndpoint = sprintf(
            "https://login.microsoftonline.com/%s/oauth2/v2.0/token",
            $this->tenantId
        );
        $data = [
            'client_id' => $this->getConfigurationData()->getClientId(),
            'client_secret' => $this->getConfigurationData()->getClientSecret(),
            'code' => $authorizationCode,
            'redirect_uri' => $this->getConfigurationData()->getRedirectUrl() . $this->getConfigurationData()->getId(),
            'grant_type' => 'authorization_code',
            // 'grant_type' => 'client_credentials',
            'scope' => $this->getConfigurationData()->getScope(),
        ];
        $ch = curl_init($tokenEndpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $response = curl_exec($ch);
        if ($response === false) {
            throw new Exception('Error fetching access token: ' . curl_error($ch));
        }
        curl_close($ch);
        $result = json_decode($response, true);
        if (isset($result['error'])) {
            throw new Exception('Error in response: ' . $result['error_description']);
        }
        return $result['access_token'];
    }

    public function getEmails()
    {
        $accessToken = $this->readTokenFromFile();
       
        $baseUrl = "https://graph.microsoft.com/v1.0/me/messages";
        $lastReadDateTime = (new DateTime($this->_configurationData->getLastReadedEmails()))->format(DateTime::ATOM);
        $lastReadDateTime = new DateTime($this->_configurationData->getLastReadedEmails(), new DateTimeZone('UTC'));
        // $lastReadDateTime->modify('+1 second');
        $lastReadDateTimeFormatted = $lastReadDateTime->format(DateTime::ATOM);
        $params = [
            '$filter' => "receivedDateTime gt {$lastReadDateTimeFormatted}"
        ];
      
       
        $url = $this->buildMailUrl($baseUrl, $params);
        $headers = [
            'Authorization: Bearer ' . $accessToken,
            'Accept: application/json'
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $response = curl_exec($ch);
        curl_close($ch);
        return $this->parseMails(json_decode($response, true));
    }

    public function buildMailUrl($baseUrl, $params = [])
    {
        return $baseUrl . '?' . http_build_query($params);
    }

    public function saveTokenToFile($data)
    {
        $filePath = Mage::getBaseDir('var') . DS . 'export' . DS . $this->getConfigurationData()->getId() . '.txt';
        try {
            $io = new Varien_Io_File();
            $io->setAllowCreateFolders(true);
            $exportDir = Mage::getBaseDir('var') . DS . 'export';
            if (!is_dir($exportDir)) {
                $io->mkdir($exportDir, 0755, true);
            }
            $io->open(array('path' => $exportDir));
            $io->streamOpen($filePath, 'w+');
            $io->streamLock(true);
            $io->streamWrite($data);
            $io->streamUnlock();
            $io->streamClose();
            return true;
        } catch (Exception $e) {
            Mage::logException($e);
            return false;
        }
    }
    public function readTokenFromFile()
    {
        $configObj = $this->getConfigurationData() ??  $this->getEmailData()->getConfigData();
        $filePath = Mage::getBaseDir('var') . DS . 'export' . DS . $configObj->getId() . '.txt';
        try {
            $io = new Varien_Io_File();
            if ($io->fileExists($filePath)) {
                $io->open(array('path' => Mage::getBaseDir('var') . DS . 'export'));
                $data = $io->read($filePath);
                return $data;
            } else {
                return 'File does not exist.';
            }
        } catch (Exception $e) {
            Mage::logException($e);
            return false;
        }
    }

    public function parseMails($message)
    {
        $emails = $message['value'];
        if (isset($message['error'])) {
            $this->handleError($message['error']);
        } else {
            $parsedEmails = array();
            foreach ($emails as $email) {
                $toAddresses = isset($email['toRecipients'])
                    ? array_map(function ($recipient) {
                        return $recipient['emailAddress']['address'] ?? 'N/A';
                    }, $email['toRecipients']) : [];
                $to = implode(', ', $toAddresses);
                $parsedEmail = array(
                    'id' => $email['id'],
                    'createdDateTime' => $email['createdDateTime'],
                    'from' => isset($email['from']['emailAddress']['address'])
                        ? $email['from']['emailAddress']['address'] : '',
                    'to' => $to,
                    'subject' => isset($email['subject']) ? $email['subject'] : '',
                    'body' => isset($email['body']['content'])
                        ? trim(strip_tags($email['body']['content'])) : 'No Content',
                    'has_attachments' => isset($email['hasAttachments']) && $email['hasAttachments']
                        ? true : false
                );
                $parsedEmails[] = $parsedEmail;
            }
            var_dump($parsedEmails['has_attachments']);
            return $parsedEmails;
        }
    }

    private function handleError($error)
    {
        switch ($error['code']) {
            case 'InvalidAuthenticationToken':
                print_r('Error: Invalid authentication token.');
                Mage::log('Error: Invalid authentication token.', null, 'outlook_emails.log');
                break;
                case 'InvalidGrant':
                print_r('Error: Invalid grant. This may be due to an expired authorization code.');
                Mage::log('Error: Invalid grant. This may be due to an expired authorization code.', null, 'outlook_emails.log');
                break;
            case 'ServiceNotAvailable':
                print_r('Error: Microsoft service is currently unavailable.');
                Mage::log('Error: Microsoft service is currently unavailable.', null, 'outlook_emails.log');
                break;
            default:
                print_r($error['message']);
                Mage::log('Error: ' . $error['message'], null, 'outlook_emails.log');
                break;
        }
    }

    public function fetchAllAttachments(){
        $messageId =$this->getEmailData()->getMessageId(); 
        $url = "https://graph.microsoft.com/v1.0/me/messages/{$messageId}/attachments";
        $headers = [
            'Authorization: Bearer ' . $this->readTokenFromFile(),
            'Accept: application/json'
        ]; 
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $response = curl_exec($ch);
        curl_close($ch);

        $attachments = json_decode($response, true);
        return $attachments;
        $downloadedAttachments = [];
        foreach ($attachments['value'] as $attachment) {
            if (isset($attachment['contentBytes'])) {
                $fileName = $attachment['name'];
                $this->saveAttachment($attachment);
                $downloadedAttachment[] = [
                    'id' => $this->getEmailData()->getId(),
                    'filename' => $fileName,
                ];
            }
            $downloadedAttachments[] = $downloadedAttachment;
        }
        return $downloadedAttachments;
    }

    // public function downloadAttachments(Ccc_Outlook_Model_Configuration $configuration, $params)
    // {

    //     $messageId = $params['message_id'];
    //     $url = "https://graph.microsoft.com/v1.0/me/messages/{$messageId}/attachments";
    //     $headers = [
    //         'Authorization: Bearer ' . $params['accesstoken'],
    //         'Accept: application/json'
    //     ];

    //     $ch = curl_init();
    //     curl_setopt($ch, CURLOPT_URL, $url);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    //     $response = curl_exec($ch);
    //     curl_close($ch);

    //     $attachments = json_decode($response, true);
    //     $downloadedAttachments = [];
    //     foreach ($attachments['value'] as $attachment) {
    //         if (isset($attachment['contentBytes'])) {
    //             $fileName = $attachment['name'];
    //             $this->saveAttachment($configuration, $attachment, $params['email_id']);
    //             $downloadedAttachment[] = [
    //                 'id' => $attachment['id'],
    //                 'filename' => $fileName,
    //             ];
    //         }
    //         $downloadedAttachments[] = $downloadedAttachment;
    //     }
    //     return $downloadedAttachments;
    // }

    public function saveAttachment( $attachment)
    {
        $configObj = $this->getConfigurationData() ??  $this->getEmailData()->getConfigData();
        $filePath = Mage::getBaseDir('var') . DS . 'attachment' . DS . $configObj->getId() . DS . $this->getEmailData()->getId() . DS . $attachment['name'];
        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }
        file_put_contents($filePath, base64_decode($attachment['contentBytes']));
    }



}



