<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="appState" x-init="init()" :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

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
            numPages: 0
        };

        document.addEventListener('alpine:init', () => {
            Alpine.data('appState', () => ({
                // Estado Geral da UI
                darkMode: localStorage.getItem('color-theme') === 'dark',
                sidebarOpen: true,
                showReader: false,
                showCreate: false,
                showSubmissions: false,
                activeTab: 'todos',
                saveTimeout: null,

                // Estado do Leitor de PDF (sem o pdfDoc que fica em window._pdfState)
                selectedBook: null,
                pageNum: 1,
                totalPages: 0,
                pdfScale: 1.2,
                loading: false,
                pdfError: null,
                progressSaved: false,

                init() {
                    this.sidebarOpen = window.innerWidth > 1024;
                    this.$watch('darkMode', val => localStorage.setItem('color-theme', val ? 'dark' : 'light'));
                },

                toggleTheme() { this.darkMode = !this.darkMode; },

                async openReader(book) {
                    this.selectedBook = book;
                    this.showReader = true;
                    this.loading = true;
                    window._pdfState.doc = null;
                    window._pdfState.numPages = 0;
                    this.pageNum = 1;
                    this.totalPages = 0;
                    this.pdfScale = 1.2;
                    this.pdfError = null;
                    this.progressSaved = false;

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
                    this.showReader = false;
                    window._pdfState.doc = null;
                    window._pdfState.numPages = 0;
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
                        
                        this.renderPage(this.pageNum);
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

                        this.loading = false;
                        this.pageNum = num;
                        this.updateProgress(num, pdfDoc.numPages);
                    } catch (error) {
                        console.error("Erro na renderização:", error);
                        this.loading = false;
                        this.pdfError = `Erro ao renderizar página ${num}: ${error.message}`;
                    }
                },

                goToPage(num) {
                    num = parseInt(num);
                    if (isNaN(num) || !window._pdfState.doc) return;
                    num = Math.max(1, Math.min(num, window._pdfState.numPages));
                    this.renderPage(num);
                },

                goToPrevPage() {
                    if (this.pageNum <= 1) return;
                    this.renderPage(this.pageNum - 1);
                },

                goToNextPage() {
                    if (!window._pdfState.doc || this.pageNum >= this.totalPages) return;
                    this.renderPage(this.pageNum + 1);
                },

                zoomIn() {
                    if (this.pdfScale >= 3) return;
                    this.pdfScale = Math.min(3, this.pdfScale + 0.2);
                    this.renderPage(this.pageNum);
                },

                zoomOut() {
                    if (this.pdfScale <= 0.5) return;
                    this.pdfScale = Math.max(0.5, this.pdfScale - 0.2);
                    this.renderPage(this.pageNum);
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

        // Start Alpine after registering listeners
        Alpine.start();
    </script>
</body>
</html>
