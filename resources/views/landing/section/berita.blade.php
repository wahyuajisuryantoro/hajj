<!-- start news list -->
<section class="section news-list" id="berita">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center gap-2">
                <div>
                    <h5 class="mb-0">Berita</h5>
                    <small class="text-muted">Daftar Berita Kami</small>
                </div>
            </div>
        </div>
        <div class="list-group mb-4">
            @forelse($news as $item)
            <a href="#" class="list-group-item list-group-item-action border-0 mb-3">
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <img src="{{ $item->picture ?: asset('images/default-news.jpg') }}" 
                             alt="{{ $item->name }}" 
                             class="rounded"
                             style="width: 100px; height: 70px; object-fit: cover;">
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1">{{ $item->name }}</h6>
                        <small class="text-muted">
                            {{ \Carbon\Carbon::parse($item->created_at)->format('l, d F Y H:i') }}
                        </small>
                    </div>
                </div>
            </a>
            @empty
            <div class="text-center py-4">
                <p class="text-muted">Tidak ada berita tersedia</p>
            </div>
            @endforelse
        </div>
    </div>
</section>
<!-- end news list -->