@extends('landing.layouts.app')

@section('content')
<div class="container my-5">
    <h1 class="mb-4 text-center fw-bold">Jelajahi Program Kami</h1>
    <p class="text-center text-muted mb-5">Temukan berbagai pengalaman perjalanan menarik yang dirancang khusus untuk Anda</p>

    <div class="row">
        <div class="col-lg-3">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h4 class="card-title mb-4">Saring Pencarian</h4>
                    <form action="{{ route('programs.index') }}" method="GET">
                        <div class="mb-3">
                            <label for="search" class="form-label">Cari Program</label>
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
                            <label class="form-label">Rentang Harga</label>
                            <div class="row g-2">
                                <div class="col">
                                    <input type="number" class="form-control" id="min_price" name="min_price" value="{{ request('min_price') }}" placeholder="Min">
                                </div>
                                <div class="col">
                                    <input type="number" class="form-control" id="max_price" name="max_price" value="{{ request('max_price') }}" placeholder="Max">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="sort" class="form-label">Urutkan Berdasarkan</label>
                            <select class="form-select" id="sort" name="sort">
                                <option value="tanggal_berangkat" {{ request('sort') == 'tanggal_berangkat' ? 'selected' : '' }}>Tanggal Berangkat</option>
                                <option value="price" {{ request('sort') == 'price' ? 'selected' : '' }}>Harga</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="direction" class="form-label">Arah Pengurutan</label>
                            <select class="form-select" id="direction" name="direction">
                                <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>Naik</option>
                                <option value="desc" {{ request('direction') == 'desc' ? 'selected' : '' }}>Turun</option>
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
                @foreach($programs as $program)
                    <div class="col">
                        <div class="card h-100 shadow-sm hover-shadow">
                            <img src="{{ $program->picture ?: asset('images/default-program.jpg') }}" class="card-img-top" alt="{{ $program->name }}">
                            <div class="card-body">
                                <h5 class="card-title">{{ $program->name }}</h5>
                                <p class="card-text text-primary fw-bold">{{ $program->formatted_price }}</p>
                                <p class="card-text"><small class="text-muted">Keberangkatan: {{ $program->formatted_tanggal_berangkat }}</small></p>
                            </div>
                            <div class="card-footer bg-transparent border-top-0 text-center">
                                <a href="{{ route('programs.show', $program->id) }}" class="btn btn-outline-primary btn-sm">Lihat Detail</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $programs->links('vendor.pagination.custom-landing') }}
            </div>
        </div>
    </div>
</div>
@endsection
