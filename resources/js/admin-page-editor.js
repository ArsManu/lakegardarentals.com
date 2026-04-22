import Quill from 'quill';
import 'quill/dist/quill.snow.css';
import Alpine from 'alpinejs';

const toolbarOptions = [
    [{ header: [1, 2, 3, 4, false] }],
    ['bold', 'italic', 'underline', 'strike'],
    ['blockquote', 'code', 'code-block'],
    [{ list: 'ordered' }, { list: 'bullet' }],
    [{ indent: '-1' }, { indent: '+1' }],
    [{ align: [] }],
    [{ script: 'sub' }, { script: 'super' }],
    ['link'],
    ['clean'],
];

const selectedFlexUploads = new Map();

/**
 * Push the latest Quill HTML into each hidden textarea before the form is posted.
 * Without this, the last edit can be missing from the submitted value in edge cases.
 */
function syncAllQuillTextareas() {
    document.querySelectorAll('.js-quill-field').forEach((wrap) => {
        const host = wrap.querySelector('.js-quill-host');
        const textarea = wrap.querySelector('textarea.js-quill-source');
        if (!host || !textarea) {
            return;
        }
        const quill = Quill.find(host);
        if (quill) {
            textarea.value = quill.getSemanticHTML();
        }
    });
}

document.addEventListener('submit', syncAllQuillTextareas, true);

function enhanceToolbar(quill, host, sync) {
    const toolbar = quill.getModule('toolbar');
    if (!toolbar?.container) {
        return;
    }

    const cleanBtn = toolbar.container.querySelector('button.ql-clean');
    if (cleanBtn) {
        cleanBtn.setAttribute('title', 'Remove formatting');
        cleanBtn.setAttribute('aria-label', 'Remove formatting');
    }

    const span = document.createElement('span');
    span.className = 'ql-formats';
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'ql-html';
    btn.textContent = 'HTML';
    btn.setAttribute('title', 'View or edit HTML source');
    btn.setAttribute('aria-label', 'View or edit HTML source');
    span.appendChild(btn);
    toolbar.container.appendChild(span);

    let sourceTextarea = null;
    let sourceOpen = false;
    const container = quill.container;

    const closeSource = () => {
        if (!sourceOpen || !sourceTextarea) {
            return;
        }
        const html = sourceTextarea.value;
        quill.clipboard.dangerouslyPasteHTML(html && html.trim() !== '' ? html : '<p><br></p>');
        sourceTextarea.remove();
        sourceTextarea = null;
        container.style.display = '';
        sourceOpen = false;
        btn.textContent = 'HTML';
        btn.setAttribute('title', 'View or edit HTML source');
        btn.setAttribute('aria-label', 'View or edit HTML source');
        sync();
    };

    btn.addEventListener('click', () => {
        if (!sourceOpen) {
            sourceTextarea = document.createElement('textarea');
            sourceTextarea.className =
                'ql-html-source block w-full resize-y border-0 border-t border-stone-200 bg-stone-50 p-3 font-mono text-sm leading-relaxed text-stone-800 outline-none';
            sourceTextarea.style.minHeight = `${Math.max(200, container.offsetHeight)}px`;
            sourceTextarea.value = quill.getSemanticHTML();
            container.style.display = 'none';
            host.appendChild(sourceTextarea);
            sourceTextarea.focus();
            sourceOpen = true;
            btn.textContent = 'Done';
            btn.setAttribute('title', 'Apply HTML and return to visual editor');
            btn.setAttribute('aria-label', 'Apply HTML and return to visual editor');
            return;
        }
        closeSource();
    });

    const form = host.closest('form');
    if (form) {
        form.addEventListener(
            'submit',
            () => {
                if (sourceOpen) {
                    closeSource();
                } else {
                    sync();
                }
            },
            true,
        );
    }
}

function attachQuillToTextarea(textarea) {
    if (!textarea || textarea.dataset.quillBound === '1') {
        return;
    }
    const wrap = textarea.closest('.js-quill-field');
    if (!wrap) {
        return;
    }
    const host = wrap.querySelector('.js-quill-host');
    if (!host) {
        return;
    }
    textarea.dataset.quillBound = '1';
    const quill = new Quill(host, {
        theme: 'snow',
        modules: {
            toolbar: toolbarOptions,
        },
    });
    const initial = textarea.value || '';
    if (initial) {
        quill.clipboard.dangerouslyPasteHTML(initial);
    }
    const form = wrap.closest('form');
    const sync = () => {
        textarea.value = quill.getSemanticHTML();
        if (form) {
            form.dispatchEvent(new Event('input', { bubbles: true }));
        }
    };
    quill.on('text-change', sync);
    sync();
    enhanceToolbar(quill, host, sync);
}

/**
 * Quill fields inside Alpine-driven flex blocks get their textarea value from x-init
 * after this runs; binding too early shows an empty editor until full page reload.
 */
