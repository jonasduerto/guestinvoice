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

header('Content-Type: application/json');

try {
    switch ($action) {
        case 'get_logs':
            $start = isset($_POST['start']) ? (int)$_POST['start'] : 0;
            $length = isset($_POST['length']) ? (int)$_POST['length'] : 10;
            $search = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
            
            // Consulta base
            $query = Capsule::table('guest_invoice_logs');
            
            // Aplicar búsqueda
            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('action', 'like', '%'.$search.'%')
                      ->orWhere('module', 'like', '%'.$search.'%')
                      ->orWhere('datetime', 'like', '%'.$search.'%');
                });
            }
            
            // Obtener total de registros
            $totalRecords = $query->count();
            
            // Aplicar paginación
            $logs = $query->orderBy('datetime', 'desc')
                         ->offset($start)
                         ->limit($length)
                         ->get();
            
            // Formatear respuesta
            $response = [
                'draw' => isset($_POST['draw']) ? (int)$_POST['draw'] : 0,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => []
            ];
            
            foreach ($logs as $log) {
                $response['data'][] = [
                    'datetime' => $log->datetime,
                    'module' => $log->module,
                    'action' => $log->action,
                    'details' => json_decode($log->request, true),
                    'ip' => $log->ip ?? 'N/A'
                ];
            }
            
            echo json_encode($response);
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