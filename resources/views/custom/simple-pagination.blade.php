@if ($paginator->hasPages())
    <nav aria-label="Simple Pagination Navigation" class="d-flex justify-content-between align-items-center">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="btn btn-outline-secondary disabled">
                <i class="bx bx-chevron-left me-1"></i>
                Sebelumnya
            </span>
        @else
            <a class="btn btn-outline-primary" href="{{ $paginator->previousPageUrl() }}" rel="prev">
                <i class="bx bx-chevron-left me-1"></i>
                Sebelumnya
            </a>
        @endif

        {{-- Pagination Info --}}
        <span class="text-muted small">
            Halaman {{ $paginator->currentPage() }} dari {{ $paginator->lastPage() }}
        </span>

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a class="btn btn-outline-primary" href="{{ $paginator->nextPageUrl() }}" rel="next">
                Selanjutnya
                <i class="bx bx-chevron-right ms-1"></i>
            </a>
        @else
            <span class="btn btn-outline-secondary disabled">
                Selanjutnya
                <i class="bx bx-chevron-right ms-1"></i>
            </span>
        @endif
    </nav>
@endif
