@extends('pages.account.index')

@section('account-content')
    <div class="card mb-6">
        <h5 class="card-header mb-2">Perbarui Foto Profil Anda</h5>
        <div class="card-body pt-0">
            <form id="formProfilePicture" method="POST" enctype="multipart/form-data"
                action="{{ route('account.update-profile-picture') }}">
                @csrf
                <div class="d-flex align-items-start align-items-sm-center gap-6">
                    <img src="{{ $mitra->picture_profile ? $mitra->picture_profile : asset('assets/img/avatars/1.png') }}"
                        alt="user-avatar" class="d-block w-px-100 h-px-100 rounded-4" id="uploadedAvatarPreview" />
                    <div class="button-wrapper">
                        <label for="uploadPicture" class="btn btn-primary me-3 mb-4" tabindex="0">
                            <span class="d-none d-sm-block">Pilih Foto Baru</span>
                            <i class="ri-upload-2-line d-block d-sm-none"></i>
                            <input type="file" id="uploadPicture"
                                class="account-file-input @error('picture_profile') is-invalid @enderror"
                                name="picture_profile" hidden accept="image/png, image/jpeg, image/jpg" />
                        </label>
                        {{-- <button type="button" class="btn btn-outline-danger account-picture-reset mb-4">
                            <i class="ri-refresh-line d-block d-sm-none"></i>
                            <span class="d-none d-sm-block">Reset</span>
                        </button> --}}
                        <div>Format File Wajib JPG, JPEG, PNG. Ukuran Maksimal 2MB</div>
                        @error('picture_profile')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-success save-picture-btn" disabled>Save Photo</button>
                </div>
            </form>
        </div>
    </div>
    <div class="card mb-6">
        <h5 class="card-header mb-1">Perbarui Informasi Dasar Akun Anda</h5>
        <div class="card-body pt-0">
            <form id="formAccountSettings" method="POST" action="{{ route('account.update-profile') }}">
                @csrf
                <div class="row mt-1 g-5">
                    <!-- Nama -->
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input class="form-control" type="text" id="name" name="name"
                                value="{{ old('name', $mitra->name) }}" required autofocus />
                            <label for="name">Name</label>
                        </div>
                    </div>
                    <!-- Email -->
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input class="form-control" type="email" name="email" id="email"
                                value="{{ old('email', $mitra->email) }}" required />
                            <label for="email">E-mail</label>
                        </div>
                    </div>
                    <!-- Nomor Telepon -->
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control" id="phone" name="phone"
                                value="{{ old('phone', $mitra->phone) }}" required />
                            <label for="phone">Phone Number</label>
                        </div>
                    </div>
                    <!-- Tempat Lahir -->
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control" id="birth_place" name="birth_place"
                                value="{{ old('birth_place', $mitra->birth_place) }}" required />
                            <label for="birth_place">Birth Place</label>
                        </div>
                    </div>
                    <!-- Tanggal Lahir -->
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input class="form-control" type="date" id="birth_date" name="birth_date"
                                value="{{ old('birth_date', $mitra->birth_date ? \Carbon\Carbon::parse($mitra->birth_date)->format('Y-m-d') : '') }}"
                                required />
                            <label for="birth_date">Birth Date</label>
                            @error('birth_date')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Alamat -->
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <textarea class="form-control" id="address" name="address" required>{{ old('address', $mitra->address) }}</textarea>
                            <label for="address">Address</label>
                        </div>
                    </div>
                </div>
                <div class="mt-6">
                    <button type="submit" class="btn btn-primary me-3 save-basic-btn" disabled>Save changes</button>
                    {{-- <button type="reset" class="btn btn-outline-secondary">Reset</button> --}}
                </div>
            </form>
        </div>
    </div>
@endsection
