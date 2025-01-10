@extends('layouts.master')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row gy-4">
            <!-- Jamaah Sidebar -->
            <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
                <!-- Jamaah Card -->
                <div class="card mb-4">
                    <div class="card-body pt-4">
                        <div class="user-avatar-section">
                            <div class="d-flex align-items-center flex-column">
                                @if ($jamaah->picture_profile)
                                    <img class="img-fluid rounded-3" src="{{ $jamaah->picture_profile }}"
                                        style="max-width: 120px; height: auto;" alt="{{ $jamaah->name }}" />
                                @else
                                    <div class="avatar avatar-xl mb-3">
                                        <span class="avatar-initial bg-label-primary rounded-3">
                                            {{ strtoupper(substr($jamaah->name ?? 'U', 0, 2)) }}
                                        </span>
                                    </div>
                                @endif
                                <div class="user-info text-center">
                                    <h5>{{ $jamaah->name ?? 'Tidak ada informasi' }}</h5>
                                    <span class="badge bg-label-primary rounded-pill">{{ $jamaah->code }}</span>
                                </div>
                            </div>
                        </div>

                        <h5 class="pb-3 border-bottom mb-4">Detail Akun</h5>
                        <div class="info-container">
                            <ul class="list-unstyled">
                                <li class="mb-3">
                                    <span class="fw-medium text-heading me-2">Email:</span>
                                    <span>{{ $jamaah->email ?? 'Tidak ada informasi' }}</span>
                                </li>
                                <li class="mb-3">
                                    <span class="fw-medium text-heading me-2">Phone:</span>
                                    <span>{{ $jamaah->phone ?? 'Tidak ada informasi' }}</span>
                                </li>
                                <li class="mb-3">
                                    <span class="fw-medium text-heading me-2">Status:</span>
                                    <span class="badge bg-label-{{ $jamaah->status === 'active' ? 'success' : 'danger' }}">
                                        {{ ucfirst($jamaah->status) ?? 'Tidak ada informasi' }}
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- /Jamaah Card -->
            </div>
            <!--/ Jamaah Sidebar -->

            <!-- Jamaah Content -->
            <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">
                <!-- Program Info -->
                <div class="card mb-4">
                    <div class="card-header py-3">
                        <h5 class="mb-0">Informasi Program</h5>
                    </div>
                    <div class="card-body py-4">
                        <div class="row g-4">
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar">
                                        <div class="avatar-initial bg-label-primary rounded">
                                            <i class="ri-star-s-line ri-24px"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <span class="d-block text-muted small">Nama Program</span>
                                        <span
                                            class="fw-semibold">{{ $jamaah->program->name ?? 'Tidak ada informasi' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar">
                                        <div class="avatar-initial bg-label-info rounded">
                                            <i class="ri-calendar-line ri-24px"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <span class="d-block text-muted small">Tanggal Program</span>
                                        <span
                                            class="fw-semibold">{{ $jamaah->date_program ? $jamaah->date_program->format('d M Y') : 'Tidak ada informasi' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar">
                                        <div class="avatar-initial bg-label-success rounded">
                                            <i class="ri-map-pin-line ri-24px"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <span class="d-block text-muted small">Kota Program</span>
                                        <span
                                            class="fw-semibold">{{ $jamaah->city_program ?? 'Tidak ada informasi' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar">
                                        <div class="avatar-initial bg-label-secondary rounded">
                                            <i class="ri-user-line ri-24px"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <span class="d-block text-muted small">Mitra</span>
                                        <span
                                            class="fw-semibold">{{ $jamaah->mitra->name ?? 'Tidak ada informasi' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Program Info -->

                <!-- Payment Info -->
                <div class="card mb-4">
                    <div class="card-header py-3">
                        <h5 class="mb-0">Informasi Pembayaran</h5>
                    </div>
                    <div class="card-body py-4">
                        <div class="row g-4">
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar">
                                        <div class="avatar-initial bg-label-warning rounded">
                                            <i class="ri-money-dollar-circle-line ri-24px"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <span class="d-block text-muted small">Nilai</span>
                                        <span
                                            class="fw-semibold">{{ 'Rp ' . number_format($jamaah->value ?? 0, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar">
                                        <div class="avatar-initial bg-label-danger rounded">
                                            <i class="ri-refund-2-line ri-24px"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <span class="d-block text-muted small">Total Pembayaran</span>
                                        <span
                                            class="fw-semibold">{{ $jamaah->formatted_total_payment ?? 'Tidak ada informasi' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar">
                                        <div class="avatar-initial bg-label-secondary rounded">
                                            <i class="ri-bank-card-line ri-24px"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <span class="d-block text-muted small">Status Pembayaran</span>
                                        <span
                                            class="fw-semibold">{{ $jamaah->status_payment_label ?? 'Tidak ada informasi' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar">
                                        <div class="avatar-initial bg-label-primary rounded">
                                            <i class="ri-flight-takeoff-line ri-24px"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <span class="d-block text-muted small">Status Keberangkatan</span>
                                        <span
                                            class="fw-semibold">{{ $jamaah->status_berangkat_label ?? 'Tidak ada informasi' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Payment Info -->
            </div>
        </div>
        <div class="mt-4">
            <a href="{{ url()->previous() }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back
            </a>
        </div>
    </div>
@endsection
