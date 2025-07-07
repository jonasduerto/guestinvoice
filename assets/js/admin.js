console.log('Admin JS loaded - pagination functions available');

// Track initialization state
const paginationState = {
    initializing: false,
    observer: null,
    initializedTables: new Set()
};

// Core pagination function
function initPagination(tableId, itemsPerPage) {
    itemsPerPage = itemsPerPage || 10;
    console.log('Initializing pagination for:', tableId);
    
    const table = document.querySelector(tableId);
    if (!table) {
        console.error('Table not found:', tableId);
        return false;
    }
    
    const rows = table.querySelectorAll('tbody tr');
    const totalItems = rows.length;
    const totalPages = Math.ceil(totalItems / itemsPerPage);
    
    console.log('Found', totalItems, 'rows and', totalPages, 'pages');
    
    // Create pagination controls
    const pagination = document.createElement('div');
    pagination.className = 'pagination';
    pagination.innerHTML = `
        <ul class="pagination">
            <li class="page-item"><a class="page-link" href="#" data-page="prev">&laquo;</a></li>
            ${Array.from({length: totalPages}, (_, i) => 
                `<li class="page-item"><a class="page-link" href="#" data-page="${i + 1}">${i + 1}</a></li>`
            ).join('')}
            <li class="page-item"><a class="page-link" href="#" data-page="next">&raquo;</a></li>
        </ul>
    `;
    
    // Insert pagination after the table
    table.parentNode.insertBefore(pagination, table.nextSibling);
    
    // Show first page by default
    showPage(1);
    
    // Add event listeners
    pagination.addEventListener('click', function(e) {
        e.preventDefault();
        const target = e.target.closest('[data-page]');
        if (!target) return;
        
        let page = target.dataset.page;
        const currentPage = parseInt(pagination.querySelector('.active')?.textContent || '1');
        
        if (page === 'prev' && currentPage > 1) {
            showPage(currentPage - 1);
        } else if (page === 'next' && currentPage < totalPages) {
            showPage(currentPage + 1);
        } else if (!isNaN(parseInt(page))) {
            showPage(parseInt(page));
        }
    });
    
    function showPage(page) {
        // Hide all rows
        Array.from(rows).forEach(row => row.style.display = 'none');
        
        // Show rows for current page
        const start = (page - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        
        for (let i = start; i < end && i < rows.length; i++) {
            rows[i].style.display = '';
        }
        
        // Update pagination controls
        const activeItems = pagination.querySelectorAll('.active');
        activeItems.forEach(item => item.classList.remove('active'));
        
        const currentPageItem = pagination.querySelector(`[data-page="${page}"]`);
        if (currentPageItem) {
            currentPageItem.parentElement.classList.add('active');
        }
        
        // Disable prev/next buttons when at first/last page
        const prevButton = pagination.querySelector('[data-page="prev"]');
        const nextButton = pagination.querySelector('[data-page="next"]');
        
        if (prevButton) {
            prevButton.parentElement.classList.toggle('disabled', page === 1);
        }
        if (nextButton) {
            nextButton.parentElement.classList.toggle('disabled', page === totalPages);
        }
    }
}

// Global function to initialize pagination for a table
function setupTablePagination(tableSelector, itemsPerPage = 15) {
    // Prevent re-entrancy
    if (paginationState.initializing) {
        console.log('Pagination initialization already in progress');
        return false;
    }
    
    console.log('Setting up pagination for:', tableSelector);
    
    if (typeof initPagination !== 'function') {
        console.error('initPagination function not found!');
        return false;
    }
    
    const table = document.querySelector(tableSelector);
    if (!table) {
        console.warn('Table not found:', tableSelector);
        return false;
    }
    
    // Skip if already initialized
    if (paginationState.initializedTables.has(tableSelector)) {
        console.log('Pagination already initialized for:', tableSelector);
        return true;
    }
    
    // Set initializing flag
    paginationState.initializing = true;
    
    try {
        // Disconnect observer temporarily to prevent re-triggering
        if (paginationState.observer) {
            paginationState.observer.disconnect();
        }
        
        console.log('Initializing pagination for table:', tableSelector);
        initPagination(tableSelector, itemsPerPage);
        
        // Mark as initialized
        paginationState.initializedTables.add(tableSelector);
        table.dataset.paginationInitialized = 'true';
        
        console.log('Pagination initialized for:', tableSelector);
        return true;
    } catch (error) {
        console.error('Error initializing pagination:', error);
        return false;
    } finally {
        // Re-enable initialization
        paginationState.initializing = false;
        
        // Reconnect observer if needed
        if (paginationState.observer) {
            startObserving();
        }
    }
}

// Set up mutation observer for dynamic content
function startObserving() {
    // Disconnect existing observer if any
    if (paginationState.observer) {
        paginationState.observer.disconnect();
    }
    
    // Create new observer
    paginationState.observer = new MutationObserver((mutations) => {
        // Only proceed if not already initializing
        if (paginationState.initializing) return;
        
        // Check if our table was added or changed
        const table = document.querySelector('#guestLinksTable');
        if (table && !paginationState.initializedTables.has('#guestLinksTable')) {
            console.log('New table content detected, initializing pagination');
            setupTablePagination('#guestLinksTable', 15);
        }
    });
    
    // Only observe the app container, not the whole document
    const appContainer = document.getElementById('app-container');
    if (appContainer) {
        paginationState.observer.observe(appContainer, {
            childList: true,
            subtree: true
        });
        console.log('Started observing app container for changes');
    }
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        console.log('DOM loaded, starting pagination setup');
        startObserving();
    });
} else {
    console.log('DOM already loaded, starting pagination setup');
    startObserving();
}

// Export for use in other scripts
window.GuestInvoicePagination = {
    setupTablePagination: setupTablePagination
};