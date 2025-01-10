@extends('pages.account.index')

@section('account-content')
    <!-- Form Edit Bank -->
    <div class="card mb-6">
        <div class="card-header">
            Edit Informasi Bank
        </div>
        <div class="card-body pt-0">
            <!-- Tampilkan Alert di sini jika ada -->
            @include('sweetalert::alert')

            <form id="formEditBank" method="POST" action="{{ route('account.update-bank') }}">
                @csrf
                <div class="row g-5">
                    <!-- Nama Bank -->
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <select class="form-select @error('bank') is-invalid @enderror" id="bank" name="bank" required>
                                <option value="">Pilih Bank</option>
                                <option value="BCA" {{ old('bank', $mitra->bank) == 'BCA' ? 'selected' : '' }}>BCA</option>
                                <option value="BNI" {{ old('bank', $mitra->bank) == 'BNI' ? 'selected' : '' }}>BNI</option>
                                <option value="Mandiri" {{ old('bank', $mitra->bank) == 'Mandiri' ? 'selected' : '' }}>Mandiri</option>
                                <option value="BRI" {{ old('bank', $mitra->bank) == 'BRI' ? 'selected' : '' }}>BRI</option>
                                <!-- Tambahkan opsi bank lainnya sesuai kebutuhan -->
                            </select>
                            <label for="bank">Nama Bank</label>
                            @error('bank')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Nomor Rekening -->
                    <div class="col-md-6">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control @error('bank_number') is-invalid @enderror" id="bank_number" name="bank_number" value="{{ old('bank_number', $mitra->bank_number) }}" required />
                            <label for="bank_number">Nomor Rekening</label>
                            @error('bank_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Nama Pemilik Rekening -->
                    <div class="col-md-12">
                        <div class="form-floating form-floating-outline">
                            <input type="text" class="form-control @error('bank_name') is-invalid @enderror" id="bank_name" name="bank_name" value="{{ old('bank_name', $mitra->bank_name) }}" required />
                            <label for="bank_name">Nama Pemilik Rekening</label>
                            @error('bank_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" class="btn btn-primary save-bank-btn" disabled>Save Changes</button>
                    <button type="reset" class="btn btn-outline-secondary reset-bank-btn">Reset</button>
                </div>
            </form>
        </div>
    </div>
@endsection

