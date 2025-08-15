<?php
namespace GuestInvoice\Http;

use GuestInvoice\Services\LinkService;
use GuestInvoice\Services\SessionService;
use GuestInvoice\Services\SecurityService;
use GuestInvoice\GuestInvoiceUI;
use WHMCS\Database\Capsule;
use Carbon\Carbon;

class GuestRouteHandler
{
    private const MAX_ATTEMPTS = 5;               // intentos antes de bloquear IP
    private const BLOCK_TTL    = 900;             // 15 minutos de bloqueo
    private const TOKEN_FAILS  = 10;              // destruir token tras 10 fallos

    public static function handle(array $segments): void
    {
        [$invoiceId, $token] = [(int)($segments[1] ?? 0), $segments[2] ?? ''];

        if (!$invoiceId || !$token) {
            static::abort('Invalid link', 400);
        }

        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

        // 1) Comprobar si la IP está bloqueada
        $blockKey = "guest_block_$ip";
        if (static::isIpBlocked($ip)) {
            http_response_code(429);
            static::abort('Too many attempts. Please try again later.', 429);
        }

        // 2) Comprobar rate-limit por IP
        $attemptKey = "guest_attempt_$ip";
        $attempts   = (int)($_SESSION[$attemptKey] ?? 0);
        if ($attempts >= self::MAX_ATTEMPTS) {
            static::blockIp($ip);
            http_response_code(429);
            static::abort('Too many attempts. Please try again later.', 429);
        }

        try {
            $linkService = new LinkService();
            $link        = $linkService->validateGuestLink($invoiceId, $token);

            // Éxito: limpiar contadores
            unset($_SESSION[$attemptKey]);

            $linkService->recordAccess($link->id);
            $sessionService = new SessionService();
            $redirectUrl    = $sessionService->createGuestSession($link);

            header("Location: {$redirectUrl}");
            exit;

        } catch (\Throwable $e) {
            // Incrementar fallos
            $_SESSION[$attemptKey] = ++$attempts;

            // Contar fallos contra el token
            static::countTokenFailure($invoiceId, $token);

            // Log
            Capsule::table('guest_invoice_logs')->insert([
                'module'  => 'guest_invoice',
                'action'  => 'invalid_access_attempt',
                'data'    => json_encode([
                    'ip'        => $ip,
                    'invoiceId' => $invoiceId,
                    'token'     => $token,
                    'attempt'   => $attempts,
                ]),
                'created_at' => Carbon::now(),
            ]);

            static::abort('Invalid or expired link', 403);
        }
    }

    /* -----------------------------------------------------------------
     *  Helpers privados
     * ----------------------------------------------------------------- */

    private static function isIpBlocked(string $ip): bool
    {
        return Capsule::table('guest_invoice_blocked_ips')
                      ->where('ip', $ip)
                      ->where('blocked_until', '>', Carbon::now())
                      ->exists();
    }

    private static function blockIp(string $ip): void
    {
        Capsule::table('guest_invoice_blocked_ips')->updateOrInsert(
            ['ip' => $ip],
            ['blocked_until' => Carbon::now()->addSeconds(self::BLOCK_TTL)]
        );
    }

    private static function countTokenFailure(int $invoiceId, string $token): void
    {
        $table = 'guest_invoice';
        $row   = Capsule::table($table)
                        ->where('invoiceId', $invoiceId)
                        ->where('authid', $token)
                        ->first(['id', 'fail_count']);

        if (!$row) return;

        $newFails = ($row->fail_count ?? 0) + 1;
        Capsule::table($table)->where('id', $row->id)->update(['fail_count' => $newFails]);

        if ($newFails >= self::TOKEN_FAILS) {
            Capsule::table($table)->where('id', $row->id)->update(['status' => 0]);
        }
    }

    private static function abort(string $message, int $code): void
    {
        http_response_code($code);
        (new GuestInvoiceUI())->renderErrorPage($message, $code);
        exit;
    }
}