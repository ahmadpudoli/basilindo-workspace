import * as pdfjsLib from 'pdfjs-dist';
import pdfWorkerUrl from 'pdfjs-dist/build/pdf.worker.min.mjs?url';

pdfjsLib.GlobalWorkerOptions.workerSrc = pdfWorkerUrl;

const renderPdfPages = async (container) => {
    const status = container.querySelector('[data-pdf-status]');
    const pagesHost = container.querySelector('[data-pdf-pages]');
    const loadingTask = pdfjsLib.getDocument({ url: container.dataset.pdfUrl, withCredentials: true });
    const pdf = await loadingTask.promise;

    status.textContent = `${pdf.numPages} halaman PDF ditemukan. Menyiapkan lampiran…`;

    for (let pageNumber = 1; pageNumber <= pdf.numPages; pageNumber += 1) {
        status.textContent = `Menyiapkan halaman ${pageNumber} dari ${pdf.numPages}…`;
        const page = await pdf.getPage(pageNumber);
        const baseViewport = page.getViewport({ scale: 1 });
        const scale = Math.min(2, 1400 / baseViewport.width, 1900 / baseViewport.height);
        const viewport = page.getViewport({ scale });
        const canvas = document.createElement('canvas');
        const context = canvas.getContext('2d', { alpha: false });

        canvas.width = Math.ceil(viewport.width);
        canvas.height = Math.ceil(viewport.height);
        await page.render({ canvas, canvasContext: context, viewport }).promise;

        const imageBlob = await new Promise((resolve) => canvas.toBlob(resolve, 'image/png'));
        if (!imageBlob) throw new Error(`Halaman ${pageNumber} gagal disiapkan untuk cetak.`);

        const sheet = document.createElement('section');
        sheet.className = 'pdf-page-sheet';
        if (container.dataset.firstAttachment === 'true' && pageNumber === 1) sheet.classList.add('is-first-attachment-page');

        const heading = document.createElement('header');
        heading.className = 'pdf-page-heading';
        const label = document.createElement('strong');
        label.textContent = `Bukti rincian #${container.dataset.lineNumber} · ${container.dataset.category}`;
        const pageCount = document.createElement('span');
        pageCount.textContent = `Halaman ${pageNumber} dari ${pdf.numPages}`;
        heading.append(label, pageCount);

        const image = document.createElement('img');
        image.className = 'pdf-page-image';
        image.alt = `${container.dataset.description} · halaman ${pageNumber}`;
        image.src = URL.createObjectURL(imageBlob);
        sheet.append(heading, image);
        pagesHost.append(sheet);

        canvas.width = 0;
        canvas.height = 0;
        page.cleanup();
    }

    await loadingTask.destroy();
    status.remove();
};

document.addEventListener('DOMContentLoaded', async () => {
    const printButton = document.querySelector('[data-print-document]');
    const pdfContainers = [...document.querySelectorAll('[data-pdf-print]')];
    for (const container of pdfContainers) {
        try {
            await renderPdfPages(container);
        } catch (error) {
            const status = container.querySelector('[data-pdf-status]');
            if (status) {
                status.classList.add('pdf-page-error');
                status.textContent = 'PDF tidak berhasil dimuat untuk pratinjau. Buka file ini dan cetak secara terpisah.';
                const link = document.createElement('a');
                link.href = container.dataset.pdfUrl;
                link.target = '_blank';
                link.rel = 'noopener';
                link.textContent = ' Buka file PDF';
                status.append(link);
            }
            console.error('Gagal merender PDF lampiran reimbursement.', error);
        }
    }
    if (!printButton) return;
    printButton.disabled = false;
    printButton.textContent = 'Cetak dokumen';
    printButton.addEventListener('click', async () => {
        printButton.disabled = true;
        await Promise.all([...document.images].map((image) => image.decode().catch(() => undefined)));
        printButton.disabled = false;
        window.print();
    });
});
