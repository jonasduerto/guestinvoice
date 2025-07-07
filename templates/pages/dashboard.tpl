<div class="gi-admin-container">
    {* <div class="gi-page-header">
        <h1><i class="fas fa-tachometer-alt"></i> {$_lang.nav_dashboard}</h1>
        <p>{$_lang.guestinvoicedesc}</p>
    </div> *}

    <!-- Stats Cards -->
    <div class="gi-stats-grid">
        <div class="gi-stat-card">
            <div class="gi-stat-icon">
                <i class="fas fa-link"></i>
            </div>
            <div class="gi-stat-content">
                <h3>{$activeLinks}</h3>
                <p>{$_lang.activelinks}</p>
            </div>
        </div>

        <div class="gi-stat-card">
            <div class="gi-stat-icon">
                <i class="fas fa-eye"></i>
            </div>
            <div class="gi-stat-content">
                <h3>{$totalAccesses}</h3>
                <p>{$_lang.totalaccesses}</p>
            </div>
        </div>

        <div class="gi-stat-card">
            <div class="gi-stat-icon">
                <i class="fas fa-history"></i>
            </div>
            <div class="gi-stat-content">
                <h3>{$expiredLinks}</h3>
                <p>{$_lang.expiredlinks}</p>
            </div>
        </div>
    </div>
</div>
