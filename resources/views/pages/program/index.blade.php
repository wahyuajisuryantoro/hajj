@extends('layouts.master')
@section('style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/app-academy.css') }}" />
@endsection
@section('content')
    <div class="card mb-6">
        <div class="card-header d-flex flex-wrap justify-content-between gap-4">
            <div class="card-title mb-0 me-1">
                <h5 class="mb-0">Program Umrah</h5>
                <p class="mb-0 text-body">Total {{ $programs->total() }} program tersedia</p>
            </div>
            <div class="d-flex justify-content-md-end align-items-center gap-6 flex-wrap">
                <select class="form-select form-select-sm w-px-250" name="category">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->code }}" {{ request('category') == $category->code ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            
                <div class="form-check form-switch mb-0">
                    <input type="checkbox" class="form-check-input" id="hidePassedPrograms" name="hide_past" 
                        {{ request('hide_past') ? 'checked' : '' }} />
                    <label class="form-check-label text-nowrap mb-0" for="hidePassedPrograms">Sembunyikan yang sudah lewat</label>
                </div>
            </div>
        </div>
        <div class="card-body mt-1">
            <div class="row gy-6 mb-6">
                @forelse($programs as $program)
                    <div class="col-sm-6 col-lg-4">
                        <div class="card p-2 h-100 shadow-none border rounded-3">
                            <div class="rounded-4 text-center mb-5">
                                <img class="img-fluid" src="{{ $program->picture }}" 
                                     alt="{{ $program->name }}" />
                            </div>
                            <div class="card-body p-3 pt-0">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <span class="badge rounded-pill bg-label-primary">{{ $program->code_category }}</span>
                                    {{-- <p class="d-flex align-items-center justify-content-center fw-medium gap-1 mb-0">
                                        {{ $program->sisa_kursi }}/{{ $program->kuota }} <span class="text-warning">
                                            <i class="ri-user-line ri-24px me-1"></i>
                                        </span>
                                    </p> --}}
                                </div>
                                <a href="{{ route('member.program.show', $program->code) }}" class="h5">{{ $program->name }}</a>
                                <p class="mt-1">{{ Str::limit($program->desc, 100) }}</p>
                                <p class="d-flex align-items-center mb-1">
                                    <i class="ri-time-line ri-20px me-1"></i>{{ $program->duration }} Hari
                                </p>
                                <p class="d-flex align-items-center mb-1">
                                    <i class="ri-calendar-line ri-20px me-1"></i>{{ $program->formatted_tanggal_berangkat }}
                                </p>
                                <p class="h5 mb-4">{{ $program->formatted_price }}</p>
                                
                                <div class="d-flex flex-column flex-md-row gap-4 text-nowrap">
                                    <a class="w-100 btn btn-outline-primary d-flex align-items-center"
                                        href="{{ route('member.program.show', $program->code) }}">
                                        <span class="me-2">Detail Program</span>
                                        <i class="ri-arrow-right-line ri-16px lh-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center">
                        <p>Tidak ada program yang tersedia saat ini.</p>
                    </div>
                @endforelse
            </div>
            
           
        </div>
    </div>
    {{ $programs->links('vendor.pagination.custom') }}
@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle category filter change
            const categorySelect = document.querySelector('select[name="category"]');
            categorySelect.addEventListener('change', function() {
                updateFilters();
            });

            // Handle hide past programs toggle
            const hidePastCheckbox = document.querySelector('input[name="hide_past"]');
            hidePastCheckbox.addEventListener('change', function() {
                updateFilters();
            });

            function updateFilters() {
                const category = categorySelect.value;
                const hidePast = hidePastCheckbox.checked;
                
                let url = new URL(window.location.href);
                url.searchParams.set('category', category);
                url.searchParams.set('hide_past', hidePast ? '1' : '');
                
                window.location.href = url.toString();
            }
        });
    </script>
@endsection