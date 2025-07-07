class GuestInvoiceSPA {
    constructor() {
        this.views = {
            dashboard: null,
            settings: null,
            activityHistory: null,
            generate: null
        };

        this.init();
    }

    async init() {
        // Configura el router
        this.setupRouter();

        // Carga la vista inicial
        const initialView = this.getCurrentViewFromHash() || APP_CONFIG.currentView;
        await this.loadView(initialView);

        // Establece el menú activo
        this.setActiveNav(initialView);
    }

    setupRouter() {
        // Maneja cambios en el hash
        window.addEventListener('hashchange', () => {
            const view = this.getCurrentViewFromHash();
            if (view && this.views.hasOwnProperty(view)) {
                this.loadView(view);
                this.setActiveNav(view);
            }
        });
    }

    getCurrentViewFromHash() {
        const hash = window.location.hash.substring(1);
        return Object.keys(this.views).includes(hash) ? hash : null;
    }

    setActiveNav(view) {
        document.querySelectorAll('.gi-main-nav li').forEach(li => {
            li.classList.toggle('active', li.dataset.view === view);
        });
    }

    async loadView(viewName, params = {}) {
        try {
            this.showLoading();

            // If activityHistory and page param, always fetch fresh (no cache)
            if (viewName === 'activityHistory' && params.page) {
                // continue to AJAX fetch
            } else if (this.views[viewName]) {
                document.getElementById('app-container').innerHTML = this.views[viewName];
                setTimeout(() => {
                    this.initViewComponents(viewName, document.getElementById('app-container'));
                    this.hideLoading();
                }, 200);
                return;
            }

            // Build query string
            const csrf = APP_CONFIG.csrf_token || (typeof csrf_token !== 'undefined' ? csrf_token : '');
            let url = `${APP_CONFIG.moduleLink}&ajax_action=load_view&view=${viewName}&csrf_token=${encodeURIComponent(csrf)}`;
            if (viewName === 'activityHistory' && params.page) {
                url += `&page=${params.page}`;
            }
            const response = await axios.get(url);

            if (response.data.success) {
                // Only cache if not paginated activityHistory
                if (!(viewName === 'activityHistory' && params.page)) {
                    this.views[viewName] = response.data.html;
                }
                const container = document.getElementById('app-container');
                container.innerHTML = response.data.html;
                this.initViewComponents(viewName, container);
                
                // Pagination is now handled by admin.js through the MutationObserver
            } else {
                throw new Error(response.data.error || APP_CONFIG.lang.errorLoading);
            }
        } catch (error) {
            console.error('Error loading view:', error);
            document.getElementById('app-container').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> 
                    ${APP_CONFIG.lang.errorLoading}: ${error.message}
                </div>
            `;
        } finally {
            this.hideLoading();
        }
    }

    showLoading() {
        var spinner = document.getElementById('loading-spinner');
        if (spinner) {
            spinner.style.display = 'flex';
        }
    }

    hideLoading() {
        var spinner = document.getElementById('loading-spinner');
        if (spinner) {
            spinner.style.display = 'none';
        }
    }

    initViewComponents(viewName, container) {
        // Aquí puedes inicializar componentes específicos para cada vista
        switch(viewName) {
            case 'activityHistory':
                this.initActivityHistoryTable(container);
                break;
            case 'settings':
                this.initSettingsForm(container);
                break;
            case 'generate':
                this.initGenerateForm(container);
                break;
            // ... otros casos
        }
    }

    initActivityHistoryTable(container) {
        // Inicialización de DataTables o similar
        // Aquí puedes manipular el DOM de logs usando el container
        console.log('Initializing activity history table...', container);
    }

    initGenerateForm(container) {
        // Inicialización del formulario de generación
        // Aquí puedes manipular el DOM de generate usando el container
        console.log('Initializing generate form...', container);
    }

    initSettingsForm(container) {
        const form = container.querySelector('#gi-settings-form');
        if (!form) return;

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            }
            try {
                const fd = new FormData(form);
                fd.append('ajax_action', 'save_settings');
                fd.append('csrf_token', APP_CONFIG.csrf_token);

                const resp = await axios.post(APP_CONFIG.moduleLink, fd);
                if (resp.data && resp.data.success) {
                    container.innerHTML = resp.data.html;
                    if (this.views) {
                        this.views['settings'] = resp.data.html;
                    }
                } else {
                    alert((resp.data && resp.data.message) || APP_CONFIG.lang.server_error || 'Error');
                }
            } catch (err) {
                alert(APP_CONFIG.lang.server_error || 'Server Error');
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-save"></i> ' + (APP_CONFIG.lang.savechanges || 'Save');
                }
            }
        });
    }
}

// Iniciar la aplicación cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
   
    // var activityLogsTable = document.getElementById('activityLogsTable');
    // activityLogsTable = dynatable({

    // });
    
    // var guestLinksTable = document.getElementById('guestLinksTable');
    // guestLinksTable = dynatable({

    // });



    // Initialize clipboard.js
    var clipboard = new ClipboardJS('.gi-copy-link', {
        text: function(trigger) {
            return $(trigger).data('link');
        }
    });

    // Show tooltip when copied
    $('.gi-copy-link').on('click', function() {
        var $this = $(this);
        var originalTitle = $this.attr('title');
        $this.attr('title', '{$_lang.copied}').tooltip('show');
        setTimeout(function() {
            $this.attr('title', originalTitle).tooltip('hide');
        }, 2000);
    });


    // Move the loading spinner outside the app-container for global visibility
    var spinner = document.getElementById('loading-spinner');
    if (spinner && spinner.parentNode && spinner.parentNode.id === 'wrapguestinvoice') {
        document.body.appendChild(spinner);
    }
    new GuestInvoiceSPA();
});