@extends('layouts.master')
@section('content')
    <div class="row g-6">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap mb-6 gap-1">
                        <div class="me-1">
                            <h5 class="mb-0">{{ $program->name }}</h5>
                            <p class="mb-0">Kode Program: <span class="fw-medium text-heading">{{ $program->code }}</span>
                            </p>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge bg-label-primary rounded-pill">{{ $program->code_category }}</span>
                            <a href="{{ route('member.program') }}" class="btn btn-outline-primary ms-4">
                                <i class="ri-arrow-left-line ri-24px"></i> Kembali
                            </a>
                        </div>
                    </div>
                    <div class="card academy-content shadow-none border">
                        <div class="p-2">
                            <div class="cursor-pointer">
                                <img src="{{ $program->picture }}" alt="{{ $program->name }}" class="w-100 rounded"
                                    style="max-height: 600px; object-fit: cover;">
                            </div>
                        </div>
                        <div class="card-body pt-3">
                            @php
                                $formatted_desc = $program->formatted_desc;
                            @endphp

                            {{-- Program Info --}}
                            <div class="d-flex flex-wrap gap-3 mb-4">
                                @if ($formatted_desc['pesawat'])
                                    <div class="badge bg-label-info">
                                        <i class="ri-plane-line me-1"></i>
                                        {{ $formatted_desc['pesawat'] }}
                                    </div>
                                @endif
                                @if ($formatted_desc['durasi'])
                                    <div class="badge bg-label-primary">
                                        <i class="ri-time-line me-1"></i>
                                        {{ $formatted_desc['durasi'] }} Hari
                                    </div>
                                @endif
                            </div>

                            {{-- Hotel Info --}}
                            <div class="mb-4">
                                <h5 class="mb-3">Akomodasi Hotel</h5>
                                <div class="row g-3">
                                    @foreach ($formatted_desc['hotels'] as $hotel)
                                        <div class="col-md-6">
                                            <div class="p-3 border rounded">
                                                <p class="fw-medium mb-1">{{ $hotel['lokasi'] }}</p>
                                                <p class="mb-0 text-muted">
                                                    <i class="ri-hotel-line me-2"></i>{{ $hotel['nama'] }}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Fasilitas --}}
                            @if (count($formatted_desc['fasilitas']) > 0)
                                <div class="mb-4">
                                    <h5 class="mb-3">Fasilitas Program</h5>
                                    <div class="row g-3">
                                        @foreach (array_chunk($formatted_desc['fasilitas'], ceil(count($formatted_desc['fasilitas']) / 2)) as $chunk)
                                            <div class="col-md-6">
                                                <ul class="list-unstyled mb-0">
                                                    @foreach ($chunk as $fasilitas)
                                                        <li class="mb-2">
                                                            <i
                                                                class="ri-check-line text-success me-2"></i>{{ $fasilitas }}
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Non Fasilitas --}}
                            @if (count($formatted_desc['non_fasilitas']) > 0)
                                <div class="mb-4">
                                    <h5 class="mb-3">Tidak Termasuk</h5>
                                    <ul class="list-unstyled">
                                        @foreach ($formatted_desc['non_fasilitas'] as $item)
                                            <li class="mb-2">
                                                <i class="ri-close-line text-danger me-2"></i>{{ $item }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            {{-- Pembayaran --}}
                            @if (count($formatted_desc['pembayaran']) > 0)
                                <div class="bg-label-primary p-4 rounded">
                                    <h5 class="mb-3">Ketentuan Pembayaran</h5>
                                    <ul class="list-unstyled mb-0">
                                        @foreach ($formatted_desc['pembayaran'] as $ketentuan)
                                            <li class="mb-2">
                                                <i class="ri-secure-payment-line me-2"></i>{{ $ketentuan }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <hr class="my-6" />

                            <h5>Informasi Program</h5>
                            <div class="d-flex flex-wrap row-gap-2">
                                <div class="me-12">
                                    <p class="text-nowrap mb-2">
                                        <i class="ri-calendar-line ri-20px me-2"></i>Keberangkatan:
                                        {{ $program->formatted_tanggal_berangkat }}
                                    </p>
                                    <p class="text-nowrap mb-2">
                                        <i class="ri-time-line ri-20px me-2"></i>Durasi: {{ $program->duration }} Hari
                                    </p>
                                    <p class="text-nowrap mb-2">
                                        <i class="ri-map-pin-line ri-20px me-2"></i>Kota: {{ $program->code_city }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-nowrap mb-2">
                                        <i class="ri-group-line ri-20px me-2"></i>Kuota: {{ $program->kuota }} Jamaah
                                    </p>
                                    <p class="text-nowrap mb-2">
                                        <i class="ri-user-line ri-20px me-2"></i>Sisa Kursi: {{ $program->sisa_kursi }}
                                    </p>
                                    <p class="text-nowrap mb-0">
                                        <i class="ri-money-dollar-circle-line ri-20px me-2"></i>Harga:
                                        {{ $program->formatted_price }}
                                    </p>
                                </div>
                            </div>

                            <hr class="my-6" />

                            <div class="d-grid gap-2">
                                @if ($program->sisa_kursi > 0)
                                    <p class="text-center text-success mb-0">
                                        <i class="ri-checkbox-circle-line me-1"></i>Masih tersedia
                                        {{ $program->sisa_kursi }} kursi
                                    </p>
                                @else
                                    <p class="text-center text-danger mb-0">
                                        <i class="ri-error-warning-line me-1"></i>Kuota sudah penuh
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
