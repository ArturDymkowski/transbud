@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb
        pageTitle="{{ __('labels.dashboard.title') }}"
        :breadcrumbs="[
        __('labels.dashboard.title') => null
    ]"
    />

    <h3 class="mb-4 text-lg font-semibold text-gray-800 dark:text-white/90">
        {{ __('labels.dashboard.expiring_documents') }}
    </h3>

    @php
        $boxes = [
            [
                'key' => \App\Helpers\ExpiryHelper::GREEN,
                'title' => __('labels.dashboard.box_30_days'),
                'container' => 'border-green-200 bg-green-50 dark:border-green-500/30 dark:bg-green-500/10',
                'header' => 'text-green-700 dark:text-green-400',
                'itemBorder' => 'border-green-200 dark:border-green-500/20',
            ],
            [
                'key' => \App\Helpers\ExpiryHelper::YELLOW,
                'title' => __('labels.dashboard.box_14_days'),
                'container' => 'border-yellow-200 bg-yellow-50 dark:border-yellow-500/30 dark:bg-yellow-500/10',
                'header' => 'text-yellow-700 dark:text-yellow-400',
                'itemBorder' => 'border-yellow-200 dark:border-yellow-500/20',
            ],
            [
                'key' => \App\Helpers\ExpiryHelper::RED,
                'title' => __('labels.dashboard.box_7_days'),
                'container' => 'border-red-200 bg-red-50 dark:border-red-500/30 dark:bg-red-500/10',
                'header' => 'text-red-700 dark:text-red-400',
                'itemBorder' => 'border-red-200 dark:border-red-500/20',
            ],
        ];
    @endphp

    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
        @foreach($boxes as $box)
            <div class="rounded-2xl border p-5 {{ $box['container'] }}">
                <h3 class="mb-4 text-base font-semibold {{ $box['header'] }}">
                    {{ $box['title'] }}
                </h3>

                @if(empty($grouped[$box['key']]))
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ __('labels.dashboard.empty_box') }}
                    </p>
                @else
                    <div class="custom-scrollbar flex max-h-[420px] flex-col gap-3 overflow-y-auto pr-1">
                        @foreach($grouped[$box['key']] as $item)
                            <a href="{{ $item['route'] }}" wire:navigate
                                class="block rounded-lg border bg-white p-3 transition hover:shadow-sm dark:bg-gray-900 {{ $box['itemBorder'] }}">
                                <p class="text-sm font-medium text-gray-800 dark:text-white/90">
                                    {{ $item['subject'] }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $item['label'] }}: {{ $item['date'] }}
                                </p>
                                <p class="mt-1 text-xs font-medium {{ $box['header'] }}">
                                    @if($item['days'] < 0)
                                        {{ $item['days'] === -1 ? __('labels.dashboard.expired_one_day_ago') : __('labels.dashboard.expired_days_ago', ['days' => abs($item['days'])]) }}
                                    @elseif($item['days'] === 0)
                                        {{ __('labels.dashboard.expires_today') }}
                                    @elseif($item['days'] === 1)
                                        {{ __('labels.dashboard.day_left') }}
                                    @else
                                        {{ __('labels.dashboard.days_left', ['days' => $item['days']]) }}
                                    @endif
                                </p>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
    </div>
@endsection
