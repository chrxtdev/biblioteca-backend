<x-app-layout>
    <style>
        nav[x-data] {
            display: none !important;
        }

        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        [x-cloak] {
            display: none !important;
        }

        .book-card {
            transition: all 0.3s ease;
        }

        .book-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .category-pill {
            transition: all 0.2s ease;
        }

        .category-pill:hover {
            transform: scale(1.05);
        }

        .horizontal-scroll {
            scrollbar-width: thin;
            scrollbar-color: #4B5563 #1F2937;
        }

        .horizontal-scroll::-webkit-scrollbar {
            height: 6px;
        }

        .horizontal-scroll::-webkit-scrollbar-track {
            background: #1F2937;
            border-radius: 3px;
        }

        .horizontal-scroll::-webkit-scrollbar-thumb {
            background: #4B5563;
            border-radius: 3px;
        }
    </style>

    <div x-data="dashboardData" x-init="init()" :class="{ 'dark': darkMode }"
        class="bg-gray-50 dark:bg-gray-900 min-h-screen font-sans transition-colors duration-300">

        <!-- Sidebar (Coluna Esquerda) -->
        @include('dashboard.partials.sidebar')

        <!-- Conteúdo Principal (Coluna Central) -->
        <div class="flex-1 p-6 overflow-y-auto">
            @include('dashboard.partials.search-bar')
            @include('dashboard.partials.recommended-section')
            @include('dashboard.partials.categories-section')
        </div>

        <!-- Book Details Panel (Coluna Direita) -->
        @include('dashboard.partials.book-details-panel')

        <!-- Modais -->
        @include('dashboard.partials.modal-create')
        @include('dashboard.partials.modal-reader')
        @include('dashboard.partials.modal-submissions')

    </div>
</x-app-layout>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('dashboardData', () => ({
        darkMode: localStorage.getItem('theme') === 'dark',
        showReader: false,
        showCreate: false,
        showMySubmissions: false,
        pdfUrl: '',
        bookTitle: '',
        currentBookId: null,
        currentPage: 1,
        totalPages: 0,
        readingProgress: {},
        activeTab: 'todos',
        activeCategory: 'all',
        selectedBook: null,

        toggleTheme() {
            this.darkMode = !this.darkMode;
            localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
            if (this.darkMode) document.documentElement.classList.add('dark');
            else document.documentElement.classList.remove('dark');
        },

        init() {
            if (this.darkMode) document.documentElement.classList.add('dark');
        },

        openReader(url, title, bookId) {
            this.pdfUrl = url;
            this.bookTitle = title;
            this.currentBookId = bookId;
            this.showReader = true;

            // Set selected book for details panel
            this.selectedBook = {
                id: bookId,
                title: title,
                file: url,
                cover: null, // Will be set based on actual book data
                author: 'Unknown Author',
                rating: '4.8',
                pages: '320',
                ratings: '643',
                reviews: '110',
                description: 'A comprehensive guide to modern practices and strategies.'
            };

            this.loadReadingProgress(bookId);
        },

        loadReadingProgress(bookId) {
            fetch(`/api/reading-progress/${bookId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.progress) {
                        this.readingProgress = data.progress;
                        this.currentPage = data.progress.current_page;
                        this.totalPages = data.progress.total_pages;
                    }
                })
                .catch(error => console.log('Erro ao carregar progresso:', error));
        },

        updateProgress(page, total) {
            this.currentPage = page;
            this.totalPages = total;

            if (this.currentBookId) {
                fetch('/api/reading-progress', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        book_id: this.currentBookId,
                        current_page: page,
                        total_pages: total
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        this.readingProgress = data.progress;
                    })
                    .catch(error => console.log('Erro ao salvar progresso:', error));
            }
        },

        markAsCompleted() {
            if (this.currentBookId) {
                fetch('/api/reading-progress/complete', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        book_id: this.currentBookId
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        this.readingProgress = data.progress;
                        window.location.reload();
                    })
                    .catch(error => console.log('Erro ao marcar como concluído:', error));
            }
        },

        navigatePage(direction) {
            const iframe = document.getElementById('pdfViewer');
            if (!iframe.contentWindow) return;

            try {
                if (direction === 'next') {
                    iframe.contentWindow.postMessage({ type: 'nextPage' }, '*');
                } else {
                    iframe.contentWindow.postMessage({ type: 'prevPage' }, '*');
                }
            } catch (error) {
                console.log('Erro ao navegar:', error);
            }
        }
    }))
})
</script>