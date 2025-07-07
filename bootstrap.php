<?php
// modules/addons/guestinvoice/bootstrap.php

/**
 * Bootstrap file for GuestInvoice module
 * 
 * Handles all access contexts:
 * - Admin area
 * - Client area
 * - AJAX requests
 * - Cron jobs
 * - API calls
 */

// 1. WHMCS Context Check - Versión mejorada
$isValidContext = (
    defined('WHMCS') || // Admin/Client area
    isset($_REQUEST['ajax_action']) || // AJAX calls
    // php_sapi_name() === 'cli' || // Cron jobs
    (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') // AJAX detection
);

if (!$isValidContext) {
    header('HTTP/1.1 403 Forbidden');
    die('Access denied. Invalid execution context.');
}

// 2. Define paths
define('GUESTINVOICE_ROOT', dirname(__DIR__));
define('GUESTINVOICE_LANG_DIR', GUESTINVOICE_ROOT . '/lang');
define('GUESTINVOICE_TEMPLATES_DIR', GUESTINVOICE_ROOT . '/templates');

// 3. Load WHMCS environment if not already loaded
if (!function_exists('logModuleCall')) {
    require_once GUESTINVOICE_ROOT . '../../../init.php';
    require_once GUESTINVOICE_ROOT . '../../../includes/functions.php';
}

// 4. Autoloader
require_once __DIR__ . '/autoload.php';

// 5. Verify core class exists
if (!class_exists('GuestInvoice\GuestInvoiceCore')) {
    throw new Exception('Failed to load GuestInvoice core classes. Check file permissions and paths.');
}