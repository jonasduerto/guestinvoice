<?php
namespace GuestInvoice\Services;

use WHMCS\Database\Capsule;

class DashboardService
{
    const LINKS_TABLE = 'guest_invoice';

    /**
     * Obtiene datos para el dashboard
     */
    public function getDashboardData(): array
    {
        $now = time();
        $links = Capsule::table(self::LINKS_TABLE)->get();
        
        $totalLinks = $links->count();
        $activeLinks = 0;
        $totalAccesses = 0;
        
        foreach ($links as $link) {
            $totalAccesses += (int)$link->access_count;
            
            // Check if link is active (status is 1 and validtime is in the future)
            if ($link->status == 1 && $link->validtime > $now) {
                $activeLinks++;
            }
        }
        
        return [
            'totalLinks' => $totalLinks,
            'activeLinks' => $activeLinks,
            'expiredLinks' => $totalLinks - $activeLinks,
            'totalAccesses' => $totalAccesses,
            'recentActivityHistory' => $this->getRecentActivityHistory(),
        ];
    }

    /**
     * Genera el breadcrumb para navegación
     */
    public function getBreadcrumb(string $action, array $lang): string
    {
        $labels = [
            'dashboard' => $lang['nav_dashboard'] ?? 'Dashboard',
            'settings' => $lang['nav_settings'] ?? 'Settings',
            'activityHistory' => $lang['nav_activityHistory'] ?? 'Activity History',
            'generate' => $lang['nav_generate'] ?? 'Generate Link',
        ];

        $label = $labels[$action] ?? ucfirst($action);
        return '<span class="gi-breadcrumb-item">' . htmlspecialchars($label) . '</span>';
    }

    /**
     * Obtiene los activityHistory recientes para el dashboard
     */
    private function getRecentActivityHistory(): array
    {
        return Capsule::table('guest_invoice_logs')
            ->orderByDesc('id')
            ->limit(5)
            ->get()
            ->toArray();
    }
}