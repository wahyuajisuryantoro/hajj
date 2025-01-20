@extends('landing.layouts.app')

@section('content')
<div class="container my-5">
    <h1 class="mb-4 text-center fw-bold">Jelajahi Berita Kami</h1>
    <p class="text-center text-muted mb-5">Temukan informasi terkini dan menarik seputar perjalanan dan wisata</p>

    <div class="row">
        <div class="col-lg-3">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h4 class="card-title mb-4">Saring Pencarian</h4>
                    <form action="{{ route('news.index') }}" method="GET">
                        <div class="mb-3">
                            <label for="search" class="form-label">Cari Berita</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Masukkan kata kunci...">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="category" class="form-label">Kategori</label>
                            <select class="form-select" id="category" name="category">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->code }}" {{ request('category') == $category->code ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="sort" class="form-label">Urutkan Berdasarkan</label>
                            <select class="form-select" id="sort" name="sort">
                                <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Tanggal</option>
                                <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Judul</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="direction" class="form-label">Arah Pengurutan</label>
                            <select class="form-select" id="direction" name="direction">
                                <option value="desc" {{ request('direction') == 'desc' ? 'selected' : '' }}>Terbaru</option>
                                <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>Terlama</option>
                            </select>
                        </div>
                        <input type="hidden" name="page" value="1">
                        <button type="submit" class="btn btn-primary w-100">Terapkan Filter</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                @foreach($news as $item)
                    <div class="col">
                        <div class="card h-100 shadow-sm hover-shadow">
                            <img src="{{ $item->picture ?: asset('images/default-news.jpg') }}" class="card-img-top" alt="{{ $item->name }}">
                            <div class="card-body">
                                <h5 class="card-title">{{ $item->name }}</h5>
                                <p class="card-text"><small class="text-muted">{{ $item->created_at->format('d M Y') }}</small></p>
                            </div>
                            <div class="card-footer bg-transparent border-top-0 text-center">
                                <a href="{{ $item->url }}" class="btn btn-outline-primary btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; line-height: 1.5; border-radius: 0.2rem;">Baca Selengkapnya</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $news->links('vendor.pagination.custom-landing') }}
            </div>
        </div>
    </div>
</div>
@endsection
