@if ($paginator->hasPages())
    <nav aria-label="Page navigation" class="d-flex align-items-center justify-content-center">
        <ul class="pagination mb-0">
            {{-- Tombol Halaman Pertama --}}
            <li class="page-item first {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
                <a class="page-link" href="{{ $paginator->url(1) }}">
                    <i class="tf-icon ri-skip-back-mini-line ri-22px"></i>
                </a>
            </li>

            {{-- Tombol Sebelumnya --}}
            <li class="page-item prev {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
                <a class="page-link" href="{{ $paginator->previousPageUrl() }}">
                    <i class="tf-icon ri-arrow-left-s-line ri-22px"></i>
                </a>
            </li>

            {{-- Nomor Halaman --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="page-item disabled">
                        <span class="page-link">{{ $element }}</span>
                    </li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <li class="page-item {{ $page == $paginator->currentPage() ? 'active' : '' }}">
                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                        </li>
                    @endforeach
                @endif
            @endforeach

            {{-- Tombol Selanjutnya --}}
            <li class="page-item next {{ $paginator->hasMorePages() ? '' : 'disabled' }}">
                <a class="page-link" href="{{ $paginator->nextPageUrl() }}">
                    <i class="tf-icon ri-arrow-right-s-line ri-22px"></i>
                </a>
            </li>

            {{-- Tombol Halaman Terakhir --}}
            <li class="page-item last {{ $paginator->hasMorePages() ? '' : 'disabled' }}">
                <a class="page-link" href="{{ $paginator->url($paginator->lastPage()) }}">
                    <i class="tf-icon ri-skip-forward-mini-line ri-22px"></i>
                </a>
            </li>
        </ul>
    </nav>
@endif