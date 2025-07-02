<?php

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use WHMCS\Database\Capsule;

/**
 * Hooks para el módulo Guest Invoice
 */

// Hook para agregar botón en facturas
add_hook('ClientAreaPageViewInvoice', 1, function($vars) {
    $settings = Helper::getSettings();
    
    if (isset($settings['show_invoice_button']) && $settings['show_invoice_button'] == 'enabled') {
        $invoiceId = $vars['invoiceid'];
        $token = Helper::generateGuestLink($invoiceId);
        
        if ($token) {
            $guestLink = $GLOBALS['CONFIG']['SystemURL'] . '/includes/guestinvoice.php?id=' . $invoiceId . '&token=' . $token;
            
            return [
                'guest_invoice_link' => $guestLink,
                'show_guest_button' => true
            ];
        }
    }
    return [];
});

// Hook para manejar peticiones AJAX
add_hook('ClientAreaPage', 1, function($vars) {
    if (isset($_POST['action']) && $_POST['action'] === 'generate_guest_link') {
        handleGenerateGuestLinkRequest();
    }
});

// Hook para agregar JavaScript y CSS
add_hook('ClientAreaHeadOutput', 1, function($vars) {
    if (isset($vars['show_guest_button']) && $vars['show_guest_button']) {
        return '<link rel="stylesheet" href="modules/addons/guestinvoice/assets/css/style.css">
<script src="modules/addons/guestinvoice/assets/js/guest-invoice.js"></script>';
    }
});

// Hook para agregar botón en la plantilla de factura
add_hook('ClientAreaPageViewInvoice', 2, function($vars) {
    if (isset($vars['show_guest_button']) && $vars['show_guest_button']) {
        return '
        <div class="guest-invoice-section">
            <button class="btn btn-primary generate-guest-link" data-invoice-id="' . $vars['invoice_id'] . '">
                <i class="fa fa-link"></i> {$_ADDONLANG.generate_temp_link_btn}
            </button>
        </div>
        ';
    }
});

// Funciones auxiliares
function getGuestInvoiceSettings() {
    $settings = [];
    try {
        $configs = Capsule::table('guest_invoice_setting')->get();
        foreach ($configs as $config) {
            $settings[$config->setting] = $config->value;
        }
    } catch (Exception $e) {
        // Configuración por defecto si hay error
        $settings = [
            'enable_invoice_btn' => 'enabled',
            'invoice_link_validity' => '24',
            'recaptchaEnable' => 'disabled',
            'viewInvoiceBtnEnable' => 'enabled'
        ];
    }
    return $settings;
}

function handleGenerateGuestLinkRequest() {
    // Seguridad básica AJAX
    if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
        http_response_code(403);
        die($_ADDONLANG.ajax_access_denied);
    }
    if (!isset($_SESSION['uid']) || empty($_SESSION['uid'])) {
        http_response_code(401);
        die(json_encode(['error' => $_ADDONLANG.ajax_unauthenticated]));
    }
    $invoiceId = isset($_POST['invoice_id']) ? (int)$_POST['invoice_id'] : 0;
    $duration = isset($_POST['duration']) ? (int)$_POST['duration'] : 24;
    $sendEmail = isset($_POST['send_email']) ? (bool)$_POST['send_email'] : false;
    if ($invoiceId <= 0) {
        http_response_code(400);
        die(json_encode(['error' => $_ADDONLANG.ajax_invalid_invoice_id]));
    }
    if (!in_array($duration, [1, 4, 12, 24])) {
        http_response_code(400);
        die(json_encode(['error' => $_ADDONLANG.ajax_invalid_duration]));
    }
    try {
        $invoice = Capsule::table('tblinvoices')
            ->where('id', $invoiceId)
            ->where('userid', $_SESSION['uid'])
            ->first();
        if (!$invoice) {
            http_response_code(404);
            die(json_encode(['error' => $_ADDONLANG.ajax_invoice_not_found]));
        }
        $clientId = $invoice->userid;
        $userId = $_SESSION['adminid'] ?? 0;
        $referralLink = createGuestInvoiceLink($userId, $clientId, $invoiceId, $duration);
        if (!$referralLink) {
            http_response_code(500);
            die(json_encode(['error' => $_ADDONLANG.ajax_link_generation_failed]));
        }
        if ($sendEmail) {
            sendGuestLinkEmail($invoice, $referralLink, $duration);
        }
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'link' => $referralLink,
            'expires_at' => date('Y-m-d H:i:s', time() + ($duration * 3600)),
            'duration' => $duration
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        logGuestInvoiceAction('error', $invoiceId, ['message' => $e->getMessage()]);
        echo json_encode(['error' => $_ADDONLANG.ajax_internal_server_error]);
    }
    exit;
}

