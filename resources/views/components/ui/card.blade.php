@props(['title' => null, 'subtitle' => null, 'collapsible' => false, 'collapsed' => false])

@php
    $isCollapsible = $collapsible && $title;
@endphp

<div
    @if ($isCollapsible) x-data="{ open: @js(!$collapsed) }" @endif
    {{ $attributes->merge(['class' => 'bg-white rounded-lg border border-gray-200 shadow-sm']) }}
>
    @if ($title || isset($actions))
        <div
            @if ($isCollapsible)
                @click="open = !open"
                role="button"
                tabindex="0"
                @keydown.enter="open = !open"
                @keydown.space.prevent="open = !open"
                :aria-expanded="open.toString()"
            @endif
            class="flex items-center justify-between px-5 py-4 border-b border-gray-200 {{ $isCollapsible ? 'cursor-pointer select-none' : '' }}"
        >
            <div>
                @if ($title)
                    <h3 class="text-sm font-semibold text-gray-900">{{ $title }}</h3>
                @endif
                @if ($subtitle)
                    <p class="text-xs text-gray-500 mt-0.5">{{ $subtitle }}</p>
                @endif
            </div>
            <div class="flex items-center gap-3 shrink-0">
                @isset($actions)
                    <div class="flex items-center gap-2" @if ($isCollapsible) @click.stop @endif>{{ $actions }}</div>
                @endisset
                @if ($isCollapsible)
                    <button type="button" class="p-1 rounded hover:bg-gray-100 text-gray-500">
                        <svg :class="{ 'rotate-180': open }" class="h-4 w-4 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>
                @endif
            </div>
        </div>
    @endif

    <div class="p-5" @if ($isCollapsible) x-show="open" x-cloak @endif>
        {{ $slot }}
    </div>
</div>
