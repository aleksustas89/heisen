@if ($paginator->hasPages())

        <div class="uk-flex uk-flex-middle uk-flex-between uk-section-xsmall pagination">
            <div>
                <p class="small text-muted">
                    Показано с
                    <span class="fw-semibold">{{ $paginator->firstItem() }}</span>
                    по
                    <span class="fw-semibold">{{ $paginator->lastItem() }}</span>, всего: 
                    <span class="fw-semibold">{{ $paginator->total() }}</span>
                </p>
            </div>

            <div class=" tm-pagen">
                <ul class="uk-pagination" uk-margin>
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <li class="page-item disabled uk-flex uk-flex-center" aria-disabled="true" aria-label="@lang('pagination.previous')">
                            <span class="uk-flex uk-flex-center" uk-pagination-previous></span>
                        </li>
                    @else
                        <li class="page-item uk-flex uk-flex-center">
                            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">
                                <span uk-pagination-previous></span>
                            </a>
                        </li>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <li class="disabled" aria-disabled="true"><span>{{ $element }}</span></li>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <li class="uk-active" aria-current="page"><span>{{ $page }}</span></li>
                                @else
                                    <li><a href="{{ $url }}">{{ $page }}</a></li>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <li class="page-item uk-flex uk-flex-center">
                            <a class="js-pagination-more" data-n="{{ $paginator->currentPage() + 1 }}" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">
                                <span uk-pagination-next></span>
                            </a>
                        </li>
                    @else
                        <li class="page-item disabled uk-flex uk-flex-center" aria-disabled="true" aria-label="@lang('pagination.next')">
                            <span class="uk-flex uk-flex-center" uk-pagination-next></span>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
@endif