<!-- Search Bar -->
<div class="mb-8">
    <div class="relative max-w-2xl mx-auto">
        <form method="GET" action="{{ route('aluno') }}">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Search for books, authors..." 
                   class="w-full px-12 py-4 bg-white dark:bg-gray-800 rounded-2xl shadow-lg text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-lg">
            <svg class="w-6 h-6 text-gray-400 absolute left-4 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </form>
    </div>
</div>
