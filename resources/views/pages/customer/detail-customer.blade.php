@extends('layouts.master')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row gy-4 justify-content-center">
            <!-- Card untuk Avatar dan Detail Akun -->
            <div class="col-xl-6 col-lg-6 col-md-8">
                <div class="card h-100 mb-4">
                    <div class="card-body text-center">
                        <!-- Avatar Section -->
                        <div class="user-avatar-section mb-4">
                            @if ($customer->picture_ktp)
                                <img class="img-fluid rounded-circle mb-3" src="{{ asset($customer->picture_ktp) }}" height="120" width="120" alt="{{ $customer->name }}" />
                            @else
                                <div class="avatar avatar-xl mb-3 bg-label-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 120px; height: 120px;">
                                    <span class="avatar-initial fs-4">{{ strtoupper(substr($customer->name ?? 'U', 0, 2)) }}</span>
                                </div>
                            @endif
                            <div class="user-info text-center">
                                <h5 class="mb-1">{{ $customer->name ?? 'Tidak ada informasi' }}</h5>
                                <span class="badge bg-label-primary rounded-pill">{{ $customer->code }}</span>
                            </div>
                        </div>

                        <!-- Detail Akun -->
                        <h5 class="pb-3 border-bottom mb-4">Detail Akun</h5>
                        <div class="info-container">
                            <ul class="list-unstyled">
                                <li class="mb-3 d-flex align-items-center">
                                    <i class="ri-mail-line me-2 text-primary"></i>
                                    <span class="fw-medium text-heading me-2">Email:</span>
                                    <span>{{ $customer->email ?? 'Tidak ada informasi' }}</span>
                                </li>
                                <li class="mb-3 d-flex align-items-center">
                                    <i class="ri-phone-line me-2 text-success"></i>
                                    <span class="fw-medium text-heading me-2">Phone:</span>
                                    <span>{{ $customer->phone ?? 'Tidak ada informasi' }}</span>
                                </li>
                                <li class="mb-3 d-flex align-items-center">
                                    <i class="ri-user-2-line me-2 text-warning"></i>
                                    <span class="fw-medium text-heading me-2">Status:</span>
                                    <span class="badge bg-label-{{ $customer->status === 'active' ? 'success' : 'danger' }}">
                                        {{ ucfirst($customer->status) ?? 'Tidak ada informasi' }}
                                    </span>
                                </li>
                                <li class="mb-3 d-flex align-items-center">
                                    <i class="ri-file-text-line me-2 text-secondary"></i>
                                    <span class="fw-medium text-heading me-2">NIK:</span>
                                    <span>{{ $customer->NIK ?? 'Tidak ada informasi' }}</span>
                                </li>
                                <li class="mb-3 d-flex align-items-center">
                                    <i class="ri-calendar-line me-2 text-info"></i>
                                    <span class="fw-medium text-heading me-2">Tanggal Lahir:</span>
                                    <span>{{ \Carbon\Carbon::parse($customer->birth_date)->format('d M Y') }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card untuk Informasi Customer -->
            <div class="col-xl-6 col-lg-6 col-md-8">
                <div class="card h-100 mb-4">
                    <div class="card-header py-3">
                        <h5 class="mb-0">Informasi Customer</h5>
                    </div>
                    <div class="card-body py-4">
                        <div class="row g-4">
                            <!-- Username -->
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center gap-3">
                                    <i class="ri-user-3-line ri-24px text-info"></i>
                                    <div>
                                        <span class="d-block text-muted small">Username</span>
                                        <span class="fw-semibold">{{ $customer->username ?? 'Tidak ada informasi' }}</span>
                                    </div>
                                </div>
                            </div>
                            <!-- Alamat -->
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center gap-3">
                                    <i class="ri-map-pin-line ri-24px text-success"></i>
                                    <div>
                                        <span class="d-block text-muted small">Alamat</span>
                                        <span class="fw-semibold">{{ $customer->address ?? 'Tidak ada informasi' }}</span>
                                    </div>
                                </div>
                            </div>
                            <!-- Pekerjaan -->
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center gap-3">
                                    <i class="ri-briefcase-line ri-24px text-secondary"></i>
                                    <div>
                                        <span class="d-block text-muted small">Pekerjaan</span>
                                        <span class="fw-semibold">{{ $customer->job ?? 'Tidak ada informasi' }}</span>
                                    </div>
                                </div>
                            </div>
                            <!-- Cabang -->
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center gap-3">
                                    <i class="ri-store-2-line ri-24px text-warning"></i>
                                    <div>
                                        <span class="d-block text-muted small">Cabang</span>
                                        <span class="fw-semibold">{{ $customer->cabang->name ?? 'Tidak ada informasi' }}</span>
                                    </div>
                                </div>
                            </div>
                            <!-- Mitra -->
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center gap-3">
                                    <i class="ri-user-star-line ri-24px text-danger"></i>
                                    <div>
                                        <span class="d-block text-muted small">Mitra</span>
                                        <span class="fw-semibold">{{ $customer->mitra->name ?? 'Tidak ada informasi' }}</span>
                                    </div>
                                </div>
                            </div>
                            <!-- Program -->
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center gap-3">
                                    <i class="ri-clipboard-line ri-24px text-primary"></i>
                                    <div>
                                        <span class="d-block text-muted small">Program</span>
                                        <span class="fw-semibold">{{ $customer->program->name ?? 'Tidak ada informasi' }}</span>
                                    </div>
                                </div>
                            </div>
                            <!-- Kota -->
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center gap-3">
                                    <i class="ri-building-line ri-24px text-info"></i>
                                    <div>
                                        <span class="d-block text-muted small">Kota</span>
                                        <span class="fw-semibold">{{ $customer->city->name ?? 'Tidak ada informasi' }}</span>
                                    </div>
                                </div>
                            </div>
                            <!-- Provinsi -->
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center gap-3">
                                    <i class="ri-map-pin-2-line ri-24px text-success"></i>
                                    <div>
                                        <span class="d-block text-muted small">Provinsi</span>
                                        <span class="fw-semibold">{{ $customer->province->name ?? 'Tidak ada informasi' }}</span>
                                    </div>
                                </div>
                            </div>
                            <!-- Status Prospek -->
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center gap-3">
                                    <i class="ri-heart-line ri-24px text-warning"></i>
                                    <div>
                                        <span class="d-block text-muted small">Status Prospek</span>
                                        <span class="fw-semibold">
                                            <span class="badge bg-label-{{ $customer->status_prospek === 'active' ? 'success' : 'secondary' }}">
                                                {{ ucfirst($customer->status_prospek) }}
                                            </span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <!-- Status Jamaah -->
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center gap-3">
                                    <i class="ri-user-heart-line ri-24px text-danger"></i>
                                    <div>
                                        <span class="d-block text-muted small">Status Jamaah</span>
                                        <span class="fw-semibold">
                                            <span class="badge bg-label-{{ $customer->status_jamaah === 'active' ? 'success' : 'secondary' }}">
                                                {{ ucfirst($customer->status_jamaah) }}
                                            </span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <!-- Status Alumni -->
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center gap-3">
                                    <i class="ri-user-follow-line ri-24px text-primary"></i>
                                    <div>
                                        <span class="d-block text-muted small">Status Alumni</span>
                                        <span class="fw-semibold">
                                            <span class="badge bg-label-{{ $customer->status_alumni === 'active' ? 'success' : 'secondary' }}">
                                                {{ ucfirst($customer->status_alumni) }}
                                            </span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <!-- Catatan -->
                            <div class="col-12">
                                <div class="d-flex align-items-start gap-3">
                                    <i class="ri-file-text-line ri-24px text-secondary mt-1"></i>
                                    <div>
                                        <span class="d-block text-muted small">Catatan</span>
                                        <span class="fw-semibold">{{ $customer->note ?? 'Tidak ada informasi' }}</span>
                                    </div>
                                </div>
                            </div>
                            <!-- Birth Place -->
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center gap-3">
                                    <i class="ri-map-pin-2-line ri-24px text-info"></i>
                                    <div>
                                        <span class="d-block text-muted small">Tempat Lahir</span>
                                        <span class="fw-semibold">{{ $customer->birth_place ?? 'Tidak ada informasi' }}</span>
                                    </div>
                                </div>
                            </div>
                            <!-- Birth Date -->
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center gap-3">
                                    <i class="ri-calendar-line ri-24px text-primary"></i>
                                    <div>
                                        <span class="d-block text-muted small">Tanggal Lahir</span>
                                        <span class="fw-semibold">{{ \Carbon\Carbon::parse($customer->birth_date)->format('d M Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection