<?php

namespace WHMCS\Module\Addon\GuestInvoice\Admin;

use WHMCS\Database\Capsule;

class Controller {

    public function dashboard($vars) {
        $modulelink = $vars['modulelink'];
        
        $content = '<div class="alert alert-info">
            <h4><i class="fas fa-info-circle"></i> Guest Invoice Dashboard</h4>
            <p>Bienvenido al sistema de enlaces temporales para facturas.</p>
        </div>';
        
        $content .= '<div class="row">
            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Enlaces Activos</h3>
                    </div>
                    <div class="panel-body">
                        <h2>' . $this->getActiveLinksCount() . '</h2>
                        <p>Enlaces temporales activos</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Accesos Hoy</h3>
                    </div>
                    <div class="panel-body">
                        <h2>' . $this->getTodayAccessCount() . '</h2>
                        <p>Accesos registrados hoy</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Enlaces Expirados</h3>
                    </div>
                    <div class="panel-body">
                        <h2>' . $this->getExpiredLinksCount() . '</h2>
                        <p>Enlaces que han expirado</p>
                    </div>
                </div>
            </div>
        </div>';
        
        $content .= '<div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Acciones Rápidas</h3>
                    </div>
                    <div class="panel-body">
                        <a href="' . $modulelink . '&action=settings" class="btn btn-primary">
                            <i class="fas fa-cogs"></i> Configuración
                        </a>
                        <a href="' . $modulelink . '&action=logs" class="btn btn-info">
                            <i class="fas fa-list"></i> Ver Logs
                        </a>
                        <a href="' . $modulelink . '&action=cleanup" class="btn btn-warning">
                            <i class="fas fa-broom"></i> Limpiar Enlaces Expirados
                        </a>
                    </div>
                </div>
            </div>
        </div>';

        return $content;
    }

    public function settings($vars) {
        $modulelink = $vars['modulelink'];
        
        if ($_POST['save_settings']) {
            $this->saveSettings($_POST);
            return '<div class="alert alert-success">Configuración guardada correctamente.</div>';
        }
        
        $settings = $this->getSettings();
        
        $content = '<form method="post" action="' . $modulelink . '&action=settings">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Configuración del Módulo</h3>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label>Mostrar botón en facturas</label>
                        <select name="show_invoice_button" class="form-control">
                            <option value="enabled" ' . ($settings['show_invoice_button'] == 'enabled' ? 'selected' : '') . '>Habilitado</option>
                            <option value="disabled" ' . ($settings['show_invoice_button'] == 'disabled' ? 'selected' : '') . '>Deshabilitado</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Validez del enlace (horas)</label>
                        <input type="number" name="link_validity_hours" value="' . $settings['link_validity_hours'] . '" class="form-control" min="1" max="168">
                        <small class="form-text text-muted">1-168 horas (1 semana máximo)</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Mostrar reCAPTCHA</label>
                        <select name="show_recaptcha" class="form-control">
                            <option value="enabled" ' . ($settings['show_recaptcha'] == 'enabled' ? 'selected' : '') . '>Habilitado</option>
                            <option value="disabled" ' . ($settings['show_recaptcha'] == 'disabled' ? 'selected' : '') . '>Deshabilitado</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Activar logs</label>
                        <select name="module_logging" class="form-control">
                            <option value="1" ' . ($settings['module_logging'] == '1' ? 'selected' : '') . '>Habilitado</option>
                            <option value="0" ' . ($settings['module_logging'] == '0' ? 'selected' : '') . '>Deshabilitado</option>
                        </select>
                    </div>
                    
                    <button type="submit" name="save_settings" class="btn btn-primary">Guardar Configuración</button>
                </div>
            </div>
        </form>';

        return $content;
    }

