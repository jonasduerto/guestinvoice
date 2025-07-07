<?php
namespace GuestInvoice\Services;

use WHMCS\Config\Setting;
use WHMCS\Session;
use Exception;

class SessionService {
    /**
     * Session array key used by the GuestInvoice addon.
     */
    public const SESSION_KEY = 'guest_invoice';

    /**
     * Check if the current user is browsing under a guest-invoice temporary session.
     *
     * @return bool
     */
    public static function isGuestSession(): bool
    {
        return isset($_SESSION[self::SESSION_KEY]) || Session::get(self::SESSION_KEY) !== null;
    }

    /**
     * Destroy the temporary guest session and log the reason for auditing.
     *
     * @param string $reason Descriptive message of why the session was terminated
     */
    public static function terminateGuestSession(string $reason): void
    {
        if (isset($_SESSION[self::SESSION_KEY])) {
            unset($_SESSION[self::SESSION_KEY]);
        }

        \WHMCS\Session::delete(self::SESSION_KEY);

        $clientId = \WHMCS\Session::get('uid') ?? 'N/A';
        logActivity(sprintf(
            'Guest session terminated - Reason: %s - Client: %s, IP: %s',
            $reason,
            $clientId,
            $_SERVER['REMOTE_ADDR'] ?? ''
        ));
    }

    /**
     * Crea la sesión de invitado y devuelve la URL SSO de redirección.
     */
    public function createGuestSession(object $link): string {
        // Inicializar la sesión de invitado a través del SecurityService
        SecurityService::initGuestSession(
            $link->invoiceId,
            $link->authid,
            $link->clientId
        );
        // Guardar datos mínimos en la sesión propia del addon
        Session::set(self::SESSION_KEY, [
            'invoice_id' => $link->invoiceId,
            'client_id' => $link->clientId,
            'expires' => time() + 3600, // 1 hora
            'token' => $link->authid
        ]);
        // Generar el token SSO para redirigir al cliente directamente a la factura
        $response = self::generateInvoiceRedirect($link->invoiceId, $link->clientId);

        if (($response['result'] ?? 'error') !== 'success') {
            throw new Exception('Error al generar token de acceso');
        }

        return $response['redirect_url'];
    }

    /**
     * Fallback simple si se necesita construir manualmente la URL.
     */
    private static function generateInvoiceRedirect(int $invoiceId, int $clientId): array {
        return localAPI('CreateSsoToken', [
            'client_id'         => $clientId,
            'destination'       => 'sso:custom_redirect',
            'sso_redirect_path' => '/viewinvoice.php?id=' . $invoiceId,
            'responsetype'      => 'json',
        ]);
    }
}