<?php
if (!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

// Validar acceso
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
    die('Acceso no permitido');
}

// Procesar acciones AJAX
$action = isset($_POST['action']) ? $_POST['action'] : '';
$invoiceId = isset($_POST['invoice_id']) ? (int)$_POST['invoice_id'] : 0;

header('Content-Type: application/json');

try {
    switch ($action) {
        case 'generate_guest_link':
            $duration = isset($_POST['duration']) ? (int)$_POST['duration'] : 1;
            $sendEmail = isset($_POST['send_email']) ? (bool)$_POST['send_email'] : false;
            
            // Validar factura
            $invoice = Capsule::table('tblinvoices')->where('id', $invoiceId)->first();
            if (!$invoice) {
                throw new Exception('Factura no encontrada');
            }
            
            // Generar token seguro
            $token = bin2hex(random_bytes(32));
            $validTime = time() + ($duration * 3600);
            
            // Guardar enlace en la base de datos
            $linkId = Capsule::table('guest_invoice')->insertGetId([
                'invoiceId' => $invoiceId,
                'authid' => $token,
                'validtime' => $validTime,
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            
            // Construir URL del enlace
            $systemUrl = rtrim(\WHMCS\Config\Setting::getValue('SystemURL'), '/');
            $link = $systemUrl . '/guestinvoice.php?id=' . $invoiceId . '&token=' . $token;
            
            // Enviar email si está habilitado
            if ($sendEmail) {
                $this->sendGuestLinkEmail($invoiceId, $link, $validTime);
            }
            
            echo json_encode([
                'success' => true,
                'link' => $link,
                'expires_at' => date('Y-m-d H:i:s', $validTime)
            ]);
            break;
            
        default:
            throw new Exception('Acción no válida');
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}