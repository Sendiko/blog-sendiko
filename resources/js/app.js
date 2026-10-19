const initializeArticleEditor = () => {
    const form = document.querySelector('[data-article-form]');

    if (!form || form.dataset.uploadInitialized) {
        return;
    }

    form.dataset.uploadInitialized = 'true';
    const imageInput = form.querySelector('[data-inline-image]');
    const uploadButton = form.querySelector('[data-image-upload]');
    const body = form.querySelector('#body');
    const characterCount = form.querySelector('[data-character-count]');
    const status = form.querySelector('[data-upload-status]');
    const editorTabs = form.querySelectorAll('[data-editor-tab]');
    const editorPanes = form.querySelectorAll('[data-editor-pane]');
    const previewContent = form.querySelector('[data-preview-content]');
    const previewPlaceholder = form.querySelector('[data-preview-placeholder]');
    const previewError = form.querySelector('[data-preview-error]');
    let previewController;
    let previewTimer;

    uploadButton.addEventListener('click', () => imageInput.click());

    const updateCharacterCount = () => {
        characterCount.textContent = `${body.value.length.toLocaleString()} / ${body.maxLength.toLocaleString()} characters`;
    };

    updateCharacterCount();

    const updatePreview = async () => {
        previewController?.abort();

        if (!body.value.trim()) {
            previewContent.replaceChildren();
            previewPlaceholder.hidden = false;
            previewError.hidden = true;
            return;
        }

        previewController = new AbortController();
        previewPlaceholder.hidden = true;
        previewError.hidden = true;

        try {
            const response = await fetch(form.dataset.markdownPreviewUrl, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': form.querySelector('[name="_token"]').value,
                },
                body: JSON.stringify({ body: body.value }),
                credentials: 'same-origin',
                signal: previewController.signal,
            });
            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message ?? 'The Markdown preview could not be rendered.');
            }

            previewContent.innerHTML = result.html;
        } catch (error) {
            if (error instanceof DOMException && error.name === 'AbortError') {
                return;
            }

            previewError.textContent = error instanceof Error
                ? error.message
                : 'The Markdown preview could not be rendered.';
            previewError.hidden = false;
        }
    };

    const setEditorView = (view) => {
        body.required = view === 'write';

        editorTabs.forEach((tab) => {
            const selected = tab.dataset.editorTab === view;
            tab.setAttribute('aria-selected', String(selected));
            tab.classList.toggle('bg-zinc-100', selected);
            tab.classList.toggle('text-zinc-900', selected);
            tab.classList.toggle('dark:bg-zinc-800', selected);
            tab.classList.toggle('dark:text-white', selected);
            tab.classList.toggle('text-zinc-600', !selected);
            tab.classList.toggle('dark:text-zinc-300', !selected);
        });

        editorPanes.forEach((pane) => {
            pane.hidden = pane.dataset.editorPane !== view;
        });

        if (view === 'preview') {
            updatePreview();
        }
    };

    editorTabs.forEach((tab) => {
        tab.addEventListener('click', () => setEditorView(tab.dataset.editorTab));
    });

    body.addEventListener('input', () => {
        updateCharacterCount();

        if (form.querySelector('[data-editor-tab][aria-selected="true"]')?.dataset.editorTab !== 'preview') {
            return;
        }

        window.clearTimeout(previewTimer);
        previewTimer = window.setTimeout(updatePreview, 250);
    });

    imageInput.addEventListener('change', async () => {
        const image = imageInput.files?.[0];

        if (!image) {
            return;
        }

        uploadButton.disabled = true;
        status.textContent = 'Uploading photo…';

        try {
            const data = new FormData();
            data.append('image', image);

            const response = await fetch(form.dataset.imageUploadUrl, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': form.querySelector('[name="_token"]').value,
                },
                body: data,
                credentials: 'same-origin',
            });
            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.errors?.image?.[0] ?? result.message ?? 'The photo could not be uploaded.');
            }

            const alt = result.alt.replace(/[\r\n]+/g, ' ').replace(/[\]\\]/g, '\\$&');
            const markdown = `![${alt}](${result.url})`;
            const start = body.selectionStart;
            const end = body.selectionEnd;
            const prefix = start > 0 && body.value[start - 1] !== '\n' ? '\n\n' : '';
            const suffix = end < body.value.length && body.value[end] !== '\n' ? '\n\n' : '';

            body.setRangeText(`${prefix}${markdown}${suffix}`, start, end, 'end');
            updateCharacterCount();
            body.focus();
            status.textContent = 'Photo uploaded and added to your article.';
        } catch (error) {
            status.textContent = error instanceof Error ? error.message : 'The photo could not be uploaded.';
        } finally {
            uploadButton.disabled = false;
            imageInput.value = '';
        }
    });
};

document.addEventListener('DOMContentLoaded', initializeArticleEditor);
document.addEventListener('livewire:navigated', initializeArticleEditor);