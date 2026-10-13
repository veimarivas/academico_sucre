@if ($paginator->hasPages())
    <nav>
        <ul class="pagination pagination-sm justify-content-center mb-0 mt-3">
            @if ($paginator->onFirstPage())
                <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
            @else
                <li class="page-item">
                    <button class="page-link" data-page="{{ $paginator->currentPage() - 1 }}">&laquo;</button>
                </li>
            @endif

            @for ($i = 1; $i <= $paginator->lastPage(); $i++)
                @if ($i == $paginator->currentPage())
                    <li class="page-item active"><span class="page-link">{{ $i }}</span></li>
                @else
                    <li class="page-item">
                        <button class="page-link" data-page="{{ $i }}">{{ $i }}</button>
                    </li>
                @endif
            @endfor

            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <button class="page-link" data-page="{{ $paginator->currentPage() + 1 }}">&raquo;</button>
                </li>
            @else
                <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
            @endif
        </ul>
    </nav>
@endif