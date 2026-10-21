import './bootstrap';
import * as bootstrap from 'bootstrap';

const themeStorageKey = 'basilindo-workspace-theme';
const sidebarStorageKey = 'basilindo-workspace-sidebar-collapsed';
const savedTheme = window.localStorage.getItem(themeStorageKey);
if (savedTheme === 'dark' || savedTheme === 'light') {
    document.documentElement.dataset.theme = savedTheme;
}

document.addEventListener('DOMContentLoaded', () => {
    const enhanceFloatingFields = () => {
        document.querySelectorAll('label.form-label').forEach((label) => {
            const field = label.nextElementSibling;
            if (!field || !field.matches('input:not([type="file"]).form-control, select.form-select, textarea.form-control')) return;
            if (label.parentElement?.classList.contains('workspace-floating-field')) return;

            const wrapper = document.createElement('div');
            wrapper.className = 'workspace-floating-field workspace-floating-field--auto';
            label.parentNode.insertBefore(wrapper, label);
            wrapper.append(field, label);

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

    enhanceFloatingFields();

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

    const floatingObserver = new MutationObserver(enhanceFloatingFields);
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
    const previewTitle = document.querySelector('#workspacePreviewTitle');
    const previewFallback = document.querySelector('[data-workspace-preview-fallback]');
    const previewModal = previewModalElement ? bootstrap.Modal.getOrCreateInstance(previewModalElement) : null;

    document.addEventListener('click', (event) => {
        const trigger = event.target.closest('[data-workspace-preview]');
        if (!trigger || !previewModal || !previewStage) return;

        event.preventDefault();
        const url = trigger.dataset.previewUrl || trigger.href;
        const mime = trigger.dataset.previewType || '';
        const title = trigger.dataset.previewTitle || 'Preview dokumen';
        previewTitle.textContent = title;
        previewFallback.href = url;
        previewStage.replaceChildren();
        showLoading('Memuat dokumen...');

        if (mime === 'application/pdf') {
            const frame = document.createElement('iframe');
            frame.src = `${url}#toolbar=1&view=FitH`;
            frame.title = `Preview PDF: ${title}`;
            frame.addEventListener('load', hideLoading, { once: true });
            previewStage.append(frame);
        } else if (mime.startsWith('image/')) {
            const image = document.createElement('img');
            image.src = url;
            image.alt = title;
            image.addEventListener('load', hideLoading, { once: true });
            previewStage.append(image);
        } else {
            const message = document.createElement('div');
            message.className = 'workspace-preview-loading';
            message.textContent = 'Format file ini tidak dapat ditampilkan langsung. Gunakan tombol buka di tab baru.';
            previewStage.append(message);
            hideLoading();
        }

        previewModal.show();
    });

    previewModalElement?.addEventListener('hidden.bs.modal', () => {
        previewStage?.replaceChildren();
        if (previewFallback) previewFallback.removeAttribute('href');
    });

    const confirmModalElement = document.querySelector('#workspaceConfirmModal');
    const confirmMessage = document.querySelector('[data-workspace-confirm-message]');
    const confirmSubmit = document.querySelector('[data-workspace-confirm-submit]');
    const confirmModal = confirmModalElement ? bootstrap.Modal.getOrCreateInstance(confirmModalElement) : null;
    let pendingConfirmForm = null;
    const loadingOverlay = document.querySelector('[data-workspace-loading]');
    const loadingMessage = loadingOverlay?.querySelector('[data-workspace-loading-message]');
    const showLoading = (message = 'Memproses...') => {
        if (!loadingOverlay) return;
        if (loadingMessage) loadingMessage.textContent = message;
        loadingOverlay.classList.add('is-visible');
        loadingOverlay.setAttribute('aria-hidden', 'false');
    };
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
        if (!(form instanceof HTMLFormElement) || form.dataset.confirmHandled === 'true' || !form.dataset.confirm || !confirmModal) return;

        event.preventDefault();
        event.stopImmediatePropagation();
        pendingConfirmForm = form;
        confirmMessage.textContent = form.dataset.confirm;
        confirmSubmit.textContent = form.dataset.confirmAction || 'Lanjutkan';
        confirmModal.show();
    }, true);

    confirmSubmit?.addEventListener('click', () => {
        if (!pendingConfirmForm) return;
        const form = pendingConfirmForm;
        pendingConfirmForm = null;
        form.dataset.confirmHandled = 'true';
        form.removeAttribute('onsubmit');
        confirmModal.hide();
        showLoading(formLoadingMessage(form));
        form.submit();
    });

    document.addEventListener('submit', (event) => {
        const form = event.target;
        if (form instanceof HTMLFormElement && form.dataset.confirmHandled !== 'true' && !form.dataset.confirm) showLoading(formLoadingMessage(form));
    });

    document.addEventListener('click', (event) => {
        const link = event.target.closest('a[href]');
        if (!link || event.defaultPrevented || link.target === '_blank' || link.dataset.workspacePreview || link.closest('.dropdown-menu') || link.getAttribute('href')?.startsWith('#')) return;
        if (link.origin === window.location.origin) showLoading('Memuat halaman...');
    });

    document.querySelectorAll('[data-permission-group-toggle]').forEach((toggle) => {
        const group = toggle.closest('.workspace-permission-group');
        const items = [...(group?.querySelectorAll('[data-permission-item]') ?? [])];
        const sync = () => {
            toggle.checked = items.length > 0 && items.every((item) => item.checked);
            toggle.indeterminate = items.some((item) => item.checked) && !toggle.checked;
        };
        toggle.addEventListener('change', () => items.forEach((item) => { item.checked = toggle.checked; }));
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
                label.textContent = selectedLabels.length ? selectedLabels.join(', ') : (wrapper.dataset.dropdownEmptyLabel || 'Pilih dari daftar');
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

    document.querySelectorAll('[data-local-decimal]').forEach((input) => {
        input.addEventListener('input', () => { input.value = input.value.replace(/[^\d.,]/g, ''); });
        input.addEventListener('blur', () => {
            const value = input.value.trim();
            if (!value) return;
            const canonical = value.includes(',') ? value.replace(/\./g, '').replace(',', '.') : value;
            if (!/^\d+(?:\.\d+)?$/.test(canonical)) return;
            const [integer, fraction] = canonical.split('.');
            input.value = integer.replace(/\B(?=(\d{3})+(?!\d))/g, '.') + (fraction ? `,${fraction}` : '');
        });
    });

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
