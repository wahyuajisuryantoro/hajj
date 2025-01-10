@extends('layouts.master')
@section('style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/app-academy.css') }}" />
    <style>
        .badge {
            font-size: 0.85rem;
            padding: 0.4rem 0.75rem;
        }

        .overlay-hover {
            transition: opacity 0.3s ease;
        }

        .card:hover .overlay-hover {
            opacity: 1 !important;
        }

        .content-wrapper i {
            font-size: 1rem;
            vertical-align: middle;
        }
    </style>
@endsection
@section('content')
    <div class="card mb-6">
        <div class="card-header d-flex flex-wrap justify-content-between gap-4">
            <div class="card-title mb-0 me-1">
                <h5 class="mb-0">E-Flayer Marketing</h5>
                <p class="mb-0 text-body">Klik gambar untuk melihat ukuran penuh & download</p>
            </div>
        </div>
        <div class="card-body mt-1">
            <div class="row gy-6 mb-6">
                @forelse($eflayers as $flayer)
                    <div class="col-sm-6 col-lg-4">
                        <div class="card p-2 h-100 shadow-none border rounded-3">
                            <div class="rounded-4 text-center mb-3 position-relative">

                                <a href="{{ $flayer->picture }}" target="_blank" class="d-block"
                                    download="{{ $flayer->name }}">
                                    <img class="img-fluid rounded" src="{{ $flayer->picture }}" alt="{{ $flayer->name }}" />
                                    <div
                                        class="overlay-hover position-absolute top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center bg-dark bg-opacity-25 rounded opacity-0">
                                        <i class="ri-download-2-line text-white ri-2x"></i>
                                    </div>
                                </a>
                            </div>
                            <div class="card-body p-3 pt-0">
                                <h5 class="mb-3">{{ $flayer->name }}</h5>
                                @if ($flayer->content)
                                    @php
                                        $content = $flayer->formatted_content;
                                    @endphp

                                    {{-- Badges Info --}}
                                    <div class="d-flex flex-wrap gap-2 mb-3">
                                        @if ($content['pesawat'])
                                            <div class="badge bg-label-info">
                                                <i class="ri-plane-line me-1"></i>{{ $content['pesawat'] }}
                                            </div>
                                        @endif
                                        @if ($content['durasi'])
                                            <div class="badge bg-label-primary">
                                                <i class="ri-time-line me-1"></i>{{ $content['durasi'] }} Hari
                                            </div>
                                        @endif
                                        @if ($content['tanggal'])
                                            <div class="badge bg-label-success">
                                                <i class="ri-calendar-line me-1"></i>{{ $content['tanggal'] }}
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Hotel Info --}}
                                    <div class="mb-3">
                                        <div class="row g-2">
                                            @foreach ($content['hotels'] as $hotel)
                                                <div class="col-12">
                                                    <div class="d-flex align-items-center">
                                                        <i class="ri-hotel-line me-2"></i>
                                                        <div>
                                                            <small class="text-muted d-block">{{ $hotel['lokasi'] }}</small>
                                                            <span>{{ $hotel['nama'] }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- Quick Info --}}
                                    <div class="bg-label-primary rounded p-2 mb-3">
                                        <div class="d-flex gap-3">
                                            <div>
                                                <i class="ri-check-line text-primary"></i>
                                                {{ count($content['fasilitas']) }} Fasilitas
                                            </div>
                                            <div>
                                                <i class="ri-information-line text-primary"></i>
                                                Cicilan Tersedia
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <button type="button" class="btn btn-primary btn-share"
                                    onclick="showShareModal('{{ $flayer->name }}', '{{ $flayer->code }}', '{{ $flayer->picture }}')">
                                    <i class="ri-share-line me-1"></i> Bagikan
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center">
                        <p>Tidak ada e-flayer tersedia saat ini.</p>
                    </div>
                @endforelse
            </div>

            {{ $eflayers->links('vendor.pagination.custom') }}
        </div>
        <!-- Modal Share -->
        <div class="modal fade" id="shareModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-simple">
                <div class="modal-content">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="modal-body pt-4 pt-md-0 px-0 pb-md-0">
                        <div class="text-center mb-4">
                            <h4 class="mb-2">Bagikan E-Flayer</h4>
                            <p class="text-center mb-4">
                                Bagikan e-flayer ini ke media sosial atau salin linknya untuk dibagikan ke teman Anda
                            </p>
                        </div>
                        <hr class="my-4" />
                        <div class="text-center mb-4">
                            <img id="previewImage" src="" alt="Preview" class="img-fluid rounded"
                                style="max-height: 300px">
                            <h5 class="mt-3 flayer-title"></h5>
                        </div>

                        <div class="row g-4">
                            <div class="col-lg-9">
                                <label class="mb-2" for="shareUrl">
                                    Salin link e-flayer untuk dibagikan ke platform lain 🔗
                                </label>
                                <div class="input-group input-group-merge">
                                    <input type="text" id="shareUrl" class="form-control" readonly />
                                    <button class="input-group-text text-primary cursor-pointer text-uppercase"
                                        id="copyShareUrl">
                                        Copy link
                                    </button>
                                </div>
                            </div>
                            <div class="col-lg-3 d-flex align-items-end">
                                <div class="btn-social">
                                    <button type="button" class="btn btn-icon btn-facebook share-social"
                                        data-type="facebook">
                                        <i class="tf-icons ri-facebook-circle-line ri-22px"></i>
                                    </button>
                                    <button type="button" class="btn btn-icon btn-twitter share-social"
                                        data-type="twitter">
                                        <i class="tf-icons ri-twitter-line ri-22px"></i>
                                    </button>
                                    <button type="button" class="btn btn-icon btn-whatsapp share-social"
                                        data-type="whatsapp">
                                        <i class="tf-icons ri-whatsapp-line ri-22px"></i>
                                    </button>
                                    <button type="button" class="btn btn-icon btn-telegram share-social"
                                        data-type="telegram">
                                        <i class="tf-icons ri-telegram-line ri-22px"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
