<x-layouts.app>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-gray-900">{{ __('carbex.profile.title') }}</h1>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <div class="lg:col-span-1">
            <x-settings-menu active="profile" />
        </div>
        <div class="lg:col-span-3">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('carbex.profile.info') }}</h2>
                <form class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">{{ __('carbex.profile.name') }}</label>
                        <input type="text" id="name" name="name" value="{{ auth()->user()->name }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">{{ __('carbex.profile.email') }}</label>
                        <input type="email" id="email" name="email" value="{{ auth()->user()->email }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                    </div>
                    <div class="pt-4">
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700">
                            {{ __('carbex.profile.save') }}
                        </button>
                    </div>
                </form>
            </div>

            <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('carbex.profile.password') }}</h2>
                <form class="space-y-4">
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700">{{ __('carbex.profile.current_password') }}</label>
                        <input type="password" id="current_password" name="current_password"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                    </div>
                    <div>
                        <label for="new_password" class="block text-sm font-medium text-gray-700">{{ __('carbex.profile.new_password') }}</label>
                        <input type="password" id="new_password" name="new_password"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                    </div>
                    <div>
                        <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700">{{ __('carbex.profile.confirm_password') }}</label>
                        <input type="password" id="new_password_confirmation" name="new_password_confirmation"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                    </div>
                    <div class="pt-4">
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700">
                            {{ __('carbex.profile.change_password') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
