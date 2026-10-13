@if ($paginator->hasPages())
<nav>
    <ul class="pagination pagination-sm justify-content-center mb-0 mt-3">
        @if ($paginator->onFirstPage())
            <li class="page-item disabled"><span class="page-link"><i class="ri-skip-back-mini-line"></i></span></li>
            <li class="page-item disabled"><span class="page-link"><i class="ri-arrow-left-s-line"></i></span></li>
        @else
            <li class="page-item"><button class="page-link" data-page="1"><i class="ri-skip-back-mini-line"></i></button></li>
            <li class="page-item"><button class="page-link" data-page="{{ $paginator->currentPage() - 1 }}"><i class="ri-arrow-left-s-line"></i></button></li>
        @endif

        @for ($i = 1; $i <= $paginator->lastPage(); $i++)
            @if ($i == $paginator->currentPage())
                <li class="page-item active"><span class="page-link">{{ $i }}</span></li>
            @else
                <li class="page-item"><button class="page-link" data-page="{{ $i }}">{{ $i }}</button></li>
            @endif
        @endfor

        @if ($paginator->hasMorePages())
            <li class="page-item"><button class="page-link" data-page="{{ $paginator->currentPage() + 1 }}"><i class="ri-arrow-right-s-line"></i></button></li>
            <li class="page-item"><button class="page-link" data-page="{{ $paginator->lastPage() }}"><i class="ri-skip-forward-mini-line"></i></button></li>
        @else
            <li class="page-item disabled"><span class="page-link"><i class="ri-arrow-right-s-line"></i></span></li>
            <li class="page-item disabled"><span class="page-link"><i class="ri-skip-forward-mini-line"></i></span></li>
        @endif
    </ul>
</nav>
@endif