<script>
    let shareModal;
    const BASE_URL = 'https://www.hasanahtours.com/program/';
    
    document.addEventListener('DOMContentLoaded', function() {
        shareModal = new bootstrap.Modal(document.getElementById('shareModal'));
        
        // Handle tombol copy
        document.getElementById('copyShareUrl')?.addEventListener('click', function() {
            const shareUrl = document.getElementById('shareUrl');
            shareUrl.select();
            document.execCommand('copy');
            
            this.textContent = 'Copied!';
            setTimeout(() => this.textContent = 'Copy Link', 2000);
        });

        // Handle tombol share sosial media
        document.querySelectorAll('.share-social').forEach(btn => {
            btn.addEventListener('click', function() {
                const type = this.dataset.type;
                const url = document.getElementById('shareUrl').value;
                const title = document.querySelector('#shareModal .flayer-title').textContent;
                
                let shareUrl;
                switch(type) {
                    case 'facebook':
                        shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`;
                        break;
                    case 'twitter':
                        shareUrl = `https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}&text=${encodeURIComponent('Lihat E-Flayer: ' + title)}`;
                        break;
                    case 'whatsapp':
                        shareUrl = `https://wa.me/?text=${encodeURIComponent('Lihat E-Flayer: ' + title + '\n\n' + url)}`;
                        break;
                    case 'telegram':
                        shareUrl = `https://t.me/share/url?url=${encodeURIComponent(url)}&text=${encodeURIComponent('Lihat E-Flayer: ' + title)}`;
                        break;
                }
                
                if (shareUrl) {
                    window.open(shareUrl, '_blank', 'width=600,height=400');
                }
            });
        });
    });

    function showShareModal(title, code, imageUrl) {
        const shareUrl = BASE_URL + code;
        
        // Update modal content
        document.querySelector('#shareModal .flayer-title').textContent = title;
        document.getElementById('shareUrl').value = shareUrl;
        document.getElementById('previewImage').src = imageUrl;
        
        shareModal.show();
    }
</script>
@endsection
