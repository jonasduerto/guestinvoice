<?php

namespace GuestInvoice\Services;

use WHMCS\Session; // Asegúrate de que esté importado
use WHMCS\Config\Setting;
use WHMCS\Database\Capsule;
use Exception;
use Carbon\Carbon;

class SecurityService
{
    const SESSION_KEY = 'guest_invoice';
    const TABLE_NAME = 'guest_invoice';
    const SESSION_LIFETIME = 3600; // 1 hour
    const CSRF_TOKEN_LIFETIME = 3600; // 1 hour
    
    // Guest links must not access general Client Area pages. Only the
    // invoice view itself is permitted. Payment gateway callbacks do not run
    // in the user's browser so they do not require guest access.
    protected static $allowedScripts = [
        'viewinvoice.php', // invoice view
        'index.php',       // pretty URL routes handled by WHMCS router (e.g. /invoice/{id}/pay)
    ];
    
    // Restrict actions query-param on viewinvoice.php
    protected static $allowedActions = [
        '',      // default invoice view
        'pay',   // initiate payment
    ];

    /**
     * Initialize a guest session with enhanced security
     */
    public static function initGuestSession(int $invoiceId, string $token, int $clientId): void
    {
        if (!self::validateToken($token, $invoiceId)) {
            throw new Exception('Invalid format'); //Hijacking attempt
        }

        session_regenerate_id(true);
        
        $sessionData = [
            'invoice_id' => $invoiceId,
            'token' => $token,
            'client_id' => $clientId,
            'expires' => time() + self::SESSION_LIFETIME,
            'start_time' => time(),
            'security_token' => self::createToken(),
            'ip_address' => self::getClientIp(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
        ];
        
        $_SESSION[self::SESSION_KEY] = $sessionData;
        self::simulateClientLogin($clientId);
        
        self::logSessionStart($invoiceId, $clientId);
    }

    /**
     * Validate current session and access permissions
     */
    public static function validateSession(): bool
    {
        try {
            // 1. Verificar sesión de invitado
            if (!SessionService::isGuestSession()) {
                // print_r('DEBUG: No hay sesión de invitado');
                return false; // No hay sesión de invitado
            }

            // 2. Obtener datos de sesión (de $_SESSION o WHMCS\Session)
            $session = $_SESSION[self::SESSION_KEY] ?? Session::get(self::SESSION_KEY);
            if (empty($session) || !is_array($session)) {
                self::terminateSession('Invalid session data');
                // print_r('DEBUG: Invalid session data');
                return false; // Datos de sesión inválidos
            }
            
            // 3. Validar expiración
            if (time() > (int)($session['expires'] ?? 0)) {
                self::terminateSession('Session expired');
                // print_r('DEBUG: Session expired');
                return false; // Sesión expirada
            }

            // 4. Validar acceso a la página actual
            $currentFile = basename($_SERVER['SCRIPT_NAME'] ?? '');
            $allowedScripts = ['viewinvoice.php', 'index.php'];
            
            if (!in_array($currentFile, $allowedScripts, true)) {
                self::terminateSession('Intento de acceso a página no permitida: ' . $currentFile);
                // Redirigir a logout para limpiar cualquier sesión de WHMCS
                if ($currentFile !== 'logout.php') {
                    self::redirectToLogout('security=1');
                }
                return false;
            }

            // 5. Validar ID de factura si es viewinvoice.php
            if ($currentFile === 'viewinvoice.php') {
                $invoiceId = (int)($_GET['id'] ?? 0);
                // print_r('DEBUG: Invoice ID: ' . $invoiceId);
                // print_r('DEBUG: Session Invoice ID: ' . $session['invoice_id']);
                return (bool)($invoiceId === (int)($session['invoice_id'] ?? 0));
            }

            // 6. Validar IP (solo si la IP está configurada en la sesión)
            if (isset($session['ip_address']) && self::getClientIp() !== $session['ip_address']) {
                self::terminateSession('IP address changed');
                // print_r('DEBUG: IP address changed');
                return false; // IP no coincide
            }

            // Si llegamos aquí, todas las validaciones pasaron
            return true;
            
        } catch (\Exception $e) {
            // print_r('DEBUG: GuestInvoice Error in validateSession: ' . $e->getMessage());
            return false; // Error inesperado
        }
    }

    /**
     * @deprecated Use validateSession() instead
     */
    public static function isAccessAllowed(): bool
    {
        return false;
        // if (!SessionService::isGuestSession()) {
        //     return false;
        // }

        // $currentFile = basename($_SERVER['SCRIPT_NAME']);
        // $session = $_SESSION[self::SESSION_KEY];
        
        // if (!in_array($currentFile, self::$allowedScripts)) {
        //     return false;
        // }

        // // /viewinvoice.php?id={id}&action=pay etc.
        // if ($currentFile === 'viewinvoice.php') {
        //     $invoiceId = (int)($_GET['id'] ?? 0);
        //     $action    = $_GET['action'] ?? '';
        //     return $invoiceId === $session['invoice_id'] &&
        //            in_array($action, self::$allowedActions, true);
        // }

        // // Pretty route handled by WHMCS: index.php?rp=/invoice/{id}/pay
        // if ($currentFile === 'index.php') {
        //     $rp        = $_GET['rp'] ?? '';
        //     $expected  = '/invoice/' . $session['invoice_id'] . '/pay';
        //     return $rp === $expected;
        // }

        // return false;
    }

    /**
     * Terminate session with logging
     */
    public static function terminateSession(string $reason): void
    {
        if (!SessionService::isGuestSession()) {
            return;
        }

        $session = $_SESSION[self::SESSION_KEY];
        $logData = [
            'client_id' => $session['client_id'] ?? 0,
            'invoice_id' => $session['invoice_id'] ?? 0,
            'ip_address' => self::getClientIp(),
            'reason' => $reason
        ];

        ActivityHistoryService::logActivity('session_terminated', $logData);
        Session::destroy();
    }

    /**
     * Generate cryptographically secure token
     */
    public static function createToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * Validate token format
     */
    public static function validateToken(string $token, int $invoiceId): ?object {
        $now = time();
        return Capsule::table('guest_invoice')
            ->where('authid', $token)
            ->where('invoiceId', $invoiceId)
            ->where('status', 1)
            ->where('validtime', '>', $now)
            ->first();
    }

    // For Access Tokens (URLs)
    public static function generateAccessToken(): string {
        return bin2hex(random_bytes(16)); // 32 chars for URLs
    }

    public static function validateAccessToken(string $token): bool {
        return Capsule::table(self::TABLE_NAME)
            ->where('authid', $token)
            ->where('status', 1)
            ->where('validtime', '>', time())
            ->exists();
    }

    // Para CSRF Tokens (Forms)
    public static function getCSRFToken(): string
    {
        $token = Session::get(self::SESSION_KEY);
        if (empty($token)) {
            $token = bin2hex(random_bytes(32)); // Genera un token único
            Session::set(self::SESSION_KEY, $token);
            // Mantener compatibilidad con código legacy que accede a \\$_SESSION directamente
            $_SESSION[self::SESSION_KEY] = $token;
        }
        return $token;
    }

    public static function validateCSRFToken(string $token): bool
    {
        $sessionToken = Session::get(self::SESSION_KEY) ?? ($_SESSION[self::SESSION_KEY] ?? null);
        return !empty($sessionToken) && hash_equals($sessionToken, $token);
    }

    /**
     * Get active guest session data
     */
    public static function getSessionData(): ?array
    {
        return $_SESSION[self::SESSION_KEY] ?? null;
    }

    /**
     * Get remaining session time in minutes
     */
    public static function getRemainingSessionTime(): int
    {
        if (!SessionService::isGuestSession()) {
            return 0;
        }
        
        $remaining = $_SESSION[self::SESSION_KEY]['expires'] - time();
        return max(0, (int)ceil($remaining / 60));
    }

    /*****************************************************************
     * Database Operations
     ****************************************************************/

    /**
     * Get valid link by token with cache validation
     */
    public static function getValidLink(string $token, int $invoiceId): ?object
    {
        if (!self::validateToken($token, $invoiceId)) {
            return null;
        }

        return Capsule::table(self::TABLE_NAME)
            ->where('authid', $token)
            ->where('status', 1)
            ->where('validtime', '>', time())
            ->first();
    }

    /**
     * Record link access in database
     */
    public static function recordAccess(int $linkId): void
    {
        Capsule::table(self::TABLE_NAME)
            ->where('id', $linkId)
            ->increment('access_count', 1, [
                'updated_at' => Carbon::now()
            ]);
    }

    /**
     * Disable token and log the action
     */
    public static function disableToken(string $token): bool
    {
        $result = Capsule::table(self::TABLE_NAME)
            ->where('authid', $token)
            ->update([
                'status' => 0,
                'updated_at' => Carbon::now()
            ]);

        if ($result) {
            ActivityHistoryService::logActivity('token_disabled', ['token' => $token]);
            return true;
        }

        return false;
    }

    /*****************************************************************
     * Private Helper Methods
     ****************************************************************/

    private static function simulateClientLogin(int $clientId): void
    {
        // Retrieve only public client details. We purposefully avoid fetching the
        // password hash to eliminate any possibility of establishing a full
        // authenticated session.
        $client = Capsule::table('tblclients')
            ->where('id', $clientId)
            ->first(['firstname', 'lastname']);

        if ($client) {
            // Expose a display label for UI purposes without granting WHMCS login.
            $_SESSION['guest_client_name'] = "{$client->firstname} {$client->lastname}";
        }
    }

    /**
     * Redirect to the WHMCS logout page and stop further execution.
     * Extra query parameters may be appended for user-visible messaging.
     */
    private static function redirectToLogout(string $params = ''): void
    {
        $url = rtrim(Setting::getValue('SystemURL'), '/') . '/logout.php';
        if ($params) {
            $url .= '?' . $params;
        }

        // Evita redirecciones múltiples si ya estamos en la página de logout
        if (basename($_SERVER['SCRIPT_NAME']) !== 'logout.php') {
            header("Location: {$url}");
            exit;
        }
    }

    private static function getClientIp(): string
    {
        return $_SERVER['HTTP_CLIENT_IP'] ?? 
               $_SERVER['HTTP_X_FORWARDED_FOR'] ?? 
               $_SERVER['REMOTE_ADDR'] ?? '';
    }

    private static function logSessionStart(int $invoiceId, int $clientId): void
    {
        ActivityHistoryService::logActivity('session_started', [
            'invoice_id' => $invoiceId,
            'client_id' => $clientId,
            'ip_address' => self::getClientIp()
        ]);
    }
}