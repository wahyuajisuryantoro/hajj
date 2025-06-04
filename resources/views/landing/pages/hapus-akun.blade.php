@extends('landing.layouts.app')

@section('content')
<div class="container mt-5 pt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white text-center">
                    <h2 class="mb-0">🗑️ Penghapusan Akun</h2>
                    <h4 class="mb-0">Aplikasi HAJJ</h4>
                    <p class="mb-0">Dikembangkan oleh: <strong>Bumi Tekno Indonesia</strong></p>
                </div>
                <div class="card-body">
                    <!-- Peringatan Penting -->
                    <div class="alert alert-warning">
                        <h5 class="alert-heading">⚠️ PENTING</h5>
                        <p><strong>Penghapusan akun hanya dapat dilakukan oleh Administrator sistem.</strong></p>
                        <p class="mb-0">Semua permintaan penghapusan akun harus melalui tim admin kami untuk memastikan keamanan dan verifikasi identitas.</p>
                    </div>

                    <!-- Kontak Admin Prominent -->
                    <div class="card bg-light border-danger mb-4">
                        <div class="card-body text-center">
                            <h4 class="text-danger">📞 HUBUNGI ADMIN UNTUK HAPUS AKUN</h4>
                            <p class="lead">Untuk menghapus akun Anda di aplikasi <strong>HAJJ</strong>, silakan hubungi admin:</p>
                            <a href="https://wa.me/6282137095847?text=Halo Admin, saya ingin menghapus akun saya di aplikasi HAJJ.%0A%0ANama: [Nama Anda]%0AUsername: [Username Anda]%0A%0ATerima kasih" 
                               class="btn btn-success btn-lg" target="_blank">
                               <i class="fab fa-whatsapp"></i> WHATSAPP ADMIN SEKARANG
                            </a>
                            <br>
                            <small class="text-muted mt-2 d-block">Nomor: +62 821-3709-5847</small>
                        </div>
                    </div>

                    <!-- Langkah-langkah -->
                    <div class="alert alert-info">
                        <h5 class="alert-heading">📋 Langkah-langkah Penghapusan Akun:</h5>
                        <ol class="mb-0">
                            <li><strong>Hubungi Admin</strong> melalui WhatsApp di nomor: 
                                <a href="https://wa.me/6282137095847" target="_blank" class="font-weight-bold">0821-3709-5847</a>
                            </li>
                            <li><strong>Berikan Informasi:</strong>
                                <ul class="mt-2">
                                    <li>Username akun Anda di aplikasi HAJJ</li>
                                    <li>Nama lengkap sesuai akun</li>
                                    <li>Alasan penghapusan akun (opsional)</li>
                                </ul>
                            </li>
                            <li><strong>Verifikasi Identitas:</strong> Admin akan memverifikasi identitas Anda</li>
                            <li><strong>Pemrosesan:</strong> Admin akan memproses permintaan dalam <strong>1-3 hari kerja</strong></li>
                            <li><strong>Konfirmasi:</strong> Anda akan menerima konfirmasi setelah akun berhasil dihapus</li>
                        </ol>
                    </div>

                    <!-- Data yang akan dihapus -->
                    <div class="card border-success mb-3">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">🗂️ Data Yang Akan Dihapus Permanen</h5>
                        </div>
                        <div class="card-body">
                            <p>Setelah akun dihapus, data berikut akan dihapus secara permanen dari aplikasi <strong>HAJJ</strong>:</p>
                            <ul class="mb-0">
                                <li>Informasi profil pengguna (nama, email, nomor telepon)</li>
                                <li>Riwayat transaksi bonus dan ujroh</li>
                                <li>Data aktivitas dalam aplikasi HAJJ</li>
                                <li>Preferensi dan pengaturan akun</li>
                                <li>Riwayat komunikasi dengan customer service</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Data yang tetap disimpan -->
                    <div class="card border-warning mb-3">
                        <div class="card-header bg-warning">
                            <h5 class="mb-0">📁 Data Yang Tetap Disimpan (Maksimal 3 Tahun)</h5>
                        </div>
                        <div class="card-body">
                            <p>Untuk keperluan kepatuhan hukum dan audit:</p>
                            <ul class="mb-0">
                                <li>Log transaksi finansial (untuk audit keuangan)</li>
                                <li>Data yang diperlukan untuk kepatuhan regulasi</li>
                                <li>Informasi untuk menyelesaikan sengketa hukum (jika ada)</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Waktu pemrosesan -->
                    <div class="alert alert-secondary">
                        <h5 class="alert-heading">⏰ Waktu Pemrosesan</h5>
                        <p><strong>Penghapusan akun akan diproses dalam 1-3 hari kerja</strong> setelah verifikasi identitas selesai.</p>
                        <p class="mb-0">Anda akan menerima konfirmasi melalui WhatsApp setelah proses penghapusan selesai.</p>
                    </div>

                    <!-- Peringatan terakhir -->
                    <div class="alert alert-danger">
                        <h5 class="alert-heading">🚨 PERINGATAN</h5>
                        <p class="mb-0">
                            <strong>Catatan:</strong> Penghapusan akun bersifat <strong>PERMANEN</strong> dan tidak dapat dibatalkan.
                            Semua data terkait akun Anda akan dihapus dari sistem aplikasi HAJJ.
                        </p>
                    </div>

                    <!-- Kontak support -->
                    <div class="text-center mt-4 p-3 bg-light rounded">
                        <h5>📞 Kontak Admin</h5>
                        <p class="mb-1"><strong>WhatsApp:</strong> +62 821-3709-5847</p>
                        <p class="mb-0"><strong>Jam Operasional:</strong> Senin-Jumat, 09:00-17:00 WIB</p>
                    </div>
                </div>
                
                <div class="card-footer text-center text-muted">
                    <small>
                        Halaman ini untuk aplikasi <strong>HAJJ</strong><br>
                        Dikembangkan oleh: <strong>Bumi Tekno Indonesia</strong><br>
                        Terakhir diperbarui: {{ date('d F Y') }}
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection