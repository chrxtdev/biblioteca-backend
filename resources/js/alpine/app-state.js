const renderTasks = {}; // Store active render tasks outside Alpine to avoid Proxy issues

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

        try {
            const progress = await fetch(`/api/reading-progress/${book.id}`).then(res => res.json());
            if (progress.progress && progress.progress.current_page > 1) {
                this.pageNum = progress.progress.current_page;
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

            // Renderiza página principal
            const page = await pdfDoc.getPage(num);
            const canvas = document.getElementById('pdf-canvas');
            if (!canvas) {
                throw new Error("Canvas não encontrado");
            }
            const ctx = canvas.getContext('2d');
            const viewport = page.getViewport({ scale: this.pdfScale });
            canvas.height = viewport.height;
            canvas.width = viewport.width;

            // Store new task
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
                    canvas2.height = viewport2.height;
                    canvas2.width = viewport2.width;

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

    async renderAllPages() {
        const pdfDoc = window._pdfState.doc;
        if (!pdfDoc) return;

        this.loading = true;
        const container = document.getElementById('pdf-scroll-container');
        if (!container) return;

        // Limpa observer anterior
        if (this.pageObserver) {
            this.pageObserver.disconnect();
            this.pageObserver = null;
        }

        container.innerHTML = '';
        window._pdfState.renderedPages = [];

        // Cancel all active render tasks to prevent writing to detached DOM elements
        Object.keys(renderTasks).forEach(key => {
            if (renderTasks[key]) {
                renderTasks[key].cancel();
                delete renderTasks[key];
            }
        });

        // Limpa timeouts de renderização pendentes (se houver referências)
        // Nota: Os timeouts estão atrelados aos elementos canvas que serão removidos via innerHTML = '',
        // mas é boa prática garantir que não disparem erros.
        // Como não temos lista global de timeouts, confiamos que o innerHTML remove os targets.

        try {
            const page1 = await pdfDoc.getPage(1);
            const viewport1 = page1.getViewport({ scale: this.pdfScale });

            // Configura IntersectionObserver
            this.pageObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    const canvas = entry.target;
                    const pageNum = parseInt(canvas.dataset.page);

                    if (entry.isIntersecting) {
                        // DEBOUNCE: Só renderiza se a página ficar visível por 200ms
                        // Isso evita travar o browser ao rolar muito rápido
                        canvas.renderTimeout = setTimeout(() => {
                            this.renderSinglePageOnScroll(canvas, pageNum);
                        }, 200);
                    } else {
                        // Se saiu da tela antes de renderizar, cancela o timeout
                        if (canvas.renderTimeout) {
                            clearTimeout(canvas.renderTimeout);
                            delete canvas.renderTimeout;
                        }

                        // UNLOAD: Limpa memória se a página sair da tela (Virtual List)
                        this.unloadPage(canvas);
                    }
                });
            }, {
                root: container,
                rootMargin: '2500px', // Aumentado um pouco mais para dar gordura
                threshold: 0
            });

            // Cria Placeholders instantaneamente
            for (let i = 1; i <= pdfDoc.numPages; i++) {
                const canvas = document.createElement('canvas');
                canvas.id = `pdf-page-${i}`;
                canvas.className = 'shadow-2xl rounded-lg bg-white mb-6 mx-auto';
                canvas.dataset.page = i;

                // Define tamanho inicial para a barra de rolagem funcionar
                canvas.width = viewport1.width;
                canvas.height = viewport1.height;
                canvas.style.width = `${viewport1.width}px`;
                canvas.style.height = `${viewport1.height}px`;
                canvas.style.display = 'block';

                container.appendChild(canvas);
                this.pageObserver.observe(canvas);
            }

            this.loading = false; // Libera UI imediatamente

            // Restaura a posição da página atual
            setTimeout(() => {
                this.goToPage(this.pageNum);
                // Força renderização da página atual imediatamente para evitar "branco" inicial
                const currentCanvas = document.getElementById(`pdf-page-${this.pageNum}`);
                if (currentCanvas) {
                    this.renderSinglePageOnScroll(currentCanvas, this.pageNum);
                }
            }, 100);

        } catch (error) {
            console.error("Erro lazy load:", error);
            this.loading = false;
            this.pdfError = error.message;
        }
    },

    async renderSinglePageOnScroll(canvas, pageNum) {
        // console.log(`Tentando renderizar página ${pageNum}...`, { rendered: canvas.dataset.rendered, rendering: canvas.dataset.rendering });
        if (canvas.dataset.rendered === "true") return;

        // Se já estiver renderizando, não inicia outra tarefa, MAS verifica se a tarefa existe
        if (canvas.dataset.rendering === "true") {
            // Se não existe tarefa ativa mas está marcado como rendering, é um estado inválido -> limpa
            if (!renderTasks[canvas.id]) {
                delete canvas.dataset.rendering;
            } else {
                return;
            }
        }

        canvas.dataset.rendering = "true";

        try {
            const pdfDoc = window._pdfState.doc;
            const page = await pdfDoc.getPage(pageNum);
            const ctx = canvas.getContext('2d');
            const viewport = page.getViewport({ scale: this.pdfScale });

            // Ajusta tamanho se for diferente da pág 1
            if (canvas.width !== viewport.width || canvas.height !== viewport.height) {
                canvas.width = viewport.width;
                canvas.height = viewport.height;
                canvas.style.width = `${viewport.width}px`;
                canvas.style.height = `${viewport.height}px`;
            }

            // Cancel previous render on this specific canvas
            if (renderTasks[canvas.id]) {
                renderTasks[canvas.id].cancel();
                delete renderTasks[canvas.id];
            }

            const renderTask = page.render({ canvasContext: ctx, viewport: viewport });
            renderTasks[canvas.id] = renderTask;

            await renderTask.promise;
            delete renderTasks[canvas.id];

            canvas.dataset.rendered = "true";
            delete canvas.dataset.rendering;

            // NÃO paramos de observar. O Virtual Scroll precisa continuar observando para 
            // renderizar de novo se o usuário rolar para cima.
            // if (this.pageObserver) {
            //    this.pageObserver.unobserve(canvas);
            // }
        } catch (e) {
            delete canvas.dataset.rendering; // Allow retry on error
            if (e.name === 'RenderingCancelledException') {
                return;
            }
            console.error(`Erro render pag ${pageNum}:`, e);
        }
    },

    unloadPage(canvas) {
        // Se estiver renderizando, cancela
        if (renderTasks[canvas.id]) {
            renderTasks[canvas.id].cancel();
            delete renderTasks[canvas.id];
        }

        delete canvas.dataset.rendering;

        // Se já estava renderizado, limpa o canvas para liberar memória
        if (canvas.dataset.rendered === "true") {
            const ctx = canvas.getContext('2d');
            // Mantém o tamanho para não perder o scroll, mas limpa os pixels
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            delete canvas.dataset.rendered;
            // console.log(`Memória liberada: Página ${canvas.dataset.page}`);
        }
    },


    handlePdfScroll(event) {
        if (this.viewMode !== 'scroll') return;

        const container = event.target;

        // Debounce do Scroll para garantir renderização das páginas visíveis quando parar
        clearTimeout(this.scrollTimeout);
        this.scrollTimeout = setTimeout(() => {
            const canvases = container.querySelectorAll('canvas[data-page]');
            const containerRect = container.getBoundingClientRect();

            canvases.forEach(canvas => {
                const rect = canvas.getBoundingClientRect();
                // Se está visível e não renderizado (ex: debounce cancelou), força render
                if (rect.top < containerRect.bottom && rect.bottom > containerRect.top) {
                    const pageNum = parseInt(canvas.dataset.page);
                    if (canvas.dataset.rendered !== "true" && !canvas.dataset.rendering) {
                        this.renderSinglePageOnScroll(canvas, pageNum);
                    }
                }
            });
        }, 150);

        const canvases = container.querySelectorAll('canvas[data-page]');

        for (const canvas of canvases) {
            const rect = canvas.getBoundingClientRect();
            const containerRect = container.getBoundingClientRect();

            // Se a página está visível (pelo menos 50% dela)
            if (rect.top < containerRect.bottom && rect.bottom > containerRect.top + containerRect.height / 2) {
                const newPage = parseInt(canvas.dataset.page);
                if (newPage !== this.pageNum) {
                    this.pageNum = newPage;
                    this.updateProgress(newPage, this.totalPages);
                }
                break;
            }
        }
    },

    goToPage(num) {
        num = parseInt(num);
        if (isNaN(num) || !window._pdfState.doc) return;
        num = Math.max(1, Math.min(num, window._pdfState.numPages));

        if (this.viewMode === 'scroll') {
            // Scroll até a página
            const canvas = document.getElementById(`pdf-page-${num}`);
            if (canvas) {
                canvas.scrollIntoView({ behavior: 'smooth', block: 'start' });
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
            this.renderAllPages();
        } else {
            this.renderPage(this.pageNum);
        }
    },

    zoomOut() {
        if (this.pdfScale <= 0.5) return;
        this.pdfScale = Math.max(0.5, this.pdfScale - 0.2);
        if (this.viewMode === 'scroll') {
            this.renderAllPages();
        } else {
            this.renderPage(this.pageNum);
        }
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
                body: JSON.stringify({ book_id: this.selectedBook.id, current_page: page, total_pages: total })
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
