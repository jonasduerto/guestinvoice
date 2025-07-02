<?php

namespace WHMCS\Module\Addon\GuestInvoice;

use WHMCS\Database\Capsule;

class Helper {

    public static function getSettings() {
        $settings = [];
        $configs = Capsule::table('guest_invoice_setting')->get();
        
        foreach ($configs as $config) {
            $settings[$config->setting] = $config->value;
        }
        
        return $settings;
    }

    public static function generateGuestLink($invoiceId) {
        try {
            // Verificar si ya existe un enlace activo
            $existingLink = Capsule::table('guest_invoice')
                ->where('invoiceId', $invoiceId)
                ->where('status', 1)
                ->where('validtime', '>', time())
                ->first();
            
            if ($existingLink) {
                return $existingLink->authid;
            }
            
            // Obtener datos de la factura
            $invoice = Capsule::table('tblinvoices')->where('id', $invoiceId)->first();
            if (!$invoice) {
                return false;
            }
            
            // Generar nuevo token
            $token = bin2hex(random_bytes(32));
            $settings = self::getSettings();
            $validityHours = (int)$settings['link_validity_hours'];
            $validTime = time() + ($validityHours * 3600);
            
            Capsule::table('guest_invoice')->insert([
                'userId' => $_SESSION['adminid'] ?? 0,
                'clientId' => $invoice->userid,
                'invoiceId' => $invoiceId,
                'authid' => $token,
                'validtime' => $validTime,
                'referralLink' => $GLOBALS['CONFIG']['SystemURL'] . '/includes/guestinvoice.php?id=' . $invoiceId . '&token=' . $token,
                'status' => 1, // 1 = activo
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            
            // Log de creación
            self::logAction('link_created', $invoiceId, [
                'token' => $token,
                'validtime' => $validTime
            ]);
            
            return $token;
        } catch (Exception $e) {
            self::logAction('error', $invoiceId, ['message' => $e->getMessage()]);
            return false;
        }
    }

    public static function logAction($action, $invoiceId, $data = []) {
        $settings = self::getSettings();
        
        if (!empty($settings['module_logging']) && $settings['module_logging'] == '1') {
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