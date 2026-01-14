<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('status') === 'livro-enviado')
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                role="alert">
                <strong class="font-bold">Sucesso!</strong>
                <span class="block sm:inline">Seu livro foi enviado para análise da bibliotecária.</span>
            </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4 border-b pb-2">📂 Meus Envios Recentes</h3>

                    @if($myBooks->isEmpty())
                    <p class="text-gray-500 italic">Você ainda não enviou nenhum livro.</p>
                    @else
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-gray-500 text-sm">
                                <th class="pb-2">Capa:</th>
                                <th class="pb-2">Título:</th>
                                <th class="pb-2">Autor:</th>
                                <th class="pb-2">Status:</th>
                                <th class="pb-2">Enviado em:</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($myBooks as $myBook)
                            <tr class="border-b hover:bg-gray-50">

                                <td class="py-3 pr-4">
                                    @if($myBook->cover_path)
                                    <img src="{{ asset('storage/' . $myBook->cover_path) }}"
                                        alt="Capa"
                                        class="w-12 h-20 object-cover rounded shadow-sm border border-gray-200">
                                    @else
                                    <div
                                        class="w-12 h-16 bg-gray-200 rounded flex items-center justify-center text-gray-400 text-xs">
                                        Sem Capa
                                    </div>
                                    @endif
                                </td>

                                <td class="py-3 font-medium text-gray-900">{{ $myBook->title }}</td>

                                <td class="py-3 text-gray-600">{{ $myBook->author }}</td>

                                <td class="py-3">
                                    @if($myBook->is_verified)
                                    <span
                                        class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded">Aprovado</span>
                                    @elseif($myBook->rejection_reason)
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="bg-red-100 text-red-800 text-xs font-semibold px-2.5 py-0.5 rounded">Recusado</span>
                                        <button
                                            x-data=""
                                            x-on:click="alert('MOTIVO DA REJEIÇÃO:\n\n' + $el.dataset.reason)"
                                            data-reason="{{ $myBook->rejection_reason }}"
                                            class="text-xs text-red-600 underline hover:text-red-800 cursor-pointer">
                                            Ver motivo
                                        </button>
                                    </div>
                                    @else
                                    <span
                                        class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-2.5 py-0.5 rounded">Em Análise</span>
                                    @endif
                                </td>

                                <td class="py-3 text-sm text-gray-500">
                                    {{ $myBook->created_at->format('d/m/Y') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
            </div>

            <div class="mt-4">
                {{ $myBooks->links() }}
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold">📚 Livros Disponíveis</h3>

                        <form method="GET" action="{{ route('dashboard') }}" class="flex gap-2">
                            <input type="text"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="Buscar título ou autor..."
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">
                                Buscar
                            </button>
                            @if(request('search'))
                            <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md text-sm hover:bg-gray-300">
                                Limpar
                            </a>
                            @endif
                        </form>
                    </div>

                    <div class="mt-4">
                        {{ $books->appends(['search' => request('search')])->links() }}
                    </div>


                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h3 class="text-lg font-bold mb-4">📚 Livros Disponíveis na Biblioteca</h3>

                            @if($books->isEmpty())
                            <p class="text-gray-500 italic">Nenhum livro disponível no momento.</p>
                            @else
                            <div class="grid gap-4"> @foreach($books as $book)
                                <div
                                    class="p-4 border rounded-lg hover:bg-gray-50 flex items-center justify-between transition duration-150">

                                    <div class="flex items-center gap-4">

                                        <div class="flex-shrink-0">
                                            @if($book->cover_path)
                                            <img src="{{ asset('storage/' . $book->cover_path) }}"
                                                alt="{{ $book->title }}"
                                                class="w-12 h-16 object-cover rounded shadow-sm border border-gray-200">
                                            @else
                                            <div
                                                class="w-12 h-16 bg-gray-200 rounded flex items-center justify-center text-gray-400">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                                </svg>
                                            </div>
                                            @endif
                                        </div>

                                        <div>
                                            <h4 class="text-lg font-bold text-gray-800 leading-tight">{{ $book->title }}</h4>
                                            <p class="text-sm text-gray-600">Por: {{ $book->author }}</p>
                                            <p class="text-xs text-gray-400 mt-1">{{ $book->course ?? 'Geral' }}</p>
                                        </div>
                                    </div>

                                    <a href="{{ asset('storage/' . $book->file_path) }}" target="_blank"
                                        class="ml-4 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition shadow-sm whitespace-nowrap">
                                        Ler PDF
                                    </a>

                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
</x-app-layout>