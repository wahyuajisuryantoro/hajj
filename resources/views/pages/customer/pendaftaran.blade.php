@extends('layouts.master')
@section('style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/spinkit/spinkit.css') }}" />

    <style>
        /* Overlay Spinner */
        .spinner-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            /* Ensure the spinner appears on top */
            display: none;
            /* Hide the spinner initially */
        }

        /* Center spinner content */
        .spinner-container {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Show spinner when active */
        #loading-spinner.show {
            display: flex;
        }
    </style>
@endsection
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card mb-4">
            <h5 class="card-header">Pendaftaran Customer Baru</h5>
            <form class="card-body" method="POST" action="{{ route('customer.store') }}" enctype="multipart/form-data">
                <div id="loading-spinner" class="spinner-overlay">
                    <div class="spinner-container">
                        <div class="sk-fold sk-primary">
                            <div class="sk-fold-cube"></div>
                            <div class="sk-fold-cube"></div>
                            <div class="sk-fold-cube"></div>
                            <div class="sk-fold-cube"></div>
                        </div>
                    </div>
                </div>
                @csrf
                <h6>1. Mitra Pengaju</h6>
                <div class="row g-3">
                    {{-- <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="username" name="username" class="form-control" placeholder="john.doe"
                                value="{{ old('username') }}" required />
                            <label for="username">Username</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="email" id="email" name="email" class="form-control"
                                placeholder="john@example.com" value="{{ old('email') }}" />
                            <label for="email">Email</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-password-toggle">
                            <div class="input-group input-group-merge">
                                <div class="form-floating form-floating-outline">
                                    <input type="password" id="password" name="password" class="form-control" />
                                    <label for="password">Password</label>
                                </div>
                                <span class="input-group-text cursor-pointer"><i class="ri-eye-off-line"></i></span>
                            </div>
                        </div>
                    </div> --}}
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="code_mitra" name="code_mitra" class="form-control"
                                value="{{ Auth::guard('mitra')->user()->code }}" />
                            <label for="code_mitra">Mitra Pengaju</label>
                        </div>
                    </div>
                </div>

                <hr class="my-4 mx-n4" />
                <h6>2. Data Pribadi</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="name" name="name" class="form-control"
                                placeholder="Nama Lengkap" value="{{ old('name') }}" required />
                            <label for="name">Nama Lengkap</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="NIK" name="NIK" class="form-control"
                                placeholder="1234567890123456" value="{{ old('NIK') }}" required />
                            <label for="NIK">NIK</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <select id="sex" name="sex" class="form-select" required>
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="L" {{ old('sex') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('sex') == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                            <label for="sex">Jenis Kelamin</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="birth_place" name="birth_place" class="form-control"
                                placeholder="Tempat Lahir" value="{{ old('birth_place') }}" />
                            <label for="birth_place">Tempat Lahir</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="birth_date" name="birth_date" class="form-control flatpickr"
                                placeholder="YYYY-MM-DD" value="{{ old('birth_date') }}" />
                            <label for="birth_date">Tanggal Lahir</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="phone" name="phone" class="form-control phone-mask"
                                placeholder="6285700154847" value="{{ old('phone') }}" required />
                            <label for="phone">No. Telepon</label>
                        </div>
                    </div>
                    <hr class="my-4 mx-n4" />
                    <h6>3. Data Alamat</h6>
                    <div class="col-12">
                        <div class="form-floating form-floating-outline">
                            <textarea class="form-control" id="address" name="address" rows="3" placeholder="Alamat Lengkap">{{ old('address') }}</textarea>
                            <label for="address">Alamat Lengkap</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <select id="code_province" name="code_province" class="select2 form-select">
                                <option value="">Pilih Provinsi</option>
                                @foreach ($provinces as $province)
                                    <option value="{{ $province->code }}"
                                        {{ old('code_province') == $province->code ? 'selected' : '' }}>
                                        {{ $province->name }}
                                    </option>
                                @endforeach
                            </select>
                            <label for="code_province">Provinsi</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <select id="code_city" name="code_city" class="select2 form-select">
                                <option value="">Pilih Kota/Kabupaten</option>
                                @foreach ($cities as $city)
                                    <option value="{{ $city->code }}"
                                        {{ old('code_city') == $city->code ? 'selected' : '' }}>
                                        {{ $city->name }}
                                    </option>
                                @endforeach
                            </select>
                            <label for="code_city">Kota/Kabupaten</label>
                        </div>
                    </div>
                    {{-- <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <select id="code_cabang" name="code_cabang" class="form-select">
                                <option value="">Pilih Cabang</option>
                                @foreach ($cabangs as $cabang)
                                    <option value="{{ $cabang->code }}"
                                        {{ old('code_cabang') == $cabang->code ? 'selected' : '' }}>
                                        {{ $cabang->name }}
                                    </option>
                                @endforeach
                            </select>
                            <label for="code_cabang">Cabang</label>
                        </div>
                    </div>
                    --}}
                    {{-- <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <select id="status_prospek" name="status_prospek" class="form-select" required>
                                <option value="">Pilih Status Prospek</option>
                                <option value="cold" {{ old('status_prospek') == 'cold' ? 'selected' : '' }}>Cold</option>
                                <option value="warm" {{ old('status_prospek') == 'warm' ? 'selected' : '' }}>Warm</option>
                                <option value="hot" {{ old('status_prospek') == 'hot' ? 'selected' : '' }}>Hot</option>
                            </select>
                            <label for="status_prospek">Status Prospek</label>
                        </div>
                    </div> --}}
                    {{-- <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <select id="code_category" name="code_category" class="form-select">
                                <option value="">Pilih Kategori</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->code }}"
                                        {{ old('code_category') == $category->code ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <label for="code_category">Kategori</label>
                        </div>
                    </div> --}}
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <select id="code_program" name="code_program" class="form-select">
                                <option value="">Pilih Program</option>
                                @foreach ($programs as $program)
                                    <option value="{{ $program->code }}"
                                        {{ old('code_program') == $program->code ? 'selected' : '' }}>
                                        {{ $program->name }}
                                    </option>
                                @endforeach
                            </select>
                            <label for="code_program">Program</label>
                        </div>
                    </div>
                </div>

                <hr class="my-4 mx-n4" />
                <h6>3. Dokumen</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="file" id="picture_ktp" name="picture_ktp" class="form-control"
                                accept="image/*" required />
                            <label for="picture_ktp">Foto KTP</label>
                            @if (session('errors') && session('errors')->has('picture_ktp'))
                                <div class="text-danger mt-1">
                                    {{ session('errors')->first('picture_ktp') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="pt-4">

                    <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
                    <button type="reset" class="btn btn-label-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <!-- Vendors JS -->
    <script src="{{ asset('assets/vendor/libs/cleavejs/cleave.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/cleavejs/cleave-phone.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/js/form-layouts.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Initialize Flatpickr
            $('.flatpickr').flatpickr({
                dateFormat: "Y-m-d"
            });

            // Initialize Select2
            $('.select2').select2();

            // Initialize phone mask
            new Cleave('.phone-mask', {
                phone: true,
                phoneRegionCode: 'ID'
            });

            $('form').on('submit', function(e) {
                e.preventDefault();

                // Show the spinner and disable submit button
                $('#loading-spinner').show();
                $('button[type="submit"]').prop('disabled', true);

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message
                            }).then((result) => {
                                $('form')[0].reset();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message ||
                                    'Terjadi kesalahan pada sistem.'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan pada sistem. Silakan coba lagi nanti.'
                        });
                    },
                    complete: function() {
                        $('#loading-spinner').hide();
                        $('button[type="submit"]').prop('disabled', false);
                    }
                });
            });
        });
    </script>
@endsection
