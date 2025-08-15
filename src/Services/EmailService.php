<?php

namespace GuestInvoice\Services;

use WHMCS\Database\Capsule;
use WHMCS\Mail\Template;
use WHMCS\User\Client;

class EmailService
{
    /**
     * Sends the guest invoice link to client email
     *
     * @param int $clientId
     * @param int $invoiceId
     * @param string $guestLink
     * @param string $expiryDate
     * @return array
     */
    public static function sendGuestLinkToClient(int $clientId, int $invoiceId, string $guestLink, string $expiryDate): array
    {
        try {
            $client = Client::find($clientId);
            if (!$client) {
                throw new \Exception('Client not found');
            }

            // Get invoice number
            $invoice = localAPI('GetInvoice', ['invoiceid' => $invoiceId]);
            if ($invoice['result'] !== 'success') {
                throw new \Exception('Could not retrieve invoice');
            }

            $mergeFields = [
                'client_name' => $client->fullName,
                'invoice_num' => $invoice['invoicenum'] ?: $invoiceId,
                'guest_link' => $guestLink,
                'expiry_date' => $expiryDate,
            ];

            $email = new Template();
            $email->setTemplate('Guest Invoice Link');
            
            // Create template if it doesn't exist
            if (!$email->templateExists()) {
                $emailTemplate = file_get_contents(
                    dirname(__DIR__) . '/templates/emails/guest_invoice_link.tpl'
                );
                
                $templateId = localAPI('AddEmailTemplate', [
                    'type' => 'product',
                    'name' => 'Guest Invoice Link',
                    'subject' => 'Payment Link for Invoice #' . $mergeFields['invoice_num'],
                    'message' => $emailTemplate,
                    'custom' => true,
                    'plaintext' => 0,
                ]);

                if ($templateId['result'] !== 'success') {
                    throw new \Exception('Failed to create email template');
                }
            }

            // Send the email
            $result = localAPI('SendEmail', [
                'messagename' => 'Guest Invoice Link',
                'id' => $clientId,
                'customtype' => 'product',
                'customsubject' => 'Payment Link for Invoice #' . $mergeFields['invoice_num'],
                'custommessage' => $emailTemplate ?? null,
                'customvars' => base64_encode(serialize($mergeFields)),
            ]);

            if ($result['result'] !== 'success') {
                throw new \Exception('Failed to send email: ' . ($result['message'] ?? 'Unknown error'));
            }

            return [
                'success' => true,
                'message' => 'Email sent successfully',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Sends test guest link to specified email
     *
     * @param string $testEmail
     * @param int $invoiceId
     * @param string $guestLink
     * @param string $expiryDate
     * @return array
     */
    public static function sendTestGuestLink(string $testEmail, int $invoiceId, string $guestLink, string $expiryDate): array
    {
        try {
            // Get invoice number
            $invoice = localAPI('GetInvoice', ['invoiceid' => $invoiceId]);
            if ($invoice['result'] !== 'success') {
                throw new \Exception('Could not retrieve invoice');
            }

            $mergeFields = [
                'client_name' => 'Test User',
                'invoice_num' => $invoice['invoicenum'] ?: $invoiceId,
                'guest_link' => $guestLink,
                'expiry_date' => $expiryDate,
            ];

            // Send email using WHMCS function
            $result = localAPI('SendEmail', [
                'messagename' => 'Guest Invoice Link',
                'customsubject' => '[TEST] Payment Link for Invoice #' . $mergeFields['invoice_num'],
                'custommessage' => file_get_contents(
                    dirname(__DIR__) . '/templates/emails/guest_invoice_link.tpl'
                ),
                'customtype' => 'product',
                'customvars' => base64_encode(serialize($mergeFields)),
                'customheaders' => base64_encode(serialize([
                    'To' => $testEmail,
                ])),
            ]);

            if ($result['result'] !== 'success') {
                throw new \Exception('Failed to send test email: ' . ($result['message'] ?? 'Unknown error'));
            }

            return [
                'success' => true,
                'message' => 'Test email sent successfully',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
