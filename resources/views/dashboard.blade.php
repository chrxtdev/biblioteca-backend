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
                                <th class="pb-2">Título</th>
                                <th class="pb-2">Status</th>
                                <th class="pb-2">Data</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($myBooks as $myBook)
                                <tr class="border-b">
                                    <td class="py-2">{{ $myBook->title }}</td>
                                    <td class="py-2">
                                        @if($myBook->is_verified)
                                            <span
                                                class="bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded">Aprovado</span>
                                        @else
                                            <span
                                                class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-2.5 py-0.5 rounded">Em Análise</span>
                                        @endif
                                    </td>
                                    <td class="py-2 text-sm text-gray-500">
                                        {{ $myBook->created_at->format('d/m/Y') }}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4">📚 Livros Disponíveis na Biblioteca</h3>

                    @if($books->isEmpty())
                        <p>Nenhum livro disponível no momento.</p>
                    @else
                        <ul>
                            @foreach($books as $book)
                                <li class="mb-2 p-2 border-b">
                                    <span class="font-bold">{{ $book->title }}</span>
                                    <a href="{{ asset('storage/' . $book->file_path) }}" target="_blank"
                                       class="text-blue-500 hover:underline ml-2">
                                        Ler PDF
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
