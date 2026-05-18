<div>
    <div class="rounded-2xl border border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-white/[0.03]">
        <!-- Header -->
        <div class="flex flex-col gap-2 px-5 mb-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center w-full">
                <x-form.input.select wire:model.live="perPage" label="Per Page" :options="$this->optionsPerPage" name="perPage"/>
                <x-form.input.select label="Active" :options="['' => 'All', 0 => 'No', 1 => 'Yes']" name="isActive"
                                     wire:model.live="isActive"/>
            </div>
            <form>
                <div class="relative">
                    <button type="button" class="absolute -translate-y-1/2 left-4 top-1/2">
                        <svg class="fill-gray-500 dark:fill-gray-400" width="20" height="20" viewBox="0 0 20 20"
                             fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                  d="M3.04199 9.37381C3.04199 5.87712 5.87735 3.04218 9.37533 3.04218C12.8733 3.04218 15.7087 5.87712 15.7087 9.37381C15.7087 12.8705 12.8733 15.7055 9.37533 15.7055C5.87735 15.7055 3.04199 12.8705 3.04199 9.37381ZM9.37533 1.54218C5.04926 1.54218 1.54199 5.04835 1.54199 9.37381C1.54199 13.6993 5.04926 17.2055 9.37533 17.2055C11.2676 17.2055 13.0032 16.5346 14.3572 15.4178L17.1773 18.2381C17.4702 18.531 17.945 18.5311 18.2379 18.2382C18.5308 17.9453 18.5309 17.4704 18.238 17.1775L15.4182 14.3575C16.5367 13.0035 17.2087 11.2671 17.2087 9.37381C17.2087 5.04835 13.7014 1.54218 9.37533 1.54218Z"
                                  fill=""/>
                        </svg>
                    </button>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search..."
                           class="h-[42px] w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pl-[42px] pr-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800 xl:w-[300px]"/>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-hidden">
            <!-- Selected options -->
            <div class="max-w-full px-5 overflow-x-auto" x-data="{
                selected: @entangle('selected'),
                idsOnPage: @entangle('idsOnPage'),
                allIds: {{ json_encode($drivers->pluck('id')) }},

                isAllPageSelected() {
                    return this.idsOnPage.length > 0 && this.idsOnPage.every(id => this.selected.includes(id));
                },

                togglePage() {
                    if (this.isAllPageSelected()) {
                        this.selected = this.selected.filter(id => !this.idsOnPage.includes(id));
                    } else {
                        this.selected = [...new Set([...this.selected, ...this.idsOnPage])];
                    }
                }
            }">
                <div x-show="selected.length > 0"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-2"
                     class="flex items-center justify-between px-4 py-3 mb-4 bg-brand-50 border border-brand-200 rounded-lg dark:bg-brand-900/20 dark:border-brand-800">

                    <div class="flex items-center gap-2 text-sm font-medium text-brand-700 dark:text-brand-400">
                        <span x-text="selected.length"
                              class="flex items-center justify-center w-6 h-6 text-xs text-white rounded-full bg-brand-500"></span>
                        <span>Zaznaczono rekordy</span>
                    </div>

                    <div class="flex items-center gap-4">
                        <button type="button"
                                @click="selected = []"
                                class="text-sm font-semibold text-gray-500 transition hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                            Odznacz wszystkie
                        </button>

                        <button type="button"
                                wire:click="deleteSelected"
                                wire:confirm="Czy na pewno chcesz usunąć zaznaczone rekordy? Tej operacji nie da się cofnąć."
                                class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-semibold text-white bg-red-600 rounded-md hover:bg-red-700 transition focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                            <x-heroicon-o-trash class="w-4 h-4"/>
                            Usuń zaznaczone
                        </button>
                    </div>
                </div>

                <!-- Search Indicator -->
                @if(filled($search) || filled($isActive))
                    <div
                        class="flex flex-wrap items-center justify-between gap-3 px-4 py-2 mb-4 bg-gray-50 border border-gray-200 rounded-lg dark:bg-gray-800/40 dark:border-gray-700">

                        <div class="flex flex-wrap items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                            <span class="font-medium text-gray-500 dark:text-gray-400">Filtry:</span>

                            @if(filled($search))
                                <span
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium text-brand-700 bg-brand-50 rounded-md border border-brand-200 dark:bg-brand-900/30 dark:text-brand-400 dark:border-brand-800">
                    Search: "{{ $search }}"

                    <button type="button" wire:click="$set('search', '')"
                            class="hover:text-brand-900 dark:hover:text-brand-200">
                        <x-heroicon-m-x-mark class="w-3.5 h-3.5"/>
                    </button>
                </span>
                            @endif

                            @if(filled($isActive))
                                <span
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium text-brand-700 bg-brand-50 rounded-md border border-brand-200 dark:bg-brand-900/30 dark:text-brand-400 dark:border-brand-800">
                    Status: {{ $isActive === '1' ? 'Aktywni' : 'Nieaktywni' }}
                    <button type="button" wire:click="$set('isActive', '')"
                            class="hover:text-brand-900 dark:hover:text-brand-200">
                        <x-heroicon-m-x-mark class="w-3.5 h-3.5"/>
                    </button>
                </span>
                            @endif
                        </div>

                        <button type="button"
                                wire:click="resetFilters"
                                class="text-xs font-semibold transition text-brand-600 hover:text-brand-700 dark:text-brand-400 dark:hover:text-brand-300">
                            Wyczyść wszystko
                        </button>
                    </div>
                @endif

                <table class="min-w-full">
                    <thead>
                    <tr class="border-gray-200 border-y dark:border-gray-700">
                        <th scope="col"
                            class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
                            <x-form.input.checkbox name="selectAll" @click="togglePage"
                                                   x-bind:checked="isAllPageSelected()"/>
                        </th>
                        <th scope="col"
                            class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">ID
                        </th>
                        <th scope="col"
                            class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
                            Nazwa
                        </th>
                        <th scope="col"
                            class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
                            Telefon
                        </th>
                        <th scope="col"
                            class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
                            Pesel
                        </th>
                        <th scope="col"
                            class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
                            Adres
                        </th>
                        <th scope="col"
                            class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">Data
                            wygaśnięcia prawa jazdy
                        </th>
                        <th scope="col"
                            class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">Data
                            wygaśnięcia badań medycznych
                        </th>
                        <th scope="col"
                            class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
                            Aktywny
                        </th>
                        <th scope="col"
                            class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
                            Akcje
                        </th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($drivers as $driver)
                        <tr wire:key="driver-row-{{ $driver->id }}">
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    <x-form.input.checkbox name="check_{{ $driver->id }}" value="{{ $driver->id }}"
                                                           x-model="selected"/>
                                </div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $driver->id }}</div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $driver->name ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $driver->phone ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $driver->pesel ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div
                                    class="text-sm text-gray-500 dark:text-gray-400">{!! $driver->fullAddress ?? '-' !!}</div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div
                                    class="text-sm text-gray-500 dark:text-gray-400">{{ $driver->license_expiry_date ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div
                                    class="text-sm text-gray-500 dark:text-gray-400">{{ $driver->medical_exam_valid_until ?? '-' }}</div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    <x-form.input.toggle wire:change="toggleActive({{ $driver->id }})"
                                                         name="{{ $driver->id }}" :isActive="$driver->is_active"/>
                                </div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500 dark:text-gray-400 flex space-x-2">
                                    <button type="button" wire:click="editDriver({{ $driver->id }})">
                                        <x-heroicon-o-pencil-square class="w-6 h-6 hover:text-green-500"/>
                                    </button>
                                    <a href="">
                                        <x-heroicon-o-trash class="w-6 h-6 hover:text-red-500"/>
                                    </a>

                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex flex-col items-center justify-between gap-4 px-4 py-3 border-t border-gray-100 dark:border-gray-800 sm:flex-row">

            <div class="text-sm text-gray-600 dark:text-gray-400">
                @if($drivers->total() > 0)
                    Pozycje od
                    <span class="font-semibold text-gray-700 dark:text-gray-200">{{ $drivers->firstItem() }}</span>
                    do
                    <span class="font-semibold text-gray-700 dark:text-gray-200">{{ $drivers->lastItem() }}</span>
                    z
                    <span class="font-semibold text-gray-700 dark:text-gray-200">{{ $drivers->total() }}</span> łącznie
                @else
                    Nie znaleziono żadnych pozycji
                @endif
            </div>

            <div class="w-full sm:w-auto">
                {{ $drivers->links() }}
            </div>

        </div>
    </div>

    <x-ui.modal wire:model="showEditModal" class="max-w-4xl text-left">

        <div class="relative w-full rounded-3xl bg-white p-6 dark:bg-gray-900 lg:p-10">

        <!-- Formularz -->
        <form wire:submit="updateDriver">

            <h4 class="mb-6 text-lg font-medium text-gray-800 dark:text-white/90">Edycja Kierowcy</h4>

            <div class="grid grid-cols-1 gap-6">

                <!-- Sekcja: Dane podstawowe -->
                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-950">
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-white">
                            Dane podstawowe
                        </h2>
                    </div>

                    <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">

                        <div class="col-span-1">
                            <x-form.input.text-input name="driverData.name" label="Imię i Nazwisko" wire:model="driverData.name"/>
                        </div>

                        <div class="col-span-1">
                            <x-form.input.text-input name="driverData.phone" label="Telefon" wire:model="driverData.phone"/>
                        </div>

                        <div class="col-span-1">
                            <x-form.input.text-input name="driverData.pesel" label="PESEL" wire:model="driverData.pesel"/>
                        </div>

                    </div>
                </div>

                <!-- Sekcja: Dokumenty -->
                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-950">
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-white">
                            Uprawnienia i status
                        </h2>
                    </div>

                    <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">

                            <x-form.input.text-input name="driverData.driving_license_number" label="Nr prawa jazdy" wire:model="driverData.driving_license_number"/>

                            <x-form.date-picker name="driverData.license_expiry_date" label="Ważność prawa jazdy" wire:model="driverData.license_expiry_date" defaultDate="{{ $driverData['license_expiry_date'] ?? '' }}"/>

                            <x-form.date-picker name="driverData.medical_exam_valid_until" label="Badania lekarskie do" wire:model="driverData.medical_exam_valid_until" defaultDate="{{ $driverData['medical_exam_valid_until'] ?? '' }}"/>

                    </div>
                </div>

                <!-- Sekcja: Adres -->
                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-950">
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-white">
                            Adres Zamieszkania
                        </h2>
                    </div>

                    <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2">

                        <x-form.input.select name="driverData.country" label="Kraj" wire:model="driverData.country" :options="\App\Enums\CountriesEnum::getOptions()" default="{{ \App\Enums\CountriesEnum::POLAND }}" />

                        <x-form.input.text-input name="driverData.zipcode" label="Kod pocztowy" wire:model="driverData.zipcode"/>

                        <x-form.input.text-input name="driverData.city" label="Miasto" wire:model="driverData.city"/>
                        <x-form.input.text-input name="driverData.street" label="Ulica" wire:model="driverData.street"/>
                        <x-form.input.text-input name="driverData.street_nr" label="Nr budynku" wire:model="driverData.street_nr"/>
                        <x-form.input.text-input name="driverData.home_nr" label="Nr lokalu" wire:model="driverData.home_nr"/>

                    </div>
                </div>

            </div>

            <div class="flex items-center justify-end w-full gap-3 mt-6">
                <x-ui.button @click="open = false" class="w-full" size="sm" variant="outline">Close</x-ui.button>
                <x-ui.button @click="open = false" class="w-full" size="sm" variant="primary">Save Changes</x-ui.button>
            </div>

        </form>
        </div>
    </x-ui.modal>

</div>
