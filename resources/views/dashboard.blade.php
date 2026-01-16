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

    <div x-data="dashboard" x-init="init()" :class="{ 'dark': darkMode }"
        class="bg-gray-50 dark:bg-gray-900 min-h-screen font-sans transition-colors duration-300 flex">

        <!-- Sidebar (Coluna Esquerda) -->
        @include('dashboard.partials.sidebar')

        <!-- Conteúdo Principal (Coluna Central) -->
        <div class="flex-1 overflow-y-auto">
            @include('dashboard.partials.main-content')
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
            Alpine.data('dashboard', () => ({
                darkMode: localStorage.getItem('theme') === 'dark',
                showReader: false,
                showCreate: false,
                showSubmissions: false,
                
                // Variáveis do Leitor
                pdfUrl: '',
                selectedBook: null,
                readingProgress: [],
                currentPage: 1,
                totalPages: 0,
                activeTab: 'todos',
                activeCategory: 'all',

                init() {
                    if (this.darkMode) document.documentElement.classList.add('dark');
                    this.readingProgress = []; 
                },

                toggleTheme() {
                    this.darkMode = !this.darkMode;
                    localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
                    if (this.darkMode) document.documentElement.classList.add('dark');
                    else document.documentElement.classList.remove('dark');
                },

                openReader(book) {
                    console.log('Abrindo livro:', book.title);
                    
                    this.selectedBook = book;
                    this.pdfUrl = '/storage/' + book.file_path;
                    this.currentPage = 1;
                    this.totalPages = 0;
                    this.showReader = true;
                },

                updateProgress(page, total) {
                    this.currentPage = page;
                    this.totalPages = total;
                    console.log('Progresso atualizado:', page, 'de', total);
                },

                // Funções placeholders para não quebrar
                saveProgress(page, total) { console.log('Salvando progresso...'); },
                markAsCompleted() { console.log('Marcando completo...'); },
                navigatePage(direction) { 
                    const iframe = document.querySelector('#pdf-iframe');
                    if (iframe && iframe.contentWindow) {
                        iframe.contentWindow.postMessage({ type: direction === 'next' ? 'nextPage' : 'prevPage' }, '*');
                    }
                }
            }));
        });
    </script>