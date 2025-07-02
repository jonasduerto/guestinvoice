<?php

// --- CONFIGURACIÓN Y DEPENDENCIAS ---
if (!defined('WHMCS')) {
    define('WHMCS', true);
}

require_once __DIR__ . '/../init.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/gatewayfunctions.php';
require_once __DIR__ . '/../includes/invoicefunctions.php';

use WHMCS\Database\Capsule;
use WHMCS\ClientArea;
use WHMCS\Exception\Fatal;
use WHMCS\Session;
use WHMCS\App;

// --- FUNCIONES AUXILIARES ---
function getGuestInvoiceSecret() {
    // Busca el secreto HMAC en la tabla de configuración
    $row = Capsule::table('guest_invoice_setting')->where('setting', 'hmac_secret')->first();
    if ($row && !empty($row->value)) {
        return $row->value;
    }
    
    // Si no existe, generar uno nuevo y guardarlo
    $secret = bin2hex(random_bytes(32));
    Capsule::table('guest_invoice_setting')->insert([
        'setting' => 'hmac_secret',
        'value' => $secret
    ]);
    
    return $secret;
}

function validateToken($token, $invoiceId) {
    $secret = getGuestInvoiceSecret();
    
    // Verificar formato del token (debe ser alfanumérico y tener longitud válida)
    if (!preg_match('/^[a-f0-9]{64}$/', $token)) {
        return false;
    }
    
    // Buscar el enlace en la base de datos
    $link = Capsule::table('guest_invoice')
        ->where('authid', $token)
        ->where('invoiceId', $invoiceId)
        ->where('status', 1) // 1 = activo
        ->first();
    
    if (!$link) {
        return false;
    }
    
    // Verificar si ha expirado
    $currentTime = time();
    if ($currentTime > $link->validtime) {
        // Marcar como expirado
        Capsule::table('guest_invoice')
            ->where('id', $link->id)
            ->update(['status' => 2]); // 2 = expirado
        return false;
    }
    
    return $link;
}

function logAccess($action, $invoiceId, $request = null, $response = null) {
    try {
        Capsule::table('guest_invoice_logs')->insert([
            'module' => 'guest_invoice',
            'action' => $action,
            'request' => $request ? json_encode($request) : null,
            'response' => $response ? json_encode($response) : null,
            'datetime' => date('Y-m-d H:i:s')
        ]);
    } catch (Exception $e) {
        // Silenciar errores de logging
    }
}

function generateSecureToken($invoiceId, $clientId, $userId) {
    $secret = getGuestInvoiceSecret();
    $timestamp = time();
    $data = $invoiceId . '|' . $clientId . '|' . $timestamp;
    return hash_hmac('sha256', $data, $secret);
}

function logGuestInvoiceAccess($action, $invoiceId, $data = []) {
    try {
        Capsule::table('guest_invoice_logs')->insert([
            'datetime' => date('Y-m-d H:i:s'),
            'module'   => 'Guest Invoice',
            'action'   => $action,
            'request'  => json_encode(['invoice_id' => $invoiceId]),
            'response' => json_encode($data)
        ]);
    } catch (Exception $e) {
        // Silenciar errores de logging
    }
}

// --- VALIDACIÓN DE ENTRADA ---
$invoiceId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$token = isset($_GET['token']) ? trim($_GET['token']) : '';

function display_error($message) {
    $ca = new ClientArea();
    $ca->setPageTitle('Error');
    $ca->addToBody(sprintf('<div class="alert alert-danger">%s</div>', $message));
    $ca->setTemplate('error');
    $ca->output();
    exit;
}

// Validar parámetros de entrada
if ($invoiceId <= 0 || empty($token)) {
    logGuestInvoiceAccess('invalid_parameters', 0, ['error' => 'Parámetros inválidos', 'get' => $_GET]);
    display_error('Los parámetros proporcionados son inválidos.');
}

// Validar formato del token
if (!preg_match('/^[a-f0-9]{64}$/', $token)) {
    logGuestInvoiceAccess('invalid_token_format', $invoiceId, ['error' => 'Formato de token inválido']);
    display_error('El token proporcionado es inválido.');
}

// --- BUSCAR ENLACE EN LA BASE DE DATOS ---
$link = Capsule::table('guest_invoice')
    ->where('invoiceId', $invoiceId)
    ->where('authid', $token)
    ->where('status', 1)
    ->first();

if (!$link) {
    logGuestInvoiceAccess('token_not_found', $invoiceId, ['error' => 'Token no encontrado o inactivo', 'token' => $token]);
    display_error('El enlace de la factura es inválido o no se ha encontrado.');
}

// --- VALIDAR EXPIRACIÓN ---
if (time() > $link->validtime) {
    // Marcar como expirado
    Capsule::table('guest_invoice')->where('id', $link->id)->update(['status' => 2]);
    logGuestInvoiceAccess('token_expired', $invoiceId, ['error' => 'Token expirado', 'validtime' => $link->validtime]);

    // Destruir cualquier sesión existente si el enlace ha expirado
    if (isset($_SESSION['guest_invoice_access'])) {
        $_SESSION = array();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }

    display_error('El enlace de la factura ha expirado.');
}

// --- OBTENER DATOS DE LA FACTURA ---
try {
    $invoice = Capsule::table('tblinvoices')
        ->where('id', $invoiceId)
        ->first();
    
    if (!$invoice) {
        logGuestInvoiceAccess('invoice_not_found', $invoiceId, ['error' => 'Factura no encontrada']);
        display_error('No se ha podido encontrar la factura solicitada.');
    }
    
    $client = Capsule::table('tblclients')
        ->where('id', $invoice->userid)
        ->first();
    
    if (!$client) {
        logGuestInvoiceAccess('client_not_found', $invoiceId, ['error' => 'Cliente no encontrado']);
        display_error('No se ha podido encontrar el cliente asociado a la factura.');
    }
    
} catch (Exception $e) {
    logGuestInvoiceAccess('database_error', $invoiceId, ['error' => $e->getMessage()]);
    display_error('Ha ocurrido un error al procesar su solicitud. Por favor, inténtelo de nuevo más tarde.');
}

// --- SIMULATE CLIENT LOGIN AND REDIRECT ---

// Log in the user associated with the invoice
// This creates the necessary session to view the invoice
Session::set('uid', $client->id);
// For simplicity, we assume the main account holder.
// If contacts need to be supported, this logic would need expansion.
Session::set('cid', $client->id); 
Session::set('guest_invoice_access', true); 


// --- ACTUALIZAR updated_at DEL ENLACE ---
try {
    Capsule::table('guest_invoice')->where('id', $link->id)->update([
        'updated_at' => date('Y-m-d H:i:s'),
        'access_count' => Capsule::raw('access_count + 1')
    ]);
    logGuestInvoiceAccess('guest_access_success', $invoiceId, ['client_id' => $client->id]);
} catch (Exception $e) {
    // Log error but proceed with redirect
    logGuestInvoiceAccess('update_link_error', $invoiceId, ['error' => $e->getMessage()]);
}

// Redirect to the standard invoice view page
$systemUrl = App::getSystemURL();
header("Location: " . $systemUrl . "viewinvoice.php?id=" . $invoiceId);
exit;

