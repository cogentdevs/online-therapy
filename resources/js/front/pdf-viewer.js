import { GlobalWorkerOptions, getDocument } from 'pdfjs-dist';
import pdfWorkerUrl from 'pdfjs-dist/build/pdf.worker.min.mjs?url';

GlobalWorkerOptions.workerSrc = pdfWorkerUrl;

const minimumZoom = 0.5;
const maximumZoom = 2.5;
const zoomStep = 0.25;

const initializePdfViewer = async (viewer) => {
    const pdfUrl = viewer.dataset.pdfUrl;
    const canvas = viewer.querySelector('[data-pdf-canvas]');
    const canvasArea = viewer.querySelector('[data-pdf-canvas-area]');
    const loadingState = viewer.querySelector('[data-pdf-loading]');
    const errorState = viewer.querySelector('[data-pdf-error]');
    const currentPageOutput = viewer.querySelector('[data-pdf-current-page]');
    const totalPagesOutput = viewer.querySelector('[data-pdf-total-pages]');
    const zoomOutput = viewer.querySelector('[data-pdf-zoom]');
    const previousButton = viewer.querySelector('[data-pdf-previous]');
    const nextButton = viewer.querySelector('[data-pdf-next]');
    const zoomOutButton = viewer.querySelector('[data-pdf-zoom-out]');
    const zoomInButton = viewer.querySelector('[data-pdf-zoom-in]');
    const fitWidthButton = viewer.querySelector('[data-pdf-fit-width]');
    const printButton = viewer.querySelector('[data-pdf-print]');
    const requestedInitialPage = Number.parseInt(viewer.dataset.initialPage || '1', 10);

    if (!pdfUrl || !canvas || !canvasArea) {
        return;
    }

    let pdfDocument;
    let currentPage = 1;
    let zoom = 1;
    let renderTask;
    let resizeTimer;

    const updateToolbar = () => {
        viewer.dataset.currentPage = String(currentPage);
        viewer.dataset.totalPages = String(pdfDocument.numPages);
        currentPageOutput.textContent = String(currentPage);
        totalPagesOutput.textContent = String(pdfDocument.numPages);
        zoomOutput.textContent = `${Math.round(zoom * 100)}%`;
        previousButton.disabled = currentPage <= 1;
        nextButton.disabled = currentPage >= pdfDocument.numPages;
        zoomOutButton.disabled = zoom <= minimumZoom;
        zoomInButton.disabled = zoom >= maximumZoom;
        viewer.dispatchEvent(new CustomEvent('front-pdf-page-change', {
            detail: { currentPage, totalPages: pdfDocument.numPages },
        }));
    };

    const renderPage = async () => {
        renderTask?.cancel();

        const page = await pdfDocument.getPage(currentPage);
        const unscaledViewport = page.getViewport({ scale: 1 });
        const availableWidth = Math.max(canvasArea.clientWidth - 32, 240);
        const fitScale = availableWidth / unscaledViewport.width;
        const displayScale = fitScale * zoom;
        const pixelRatio = window.devicePixelRatio || 1;
        const viewport = page.getViewport({ scale: displayScale * pixelRatio });

        canvas.width = Math.floor(viewport.width);
        canvas.height = Math.floor(viewport.height);
        canvas.style.width = `${Math.floor(viewport.width / pixelRatio)}px`;
        canvas.style.height = `${Math.floor(viewport.height / pixelRatio)}px`;

        renderTask = page.render({
            canvasContext: canvas.getContext('2d'),
            viewport,
        });

        try {
            await renderTask.promise;
        } catch (error) {
            if (error?.name !== 'RenderingCancelledException') {
                throw error;
            }
        }

        updateToolbar();
    };

    const printDocument = async () => {
        printButton.disabled = true;
        const printFrame = document.createElement('iframe');
        printFrame.className = 'front-pdf-viewer__print-frame';
        printFrame.setAttribute('aria-hidden', 'true');
        document.body.append(printFrame);

        try {
            const images = [];

            for (let pageNumber = 1; pageNumber <= pdfDocument.numPages; pageNumber += 1) {
                const page = await pdfDocument.getPage(pageNumber);
                const viewport = page.getViewport({ scale: 1.5 });
                const printCanvas = document.createElement('canvas');
                printCanvas.width = Math.floor(viewport.width);
                printCanvas.height = Math.floor(viewport.height);
                await page.render({ canvasContext: printCanvas.getContext('2d'), viewport }).promise;
                images.push(printCanvas.toDataURL('image/png'));
            }

            const printDocument = printFrame.contentDocument;
            printDocument.open();
            printDocument.write(`<!doctype html><html><head><title>PDF</title><style>@page{margin:0}body{margin:0}.page{display:block;page-break-after:always;width:100%}.page:last-child{page-break-after:auto}</style></head><body>${images.map((image) => `<img class="page" src="${image}" alt="">`).join('')}</body></html>`);
            printDocument.close();

            await Promise.all([...printDocument.images].map((image) => image.decode()));
            printFrame.contentWindow.focus();
            printFrame.contentWindow.print();
        } finally {
            printButton.disabled = false;
            window.setTimeout(() => printFrame.remove(), 1000);
        }
    };

    try {
        pdfDocument = await getDocument({ url: pdfUrl }).promise;
        currentPage = Number.isInteger(requestedInitialPage)
            ? Math.min(Math.max(requestedInitialPage, 1), pdfDocument.numPages)
            : 1;
        loadingState.hidden = true;
        canvasArea.hidden = false;
        await renderPage();

        previousButton.addEventListener('click', async () => {
            if (currentPage > 1) {
                currentPage -= 1;
                await renderPage();
            }
        });
        nextButton.addEventListener('click', async () => {
            if (currentPage < pdfDocument.numPages) {
                currentPage += 1;
                await renderPage();
            }
        });
        zoomOutButton.addEventListener('click', async () => {
            zoom = Math.max(minimumZoom, zoom - zoomStep);
            await renderPage();
        });
        zoomInButton.addEventListener('click', async () => {
            zoom = Math.min(maximumZoom, zoom + zoomStep);
            await renderPage();
        });
        fitWidthButton.addEventListener('click', async () => {
            zoom = 1;
            await renderPage();
        });
        printButton?.addEventListener('click', printDocument);

        window.addEventListener('resize', () => {
            window.clearTimeout(resizeTimer);
            resizeTimer = window.setTimeout(renderPage, 150);
        });
    } catch (error) {
        loadingState.hidden = true;
        canvasArea.hidden = true;
        errorState.hidden = false;
    }
};

document.querySelectorAll('[data-pdf-viewer]').forEach(initializePdfViewer);
