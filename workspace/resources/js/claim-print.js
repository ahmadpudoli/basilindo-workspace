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
        label.textContent = container.dataset.documentLabel || `Bukti rincian #${container.dataset.lineNumber} · ${container.dataset.category}`;
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
    const paperSizeSelect = document.querySelector('[data-paper-size]');
    const paperSizes = {
        a4: { width: 210, height: 297 },
        letter: { width: 215.9, height: 279.4 },
        legal: { width: 215.9, height: 355.6 },
        a3: { width: 297, height: 420 },
    };
    const pageSizeStyle = document.createElement('style');
    pageSizeStyle.id = 'claim-print-page-size';
    document.head.append(pageSizeStyle);
    const printPageHost = document.querySelector('[data-print-pages]');
    const firstPrintPage = printPageHost?.querySelector('[data-print-first-page]');
    const flowBlocks = firstPrintPage ? [...firstPrintPage.querySelectorAll('[data-print-flow-block]')] : [];

    const paginateFlowBlocks = () => {
        if (!printPageHost || !firstPrintPage || flowBlocks.length === 0) return;

        printPageHost.querySelectorAll(':scope > [data-generated-print-page]').forEach((page) => page.remove());
        flowBlocks.forEach((block) => block.remove());
        let activePage = firstPrintPage;

        for (const block of flowBlocks) {
            activePage.append(block);
            if (activePage.scrollHeight <= activePage.clientHeight + 2) continue;

            block.remove();
            activePage = document.createElement('main');
            activePage.className = 'paper generated-print-page';
            activePage.dataset.generatedPrintPage = '';
            printPageHost.append(activePage);
            activePage.append(block);
        }
    };

    const updatePaperSize = () => {
        const size = paperSizes[paperSizeSelect?.value] || paperSizes.a4;
        const root = document.documentElement;
        const mobileViewport = window.matchMedia('(max-width: 600px)').matches;
        const availableWidth = Math.max(0, window.innerWidth - (mobileViewport ? 24 : 64));
        const previewWidth = Math.min((size.width * 96) / 25.4, availableWidth, 900);
        const previewMargin = (previewWidth * 14) / size.width;
        root.style.setProperty('--paper-width', `${size.width}mm`);
        root.style.setProperty('--paper-height', `${size.height}mm`);
        root.style.setProperty('--paper-ratio', `${size.width} / ${size.height}`);
        root.style.setProperty('--paper-preview-width', `${previewWidth}px`);
        root.style.setProperty('--paper-preview-height', `${(previewWidth * size.height) / size.width}px`);
        root.style.setProperty('--paper-preview-margin', `${previewMargin}px`);
        root.style.setProperty('--paper-print-height', `${size.height - 28}mm`);
        pageSizeStyle.textContent = `@page { size: ${size.width}mm ${size.height}mm; margin: 14mm; }`;
        paginateFlowBlocks();
    };

    paperSizeSelect?.addEventListener('change', updatePaperSize);
    window.addEventListener('resize', updatePaperSize, { passive: true });
    updatePaperSize();

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