function scanQuillFields(root = document) {
    root.querySelectorAll('textarea.js-quill-source').forEach((textarea) => {
        if (textarea.closest('[data-flex-editor]')) {
            return;
        }
        attachQuillToTextarea(textarea);
    });
}

function previewFile(event) {
    const file = event?.target?.files?.[0];
    return file ? URL.createObjectURL(file) : '';
}

function flexUploadMapKey(kind, uploadKey) {
    return `${kind}:${uploadKey}`;
}

function rememberFlexUpload(event, kind, uploadKey) {
    const file = event?.target?.files?.[0];
    const key = flexUploadMapKey(kind, uploadKey);

    if (!file) {
        selectedFlexUploads.delete(key);
        return '';
    }

    selectedFlexUploads.set(key, file);

    return URL.createObjectURL(file);
}

function setFlexUploadName(input, kind, index) {
    if (!input) {
        return;
    }

    if (index === '' || index === null || typeof index === 'undefined') {
        input.removeAttribute('name');
        return;
    }

    if (kind === 'image') {
        input.name = `flex_block_image_file[${index}]`;
        return;
    }
    if (kind === 'left') {
        input.name = `flex_block_left_file[${index}]`;
        return;
    }
    if (kind === 'right') {
        input.name = `flex_block_right_file[${index}]`;
        return;
    }

    input.removeAttribute('name');
}

function prepareFlexUploads(form) {
    form.querySelectorAll('input[type="file"][data-flex-upload-kind]').forEach((input) => {
        const kind = input.dataset.flexUploadKind || '';
        const index = input.dataset.flexUploadIndex || '';
        setFlexUploadName(input, kind, index);
    });
}

function formHasRememberedFlexUploads(form) {
    return Array.from(form.querySelectorAll('input[type="file"][data-flex-upload-kind][data-flex-upload-key]')).some((input) => {
        const kind = input.dataset.flexUploadKind || '';
        const uploadKey = input.dataset.flexUploadKey || '';

        return selectedFlexUploads.has(flexUploadMapKey(kind, uploadKey));
    });
}

function buildFlexUploadFormData(form) {
    const formData = new FormData(form);

    form.querySelectorAll('input[type="file"][data-flex-upload-kind][data-flex-upload-key]').forEach((input) => {
        const kind = input.dataset.flexUploadKind || '';
        const index = input.dataset.flexUploadIndex || '';
        const uploadKey = input.dataset.flexUploadKey || '';
        const file = selectedFlexUploads.get(flexUploadMapKey(kind, uploadKey));

        if (!file || index === '') {
            return;
        }

        if (kind === 'image') {
            formData.set(`flex_block_image_file[${index}]`, file, file.name);
            return;
        }
        if (kind === 'left') {
            formData.set(`flex_block_left_file[${index}]`, file, file.name);
            return;
        }
        if (kind === 'right') {
            formData.set(`flex_block_right_file[${index}]`, file, file.name);
        }
    });

    return formData;
}

async function submitFormWithRememberedFlexUploads(form) {
    const response = await fetch(form.action, {
        method: 'POST',
        body: buildFlexUploadFormData(form),
        credentials: 'same-origin',
        headers: {
            Accept: 'text/html,application/xhtml+xml',
            'X-Requested-With': 'XMLHttpRequest',
        },
        redirect: 'manual',
    });

    // Do not use document.write() here: it replaces the document without a real navigation,
    // so Vite/CSS and Quill often fail to re-init (broken toolbar). Follow Laravel's redirect instead.
    const status = response.status;
    if (status >= 300 && status < 400) {
        const loc = response.headers.get('Location');
        if (loc) {
            window.location.assign(new URL(loc, window.location.href).href);
            return;
        }
        window.location.reload();
        return;
    }

    if (response.ok) {
        window.location.reload();
        return;
    }

    const html = await response.text();
    document.open();
    document.write(html);
    document.close();
}

function newFlexBlockId() {
    if (typeof crypto !== 'undefined' && typeof crypto.randomUUID === 'function') {
        try {
            return crypto.randomUUID();
        } catch {
            /* non-secure context or unsupported */
        }
    }
    return 'fb-'.concat(Date.now().toString(36), '-', Math.random().toString(36).slice(2, 11));
}

function defaultFlexBlock(type) {
    const id = newFlexBlockId();
    switch (type) {
        case 'split_media':
            return {
                _id: id,
                type: 'split_media',
                layout: 'image_left',
                image_path: '',
                image_alt: '',
                heading: '',
                body_html: '',
            };
        case 'full_bleed_image':
            return {
                _id: id,
                type: 'full_bleed_image',
                image_path: '',
                image_alt: '',
                caption: '',
            };
        case 'two_images':
            return {
                _id: id,
                type: 'two_images',
                left_path: '',
                left_alt: '',
                right_path: '',
                right_alt: '',
            };
        case 'rich_text':
            return {
                _id: id,
                type: 'rich_text',
                heading: '',
                html: '',
            };
        default:
            return {
                _id: id,
                type: 'rich_text',
                heading: '',
                html: '',
            };
    }
}

