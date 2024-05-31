<?php
class Ccc_Outlook_Model_Observer
{
    public function readMail()
    {
        try {
            $emails = Mage::getModel('ccc_outlook/outlook')->getEmails();
            Mage::log($emails, null, 'outlook_emails.log'); // Log the emails array for debugging

            foreach ($emails as $email) {
                $from = isset($email['from']['emailAddress']['address']) ? $email['from']['emailAddress']['address'] : 'N/A';
                $toAddresses = isset($email['toRecipients']) ? array_map(function($recipient) {
                    return $recipient['emailAddress']['address'] ?? 'N/A';
                }, $email['toRecipients']) : [];
                $to = implode(', ', $toAddresses);
                $subject = $email['subject'] ?? 'No Subject';
                $body = isset($email['body']['content']) ? strip_tags($email['body']['content']) : 'No Content';
                $createdDateTime = isset($email['createdDateTime']) ? date('Y-m-d H:i:s', strtotime($email['createdDateTime'])) : 'N/A';

                $accessToken = Mage::getModel('ccc_outlook/outlook')->readTokenFromFile();
                $attachments = isset($email['hasAttachments']) && $email['hasAttachments'] ? $this->getAttachments($email['id'], $accessToken) : [];
                $attachmentNames = array_map(function($attachment) {
                    return $attachment['name'];
                }, $attachments);

                echo "From: $from\n";
                echo "To: $to\n";
                echo "Subject: $subject\n";
                echo "Body: $body\n";
                echo "Created Date: $createdDateTime\n";
                if (!empty($attachmentNames)) {
                    echo "Attachments: " . implode(', ', $attachmentNames) . "\n";
                }
                echo "------------------------------\n";
            }
        } catch (Exception $e) {
            Mage::log('Error reading emails: ' . $e->getMessage(), null, 'outlook_emails.log');
        }
    }

    private function getAttachments($messageId, $accessToken)
    {
        $url = "https://graph.microsoft.com/v1.0/me/messages/{$messageId}/attachments";
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

        $attachments = json_decode($response, true);
        return $attachments['value'] ?? [];
    }
}


