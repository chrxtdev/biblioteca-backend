<x-app-layout>
    <style>
        nav[x-data] { display: none !important; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        [x-cloak] { display: none !important; }
    </style>

    <div x-data="{ 
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
                
                // Carregar progresso existente
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
                        // Recarregar página para mostrar progresso atualizado
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
        }" 
        x-init="init()"
        :class="{ 'dark': darkMode }"
        class="bg-gray-50 dark:bg-gray-900 min-h-screen font-sans transition-colors duration-300 flex">

        <!-- Sidebar (Coluna Esquerda) -->
        @include('dashboard.partials.sidebar')

        <!-- Conteúdo Principal (Coluna Central) -->
        <div class="flex-1 max-w-5xl mx-auto">
            @include('dashboard.partials.main-content')
        </div>

        <!-- Widgets (Coluna Direita) -->
        @include('dashboard.partials.widgets')

        <!-- Modais -->
        @include('dashboard.partials.modal-create')
        @include('dashboard.partials.modal-reader')
        @include('dashboard.partials.modal-submissions')

    </div>
</x-app-layout>