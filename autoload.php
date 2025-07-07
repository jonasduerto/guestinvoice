<?php
// modules/addons/guestinvoice/autoload.php
if (!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

/**
 * Autoloader for Guest Invoice module
 * 
 * @package GuestInvoice
 * @version 2.0
 */
// modules/addons/guestinvoice/autoload.php
/*
/modules/addons/guestinvoice/
├── Services/               # This is the services directory
│   ├── SecurityService.php   # This is the security service file
│   ├── AjaxHandler.php       # This is the ajax handler file
│   └── LinkService.php       # This is the link service file
├── GuestInvoiceCore.php      # This is the core file
├── GuestInvoiceUI.php        # If you need UI-specific functionality
├── bootstrap.php             # This is the bootstrap file
├── guestinvoice.php          # This is the main file
├── autoload.php            # This is the autoloader file
├── hooks.php               # This is the hooks file
├── templates/              # This is the templates directory
├── lang/                   # This is the language directory
*/

spl_autoload_register(function ($className) {
    // Definimos el namespace base y la ruta
    $namespace = 'GuestInvoice\\';
    $baseDir = __DIR__ . '/';
    
    // Verificamos si la clase pertenece a nuestro namespace
    if (strpos($className, $namespace) !== 0) {
        return;
    }
    
    // Convertimos namespace a ruta de archivo
    $relativeClass = substr($className, strlen($namespace));
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    
    // Verificamos si el archivo existe y es legible
    if (is_readable($file)) {
        require $file;
    } else {
        // Opcional: Registrar error si el archivo no existe
        logModuleCall('guestinvoice', 'autoload', $className, 'Class file not found: ' . $file);
    }
});

// Cargar dependencias esenciales que no pueden ser autoloaded
require_once __DIR__ . '/../../../init.php';
require_once __DIR__ . '/../../../includes/functions.php';
