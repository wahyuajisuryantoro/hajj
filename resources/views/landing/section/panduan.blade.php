<!-- start features -->
<section class="section features" id="panduan">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center gap-2">
                <div>
                    <h5 class="mb-0">Panduan Haji</h5>
                    <small class="text-muted">Daftar Panduan Haji dan Umroh</small>
                </div>
            </div>
        </div>

        <div class="list-group">
            @php
                $guides = [
                    [
                        'title' => 'Panduan Umroh',
                        'file' => 'panudan_umroh.pdf'
                    ],
                    [
                        'title' => 'Konsultasi Manasik Haji Dan Umroh',
                        'file' => 'konsultasi_manasik_haji_dan_umroh.pdf'
                    ],
                    [
                        'title' => 'Doa-doa Haji Dan Umroh 1',
                        'file' => 'doa_haji_umroh_1.pdf'
                    ],
                    [
                        'title' => 'Doa Dzikir Haji Dan Umroh 2',
                        'file' => 'doa_dzikir_umroh.pdf'
                    ],
                    [
                        'title' => 'Panduan Manasik Haji',
                        'file' => 'panduan_manasik_haji.pdf'
                    ],
                    [
                        'title' => 'Seri Buku Kumpulan Doa',
                        'file' => 'seri_buku_kumpulan_doa.pdf'
                    ],
                ];
            @endphp

           @foreach($guides as $guide)
                <div class="list-group-item border-0 d-flex justify-content-between align-items-center py-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="pdf-icon">
                            <i class="mdi mdi-file-pdf-box text-danger"></i>
                        </div>
                        <span>{{ $guide['title'] }}</span>
                    </div>
                    <a href="{{ route('download.guide', ['file' => $guide['file']]) }}" 
                       class="download-link">
                        <i class="mdi mdi-download text-primary"></i>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
<!-- end features -->