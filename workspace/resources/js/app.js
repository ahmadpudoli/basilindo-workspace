import './bootstrap';
import * as bootstrap from 'bootstrap';

// Inline Blade widgets use Bootstrap's modal/dropdown API as well.
window.bootstrap = bootstrap;

const themeStorageKey = 'basilindo-workspace-theme';
const sidebarStorageKey = 'basilindo-workspace-sidebar-collapsed';
const savedTheme = window.localStorage.getItem(themeStorageKey);
if (savedTheme === 'dark' || savedTheme === 'light') {
    document.documentElement.dataset.theme = savedTheme;
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((element) => {
        bootstrap.Tooltip.getOrCreateInstance(element);
    });
    document.addEventListener('shown.bs.modal', (event) => {
        event.target.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((element) => {
            bootstrap.Tooltip.getInstance(element)?.dispose();
            bootstrap.Tooltip.getOrCreateInstance(element, { container: 'body' });
        });
    });

    const pushButton = document.querySelector('[data-web-push-enable]');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const decodeVapidKey = (value) => Uint8Array.from(atob(`${value.replace(/-/g, '+').replace(/_/g, '/')}${'='.repeat((4 - value.length % 4) % 4)}`), (character) => character.charCodeAt(0));
    const setupWebPush = async () => {
        if (!pushButton || !('serviceWorker' in navigator) || !('PushManager' in window) || !window.isSecureContext) return;
        try {
            const config = await fetch('/workspace/notifications/push/config', { headers: { Accept: 'application/json' } }).then((response) => response.json());
            if (!config.enabled) return;
            pushButton.hidden = false;
            pushButton.addEventListener('click', async () => {
                const permission = await Notification.requestPermission();
                if (permission !== 'granted') return;
                const registration = await navigator.serviceWorker.register('/sw.js');
                let subscription = await registration.pushManager.getSubscription();
                if (!subscription) subscription = await registration.pushManager.subscribe({ userVisibleOnly: true, applicationServerKey: decodeVapidKey(config.publicKey) });
                await fetch('/workspace/notifications/push/subscriptions', {
                    method: 'POST', headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': csrf },
                    body: JSON.stringify(subscription.toJSON()),
                });
                pushButton.title = 'Notifikasi perangkat aktif';
            }, { once: true });
        } catch (_) {}
    };
    setupWebPush();

    const enhanceFloatingFields = () => {
        document.querySelectorAll('label.form-label').forEach((label) => {
            const field = label.nextElementSibling;
            if (!field || !field.matches('input:not([type="file"]).form-control, select.form-select, textarea.form-control, .workspace-dropdown-multiselect')) return;
            if (label.parentElement?.classList.contains('workspace-floating-field')) return;

            const wrapper = document.createElement('div');
            wrapper.className = 'workspace-floating-field workspace-floating-field--auto';
            label.parentNode.insertBefore(wrapper, label);
            wrapper.append(field, label);

            if (field.matches('.workspace-dropdown-multiselect')) return;

            if (!field.id) {
                const fieldName = field.name || 'field';
                field.id = `workspace-${fieldName.replace(/[^a-zA-Z0-9_-]/g, '-')}-${Math.random().toString(36).slice(2, 7)}`;
            }
            label.htmlFor = field.id;
            if (!field.getAttribute('placeholder')) field.setAttribute('placeholder', ' ');
            field.addEventListener('input', () => wrapper.classList.toggle('has-value', field.value !== ''));
            field.addEventListener('change', () => wrapper.classList.toggle('has-value', field.value !== ''));
            wrapper.classList.toggle('has-value', field.value !== '');
        });
    };

    const formatWorkspaceDate = (value) => {
        const match = /^(\d{4})-(\d{2})-(\d{2})$/.exec(value || '');
        return match ? `${match[3]}/${match[2]}/${match[1]}` : '';
    };
    const parseWorkspaceDate = (value) => {
        const match = /^(\d{2})\/(\d{2})\/(\d{4})$/.exec(value || '');
        if (!match) return '';
        const day = Number(match[1]);
        const month = Number(match[2]);
        const year = Number(match[3]);
        const date = new Date(Date.UTC(year, month - 1, day));
        return date.getUTCFullYear() === year && date.getUTCMonth() === month - 1 && date.getUTCDate() === day
            ? `${year.toString().padStart(4, '0')}-${month.toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`
            : '';
    };
    const enhanceDateInputs = () => {
        document.querySelectorAll('input[type="date"]:not([data-workspace-date-enhanced]):not([data-workspace-date-value])').forEach((input) => {
            const fieldName = input.name;
            const hidden = document.createElement('input');
            hidden.type = 'date';
            hidden.className = 'workspace-date-picker-source';
            hidden.name = fieldName;
            hidden.value = input.value || '';
            hidden.dataset.workspaceDateValue = 'true';

            const dateRow = input.closest('[data-date-row]');
            const dateControl = dateRow ? document.createElement('div') : input.parentElement;
            if (dateRow) {
                dateControl.className = 'workspace-date-control';
                input.parentElement.insertBefore(dateControl, input);
                dateControl.append(input);
            }

            input.dataset.workspaceDateEnhanced = 'true';
            input.dataset.workspaceDateName = fieldName || '';
            input.type = 'text';
            input.name = '';
            input.inputMode = 'numeric';
            input.autocomplete = 'off';
            input.placeholder = 'dd/mm/yyyy';
            input.value = formatWorkspaceDate(hidden.value);
            input.setAttribute('aria-label', input.getAttribute('aria-label') || 'Tanggal (dd/mm/yyyy)');
            dateControl?.classList.add('workspace-date-control');
            input.insertAdjacentElement('afterend', hidden);
            const pickerButton = document.createElement('button');
            pickerButton.type = 'button';
            pickerButton.className = 'workspace-date-picker-button';
            pickerButton.dataset.workspaceDatePicker = 'true';
            pickerButton.setAttribute('aria-label', 'Buka kalender');
            pickerButton.innerHTML = '<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="5" width="16" height="15" rx="2"></rect><path d="M8 3v4M16 3v4M4 10h16"></path></svg>';
            input.insertAdjacentElement('afterend', pickerButton);

            const sync = () => {
                const digits = input.value.replace(/\D/g, '').slice(0, 8);
                const formatted = digits.length > 4
                    ? `${digits.slice(0, 2)}/${digits.slice(2, 4)}/${digits.slice(4)}`
                    : (digits.length > 2 ? `${digits.slice(0, 2)}/${digits.slice(2)}` : digits);
                if (input.value !== formatted) input.value = formatted;
                hidden.value = parseWorkspaceDate(formatted);
            };
            input.addEventListener('input', sync);
            input.addEventListener('blur', sync);
        });
    };

    document.addEventListener('change', (event) => {
        const source = event.target.closest?.('[data-workspace-date-value]');
        if (!source) return;
        const display = source.parentElement?.querySelector('[data-workspace-date-enhanced]');
        if (display) display.value = formatWorkspaceDate(source.value);
    });
    document.addEventListener('click', (event) => {
        const button = event.target.closest?.('[data-workspace-date-picker]');
        if (!button) return;
        event.preventDefault();
        const source = button.parentElement?.querySelector('[data-workspace-date-value]');
        if (!source) return;
        try {
            if (typeof source.showPicker === 'function') source.showPicker();
            else source.click();
        } catch (error) {
            source.click();
        }
    });

    enhanceFloatingFields();
    enhanceDateInputs();

    const bindMobilePanelToggle = (toggle) => {
        if (toggle.dataset.mobileToggleBound === 'true') return;
        toggle.dataset.mobileToggleBound = 'true';
        toggle.addEventListener('click', () => {
            const panel = toggle.closest('[data-mobile-panel]');
            if (!panel) return;
            const expanded = panel.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', expanded ? 'true' : 'false');
            const icon = toggle.querySelector('[data-mobile-toggle-icon]');
            if (icon) icon.textContent = expanded ? '−' : '+';
        });
    };

    const enhanceMobileFilterPanels = () => {
        if (!window.matchMedia('(max-width: 768px)').matches) return;

        document.querySelectorAll('.workspace-content form[method="GET"], .workspace-content form.workspace-search-form:not([method])').forEach((form) => {
            if (form.matches('[data-no-mobile-collapse]')) return;

            let panel = form.closest('[data-mobile-panel]');
            if (!panel) {
                panel = document.createElement('section');
                panel.className = 'workspace-mobile-panel workspace-auto-filter-panel';
                panel.dataset.mobilePanel = '';

                const toggle = document.createElement('button');
                toggle.type = 'button';
                toggle.className = 'workspace-mobile-panel-toggle';
                toggle.dataset.mobileToggle = '';
                toggle.setAttribute('aria-expanded', 'false');
                const filterTitle = form.dataset.filterTitle
                    || (window.location.pathname === '/workspace/hris/approvals' ? 'Filter pengajuan' : 'Filter data');
                toggle.innerHTML = `${filterTitle}<span data-mobile-toggle-icon>+</span>`;

                form.parentNode.insertBefore(panel, form);
                form.classList.remove('mb-3', 'mt-3');
                form.classList.add('workspace-mobile-panel-content');
                panel.append(toggle, form);
            }

            panel.querySelectorAll('[data-mobile-toggle]').forEach(bindMobilePanelToggle);
        });

        document.querySelectorAll('[data-mobile-toggle]').forEach(bindMobilePanelToggle);
    };

    enhanceMobileFilterPanels();
    window.matchMedia('(max-width: 768px)').addEventListener('change', (event) => {
        if (event.matches) enhanceMobileFilterPanels();
    });

    if (window.location.pathname === '/workspace/hris/leave-requests') {
        document.querySelectorAll('table tbody tr').forEach((row) => {
            const cell = row.children[2];
            if (!cell || !cell.textContent.includes('/')) return;
            const dates = cell.textContent.split(',').map((date) => date.trim()).filter(Boolean);
            if (!dates.length) return;
            const badges = document.createElement('div');
            badges.className = 'workspace-leave-date-badges';
            dates.forEach((date) => {
                const badge = document.createElement('span');
                badge.className = 'badge rounded-pill text-bg-light';
                badge.textContent = date;
                badges.append(badge);
            });
            cell.replaceChildren(badges);
        });
    }

    if (window.location.pathname === '/workspace/settings/hris/workflow') {
        const workflowTable = document.querySelector('.workspace-list-pagination')?.closest('.card')?.querySelector('table');
        const sortableColumns = { Perusahaan: 'company_id', Workflow: 'workflow_key', Approver: 'user_id' };
        workflowTable?.querySelectorAll('thead th').forEach((header) => {
            const label = header.textContent.trim();
            const column = sortableColumns[label];
            if (!column) return;
            const params = new URLSearchParams(window.location.search);
            const active = params.get('sort') === column;
            const currentDirection = params.get('direction') === 'desc' ? 'desc' : 'asc';
            const direction = active && currentDirection === 'asc' ? 'desc' : 'asc';
            params.set('sort', column);
            params.set('direction', direction);
            params.delete('page');
            const link = document.createElement('a');
            link.className = `workspace-sort-link${active ? ' active' : ''}`;
            link.href = `${window.location.pathname}?${params.toString()}`;
            link.textContent = label;
            const indicator = document.createElement('span');
            indicator.setAttribute('aria-hidden', 'true');
            indicator.textContent = active ? (currentDirection === 'desc' ? ' ↓' : ' ↑') : ' ↕';
            link.append(indicator);
            header.replaceChildren(link);
        });
    }

    document.querySelectorAll('[data-subject-audit-toggle]').forEach((toggle) => {
        const panel = toggle.closest('[data-subject-audit-panel]');
        if (!panel) return;

        toggle.addEventListener('click', () => {
            const collapsed = panel.classList.toggle('is-collapsed');
            toggle.setAttribute('aria-expanded', String(!collapsed));
            toggle.setAttribute('title', collapsed ? 'Buka audit trail' : 'Tutup audit trail');
        });
    });

    document.querySelectorAll('[data-document-upload]').forEach((upload) => {
        const input = upload.querySelector('[data-document-upload-input]');
        const preview = upload.querySelector('[data-document-upload-preview]');
        if (!input || !preview) return;

        let previewUrl = null;
        const formatBytes = (bytes) => {
            if (bytes < 1024) return `${bytes} B`;
            if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} KB`;
            return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
        };
        const fileIcon = (file) => {
            const icon = document.createElement('span');
            icon.className = 'workspace-document-file-icon';
            icon.textContent = file.type === 'application/pdf'
                ? 'PDF'
                : (file.name.split('.').pop()?.slice(0, 4).toUpperCase() || 'FILE');
            return icon;
        };
        const renderFile = () => {
            if (previewUrl) URL.revokeObjectURL(previewUrl);
            previewUrl = null;
            preview.replaceChildren();
            const file = input.files?.[0];
            if (!file) {
                preview.innerHTML = '<div class="workspace-document-preview-empty"><span class="workspace-document-preview-empty-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M6 3h8l4 4v14H6zM14 3v5h5M9 13h6M9 17h4"/></svg></span><span>Pilih file untuk melihat preview</span></div>';
                return;
            }

            const body = document.createElement('div');
            body.className = 'workspace-document-preview-body';
            if (file.type.startsWith('image/')) {
                previewUrl = URL.createObjectURL(file);
                const image = document.createElement('img');
                image.src = previewUrl;
                image.alt = `Preview ${file.name}`;
                image.className = 'workspace-document-image-preview';
                body.append(image);
            } else {
                body.append(fileIcon(file));
            }

            const meta = document.createElement('div');
            meta.className = 'workspace-document-preview-meta';
            const name = document.createElement('strong');
            name.textContent = file.name;
            const details = document.createElement('span');
            details.textContent = `${file.type || 'Format file'} · ${formatBytes(file.size)}`;
            meta.append(name, details);
            preview.append(body, meta);
        };

        input.addEventListener('change', renderFile);
    });

    const richTextEditors = document.querySelectorAll('.workspace-richtext-editor');
    if (window.jQuery?.fn?.summernote) {
        window.jQuery(richTextEditors).summernote({
            height: 60,
            placeholder: 'Tulis deskripsi dokumen...',
            dialogsInBody: true,
            toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link', 'table']],
                ['view', ['undo', 'redo', 'codeview']],
            ],
        });
    }

    const floatingObserver = new MutationObserver(() => {
        enhanceFloatingFields();
        enhanceDateInputs();
    });
    floatingObserver.observe(document.body, { childList: true, subtree: true });

    document.querySelectorAll('[data-company-brand-upload]').forEach((upload) => {
        const input = upload.querySelector('[data-company-brand-input]');
        const preview = upload.querySelector('[data-company-brand-preview]');
        if (!input || !preview) return;

        input.addEventListener('change', () => {
            const file = input.files?.[0];
            if (!file || !file.type.startsWith('image/')) return;

            const url = URL.createObjectURL(file);
            preview.innerHTML = `<img src="${url}" alt="Preview logo perusahaan" data-company-brand-preview-image>`;
        });
    });

    document.querySelectorAll('[data-password-toggle]').forEach((toggle) => {
        const input = document.querySelector(toggle.dataset.passwordToggle);
        if (!input) return;
        toggle.addEventListener('click', () => {
            input.type = input.type === 'password' ? 'text' : 'password';
            toggle.setAttribute('aria-label', input.type === 'password' ? 'Tampilkan kata sandi' : 'Sembunyikan kata sandi');
        });
    });

    const previewModalElement = document.querySelector('#workspacePreviewModal');
    const previewStage = document.querySelector('[data-workspace-preview-stage]');
    const previewFrame = previewStage?.querySelector('[data-workspace-preview-frame]');
    const previewTitle = document.querySelector('#workspacePreviewTitle');
    const previewFallback = document.querySelector('[data-workspace-preview-fallback]');
    const previewModal = previewModalElement ? bootstrap.Modal.getOrCreateInstance(previewModalElement) : null;

    document.addEventListener('click', (event) => {
        const trigger = event.target.closest('[data-workspace-preview]');
        if (!trigger || !previewModal || !previewStage || !previewFrame) return;

        event.preventDefault();
        const url = trigger.dataset.previewUrl || trigger.href;
        const mime = trigger.dataset.previewType || '';
        const title = trigger.dataset.previewTitle || 'Preview dokumen';
        previewTitle.textContent = title;
        previewFallback.href = trigger.dataset.previewFallbackUrl || url;
        previewStage.querySelector('.workspace-preview-unavailable')?.remove();
        previewFrame.hidden = false;
        previewFrame.title = `Preview dokumen: ${title}`;
        previewFrame.onload = hideLoading;
        showLoading('Memuat dokumen...');

        if (mime === 'application/pdf') {
            previewFrame.title = `Preview PDF: ${title}`;
            previewFrame.src = `${url}#toolbar=1&view=FitH`;
        } else if (mime.startsWith('image/')) {
            previewFrame.src = url;
        } else {
            const message = document.createElement('div');
            message.className = 'workspace-preview-loading workspace-preview-unavailable';
            message.textContent = 'Format file ini tidak dapat ditampilkan langsung. Gunakan tombol buka di tab baru.';
            previewStage.append(message);
            previewFrame.hidden = true;
            hideLoading();
        }

        previewModal.show();
    });

    previewModalElement?.addEventListener('hidden.bs.modal', () => {
        if (previewFrame) {
            previewFrame.onload = null;
            previewFrame.src = 'about:blank';
            previewFrame.hidden = false;
        }
        previewStage?.querySelector('.workspace-preview-unavailable')?.remove();
        if (previewFallback) previewFallback.removeAttribute('href');
    });

    const confirmModalElement = document.querySelector('#workspaceConfirmModal');
    const confirmMessage = document.querySelector('[data-workspace-confirm-message]');
    const confirmCommentWrap = document.querySelector('[data-workspace-confirm-comment-wrap]');
    const confirmComment = document.querySelector('[data-workspace-confirm-comment]');
    const confirmSubmit = document.querySelector('[data-workspace-confirm-submit]');
    const confirmModal = confirmModalElement ? bootstrap.Modal.getOrCreateInstance(confirmModalElement) : null;
    let pendingConfirmForm = null;
    let pendingConfirmSubmitter = null;
    const loadingOverlay = document.querySelector('[data-workspace-loading]');
    const loadingMessage = loadingOverlay?.querySelector('[data-workspace-loading-message]');
    const showLoading = (message = 'Memproses...') => {
        if (!loadingOverlay) return;
        if (loadingMessage) loadingMessage.textContent = message;
        loadingOverlay.classList.add('is-visible');
        loadingOverlay.setAttribute('aria-hidden', 'false');
    };
    window.addEventListener('workspace:show-loading', (event) => showLoading(event.detail || 'Memproses...'));
    const formLoadingMessage = (form) => {
        const method = (form.querySelector('input[name="_method"]')?.value || form.method || 'GET').toUpperCase();
        const actionText = form.querySelector('button[type="submit"], button')?.textContent?.trim().toLowerCase() || '';
        if (method === 'DELETE') return actionText.includes('arsip') ? 'Mengarsipkan data...' : 'Menghapus data...';
        if (method === 'PUT' || method === 'PATCH') return 'Memperbarui data...';
        if (method === 'POST' && !form.classList.contains('workspace-login-form')) return 'Menambahkan data...';
        return 'Loading...';
    };
    const hideLoading = () => {
        loadingOverlay?.classList.remove('is-visible');
        loadingOverlay?.setAttribute('aria-hidden', 'true');
    };
    window.addEventListener('workspace:hide-loading', hideLoading);

    const validateWorkspaceForm = (form) => {
        const type = form.dataset.clientValidation;
        if (!type) return true;
        form.querySelectorAll('.client-validation-error').forEach((element) => element.remove());
        form.querySelectorAll('.is-invalid').forEach((field) => field.classList.remove('is-invalid'));
        const errors = [];
        const field = (name) => form.querySelector(`[name="${name}"]`);
        const value = (name) => field(name)?.value?.trim() || '';
        const addError = (name, message, target = field(name)) => {
            if (!target) return;
            target.classList.add('is-invalid');
            const container = target.closest('.workspace-floating-field, .workspace-dropdown-multiselect, .company-brand-upload-content') || target.parentElement;
            const error = document.createElement('div');
            error.className = 'invalid-feedback d-block client-validation-error';
            error.textContent = message;
            container?.append(error);
            errors.push({ target, message });
        };

        if (type === 'company') {
            if (!value('name')) addError('name', 'Nama perusahaan wajib diisi.');
            if (!value('code')) addError('code', 'Kode perusahaan wajib diisi.');
            if (!value('entity_type')) addError('entity_type', 'Jenis entitas wajib dipilih.');
            if (!value('classification')) addError('classification', 'Klasifikasi wajib dipilih.');
            if (!value('status')) addError('status', 'Status wajib dipilih.');
            const logo = field('logo')?.files?.[0];
            if (logo && !['image/jpeg', 'image/png', 'image/webp'].includes(logo.type)) addError('logo', 'Logo harus berupa JPG, PNG, atau WEBP.', field('logo'));
            if (logo && logo.size > 2 * 1024 * 1024) addError('logo', 'Ukuran logo maksimal 2 MB.', field('logo'));
        }
        if (type === 'vendor') {
            if (!value('vendor_company_id')) addError('vendor_company_id', 'Master perusahaan vendor wajib dipilih.');
            if (!value('name')) addError('name', 'Nama vendor wajib diisi.');
            if (!value('status')) addError('status', 'Status wajib dipilih.');
            const operational = [...form.querySelectorAll('[name="operational_company_ids[]"]:checked')];
            if (!operational.length) addError('operational_company_ids[]', 'Pilih minimal satu perusahaan operasional.', form.querySelector('[data-searchable-dropdown]'));
        }
        if (type === 'employee') {
            if (!value('company_id')) addError('company_id', 'Perusahaan wajib dipilih.');
            if (!value('employee_number')) addError('employee_number', 'Nomor karyawan wajib diisi.');
            else if (value('employee_number').length > 50) addError('employee_number', 'Nomor karyawan maksimal 50 karakter.');
            if (!value('full_name')) addError('full_name', 'Nama lengkap wajib diisi.');
            else if (value('full_name').length > 150) addError('full_name', 'Nama lengkap maksimal 150 karakter.');
            if (value('email') && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value('email'))) addError('email', 'Format email tidak valid.');
            if (value('job_title') && value('job_title').length > 150) addError('job_title', 'Jabatan maksimal 150 karakter.');
            if (value('phone') && value('phone').length > 50) addError('phone', 'Nomor telepon maksimal 50 karakter.');
            if (!value('status')) addError('status', 'Status wajib dipilih.');
        }
        if (type === 'hris-workflow') {
            if (!value('company_id')) addError('company_id', 'Perusahaan operasional wajib dipilih.');
            if (!value('user_id')) addError('user_id', 'Approver wajib dipilih.');
        }
        if (type === 'hris-claim') {
            const scope = form.querySelector('[name="expense_scope"]:checked')?.value || '';
            const reference = form.querySelector('[name="work_reference_type"]:checked')?.value || '';
            const items = [...form.querySelectorAll('#claim-items .claim-item')];
            const selectedAttachments = items.map((item) => item.querySelector('[data-claim-attachment-input]')?.files?.[0]).filter(Boolean);
            if (!['internal', 'external_project'].includes(scope)) addError('expense_scope', 'Keperluan wajib dipilih.', form.querySelector('.workspace-claim-scope-grid'));
            if (!items.length) addError('items', 'Tambahkan minimal satu rincian biaya.', form.querySelector('#claim-items'));
            let selectedCurrency = '';
            items.forEach((item, index) => {
                const category = item.querySelector('[name$="[expense_category_id]"]');
                const categoryPicker = category?.parentElement?.querySelector('.workspace-select-picker, [data-select-picker], .dropdown button') || category;
                if (!category?.value) addError(category?.name || 'items', `Rincian ${index + 1}: kategori biaya wajib dipilih.`, categoryPicker);
                const amountField = item.querySelector('[name$="[amount]"]');
                const rawAmount = amountField?.value?.trim() || '';
                const canonicalAmount = rawAmount.includes(',') ? rawAmount.replace(/\./g, '').replace(',', '.') : rawAmount;
                if (!rawAmount) addError(amountField?.name || 'items', `Rincian ${index + 1}: nominal wajib diisi.`, amountField);
                else if (!/^\d+(?:\.\d{1,2})?$/.test(canonicalAmount) || Number(canonicalAmount) <= 0) addError(amountField.name, `Rincian ${index + 1}: nominal harus lebih dari 0 dengan maksimal 2 desimal.`, amountField);
                const description = item.querySelector('[name$="[description]"]');
                if (!description?.value?.trim()) addError(description?.name || 'items', `Rincian ${index + 1}: deskripsi wajib diisi.`, description);
                else if (description.value.trim().length > 2000) addError(description.name, `Rincian ${index + 1}: deskripsi maksimal 2.000 karakter.`, description);
                const attachment = item.querySelector('[data-claim-attachment-input]')?.files?.[0];
                const attachmentToken = item.querySelector('[data-claim-attachment-token]')?.value || '';
                if (!attachment && !attachmentToken && item.dataset.hasExistingAttachment !== 'true') addError('items', `Rincian ${index + 1}: bukti nota wajib dilampirkan.`, item.querySelector('[data-choose-claim-file]'));
                if (attachment && attachment.size > 10 * 1024 * 1024) addError('items', `Rincian ${index + 1}: ukuran bukti maksimal 10 MB.`, item.querySelector('[data-choose-claim-file]'));
                if (attachment && !/\.(pdf|jpe?g|png|webp|heic|heif)$/i.test(attachment.name)) addError('items', `Rincian ${index + 1}: pilih file PDF atau gambar yang didukung.`, item.querySelector('[data-choose-claim-file]'));
                const currency = item.querySelector('[name$="[currency]"]')?.value || '';
                if (!['IDR', 'USD', 'SGD'].includes(currency)) addError(item.querySelector('[name$="[currency]"]')?.name || 'items', `Rincian ${index + 1}: mata uang tidak valid.`, item.querySelector('[name$="[currency]"]'));
                else if (selectedCurrency && selectedCurrency !== currency) addError('items', 'Satu pengajuan hanya dapat menggunakan satu mata uang.', item.querySelector('[name$="[currency]"]'));
                else selectedCurrency = currency;
            });
            if (selectedAttachments.reduce((total, file) => total + file.size, 0) > 50 * 1024 * 1024) addError('items', 'Total ukuran seluruh bukti nota maksimal 50 MB per pengajuan.', items.find((item) => item.querySelector('[data-claim-attachment-input]')?.files?.[0])?.querySelector('[data-choose-claim-file]'));
            if (scope === 'external_project') {
                const referenceRadios = form.querySelector('#claim-reference-field .d-flex');
                if (!['project', 'opportunity', 'none'].includes(reference)) addError('work_reference_type', 'Pilih jenis referensi pekerjaan.', referenceRadios);
                if (reference === 'project' && !value('project_id')) addError('project_id', 'Project wajib dipilih.', form.querySelector('#claim-project-field [data-opportunity-table-picker] button, #claim-project-field button, #claim-project-field select'));
                const opportunityId = value('opportunity_id');
                if (reference === 'opportunity' && !opportunityId) addError('opportunity_id', 'Opportunity wajib dipilih.', form.querySelector('#claim-opportunity-field [data-opportunity-table-picker] button, #claim-opportunity-field button, #claim-opportunity-field select'));
                else if (reference === 'opportunity' && opportunityId.length > 36) addError('opportunity_id', 'ID opportunity maksimal 36 karakter.', form.querySelector('#claim-opportunity-field [data-opportunity-table-picker] button, #claim-opportunity-field button, #claim-opportunity-field select'));
            }
            const purpose = value('purpose');
            if (purpose.length > 2000) addError('purpose', 'Keterangan umum maksimal 2.000 karakter.');
            const poNumber = value('po_number');
            if (poNumber.length > 100) addError('po_number', 'Nomor PO maksimal 100 karakter.');
            form.querySelectorAll('[name^="items["][name$="[receipt_date]"], [name^="items["][name$="[activity_date]"]').forEach((dateField) => {
                if (dateField.name.endsWith('[receipt_date]') && !dateField.value) addError(dateField.name, 'Tanggal nota wajib diisi.', dateField);
                else if (dateField.value && Number.isNaN(Date.parse(dateField.value))) addError(dateField.name, 'Format tanggal tidak valid.', dateField);
            });
        }
        if (type === 'document') {
            if (!value('title')) addError('title', 'Judul dokumen wajib diisi.');
            if (!value('company_id')) addError('company_id', 'Perusahaan internal wajib dipilih.');
            if (!value('document_type_id')) addError('document_type_id', 'Jenis dokumen wajib dipilih.');
            if (!value('document_date')) addError('document_date', 'Tanggal dokumen wajib diisi.');
            const upload = field('upload')?.files?.[0];
            if (field('upload') && !upload) addError('upload', 'File dokumen wajib dipilih.', field('upload'));
            if (upload && !['application/pdf', 'image/jpeg', 'image/png'].includes(upload.type)) addError('upload', 'File harus berupa PDF, JPG, atau PNG.', field('upload'));
            if (upload && upload.size > 50 * 1024 * 1024) addError('upload', 'Ukuran file maksimal 50 MB.', field('upload'));
            const amount = value('amount');
            if (amount) {
                const canonical = amount.includes(',') ? amount.replace(/\./g, '').replace(',', '.') : amount;
                if (!/^\d+(?:\.\d+)?$/.test(canonical)) addError('amount', 'Nominal harus berupa angka desimal yang valid.', field('amount'));
            }
            form.querySelectorAll('[name^="metadata["][required]').forEach((metadataField) => {
                if (!metadataField.value?.trim()) addError(metadataField.name, 'Metadata wajib diisi.', metadataField);
            });
            form.querySelectorAll('[data-metadata-type="numeric"], [data-metadata-type="numeric_decimal"]').forEach((metadataField) => {
                const value = metadataField.value.trim();
                if (!value) return;
                const canonical = value.replace(/\./g, '').replace(',', '.');
                const pattern = metadataField.dataset.metadataType === 'numeric' ? /^\d+$/ : /^\d+(?:\.\d+)?$/;
                if (!pattern.test(canonical)) addError(metadataField.name, metadataField.dataset.metadataType === 'numeric' ? 'Metadata harus berupa angka bulat dengan format Indonesia.' : 'Metadata harus berupa angka desimal dengan format Indonesia.', metadataField);
            });
        }
        if (type === 'document-type') {
            if (!value('name')) addError('name', 'Nama tipe dokumen wajib diisi.');
            else if (value('name').length > 255) addError('name', 'Nama tipe dokumen maksimal 255 karakter.');
            if (!value('code')) addError('code', 'Kode tipe dokumen wajib diisi.');
            else if (value('code').length > 100) addError('code', 'Kode tipe dokumen maksimal 100 karakter.');

            const allowedMetadataTypes = new Set(['string', 'text', 'numeric', 'numeric_decimal', 'date', 'datetime', 'boolean']);
            const metadataNames = new Set();
            form.querySelectorAll('[data-metadata-row]').forEach((row) => {
                const metadataName = row.querySelector('[data-metadata-name]');
                const metadataType = row.querySelector('[data-metadata-type]');
                const fieldName = metadataName?.value?.trim() || '';
                const fieldType = metadataType?.value || '';

                if (!fieldName) addError('metadata-field', 'Nama field metadata wajib diisi.', metadataName);
                else if (!/^[a-z][a-z0-9_]*$/.test(fieldName)) addError('metadata-field', 'Nama field hanya boleh berisi huruf kecil, angka, dan garis bawah.', metadataName);
                else if (metadataNames.has(fieldName)) addError('metadata-field', 'Nama field metadata tidak boleh sama.', metadataName);
                else metadataNames.add(fieldName);
                if (!allowedMetadataTypes.has(fieldType)) addError('metadata-type', 'Tipe input metadata tidak valid.', metadataType);
            });
        }
        if (type === 'leave-request') {
            form.querySelectorAll('[data-date-error], [data-document-error], [data-reason-error]').forEach((element) => {
                element.textContent = '';
                element.hidden = true;
                element.classList.remove('d-block');
            });
            const selectedType = form.querySelector('[name="type"]:checked')?.value || '';
            const dates = [...form.querySelectorAll('[data-date-list] input[type="date"]')];
            const values = dates.map((input) => input.value.trim()).filter(Boolean);
            const holidays = JSON.parse(form.dataset.holidays || '[]');
            const holidayDates = new Map(holidays.map((holiday) => [holiday.date, holiday]));
            if (!selectedType) addError('type', 'Jenis pengajuan wajib dipilih.', form.querySelector('[name="type"]'));
            dates.forEach((input) => { if (!input.value) addError(input.name, 'Tanggal pengajuan wajib diisi.', input); });
            if (values.length !== new Set(values).size) {
                const seen = new Set();
                const duplicate = dates.find((input) => input.value && (seen.has(input.value) || !seen.add(input.value)));
                addError(duplicate?.name || 'dates', 'Tanggal pengajuan tidak boleh duplikat.', duplicate || dates[0]);
            }
            if (selectedType === 'leave' && values.length > Number(form.dataset.remainingLeave || 0)) addError('dates', `Jumlah tanggal tidak boleh melebihi sisa kuota (${form.dataset.remainingLeave || 0} hari).`, dates[0]);
            const holidayInput = dates.find((input) => input.value && holidayDates.has(input.value));
            if (holidayInput) addError(holidayInput.name, `Tanggal merupakan hari libur (${holidayDates.get(holidayInput.value).name}) dan tidak dapat dipilih.`, holidayInput);
            if (!value('reason')) addError('reason', 'Alasan pengajuan wajib diisi.');
            if (value('reason').length > 2000) addError('reason', 'Alasan maksimal 2.000 karakter.');
            const file = field('supporting_document')?.files?.[0];
            if (selectedType === 'sick' && !file && !form.dataset.hasSupportingDocument) addError('supporting_document', 'Surat pendukung wajib diunggah untuk pengajuan sakit.', field('supporting_document'));
            if (file && (!/\.(pdf|jpe?g|png)$/i.test(file.name) || file.size > 10 * 1024 * 1024)) addError('supporting_document', 'Dokumen harus PDF/JPG/PNG dan maksimal 10 MB.', field('supporting_document'));
        }
        if (errors.length) {
            errors[0].target.scrollIntoView({ behavior: 'smooth', block: 'center' });
            errors[0].target.focus({ preventScroll: true });
            return false;
        }
        return true;
    };

    document.addEventListener('submit', (event) => {
        const form = event.target;
        if (form instanceof HTMLFormElement && form.dataset.clientValidation && form.dataset.confirmHandled !== 'true' && !validateWorkspaceForm(form)) {
            event.preventDefault();
            event.stopImmediatePropagation();
        }
    }, true);

    document.querySelectorAll('form').forEach((form) => {
        const method = form.querySelector('input[name="_method"]')?.value?.toUpperCase();
        if (method === 'DELETE' && !form.dataset.confirm) {
            const actionLabel = form.querySelector('button[type="submit"], button')?.textContent?.trim().toLowerCase();
            form.dataset.confirm = actionLabel?.includes('arsip')
                ? 'Data ini akan dipindahkan ke arsip. Lanjutkan?'
                : 'Data ini akan dihapus. Lanjutkan?';
        }
    });

    document.addEventListener('submit', (event) => {
        const form = event.target;
        if (!(form instanceof HTMLFormElement) || form.dataset.confirmHandled === 'true' || !form.dataset.confirm) return;
        if (form.dataset.confirmValue && event.submitter?.value !== form.dataset.confirmValue) return;

        // Keep destructive/submit confirmations effective even if the shared modal
        // is unavailable on a page (for example, during a partial layout render).
        if (!confirmModal) {
            event.preventDefault();
            event.stopImmediatePropagation();
            if (!window.confirm(form.dataset.confirm)) return;
            form.dataset.confirmHandled = 'true';
            form.requestSubmit(event.submitter || undefined);
            return;
        }

        event.preventDefault();
        event.stopImmediatePropagation();
        pendingConfirmForm = form;
        pendingConfirmSubmitter = event.submitter;
        confirmMessage.textContent = form.dataset.confirm;
        confirmSubmit.textContent = form.dataset.confirmAction || 'Lanjutkan';
        const hasOptionalComment = form.dataset.confirmComment === 'optional';
        confirmCommentWrap?.classList.toggle('d-none', !hasOptionalComment);
        if (confirmComment) confirmComment.value = '';
        confirmModal.show();
    }, true);

    confirmSubmit?.addEventListener('click', () => {
        if (!pendingConfirmForm) return;
        const form = pendingConfirmForm;
        pendingConfirmForm = null;
        form.dataset.confirmHandled = 'true';
        form.removeAttribute('onsubmit');
        const submitter = pendingConfirmSubmitter || form.querySelector('button[name][value], input[type="submit"][name][value]');
        if (submitter?.name && !form.querySelector(`[name="${CSS.escape(submitter.name)}"][data-workspace-confirm-value]`)) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = submitter.name;
            input.value = submitter.value;
            input.dataset.workspaceConfirmValue = 'true';
            form.append(input);
        }
        if (form.dataset.confirmComment === 'optional') {
            form.querySelector('input[name="comment"][data-workspace-confirm-value]')?.remove();
            const comment = confirmComment?.value?.trim() || '';
            if (comment) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'comment';
                input.value = comment;
                input.dataset.workspaceConfirmValue = 'true';
                form.append(input);
            }
        }
        confirmModal.hide();
        const beforeConfirmedSubmit = new CustomEvent('workspace:before-confirmed-submit', {
            cancelable: true,
            detail: { submitter, submit: () => form.submit() },
        });
        if (!form.dispatchEvent(beforeConfirmedSubmit)) {
            pendingConfirmSubmitter = null;
            return;
        }
        showLoading(formLoadingMessage(form));
        pendingConfirmSubmitter = null;
        form.submit();
    });

    document.addEventListener('submit', (event) => {
        const form = event.target;
        if (event.defaultPrevented) return;
        const confirmationWasSkipped = form instanceof HTMLFormElement && form.dataset.confirmValue && event.submitter?.value !== form.dataset.confirmValue;
        if (form instanceof HTMLFormElement && form.dataset.confirmHandled !== 'true' && (!form.dataset.confirm || confirmationWasSkipped)) showLoading(formLoadingMessage(form));
    });

    document.addEventListener('click', (event) => {
        const link = event.target.closest('a[href]');
        if (!link || event.defaultPrevented || link.dataset.workspaceNoLoading || link.target === '_blank' || link.dataset.workspacePreview || link.closest('.dropdown-menu') || link.getAttribute('href')?.startsWith('#')) return;
        if (link.origin === window.location.origin) showLoading('Memuat halaman...');
    });

    document.querySelectorAll('.workspace-permission-group').forEach((group) => {
        const items = [...group.querySelectorAll('[data-permission-item]')];
        const groupToggle = group.querySelector('[data-permission-group-toggle]');
        const groupCounter = group.querySelector('[data-permission-group-count]');
        const subgroups = [...group.querySelectorAll('.workspace-permission-subgroup')];
        const sync = () => {
            const selectedCount = items.filter((item) => item.checked).length;
            if (groupToggle) {
                groupToggle.checked = items.length > 0 && selectedCount === items.length;
                groupToggle.indeterminate = selectedCount > 0 && !groupToggle.checked;
            }
            if (groupCounter) groupCounter.textContent = `${selectedCount} dipilih`;
            subgroups.forEach((subgroup) => {
                const subgroupItems = [...subgroup.querySelectorAll('[data-permission-item]')];
                const subgroupToggle = subgroup.querySelector('[data-permission-subgroup-toggle]');
                const counter = subgroup.querySelector('.workspace-permission-subgroup-title em');
                const subgroupSelected = subgroupItems.filter((item) => item.checked).length;
                if (subgroupToggle) {
                    subgroupToggle.checked = subgroupItems.length > 0 && subgroupSelected === subgroupItems.length;
                    subgroupToggle.indeterminate = subgroupSelected > 0 && !subgroupToggle.checked;
                }
                if (counter) counter.textContent = `${subgroupSelected} dipilih`;
            });
        };
        const setChecked = (targets, checked) => {
            targets.forEach((item) => { item.checked = checked; });
            sync();
        };
        groupToggle?.addEventListener('change', () => setChecked(items, groupToggle.checked));
        subgroups.forEach((subgroup) => {
            const subgroupToggle = subgroup.querySelector('[data-permission-subgroup-toggle]');
            const subgroupItems = [...subgroup.querySelectorAll('[data-permission-item]')];
            subgroupToggle?.addEventListener('change', () => setChecked(subgroupItems, subgroupToggle.checked));
        });
        items.forEach((item) => item.addEventListener('change', sync));
        sync();
    });

    document.querySelectorAll('[data-searchable-multiselect]').forEach((wrapper) => {
        const search = wrapper.querySelector('[data-multiselect-search]');
        const select = wrapper.querySelector('[data-multiselect-options]');
        const count = wrapper.querySelector('[data-multiselect-count]');
        if (!search || !select) return;
        const update = () => {
            const query = search.value.trim().toLowerCase();
            [...select.options].forEach((option) => {
                option.hidden = query !== '' && !option.text.toLowerCase().includes(query);
            });
            if (count) count.textContent = `${[...select.selectedOptions].length} dipilih`;
        };
        search.addEventListener('input', update);
        select.addEventListener('change', update);
        update();
    });

    document.querySelectorAll('[data-searchable-dropdown]').forEach((wrapper) => {
        const search = wrapper.querySelector('[data-dropdown-search]');
        const label = wrapper.querySelector('[data-dropdown-label]');
        const items = [...wrapper.querySelectorAll('[data-dropdown-item]')];
        const update = () => {
            const selected = items.filter((item) => item.checked);
            const selectedLabels = selected.map((item) => item.dataset.dropdownValue || item.closest('[data-dropdown-option]')?.querySelector('span')?.textContent.trim() || item.value);
            if (label) {
                label.textContent = selectedLabels.length
                    ? (wrapper.dataset.dropdownSummary === 'count' ? `${selectedLabels.length} dipilih` : selectedLabels.join(', '))
                    : (wrapper.dataset.dropdownEmptyLabel || 'Pilih dari daftar');
                label.title = selectedLabels.join(', ');
            }
            const query = search?.value.trim().toLowerCase() || '';
            wrapper.querySelectorAll('[data-dropdown-option]').forEach((option) => {
                option.classList.toggle('is-filtered', query !== '' && !option.textContent.toLowerCase().includes(query));
            });
        };
        search?.addEventListener('input', update);
        items.forEach((item) => item.addEventListener('change', () => {
            if (wrapper.dataset.singleSelect === 'true' && item.checked) {
                items.forEach((other) => { if (other !== item) other.checked = false; });
            }
            update();
        }));
        update();
    });

    document.querySelectorAll('[data-document-company-select]').forEach((select) => {
        select.addEventListener('change', () => {
            if (window.Livewire) window.Livewire.dispatch('document-company-changed', { companyId: select.value || null });
        });
    });

    const formatLocalNumberInput = (input) => {
        const raw = input.value;
        const cursor = input.selectionStart ?? raw.length;
        const clean = raw.replace(/[^\d.,]/g, '');
        const type = input.dataset.metadataType || 'numeric_decimal';
        const commaIndex = clean.indexOf(',');
        let integer = commaIndex >= 0 ? clean.slice(0, commaIndex) : clean;
        const fraction = type === 'numeric_decimal' && commaIndex >= 0 ? clean.slice(commaIndex + 1).replace(/\D/g, '') : null;

        integer = integer.replace(/[.,]/g, '').replace(/^0+(?=\d)/, '') || (clean ? '0' : '');
        input.value = integer.replace(/\B(?=(\d{3})+(?!\d))/g, '.') + (fraction !== null ? `,${fraction}` : '');

        const digitsBeforeCursor = raw.slice(0, cursor).replace(/\D/g, '').length;
        let nextCursor = 0;
        let seenDigits = 0;
        while (nextCursor < input.value.length && seenDigits < digitsBeforeCursor) {
            if (/\d/.test(input.value[nextCursor])) seenDigits += 1;
            nextCursor += 1;
        }
        if (raw.slice(0, cursor).endsWith(',')) {
            const commaPosition = input.value.indexOf(',');
            if (commaPosition >= 0) nextCursor = commaPosition + 1;
        }
        input.setSelectionRange(nextCursor, nextCursor);
    };

    document.addEventListener('input', (event) => {
        if (event.target.matches?.('[data-local-decimal]')) formatLocalNumberInput(event.target);
    });
    document.addEventListener('blur', (event) => {
        if (event.target.matches?.('[data-local-decimal]')) formatLocalNumberInput(event.target);
    }, true);

    document.querySelectorAll('[data-tag-input]').forEach((wrapper) => {
        const hidden = wrapper.querySelector('[data-tag-value]');
        const entry = wrapper.querySelector('[data-tag-entry]');
        const chips = wrapper.querySelector('[data-tag-chips]');
        if (!hidden || !entry || !chips) return;
        let tags = hidden.value.split(',').map((tag) => tag.trim().toLowerCase()).filter(Boolean).filter((tag, index, values) => values.indexOf(tag) === index);
        const sync = () => {
            chips.replaceChildren();
            tags.forEach((tag, index) => {
                const chip = document.createElement('span');
                chip.className = 'workspace-tag-chip';
                chip.textContent = tag;
                const remove = document.createElement('button');
                remove.type = 'button';
                remove.className = 'workspace-tag-chip-remove';
                remove.setAttribute('aria-label', `Hapus tag ${tag}`);
                remove.textContent = '×';
                remove.addEventListener('click', () => { tags.splice(index, 1); sync(); });
                chip.append(remove);
                chips.append(chip);
            });
            hidden.value = tags.join(', ');
        };
        const add = () => {
            const value = entry.value.trim().replace(/,$/, '').toLowerCase();
            if (value && !tags.includes(value)) tags.push(value);
            entry.value = '';
            sync();
        };
        entry.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' || event.key === ',') { event.preventDefault(); add(); }
            if (event.key === 'Backspace' && !entry.value && tags.length) { tags.pop(); sync(); }
        });
        entry.addEventListener('blur', add);
        sync();
    });

    const companyEntityType = document.querySelector('select[name="entity_type"]');
    const companyClassification = document.querySelector('select[name="classification"]');
    const companyRoleControl = document.querySelector('[data-company-role-control]');
    const companyVisibilityControl = document.querySelector('[data-company-visibility-control]');
    const companyGroupRoleHint = document.querySelector('[data-company-group-role-hint]');
    const companyRoleTrigger = document.querySelector('[data-company-role-trigger]');
    const companyRoleHint = document.querySelector('[data-company-role-hint]');
    const companyParent = document.querySelector('[data-company-parent]');
    const companyVendorRoleLocked = Boolean(document.querySelector('[data-company-vendor-role][disabled]'));
    if (companyEntityType && (companyRoleControl || companyVisibilityControl)) {
        const syncCompanyRoleField = () => {
            const isGroup = companyEntityType.value === 'group';
            const isExternal = companyClassification?.value === 'external';
            const rolesReady = Boolean(companyEntityType.value && companyClassification?.value);
            companyRoleControl?.classList.toggle('d-none', isGroup);
            companyVisibilityControl?.classList.toggle('d-none', isGroup);
            companyGroupRoleHint?.classList.toggle('d-none', !isGroup);
            companyRoleTrigger && (companyRoleTrigger.disabled = isGroup || !rolesReady);
            if (companyRoleHint) companyRoleHint.textContent = isGroup ? 'Role Group ditetapkan otomatis berdasarkan jenis entitas.' : rolesReady ? 'Pilih satu atau beberapa peran bisnis yang sesuai.' : 'Isi jenis entitas dan klasifikasi terlebih dahulu untuk memilih peran bisnis.';
            companyRoleControl?.querySelectorAll('input[name="roles[]"][data-dropdown-item]').forEach((input) => {
                const isOperatingCompany = input.value === 'operating_company';
                const isVendorRole = input.value === 'vendor';
                input.disabled = isGroup || !rolesReady || (isExternal && isOperatingCompany) || (companyVendorRoleLocked && isVendorRole);
                input.closest('[data-dropdown-option]')?.classList.toggle('is-disabled', input.disabled);
                if ((isGroup || !rolesReady || (isExternal && isOperatingCompany)) && !(companyVendorRoleLocked && isVendorRole)) input.checked = false;
            });
            if (companyParent) {
                companyParent.disabled = !companyClassification?.value;
                [...companyParent.options].forEach((option) => {
                    if (!option.value) return;
                    const allowed = Boolean(companyClassification?.value) && option.dataset.parentClassification === companyClassification.value;
                    option.disabled = !allowed;
                    if (!allowed && option.selected) companyParent.value = '';
                });
            }
            companyVisibilityControl?.querySelectorAll('input[name="visibility_company_ids[]"]').forEach((input) => {
                input.disabled = isGroup;
                if (isGroup) input.checked = false;
            });
        };
        companyEntityType.addEventListener('change', syncCompanyRoleField);
        companyClassification?.addEventListener('change', syncCompanyRoleField);
        syncCompanyRoleField();
    }

    const companyIndex = document.querySelector('[data-company-index]');
    if (companyIndex) {
        const companySearch = companyIndex.querySelector('[data-company-search]');
        const companyTable = companyIndex.querySelector('table');
        const companyPagination = companyIndex.querySelector('[data-company-pagination]');
        let companyRequest;

        const loadCompanies = async (targetUrl, updateHistory = true) => {
            const url = new URL(targetUrl, window.location.origin);
            const displayUrl = new URL(url);
            displayUrl.searchParams.delete('partial');
            url.searchParams.set('partial', '1');
            companyRequest?.abort();
            companyRequest = new AbortController();
            companyIndex.classList.add('is-loading');

            try {
                const response = await fetch(url, {
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    signal: companyRequest.signal,
                });
                if (!response.ok) throw new Error('Gagal memuat daftar perusahaan.');
                const payload = await response.json();
                const head = companyTable?.querySelector('thead');
                const body = companyTable?.querySelector('tbody');
                if (head) head.outerHTML = payload.head;
                if (body) body.outerHTML = payload.body;
                if (companyPagination) companyPagination.innerHTML = payload.pagination;
                if (companySearch) {
                    ['q', 'entity_type', 'classification', 'status', 'limit'].forEach((name) => {
                        const field = companySearch.querySelector(`[name="${name}"]`);
                        if (field) field.value = displayUrl.searchParams.get(name) || (name === 'limit' ? '10' : '');
                    });
                    const selectedRoles = displayUrl.searchParams.getAll('roles[]');
                    companySearch.querySelectorAll('[name="roles[]"]').forEach((field) => { field.checked = selectedRoles.includes(field.value); });
                    companySearch.querySelectorAll('[data-searchable-dropdown]').forEach((dropdown) => dropdown.querySelector('[data-dropdown-item]')?.dispatchEvent(new Event('change')));
                }
                if (updateHistory) window.history.pushState({}, '', displayUrl.pathname + displayUrl.search);
            } catch (error) {
                if (error.name !== 'AbortError') console.error(error);
            } finally {
                companyIndex.classList.remove('is-loading');
                hideLoading();
            }
        };

        companySearch?.addEventListener('submit', (event) => {
            event.preventDefault();
            const url = new URL(window.location.href);
            url.searchParams.delete('page');
            const formData = new FormData(companySearch);
            ['q', 'entity_type', 'classification', 'status', 'limit'].forEach((name) => {
                const value = formData.get(name)?.toString().trim() || '';
                value ? url.searchParams.set(name, value) : url.searchParams.delete(name);
            });
            url.searchParams.delete('roles[]');
            formData.getAll('roles[]').forEach((role) => url.searchParams.append('roles[]', role.toString()));
            loadCompanies(url);
        });

        companyPagination?.addEventListener('click', (event) => {
            const link = event.target.closest('a[href]');
            if (!link) return;
            event.preventDefault();
            loadCompanies(link.href);
        });

        companyTable?.addEventListener('click', (event) => {
            const link = event.target.closest('[data-company-sort]');
            if (!link) return;
            event.preventDefault();
            loadCompanies(link.href);
        });

        window.addEventListener('popstate', () => loadCompanies(window.location.href, false));
    }

    confirmModalElement?.addEventListener('hidden.bs.modal', () => {
        pendingConfirmForm = null;
        pendingConfirmSubmitter = null;
    });

    const metadataEditor = document.querySelector('[data-metadata-editor]');
    if (metadataEditor) {
        const metadataList = metadataEditor.querySelector('[data-metadata-list]');
        const metadataEmpty = metadataEditor.querySelector('[data-metadata-empty]');
        const metadataJson = metadataEditor.querySelector('[data-metadata-json]');
        const metadataAdd = metadataEditor.querySelector('[data-metadata-add]');
        let draggedRow = null;

        const readMetadata = () => {
            try {
                const value = JSON.parse(metadataJson.value || '[]');
                if (Array.isArray(value)) return value;
                return Object.entries(value).map(([field_name, config]) => ({ field_name, ...(typeof config === 'object' ? config : { type: config }) }));
            } catch { return []; }
        };

        const syncMetadata = () => {
            const fields = [...metadataList.querySelectorAll('[data-metadata-row]')].map((row) => ({
                field_name: row.querySelector('[data-metadata-name]').value.trim(),
                type: row.querySelector('[data-metadata-type]').value,
                mandatory: row.querySelector('[data-metadata-required]').checked,
            })).filter((field) => field.field_name !== '');
            metadataJson.value = JSON.stringify(fields);
            metadataEmpty.hidden = metadataList.children.length > 0;
        };

        const createMetadataRow = (field = {}) => {
            const row = document.createElement('div');
            row.className = 'workspace-metadata-row';
            row.draggable = true;
            row.dataset.metadataRow = 'true';
            const types = [['string', 'Teks'], ['text', 'Teks panjang'], ['numeric', 'Angka'], ['numeric_decimal', 'Angka desimal'], ['date', 'Tanggal'], ['datetime', 'Tanggal & waktu'], ['boolean', 'Ya/Tidak']];
            row.innerHTML = `<span class="workspace-metadata-drag" title="Tarik untuk mengurutkan" aria-label="Tarik untuk mengurutkan">⋮⋮</span><div class="workspace-metadata-row-field"><label>Nama field</label><input type="text" data-metadata-name placeholder="contoh: nomor_invoice"></div><div class="workspace-metadata-row-field"><label>Tipe input</label><select data-metadata-type>${types.map(([value, label]) => `<option value="${value}">${label}</option>`).join('')}</select></div><label class="workspace-metadata-required"><input type="checkbox" data-metadata-required> Wajib</label><button type="button" class="workspace-metadata-remove" data-metadata-remove aria-label="Hapus metadata">&times;</button>`;
            row.querySelector('[data-metadata-name]').value = field.field_name || '';
            row.querySelector('[data-metadata-type]').value = field.type || 'string';
            row.querySelector('[data-metadata-required]').checked = field.mandatory !== false;
            row.querySelectorAll('input, select').forEach((input) => input.addEventListener('input', syncMetadata));
            row.querySelector('[data-metadata-remove]').addEventListener('click', () => { row.remove(); syncMetadata(); });
            row.addEventListener('dragstart', (event) => { draggedRow = row; row.classList.add('is-dragging'); event.dataTransfer.effectAllowed = 'move'; });
            row.addEventListener('dragend', () => { row.classList.remove('is-dragging'); draggedRow = null; syncMetadata(); });
            row.addEventListener('dragover', (event) => {
                event.preventDefault();
                if (!draggedRow || draggedRow === row) return;
                const box = row.getBoundingClientRect();
                metadataList.insertBefore(draggedRow, event.clientY < box.top + box.height / 2 ? row : row.nextSibling);
            });
            return row;
        };

        readMetadata().forEach((field) => metadataList.append(createMetadataRow(field)));
        syncMetadata();
        metadataAdd.addEventListener('click', () => { const row = createMetadataRow(); metadataList.append(row); row.querySelector('[data-metadata-name]').focus(); syncMetadata(); });
        metadataEditor.closest('form')?.addEventListener('submit', syncMetadata);
    }

    document.querySelectorAll('[data-workspace-theme-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const nextTheme = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
            document.documentElement.dataset.theme = nextTheme;
            window.localStorage.setItem(themeStorageKey, nextTheme);
        });
    });

    document.querySelectorAll('[data-claim-image-viewer]').forEach((viewer) => {
        const frame = viewer.querySelector('iframe');
        const toolbar = viewer.querySelector('[data-claim-image-zoom-toolbar]');
        const levelLabel = viewer.querySelector('[data-claim-zoom-level]');
        if (!frame || !toolbar || !levelLabel) return;

        let image = null;
        let stage = null;
        let scale = 1;
        const applyScale = (nextScale) => {
            if (!image || !stage) return;
            if (!image.naturalWidth || !image.naturalHeight) return;
            scale = Math.min(4, Math.max(1, Math.round(nextScale * 100) / 100));
            levelLabel.textContent = `${Math.round(scale * 100)}%`;
            const document = frame.contentDocument;
            const viewportWidth = Math.max(1, frame.clientWidth - 24);
            const viewportHeight = Math.max(1, frame.clientHeight - 24);
            const fitScale = Math.min(viewportWidth / image.naturalWidth, viewportHeight / image.naturalHeight);
            const imageWidth = Math.max(1, Math.round(image.naturalWidth * fitScale * scale));
            const imageHeight = Math.max(1, Math.round(image.naturalHeight * fitScale * scale));

            if (!document?.body) return;
            document.body.style.margin = '0';
            document.body.style.overflow = 'auto';
            stage.style.display = 'grid';
            stage.style.placeItems = 'center';
            stage.style.width = `${Math.max(viewportWidth, imageWidth)}px`;
            stage.style.height = `${Math.max(viewportHeight, imageHeight)}px`;
            image.style.maxWidth = 'none';
            image.style.maxHeight = 'none';
            image.style.width = `${imageWidth}px`;
            image.style.height = `${imageHeight}px`;
            image.style.objectFit = 'fill';
        };
        const fitToViewport = () => { if (image) applyScale(scale); };

        const loadImageDocument = () => {
            try {
                image = frame.contentDocument?.querySelector('img') ?? null;
            } catch {
                image = null;
            }
            toolbar.hidden = !image;
            if (image) {
                stage = frame.contentDocument.createElement('div');
                stage.append(image);
                frame.contentDocument.body.replaceChildren(stage);
            } else {
                stage = null;
            }
            scale = 1;
            requestAnimationFrame(() => applyScale(1));
        };
        frame.addEventListener('load', loadImageDocument);
        try {
            if (frame.contentDocument?.readyState === 'complete') loadImageDocument();
        } catch {
            toolbar.hidden = true;
        }
        viewer.querySelector('[data-claim-zoom-in]')?.addEventListener('click', () => applyScale(scale + 0.25));
        viewer.querySelector('[data-claim-zoom-out]')?.addEventListener('click', () => applyScale(scale - 0.25));
        viewer.querySelector('[data-claim-zoom-reset]')?.addEventListener('click', () => applyScale(1));
        viewer.closest('.modal')?.addEventListener('shown.bs.modal', fitToViewport);
        window.addEventListener('resize', fitToViewport);
    });

    const appShell = document.querySelector('.workspace-app-shell');
    const sidebarToggle = document.querySelector('[data-workspace-sidebar-toggle]');
    const desktopQuery = window.matchMedia('(min-width: 992px)');

    const setSidebarState = (collapsed, persist = true) => {
        if (!appShell) return;
        appShell.classList.toggle('sidebar-collapsed', collapsed && desktopQuery.matches);
        if (sidebarToggle) {
            const label = collapsed ? 'Tampilkan sidebar' : 'Perkecil sidebar';
            sidebarToggle.setAttribute('aria-label', label);
            sidebarToggle.setAttribute('title', label);
        }
        if (persist) window.localStorage.setItem(sidebarStorageKey, collapsed ? '1' : '0');
    };

    if (appShell) {
        setSidebarState(window.localStorage.getItem(sidebarStorageKey) === '1', false);
        sidebarToggle?.addEventListener('click', () => {
            setSidebarState(!appShell.classList.contains('sidebar-collapsed'));
        });
        desktopQuery.addEventListener?.('change', () => {
            setSidebarState(window.localStorage.getItem(sidebarStorageKey) === '1', false);
        });
    }
});
