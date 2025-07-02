(function($) {
    "use strict";

    $(document).ready(function() {
        // Añadir botón de generación de link en la vista de factura
        if ($('#invoice-container').length) {
            addGenerateLinkButton();
        }
    });

    function addGenerateLinkButton() {
        const buttonHtml = `
            <div class="text-center margin-bottom">
                <button id="generateGuestLink" class="btn btn-primary">
                    <i class="fas fa-link"></i> ${lang.generate_guest_link}
                </button>
            </div>
        `;
        
        $('#invoice-container').prepend(buttonHtml);
        
        $('#generateGuestLink').on('click', function() {
            generateGuestLink();
        });
    }

    function generateGuestLink() {
        const invoiceId = $('#invoiceid').val();
        
        if (!invoiceId) {
            alert(lang.invoice_not_found);
            return;
        }
        
        $.ajax({
            url: CONFIG.baseUrl + 'modules/addons/guestinvoice/ajaxfile.php',
            type: 'POST',
            data: {
                action: 'generate_guest_link',
                invoice_id: invoiceId,
                token: csrfToken
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showLinkModal(response.link);
                } else {
                    alert(response.message || lang.generation_failed);
                }
            },
            error: function() {
                alert(lang.request_failed);
            }
        });
    }

    function showLinkModal(link) {
        const modalHtml = `
            <div class="modal fade" id="guestLinkModal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">${lang.guest_link}</h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <input type="text" class="form-control" id="guestLinkInput" value="${link}" readonly>
                            </div>
                            <button id="copyGuestLink" class="btn btn-default">
                                <i class="far fa-copy"></i> ${lang.copy_link}
                            </button>
                            <a href="mailto:?subject=${lang.invoice_link}&body=${link}" class="btn btn-default">
                                <i class="far fa-envelope"></i> ${lang.send_email}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        $('body').append(modalHtml);
        $('#guestLinkModal').modal('show');
        
        $('#copyGuestLink').on('click', function() {
            copyToClipboard(link);
        });
    }

    function copyToClipboard(text) {
        const input = document.createElement('input');
        input.value = text;
        document.body.appendChild(input);
        input.select();
        document.execCommand('copy');
        document.body.removeChild(input);
        
        alert(lang.link_copied);
    }

})(jQuery);


/**
 * Guest Invoice JavaScript
 * Maneja la interfaz de usuario para generar enlaces temporales
 */

(function($) {
    'use strict';
    
    // Configuración
    const CONFIG = {
        ajaxUrl: window.location.href, // Usar la URL actual para los hooks
        durations: [
            { value: 1, label: '1 hora' },
            { value: 4, label: '4 horas' },
            { value: 12, label: '12 horas' },
            { value: 24, label: '24 horas' }
        ]
    };
    
    // Clase principal
    class GuestInvoice {
        constructor() {
            this.init();
        }
        
        init() {
            this.bindEvents();
            this.createModal();
        }
        
        bindEvents() {
            // Botón para generar enlace
            $(document).on('click', '.generate-guest-link', (e) => {
                e.preventDefault();
                this.showModal();
            });
            
            // Copiar enlace al portapapeles
            $(document).on('click', '.copy-link', (e) => {
                e.preventDefault();
                this.copyToClipboard($(e.target).data('link'));
            });
            
            // Enviar por email
            $(document).on('click', '.send-email', (e) => {
                e.preventDefault();
                this.sendEmail($(e.target).data('link'));
            });
        }
        
        createModal() {
            const modalHtml = `
                <div id="guestInvoiceModal" class="modal fade" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Generar Enlace Temporal</h4>
                            </div>
                            <div class="modal-body">
                                <form id="guestLinkForm">
                                    <div class="form-group">
                                        <label for="duration">Duración del enlace:</label>
                                        <select id="duration" name="duration" class="form-control">
                                            ${CONFIG.durations.map(d => 
                                                `<option value="${d.value}">${d.label}</option>`
                                            ).join('')}
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>
                                            <input type="checkbox" id="sendEmail" name="send_email">
                                            Enviar enlace por email
                                        </label>
                                    </div>
                                </form>
                                <div id="linkResult" style="display: none;">
                                    <div class="alert alert-success">
                                        <h5>Enlace generado exitosamente</h5>
                                        <div class="input-group">
                                            <input type="text" id="generatedLink" class="form-control" readonly>
                                            <span class="input-group-btn">
                                                <button class="btn btn-default copy-link" type="button" data-link="">
                                                    <i class="fa fa-copy"></i> Copiar
                                                </button>
                                            </span>
                                        </div>
                                        <small class="text-muted">
                                            Expira: <span id="expiresAt"></span>
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                                <button type="button" class="btn btn-primary" id="generateLink">Generar Enlace</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            $('body').append(modalHtml);
            
            // Bind del botón generar
            $('#generateLink').on('click', () => {
                this.generateLink();
            });
        }
        
        showModal() {
            const invoiceId = $('.generate-guest-link').data('invoice-id');
            $('#guestInvoiceModal').data('invoice-id', invoiceId);
            $('#guestInvoiceModal').modal('show');
        }
        
        generateLink() {
            const invoiceId = $('#guestInvoiceModal').data('invoice-id');
            const duration = $('#duration').val();
            const sendEmail = $('#sendEmail').is(':checked');
            
            const $btn = $('#generateLink');
            const originalText = $btn.text();
            
            $btn.prop('disabled', true).text('Generando...');
            
            $.ajax({
                url: CONFIG.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'generate_guest_link',
                    invoice_id: invoiceId,
                    duration: duration,
                    send_email: sendEmail
                },
                dataType: 'json',
                success: (response) => {
                    if (response.success) {
                        this.showGeneratedLink(response.link, response.expires_at);
                        
                        // Log de éxito
                        this.logAction('link_generated_success', {
                            invoice_id: invoiceId,
                            duration: duration,
                            send_email: sendEmail
                        });
                    } else {
                        this.showError(response.error || 'Error al generar el enlace');
                    }
                },
                error: (xhr, status, error) => {
                    let errorMsg = 'Error de conexión';
                    try {
                        const response = JSON.parse(xhr.responseText);
                        errorMsg = response.error || errorMsg;
                    } catch (e) {
                        // Usar mensaje por defecto
                    }
                    this.showError(errorMsg);
                },
                complete: () => {
                    $btn.prop('disabled', false).text(originalText);
                }
            });
        }
        
        showGeneratedLink(link, expiresAt) {
            $('#generatedLink').val(link);
            $('#expiresAt').text(expiresAt);
            $('.copy-link').data('link', link);
            $('#linkResult').show();
            
            // Scroll al resultado
            $('#linkResult')[0].scrollIntoView({ behavior: 'smooth' });
        }
        
        showError(message) {
            const errorHtml = `
                <div class="alert alert-danger">
                    <i class="fa fa-exclamation-triangle"></i>
                    ${message}
                </div>
            `;
            
            $('#linkResult').html(errorHtml).show();
        }
        
        copyToClipboard(text) {
            if (navigator.clipboard && window.isSecureContext) {
                // Método moderno
                navigator.clipboard.writeText(text).then(() => {
                    this.showCopySuccess();
                }).catch(() => {
                    this.fallbackCopyToClipboard(text);
                });
            } else {
                // Método fallback
                this.fallbackCopyToClipboard(text);
            }
        }
        
        fallbackCopyToClipboard(text) {
            const textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'fixed';
            textArea.style.left = '-999999px';
            textArea.style.top = '-999999px';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            
            try {
                document.execCommand('copy');
                this.showCopySuccess();
            } catch (err) {
                this.showError('No se pudo copiar al portapapeles');
            }
            
            document.body.removeChild(textArea);
        }
        
        showCopySuccess() {
            const $btn = $('.copy-link');
            const originalText = $btn.html();
            
            $btn.html('<i class="fa fa-check"></i> Copiado').addClass('btn-success');
            
            setTimeout(() => {
                $btn.html(originalText).removeClass('btn-success');
            }, 2000);
        }
        
        sendEmail(link) {
            // Esta función se puede implementar si se necesita envío adicional
            // Por ahora, el envío se hace automáticamente si se marca la casilla
            this.showError('El envío por email se realiza automáticamente al generar el enlace');
        }
        
        logAction(action, data = {}) {
            // Log de acciones del usuario (opcional)
            console.log('Guest Invoice Action:', action, data);
        }
    }
    
    // Inicializar cuando el DOM esté listo
    $(document).ready(() => {
        new GuestInvoice();
    });
    
})(jQuery); 