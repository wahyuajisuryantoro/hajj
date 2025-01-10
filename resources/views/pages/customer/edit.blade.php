@extends('layouts.master')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card mb-4">
            <h5 class="card-header">Edit Data Customer</h5>
            <form class="card-body" method="POST" action="{{ route('customer.update', $customer->id) }}"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <h6>1. Informasi Akun</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="username" name="username" class="form-control" placeholder="john.doe"
                                value="{{ old('username', $customer->username) }}" required />
                            <label for="username">Username</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="email" id="email" name="email" class="form-control"
                                placeholder="john@example.com" value="{{ old('email', $customer->email) }}" />
                            <label for="email">Email</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-password-toggle">
                            <div class="input-group input-group-merge">
                                <div class="form-floating form-floating-outline">
                                    <input type="password" id="password" name="password" class="form-control"
                                        placeholder="Kosongkan jika tidak ingin mengubah password" />
                                    <label for="password">Password (Opsional)</label>
                                </div>
                                <span class="input-group-text cursor-pointer"><i class="ri-eye-off-line"></i></span>
                            </div>
                            <small class="text-muted">Kosongkan jika tidak ingin mengubah password</small>
                        </div>
                    </div>
                </div>

                <hr class="my-4 mx-n4" />
                <h6>2. Data Pribadi</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="name" name="name" class="form-control"
                                placeholder="Nama Lengkap" value="{{ old('name', $customer->name) }}" required />
                            <label for="name">Nama Lengkap</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="NIK" name="NIK" class="form-control"
                                placeholder="1234567890123456" value="{{ old('NIK', $customer->NIK) }}" required />
                            <label for="NIK">NIK</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <select id="sex" name="sex" class="form-select" required>
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="L" {{ old('sex', $customer->sex) == 'L' ? 'selected' : '' }}>Laki-laki
                                </option>
                                <option value="P" {{ old('sex', $customer->sex) == 'P' ? 'selected' : '' }}>Perempuan
                                </option>
                            </select>
                            <label for="sex">Jenis Kelamin</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="birth_place" name="birth_place" class="form-control"
                                placeholder="Tempat Lahir" value="{{ old('birth_place', $customer->birth_place) }}" />
                            <label for="birth_place">Tempat Lahir</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="birth_date" name="birth_date" class="form-control flatpickr"
                                placeholder="YYYY-MM-DD" value="{{ old('birth_date', $customer->birth_date) }}" />
                            <label for="birth_date">Tanggal Lahir</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="phone" name="phone" class="form-control phone-mask"
                                placeholder="6285700154847" value="{{ old('phone', $customer->phone) }}" required />
                            <label for="phone">No. Telepon</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-floating form-floating-outline">
                            <textarea class="form-control" id="address" name="address" rows="3" placeholder="Alamat Lengkap">{{ old('address', $customer->address) }}</textarea>
                            <label for="address">Alamat Lengkap</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <select id="code_province" name="code_province" class="select2 form-select">
                                <option value="">Pilih Provinsi</option>
                                @foreach ($provinces as $province)
                                    <option value="{{ $province->code }}"
                                        {{ old('code_province', $customer->code_province) == $province->code ? 'selected' : '' }}>
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
                                        {{ old('code_city', $customer->code_city) == $city->code ? 'selected' : '' }}>
                                        {{ $city->name }}
                                    </option>
                                @endforeach
                            </select>
                            <label for="code_city">Kota/Kabupaten</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <select id="code_cabang" name="code_cabang" class="form-select" required>
                                <option value="">Pilih Cabang</option>
                                @foreach ($cabangs as $cabang)
                                    <option value="{{ $cabang->code }}"
                                        {{ old('code_cabang', $customer->code_cabang) == $cabang->code ? 'selected' : '' }}>
                                        {{ $cabang->name }}
                                    </option>
                                @endforeach
                            </select>
                            <label for="code_cabang">Cabang</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" id="code_mitra" name="code_mitra" class="form-control" readonly
                                value="{{ old('code_mitra', $customer->code_mitra) }}" />
                            <label for="code_mitra">Mitra Pengaju</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <select id="status_prospek" name="status_prospek" class="form-select" required>
                                <option value="">Pilih Status Prospek</option>
                                <option value="cold"
                                    {{ old('status_prospek', $customer->status_prospek) == 'cold' ? 'selected' : '' }}>Cold
                                </option>
                                <option value="warm"
                                    {{ old('status_prospek', $customer->status_prospek) == 'warm' ? 'selected' : '' }}>Warm
                                </option>
                                <option value="hot"
                                    {{ old('status_prospek', $customer->status_prospek) == 'hot' ? 'selected' : '' }}>Hot
                                </option>
                            </select>
                            <label for="status_prospek">Status Prospek</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <select id="code_category" name="code_category" class="form-select">
                                <option value="">Pilih Kategori</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->code }}"
                                        {{ old('code_category', $customer->code_category) == $category->code ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <label for="code_category">Kategori</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <select id="code_program" name="code_program" class="form-select">
                                <option value="">Pilih Program</option>
                                @foreach ($programs as $program)
                                    <option value="{{ $program->code }}"
                                        {{ old('code_program', $customer->code_program) == $program->code ? 'selected' : '' }}>
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
                        <div class="form-floating form-floating-outline mb-2">
                            <input type="file" id="picture_ktp" name="picture_ktp" class="form-control"
                                accept="image/*" />
                            <label for="picture_ktp">Foto KTP (Opsional)</label>
                        </div>
                        @if ($customer->picture_ktp)
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h6 class="card-subtitle mb-2 text-muted">KTP Saat Ini:</h6>
                                    <img src="{{ asset($customer->picture_ktp) }}" class="img-fluid rounded"
                                        alt="KTP Preview" style="max-height: 200px;">
                                </div>
                            </div>
                        @endif
                        <small class="text-muted">Upload gambar baru untuk mengganti yang ada</small>
                    </div>
                </div>
                <hr class="my-4 mx-n4" />

                <div class="row">
                    <div class="col-12 d-flex justify-content-between align-items-center">
                        <a href="{{ route('customer.list') }}" class="btn btn-label-secondary">
                            <i class="ri-arrow-left-line me-1"></i>Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line me-1"></i>Simpan Perubahan
                        </button>
                    </div>
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

    <script>
        $(document).ready(function() {
            $('.flatpickr').flatpickr({
                dateFormat: "Y-m-d",
                defaultDate: "{{ old('birth_date', $customer->birth_date) }}"
            });

            $('.select2').select2();

            new Cleave('.phone-mask', {
                phone: true,
                phoneRegionCode: 'ID'
            });

            $('#picture_ktp').change(function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('.ktp-preview').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>
@endsection
