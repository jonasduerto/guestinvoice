<?php

namespace GuestInvoice\Lib\Admin;

use GuestInvoice\Lib\Helper;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

/**
 * Controlador principal del área de administración
 */
class Index {
    
    /**
     * @var array Configuración del módulo
     */
    protected $config = [];
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->loadConfig();
    }
    
    /**
     * Carga la configuración del módulo
     */
    protected function loadConfig() {
        $this->config = [
            'name' => 'Guest Invoice',
            'version' => '1.0',
            'author' => 'Whmcs Global Services',
            'description' => 'Allows clients to view invoices without logging in',
            'language' => 'english'
        ];
    }
    
    /**
     * Maneja las solicitudes entrantes
     * @param array $vars Variables de WHMCS
     */
    public function handleRequest($vars) {
        $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'index';
        $controller = new Controller();
        
        if (method_exists($controller, $action)) {
            return $controller->$action($vars);
        }
        
        // Acción por defecto
        return $controller->index($vars);
    }
}