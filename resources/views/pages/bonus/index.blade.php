@extends('layouts.master')

@section('style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/app-logistics-dashboard.css') }}" />
@endsection

@section('content')
    <div class="row g-6 mb-6"> 
        <div class="col-sm-6 col-lg-4">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded-3 bg-label-primary">
                                <i class="ri-money-dollar-circle-line ri-24px"></i>
                            </span>
                        </div>
                        <h4 class="mb-0">Rp {{ number_format($totalBonus, 0, ',', '.') }}</h4>
                    </div>
                    <h6 class="mb-0 fw-normal">Total Rekap Bonus</h6>
                    <p class="mb-0">
                        <span class="me-1 fw-medium">{{ number_format($bonusPercentage, 1) }}%</span>
                        <small class="text-muted">dari minggu lalu</small>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4">
            <div class="card card-border-shadow-warning h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded-3 bg-label-warning">
                                <i class="ri-exchange-dollar-line ri-24px"></i>
                            </span>
                        </div>
                        <h4 class="mb-0">Rp {{ number_format($totalTransfer, 0, ',', '.') }}</h4>
                    </div>
                    <h6 class="mb-0 fw-normal">Total Komisi Ditransfer</h6>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4">
            <div class="card card-border-shadow-info h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded-3 bg-label-info">
                                <i class="ri-wallet-3-line ri-24px"></i>
                            </span>
                        </div>
                        <h4 class="mb-0">Rp {{ number_format($saldo, 0, ',', '.') }}</h4>
                    </div>
                    <h6 class="mb-0 fw-normal">Saldo Bonus</h6>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Data -->
    <div class="col-12 order-5">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Riwayat Transaksi Bonus</h5>
            </div>
            <div class="card-datatable table-responsive">
                <table class="datatables-basic table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Nama Jamaah</th>
                            <th>Keterangan</th>
                            <th>Nilai</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    
    <script>
        $(function() {
            var dt_basic_table = $('.datatables-basic');
            
            if (dt_basic_table.length) {
                dt_basic_table.DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('bonus.index') }}",
                    columns: [
                        { 
                            data: 'DT_RowIndex', 
                            name: 'DT_RowIndex', 
                            orderable: false, 
                            searchable: false
                        },
                        { 
                            data: 'tanggal_transaksi', 
                            name: 'tanggal_transaksi',
                            orderable: true,
                            searchable: true
                        },
                        { 
                            data: 'nama_jamaah', 
                            name: 'jamaah.name',  // menggunakan nama relasi dan kolom untuk pengurutan
                            orderable: true,
                            searchable: true
                        },
                        { 
                            data: 'desc', 
                            name: 'desc',
                            orderable: true,
                            searchable: true,
                            defaultContent: 'Tidak ada deskripsi untuk transaksi ini',
                            render: function(data, type, row) {
                                if (type === 'display') {
                                    return data || 'Tidak ada deskripsi untuk transaksi ini';
                                }
                                return data;
                            }
                        },
                        { 
                            data: 'value', 
                            name: 'value',
                            orderable: true,
                            searchable: true
                        },
                        { 
                            data: 'status', 
                            name: 'status',
                            orderable: true,
                            searchable: true
                        }
                    ],
                    order: [[1, 'desc']],
                    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                    lengthMenu: [10, 25, 50, 75, 100],
                    responsive: true
                });
            }
        });
    </script>
@endsection