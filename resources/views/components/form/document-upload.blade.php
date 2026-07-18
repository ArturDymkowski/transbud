<div class="flex flex-col gap-2 p-3 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800">
    <x-form.input.file-input
        :name="'driverData.' . $field"
        :label="$label"
        wire:model="driverData.{{ $field }}" />

    @if($hasPendingFile())
        {{-- jeszcze niezapisany plik --}}
            <div class="mt-1 aspect-[16/10] w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-950 flex items-center justify-center overflow-hidden relative">
                @php
                    $iconUrl = str_starts_with($file->getMimeType(), 'image/') ? $file->temporaryUrl() : asset('images/icons/file-pdf.svg')
                @endphp
                <img src="{{ $iconUrl }}" class="w-full h-full" alt="{{ __('labels.general.document_preview') }}">

                <button type="button"
                        wire:click="removeDocument('{{ $field }}')"
                        wire:loading.attr="disabled"
                        class="absolute top-2 right-2 flex h-6 w-6 items-center justify-center rounded-full bg-gray-900/70 text-white hover:bg-red-600 transition-colors"
                        title="{{ __('labels.general.delete_document') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
    @elseif($hasExistingMedia())
        {{-- plik już zapisany w bazie --}}
        <div class="mt-1 aspect-[16/10] w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-950 flex items-center justify-center overflow-hidden relative">
            @php
                $existingMedia = $existingMediaId;
                $previewUrl = $existingMedia && $existingMedia['mime_type'] === 'application/pdf'
                    ? asset('images/icons/file-pdf.svg')
                    : ($existingMedia ? route('driver-documents.show', $existingMedia['id']) : null);
            @endphp
            <img src="{{ $previewUrl }}" class="w-full h-full" alt="{{ __('labels.general.document_preview') }}">

            <div class="absolute top-2 right-2 flex items-center gap-1.5">
                <button type="button"
                        wire:click="downloadDocument('{{ $field }}')"
                        wire:loading.attr="disabled"
                        class="flex h-6 w-6 items-center justify-center rounded-full bg-gray-900/70 text-white hover:bg-brand-600 transition-colors"
                        title="{{ __('labels.general.download_document') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10.75 2.75a.75.75 0 00-1.5 0v7.19L6.03 6.72a.75.75 0 00-1.06 1.06l4.5 4.5a.75.75 0 001.06 0l4.5-4.5a.75.75 0 10-1.06-1.06l-3.22 3.22V2.75z" />
                        <path d="M3.5 12.75a.75.75 0 00-1.5 0v2.5A2.75 2.75 0 004.75 18h10.5A2.75 2.75 0 0018 15.25v-2.5a.75.75 0 00-1.5 0v2.5c0 .69-.56 1.25-1.25 1.25H4.75c-.69 0-1.25-.56-1.25-1.25v-2.5z" />
                    </svg>
                </button>

                <button type="button"
                        wire:click="removeDocument('{{ $field }}')"
                        wire:loading.attr="disabled"
                        class="flex h-6 w-6 items-center justify-center rounded-full bg-gray-900/70 text-white hover:bg-red-600 transition-colors"
                        title="{{ __('labels.general.delete_document') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>
    @else
        <div class="mt-1 h-12 w-full rounded-lg border border-dashed border-gray-200 dark:border-gray-800 bg-gray-100/50 dark:bg-gray-950/50 flex items-center justify-center">
            <span class="text-xs text-gray-400 dark:text-gray-500 font-medium">{{ __('labels.general.no_preview') }}</span>
        </div>
    @endif

    @error('driverData.' . $field)
    <span class="text-xs text-red-500">{{ $message }}</span>
    @enderror
</div>
