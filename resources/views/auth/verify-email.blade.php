<x-layouts.auth-split>
    <div class="mb-8">
        <h2 class="mt-6 text-3xl font-bold tracking-tight text-gray-900">Verifique seu Email 📧</h2>
        <div class="mt-2 text-sm text-gray-600">
            {{ __('Obrigado por se inscrever! Antes de começarmos, poderia verificar seu endereço de email clicando no link que acabamos de enviar? Se você não recebeu, teremos prazer em enviar outro.') }}
        </div>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ __('Um novo link de verificação foi enviado para o endereço de email fornecido durante o registro.') }}
        </div>
    @endif

    <div class="mt-4 flex flex-col space-y-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <button type="submit" 
                    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-teal-500 hover:from-blue-700 hover:to-teal-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition-all duration-200 transform hover:-translate-y-0.5 shadow-md hover:shadow-lg">
                    {{ __('Reenviar Email de Verificação') }}
                </button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full text-center text-sm text-gray-600 hover:text-gray-900 underline">
                {{ __('Sair') }}
            </button>
        </form>
    </div>
</x-layouts.auth-split>
