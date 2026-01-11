<x-layouts.guest>
    <div class="w-full max-w-md mx-auto">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-900">{{ __('carbex.auth.reset_password') }}</h1>
            <p class="mt-2 text-sm text-gray-600">
                Entrez votre adresse e-mail pour recevoir un lien de reinitialisation.
            </p>
        </div>

        @if (session('status'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                <p class="text-sm text-green-700">{{ session('status') }}</p>
            </div>
        @endif

        <form method="POST" action="#" class="space-y-6">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">
                    {{ __('carbex.auth.email') }}
                </label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500"
                    placeholder="vous@exemple.com"
                >
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button
                type="submit"
                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
            >
                {{ __('carbex.auth.send_reset_link') }}
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('login') }}" class="text-sm font-medium text-green-600 hover:text-green-500">
                &larr; {{ __('carbex.auth.login_link') }}
            </a>
        </div>
    </div>
</x-layouts.guest>
