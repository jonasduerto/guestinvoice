<?php

namespace WHMCS\Module\Addon\GuestInvoice\Admin;

require_once __DIR__ . '/Controller.php';

class AdminDispatcher {

    public function dispatch($action, $vars) {
        $controller = new Controller();

        if (is_callable([$controller, $action])) {
            return $controller->$action($vars);
        } else {
            return '<p>Invalid action requested.</p>';
        }
    }
}