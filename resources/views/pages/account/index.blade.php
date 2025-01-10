@extends('layouts.master')
@section('style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/@form-validation/form-validation.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/animate-css/animate.css') }}" />
    @stack('custom-style')
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-md-12">
                @include('pages.account._partials._navigation')
                @yield('account-content')
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('assets/vendor/libs/@form-validation/popular.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/auto-focus.js') }}"></script>
    @stack('custom-scripts')

    {{-- Function Update Profil Akun --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- Upload Foto Profil ---
            const uploadInput = document.getElementById('uploadPicture');
            const previewImage = document.getElementById('uploadedAvatarPreview');
            const savePictureButton = document.querySelector('.save-picture-btn');
            const resetPictureButton = document.querySelector('.account-picture-reset');
            const formProfilePicture = document.getElementById('formProfilePicture');

            // Menangani perubahan pada input file
            uploadInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    // Validasi ukuran file (max 2MB)
                    if (file.size > 2 * 1024 * 1024) { // 2MB
                        Swal.fire(
                            'Error!',
                            'Ukuran file melebihi 2MB.',
                            'error'
                        );
                        uploadInput.value = ''; // Reset input
                        savePictureButton.disabled = true;
                        return;
                    }

                    // Validasi tipe file
                    const validTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                    if (!validTypes.includes(file.type)) {
                        Swal.fire(
                            'Error!',
                            'Format file tidak didukung. Silakan pilih JPG, JPEG, atau PNG.',
                            'error'
                        );
                        uploadInput.value = ''; // Reset input
                        savePictureButton.disabled = true;
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImage.src = e.target.result;
                        savePictureButton.disabled = false;
                    }
                    reader.readAsDataURL(file);
                } else {
                    previewImage.src = '{{ $mitra->picture_profile ? $mitra->picture_profile : asset("assets/img/avatars/1.png") }}';
                    savePictureButton.disabled = true;
                }
            });
            // Menangani submit form Upload Foto Profil dengan SweetAlert konfirmasi
            formProfilePicture.addEventListener('submit', function(e) {
                e.preventDefault(); // Mencegah submit default

                const formData = new FormData(formProfilePicture);

                // Menampilkan konfirmasi SweetAlert2
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Foto profil Anda akan diperbarui.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, simpan foto!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Mengirim data via AJAX
                        fetch(formProfilePicture.action, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    // 'Content-Type': 'multipart/form-data' // Jangan set Content-Type ketika menggunakan FormData
                                },
                                body: formData
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire(
                                        'Berhasil!',
                                        data.message,
                                        'success'
                                    ).then(() => {
                                        // Update preview dengan foto baru
                                        previewImage.src = data.data.picture_profile;
                                        savePictureButton.disabled = true;
                                        uploadInput.value = '';
                                    });
                                } else {
                                    Swal.fire(
                                        'Gagal!',
                                        data.message,
                                        'error'
                                    );
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire(
                                    'Error!',
                                    'Terjadi kesalahan saat memperbarui foto profil.',
                                    'error'
                                );
                            });
                    }
                });
            });

            // --- Pembaruan Informasi Dasar ---
            const formAccountSettings = document.getElementById('formAccountSettings');
            const saveBasicButton = document.querySelector('.save-basic-btn');
            const resetBasicButton = document.querySelector('.reset-basic-btn');
            let isFormDirty = false;

            // Fungsi untuk mengecek apakah form telah diubah
            function checkFormDirty() {
                isFormDirty = false;
                const inputs = formAccountSettings.querySelectorAll('input, textarea');
                inputs.forEach(input => {
                    if (input.value !== input.defaultValue) {
                        isFormDirty = true;
                    }
                });

                saveBasicButton.disabled = !isFormDirty;
            }

            // Mengatur event listeners untuk semua input dan textarea
            const accountInputs = formAccountSettings.querySelectorAll('input, textarea');
            accountInputs.forEach(input => {
                input.addEventListener('input', checkFormDirty);
            });

            // Menangani klik pada tombol Reset untuk form informasi dasar
            resetBasicButton.addEventListener('click', function() {
                formAccountSettings.reset();
                checkFormDirty();
            });

            // Menangani submit form Pembaruan Informasi Dasar dengan SweetAlert konfirmasi
            formAccountSettings.addEventListener('submit', function(e) {
                e.preventDefault(); // Mencegah submit default

                const formData = new FormData(formAccountSettings);

                // Menampilkan konfirmasi SweetAlert2
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Informasi profil Anda akan diperbarui.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, perbarui!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Mengirim data via AJAX
                        fetch(formAccountSettings.action, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    // 'Content-Type': 'multipart/form-data' // Jangan set Content-Type ketika menggunakan FormData
                                },
                                body: formData
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire(
                                        'Berhasil!',
                                        data.message,
                                        'success'
                                    ).then(() => {
                                        // Reset form dan disable tombol
                                        formAccountSettings.reset();
                                        checkFormDirty();
                                        // Update defaultValue untuk inputs
                                        accountInputs.forEach(input => {
                                            input.defaultValue = input.value;
                                        });
                                    });
                                } else {
                                    Swal.fire(
                                        'Gagal!',
                                        data.message,
                                        'error'
                                    );
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire(
                                    'Error!',
                                    'Terjadi kesalahan saat memperbarui profil.',
                                    'error'
                                );
                            });
                    }
                });
            });
        });
    </script>

    {{-- Function Update Akun Bank --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const formEditBank = document.getElementById('formEditBank');
            const saveBankButton = document.querySelector('.save-bank-btn');
            const resetBankButton = document.querySelector('.reset-bank-btn');
            let isBankFormDirty = false;
            function checkBankFormDirty() {
                isBankFormDirty = false;
                const inputs = formEditBank.querySelectorAll('input, select');
                inputs.forEach(input => {
                    if (input.value !== input.defaultValue && input.defaultValue !== "") {
                        isBankFormDirty = true;
                    }
                });

                saveBankButton.disabled = !isBankFormDirty;
            }
            const bankInputs = formEditBank.querySelectorAll('input, select');
            bankInputs.forEach(input => {
                input.addEventListener('input', checkBankFormDirty);
                input.addEventListener('change', checkBankFormDirty);
            });
            resetBankButton.addEventListener('click', function() {
                formEditBank.reset();
                checkBankFormDirty();
                bankInputs.forEach(input => {
                    input.defaultValue = input.value;
                });
            });

            formEditBank.addEventListener('submit', function(e) {
                e.preventDefault(); 
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Informasi bank Anda akan diperbarui.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, perbarui!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Mengirim form secara manual
                        formEditBank.submit();
                    }
                });
            });
            @if(session('success_bank'))
                Swal.fire(
                    'Berhasil!',
                    '{{ session('success_bank') }}',
                    'success'
                );
            @endif

            @if(session('error_bank'))
                Swal.fire(
                    'Gagal!',
                    '{{ session('error_bank') }}',
                    'error'
                );
            @endif
        });
    </script>

    {{-- Function Update Password --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePasswordElements = document.querySelectorAll('.toggle-password');
            togglePasswordElements.forEach(function(toggle) {
                toggle.addEventListener('click', function() {
                    const input = this.parentElement.querySelector('input');
                    if (input.type === 'password') {
                        input.type = 'text';
                        this.querySelector('i').classList.remove('ri-eye-off-line');
                        this.querySelector('i').classList.add('ri-eye-line');
                    } else {
                        input.type = 'password';
                        this.querySelector('i').classList.remove('ri-eye-line');
                        this.querySelector('i').classList.add('ri-eye-off-line');
                    }
                });
            });
            @if(session('success'))
                Swal.fire({
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    title: 'Gagal!',
                    text: '{{ session('error') }}',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            @endif
        });
    </script>
@endsection
