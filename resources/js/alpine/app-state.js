const renderTasks = {}; // Store active render tasks outside Alpine to avoid Proxy issues

// Render queue system for controlled concurrency
const MAX_CONCURRENT_RENDERS = 3;
let activeRenders = 0;
let renderQueue = [];
let pageHeightCache = 0; // Cached page height for O(1) scroll calculations

export default () => ({
    // Estado Geral da UI
    isLoaded: true, // Já inicia carregado pois o script inline resolveu o flicker
    darkMode: localStorage.getItem('color-theme') === 'dark' || document.documentElement.classList.contains('dark'),
    sidebarOpen: !document.documentElement.classList.contains('sidebar-closed'),

    showReader: false,
    showDetails: false,
    showCreate: false,
    showSubmissions: false,
    activeTab: 'todos',
    saveTimeout: null,
    newsSeen: false,
    favoriteBookIds: window.initialFavoriteBookIds || [],

    isFavorited(bookId) {
        return this.favoriteBookIds.includes(bookId);
    },

    // Estado do Leitor de PDF
    selectedBook: null,
    pageNum: 1,
    totalPages: 0,
    pdfScale: 1.0,
    loading: false,
    pdfError: null,
    progressSaved: false,
    viewMode: 'single', // 'single', 'double', 'scroll'
    isFullscreen: false,
    pageObserver: null,

    // Toast Notification
    toast: { show: false, message: '', type: 'success' },

    showToast(message, type = 'success') {
        this.toast.show = true;
        this.toast.message = message;
        this.toast.type = type;
        setTimeout(() => this.toast.show = false, 3000);
    },

    init() {
        // Signal CSS that Alpine is ready (removes opacity mask)
        document.documentElement.classList.add('alpine-ready');

        this.$watch('darkMode', val => {
            localStorage.setItem('color-theme', val ? 'dark' : 'light');
            if (val) document.documentElement.classList.add('dark');
            else document.documentElement.classList.remove('dark');
        });

        this.$watch('sidebarOpen', val => {
            localStorage.setItem('sidebarOpen', val);
            if (!val) document.documentElement.classList.add('sidebar-closed');
            else document.documentElement.classList.remove('sidebar-closed');
        });

        document.addEventListener('fullscreenchange', () => {
            this.isFullscreen = !!document.fullscreenElement;
        });

        const flashMessage = document.body.dataset.flashMessage;
        const flashType = document.body.dataset.flashType || 'success';

        if (flashMessage) {
            this.$nextTick(() => this.showToast(flashMessage, flashType));
        }

        // Reage à mudança de modo de visualização
        this.$watch('viewMode', value => {
            if (!window._pdfState.doc) return;

            // Pequeno delay para garantir que x-show atualizou o DOM
            setTimeout(() => {
                if (value === 'scroll') {
                    this.renderAllPages();
                } else {
                    this.renderPage(this.pageNum);
                }
            }, 50);
        });
    },

    toggleTheme() { this.darkMode = !this.darkMode; },

    async markNewsSeen() {
        if (this.newsSeen) return;
        this.newsSeen = true;
        try {
            await fetch('/api/mark-news-seen', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
        } catch (e) { console.error('Erro ao marcar como visto:', e); }
    },

    openDetails(book) {
        this.selectedBook = book;
        this.showDetails = true;
    },

    async openReader(book) {
        this.selectedBook = book;
        this.showReader = true;
        this.loading = true;
        window._pdfState.doc = null;
        window._pdfState.numPages = 0;
        window._pdfState.renderedPages = [];
        this.pageNum = 1;
        this.totalPages = 0;
        this.pdfScale = 1.0;
        this.pdfError = null;
        this.progressSaved = false;
        this.viewMode = 'single';
        this.isFullscreen = false;

        // Restore saved preferences (page, viewMode, zoom)
        try {
            const res = await fetch(`/api/reading-progress/${book.id}`).then(r => r.json());
            if (res.progress) {
                if (res.progress.current_page > 1) {
                    this.pageNum = res.progress.current_page;
                }
                if (res.progress.view_mode && ['single', 'double', 'scroll'].includes(res.progress.view_mode)) {
                    this.viewMode = res.progress.view_mode;
                }
                if (res.progress.pdf_scale && res.progress.pdf_scale >= 0.5 && res.progress.pdf_scale <= 3.0) {
                    this.pdfScale = parseFloat(res.progress.pdf_scale);
                }
            }
        } catch (e) { console.error("Erro ao buscar progresso:", e); }

        this.loadPdf(`/storage/${book.file_path}`);
    },

    async closeReader() {
        // Salva progresso antes de fechar
        if (window._pdfState.doc && this.selectedBook) {
            await this.saveProgressNow(this.pageNum, this.totalPages);
        }

        // Sair do fullscreen se estiver
        if (document.fullscreenElement) {
            document.exitFullscreen();
        }

        // Clean up observer
        if (this.pageObserver) {
            this.pageObserver.disconnect();
            this.pageObserver = null;
        }

        // Cancel all active renders and clear queue
        Object.keys(renderTasks).forEach(key => {
            if (renderTasks[key]) {
                renderTasks[key].cancel();
                delete renderTasks[key];
            }
        });
        renderQueue = [];
        activeRenders = 0;

        this.showReader = false;
        window._pdfState.doc = null;
        window._pdfState.numPages = 0;
        window._pdfState.renderedPages = [];
        this.totalPages = 0;
        this.selectedBook = null;
        this.pdfError = null;
    },

    async loadPdf(url) {
        try {
            this.pdfError = null;

            if (!window.pdfjsLib) {
                throw new Error("PDF.js não está disponível. Recarregue a página.");
            }

            const loadingTask = window.pdfjsLib.getDocument(url);
            const pdfDoc = await loadingTask.promise;

            // Armazena fora do Alpine para evitar Proxy
            window._pdfState.doc = pdfDoc;
            window._pdfState.numPages = pdfDoc.numPages;
            this.totalPages = pdfDoc.numPages;

            if (this.viewMode === 'scroll') {
                this.renderAllPages();
            } else {
                this.renderPage(this.pageNum);
            }
        } catch (error) {
            console.error("Erro ao carregar PDF:", error);
            this.loading = false;
            this.pdfError = error.message || "Não foi possível carregar o PDF.";
        }
    },

    retryLoadPdf() {
        if (this.selectedBook) {
            this.loading = true;
            this.pdfError = null;
            this.loadPdf(`/storage/${this.selectedBook.file_path}`);
        }
    },

    async renderPage(num) {
        const pdfDoc = window._pdfState.doc;
        if (!pdfDoc) return;

        // Validar número da página
        num = Math.max(1, Math.min(num, pdfDoc.numPages));

        this.loading = true;
        try {
            // Cancel previous render on main canvas
            if (renderTasks['pdf-canvas']) {
                renderTasks['pdf-canvas'].cancel();
                delete renderTasks['pdf-canvas'];
            }

            // Renderiza página principal com suporte a HiDPI
            const page = await pdfDoc.getPage(num);
            const canvas = document.getElementById('pdf-canvas');
            if (!canvas) {
                throw new Error("Canvas não encontrado");
            }
            const ctx = canvas.getContext('2d');
            const dpr = window.devicePixelRatio || 1;
            const viewport = page.getViewport({ scale: this.pdfScale });

            // Canvas interno em resolução HiDPI, visual no tamanho CSS
            canvas.width = Math.round(viewport.width * dpr);
            canvas.height = Math.round(viewport.height * dpr);
            canvas.style.width = `${Math.round(viewport.width)}px`;
            canvas.style.height = `${Math.round(viewport.height)}px`;
            ctx.setTransform(dpr, 0, 0, dpr, 0, 0);

            const renderTask = page.render({ canvasContext: ctx, viewport: viewport });
            renderTasks['pdf-canvas'] = renderTask;

            await renderTask.promise;
            delete renderTasks['pdf-canvas'];

            // Se modo duplo, renderiza segunda página
            if (this.viewMode === 'double' && num + 1 <= pdfDoc.numPages) {
                // Pequeno delay para garantir que o elemento canvas-2 esteja visível (x-show)
                await new Promise(resolve => setTimeout(resolve, 50));

                // Cancel previous render on second canvas
                if (renderTasks['pdf-canvas-2']) {
                    renderTasks['pdf-canvas-2'].cancel();
                    delete renderTasks['pdf-canvas-2'];
                }

                const page2 = await pdfDoc.getPage(num + 1);
                const canvas2 = document.getElementById('pdf-canvas-2');
                if (canvas2) {
                    const ctx2 = canvas2.getContext('2d');
                    const viewport2 = page2.getViewport({ scale: this.pdfScale });
                    canvas2.width = Math.round(viewport2.width * dpr);
                    canvas2.height = Math.round(viewport2.height * dpr);
                    canvas2.style.width = `${Math.round(viewport2.width)}px`;
                    canvas2.style.height = `${Math.round(viewport2.height)}px`;
                    ctx2.setTransform(dpr, 0, 0, dpr, 0, 0);

                    const renderTask2 = page2.render({ canvasContext: ctx2, viewport: viewport2 });
                    renderTasks['pdf-canvas-2'] = renderTask2;

                    await renderTask2.promise;
                    delete renderTasks['pdf-canvas-2'];
                }
            }

            this.loading = false;
            this.pageNum = num;

            // Scroll para o topo do container em visualização paginada
            if (this.viewMode !== 'scroll') {
                const container = document.getElementById('pdf-container');
                if (container) container.scrollTop = 0;
            }

            this.updateProgress(num, pdfDoc.numPages);
        } catch (error) {
            if (error.name === 'RenderingCancelledException') {
                // Ignore cancelled errors
                return;
            }
            console.error("Erro na renderização:", error);
            this.loading = false;
            this.pdfError = `Erro ao renderizar página ${num}: ${error.message}`;
        }
    },

    // --- Render Queue System ---
    enqueueRender(wrapper, pageNum) {
        // Don't add duplicates
        if (renderQueue.some(item => item.pageNum === pageNum)) return;

        renderQueue.push({ wrapper, pageNum });
        // Sort by distance from current page (closer = higher priority)
        renderQueue.sort((a, b) => Math.abs(a.pageNum - this.pageNum) - Math.abs(b.pageNum - this.pageNum));
        this.processRenderQueue();
    },

    dequeueRender(pageNum) {
        renderQueue = renderQueue.filter(item => item.pageNum !== pageNum);
    },

    async processRenderQueue() {
        while (renderQueue.length > 0 && activeRenders < MAX_CONCURRENT_RENDERS) {
            const item = renderQueue.shift();
            if (!item) break;

            // Skip if wrapper was removed from DOM or already rendered
            if (!item.wrapper.isConnected) continue;
            const canvas = item.wrapper.querySelector('canvas');
            if (canvas && canvas.dataset.rendered === 'true') continue;

            activeRenders++;
            // Fire and forget — don't await, let multiple run concurrently
            this.renderSinglePageOnScroll(item.wrapper, item.pageNum).finally(() => {
                activeRenders--;
                // Process next in queue after one finishes
                this.processRenderQueue();
            });
        }
    },

    async renderAllPages() {
        const pdfDoc = window._pdfState.doc;
        if (!pdfDoc) return;

        this.loading = true;
        const container = document.getElementById('pdf-scroll-container');
        if (!container) return;

        // Clean up previous state
        if (this.pageObserver) {
            this.pageObserver.disconnect();
            this.pageObserver = null;
        }

        // Cancel all active render tasks
        Object.keys(renderTasks).forEach(key => {
            if (renderTasks[key]) {
                renderTasks[key].cancel();
                delete renderTasks[key];
            }
        });
        renderQueue = [];
        activeRenders = 0;

        container.innerHTML = '';
        window._pdfState.renderedPages = [];

        try {
            // Get first page dimensions for placeholders
            const page1 = await pdfDoc.getPage(1);
            const viewport1 = page1.getViewport({ scale: this.pdfScale });
            const pageWidth = Math.round(viewport1.width);
            const pageHeight = Math.round(viewport1.height);
            const gap = 24; // mb-6 = 1.5rem = 24px
            pageHeightCache = pageHeight + gap;

            // Build ALL placeholders at once via DocumentFragment (single reflow)
            const fragment = document.createDocumentFragment();

            for (let i = 1; i <= pdfDoc.numPages; i++) {
                const wrapper = document.createElement('div');
                wrapper.id = `pdf-page-${i}`;
                wrapper.dataset.page = i;
                wrapper.className = 'pdf-page-wrapper shadow-2xl rounded-lg bg-white mb-6 mx-auto relative';
                wrapper.style.width = `${pageWidth}px`;
                wrapper.style.height = `${pageHeight}px`;
                wrapper.style.display = 'block';
                wrapper.style.overflow = 'hidden';

                // Lightweight placeholder with page number
                wrapper.innerHTML = `<div class="absolute inset-0 flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-300 dark:text-gray-600 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                </div>`;

                fragment.appendChild(wrapper);
            }

            container.appendChild(fragment);

            // Root MUST be the scrollable parent (#pdf-container), not this non-scrolling child
            const scrollRoot = document.getElementById('pdf-container');
            this.pageObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    const wrapper = entry.target;
                    const pageNum = parseInt(wrapper.dataset.page);

                    if (entry.isIntersecting) {
                        this.enqueueRender(wrapper, pageNum);
                    } else {
                        this.dequeueRender(pageNum);
                        this.unloadPage(wrapper);
                    }
                });
            }, {
                root: scrollRoot,
                rootMargin: '800px',
                threshold: 0
            });

            // Observe all wrappers
            const wrappers = container.querySelectorAll('.pdf-page-wrapper');
            wrappers.forEach(w => this.pageObserver.observe(w));

            this.loading = false;

            // Restore scroll position and force-render current page
            // Flag prevents handlePdfScroll from saving intermediate positions during restore
            this._isRestoring = true;
            requestAnimationFrame(() => {
                this.goToPage(this.pageNum);
                const currentWrapper = document.getElementById(`pdf-page-${this.pageNum}`);
                if (currentWrapper) {
                    this.renderSinglePageOnScroll(currentWrapper, this.pageNum);
                }
                // Release after scroll animation settles
                setTimeout(() => { this._isRestoring = false; }, 600);
            });

        } catch (error) {
            console.error('Erro lazy load:', error);
            this.loading = false;
            this.pdfError = error.message;
        }
    },

    async renderSinglePageOnScroll(wrapper, pageNum) {
        let canvas = wrapper.querySelector('canvas');

        // Already rendered
        if (canvas && canvas.dataset.rendered === 'true') return;

        // Already rendering — verify task is still active
        if (wrapper.dataset.rendering === 'true') {
            const canvasId = `pdf-canvas-scroll-${pageNum}`;
            if (renderTasks[canvasId]) return; // Legit in-progress
            delete wrapper.dataset.rendering; // Stale state, allow retry
        }

        wrapper.dataset.rendering = 'true';

        try {
            const pdfDoc = window._pdfState.doc;
            if (!pdfDoc) return;

            const page = await pdfDoc.getPage(pageNum);
            const dpr = window.devicePixelRatio || 1;
            const viewport = page.getViewport({ scale: this.pdfScale });
            const w = Math.round(viewport.width);
            const h = Math.round(viewport.height);

            // Create canvas on demand (not as placeholder)
            if (!canvas) {
                canvas = document.createElement('canvas');
                canvas.className = 'block';
                wrapper.innerHTML = ''; // Remove placeholder text
                wrapper.appendChild(canvas);
            }

            const canvasId = `pdf-canvas-scroll-${pageNum}`;
            canvas.id = canvasId;

            // HiDPI: canvas interno em resolução alta, visual no tamanho CSS
            canvas.width = Math.round(w * dpr);
            canvas.height = Math.round(h * dpr);
            canvas.style.width = `${w}px`;
            canvas.style.height = `${h}px`;

            // Update wrapper size if this page differs from page 1
            if (parseInt(wrapper.style.width) !== w || parseInt(wrapper.style.height) !== h) {
                wrapper.style.width = `${w}px`;
                wrapper.style.height = `${h}px`;
            }

            // Cancel previous render on this canvas
            if (renderTasks[canvasId]) {
                renderTasks[canvasId].cancel();
                delete renderTasks[canvasId];
            }

            const ctx = canvas.getContext('2d');
            ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
            const renderTask = page.render({ canvasContext: ctx, viewport });
            renderTasks[canvasId] = renderTask;

            await renderTask.promise;
            delete renderTasks[canvasId];

            canvas.dataset.rendered = 'true';
            delete wrapper.dataset.rendering;
        } catch (e) {
            delete wrapper.dataset.rendering;
            if (e.name === 'RenderingCancelledException') return;
            console.error(`Erro render pag ${pageNum}:`, e);
        }
    },

    unloadPage(wrapper) {
        const pageNum = wrapper.dataset.page;
        const canvasId = `pdf-canvas-scroll-${pageNum}`;

        // Cancel active render
        if (renderTasks[canvasId]) {
            renderTasks[canvasId].cancel();
            delete renderTasks[canvasId];
        }
        delete wrapper.dataset.rendering;

        // Remove canvas entirely to free GPU memory, replace with lightweight placeholder
        const canvas = wrapper.querySelector('canvas');
        if (canvas && canvas.dataset.rendered === 'true') {
            wrapper.innerHTML = `<div class="absolute inset-0 flex items-center justify-center">
                <svg class="w-8 h-8 text-gray-300 dark:text-gray-600 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
            </div>`;
        }
    },


    handlePdfScroll(event) {
        if (this.viewMode !== 'scroll') return;

        // Block progress save during initial page restore to prevent page drift
        if (this._isRestoring) return;

        const container = event.target;

        // O(1) page calculation using cached page height
        if (pageHeightCache > 0) {
            const padding = 24; // p-6
            const scrollPos = container.scrollTop + (container.clientHeight / 2);
            const estimatedPage = Math.max(1, Math.min(
                Math.ceil((scrollPos - padding) / pageHeightCache),
                this.totalPages
            ));

            if (estimatedPage !== this.pageNum) {
                this.pageNum = estimatedPage;
                this.updateProgress(estimatedPage, this.totalPages);
            }
        }

        // Debounced fallback: ensure visible pages are rendered after scroll stops
        clearTimeout(this.scrollTimeout);
        this.scrollTimeout = setTimeout(() => {
            // Re-sort render queue by distance from current page
            renderQueue.sort((a, b) => Math.abs(a.pageNum - this.pageNum) - Math.abs(b.pageNum - this.pageNum));

            // Check a small window around current page for unrendered pages
            const start = Math.max(1, this.pageNum - 2);
            const end = Math.min(this.totalPages, this.pageNum + 4);
            for (let i = start; i <= end; i++) {
                const wrapper = document.getElementById(`pdf-page-${i}`);
                if (wrapper) {
                    const canvas = wrapper.querySelector('canvas');
                    if (!canvas || canvas.dataset.rendered !== 'true') {
                        this.enqueueRender(wrapper, i);
                    }
                }
            }
        }, 100);
    },

    goToPage(num) {
        num = parseInt(num);
        if (isNaN(num) || !window._pdfState.doc) return;
        num = Math.max(1, Math.min(num, window._pdfState.numPages));

        if (this.viewMode === 'scroll') {
            const canvas = document.getElementById(`pdf-page-${num}`);
            if (canvas) {
                // Use instant scroll during restore to avoid animation-triggered scroll events
                canvas.scrollIntoView({ behavior: this._isRestoring ? 'instant' : 'smooth', block: 'start' });
            }
        } else {
            this.renderPage(num);
        }
    },

    goToPrevPage() {
        if (this.pageNum <= 1) return;
        const step = this.viewMode === 'double' ? 2 : 1;
        this.renderPage(Math.max(1, this.pageNum - step));
    },

    goToNextPage() {
        if (!window._pdfState.doc || this.pageNum >= this.totalPages) return;
        const step = this.viewMode === 'double' ? 2 : 1;
        this.renderPage(Math.min(this.totalPages, this.pageNum + step));
    },

    zoomIn() {
        if (this.pdfScale >= 3) return;
        this.pdfScale = Math.min(3, this.pdfScale + 0.2);
        if (this.viewMode === 'scroll') {
            this.zoomScrollMode();
        } else {
            this.renderPage(this.pageNum);
        }
    },

    zoomOut() {
        if (this.pdfScale <= 0.5) return;
        this.pdfScale = Math.max(0.5, this.pdfScale - 0.2);
        if (this.viewMode === 'scroll') {
            this.zoomScrollMode();
        } else {
            this.renderPage(this.pageNum);
        }
    },

    async zoomScrollMode() {
        const pdfDoc = window._pdfState.doc;
        if (!pdfDoc) return;

        const savedPage = this.pageNum;

        // Block handlePdfScroll during zoom resize to prevent page drift
        this._isRestoring = true;

        const page1 = await pdfDoc.getPage(1);
        const viewport1 = page1.getViewport({ scale: this.pdfScale });
        const newWidth = Math.round(viewport1.width);
        const newHeight = Math.round(viewport1.height);
        const gap = 24;
        pageHeightCache = newHeight + gap;

        renderQueue = [];
        Object.keys(renderTasks).forEach(key => {
            if (key.startsWith('pdf-canvas-scroll-')) {
                renderTasks[key].cancel();
                delete renderTasks[key];
            }
        });
        activeRenders = 0;

        const container = document.getElementById('pdf-scroll-container');
        if (!container) return;

        const wrappers = container.querySelectorAll('.pdf-page-wrapper');
        wrappers.forEach(wrapper => {
            wrapper.style.width = `${newWidth}px`;
            wrapper.style.height = `${newHeight}px`;
            delete wrapper.dataset.rendering;

            const canvas = wrapper.querySelector('canvas');
            if (canvas) {
                wrapper.innerHTML = `<div class="absolute inset-0 flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-300 dark:text-gray-600 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                </div>`;
            }
        });

        // Restore position, then release the guard
        requestAnimationFrame(() => {
            this.pageNum = savedPage;
            this.goToPage(savedPage);
            setTimeout(() => { this._isRestoring = false; }, 600);
        });
    },

    toggleFullscreen() {
        const container = document.getElementById('pdf-reader-container');
        if (!container) return;

        if (!document.fullscreenElement) {
            container.requestFullscreen().catch(err => {
                console.error('Erro ao entrar em fullscreen:', err);
            });
        } else {
            document.exitFullscreen();
        }
    },

    updateProgress(page, total) {
        clearTimeout(this.saveTimeout);
        this.progressSaved = false;
        this.saveTimeout = setTimeout(() => this.saveProgressNow(page, total), 2000);
    },

    async saveProgressNow(page, total) {
        if (!this.selectedBook) return;
        try {
            await fetch('/api/reading-progress', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    book_id: this.selectedBook.id,
                    current_page: page,
                    total_pages: total,
                    view_mode: this.viewMode,
                    pdf_scale: this.pdfScale
                })
            });
            this.progressSaved = true;
            setTimeout(() => { this.progressSaved = false; }, 3000);
        } catch (e) { console.error('Erro ao salvar progresso:', e); }
    },

    async toggleFavorite(bookId, currentState) {
        try {
            const response = await fetch(`/favorites/toggle/${bookId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            const data = await response.json();
            if (data.success) {
                if (window.location.pathname.includes('/favorites') && !data.is_favorited) {
                    const card = document.querySelector(`#book-card-${bookId}`);
                    if (card) card.remove();
                }

                // Atualiza estado local
                if (data.is_favorited) {
                    if (!this.favoriteBookIds.includes(bookId)) this.favoriteBookIds.push(bookId);
                } else {
                    this.favoriteBookIds = this.favoriteBookIds.filter(id => id !== bookId);
                }

                return data.is_favorited;
            }
        } catch (e) { console.error('Erro ao favoritar:', e); }
        return currentState;
    },

    handleKeydown(e) {
        if (!this.showReader) return;

        // Ignorar se estiver digitando em input
        if (['INPUT', 'TEXTAREA'].includes(e.target.tagName)) return;

        switch (e.key) {
            case 'ArrowRight':
            case 'ArrowDown':
                if (this.viewMode !== 'scroll') {
                    this.goToNextPage();
                }
                break;
            case 'ArrowLeft':
            case 'ArrowUp':
                if (this.viewMode !== 'scroll') {
                    this.goToPrevPage();
                }
                break;
            case '+':
            case '=':
                this.zoomIn();
                break;
            case '-':
                this.zoomOut();
                break;
            case 'Escape':
                this.closeReader();
                break;
        }
    }
});
