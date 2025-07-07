<?php
/**
 * Guest Invoice Module - Main Entry Point
 * 
 * @package GuestInvoice
 * @version 2.0
 */

require_once __DIR__ . '/bootstrap.php';

use GuestInvoice\GuestInvoiceUI;
use GuestInvoice\GuestInvoiceCore;
use GuestInvoice\Services\AjaxHandler;


// ⚡ Handle AJAX requests first (before security check)
if (isset($_REQUEST['ajax_action'])) {
    (new AjaxHandler(GuestInvoiceCore::getInstance()))->process();
    exit;
}

// 🛡️ Direct access security check (only for non-AJAX requests)
if (!defined('WHMCS')) {
    die("This file cannot be accessed directly");
}

// 🎛️ WHMCS Standard Module Functions
function guestinvoice_config() {
    return GuestInvoiceCore::getInstance()->config();
}

function guestinvoice_activate() {
    return GuestInvoiceCore::getInstance()->activate();
}

function guestinvoice_deactivate() {
    return GuestInvoiceCore::getInstance()->deactivate();
}

function guestinvoice_output($vars) {
    $guestInvoice = GuestInvoiceUI::getInstance();
    return $guestInvoice->outputViewRender($vars);
}