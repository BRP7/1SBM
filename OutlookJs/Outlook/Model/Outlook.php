<?php
class Ccc_Outlook_Model_Outlook
{
    private $tenantId = 'common';
    public function getAuthorizationUrl(Ccc_Outlook_Model_Configuration $configuration)
    {
        $authorizationEndpoint = sprintf(
            "https://login.microsoftonline.com/%s/oauth2/v2.0/authorize",
            $this->tenantId
        );
        $authUrl = sprintf(
            "%s?client_id=%s&response_type=code&redirect_uri=%s&scope=%s",
            $authorizationEndpoint,
            $configuration->getClientId(),
            urlencode($configuration->getRedirectUrl().$configuration->getId()),
            urlencode($configuration->getScope())
        );
        return $authUrl;
    }
    public function getAccessToken(Ccc_Outlook_Model_Configuration $configuration, $authorizationCode)
    {
        $tokenEndpoint = sprintf(
            "https://login.microsoftonline.com/%s/oauth2/v2.0/token",
            $this->tenantId
        );
        $data = [
            'client_id' => $configuration->getClientId(),
            'client_secret' => $configuration->getClientSecret(),
            'code' => $authorizationCode,
            'redirect_uri' => $configuration->getRedirectUrl().$configuration->getId(),
            'grant_type' => 'authorization_code',
            // 'grant_type' => 'client_credentials',
            'scope' => $configuration->getScope(),
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
    public function getEmails(Ccc_Outlook_Model_Configuration $configuration)
    {
        $accessToken = $this->readTokenFromFile($configuration);
        $baseUrl = "https://graph.microsoft.com/v1.0/me/messages";
        $params = [
            '$filter' => 'isRead eq false',
            '$top' => 10,
            // '$skip'=>$configuration->getLastReadedId()
            '$skip'=>1
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
        return (json_decode($response, true));
    }
    public function buildMailUrl($baseUrl, $params = [])
    {
        return $baseUrl . '?' . http_build_query($params);
    }
    public function parseMails($emails)
    {
        $emails = $emails['value'] ?? [];
        $parsedEmails = array();
        foreach ($emails as $email) {
            $toAddresses = isset($email['toRecipients']) ? array_map(function ($recipient) {
                return $recipient['emailAddress']['address'] ?? 'N/A';
            }, $email['toRecipients']) : [];
            $to = implode(', ', $toAddresses);
            $parsedEmail = array(
                'from' => isset($email['from']['emailAddress']['address']) ? $email['from']['emailAddress']['address'] : '',
                'to' => $to,
                'subject' => isset($email['subject']) ? $email['subject'] : '',
                'body' => isset($email['body']['content']) ? trim(strip_tags($email['body']['content'])) : 'No Content',
                'attachments' => isset($email['hasAttachments']) && $email['hasAttachments'] ? 'Yes' : 'No'
            );
            $parsedEmails[] = $parsedEmail;
        }
        return $parsedEmails;
    }
    public function saveTokenToFile($data, Ccc_Outlook_Model_Configuration $configuration)
    {
        $filePath = Mage::getBaseDir('var') . DS . 'export' . DS . $configuration->getId() . '.txt';
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
    public function readTokenFromFile(Ccc_Outlook_Model_Configuration $configuration)
    {
        $filePath = Mage::getBaseDir('var') . DS . 'export' . DS . $configuration->getId().'.txt';
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
}



