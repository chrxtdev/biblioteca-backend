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

    <!-- PDF.js -->
    <script src="https://mozilla.github.io/pdf.js/build/pdf.mjs" type="module"></script>

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

                // Estado do Leitor de PDF
                selectedBook: null,
                pdfDoc: null,
                pageNum: 1,
                loading: false,

                init() {
                    this.sidebarOpen = window.innerWidth > 1024;
                    this.$watch('darkMode', val => localStorage.setItem('color-theme', val ? 'dark' : 'light'));
                },

                toggleTheme() { this.darkMode = !this.darkMode; },

                async openReader(book) {
                    this.selectedBook = book;
                    this.showReader = true;
                    this.loading = true;
                    this.pdfDoc = null;
                    this.pageNum = 1;

                    try {
                        const progress = await fetch(`/api/reading-progress/${book.id}`).then(res => res.json());
                        if (progress.progress && progress.progress.current_page > 1) {
                            this.pageNum = progress.progress.current_page;
                        }
                    } catch (e) { console.error("Erro ao buscar progresso:", e); }

                    this.loadPdf(`/storage/${book.file_path}`);
                },

                async loadPdf(url) {
                    const { pdfjsLib } = globalThis;
                    pdfjsLib.GlobalWorkerOptions.workerSrc = `https://mozilla.github.io/pdf.js/build/pdf.worker.mjs`;

                    try {
                        const loadingTask = pdfjsLib.getDocument(url);
                        this.pdfDoc = await loadingTask.promise;
                        this.renderPage(this.pageNum);
                    } catch (error) {
                        console.error("Erro ao carregar PDF:", error);
                        this.loading = false;
                        // Opcional: mostrar uma mensagem de erro na UI
                    }
                },

                renderPage(num) {
                    if (!this.pdfDoc) return;
                    this.loading = true;
                    this.pdfDoc.getPage(num).then(page => {
                        const canvas = document.getElementById('pdf-canvas');
                        const ctx = canvas.getContext('2d');
                        const viewport = page.getViewport({ scale: 1.5 });
                        canvas.height = viewport.height;
                        canvas.width = viewport.width;

                        page.render({ canvasContext: ctx, viewport: viewport }).promise.then(() => {
                            this.loading = false;
                            this.pageNum = num;
                            this.updateProgress(num, this.pdfDoc.numPages);
                        });
                    });
                },

                goToPrevPage() {
                    if (this.pageNum <= 1) return;
                    this.renderPage(this.pageNum - 1);
                },

                goToNextPage() {
                    if (this.pageNum >= this.pdfDoc.numPages) return;
                    this.renderPage(this.pageNum + 1);
                },

                updateProgress(page, total) {
                    clearTimeout(this.saveTimeout);
                    this.saveTimeout = setTimeout(() => this.saveProgress(page, total), 3000);
                },

                async saveProgress(page, total) {
                    if (!this.selectedBook) return;
                    try {
                        await fetch('/api/reading-progress', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ book_id: this.selectedBook.id, current_page: page, total_pages: total })
                        });
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
    </script>
</body>
</html>
