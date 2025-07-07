<?php
// modules/addons/guestinvoice/hooks.php
/**
 * Guest Invoice Module Hooks
 * 
 * Handles all WHMCS hook integrations for the Guest Invoice module
 * 
 * @package GuestInvoice
 * @version 2.0
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

require_once __DIR__ . '/bootstrap.php';

use WHMCS\Session;
use WHMCS\Config\Setting;
use GuestInvoice\GuestInvoiceUI;
use WHMCS\Authentication\Client;
use GuestInvoice\GuestInvoiceCore;
use WHMCS\Authentication\CurrentUser;
use GuestInvoice\Services\AjaxHandler;
use GuestInvoice\Services\SecurityService;
use GuestInvoice\Services\SessionService;

/**
 * Adds temporary link button to invoice details page
 * 
 * @hook ViewInvoiceDetailsPage
 * @param array $vars Invoice view parameters
 */
add_hook('ViewInvoiceDetailsPage', 1, function($vars) {
    $guestInvoice = GuestInvoiceUI::getInstance();
    // WHMCS handles sessions automatically - no session_start() needed
    $smarty = $guestInvoice->getSmartyInstance();

    // Build absolute asset path so files load correctly in admin area
    $assetPath = rtrim(Setting::getValue('SystemURL'), '/') . '/modules/addons/guestinvoice/assets/';

    $smarty->assign([
        'assetPath'   => $assetPath,
        'csrf_token'  => SecurityService::getCSRFToken(),
        'invoiceid'   => $vars['invoiceid'],
        '_lang'       => $guestInvoice->getLanguageStrings(),
        'systemUrl'   => Setting::getValue('SystemURL'),
        'clientid'    => $vars['clientid'] ?? $guestInvoice->getClientIdByInvoiceId($vars['invoiceid']),
        'ajaxEndpoint'=> $guestInvoice->getAjaxEndpoint()
    ]);

    return $smarty->display('guestlink_modal.tpl');
});

/**
 * Handles guest access control in client area
 * 
 * @hook ClientAreaPage
 * @param array $vars Client area parameters
 * @return array Modified client area parameters
 */
// add_hook('ClientAreaPage', 1, function($vars) {
    // $guestInvoice = GuestInvoiceUI::getInstance();
    // $session = (bool) SessionService::isGuestSession();
    // $security = (bool) SecurityService::validateSession();
    // echo "<pre>";
    // print_r([
    //     'SessionService::isGuestSession()' => $session,
    //     'SecurityService::validateSession()' => $security,
    //     '$_SESSION' => $_SESSION,
    //     'SESSION_KEY' => SecurityService::SESSION_KEY
    // ]);
    // if (!$session) {
    //     return $vars;
    // }

    // if (!$security) {
    //     SessionService::terminateGuestSession('Unauthorized access attempt');
    //     // header("Location: " . $guestInvoice->getSystemUrl() . "/logout.php?unauthorized=1");
    //     exit;



    // }
    
    // return $guestInvoice->decorateGuestInterface($vars);
// });


add_hook('ClientAreaPage', 1, function($vars) {
    // Depuración detallada
    $validateResult = SecurityService::validateSession();
    $isGuest = SessionService::isGuestSession();
    
    // Forzar salida inmediata
    // echo '<pre>';
    // echo 'DEBUG - validateSession type: ' . gettype($validateResult) . "\n";
    // echo 'DEBUG - validateSession value: ' . var_export($validateResult, true) . "\n";
    // echo 'DEBUG - isGuestSession: ' . ($isGuest ? 'true' : 'false') . "\n";
    // echo 'DEBUG - SCRIPT_NAME: ' . ($_SERVER['SCRIPT_NAME'] ?? 'not set') . "\n";
    // echo 'DEBUG - Current URL: ' . ($_SERVER['REQUEST_URI'] ?? 'not set') . "\n";
    // echo '</pre>';
    
    // Solo decorar la interfaz si la sesión es válida
    if ($validateResult === true) {
        $guestInvoice = GuestInvoiceUI::getInstance();
        return $guestInvoice->decorateGuestInterface($vars);
    }
    
    return $vars;
});



/**
 * Validates guest routes before module execution
 * 
 * @hook preModuleRoute
 * @param array $vars Route parameters
 */
// add_hook('preModuleRoute', 1, function($vars) {
//     $guestInvoice = GuestInvoiceUI::getInstance();
    
//     if (SessionService::isGuestSession() && !SecurityService::validateSession()) {
//         SessionService::terminateGuestSession('Invalid module route access');
//         header("Location: " . $guestInvoice->getSystemUrl() . "/logout.php?invalidroute=1");
//         exit;
//     }
// });