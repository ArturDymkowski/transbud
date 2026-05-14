<div>
    <div class="rounded-2xl border border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-white/[0.03]">
        <!-- Header -->
        <div class="flex flex-col gap-2 px-5 mb-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90"></h3>
            </div>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center justify-between w-full">
                <div class="per-page flex items-center gap-3">
                    <span class="text-gray-500 dark:text-gray-400"> Show </span>
                    <div x-data="{ isOptionSelected: false }" class="relative z-20 bg-transparent">
                        <select wire:model.live="perPage" class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-9 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none py-2 pr-8 pl-3 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" :class="isOptionSelected &amp;&amp; 'text-gray-500 dark:text-gray-400'" @click="isOptionSelected = true" @change="perPage = $event.target.value">
                            @foreach ($this->optionsPerPage as $option)
                                <option value="{{ $option }}" class="text-gray-500 dark:bg-gray-900 dark:text-gray-400"> {{ $option }} </option>
                            @endforeach
                        </select>
                        <span class="absolute top-1/2 right-2 z-30 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                      <svg class="stroke-current" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3.8335 5.9165L8.00016 10.0832L12.1668 5.9165" stroke="" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"></path>
                      </svg>
                    </span>
                    </div>
                    <span class="text-gray-500 dark:text-gray-400"> entries </span>
                </div>

                <form>
                    <div class="relative">
                        <button type="button" class="absolute -translate-y-1/2 left-4 top-1/2">
                            <svg class="fill-gray-500 dark:fill-gray-400" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M3.04199 9.37381C3.04199 5.87712 5.87735 3.04218 9.37533 3.04218C12.8733 3.04218 15.7087 5.87712 15.7087 9.37381C15.7087 12.8705 12.8733 15.7055 9.37533 15.7055C5.87735 15.7055 3.04199 12.8705 3.04199 9.37381ZM9.37533 1.54218C5.04926 1.54218 1.54199 5.04835 1.54199 9.37381C1.54199 13.6993 5.04926 17.2055 9.37533 17.2055C11.2676 17.2055 13.0032 16.5346 14.3572 15.4178L17.1773 18.2381C17.4702 18.531 17.945 18.5311 18.2379 18.2382C18.5308 17.9453 18.5309 17.4704 18.238 17.1775L15.4182 14.3575C16.5367 13.0035 17.2087 11.2671 17.2087 9.37381C17.2087 5.04835 13.7014 1.54218 9.37533 1.54218Z" fill=""/>
                            </svg>
                        </button>
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search..." class="h-[42px] w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pl-[42px] pr-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800 xl:w-[300px]"/>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-hidden">
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
                <table class="min-w-full">
                    <thead>
                    <tr class="border-gray-200 border-y dark:border-gray-700">
                        <th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
                            <x-form.input.checkbox name="selectAll" @click="togglePage" x-bind:checked="isAllPageSelected()" />
                        </th>
                        <th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">Nazwa</th>
                        <th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">Telefon</th>
                        <th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">Pesel</th>
                        <th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">Numer prawa jazdy</th>
                        <th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">Data wygaśnięcia prawa jazdy</th>
                        <th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">Data wygaśnięcia badań medycznych</th>
                        <th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">Aktywny</th>
                        <th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">Akcje</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($drivers as $driver)
                            <tr wire:key="driver-row-{{ $driver->id }}">
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        <x-form.input.checkbox name="check_{{ $driver->id }}" value="{{ $driver->id }}" x-model="selected" />
                                    </div>
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
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $driver->driving_license_number ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $driver->license_expiry_date ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $driver->medical_exam_valid_until ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $driver->is_active ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500 dark:text-gray-400 flex space-x-2">
                                        <a href="#">
                                            <x-heroicon-o-pencil-square class="w-6 h-6 hover:text-green-500" />
                                        </a>
                                        <a href="">
                                            <x-heroicon-o-trash class="w-6 h-6 hover:text-red-500" />
                                        </a>

                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{ $drivers->links() }}
    </div>
</div>

<script>
    {{--function checkboxTable() {--}}
    {{--    return {--}}
    {{--        selectAll: false,--}}
    {{--        selected: [],--}}
    {{--        items: @json($drivers) ?? [],--}}

    {{--        toggleAll() {--}}
    {{--        console.log('items:', this.items);--}}
    {{--        const allIds = Object.values(this.items).map(item => item.id);--}}
    {{--        this.selected = this.selectAll ? allIds : [];--}}
    {{--    },--}}

    {{--    updateSelectAll() {--}}
    {{--        this.selectAll = this.selected.length === Object.values(this.items).length;--}}
    {{--    }--}}
    {{--}--}}
    {{--}--}}
</script>
