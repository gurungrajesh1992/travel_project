@props(['paginator'])

@if ($paginator->hasPages())
    <nav class="flex items-center justify-between border-t border-gray-200 px-2 py-4">
        <div class="text-sm text-gray-600">
            Showing {{ $paginator->firstItem() }}-{{ $paginator->lastItem() }} of {{ $paginator->total() }}
        </div>
        <div class="flex gap-1">
            @if ($paginator->onFirstPage())
                <span class="px-3 py-1.5 text-sm rounded-md text-gray-400">Prev</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-1.5 text-sm rounded-md text-gray-700 hover:bg-gray-100">Prev</a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-1.5 text-sm rounded-md text-gray-700 hover:bg-gray-100">Next</a>
            @else
                <span class="px-3 py-1.5 text-sm rounded-md text-gray-400">Next</span>
            @endif
        </div>
    </nav>
@endif
