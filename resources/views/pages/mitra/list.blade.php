@extends('layouts.master')

@section('style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
@endsection

@section('content')
<div class="card">
    <h5 class="card-header">Daftar Mitra Anda</h5>
    <div class="row m-3">
        <div class="col-md-3">
            <label for="filterStatus" class="form-label">Filter Status</label>
            <select id="filterStatus" class="form-select">
                <option value="">Semua Status</option>
                <option value="active">Active</option>
                <option value="nonactive">Nonactive</option>
            </select>
        </div>
        <div class="col-md-3 align-self-end">
            <button id="filterButton" class="btn btn-primary">Filter</button>
            <button id="resetButton" class="btn btn-secondary">Reset</button>
        </div>
    </div>
    
    <div class="card-datatable table-responsive pt-0">
        <table class="datatables-basic table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Level</th>
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
        var table = dt_basic_table.DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('mitra.list') }}",
                data: function (d) {
                    d.level = $('#filterLevel').val();
                    d.status = $('#filterStatus').val();
                }
            },
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
                { data: 'level', name: 'level' },
                { data: 'status', name: 'status' }
            ],
            order: [[1, 'asc']],
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            lengthMenu: [10, 25, 50, 75, 100],
            responsive: true
        });

        // Event listener untuk tombol Filter
        $('#filterButton').on('click', function () {
            table.draw();
        });

        // Event listener untuk tombol Reset
        $('#resetButton').on('click', function () {
            $('#filterLevel').val('');
            $('#filterStatus').val('');
            table.draw();
        });
    }
});
</script>
@endsection
