<?php
if (!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

class GuestInvoiceMailer {
    
    /**
     * Envía el enlace temporal por email al cliente
     */
    public static function sendGuestLinkEmail($invoiceId, $link, $expiresAt) {
        // Obtener datos de la factura y cliente
        $invoice = Capsule::table('tblinvoices')
            ->where('id', $invoiceId)
            ->first();
            
        if (!$invoice) {
            throw new Exception('Factura no encontrada');
        }
        
        $client = Capsule::table('tblclients')
            ->where('id', $invoice->userid)
            ->first();
            
        if (!$client) {
            throw new Exception('Cliente no encontrado');
        }
        
        // Configurar parámetros del email
        $emailParams = [
            'messagename' => 'Guest Invoice Link',
            'id' => $invoiceId,
            'to' => $client->email,
            'customvars' => [
                'guest_invoice_link' => $link,
                'guest_invoice_expires' => date('d/m/Y H:i', $expiresAt),
                'invoice_id' => $invoiceId,
                'client_name' => $client->firstname . ' ' . $client->lastname
            ]
        ];
        
        // Enviar email
        $result = localAPI('SendEmail', $emailParams);
        
        // Registrar envío
        Capsule::table('guest_invoice_logs')->insert([
            'module' => 'guest_invoice',
            'action' => 'email_sent',
            'invoice_id' => $invoiceId,
            'client_id' => $client->id,
            'datetime' => date('Y-m-d H:i:s'),
            'data' => json_encode([
                'email' => $client->email,
                'link' => $link,
                'expires_at' => $expiresAt
            ])
        ]);
        
        return $result;
    }
    
    /**
     * Envía notificación de nuevo acceso
     */
    public static function sendAccessNotification($invoiceId, $accessData) {
        // Obtener datos de la factura
        $invoice = Capsule::table('tblinvoices')
            ->where('id', $invoiceId)
            ->first();
            
        if (!$invoice) {
            throw new Exception('Factura no encontrada');
        }
        
        // Obtener email del administrador
        $adminEmail = Capsule::table('tblconfiguration')
            ->where('setting', 'SystemEmailsFromEmail')
            ->value('value');
            
        if (!$adminEmail) {
            $adminEmail = 'admin@' . parse_url(\WHMCS\Config\Setting::getValue('SystemURL'), PHP_URL_HOST);
        }
        
        // Configurar parámetros del email
        $emailParams = [
            'messagename' => 'Guest Invoice Access',
            'to' => $adminEmail,
            'customvars' => [
                'invoice_id' => $invoiceId,
                'access_time' => date('d/m/Y H:i'),
                'access_ip' => $accessData['ip'] ?? 'Desconocida',
                'access_user_agent' => $accessData['user_agent'] ?? 'Desconocido'
            ]
        ];
        
        // Enviar email
        return localAPI('SendEmail', $emailParams);
    }
}