function normalizeFlexBlock(raw) {
    if (!raw || typeof raw !== 'object') {
        return defaultFlexBlock('rich_text');
    }
    const merged = {
        ...defaultFlexBlock(raw.type || 'rich_text'),
        ...raw,
        _id: raw._id || newFlexBlockId(),
    };
    merged.type = raw.type || 'rich_text';
    merged.image_path = raw.image_path || raw.image_url || '';
    merged.left_path = raw.left_path || raw.left_url || '';
    merged.right_path = raw.right_path || raw.right_url || '';
    return merged;
}

const createFlexEditor = (initial) => ({
        items: Array.isArray(initial) ? initial.map((b) => normalizeFlexBlock(b)) : [],

        init() {
            this.$nextTick(() => {
                document.dispatchEvent(new CustomEvent('page-flex-block-added'));
            });
        },

        storageUrl(path) {
            if (!path) {
                return '';
            }
            if (path.startsWith('http://') || path.startsWith('https://')) {
                return path;
            }
            return `/storage/${String(path).replace(/^\/+/, '')}`;
        },

        previewFile,
        rememberFlexUpload,

        setUploadName(event, kind, index) {
            setFlexUploadName(event?.target, kind, index);
        },

        add(type) {
            this.items.push(defaultFlexBlock(type));
            this.$nextTick(() => {
                document.dispatchEvent(new CustomEvent('page-flex-block-added'));
            });
        },

        remove(index) {
            this.items.splice(index, 1);
        },

        moveUp(index) {
            if (index < 1) {
                return;
            }
            const list = this.items;
            [list[index - 1], list[index]] = [list[index], list[index - 1]];
            this.$nextTick(() => document.dispatchEvent(new CustomEvent('page-flex-block-added')));
        },

        moveDown(index) {
            const list = this.items;
            if (index >= list.length - 1) {
                return;
            }
            [list[index], list[index + 1]] = [list[index + 1], list[index]];
            this.$nextTick(() => document.dispatchEvent(new CustomEvent('page-flex-block-added')));
        },
});

window.flexEditor = createFlexEditor;

document.addEventListener('alpine:init', () => {
    Alpine.data('flexEditor', createFlexEditor);
});

function formSnapshotExcludingFiles(form) {
    const data = new FormData(form);
    const parts = [];
    for (const [k, v] of data.entries()) {
        if (v instanceof File) {
            continue;
        }
        parts.push(
            `${encodeURIComponent(k)}=${encodeURIComponent(
                v === null || v === undefined ? '' : String(v),
            )}`,
        );
    }
    return parts.sort().join('&');
}

function formHasFileSelection(form) {
    return Array.from(form.querySelectorAll('input[type="file"]')).some(
        (i) => i.files && i.files.length > 0,
    );
}

/**
 * Disables the primary save button on CMS forms until a field (or file) actually changes.
 */
function initAdminDirtySaveForms() {
    document.querySelectorAll('form[data-admin-dirty-form]').forEach((form) => {
        const submitBtn = form.querySelector('button.js-primary-save');
        if (!submitBtn || form.dataset.dirtyFormBound === '1') {
            return;
        }
        form.dataset.dirtyFormBound = '1';
        const baseline = formSnapshotExcludingFiles(form);
        const recompute = () => {
            const hasFile = formHasFileSelection(form);
            const dirty = hasFile || formSnapshotExcludingFiles(form) !== baseline;
            submitBtn.disabled = !dirty;
        };
        form.addEventListener('input', recompute, true);
        form.addEventListener('change', recompute, true);
        recompute();
    });
}

document.addEventListener('DOMContentLoaded', () => {
    scanQuillFields();
    requestAnimationFrame(() => initAdminDirtySaveForms());
    document.querySelectorAll('form').forEach((form) => {
        if (!form.querySelector('[data-flex-editor]')) {
            return;
        }
        form.addEventListener('submit', async (event) => {
            prepareFlexUploads(form);

            if (!formHasRememberedFlexUploads(form)) {
                return;
            }

            event.preventDefault();

            try {
                await submitFormWithRememberedFlexUploads(form);
            } catch (error) {
                console.error('Flex upload submit failed, falling back to normal submit.', error);
                form.submit();
            }
        });
    });
});

document.addEventListener('page-flex-block-added', () => {
    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            document.querySelectorAll('[data-flex-editor] textarea.js-quill-source').forEach((textarea) => {
                attachQuillToTextarea(textarea);
            });
            document
                .querySelectorAll('form[data-admin-dirty-form][data-dirty-form-bound="1"]')
                .forEach((form) => {
                    form.dispatchEvent(new Event('input', { bubbles: true }));
                });
        });
    });
});

document.addEventListener('alpine:initialized', () => {
    requestAnimationFrame(() => {
        scanQuillFields();
        initAdminDirtySaveForms();
    });
});

window.initAdminPageEditor = {
    scanQuillFields,
    attachQuillToTextarea,
    syncAllQuillTextareas,
};
