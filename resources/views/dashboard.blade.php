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
            pdfUrl: '', 
            bookTitle: '',
            
            toggleTheme() {
                this.darkMode = !this.darkMode;
                localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
                if (this.darkMode) document.documentElement.classList.add('dark');
                else document.documentElement.classList.remove('dark');
            },
            
            init() {
                if (this.darkMode) document.documentElement.classList.add('dark');
            },

            openReader(url, title) {
                this.pdfUrl = url;
                this.bookTitle = title;
                this.showReader = true;
            }
        }" 
        x-init="init()"
        :class="{ 'dark': darkMode }"
        class="bg-gray-50 dark:bg-gray-900 min-h-screen font-sans transition-colors duration-300">

        @include('dashboard.partials.header')

        @include('dashboard.partials.book-list')

        @include('dashboard.partials.modal-create')
        @include('dashboard.partials.modal-reader')

    </div>
</x-app-layout>