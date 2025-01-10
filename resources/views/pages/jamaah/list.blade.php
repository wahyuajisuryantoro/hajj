@extends('layouts.master')

@section('style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
@endsection

@section('content')
<div class="card">
    <h5 class="card-header">Daftar Jamaah Anda</h5>
    <div class="card-datatable table-responsive pt-0">
        <table class="datatables-basic table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Pekerjaan</th>
                    <th>Total Pembayaran</th>
                    <th>Status Pembayaran</th>
                    <th>Status Keberangkatan</th>
                    <th>Status</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
<script>
$(function () {
    var dt_basic_table = $('.datatables-basic');
    
    if (dt_basic_table.length) {
        dt_basic_table.DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('jamaah.list') }}",
            columns: [
                { 
                    data: 'DT_RowIndex', 
                    name: 'DT_RowIndex', 
                    orderable: false, 
                    searchable: false
                },
                { data: 'full_name', name: 'name' },
                { data: 'email', name: 'email' },
                { data: 'phone', name: 'phone' },
                { data: 'job', name: 'job' },
                { data: 'total_payment', name: 'total_payment' },
                { data: 'status_payment', name: 'status_payment' },
                { data: 'status_berangkat', name: 'status_berangkat' },
                { data: 'status', name: 'status' }
            ],
            order: [[1, 'asc']],
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            lengthMenu: [10, 25, 50, 75, 100],
            responsive: true
        });
    }
});
</script>
@endsection