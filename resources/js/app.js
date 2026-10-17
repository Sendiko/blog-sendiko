const initializeArticleEditor = () => {
    const form = document.querySelector('[data-article-form]');

    if (!form || form.dataset.uploadInitialized) {
        return;
    }

    form.dataset.uploadInitialized = 'true';
    const imageInput = form.querySelector('[data-inline-image]');
    const uploadButton = form.querySelector('[data-image-upload]');
    const body = form.querySelector('#body');
    const status = form.querySelector('[data-upload-status]');

    uploadButton.addEventListener('click', () => imageInput.click());

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