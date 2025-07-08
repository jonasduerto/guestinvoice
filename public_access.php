<?php
// /public_access.php (en raíz de WHMCS)

use GuestInvoice\Services\LinkService;
use GuestInvoice\Services\SessionService;
// use GuestInvoice\Services\SecurityService;

// Inicialización básica
define('CLIENTAREA', true);
require_once __DIR__ . '/init.php';

// Cargar autoloader del módulo
require_once __DIR__ . '/modules/addons/guestinvoice/bootstrap.php';

// Inicializar servicios
$linkService = new LinkService();
$sessionService = new SessionService();

try {
    // Validar parámetros
    $invoiceId = (int) ($_GET['invoice_id'] ?? $_GET['id'] ?? 0);
    $token = $_GET['token'] ?? '';
    
    if (!$invoiceId || !$token) {
        throw new Exception('Parámetros inválidos');
    }

    // Validar enlace
    $link = $linkService->validateGuestLink($invoiceId, $token);
    
    // Crear sesión temporal y obtener la URL de redirección SSO
    $redirectUrl = $sessionService->createGuestSession($link);
    
    // Registrar acceso
    $linkService->recordAccess($link->id);
    
    // Redirección segura
    header("Location: {$redirectUrl}");
    exit;

} catch (Exception $e) {
    // Manejo de errores
    // $smarty = new Smarty();
    // $smarty->assign('error', $e->getMessage());
    // $smarty->display(__DIR__ . '/modules/addons/guestinvoice/templates/error.tpl');
    // exit;
    die($e->getMessage());
}