    public function logs($vars) {
        $modulelink = $vars['modulelink'];
        $page = $_GET['page'] ?? 1;
        $limit = 50;
        $offset = ($page - 1) * $limit;
        
        $logs = Capsule::table('guest_invoice_logs')
            ->where('module', 'guest_invoice')
            ->orderBy('datetime', 'desc')
            ->limit($limit)
            ->offset($offset)
            ->get();
        
        $total = Capsule::table('guest_invoice_logs')
            ->where('module', 'guest_invoice')
            ->count();
        $totalPages = ceil($total / $limit);
        
        $content = '<div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Logs del Sistema</h3>
            </div>
            <div class="panel-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Acción</th>
                            <th>Fecha</th>
                            <th>Detalles</th>
                        </tr>
                    </thead>
                    <tbody>';
        
        foreach ($logs as $log) {
            $content .= '<tr>
                <td>' . $log->id . '</td>
                <td>' . htmlspecialchars($log->action) . '</td>
                <td>' . $log->datetime . '</td>
                <td>
                    <button class="btn btn-xs btn-info" onclick="showLogDetails(' . $log->id . ')">
                        Ver Detalles
                    </button>
                </td>
            </tr>';
        }
        
        $content .= '</tbody></table>';
        
        // Paginación
        if ($totalPages > 1) {
            $content .= '<nav><ul class="pagination">';
            for ($i = 1; $i <= $totalPages; $i++) {
                $active = $i == $page ? 'active' : '';
                $content .= '<li class="' . $active . '">
                    <a href="' . $modulelink . '&action=logs&page=' . $i . '">' . $i . '</a>
                </li>';
            }
            $content .= '</ul></nav>';
        }
        
        $content .= '</div></div>';

        return $content;
    }

    public function cleanup($vars) {
        try {
            $currentTime = time();
            
            // Marcar enlaces expirados
            $expiredCount = Capsule::table('guest_invoice')
                ->where('status', 1)
                ->where('validtime', '<', $currentTime)
                ->update(['status' => 2]);
            
            $content = '<div class="alert alert-success">
                <h4><i class="fas fa-check-circle"></i> Limpieza Completada</h4>
                <p>Se han marcado ' . $expiredCount . ' enlaces como expirados.</p>
            </div>';
            
            // Log de la acción
            $this->logAction('cleanup_expired_links', 0, ['expired_count' => $expiredCount]);
            
        } catch (Exception $e) {
            $content = '<div class="alert alert-danger">
                <h4><i class="fas fa-exclamation-triangle"></i> Error en la Limpieza</h4>
                <p>Error: ' . htmlspecialchars($e->getMessage()) . '</p>
            </div>';
        }
        
        // Redirigir al dashboard
        header('Location: ' . $vars['modulelink']);
        exit;
    }

    // Funciones auxiliares movidas desde guestinvoice.php

    private function getActiveLinksCount() {
        return Capsule::table('guest_invoice')
            ->where('status', 1)
            ->where('validtime', '>', time())
            ->count();
    }

    private function getTodayAccessCount() {
        return Capsule::table('guest_invoice_logs')
            ->where('module', 'guest_invoice')
            ->whereDate('datetime', date('Y-m-d'))
            ->count();
    }

    private function getExpiredLinksCount() {
        return Capsule::table('guest_invoice')
            ->where('status', 2)
            ->count();
    }

    private function getSettings() {
        $settings = [];
        $configs = Capsule::table('guest_invoice_setting')->get();
        
        foreach ($configs as $config) {
            $settings[$config->setting] = $config->value;
        }
        
        return $settings;
    }

    private function saveSettings($data) {
        $settings = [
            'show_invoice_button',
            'link_validity_hours',
            'show_recaptcha',
            'module_logging'
        ];
        
        foreach ($settings as $setting) {
            if (isset($data[$setting])) {
                Capsule::table('guest_invoice_setting')->updateOrInsert(
                    ['setting' => $setting],
                    ['value' => $data[$setting]]
                );
            }
        }
    }

    private function logAction($action, $invoiceId, $data = []) {
        $settings = $this->getSettings();
        
        if ($settings['module_logging'] == '1') {
            try {
                Capsule::table('guest_invoice_logs')->insert([
                    'module' => 'guest_invoice',
                    'action' => $action,
                    'request' => json_encode(['invoice_id' => $invoiceId]),
                    'response' => json_encode($data),
                    'datetime' => date('Y-m-d H:i:s')
                ]);
            } catch (Exception $e) {
                // Silenciar errores de logging
            }
        }
    }
}