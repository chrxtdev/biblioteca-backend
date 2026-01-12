<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enviar Livro - Biblioteca Pública</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

<div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-lg">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">Enviar Obra para a Biblioteca</h1>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul>
                @foreach($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('books.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        <div>
            <label class="block text-gray-700 font-bold mb-2">Título da Obra</label>
            <input type="text" name="title"
                   class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500" required
                   placeholder="Ex: Cálculo I">
        </div>

        <div>
            <label class="block text-gray-700 font-bold mb-2">Autor</label>
            <input type="text" name="author"
                   class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500" required
                   placeholder="Ex: James Stewart">
        </div>

        <div>
            <label class="block text-gray-700 font-bold mb-2">Sinopse / Descrição</label>
            <textarea name="description" rows="3"
                      class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500"></textarea>
        </div>

        <div>
            <label class="block text-gray-700 font-bold mb-2">Arquivo PDF (Livro)</label>
            <input type="file" name="file_path" accept=".pdf" class="w-full p-2 border border-gray-300 rounded"
                   required>
            <p class="text-sm text-gray-500 mt-1">Apenas arquivos PDF.</p>
        </div>

        <div>
            <label class="block text-gray-700 font-bold mb-2">Capa (Imagem)</label>
            <input type="file" name="cover_path" accept="image/*" class="w-full p-2 border border-gray-300 rounded">
        </div>

        <div>
            <label class="block text-gray-700 font-bold mb-2">Curso Relacionado</label>
            <select name="course" class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500 bg-white" required>
                <option value="" disabled selected>Selecione um curso...</option>
                @foreach(\App\Models\Book::COURSES as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition">
            Enviar para Análise
        </button>
    </form>
</div>

</body>
</html>
