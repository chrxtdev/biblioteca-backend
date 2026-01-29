<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="appState" x-init="init()" :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <!-- Fonts & Styles -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style> [x-cloak] { display: none !important; } </style>

    <!-- PDF.js via CDN - versão 2.16.105 estável -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
    <script>
        // Configurar o worker do PDF.js
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';
        window.pdfjsLib = pdfjsLib;
    </script>


    <script>
        // Evita o "flash" do modo claro/escuro (FOUC)
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        <main>
            {{ $slot }}
        </main>
    </div>

    <script type="module">
        // Estado do PDF fora do Alpine para evitar Proxy wrapping
        window._pdfState = {
            doc: null,
            numPages: 0,
            renderedPages: []
        };

        document.addEventListener('alpine:init', () => {
            Alpine.data('appState', () => ({
                // Estado Geral da UI
                isLoaded: false,
                darkMode: localStorage.getItem('color-theme') === 'dark',
                sidebarOpen: localStorage.getItem('sidebarOpen') !== null ? localStorage.getItem('sidebarOpen') === 'true' : window.innerWidth > 1024,
                showReader: false,
                showCreate: false,
                showSubmissions: false,
                activeTab: 'todos',
                saveTimeout: null,
                newsSeen: false,

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


                init() {
                    // Evita animações na carga inicial
                    setTimeout(() => this.isLoaded = true, 100);

                    // Watchers para persistência
                    this.$watch('darkMode', val => localStorage.setItem('color-theme', val ? 'dark' : 'light'));
                    this.$watch('sidebarOpen', val => localStorage.setItem('sidebarOpen', val));
                    
                    // Listener para ESC sair do fullscreen
                    document.addEventListener('fullscreenchange', () => {
                        this.isFullscreen = !!document.fullscreenElement;
                    });
                },

                toggleTheme() { this.darkMode = !this.darkMode; },

                async markNewsSeen() {
                    if (this.newsSeen) return;
                    this.newsSeen = true;
                    try {
                        await fetch('/api/mark-news-seen', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                        });
                    } catch (e) { console.error('Erro ao marcar como visto:', e); }
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

                        await page.render({ canvasContext: ctx, viewport: viewport }).promise;

                        // Se modo duplo, renderiza segunda página
                        if (this.viewMode === 'double' && num + 1 <= pdfDoc.numPages) {
                            const page2 = await pdfDoc.getPage(num + 1);
                            const canvas2 = document.getElementById('pdf-canvas-2');
                            if (canvas2) {
                                const ctx2 = canvas2.getContext('2d');
                                const viewport2 = page2.getViewport({ scale: this.pdfScale });
                                canvas2.height = viewport2.height;
                                canvas2.width = viewport2.width;
                                await page2.render({ canvasContext: ctx2, viewport: viewport2 }).promise;
                            }
                        }

                        this.loading = false;
                        this.pageNum = num;
                        
                        // Scroll para o topo do container
                        const container = document.getElementById('pdf-container');
                        if (container) container.scrollTop = 0;
                        
                        this.updateProgress(num, pdfDoc.numPages);
                    } catch (error) {
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

                    try {
                        const page1 = await pdfDoc.getPage(1);
                        const viewport1 = page1.getViewport({ scale: this.pdfScale });
                        
                        // Configura IntersectionObserver
                        this.pageObserver = new IntersectionObserver((entries) => {
                            entries.forEach(entry => {
                                if (entry.isIntersecting) {
                                    const canvas = entry.target;
                                    const pageNum = parseInt(canvas.dataset.page);
                                    this.renderSinglePageOnScroll(canvas, pageNum);
                                }
                            });
                        }, {
                            root: container,
                            rootMargin: '600px', // Pré-carrega páginas muito antes de entrarem na tela
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
                    } catch (error) {
                        console.error("Erro lazy load:", error);
                        this.loading = false;
                        this.pdfError = error.message;
                    }
                },

                async renderSinglePageOnScroll(canvas, pageNum) {
                    if (canvas.dataset.rendered || canvas.dataset.rendering) return;
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

                        await page.render({ canvasContext: ctx, viewport: viewport }).promise;
                        
                        canvas.dataset.rendered = "true";
                        delete canvas.dataset.rendering;
                        
                        // Para de observar depois de renderizado (Lazy Load apenas, não Virtual Scroll completo)
                        if (this.pageObserver) {
                            this.pageObserver.unobserve(canvas);
                        }
                    } catch (e) {
                        console.error(`Erro render pag ${pageNum}:`, e);
                    }
                },

                handlePdfScroll(event) {
                    if (this.viewMode !== 'scroll') return;
                    
                    const container = event.target;
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
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
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
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                        });
                        const data = await response.json();
                        if (data.success) {
                            if (window.location.pathname.includes('/favorites') && !data.is_favorited) {
                                document.querySelector(`#book-card-${bookId}`).remove();
                            }
                            return data.is_favorited;
                        }
                    } catch (e) { console.error('Erro ao favoritar:', e); }
                    return currentState;
                }
            }));
        });

        // Watch viewMode changes
        document.addEventListener('alpine:initialized', () => {
            Alpine.effect(() => {
                const appState = Alpine.$data(document.documentElement);
                if (appState && appState.viewMode && window._pdfState.doc) {
                    if (appState.viewMode === 'scroll') {
                        setTimeout(() => appState.renderAllPages(), 100);
                    } else {
                        setTimeout(() => appState.renderPage(appState.pageNum), 100);
                    }
                }
            });
        });

        // Start Alpine after registering listeners
        Alpine.start();
    </script>
</body>
</html>
