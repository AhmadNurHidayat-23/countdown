@if ($paginator->hasPages())
    <nav class="pagination" aria-label="Halaman countdown">
        @if($paginator->onFirstPage())<span>Sebelumnya</span>@else<a href="{{ $paginator->previousPageUrl() }}#koleksi" rel="prev">← Sebelumnya</a>@endif
        <span>{{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}</span>
        @if($paginator->hasMorePages())<a href="{{ $paginator->nextPageUrl() }}#koleksi" rel="next">Berikutnya →</a>@else<span>Berikutnya</span>@endif
    </nav>
@endif
