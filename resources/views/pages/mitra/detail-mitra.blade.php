@extends('layouts.master')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Card 1: Profile Summary -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <!-- Profile Picture -->
                <div class="col-md-2 text-center">
                    @if ($mitra->picture_profile)
                        <img class="img-fluid rounded-3" src="{{ $mitra->picture_profile }}" 
                             style="max-width: 120px; height: auto;" alt="{{ $mitra->name }}" />
                    @else
                        <div class="avatar">
                            <span class="avatar-initial bg-label-primary rounded-3 h5 m-0">
                                {{ strtoupper(substr($mitra->name ?? 'U', 0, 2)) }}
                            </span>
                        </div>
                    @endif
                </div>
                
                <!-- Basic Info -->
                <div class="col-md-5">
                    <h5>{{ $mitra->name ?? 'Tidak ada informasi' }}</h5>
                    <span class="badge bg-label-primary mb-2">{{ ucfirst($mitra->level) }}</span>
                    
                    <div class="mt-3">
                        <p class="mb-2">
                            <i class="ri-profile-line me-2"></i>
                            <strong>Kode Mitra:</strong> {{ $mitra->code ?? 'Tidak ada informasi' }}
                        </p>
                        <p class="mb-2">
                            <i class="ri-door-lock-box-line me-2"></i>
                            <strong>Kode Referral:</strong> {{ $mitra->referral_code ?? 'Tidak ada informasi' }}
                        </p>
                        <p class="mb-0">
                            <i class="ri-phone-line me-2"></i>
                            <strong>Phone:</strong> {{ $mitra->phone_number ?? 'Tidak ada informasi' }}
                        </p>
                    </div>
                </div>

                <!-- Status Info -->
                <div class="col-md-5">
                    <div class="d-flex flex-column h-100 justify-content-center">
                        <p class="mb-2">
                            <strong>Status:</strong>
                            <span class="badge bg-label-{{ $mitra->status === 'active' ? 'success' : 'danger' }} ms-2">
                                {{ ucfirst($mitra->status) ?? 'Tidak ada informasi' }}
                            </span>
                        </p>
                        <p class="mb-2">
                            <strong>Email:</strong> {{ $mitra->email ?? 'Tidak ada informasi' }}
                        </p>
                        <p class="mb-0">
                            <strong>NIK:</strong> {{ $mitra->NIK ?? 'Tidak ada informasi' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 2: Personal Information -->
    <div class="card mb-4">
        <div class="card-header py-3 d-flex align-items-center">
            <i class="ri-user-info-line me-2 ri-lg"></i>
            <h5 class="mb-0">Informasi Personal</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <p class="mb-1"><strong>Tempat & Tanggal Lahir:</strong></p>
                        <p>{{ $mitra->birth_place && $mitra->birth_date ? $mitra->birth_place . ', ' . $mitra->birth_date->format('d M Y') : 'Tidak ada informasi' }}</p>
                    </div>
                    <div class="mb-3">
                        <p class="mb-1"><strong>Jenis Kelamin:</strong></p>
                        <p>{{ $mitra->sex ? ($mitra->sex === 'L' ? 'Male' : 'Female') : 'Tidak ada informasi' }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <p class="mb-1"><strong>Alamat:</strong></p>
                        <p>{{ $mitra->address ?? 'Tidak ada informasi' }}</p>
                    </div>
                    <div class="mb-3">
                        <p class="mb-1"><strong>Provinsi - Kota:</strong></p>
                        <p>{{ $mitra->code_province && $mitra->code_city ? 
                            $mitra->code_province . ' - ' . $mitra->code_city : 
                            'Tidak ada informasi' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 3: Bank & Network Information -->
    <div class="card">
        <div class="card-header py-3 d-flex align-items-center">
            <i class="ri-bank-card-line me-2 ri-lg"></i>
            <h5 class="mb-0">Informasi Bank & Jaringan</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Bank Information -->
                <div class="col-md-6">
                    <h6 class="mb-3">Informasi Bank</h6>
                    <div class="mb-3">
                        <p class="mb-1"><strong>Bank:</strong></p>
                        <p>{{ $mitra->bank ?? 'Tidak ada informasi' }}</p>
                    </div>
                    <div class="mb-3">
                        <p class="mb-1"><strong>Nomor Rekening:</strong></p>
                        <p>{{ $mitra->bank_number ?? 'Tidak ada informasi' }}</p>
                    </div>
                    <div class="mb-3">
                        <p class="mb-1"><strong>Pemilik Rekening:</strong></p>
                        <p>{{ $mitra->bank_name ?? 'Tidak ada informasi' }}</p>
                    </div>
                </div>

                <!-- Network Information -->
                <div class="col-md-6">
                    <h6 class="mb-3">Informasi Jaringan</h6>
                    <div class="mb-3">
                        <p class="mb-1"><strong>Kategori:</strong></p>
                        <p>{{ $mitra->code_category ?? 'Tidak ada informasi' }}</p>
                    </div>
                    <div class="mb-3">
                        <p class="mb-1"><strong>Cabang:</strong></p>
                        <p>{{ $mitra->code_cabang ?? 'Tidak ada informasi' }}</p>
                    </div>
                    <div class="mb-3">
                        <p class="mb-1"><strong>Upline:</strong></p>
                        @if($mitra->parent)
                            <p class="mb-0">{{ $mitra->parent->name }} ({{ $mitra->parent->code }})</p>
                        @else
                            <p class="mb-0">Tidak ada informasi</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Back Button -->
    <div class="mt-4">
        <a href="{{ url()->previous() }}" class="btn btn-secondary">
            <i class="ri-arrow-left-line me-1"></i> Kembali
        </a>
    </div>
</div>
@endsection