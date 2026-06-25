@props(['pageTitle' => 'Page', 'breadcrumbs' => []])

<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
        {{ $pageTitle }}
    </h2>
    <nav>
        <ol class="flex items-center gap-1.5">
            @foreach($breadcrumbs as $label => $url)
                <li class="flex items-center gap-1.5 text-sm">
                    @if($url)
                        <a href="{{ $url }}" class="inline-flex items-center gap-1.5 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 transition-colors">
                            {{ $label }}
                        </a>
                    @else
                        {{-- Element nieklikalny --}}
                        <span class="text-gray-800 dark:text-white/90 font-medium">
                            {{ $label }}
                        </span>
                    @endif

                    @if(!$loop->last)
                        <svg
                            class="stroke-current text-gray-400"
                            width="17"
                            height="16"
                            viewBox="0 0 17 16"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M6.0765 12.667L10.2432 8.50033L6.0765 4.33366"
                                stroke-width="1.2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
</div>
