@extends('layouts.master')

@section('style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
@endsection

@section('content')
    <div class="card">
        <h5 class="card-header">Daftar Customer</h5>
        <div class="card-datatable table-responsive pt-0">
            <table class="datatables-basic table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>No. Telepon</th>
                        <th>Status</th>
                        <th>Status Prospek</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="modal fade" id="paymentStatusModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-simple">
                <div class="modal-content p-3">
                    <div class="modal-header">
                        <h5 class="modal-title">Status Pembayaran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body px-0">
                        <div class="p-3">
                            <h6 class="mb-1" id="customerName"></h6>
                            <p class="text-muted mb-3" id="programInfo">
                                <i class="ri-flight-takeoff-line me-1"></i>
                                <span id="programName"></span>
                            </p>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Nominal</th>
                                        <th class="text-center">Bukti</th>
                                    </tr>
                                </thead>
                                <tbody id="paymentHistoryBody"></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
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
                    ajax: "{{ route('customer.list') }}",
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'name',
                            name: 'name',
                            render: function(data, type, row) {
                                return '<a href="' + "{{ route('customer.show', '') }}" + '/' + row
                                    .id + '">' + data + '</a>';
                            }
                        },
                        {
                            data: 'username',
                            name: 'username'
                        },
                        {
                            data: 'email',
                            name: 'email'
                        },
                        {
                            data: 'phone',
                            name: 'phone'
                        },
                        {
                            data: 'status',
                            name: 'status'
                        },
                        {
                            data: 'status_prospek',
                            name: 'status_prospek'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    order: [
                        [1, 'asc']
                    ],
                    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                    lengthMenu: [10, 25, 50, 75, 100],
                    responsive: true
                });
            }
        });
    </script>
    <script>
        let paymentModal;
        
        $(function() {
            paymentModal = new bootstrap.Modal(document.getElementById('paymentStatusModal'));
            
            // DataTable initialization code tetap sama
        });
    
        function showPaymentModal(customerCode) {
            $.ajax({
                url: `/customers/${customerCode}/payments`,
                method: 'GET',
                success: function(response) {
                    if(response.success) {
                        const data = response.data;
                        
                        // Update customer dan program info
                        $('#customerName').text(data.customer_name);
                        $('#programName').text(data.program_name || 'Program tidak tersedia');
                        
                        // Update riwayat pembayaran
                        let historyHtml = '';
                        if (data.payments.length > 0) {
                            data.payments.forEach(item => {
                                historyHtml += `
                                    <tr>
                                        <td>${formatDate(item.tanggal_transaksi)}</td>
                                        <td>${formatRupiah(item.value)}</td>
                                        <td class="text-center">
                                            ${item.picture_scan ? 
                                                `<a href="${item.picture_scan}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <i class="ri-image-line"></i> Lihat
                                                </a>` : '-'}
                                        </td>
                                    </tr>
                                `;
                            });
                        } else {
                            historyHtml = `
                                <tr>
                                    <td colspan="3" class="text-center">Belum ada riwayat pembayaran</td>
                                </tr>
                            `;
                        }
                        $('#paymentHistoryBody').html(historyHtml);
                        
                        // Tampilkan modal
                        paymentModal.show();
                    }
                }
            });
        }
    
        function formatRupiah(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR'
            }).format(amount);
        }
    
        function formatDate(date) {
            return new Date(date).toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        }
    </script>
@endsection
