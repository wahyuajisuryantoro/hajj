@extends('layouts.master')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card mb-4">
            <h5 class="card-header">Pendaftaran Mitra Baru</h5>
            <form class="card-body" method="POST" action="{{ route('mitra.store') }}" enctype="multipart/form-data">
                @csrf
                <h6>1. Informasi Akun</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="username" name="username" class="form-control" placeholder="john.doe"
                                required />
                            <label for="username">Username</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="email" id="email" name="email" class="form-control"
                                placeholder="john@example.com" />
                            <label for="email">Email</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-password-toggle">
                            <div class="input-group input-group-merge">
                                <div class="form-floating form-floating-outline">
                                    <input type="password" id="password" name="password" class="form-control" required />
                                    <label for="password">Password</label>
                                </div>
                                <span class="input-group-text cursor-pointer"><i class="ri-eye-off-line"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="mitra_info" name="mitra_info" 
                                   class="form-control" readonly 
                                   value="{{ $mitraInfo }}" />
                            <label for="mitra_info">Mitra Pengaju</label>
                        </div>
                    </div>   
                </div>

                <hr class="my-4 mx-n4" />
                <h6>2. Data Pribadi</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="name" name="name" class="form-control" required />
                            <label for="name">Nama Lengkap</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="NIK" name="NIK" class="form-control" />
                            <label for="NIK">NIK</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <select id="sex" name="sex" class="form-select" required>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                            <label for="sex">Jenis Kelamin</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="birth_place" name="birth_place" class="form-control" />
                            <label for="birth_place">Tempat Lahir</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="birth_date" name="birth_date" class="form-control flatpickr" />
                            <label for="birth_date">Tanggal Lahir</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="phone" name="phone" class="form-control phone-mask" required />
                            <label for="phone">No. Telepon</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-floating form-floating-outline">
                            <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                            <label for="address">Alamat Lengkap</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="code_province" name="code_province" class="form-control"
                                placeholder="Masukkan provinsi" />
                            <label for="code_province">Provinsi</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="code_city" name="code_city" class="form-control"
                                placeholder="Masukkan kota/kabupaten" />
                            <label for="code_city">Kota/Kabupaten</label>
                        </div>
                    </div>
                                              
                </div>

                <hr class="my-4 mx-n4" />
                <h6>3. Informasi Bank</h6>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="form-floating form-floating-outline">
                            <select id="bank" name="bank" class="select2 form-select">
                                <option value="BCA">BCA</option>
                                <option value="BNI">BNI</option>
                                <option value="BRI">BRI</option>
                                <option value="Mandiri">Mandiri</option>
                            </select>
                            <label for="bank">Bank</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="bank_number" name="bank_number" class="form-control" />
                            <label for="bank_number">Nomor Rekening</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="bank_name" name="bank_name" class="form-control" />
                            <label for="bank_name">Nama Pemilik Rekening</label>
                        </div>
                    </div>
                </div>

                <hr class="my-4 mx-n4" />
                <h6>4. Dokumen</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="file" id="picture_profile" name="picture_profile" class="form-control"
                                accept="image/*" />
                            <label for="picture_profile">Foto Profile</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="file" id="picture_ktp" name="picture_ktp" class="form-control"
                                accept="image/*" />
                            <label for="picture_ktp">Foto KTP</label>
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

    <div class="modal fade" id="mitraModal" tabindex="-1" aria-labelledby="mitraModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mitraModalLabel">Pilih Mitra</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-12">
                            <input type="text" id="searchMitra" class="form-control" placeholder="Cari mitra..." />
                        </div>
                    </div>
                    <div class="row" id="mitraList"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kembali</button>
                    <button type="button" class="btn btn-primary" id="selectMitra">Simpan</button>
                </div>
            </div>
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

            // Inisialisasi pencarian mitra
            $('#searchMitra').on('input', function() {
                var query = $(this).val();
                $.ajax({
                    url: '{{ route('mitra.getParentMitra') }}',
                    data: {
                        search: query
                    },
                    success: function(data) {
                        var mitraList = $('#mitraList');
                        mitraList.empty();

                        // Tampilkan hasil pencarian
                        $.each(data, function(index, mitra) {
                            var mitraItem = $('<div class="col-md-4 mb-3"></div>')
                                .append($('<div class="card cursor-pointer"></div>')
                                    .data('mitra', mitra)
                                    .click(function() {
                                        var selectedMitra = $(this).data('mitra');
                                        $('#parent_mitra').val(
                                            `${selectedMitra.name} (${selectedMitra.code})`
                                            );
                                        $('#code_mitra').val(selectedMitra.code);
                                        $('#mitraModal').modal('hide');
                                    })
                                    .append($(
                                            '<div class="card-body text-center"></div>')
                                        .append($('<h5 class="card-title mb-0"></h5>')
                                            .text(mitra.name))
                                        .append($(
                                                '<p class="card-text text-muted"></p>')
                                            .text(`(${mitra.code})`))
                                    )
                                );
                            mitraList.append(mitraItem);
                        });
                    }
                });
            });

            // Simpan mitra yang dipilih
            $('#selectMitra').click(function() {
                var selectedMitra = $('.card.cursor-pointer.active').data('mitra');
                if (selectedMitra) {
                    $('#parent_mitra').val(`${selectedMitra.name} (${selectedMitra.code})`);
                    $('#code_mitra').val(selectedMitra.code);
                    $('#mitraModal').modal('hide');
                }
            });
        });
    </script>
@endsection
