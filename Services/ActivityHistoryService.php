<?php
namespace GuestInvoice\Services;

use WHMCS\Database\Capsule;
use Carbon\Carbon;

class ActivityHistoryService
{
    const LOGS_TABLE = 'guest_invoice_logs';

    /**
     * Obtiene datos paginados de logs
     */
    public function getActivityHistoryData(): array
    {
        // Get the dashboard data to include statistics
        $dashboardService = new DashboardService();
        $dashboardData = $dashboardService->getDashboardData();
        
        // Get the activity logs
        $logsQuery = Capsule::table(self::LOGS_TABLE)->orderByDesc('id');
        
        // Get all guest invoice links with client and invoice information
        $links = Capsule::table('guest_invoice as gi')
            ->leftJoin('tblclients as c', 'gi.clientId', '=', 'c.id')
            ->leftJoin('tblinvoices as i', 'gi.invoiceId', '=', 'i.id')
            ->select(
                'gi.*',
                'c.firstname as client_firstname',
                'c.lastname as client_lastname',
                'c.companyname as client_company',
                'i.total as invoice_total',
                'i.status as invoice_status',
                'i.duedate as invoice_duedate'
            )
            ->orderBy('gi.created_at', 'desc')
            ->get();
        
        // Format the links data
        $formattedLinks = [];
        foreach ($links as $link) {
            $formattedLinks[] = [
                'id' => $link->id,
                'invoice_id' => $link->invoiceId,
                'client_name' => trim(($link->client_firstname ?? '') . ' ' . ($link->client_lastname ?? '')),
                'company_name' => $link->client_company ?? '',
                'created_at' => $link->created_at,
                'expires_at' => date('Y-m-d H:i:s', $link->validtime),
                'is_active' => $link->status == 1 && $link->validtime > time(),
                'access_count' => $link->access_count,
                'invoice_total' => $link->invoice_total,
                'invoice_status' => $link->invoice_status,
                'invoice_duedate' => $link->invoice_duedate,
                'referral_link' => $link->referralLink
            ];
        }
        
        return [
            'logs' => $logsQuery->get()->toArray(),
            'guest_links' => $formattedLinks,
            // Include the dashboard statistics
            'activeLinks' => $dashboardData['activeLinks'] ?? 0,
            'totalAccesses' => $dashboardData['totalAccesses'] ?? 0,
            'expiredLinks' => $dashboardData['expiredLinks'] ?? 0
        ];
    }

    /**
     * Registra una actividad en el log
     */
    public static function logActivity(string $action, array $data = []): void
    {
        Capsule::table(self::LOGS_TABLE)->insert([
            'module' => 'guest_invoice',
            'action' => $action,
            'data' => !empty($data) ? json_encode($data) : null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }

    /**
     * Clear all activity logs
     */
    public static function clearActivityLogs(): bool
    {
        try {
            Capsule::table(self::LOGS_TABLE)->truncate();
            return true;
        } catch (\Exception $e) {
            self::logActivity('Failed to clear Guest Invoice activity logs: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Clear access counters in the guest_invoice table
     */
    public static function clearAccessCounters(): bool
    {
        try {
            Capsule::table('guest_invoice')
                ->update(['access_count' => 0]);
            
            self::logActivity('Cleared all access counters');
            return true;
        } catch (\Exception $e) {
            self::logActivity('Failed to clear access counters: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Clear all guest invoice links
     */
    public static function clearAllGuestLinks(): bool
    {
        try {
            $deleted = Capsule::table('guest_invoice')->delete();
            self::logActivity("Cleared all guest invoice links ($deleted links removed)");
            return true;
        } catch (\Exception $e) {
            self::logActivity('Failed to clear guest invoice links: ' . $e->getMessage());
            return false;
        }
    }
}