function createGuestInvoiceLink($userId, $clientId, $invoiceId, $validHours = 24) {
    // 1. authid corto (8 chars)
    $authid = substr(bin2hex(random_bytes(8)), 0, 8);
    // 2. timestamp de expiración
    $validtime = time() + ($validHours * 3600);
    // 3. token seguro (base64, largo)
    $token = rtrim(strtr(base64_encode(random_bytes(48)), '+/', '-_'), '=');
    $token_url = urlencode($token);
    // 4. referralLink
    $domain = rtrim($GLOBALS['CONFIG']['SystemURL'], '/');
    $referralLink = "$domain/includes/guestinvoice.php?id={$invoiceId}&token={$token_url}";
    // 5. Guardar en la base
    Capsule::table('guest_invoice')->insert([
        'userId'      => $userId,
        'clientId'    => $clientId,
        'invoiceId'   => $invoiceId,
        'authid'      => $authid,
        'validtime'   => $validtime,
        'referralLink'=> $referralLink,
        'status'      => 1,
        'created_at'  => date('Y-m-d H:i:s'),
        'updated_at'  => date('Y-m-d H:i:s')
    ]);
    // 6. Log
    logGuestInvoiceAction('Add Guest Invoice', $invoiceId, [
        'userId'      => $userId,
        'clientId'    => $clientId,
        'invoiceId'   => $invoiceId,
        'authid'      => $authid,
        'validtime'   => $validtime,
        'referralLink'=> $referralLink,
        'status'      => 1,
        'created_at'  => date('Y-m-d H:i:s'),
        'updated_at'  => date('Y-m-d H:i:s')
    ]);
    return $referralLink;
}

function sendGuestLinkEmail($invoice, $referralLink, $duration) {
    try {
        $client = Capsule::table('tblclients')->where('id', $invoice->userid)->first();
        if (!$client) return false;
        $subject = $_ADDONLANG.email_subject_prefix . $invoice->id;
        $message = "<html><body><h2>{$_ADDONLANG.email_title}</h2><p>{$_ADDONLANG.email_greeting} {$client->firstname},</p><p>{$_ADDONLANG.email_body_1} #{$invoice->id}.</p><p><strong>{$_ADDONLANG.email_link_label}</strong> <a href='{$referralLink}'>{$referralLink}</a></p><p><strong>{$_ADDONLANG.email_valid_for_label}</strong> {$duration} {$_ADDONLANG.email_hours_label}</p><p><strong>{$_ADDONLANG.email_expires_label}</strong> " . date('Y-m-d H:i:s', time() + ($duration * 3600)) . "</p><br><p>{$_ADDONLANG.email_footer_1}</p><p>{$_ADDONLANG.email_footer_2}<br>{$_ADDONLANG.email_footer_3}</p></body></html>";
        $command = 'SendEmail';
        $postData = [
            'messagename' => 'Custom',
            'id' => $invoice->id,
            'customsubject' => $subject,
            'custommessage' => $message,
            'type' => 'product',
        ];
        $results = localAPI($command, $postData);
        logGuestInvoiceAction('email_sent', $invoice->id, [
            'client_email' => $client->email,
            'duration' => $duration
        ]);
        return $results['result'] == 'success';
    } catch (Exception $e) {
        logGuestInvoiceAction('email_error', $invoice->id, ['error' => $e->getMessage()]);
        return false;
    }
}

// Hook to destroy guest session after payment
add_hook('ClientAreaPageViewInvoice', 100, function($vars) {
    // If the invoice is paid and it's a guest access, destroy the session
    if (
        isset($_SESSION['guest_invoice_access']) && $_SESSION['guest_invoice_access'] &&
        isset($vars['status']) && strtolower($vars['status']) === 'paid'
    ) {
        // Unset all of the session variables.
        $_SESSION = array();

        // If it's desired to kill the session, also delete the session cookie.
        // Note: This will destroy the session, and not just the session data!
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Finally, destroy the session.
        session_destroy();

        // Optional: redirect to a confirmation or logout page
        // This ensures the user is fully logged out and sees a confirmation.
        $systemUrl = \WHMCS\App::getSystemURL();
        header("Location: " . $systemUrl . "logout.php?guestinvoice=1");
        exit;
    }
});

function logGuestInvoiceAction($action, $invoiceId, $data = []) {
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