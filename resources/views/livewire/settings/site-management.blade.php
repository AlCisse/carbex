<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-semibold text-gray-900 dark:text-white">{{ __('carbex.settings.sites') }}</h1>
            @can('create', App\Models\Site::class)
            <div class="flex items-center space-x-3">
                <x-button wire:click="openImportModal" type="button" variant="secondary">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                    </svg>
                    {{ __('carbex.sites.import.csv') }}
                </x-button>
                <x-button wire:click="openForm" type="button">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    {{ __('carbex.sites.add') }}
                </x-button>
            </div>
            @endcan
        </div>
    </x-slot>

    @if (session('success'))
        <x-alert type="success" dismissible class="mb-6">{{ session('success') }}</x-alert>
    @endif

    @if (session('error'))
        <x-alert type="error" dismissible class="mb-6">{{ session('error') }}</x-alert>
    @endif

    <!-- Sites List -->
    <div class="space-y-4">
        @forelse ($sites as $site)
        <x-card class="hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div class="flex items-start space-x-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-100 text-green-600">
                        @if ($site['type'] === 'office')
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                            </svg>
                        @elseif ($site['type'] === 'warehouse')
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                            </svg>
                        @else
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" />
                            </svg>
                        @endif
                    </div>
                    <div>
                        <div class="flex items-center space-x-2">
                            <h3 class="text-lg font-medium text-gray-900">{{ $site['name'] }}</h3>
                            @if ($site['is_primary'])
                                <x-badge variant="primary" size="sm">{{ __('carbex.sites.primary') }}</x-badge>
                            @endif
                            @if (!$site['is_active'])
                                <x-badge variant="warning" size="sm">{{ __('carbex.common.inactive') }}</x-badge>
                            @endif
                        </div>
                        <p class="text-sm text-gray-500">{{ $site['code'] }} - {{ ucfirst($site['type'] ?? 'office') }}</p>
                        @if ($site['city'] || $site['country'])
                            <p class="mt-1 text-sm text-gray-500">
                                {{ collect([$site['city'], $site['country']])->filter()->implode(', ') }}
                            </p>
                        @endif
                        @if ($site['floor_area_m2'] || $site['employee_count'])
                            <div class="mt-2 flex space-x-4 text-sm text-gray-500">
                                @if ($site['floor_area_m2'])
                                    <span>{{ number_format($site['floor_area_m2'], 0, ',', ' ') }} m2</span>
                                @endif
                                @if ($site['employee_count'])
                                    <span>{{ $site['employee_count'] }} {{ __('carbex.auth.employees') }}</span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <div class="flex items-center space-x-2">
                    @if (!$site['is_primary'])
                        @can('setPrimary', App\Models\Site::find($site['id']))
                        <button wire:click="setPrimary('{{ $site['id'] }}')" type="button" class="text-gray-400 hover:text-green-600" title="{{ __('carbex.sites.set_as_primary') }}">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                            </svg>
                        </button>
                        @endcan
                    @endif

                    @can('update', App\Models\Site::find($site['id']))
                    <button wire:click="openForm('{{ $site['id'] }}')" type="button" class="text-gray-400 hover:text-gray-600" title="{{ __('carbex.common.edit') }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                        </svg>
                    </button>
                    @endcan

                    @can('delete', App\Models\Site::find($site['id']))
                    @if (!$site['is_primary'])
                    <button wire:click="confirmDelete('{{ $site['id'] }}')" type="button" class="text-gray-400 hover:text-red-600" title="{{ __('carbex.common.delete') }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                        </svg>
                    </button>
                    @endif
                    @endcan
                </div>
            </div>
        </x-card>
        @empty
        <x-card class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" />
            </svg>
            <h3 class="mt-2 text-sm font-semibold text-gray-900">{{ __('carbex.sites.no_sites') }}</h3>
            <p class="mt-1 text-sm text-gray-500">{{ __('carbex.sites.no_sites_desc') }}</p>
            @can('create', App\Models\Site::class)
            <div class="mt-6">
                <x-button wire:click="openForm" type="button">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    {{ __('carbex.sites.add_first') }}
                </x-button>
            </div>
            @endcan
        </x-card>
        @endforelse
    </div>

    <!-- Site Form Modal -->
    @if ($showForm)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex min-h-screen items-end justify-center px-4 pb-20 pt-4 text-center sm:block sm:p-0">
            <div wire:click="closeForm" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

            <div class="inline-block transform overflow-hidden rounded-lg bg-white text-left align-bottom shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl sm:align-middle">
                <form wire:submit="save">
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">
                            {{ $editingSiteId ? __('carbex.sites.edit') : __('carbex.sites.add') }}
                        </h3>

                        <div class="space-y-4">
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <x-input wire:model="name" name="name" :label="__('carbex.sites.name')" required :error="$errors->first('name')" />
                                <x-input wire:model="code" name="code" :label="__('carbex.sites.code')" :hint="__('carbex.sites.code_hint')" :error="$errors->first('code')" />
                            </div>

                            <x-select wire:model="type" name="type" :label="__('carbex.sites.type')">
                                <option value="office">{{ __('carbex.sites.types.office') }}</option>
                                <option value="warehouse">{{ __('carbex.sites.types.warehouse') }}</option>
                                <option value="factory">{{ __('carbex.sites.types.factory') }}</option>
                                <option value="store">{{ __('carbex.sites.types.store') }}</option>
                                <option value="datacenter">{{ __('carbex.sites.types.datacenter') }}</option>
                                <option value="other">{{ __('carbex.sites.types.other') }}</option>
                            </x-select>

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <x-input wire:model="address_line_1" name="address_line_1" :label="__('carbex.organization.address')" />
                                <x-input wire:model="city" name="city" :label="__('carbex.organization.city')" />
                            </div>

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <x-input wire:model="postal_code" name="postal_code" :label="__('carbex.organization.postal_code')" />
                                <x-input wire:model="floor_area_m2" name="floor_area_m2" type="number" step="0.01" :label="__('carbex.sites.floor_area')" />
                            </div>

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <x-input wire:model="employee_count" name="employee_count" type="number" :label="__('carbex.sites.employee_count')" />
                                <x-input wire:model="electricity_provider" name="electricity_provider" :label="__('carbex.sites.electricity_provider')" />
                            </div>

                            <div class="flex items-center space-x-6">
                                <div class="flex items-center">
                                    <input wire:model="renewable_energy" id="renewable_energy" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-600">
                                    <label for="renewable_energy" class="ml-2 text-sm text-gray-900">{{ __('carbex.sites.renewable_energy') }}</label>
                                </div>
                                @if (!$editingSiteId)
                                <div class="flex items-center">
                                    <input wire:model="is_primary" id="is_primary" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-green-600 focus:ring-green-600">
                                    <label for="is_primary" class="ml-2 text-sm text-gray-900">{{ __('carbex.sites.is_primary') }}</label>
                                </div>
                                @endif
                            </div>

                            @if ($renewable_energy)
                            <x-input wire:model="renewable_percentage" name="renewable_percentage" type="number" step="0.1" min="0" max="100" :label="__('carbex.sites.renewable_percentage')" />
                            @endif
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <x-button type="submit" class="sm:ml-3">
                            {{ $editingSiteId ? __('carbex.common.save') : __('carbex.common.create') }}
                        </x-button>
                        <x-button type="button" variant="secondary" wire:click="closeForm">
                            {{ __('carbex.common.cancel') }}
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if ($showDeleteModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex min-h-screen items-end justify-center px-4 pb-20 pt-4 text-center sm:block sm:p-0">
            <div wire:click="cancelDelete" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

            <div class="inline-block transform overflow-hidden rounded-lg bg-white text-left align-bottom shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:align-middle">
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <h3 class="text-base font-semibold leading-6 text-gray-900">{{ __('carbex.sites.delete_title') }}</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">{{ __('carbex.sites.delete_confirm') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <x-button type="button" variant="danger" wire:click="delete" class="sm:ml-3">
                        {{ __('carbex.common.delete') }}
                    </x-button>
                    <x-button type="button" variant="secondary" wire:click="cancelDelete">
                        {{ __('carbex.common.cancel') }}
                    </x-button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- CSV Import Modal -->
    @if ($showImportModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex min-h-screen items-end justify-center px-4 pb-20 pt-4 text-center sm:block sm:p-0">
            <div wire:click="closeImportModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

            <div class="inline-block transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left align-bottom shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-3xl sm:align-middle">
                <div class="bg-white dark:bg-gray-800 px-4 pb-4 pt-5 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">
                            {{ __('carbex.sites.import.title') }}
                        </h3>
                        <button wire:click="downloadTemplate" type="button" class="text-sm text-emerald-600 hover:text-emerald-700 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            {{ __('carbex.sites.import.download_template') }}
                        </button>
                    </div>

                    <!-- File Upload -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('carbex.sites.import.select_file') }}
                        </label>
                        <div class="flex items-center justify-center w-full">
                            <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-600">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <svg class="w-8 h-8 mb-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    @if($csvFile)
                                        <p class="text-sm text-emerald-600 dark:text-emerald-400 font-medium">{{ $csvFile->getClientOriginalName() }}</p>
                                    @else
                                        <p class="mb-2 text-sm text-gray-500 dark:text-gray-400">
                                            <span class="font-semibold">{{ __('carbex.sites.import.click_to_upload') }}</span>
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">CSV (max 2MB)</p>
                                    @endif
                                </div>
                                <input wire:model="csvFile" type="file" class="hidden" accept=".csv,.txt" />
                            </label>
                        </div>
                        @error('csvFile')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Import Errors -->
                    @if(count($importErrors) > 0)
                        <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-lg">
                            <h4 class="text-sm font-medium text-red-800 dark:text-red-400 mb-2">{{ __('carbex.sites.import.errors') }}</h4>
                            <ul class="list-disc list-inside text-sm text-red-600 dark:text-red-400">
                                @foreach($importErrors as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Preview Table -->
                    @if(count($importPreview) > 0)
                        <div class="mb-4">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">
                                {{ __('carbex.sites.import.preview') }} ({{ count($importPreview) }} {{ __('carbex.sites.import.rows') }})
                            </h4>
                            <div class="overflow-x-auto max-h-64 border border-gray-200 dark:border-gray-700 rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700 sticky top-0">
                                        <tr>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">#</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('carbex.sites.name') }}</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('carbex.sites.site_type') }}</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('carbex.organization.city') }}</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">{{ __('carbex.sites.floor_area') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($importPreview as $row)
                                            <tr>
                                                <td class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">{{ $row['row'] }}</td>
                                                <td class="px-3 py-2 text-sm text-gray-900 dark:text-white">{{ $row['name'] }}</td>
                                                <td class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">{{ $row['type'] }}</td>
                                                <td class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">{{ $row['city'] ?? '-' }}</td>
                                                <td class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">{{ $row['floor_area_m2'] ? number_format($row['floor_area_m2'], 0) . ' m²' : '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    @if(count($importPreview) > 0)
                        <x-button type="button" wire:click="confirmImport" class="sm:ml-3">
                            {{ __('carbex.sites.import.confirm') }} ({{ count($importPreview) }})
                        </x-button>
                    @endif
                    <x-button type="button" variant="secondary" wire:click="closeImportModal">
                        {{ __('carbex.common.cancel') }}
                    </x-button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
